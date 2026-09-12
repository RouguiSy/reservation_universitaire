<?php ob_start(); ?>
<section class="auth-panel">
    <p class="eyebrow">Acces securise</p>
    <h2>Connexion</h2>
    <p class="muted">Connectez-vous pour gerer les reservations et les espaces.</p>

    <div class="form-card" style="background: #fff3cd; border: 1px solid #ffc107; margin-bottom: 20px;">
        <p style="margin: 0 0 10px 0; color: #856404; font-weight: bold;">Identifiants de demonstration</p>

        <p style="margin: 0 0 5px 0;"><strong>Administrateur :</strong></p>
        <p style="margin: 0 0 15px 0;">
            Email : <code>admin@example.com</code><br>
            Mot de passe : <code>admin123</code>
        </p>

        <p style="margin: 0 0 5px 0;"><strong>Responsable :</strong></p>
        <p style="margin: 0;">
            Email : <code>responsable@example.com</code><br>
            Mot de passe : <code>responsable123</code>
        </p>
    </div>

    <?php if (!empty($errors['global'])): ?><div class="flash error"><?= htmlspecialchars($errors['global']) ?></div><?php endif; ?>
    <form method="post" action="/login" class="form-card">
        <?= csrf_field() ?>
        <div class="form-group"><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required></div>
        <div class="form-group"><label for="password">Mot de passe</label><input id="password" name="password" type="password" required></div>
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </form>
</section>
<?php $content = ob_get_clean(); require dirname(__DIR__) . '/layout.php'; ?>