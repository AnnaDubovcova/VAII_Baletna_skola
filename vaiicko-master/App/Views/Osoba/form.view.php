<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \App\Models\Osoba $osoba */
/** @var array $errors */
/** @var string $formAction */

//$view->setLayout('root');

$isEdit = $formAction === 'edit';
$actionUrl = $isEdit
    ? $link->url('osoba.edit', ['id_osoba' => $osoba->getId()])
    : $link->url('osoba.create');
?>

<div class="container">
    <h2 class="my-4"><?= $isEdit ? 'Upravit osobu' : 'Pridat osobu' ?></h2>

    <?php if (!empty($errors['global'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars((string)$errors['global']) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= $actionUrl ?>">
        <div class="mb-3">
            <label for="meno" class="form-label">Meno</label>
            <input type="text" class="form-control" id="meno" name="meno"
                   value="<?= htmlspecialchars((string)$osoba->getMeno()) ?>" required>
            <?php if (!empty($errors['meno'])): ?>
                <div class="text-danger"><?= htmlspecialchars((string)$errors['meno']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="priezvisko" class="form-label">Priezvisko</label>
            <input type="text" class="form-control" id="priezvisko" name="priezvisko"
                   value="<?= htmlspecialchars((string)$osoba->getPriezvisko()) ?>" required>
            <?php if (!empty($errors['priezvisko'])): ?>
                <div class="text-danger"><?= htmlspecialchars((string)$errors['priezvisko']) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="datum_narodenia" class="form-label">Datum narodenia</label>
            <input type="date" class="form-control" id="datum_narodenia" name="datum_narodenia"
                   value="<?= htmlspecialchars((string)$osoba->getDatumNarodenia()) ?>" required>
            <?php if (!empty($errors['datum_narodenia'])): ?>
                <div class="text-danger"><?= htmlspecialchars((string)$errors['datum_narodenia']) ?></div>
            <?php endif; ?>
        </div>

        <hr>

        <h5>Kontakt studenta (ak je plnolety)</h5>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?= htmlspecialchars((string)($osoba->getEmail() ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label for="telefon" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="telefon" name="telefon"
                   value="<?= htmlspecialchars((string)($osoba->getTelefon() ?? '')) ?>">
        </div>

        <hr>

        <h5>Zakonný zastupca (ak je neplnolety)</h5>

        <div class="mb-3">
            <label for="zastupca_meno" class="form-label">Meno</label>
            <input type="text" class="form-control" id="zastupca_meno" name="zastupca_meno"
                   value="<?= htmlspecialchars((string)($osoba->getZastupcaMeno() ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label for="zastupca_priezvisko" class="form-label">Priezvisko</label>
            <input type="text" class="form-control" id="zastupca_priezvisko" name="zastupca_priezvisko"
                   value="<?= htmlspecialchars((string)($osoba->getZastupcaPriezvisko() ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label for="zastupca_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="zastupca_email" name="zastupca_email"
                   value="<?= htmlspecialchars((string)($osoba->getZastupcaEmail() ?? '')) ?>">
        </div>

        <div class="mb-3">
            <label for="zastupca_telefon" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="zastupca_telefon" name="zastupca_telefon"
                   value="<?= htmlspecialchars((string)($osoba->getZastupcaTelefon() ?? '')) ?>">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Ulozit zmeny' : 'Vytvorit' ?></button>
            <a href="<?= $link->url('osoba.index') ?>" class="btn btn-secondary">Spat</a>
        </div>
    </form>
</div>
