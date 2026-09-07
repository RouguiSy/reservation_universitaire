# Reservation de salles universitaires

## Services metier

### CreerReservationService
- Verifie l'existence de la salle
- Verifie que la salle est active
- Verifie que la duree ne depasse pas 24h
- Verifie l'absence de conflits
- Cree la reservation confirmee

### AnnulerReservationService
- Verifie l'existence de la reservation
- Verifie que la reservation n'est pas deja annulee
- Annule la reservation

### Exceptions
- SalleIndisponibleException::conflit()
- SalleIndisponibleException::salleInactive()
- SalleIndisponibleException::dureeExcessive()
- SalleIndisponibleException::salleNonTrouvee()

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

## Auteurs

Rougui Sy
