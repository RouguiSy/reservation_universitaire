<?php ob_start(); ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Planning</p>
        <h2>Reservations</h2>
        <p class="muted">Consultez les reservations enregistrees par salle.</p>
    </div>
    <a class="btn btn-primary" href="/reservations/create">Nouvelle reservation</a>
</section>

<form method="get" class="filter">
    <label for="salle">Filtrer par salle</label>
    <select id="salle" name="salle" onchange="this.form.submit()">
        <option value="">Toutes les salles</option>
        <?php foreach ($salles as $s): ?>
            <option value="<?= (int) $s->id ?>" <?= (string) ($salleId ?? '') === (string) $s->id ? 'selected' : '' ?>><?= htmlspecialchars($s->nom) ?></option>
        <?php endforeach; ?>
    </select>
</form>

<div class="table-card">
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>Salle</th>
                    <th>Responsable</th>
                    <th>Motif</th>
                    <th>Debut</th>
                    <th>Fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reservations->isEmpty()): ?>
                    <tr><td colspan="7" class="empty-state">Aucune reservation trouvee.</td></tr>
                <?php else: foreach ($reservations as $reservation): ?>
                    <tr>
                        <td><a href="/salles/<?= (int) ($reservation->salle?->id ?? $reservation->salle_id) ?>"><strong><?= htmlspecialchars($reservation->salle?->nom ?? 'Salle #' . $reservation->salle_id) ?></strong></a></td>
                        <td><?= htmlspecialchars($reservation->responsable) ?><br><span class="muted"><?= htmlspecialchars($reservation->email) ?></span></td>
                        <td><?= htmlspecialchars($reservation->motif) ?></td>
                        <td><?= $reservation->date_debut->format('d/m/Y H:i') ?></td>
                        <td><?= $reservation->date_fin->format('d/m/Y H:i') ?></td>
                        <td><span class="badge <?= htmlspecialchars($reservation->statut) ?>"><?= $reservation->statut === 'confirmee' ? 'Confirmee' : 'Annulee' ?></span></td>
                        <td>
                            <a href="/reservations/<?= (int) $reservation->id ?>" class="btn-link">Voir</a>
                            <?php if ($reservation->statut === 'confirmee'): ?>
                                <form action="/reservations/<?= (int) $reservation->id ?>/cancel" method="post" style="display:inline;" onsubmit="return confirm('Annuler cette reservation ?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-link text-danger">Annuler</button>
                                </form>
                            <?php else: ?>
                                <span class="muted">Annulee</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
