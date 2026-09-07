# Reservation de salles universitaires

Application de reservation de salles avec PHP 8, Eloquent ORM, et MySQL.

## Installation

```bash
git clone https://github.com/RouguiSy/reservation_universitaire.git
cd reservation_universitaire

composer install

cp .env.example .env

php bin/console migrate

php bin/console seed

php bin/console serve
