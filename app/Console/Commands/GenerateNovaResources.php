<?php

namespace App\Console\Commands;

use Arr;
use DB;
use Doctrine\DBAL\Types\Types;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne as EloquentHasOne;
use Illuminate\Database\Eloquent\Relations\HasMany as EloquentHasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo as EloquentBelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as EloquentBelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Http\Requests\NovaRequest;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use function app_path;
use function array_merge;
use function collect;
use function end;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function implode;
use function in_array;
use function lcfirst;
use function preg_match;
use function preg_match_all;
use function strlen;
use function strpos;
use function strtolower;
use function strtoupper;
use function substr;
use function trim;
use function ucfirst;
use const PREG_OFFSET_CAPTURE;

class GenerateNovaResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:nova-resources';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto create and override Nova resources';

    private int $novaVersion = 3;

    private Collection $modelNameAliases;

    private array $fieldTypes;
    private array $neededImports;
    private array $dbTypeFieldTypeMap;
    private array $fieldTypeOptionsMap;
    private array $castFieldTypeMap;
    private array $returnTypeNameFieldTypeMap;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->fieldTypes = [
            'Boolean',
            'Date',
            'DateTime',
            'ID',
            'Number',
            'Text',
            'Textarea',
            'Password',
            'BelongsTo',
            'BelongsToMany',
            'HasOne',
            'HasMany',
        ];

        $this->dbTypeFieldTypeMap = [
            Types::ARRAY => 'Text',

            Types::ASCII_STRING => 'Text',
            Types::BIGINT => 'Number',
            Types::BINARY => 'Text',
            Types::BLOB => 'Text',
            Types::BOOLEAN => 'Boolean',
            Types::DATE_MUTABLE => 'DateTime',
            Types::DATE_IMMUTABLE => 'DateTime',
            Types::DATEINTERVAL => 'Text',
            Types::DATETIME_MUTABLE => 'DateTime',
            Types::DATETIME_IMMUTABLE => 'DateTime',
            Types::DATETIMETZ_MUTABLE => 'DateTime',
            Types::DATETIMETZ_IMMUTABLE => 'DateTime',
            Types::DECIMAL => 'Number',
            Types::FLOAT => 'Number',
            Types::GUID => 'Text',
            Types::INTEGER => 'Number',
            Types::JSON => 'Textarea',

            Types::OBJECT => 'Textarea',

            Types::SIMPLE_ARRAY => 'Textarea',
            Types::SMALLINT => 'Number',
            Types::STRING => 'Text',
            Types::TEXT => 'Textarea',
            Types::TIME_MUTABLE => 'DateTime',
            Types::TIME_IMMUTABLE => 'DateTime',
        ];

        $this->neededImports = [
            'use Laravel\Nova\Fields\Boolean;',
            'use Laravel\Nova\Fields\Date;',
            'use Laravel\Nova\Fields\DateTime;',
            'use Laravel\Nova\Fields\ID;',
            'use Laravel\Nova\Fields\Number;',
            'use Laravel\Nova\Fields\Text;',
            'use Laravel\Nova\Fields\Textarea;',
            'use Laravel\Nova\Fields\BelongsTo;',
            'use Laravel\Nova\Fields\BelongsToMany;',
            'use Laravel\Nova\Fields\HasOne;',
            'use Laravel\Nova\Fields\HasMany;',
        ];

        $this->fieldTypeOptionsMap = [
            'ID' => [
                'sortable()',
                'readonly()',
            ],
            'Boolean' => [
                'sortable()',
            ],
            'Date' => [
                'sortable()',
            ],
            'DateTime' => [
                'sortable()',
            ],
            'Number' => [
                'sortable()',
            ],
            'Text' => [],
            'Textarea' => [
                'onlyOnDetail()',
            ],
            'BelongsTo' => [],
            'BelongsToMany' => [],
            'HasMany' => [],
            'HasOne' => [],
        ];

        $this->castFieldTypeMap = [
            'string' => 'Text',
            'date' => 'Date',
            'timestamp' => 'DateTime',
            'custom_datetime' => 'DateTime',
            'datetime' => 'DateTime',
            'bool' => 'Boolean',
            'boolean' => 'Boolean',
            'json' => 'Textarea',
            'int' => 'Number',
            'integer' => 'Number',
            'float' => 'Number',
            'decimal' => 'Number',
            'double' => 'Number',
            'array' => 'Textarea',
            'collection' => 'Textarea',
        ];

        $this->returnTypeNameFieldTypeMap = [
            'BelongsTo' => 'BelongsTo',
            'BelongsToMany' => 'BelongsToMany',
            'HasOne' => 'HasOne',
            'HasMany' => 'HasMany',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        // Requirements:
        // composer require haydenpierce/class-finder
        // composer require doctrine/dbal

        $this->novaVersion = (int)$this->choice('Which Nova version are you using?', [3, 4], 3);

        $this->fillModelAliases();

        $fullClassnames = ClassFinder::getClassesInNamespace('App\Models');

        foreach ($fullClassnames as $fullClassname) {
            $parts = explode('\\', $fullClassname);
            $simpleClassName = end($parts);
            $modelNameToUse = $this->getModelNameToUse($simpleClassName);

            if (Arr::has(['Resource'], $modelNameToUse)) continue;

            Artisan::call("nova:resource {$modelNameToUse}");

            $this->correctCorrespondingModel($simpleClassName);
            $this->addImports($simpleClassName);
            $this->addFieldsFor($simpleClassName);

            $this->info("Nova resource for {$simpleClassName} (alias: {$modelNameToUse}) created or refreshed");
        }

        $this->newLine();
        $this->info('Done.');

        return 0;
    }

    private function fillModelAliases(): void
    {
        $this->info('Now you can add some model name aliases to prevent naming conflicts. Just press ENTER to skip or finish this step.');

        $modelNameAliases = [];

        while (true) {
            $modelName = $this->ask('Original simple model name:');
            if (empty($modelName)) break;
            $modelName = Str::replace('.php', '', $modelName);

            if (!file_exists(app_path("Models/{$modelName}.php"))) {
                $this->warn('This model does not exist! Try again.');
                continue;
            }

            $modelNameAlias = $this->ask('Simple model alias name:');
            if (empty($modelNameAlias)) {
                $this->warn('The alias must not be empty! Try again.');
                continue;
            }

            $modelNameAliases[$modelName] = $modelNameAlias;
        }

        $this->modelNameAliases = collect($modelNameAliases);
    }

    private function getModelNameToUse(string $modelName): string
    {
        return $this->modelNameAliases->get($modelName, $modelName);
    }

    private function correctCorrespondingModel(string $modelName): void
    {
        $modelNameToUse = $this->getModelNameToUse($modelName);

        $code = file_get_contents(app_path("Nova/{$modelNameToUse}.php"));

        preg_match('/public static \$model = \\\App\\\Models/', $code, $matches, PREG_OFFSET_CAPTURE);

        $modelNameStart = $matches[0][1] + strlen($matches[0][0]);
        $modelNameEnd = $modelNameStart + strlen($modelNameToUse) + 1;

        $newCode = substr($code, 0, $modelNameStart) . '\\' . $modelName . substr($code, $modelNameEnd);

        file_put_contents(app_path("Nova/{$modelNameToUse}.php"), $newCode);
    }

    private function addImports(string $modelName): void
    {
        $modelNameToUse = $this->getModelNameToUse($modelName);

        $code = file_get_contents(app_path("Nova/{$modelNameToUse}.php"));

        preg_match('/use Illuminate\\\Http\\\Request;/', $code, $matches, PREG_OFFSET_CAPTURE);

        $importStart = $matches[0][1] + strlen($matches[0][0]);

        $imports = collect([]);
        foreach ($this->neededImports as $import) {
            $contains = strpos($code, $import);
            if ($contains === false) {
                $imports->add($import);
            }
        }

        if ($imports->isEmpty()) return;

        $newCode = substr($code, 0, $importStart) . "\n" . $imports->join("\n") . substr($code, $importStart);

        file_put_contents(app_path("Nova/{$modelNameToUse}.php"), $newCode);
    }

    private function addFieldsFor(string $modelName): void
    {
        $modelNameToUse = $this->getModelNameToUse($modelName);

        $code = file_get_contents(app_path("Nova/{$modelNameToUse}.php"));

        if ($this->novaVersion == 3) {
            preg_match('/public function fields\(Request \$request\)\s*\{\s*return \[/', $code, $matches, PREG_OFFSET_CAPTURE);
        } else if ($this->novaVersion == 4) {
            preg_match('/public function fields\(NovaRequest \$request\)\s*\{\s*return \[/', $code, $matches, PREG_OFFSET_CAPTURE);
        }

        $returnBodyStart = $matches[0][1] + strlen($matches[0][0]);

        preg_match('/];\s*}\s*\/\*\*\s*\* Get the cards available for the request/', $code, $matches, PREG_OFFSET_CAPTURE);

        $returnBodyEnd = $matches[0][1];

        $returnBody = substr($code, $returnBodyStart, $returnBodyEnd - $returnBodyStart);

        /** @var Model $model */
        $model = new ('App\Models\\' . $modelName);

        $columnListing = DB::connection($model->getConnectionName())
            ->getSchemaBuilder()
            ->getColumnListing($model->getTable());

        $columnListing = collect($columnListing)->sortBy(function ($fieldName, $key) {
            if ($fieldName == 'created_at') return 'zzzzzzz_created_at';
            if ($fieldName == 'updated_at') return 'zzzzzzz_updated_at';
            if ($fieldName == 'deleted_at') return 'zzzzzzz_deleted_at';
            return $fieldName;
        });

        $relationMethods = $this->getRelationMethodNames($modelName);

        $lines = [];
        $addedFields = [];
        $addedRelations = [];

        foreach ($columnListing as $fieldName) {
            if (in_array($fieldName, $addedFields)) continue;

            if (!$this->existsField($returnBody, $fieldName)) {

                $belongsToMethodInfo = $this->getBelongsToMethodInfoOfField($modelName, $fieldName, $relationMethods);

                if ($belongsToMethodInfo != null) {
                    $belongsToMethodName = $belongsToMethodInfo[0];
                    $belongsToMethodModel = $this->getModelNameToUse($belongsToMethodInfo[1]);

                    if ($this->existsRelation($returnBody, $belongsToMethodName)) continue;

                    $formattedRelationName = ucfirst(Str::lower(Str::replace('_', ' ', $this->pascal_case_to_snake_case($belongsToMethodName))));
                    $relationOptions = $this->getRelationOptions($modelName, $belongsToMethodName, 'BelongsTo');

                    $lines[] = "BelongsTo::make(__('{$formattedRelationName}'), '{$belongsToMethodName}', {$belongsToMethodModel}::class){$relationOptions},";

                    $addedFields[] = $fieldName;
                    $addedRelations[] = $belongsToMethodName;
                } else {
                    $formattedFieldName = ucfirst(Str::lower(Str::replace('_', ' ', $this->pascal_case_to_snake_case($fieldName))));
                    $fieldType = $this->guessFieldType($modelName, $fieldName, $relationMethods);
                    $fieldOptions = $this->guessFieldOptions($modelName, $fieldName, $fieldType);

                    $lines[] = "{$fieldType}::make(__('{$formattedFieldName}'), '{$fieldName}'){$fieldOptions},";
                    $addedFields[] = $fieldName;
                }
            }
        }

        $lines[] = '';

        foreach ($relationMethods as $relationMethod => $relationMethodType) {
            if (in_array($relationMethod, $addedRelations)) continue;

            if (!$this->existsRelation($returnBody, $relationMethod)) {
                $formattedRelationName = ucfirst(Str::lower(Str::replace('_', ' ', $this->pascal_case_to_snake_case($relationMethod))));
                $relationFieldParams = $this->getPreparedModelParamsForRelationField($modelName, $relationMethod);
                $relationOptions = $this->getRelationOptions($modelName, $relationMethod, $relationMethodType);

                $lines[] = "{$relationMethodType}::make(__('{$formattedRelationName}'), '{$relationMethod}'{$relationFieldParams}){$relationOptions},";
                $addedRelations[] = $relationMethod;
            }
        }

        $hasFilledLines = collect($lines)->filter(fn($line) => !empty($line) && trim($line) != '')->isNotEmpty();
        if (!$hasFilledLines) return;

        $newLineSpace = '            ';
        $newBody = trim($returnBody) . (!empty($lines) ? "\n\n" . $newLineSpace : '') . collect($lines)->join("\n" . $newLineSpace);

        $newCode = substr($code, 0, $returnBodyStart) . "\n" . $newLineSpace . $newBody . "\n        " . substr($code, $returnBodyEnd);

        file_put_contents(app_path("Nova/{$modelNameToUse}.php"), $newCode);
    }

    private function existsField(string $code, string $fieldName): bool
    {
        foreach ($this->fieldTypes as $fieldType) {
            $exists = preg_match("/{$fieldType}::make\(__\('[\w\s\-\_\(\)\:\,\.\;\#\+\*\$\%\&\/\=\?\<\>]+'\), '{$fieldName}'(, \w+::class)?\)/", $code) === 1;
            if ($exists) return true;
        }

        if (Str::lower($fieldName) == 'id') {
            $exists = preg_match("/ID::make\(\)/", $code) === 1;
            if ($exists) return true;
        }

        return false;
    }

    private function existsRelation(string $code, string $methodName): bool
    {
        return $this->existsField($code, $methodName);
    }

    private function getBelongsToMethodInfoOfField(string $modelName, string $fieldName, array $relationMethods): ?array
    {
        $className = 'App\Models\\' . $modelName;

        /** @var Model $model */
        $model = new ($className);

        if ($fieldName == $model->getKeyName()) return null;

        foreach ($relationMethods as $methodName => $methodFieldType) {
            $relationObj = $model->{$methodName}();

            if ($relationObj instanceof EloquentBelongsTo) {
                $foreignKeyName = $relationObj->getForeignKeyName();

                if ($fieldName == $foreignKeyName) {
                    $returningModel = $relationObj->getModel()::class;
                    $returningModelParts = explode('\\', $returningModel);
                    $returningModelSimpleName = end($returningModelParts);
                    return [$methodName, $returningModelSimpleName];
                }
            }
        }

        return null;
    }

    private function guessFieldType(string $modelName, string $fieldName, array $relationMethods): string
    {
        $className = 'App\Models\\' . $modelName;

        /** @var Model $model */
        $model = new ($className);

        $casts = $model->getCasts();
        $dates = $model->getDates();

        if ($fieldName == $model->getKeyName()) return 'ID';

        if (in_array($fieldName, $dates)) return 'DateTime';

        $cast = $casts[$fieldName] ?? null;
        if ($cast != null && in_array($cast, $this->castFieldTypeMap)) {
            return $this->castFieldTypeMap[$cast];
        }

        $column = DB::connection($model->getConnectionName())
            ->getDoctrineColumn($model->getTable(), $fieldName);

        $fieldType = $this->dbTypeFieldTypeMap[$column->getType()->getName()];

        if ($fieldType == 'Text' && $column->getLength() > 350) {
            $fieldType = 'Textarea';
        }

        return $fieldType;
    }

    private function guessFieldOptions(string $modelName, string $fieldName, string $fieldType): string
    {
        /** @var Model $model */
        $model = new ('App\Models\\' . $modelName);
        $dates = $model->getDates();
        $isFillable = in_array($fieldName, $model->getFillable());
        $isGuarded = in_array($fieldName, $model->getGuarded());

        $options = [];

        $typeOptions = $this->fieldTypeOptionsMap[$fieldType] ?? null;
        if (!empty($typeOptions)) {
            $options = array_merge($options, $typeOptions);
        }

        if (in_array($fieldName, ['created_at', 'updated_at', 'deleted_at'])) {
            $options[] = "hideWhenCreating()";
            $options[] = "hideWhenUpdating()";
        }

        if (in_array($fieldName, $dates)) {
            $options[] = "sortable()";
        }

        if ($isGuarded) {
            $options[] = "readonly()";
        }

        return (!empty($options) ? '->' : '') . collect($options)->unique()->join('->');
    }

    private function getRelationOptions(string $modelName, string $methodName, string $relationType): string
    {
        $options = [];

        $typeOptions = $this->fieldTypeOptionsMap[$relationType] ?? null;
        if (!empty($typeOptions)) {
            $options = array_merge($options, $typeOptions);
        }

        return (!empty($options) ? '->' : '') . collect($options)->unique()->join('->');
    }

    private function getRelationMethodNames(string $modelName): array
    {
        $className = 'App\Models\\' . $modelName;

        $class = new ReflectionClass($className);
        $methods = collect($class->getMethods(ReflectionMethod::IS_PUBLIC));
        $methods = $methods->filter(fn(ReflectionMethod $method) => $method->hasReturnType() && $method->getDeclaringClass()->getName() == $className);

        $relationMethods = [];

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            $returnType = $method->getReturnType();
            if ($returnType == null) continue;
            if ($returnType instanceof ReflectionNamedType) {
                $returnTypeName = $returnType->getName();
                $parts = explode('\\', $returnTypeName);
                $simpleReturnTypeName = end($parts);

                if (in_array($simpleReturnTypeName, $this->returnTypeNameFieldTypeMap)) {
                    $relationMethods[$method->getName()] = $this->returnTypeNameFieldTypeMap[$simpleReturnTypeName];
                }
            } else {
                $types = $returnType->getTypes();

                /** @var ReflectionNamedType|ReflectionType $type */
                foreach ($types as $type) {
                    if ($type instanceof ReflectionNamedType) {
                        $returnTypeName = $returnType->getName();
                        $parts = explode('\\', $returnTypeName);
                        $simpleReturnTypeName = end($parts);

                        if (in_array($simpleReturnTypeName, $this->returnTypeNameFieldTypeMap)) {
                            $relationMethods[$method->getName()] = $this->returnTypeNameFieldTypeMap[$simpleReturnTypeName];
                            break;
                        }
                    }
                }
            }
        }

        return $relationMethods;
    }

    private function getPreparedModelParamsForRelationField(string $modelName, string $relationMethod): string
    {
        $className = 'App\Models\\' . $modelName;

        /** @var Model $model */
        $model = new ($className);

        $relationObj = $model->{$relationMethod}();

        if ($relationObj instanceof Relation) {
            $returningModelNameParts = explode('\\', $relationObj->getRelated()::class);
            $simpleReturningModelName = end($returningModelNameParts);
            $returningModelNameToUse = $this->getModelNameToUse($simpleReturningModelName);
            return ", {$returningModelNameToUse}::class";
        }

        return '';
    }

    private function pascal_case_to_snake_case(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }
}
