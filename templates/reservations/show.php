<?php ob_start(); ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Detail de la reservation</p>
        <h2>Reservation #<?= (int) $reservation->id ?></h2>
        <p class="muted">Statut : <span class="badge <?= htmlspecialchars($reservation->statut) ?>"><?= $reservation->statut === 'confirmee' ? 'Confirmee' : 'Annulee' ?></span></p>
    </div>
    <div class="actions">
        <a class="btn btn-secondary" href="/reservations">Retour aux reservations</a>
        <?php if ($reservation->statut === 'confirmee'): ?>
            <form action="/reservations/<?= (int) $reservation->id ?>/cancel" method="post" style="display:inline;" onsubmit="return confirm('Annuler cette reservation ?');">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-danger">Annuler la reservation</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<div class="form-card">
    <h3>Informations de la reservation</h3>
    <p><strong>Salle :</strong> <?= htmlspecialchars($reservation->salle?->nom ?? 'Salle #' . $reservation->salle_id) ?> (<?= htmlspecialchars($reservation->salle?->batiment ?? '') ?>)</p>
    <p><strong>Responsable :</strong> <?= htmlspecialchars($reservation->responsable) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($reservation->email) ?></p>
    <p><strong>Motif :</strong> <?= htmlspecialchars($reservation->motif) ?></p>
    <p><strong>Date de debut :</strong> <?= $reservation->date_debut->format('d/m/Y H:i') ?></p>
    <p><strong>Date de fin :</strong> <?= $reservation->date_fin->format('d/m/Y H:i') ?></p>
    <p><strong>Creee le :</strong> <?= $reservation->created_at?->format('d/m/Y H:i') ?? '-' ?></p>
</div>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
