<?php ob_start(); ?>
<?php
$isEdit = isset($salle) && !empty($salle->id);
$actionUrl = $isEdit ? "/salles/{$salle->id}/edit" : "/salles";
$title = $isEdit ? "Modifier la salle : " . htmlspecialchars($salle->nom) : "Ajouter une salle";
$fieldError = static fn (string $field): string => isset($errors[$field]) ? ' has-error' : '';
?>
<section class="page-header">
    <div>
        <p class="eyebrow">Configuration</p>
        <h2><?= $title ?></h2>
        <p class="muted">Renseignez les caracteristiques de l'espace.</p>
    </div>
</section>

<form method="post" action="<?= $actionUrl ?>" class="form-card" novalidate>
    <?= csrf_field() ?>
    <div class="form-group<?= $fieldError('nom') ?>">
        <label for="nom">Nom de la salle</label>
        <input id="nom" name="nom" value="<?= htmlspecialchars((string) ($old['nom'] ?? '')) ?>" required maxlength="100">
        <span class="field-error"><?= htmlspecialchars((string) ($errors['nom'] ?? '')) ?></span>
    </div>

    <div class="form-group<?= $fieldError('batiment') ?>">
        <label for="batiment">Batiment</label>
        <input id="batiment" name="batiment" value="<?= htmlspecialchars((string) ($old['batiment'] ?? '')) ?>" required maxlength="100">
        <span class="field-error"><?= htmlspecialchars((string) ($errors['batiment'] ?? '')) ?></span>
    </div>

    <div class="form-grid">
        <div class="form-group<?= $fieldError('capacite') ?>">
            <label for="capacite">Capacite</label>
            <input id="capacite" name="capacite" value="<?= htmlspecialchars((string) ($old['capacite'] ?? '')) ?>" type="number" min="1" required>
            <span class="field-error"><?= htmlspecialchars((string) ($errors['capacite'] ?? '')) ?></span>
        </div>
        <div class="form-group<?= $fieldError('type') ?>">
            <label for="type">Type</label>
            <select id="type" name="type" required>
                <?php foreach (['cours' => 'Cours', 'amphitheatre' => 'Amphitheatre', 'laboratoire' => 'Laboratoire', 'informatique' => 'Informatique', 'reunion' => 'Reunion'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= ($old['type'] ?? 'cours') === $value ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <span class="field-error"><?= htmlspecialchars((string) ($errors['type'] ?? '')) ?></span>
        </div>
    </div>

    <label class="checkbox">
        <input type="checkbox" name="active" value="1" <?= !isset($old['active']) || $old['active'] ? 'checked' : '' ?>> Salle active
    </label>

    <div class="form-actions">
        <a class="btn btn-secondary" href="/salles">Annuler</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Enregistrer les modifications' : 'Enregistrer la salle' ?></button>
    </div>
</form>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
