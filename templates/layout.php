<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation de salles</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <h1>🏛️ Réservation de salles</h1>
            <ul>
                <li><a href="/salles">Salles</a></li>
                <li><a href="/reservations">Réservations</a></li>
                <li><a href="/salles/create">+ Nouvelle salle</a></li>
                <li><a href="/reservations/create">+ Nouvelle réservation</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash <?= $_SESSION['flash']['type'] ?>">
                <?= htmlspecialchars($_SESSION['flash']['message']) ?>
            </div>
            <?php unset($_SESSION['flash']); ?>
        <?php endif; ?>

        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 - Université de Dakar</p>
        </div>
    </footer>
</body>
</html>
