# Journal des modifications

## [v0.2.0] - 2026-09-07
### Ajoute
- Configuration d'Eloquent ORM (config/database.php)
- Migration pour la table salles
- Migration pour la table reservations
- Seed des donnees initiales (5 salles)
- Model Salle
- Model Reservation
- Scripts migrate.php, seed.php, rollback.php
- Docker Compose avec MySQL et phpMyAdmin
- Commande: docker compose up -d
- Fichier bin/Rougui pour les commandes

### Modifie
- .env avec les identifiants MySQL Docker
- docker-compose.yml avec les services mysql, phpmyadmin, web
- README.md avec les instructions Docker
- CHANGELOG.md avec le suivi des versions

## [v0.1.0] - 2026-09-07
### Ajoute
- Configuration de Composer
- Autoloading PSR-4
- Structure des dossiers (src/, config/, database/, tests/, bin/, public/, templates/)
- Fichier bin/console pour les commandes
- Fichier .env.example pour la configuration
- Commandes: php bin/console migrate, seed, serve
- Scripts Composer: composer migrate, composer seed, composer rollback

### Modifie
- README.md avec les instructions d'installation
- CHANGELOG.md avec le suivi des versions

## [v0.0.0] - 2026-09-07
### Ajoute
- Initialisation du depot
- Structure de base du projet
- .gitignore
- README.md
- CHANGELOG.md
- docker-compose.yml
- Dockerfile
