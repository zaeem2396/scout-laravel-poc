#!/bin/sh
set -e

if [ "$(id -u)" = "0" ]; then
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
fi

wait_for_database() {
    echo "Waiting for database connection..."
    until php -r "
        \$host = getenv('DB_HOST') ?: 'mysql';
        \$port = getenv('DB_PORT') ?: '3306';
        \$db = getenv('DB_DATABASE') ?: 'scout';
        \$user = getenv('DB_USERNAME') ?: 'scout';
        \$pass = getenv('DB_PASSWORD') ?: 'secret';
        try {
            new PDO(
                'mysql:host=' . \$host . ';port=' . \$port . ';dbname=' . \$db,
                \$user,
                \$pass,
                [PDO::ATTR_TIMEOUT => 3]
            );
            exit(0);
        } catch (Throwable) {
            exit(1);
        }
    "; do
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
        composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs
    fi

    wait_for_database
    run_migrations
    exec docker-php-entrypoint php-fpm
fi

exec "$@"
