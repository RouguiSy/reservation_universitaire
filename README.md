# Reservation de salles universitaires

## Architecture

### Repositories
- **SalleRepositoryInterface** : Contrat pour les operations sur les salles
- **EloquentSalleRepository** : Implementation Eloquent
- **ReservationRepositoryInterface** : Contrat pour les operations sur les reservations
- **EloquentReservationRepository** : Implementation Eloquent

### Principes
- Open/Closed : Les repositories sont fermes a la modification, ouverts a l'extension
- Inversion de dependances : Les services dependent des interfaces

### Methodes disponibles

#### SalleRepository
- trouver(int $id)
- trouverParNomEtBatiment(string $nom, string $batiment)
- toutes()
- actives()
- creer(array $donnees)
- mettreAJour(Salle $salle, array $donnees)
- supprimer(Salle $salle)

#### ReservationRepository
- trouver(int $id)
- trouverParSalle(int $salleId)
- trouverParSalleEtPeriode(int $salleId, DateTime $debut, DateTime $fin)
- trouverEnCoursParSalle(int $salleId)
- creer(array $donnees)
- annuler(Reservation $reservation)
- supprimer(Reservation $reservation)

## Versions

- v0.0.0 : Initialisation
- v0.1.0 : Composer
- v0.2.0 : Eloquent
- v0.3.0 : Modeles
- v0.4.0 : Donnees initiales
- v0.5.0 : Validation
- v0.6.0 : DTO
- v0.7.0 : Repositories

## Auteurs

Rougui Sy
