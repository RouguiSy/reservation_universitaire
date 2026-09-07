# Reservation de salles universitaires

Application de reservation de salles avec PHP 8, Eloquent ORM, et MySQL.

## Installation

```bash
docker compose up -d
docker compose exec web php bin/Rougui migrate
docker compose exec web php bin/Rougui seed
