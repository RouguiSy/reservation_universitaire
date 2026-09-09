<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation de salles</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="/">Reservation de salles</a>
        <nav>
            <ul>
                <li><a href="/salles">Salles</a></li>
                <li><a href="/reservations">Reservations</a></li>
                <li><a href="/salles/create">Nouvelle salle</a></li>
                <li><a href="/reservations/create">Nouvelle reservation</a></li>
                <?php if (($_SESSION['user']['role'] ?? null) === 'admin'): ?><li><a href="/dashboard">Dashboard</a></li><?php endif; ?>
                <?php if (isset($_SESSION['user'])): ?><li><a href="/logout">Deconnexion (<?= htmlspecialchars($_SESSION['user']['name']) ?>)</a></li><?php else: ?><li><a href="/login">Connexion</a></li><?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main class="container">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash <?= htmlspecialchars($_SESSION['flash']['type']) ?>">
            <?= htmlspecialchars($_SESSION['flash']['message']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>
    <?= $content ?? '' ?>
</main>
<footer><div class="container"><p>2026 - Universite de Dakar</p></div></footer>
</body>
</html>
