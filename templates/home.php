<?php ob_start(); ?>
<section class="home">
    <p class="eyebrow">Gestion des espaces</p>
    <h2>Organisez vos reservations simplement</h2>
    <p>Un espace clair pour piloter les salles universitaires et leur planning.</p>
    <div class="cards">
        <article class="card">
            <h3>Salles</h3>
            <p>Consultez les espaces, leur capacite et leur etat.</p>
            <a href="/salles" class="btn btn-primary">Voir les salles</a>
        </article>
        <article class="card">
            <h3>Reservations</h3>
            <p>Planifiez et suivez les reservations en cours.</p>
            <a href="/reservations" class="btn btn-primary">Voir les reservations</a>
        </article>
    </div>
</section>
<?php $content = ob_get_clean(); require __DIR__ . '/layout.php'; ?>
