<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $errors */
/** @var string $email */
?>

<h1 class="page-title">Registrácia</h1>

<form method="post" action="<?= $link->url('auth.register') ?>" class="mt-3" style="max-width: 520px;">
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input
            type="email"
            name="email"
            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            value="<?= htmlspecialchars((string)$email) ?>"
            required
        >
        <?php if (isset($errors['email'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['email']) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Heslo</label>
        <input
            type="password"
            name="password"
            class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
            required
        >
        <?php if (isset($errors['password'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['password']) ?></div>
        <?php endif; ?>
        <div class="form-text">Minimálne 8 znakov.</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Heslo znovu</label>
        <input
            type="password"
            name="password2"
            class="form-control <?= isset($errors['password2']) ? 'is-invalid' : '' ?>"
            required
        >
        <?php if (isset($errors['password2'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['password2']) ?></div>
        <?php endif; ?>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Zaregistrovať sa</button>
        <a class="btn btn-outline-secondary" href="<?= $link->url('auth.login') ?>">Mám účet</a>
    </div>
</form>
