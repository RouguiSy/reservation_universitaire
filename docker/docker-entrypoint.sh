#!/bin/bash
set -e

if [ "$1" = 'apache2-foreground' ]; then
    echo "=== Demarrage de l'application ==="
    echo "Attente de la disponibilite de PostgreSQL sur $DB_HOST:$DB_PORT..."

    for i in {1..30}; do
        if php -r "
            try {
                new PDO(
                    'pgsql:host=' . (getenv('DB_HOST') ?: 'localhost') .
                    ';port=' . (getenv('DB_PORT') ?: '5432') .
                    ';dbname=' . (getenv('DB_DATABASE') ?: 'reservation_db_aswy'),
                    getenv('DB_USERNAME') ?: 'app_user',
                    getenv('DB_PASSWORD') ?: ''
                );
                exit(0);
            } catch (Exception \$e) {
                exit(1);
            }
        " >/dev/null 2>&1; then
            echo "PostgreSQL est pret !"
            break
        fi
        echo "PostgreSQL non pret, nouvelle tentative dans 2 secondes ($i/30)..."
        sleep 2
    done

    echo "=== Initialisation automatique de la base de donnees ==="
    php bin/Rougui migrate || true
    php bin/Rougui seed || true
fi

exec "$@"
