<?php ob_start(); ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Detail de la salle</p>
        <h2><?= htmlspecialchars($salle->nom) ?></h2>
        <p class="muted">Batiment <?= htmlspecialchars($salle->batiment) ?> &bull; <?= (int) $salle->capacite ?> places &bull; <?= htmlspecialchars(ucfirst($salle->type)) ?></p>
    </div>
    <div class="actions">
        <a class="btn btn-secondary" href="/salles">Retour a la liste</a>
        <a class="btn btn-primary" href="/salles/<?= (int) $salle->id ?>/edit">Modifier</a>
        <a class="btn btn-primary" href="/reservations/create?salle=<?= (int) $salle->id ?>">Reserver cette salle</a>
    </div>
</section>

<div class="table-card">
    <h3>Reservations associees</h3>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Responsable</th>
                    <th>Motif</th>
                    <th>Debut</th>
                    <th>Fin</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($salle->reservations && $salle->reservations->count() > 0): ?>
                    <?php foreach ($salle->reservations as $res): ?>
                        <tr>
                            <td><?= htmlspecialchars($res->responsable) ?></td>
                            <td><?= htmlspecialchars($res->motif) ?></td>
                            <td><?= htmlspecialchars($res->date_debut->format('d/m/Y H:i')) ?></td>
                            <td><?= htmlspecialchars($res->date_fin->format('d/m/Y H:i')) ?></td>
                            <td><span class="badge <?= $res->statut ?>"><?= htmlspecialchars($res->statut) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-state">Aucune reservation pour cette salle.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
