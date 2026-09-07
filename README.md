# Reservation de salles universitaires

## Injection de dépendances avec PHP-DI

### Container PHP-DI
- Autowiring automatique
- Bindings pour les interfaces
- Injection dans les contrôleurs
- Injection dans les services

### Bindings

| Interface | Implementation |
|-----------|----------------|
| SalleRepositoryInterface | EloquentSalleRepository |
| ReservationRepositoryInterface | EloquentReservationRepository |

### Auto-injection
- CreerReservationService → repositories
- AnnulerReservationService → reservationRepository
- SalleController → salleRepository
- ReservationController → repositories + services

## Versions

- v0.0.0 : Initialisation
- v0.1.0 : Composer
- v0.2.0 : Eloquent
- v0.3.0 : Modeles
- v0.4.0 : Donnees initiales
- v0.5.0 : Validation
- v0.6.0 : DTO
- v0.7.0 : Repositories
- v0.8.0 : Services metier
- v0.9.0 : Controleurs et vues
- v0.10.0 : FastRoute
- v0.11.0 : PHP-DI

## Auteurs

Rougui Sy
