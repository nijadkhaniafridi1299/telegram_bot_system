DOMAIN="klaz.safe-pay.to"
REPO_NAME="klaz-safe-pay-master"

cd /var/www/${DOMAIN}/${REPO_NAME}

source .env

composer clearcache
composer config http-basic.nova.laravel.com ${NOVA_USERNAME} ${NOVA_PASSWORD}
composer install --no-interaction --prefer-dist --optimize-autoloader

npm install
npm run build

php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

php artisan horizon:terminate

php artisan nova:check-license

php artisan db:seed --class=CreateAdminUsers --force

php artisan app:install-telegram
