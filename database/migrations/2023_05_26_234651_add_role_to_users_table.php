<?php

use App\Models\User;
use App\Utils\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role', 50)->nullable();
            });
        }

        $novaAdmins = config('nova.admin_users') ?? [];
        $telescopeAdmins = config('telescope.admin_users', []);
        $horizonAdmins = config('horizon.admin_users', []);

        $adminEmails = collect(array_merge($novaAdmins, $telescopeAdmins, $horizonAdmins))
            ->map(fn($email) => trim($email))
            ->filter(fn($email) => !empty($email))
            ->unique();

        foreach ($adminEmails as $adminEmail) {
            $user = User::where('email', '=', $adminEmail)->first();
            if ($user == null) continue;

            $user->role = UserRole::ADMIN;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
