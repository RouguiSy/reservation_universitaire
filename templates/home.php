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
            <h1>Réservation de salles</h1>
            <ul>
                <li><a href="/salles">Salles</a></li>
                <li><a href="/reservations">Réservations</a></li>
                <li><a href="/salles/create">Nouvelle salle</a></li>
                <li><a href="/reservations/create">Nouvelle réservation</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <div class="home">
            <h2>Bienvenue sur la plateforme de réservation de salles</h2>
            <p>Gérez facilement vos salles et réservations.</p>

            <div class="cards">
                <div class="card">
                    <h3>Salles</h3>
                    <p>Consultez et gérez toutes les salles disponibles.</p>
                    <a href="/salles" class="btn btn-primary">Voir les salles</a>
                </div>
                <div class="card">
                    <h3>Réservations</h3>
                    <p>Créez et gérez vos réservations de salles.</p>
                    <a href="/reservations" class="btn btn-primary">Voir les réservations</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 - Université de Dakar</p>
        </div>
    </footer>
</body>
</html>
