<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>405 - Methode non autorisee</title>
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
            <h1>405</h1>
            <h2>Methode non autorisee</h2>
            <p>La methode HTTP utilisee n'est pas autorisee pour cette URL.</p>
            <?php if (!empty($allowedMethods)): ?>
                <p>Methodes autorisees : <?= htmlspecialchars(implode(', ', $allowedMethods)) ?></p>
            <?php endif; ?>
            <a href="/" class="btn btn-primary">Retour a l'accueil</a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 - Universite de Dakar</p>
        </div>
    </footer>
</body>
</html>
