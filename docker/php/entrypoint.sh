#!/bin/sh
set -e

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

wait_for_database() {
    echo "Waiting for database connection..."
    until php artisan db:show --no-ansi >/dev/null 2>&1; do
        sleep 2
    done
    echo "Database is ready."
}

run_migrations() {
    php artisan migrate --force --no-ansi
}

if [ "$1" = "php-fpm" ]; then
    if [ ! -f vendor/autoload.php ]; then
        echo "Installing Composer dependencies..."
        composer install --no-interaction --prefer-dist --optimize-autoloader
    fi

    wait_for_database
    run_migrations
    exec docker-php-entrypoint php-fpm
fi

exec "$@"
