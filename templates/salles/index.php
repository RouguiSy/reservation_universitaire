<?php ob_start(); ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Catalogue</p>
        <h2>Salles disponibles</h2>
        <p class="muted">Gerez les espaces proposes a la reservation.</p>
    </div>
    <a class="btn btn-primary" href="/salles/create">Ajouter une salle</a>
</section>

<form method="get" class="filter search-form">
    <input name="q" value="<?= htmlspecialchars($terme ?? '') ?>" placeholder="Nom ou batiment">
    <select name="batiment">
        <option value="">Tous les batiments</option>
        <?php foreach ($batiments as $option): ?>
            <option value="<?= htmlspecialchars($option) ?>" <?= ($batiment ?? '') === $option ? 'selected' : '' ?>><?= htmlspecialchars($option) ?></option>
        <?php endforeach; ?>
    </select>
    <select name="type">
        <option value="">Tous les types</option>
        <?php foreach (['cours', 'amphitheatre', 'laboratoire', 'informatique', 'reunion'] as $option): ?>
            <option value="<?= $option ?>" <?= ($type ?? '') === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-secondary" type="submit">Rechercher</button>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Batiment</th>
                    <th>Capacite</th>
                    <th>Type</th>
                    <th>Etat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($salles->isEmpty()): ?>
                    <tr><td colspan="6" class="empty-state">Aucune salle enregistree.</td></tr>
                <?php else: foreach ($salles as $salle): ?>
                    <tr>
                        <td><a href="/salles/<?= (int) $salle->id ?>"><strong><?= htmlspecialchars($salle->nom) ?></strong></a></td>
                        <td><?= htmlspecialchars($salle->batiment) ?></td>
                        <td><?= (int) $salle->capacite ?> places</td>
                        <td><?= htmlspecialchars(ucfirst($salle->type)) ?></td>
                        <td><span class="badge <?= $salle->active ? 'active' : 'inactive' ?>"><?= $salle->active ? 'Active' : 'Inactive' ?></span></td>
                        <td class="actions">
                            <a href="/salles/<?= (int) $salle->id ?>" class="btn-link">Voir</a>
                            <a href="/salles/<?= (int) $salle->id ?>/edit" class="btn-link">Modifier</a>
                            <form action="/salles/toggle/<?= (int) $salle->id ?>" method="post" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-link"><?= $salle->active ? 'Desactiver' : 'Activer' ?></button>
                            </form>
                            <form action="/salles/delete/<?= (int) $salle->id ?>" method="post" style="display:inline;" onsubmit="return confirm('Supprimer cette salle ?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn-link text-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (method_exists($salles, 'lastPage') && $salles->lastPage() > 1): ?>
    <nav class="pagination">
        <span>Page <?= $salles->currentPage() ?> / <?= $salles->lastPage() ?></span>
        <?php if ($salles->currentPage() > 1): ?>
            <a href="?q=<?= urlencode($terme ?? '') ?>&batiment=<?= urlencode($batiment ?? '') ?>&type=<?= urlencode($type ?? '') ?>&page=<?= $salles->currentPage() - 1 ?>">Precedente</a>
        <?php endif; ?>
        <?php if ($salles->hasMorePages()): ?>
            <a href="?q=<?= urlencode($terme ?? '') ?>&batiment=<?= urlencode($batiment ?? '') ?>&type=<?= urlencode($type ?? '') ?>&page=<?= $salles->currentPage() + 1 ?>">Suivante</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>

<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
