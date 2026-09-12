<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Erreur interne du serveur</title>
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
            <h1>500</h1>
            <h2>Erreur interne du serveur</h2>
            <p><?= htmlspecialchars($message ?? 'Une erreur inattendue est survenue sur le serveur.') ?></p>
            <?php if (!empty($debug) && !empty($exception)): ?>
                <div class="code-trace" style="text-align: left; background: #1e1e1e; color: #f8f8f2; padding: 1rem; border-radius: 8px; margin: 1.5rem 0; overflow-x: auto; font-family: monospace; font-size: 0.85rem;">
                    <p><strong>Exception :</strong> <?= htmlspecialchars(get_class($exception)) ?></p>
                    <p><strong>Message :</strong> <?= htmlspecialchars($exception->getMessage()) ?></p>
                    <p><strong>Fichier :</strong> <?= htmlspecialchars($exception->getFile()) ?>:<?= $exception->getLine() ?></p>
                    <pre style="margin-top: 0.5rem; white-space: pre-wrap;"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
                </div>
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
