<?php ob_start(); ?>
<section class="auth-panel">
    <p class="eyebrow">Acces securise</p>
    <h2>Connexion</h2>
    <p class="muted">Connectez-vous pour gerer les reservations et les espaces.</p>
    <?php if (!empty($errors['global'])): ?><div class="flash error"><?= htmlspecialchars($errors['global']) ?></div><?php endif; ?>
    <form method="post" action="/login" class="form-card">
        <div class="form-group"><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required></div>
        <div class="form-group"><label for="password">Mot de passe</label><input id="password" name="password" type="password" required></div>
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </form>
</section>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>
