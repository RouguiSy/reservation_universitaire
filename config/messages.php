<?php

declare(strict_types=1);

return [
    'auth' => [
        'invalid_credentials' => 'Email ou mot de passe incorrect.',
        'login_success' => 'Connexion reussie',
        'logout_success' => 'Deconnexion reussie',
        'unauthorized' => 'Authentification requise pour acceder a cette ressource.',
        'forbidden' => 'Acces reserve aux administrateurs.',
    ],
    'salle' => [
        'created' => 'Salle creee avec succes',
        'updated' => 'Salle mise a jour avec succes',
        'deleted' => 'Salle supprimee avec succes',
        'not_found' => 'Salle non trouvee',
        'status_updated' => 'Statut modifie avec succes',
        'conflict_dates' => 'La salle est deja reservee du :debut au :fin',
        'inactive' => 'Cette salle n\'est pas active',
        'duration_exceeded' => 'La duree de reservation ne peut pas depasser 24 heures',
        'already_exists' => 'Une salle avec ce nom existe deja.',
    ],
    'reservation' => [
        'created' => 'Reservation creee avec succes',
        'cancelled' => 'Reservation annulee avec succes',
        'not_found' => 'Reservation non trouvee',
        'already_cancelled' => 'Cette reservation est deja annulee',
        'conflict' => 'La salle est deja reservee sur ce creneau.',
    ],
    'validation' => [
        'fix_errors' => 'Veuillez corriger les champs signales.',
        'invalid' => 'Le champ :field est invalide.',
        'required' => 'Le champ :field est obligatoire.',
    ],
    'csrf' => [
        'invalid' => 'Jeton CSRF invalide ou manquant.',
    ],
    'error' => [
        'bad_request' => 'Requete invalide',
        'unauthorized' => 'Non autorise',
        'forbidden' => 'Acces interdit',
        'not_found' => 'Page non trouvee',
        'method_not_allowed' => 'Methode non autorisee',
        'conflict' => 'Conflit de ressource',
        'unprocessable_entity' => 'Donnees non traitables',
        'server_error' => 'Erreur interne du serveur',
    ],
];
