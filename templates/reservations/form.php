<?php ob_start(); ?>
<?php $fieldError = static fn (string $field): string => isset($errors[$field]) ? ' has-error' : ''; ?>
<section class="page-header">
    <div>
        <p class="eyebrow">Reservation</p>
        <h2>Nouvelle reservation</h2>
        <p class="muted">Planifiez un creneau dans un espace universitaire.</p>
    </div>
</section>

<form method="post" action="/reservations" class="form-card" novalidate>
    <?= csrf_field() ?>
    <div class="form-group<?= $fieldError('salle_id') ?>">
        <label for="salle_id">Salle concernee</label>
        <select id="salle_id" name="salle_id" required>
            <option value="">Selectionner une salle</option>
            <?php foreach ($salles as $salle): ?>
                <option value="<?= (int) $salle->id ?>" <?= (string) ($old['salle_id'] ?? $_GET['salle'] ?? '') === (string) $salle->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($salle->nom) ?> (<?= htmlspecialchars($salle->batiment) ?> - <?= (int) $salle->capacite ?> places)
                </option>
            <?php endforeach; ?>
        </select>
        <span class="field-error"><?= htmlspecialchars((string) ($errors['salle_id'] ?? '')) ?></span>
    </div>

    <div class="form-grid">
        <div class="form-group<?= $fieldError('responsable') ?>">
            <label for="responsable">Nom du responsable</label>
            <input id="responsable" name="responsable" value="<?= htmlspecialchars((string) ($old['responsable'] ?? '')) ?>" required maxlength="120">
            <span class="field-error"><?= htmlspecialchars((string) ($errors['responsable'] ?? '')) ?></span>
        </div>
        <div class="form-group<?= $fieldError('email') ?>">
            <label for="email">Adresse electronique</label>
            <input id="email" name="email" type="email" value="<?= htmlspecialchars((string) ($old['email'] ?? '')) ?>" required>
            <span class="field-error"><?= htmlspecialchars((string) ($errors['email'] ?? '')) ?></span>
        </div>
    </div>

    <div class="form-group<?= $fieldError('motif') ?>">
        <label for="motif">Motif de la reservation</label>
        <textarea id="motif" name="motif" rows="3" required minlength="5" maxlength="255"><?= htmlspecialchars((string) ($old['motif'] ?? '')) ?></textarea>
        <span class="field-error"><?= htmlspecialchars((string) ($errors['motif'] ?? '')) ?></span>
    </div>

    <div class="form-grid">
        <div class="form-group<?= $fieldError('date_debut') ?>">
            <label for="date_debut">Date et heure de debut</label>
            <input id="date_debut" name="date_debut" type="datetime-local" value="<?= htmlspecialchars((string) ($old['date_debut'] ?? '')) ?>" required>
            <span class="field-error"><?= htmlspecialchars((string) ($errors['date_debut'] ?? '')) ?></span>
        </div>
        <div class="form-group<?= $fieldError('date_fin') ?>">
            <label for="date_fin">Date et heure de fin</label>
            <input id="date_fin" name="date_fin" type="datetime-local" value="<?= htmlspecialchars((string) ($old['date_fin'] ?? '')) ?>" required>
            <span class="field-error"><?= htmlspecialchars((string) ($errors['date_fin'] ?? '')) ?></span>
        </div>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/reservations">Annuler</a>
        <button class="btn btn-primary" type="submit">Confirmer la reservation</button>
    </div>
</form>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
