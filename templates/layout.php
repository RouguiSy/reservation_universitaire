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
                <?php if (session()->isAdmin()): ?><li><a href="/dashboard">Dashboard</a></li><?php endif; ?>
                <?php if (session()->hasUser()): ?><li><a href="/logout">Deconnexion (<?= htmlspecialchars((string) (session()->getUser()['name'] ?? '')) ?>)</a></li><?php else: ?><li><a href="/login">Connexion</a></li><?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main class="container">
    <?php if ($flash = session()->getFlash()): ?>
        <div class="flash <?= htmlspecialchars($flash['type'] ?? '') ?>">
            <?= htmlspecialchars($flash['message'] ?? '') ?>
        </div>
    <?php endif; ?>
    <?= $content ?? '' ?>
</main>
<footer><div class="container"><p>2026 - Universite de Dakar</p></div></footer>
</body>
</html>
