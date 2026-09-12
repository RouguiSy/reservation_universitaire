# Système de Gestion des Réservations de Salles Universitaires

Application web développée en PHP orienté objet avec composants spécialisés (sans framework complet) : FastRoute, Respect\Validation, Eloquent ORM, PHP-DI et Dotenv.

---

## 1. Prérequis
- **PHP** >= 8.1 avec extensions : `pdo`, `pdo_mysql`, `pdo_sqlite`, `mbstring`, `intl`
- **Composer** 2.x
- **MySQL** 8.x ou MariaDB (ou Docker & Docker Compose)

---

## 2. Installation des dépendances

```bash
composer install
```

---

## 3. Configuration de la base de données

1. Copier le fichier d'environnement modèle :
   ```bash
   cp .env.example .env
   ```
2. Renseigner vos paramètres dans `.env` :
   ```env
   APP_ENV=development
   APP_DEBUG=true

   DB_DRIVER=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=reservation_salles
   DB_USERNAME=root
   DB_PASSWORD=secret
   ```

---

## 4. Création des tables (Migrations)

Exécuter les migrations pour générer les tables `salles`, `reservations` et `users` :
```bash
php database/migrate.php
# ou via la commande console :
composer migrate
```

---

## 5. Ajout des données initiales (Seeders)

Insérer les 5 salles de référence requises (Amphithéâtre A, Salle B12, Laboratoire Chimie, etc.) de manière idempotente (sans doublons) :
```bash
php database/seed.php
# ou via :
composer seed
```

---

## 6. Lancement du serveur

### Option A : Serveur intégré PHP (développement local)
```bash
php -S 127.0.0.1:8000 -t public
```
L'application est alors accessible à l'adresse : `http://127.0.0.1:8000`

### Option B : Avec Docker Compose
```bash
docker compose -f docker/docker-compose.yml up -d
# ou
cd docker && docker compose up -d
```
L'application est alors accessible sur : `http://localhost:8080`
Le gestionnaire phpMyAdmin est accessible sur : `http://localhost:8081`

---

## 7. Exécution des tests

Lancer la suite complète de tests (tests unitaires des validateurs, DTO, services métier, SessionManager, CSRF et tests d'intégration Eloquent) :
```bash
composer test
```

### Détail des suites de test :
- **Tests unitaires de validation :** `SalleValidatorTest`, `ReservationValidatorTest`, `AbstractValidatorTest`, `ValidationResultTest`
- **Tests unitaires métier :** `CreerReservationServiceTest`, `AnnulerReservationServiceTest` (avec doublures `InMemory`)
- **Tests de composants techniques :** `SessionManagerTest`, `CsrfServiceTest`, `ContentNegotiationTest`
- **Tests d'intégration Eloquent :** `ReservationIntegrationTest` (SQLite in-memory)

---

## 8. Support multi-format (HTML & API JSON)

L'application prend en charge la négociation de contenu pour toutes les routes :
- **Navigateur web (HTML) :** Consultation normale via l'interface utilisateur (`/salles`, `/reservations`).
- **Paramètre URL :** Ajout de `?format=json` (ex: `http://localhost:8000/salles?format=json`).
- **En-tête HTTP :** `Accept: application/json` pour les clients API (Fetch, Curl, Postman).
- Les erreurs de validation retournent un code statut `422 Unprocessable Entity` avec les erreurs indexées par champ.

---

## 9. Analyse architecturale & Principes SOLID
Consulter le fichier [ARCHITECTURE.md](ARCHITECTURE.md) pour l'analyse complète :
- MVC & Front Controller
- Table-driven Validation sans cascade de `if/else` (SRP)
- DTO typés & immuables
- Repositories & Inversion de dépendance
- SessionManager et prévention CSRF
