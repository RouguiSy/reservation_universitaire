# Reservation de salles universitaires

Application de reservation de salles avec PHP 8, Eloquent ORM, et MySQL.

## Installation

### Avec Docker (Recommandé)

```bash
# Cloner le projet
git clone https://github.com/RouguiSy/reservation_universitaire.git
cd reservation_universitaire

# Démarrer les conteneurs
docker compose up -d

# Executer les migrations
docker compose exec web php bin/Rougui migrate

# Ajouter les donnees initiales
docker compose exec web php bin/Rougui seed
