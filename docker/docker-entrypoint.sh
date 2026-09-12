#!/bin/bash
set -e

if [ "$1" = 'apache2-foreground' ]; then
    echo "Attente de la disponibilite de MySQL..."
    for i in {1..30}; do
        if php -r "new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'mysql') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME') ?: 'app_user', getenv('DB_PASSWORD') ?: 'app_password');" >/dev/null 2>&1; then
            echo "MySQL est pret !"
            break
        fi
        echo "MySQL non pret, nouvelle tentative dans 2 secondes ($i/30)..."
        sleep 2
    done

    echo "Initialisation automatique de la base de donnees..."
    php database/migrate.php || true
    php database/seed.php || true
fi

exec "$@"
