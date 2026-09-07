# Journal des modifications

## [v0.4.0] - 2026-09-07
### Ajoute
- Nouvelles salles (10 salles au total)
  - Amphitheatre B (200 places)
  - Salle B14 (35 places)
  - Laboratoire Physique (20 places)
  - Salle Informatique 2 (28 places)
  - Salle de reunion 2 (8 places) - inactive
- Reservations de test (8 reservations)
  - 2 reservations pour Amphitheatre A
  - 1 reservation pour Salle B12
  - 1 reservation pour Salle B14
  - 1 reservation pour Laboratoire Chimie
  - 1 reservation pour Laboratoire Physique
  - 1 reservation pour Salle Informatique 1
  - 1 reservation annulee pour Salle de reunion
- Donnees realistes avec professeurs et motifs

### Modifie
- database/seed.php avec des donnees enrichies
- README.md avec la liste des salles et reservations
- CHANGELOG.md avec le suivi des versions

## [v0.3.0] - 2026-09-07
### Ajoute
- Model Salle avec methodes:
  - reservations() : relation hasMany
  - reservationsConfirmees() : filtre reservations confirmees
  - estActive() : verifier si la salle est active
  - activer() : activer la salle
  - desactiver() : desactiver la salle
- Model Reservation avec methodes:
  - salle() : relation belongsTo
  - estConfirmee() : verifier si la reservation est confirmee
  - estAnnulee() : verifier si la reservation est annulee
  - annuler() : annuler la reservation
  - confirmer() : confirmer la reservation
  - chevaucheAvec() : verifier le chevauchement avec une autre reservation
  - getDureeEnHeures() : calculer la duree en heures
- Casts pour les dates (DateTimeImmutable) et booleens
- Attributs DateDebut et DateFin

## [v0.2.0] - 2026-09-07
### Ajoute
- Configuration d'Eloquent ORM (config/database.php)
- Migration pour la table salles
- Migration pour la table reservations
- Seed des donnees initiales (5 salles)
- Docker Compose avec MySQL et phpMyAdmin
- Commande: docker compose up -d
- Fichier bin/Rougui pour les commandes

## [v0.1.0] - 2026-09-07
### Ajoute
- Configuration de Composer
- Autoloading PSR-4
- Structure des dossiers
- Fichier bin/console (renomme en bin/Rougui)
- Fichier .env.example
- Commandes: php bin/Rougui migrate, seed, serve

## [v0.0.0] - 2026-09-07
### Ajoute
- Initialisation du depot
- Structure de base du projet
- .gitignore
- README.md
- CHANGELOG.md
- docker-compose.yml
- Dockerfile
