<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Osoba $osoba */
/** @var array $errors */
/** @var string $formAction */

$isEdit = $formAction === 'edit';
$actionUrl = $isEdit
        ? $link->url('osoba.edit', ['id_osoba' => $osoba->getId()])
        : $link->url('osoba.create');
?>

<h1 class="page-title">
    <?= $isEdit ? 'Upraviť osobu' : 'Pridať osobu' ?>
</h1>

<?php if (!empty($errors['global'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars((string)$errors['global']) ?>
    </div>
<?php endif; ?>

<form method="post" id="osoba-form" action="<?= $actionUrl ?>" novalidate>

    <!-- ÚDAJE ŠTUDENTA -->
    <div class="card mb-4">
        <div class="card-header">
            Údaje študenta
        </div>
        <div class="card-body">

            <div class="row">
                <!-- MENO -->
                <div class="mb-3 col-md-6">
                    <label for="meno" class="form-label">
                        Meno <span class="text-danger">*</span>
                    </label>
                    <input
                            type="text"
                            id="meno"
                            name="meno"
                            class="form-control <?= isset($errors['meno']) ? 'is-invalid' : '' ?>"
                            maxlength="80"
                            required
                            value="<?= htmlspecialchars((string)($osoba->getMeno() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['meno']) ? htmlspecialchars((string)$errors['meno']) : '' ?>
                    </div>
                </div>

                <!-- PRIEZVISKO -->
                <div class="mb-3 col-md-6">
                    <label for="priezvisko" class="form-label">
                        Priezvisko <span class="text-danger">*</span>
                    </label>
                    <input
                            type="text"
                            id="priezvisko"
                            name="priezvisko"
                            class="form-control <?= isset($errors['priezvisko']) ? 'is-invalid' : '' ?>"
                            maxlength="80"
                            required
                            value="<?= htmlspecialchars((string)($osoba->getPriezvisko() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['priezvisko']) ? htmlspecialchars((string)$errors['priezvisko']) : '' ?>
                    </div>
                </div>
            </div>

            <!-- DÁTUM NARODENIA -->
            <div class="mb-3">
                <label for="datum_narodenia" class="form-label">
                    Dátum narodenia <span class="text-danger">*</span>
                </label>
                <input
                        type="date"
                        id="datum_narodenia"
                        name="datum_narodenia"
                        class="form-control <?= isset($errors['datum_narodenia']) ? 'is-invalid' : '' ?>"
                        required
                        value="<?= htmlspecialchars((string)($osoba->getDatumNarodenia() ?? '')) ?>"
                >
                <div class="invalid-feedback">
                    <?= isset($errors['datum_narodenia']) ? htmlspecialchars((string)$errors['datum_narodenia']) : '' ?>
                </div>
            </div>

        </div>
    </div>


    <!-- KONTAKT ŠTUDENTA -->
    <div class="card mb-4">
        <div class="card-header">
            Kontakt študenta (ak je plnoletý)
        </div>
        <div class="card-body">

            <div class="row">
                <!-- EMAIL -->
                <div class="mb-3 col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                            maxlength="150"
                            value="<?= htmlspecialchars((string)($osoba->getEmail() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['email']) ? htmlspecialchars((string)$errors['email']) : '' ?>
                    </div>
                </div>

                <!-- TELEFÓN -->
                <div class="mb-3 col-md-6">
                    <label for="telefon" class="form-label">Telefón</label>
                    <input
                            type="text"
                            id="telefon"
                            name="telefon"
                            class="form-control <?= isset($errors['telefon']) ? 'is-invalid' : '' ?>"
                            maxlength="30"
                            value="<?= htmlspecialchars((string)($osoba->getTelefon() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['telefon']) ? htmlspecialchars((string)$errors['telefon']) : '' ?>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- ZÁKONNÝ ZÁSTUPCA -->
    <div class="card mb-4">
        <div class="card-header">
            Zákonný zástupca (ak je neplnoletý)
        </div>
        <div class="card-body">

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="zastupca_meno" class="form-label">Meno</label>
                    <input
                            type="text"
                            id="zastupca_meno"
                            name="zastupca_meno"
                            class="form-control <?= isset($errors['zastupca_meno']) ? 'is-invalid' : '' ?>"
                            maxlength="80"
                            value="<?= htmlspecialchars((string)($osoba->getZastupcaMeno() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['zastupca_meno']) ? htmlspecialchars((string)$errors['zastupca_meno']) : '' ?>
                    </div>
                </div>

                <div class="mb-3 col-md-6">
                    <label for="zastupca_priezvisko" class="form-label">Priezvisko</label>
                    <input
                            type="text"
                            id="zastupca_priezvisko"
                            name="zastupca_priezvisko"
                            class="form-control <?= isset($errors['zastupca_priezvisko']) ? 'is-invalid' : '' ?>"
                            maxlength="80"
                            value="<?= htmlspecialchars((string)($osoba->getZastupcaPriezvisko() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['zastupca_priezvisko']) ? htmlspecialchars((string)$errors['zastupca_priezvisko']) : '' ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-md-6">
                    <label for="zastupca_email" class="form-label">Email</label>
                    <input
                            type="email"
                            id="zastupca_email"
                            name="zastupca_email"
                            class="form-control <?= isset($errors['zastupca_email']) ? 'is-invalid' : '' ?>"
                            maxlength="150"
                            value="<?= htmlspecialchars((string)($osoba->getZastupcaEmail() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['zastupca_email']) ? htmlspecialchars((string)$errors['zastupca_email']) : '' ?>
                    </div>
                </div>

                <div class="mb-3 col-md-6">
                    <label for="zastupca_telefon" class="form-label">Telefón</label>
                    <input
                            type="text"
                            id="zastupca_telefon"
                            name="zastupca_telefon"
                            class="form-control <?= isset($errors['zastupca_telefon']) ? 'is-invalid' : '' ?>"
                            maxlength="30"
                            value="<?= htmlspecialchars((string)($osoba->getZastupcaTelefon() ?? '')) ?>"
                    >
                    <div class="invalid-feedback">
                        <?= isset($errors['zastupca_telefon']) ? htmlspecialchars((string)$errors['zastupca_telefon']) : '' ?>
                    </div>
                </div>
            </div>

        </div>
    </div>


    <!-- TLAČIDLÁ -->
    <div class="d-flex justify-content-between">
        <a href="<?= $link->url('osoba.index') ?>" class="btn btn-outline-secondary">
            Späť na zoznam
        </a>

        <button type="submit" class="btn btn-primary">
            <?= $isEdit ? 'Uložiť zmeny' : 'Vytvoriť osobu' ?>
        </button>
    </div>
</form>
