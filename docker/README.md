# Configuration Docker

Ce dossier regroupe tous les fichiers de configuration nécessaires pour exécuter l'application avec Docker.

## Structure du dossier

- `Dockerfile` : Définition de l'image PHP 8.2 Apache + extensions + Composer
- `docker-compose.yml` : Orchestration des conteneurs MySQL 8.0, Web (Apache/PHP) et phpMyAdmin
- `docker-entrypoint.sh` : Script de démarrage exécutant automatiquement les migrations et seeds
- `vhost.conf` : Configuration Apache pour servir le dossier `public/` avec réécriture d'URL
- `.dockerignore` / `Dockerfile.dockerignore` : Fichiers exclus du build Docker (vendor, git, env)

## Commandes d'exécution

### 1. Démarrer l'environnement
Depuis la racine du projet :
```bash
docker compose -f docker/docker-compose.yml up --build -d
```
Ou depuis ce dossier :
```bash
cd docker
docker compose up --build -d
```

### 2. Vérifier l'état des conteneurs
```bash
docker compose -f docker/docker-compose.yml ps
```

### 3. Consulter les logs du serveur web
```bash
docker compose -f docker/docker-compose.yml logs -f web
```

### 4. Exécuter une commande dans le conteneur web
```bash
# Lancer les tests unitaires
docker compose -f docker/docker-compose.yml exec web vendor/bin/phpunit

# Lancer les migrations manuellement
docker compose -f docker/docker-compose.yml exec web php database/migrate.php

# Lancer les seeders manuellement
docker compose -f docker/docker-compose.yml exec web php database/seed.php
```

### 5. Arrêter les conteneurs
```bash
docker compose -f docker/docker-compose.yml down
```

## Accès aux services

- **Application Web** : [http://localhost:8080](http://localhost:8080)
- **phpMyAdmin** : [http://localhost:8081](http://localhost:8081)
  - Serveur : `mysql`
  - Utilisateur : `app_user` / `app_password` ou `root` / `root`
