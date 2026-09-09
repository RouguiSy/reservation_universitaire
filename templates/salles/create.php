<?php ob_start(); ?>
<?php $fieldError = static fn (string $field): string => isset($errors[$field]) ? ' has-error' : ''; ?>
<section class="page-header"><div><p class="eyebrow">Configuration</p><h2>Ajouter une salle</h2><p class="muted">Renseignez les caracteristiques de l'espace.</p></div></section>
<form method="post" action="/salles/store" class="form-card" novalidate>
	<?= csrf_field() ?>
	<div class="form-group<?= $fieldError('nom') ?>"><label for="nom">Nom de la salle</label><input id="nom" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required maxlength="100"><span class="field-error"><?= htmlspecialchars($errors['nom'] ?? '') ?></span></div>
	<div class="form-group<?= $fieldError('batiment') ?>"><label for="batiment">Batiment</label><input id="batiment" name="batiment" value="<?= htmlspecialchars($old['batiment'] ?? '') ?>" required maxlength="100"><span class="field-error"><?= htmlspecialchars($errors['batiment'] ?? '') ?></span></div>
	<div class="form-grid"><div class="form-group<?= $fieldError('capacite') ?>"><label for="capacite">Capacite</label><input id="capacite" name="capacite" value="<?= htmlspecialchars($old['capacite'] ?? '') ?>" type="number" min="1" required><span class="field-error"><?= htmlspecialchars($errors['capacite'] ?? '') ?></span></div><div class="form-group<?= $fieldError('type') ?>"><label for="type">Type</label><select id="type" name="type" required><?php foreach (['cours' => 'Cours', 'amphitheatre' => 'Amphitheatre', 'laboratoire' => 'Laboratoire', 'informatique' => 'Informatique', 'reunion' => 'Reunion'] as $value => $label): ?><option value="<?= $value ?>" <?= ($old['type'] ?? 'cours') === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select><span class="field-error"><?= htmlspecialchars($errors['type'] ?? '') ?></span></div></div>
	<label class="checkbox"><input type="checkbox" name="active" value="1" <?= !isset($old['active']) || $old['active'] ? 'checked' : '' ?>> Salle active</label>
	<div class="form-actions"><a class="btn btn-secondary" href="/salles">Annuler</a><button class="btn btn-primary" type="submit">Enregistrer la salle</button></div>
</form>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
