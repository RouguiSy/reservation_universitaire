#  Carnet de Campus — Réservation de salles universitaires

Application web de gestion des salles et des réservations de l'université,
développée en **PHP orienté objet**, sans framework complet, avec des
composants spécialisés installés via Composer :

| Besoin | Bibliothèque |
|---|---|
| Routeur | `nikic/fast-route` |
| Validation | `respect/validation` |
| ORM | `illuminate/database` (Eloquent, via `Capsule\Manager`) |
| Conteneur d'injection | `php-di/php-di` |
| Variables d'environnement | `vlucas/phpdotenv` |

Design original « Carnet de Campus » : palette papier + terracotta,
typographie éditoriale (Fraunces/Inter), écrit à la main, **sans**
framework CSS (Bootstrap, Tailwind...).

> Ce dépôt suit une progression pédagogique par étapes. Voir
> `CHANGELOG.md` pour le détail, et `git log --oneline --decorate --tags`
> pour l'historique complet.

---

## Prérequis

- PHP ≥ 8.2 avec les extensions `pdo_mysql`, `mbstring`, `json`
- Composer 2
- Un serveur MySQL 8 (ou compatible) accessible

## Installation

```bash
git clone <url-du-depot> reservation-salles
cd reservation-salles

# 1. Installer les dépendances PHP
composer install

# 2. Configurer l'environnement
cp .env.example .env
# puis éditez .env avec vos identifiants MySQL (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# 3. Créer la base de données vide côté MySQL, par exemple :
mysql -u root -p -e "CREATE DATABASE reservation_salles CHARACTER SET utf8mb4;"

# 4. Créer les tables
composer run migrate
# équivalent à : php database/migrate.php

# 5. Ajouter les données de démonstration (5 salles)
composer run seed
# équivalent à : php database/seed.php

# 6. Lancer le serveur de développement PHP
composer run serve
# équivalent à : php -S 127.0.0.1:8000 -t public
```

Rendez-vous ensuite sur <http://127.0.0.1:8000>.

> Ce projet a été rédigé dans un environnement sandbox sans accès à
> Packagist : le code source complet est fourni et relu attentivement,
> mais **`composer install` n'a pas pu être exécuté ici**. Exécutez-le
> vous-même à la première installation ; c'est également ce que fera
> n'importe quel correcteur clonant le dépôt.

## Lancer les tests

```bash
composer run test
# équivalent à : vendor/bin/phpunit
```

- `tests/Unit` : règles métier et validation, **sans base de données**
  (doublures en mémoire, voir `tests/Support/`).
- `tests/Integration` : Eloquent réel sur une base **SQLite en mémoire**
  (aucun MySQL requis pour lancer les tests).

## Arborescence

```
reservation-salles/
├── config/            # database.php, container.php (PHP-DI), bootstrap.php
├── database/          # migrations/, seed.php, migrate.php
├── public/            # point d'entrée unique (index.php) + assets/style.css
├── routes/            # web.php (déclaration des routes FastRoute)
├── src/
│   ├── Controller/    # SalleController, ReservationController, HomeController
│   ├── DTO/           # CreerSalleDTO, CreerReservationDTO
│   ├── Database/       # CapsuleFactory (démarrage d'Eloquent)
│   ├── Exception/      # exceptions métier (SalleIndisponibleException...)
│   ├── Http/           # Request (petit wrapper de $_GET/$_POST)
│   ├── Model/          # Salle, Reservation (Eloquent)
│   ├── Repository/     # interfaces + implémentations Eloquent
│   ├── Service/        # CreerReservationService, AnnulerReservationService
│   ├── Validation/     # ValidatorInterface, ValidationResult, *Validator
│   └── View/           # ViewRenderer (moteur de rendu minimaliste)
├── templates/          # vues PHP pures (layout, salle/, reservation/, error/)
├── tests/
│   ├── Unit/           # règles métier + validation, sans base
│   ├── Integration/    # Eloquent réel sur SQLite en mémoire
│   └── Support/        # doublures InMemory*Repository
├── docs/diagramme-de-classes.md
└── ARCHITECTURE.md     # analyse des choix architecturaux (MVC, SOLID...)
```

## Règles métier (rappel)

Une réservation n'est acceptée que si :
1. la salle existe et est active ;
2. la date de début précède la date de fin ;
3. la durée ne dépasse pas 4 heures ;
4. la réservation commence dans le futur ;
5. aucune réservation **confirmée** ne chevauche la période demandée.

Une réservation annulée ne bloque plus jamais la salle.

## Documentation complémentaire

- [`ARCHITECTURE.md`](./ARCHITECTURE.md) — MVC, Front Controller, Router,
  Validator, DTO, ORM/Active Record, Repository, Service, injection par
  constructeur, conteneur, autowiring, IoC, principes SOLID : rôle,
  avantages, limites, extraits de code.
- [`docs/diagramme-de-classes.md`](./docs/diagramme-de-classes.md) —
  diagramme de classes et schéma de base de données (Mermaid).
- [`CHANGELOG.md`](./CHANGELOG.md) — historique détaillé, étape par étape.
