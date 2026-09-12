<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= (int) ($statusCode ?? 400) ?> - <?= htmlspecialchars($titre ?? 'Erreur') ?></title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>Reservation de salles</h1>
            <ul>
                <li><a href="/salles">Salles</a></li>
                <li><a href="/reservations">Reservations</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="error-page">
            <h1><?= (int) ($statusCode ?? 400) ?></h1>
            <h2><?= htmlspecialchars($titre ?? 'Une erreur est survenue') ?></h2>
            <p><?= htmlspecialchars($message ?? 'La requete n\'a pas pu etre traitee.') ?></p>
            <?php if (!empty($errors)): ?>
                <ul style="text-align: left; display: inline-block; margin: 1rem auto; color: #e53e3e;">
                    <?php foreach ($errors as $field => $err): ?>
                        <li><strong><?= htmlspecialchars((string) $field) ?> :</strong> <?= htmlspecialchars((string) $err) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <div style="margin-top: 1.5rem;">
                <a href="javascript:history.back()" class="btn btn-secondary">Retour</a>
                <a href="/" class="btn btn-primary">Accueil</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 - Universite de Dakar</p>
        </div>
    </footer>
</body>
</html>
