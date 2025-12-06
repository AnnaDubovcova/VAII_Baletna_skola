<?php
/** @var \App\Models\Obdobie $obdobie */
/** @var array $errors */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1 class="page-title">
    <?= $obdobie->getId() ? 'Upraviť obdobie' : 'Pridať nové obdobie' ?>
</h1>

<form method="post" id="obdobie-form" novalidate>

    <!-- NÁZOV -->
    <div class="mb-3">
        <label for="nazov" class="form-label">
            Názov <span class="text-danger">*</span>
        </label>
        <input
            type="text"
            id="nazov"
            name="nazov"
            class="form-control <?= isset($errors['nazov']) ? 'is-invalid' : '' ?>"
            maxlength="100"
            required
            value="<?= htmlspecialchars($obdobie->getNazov() ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= isset($errors['nazov']) ? htmlspecialchars($errors['nazov']) : '' ?>
        </div>
    </div>


    <!-- DÁTUM OD + DÁTUM DO -->
    <div class="row">
        <div class="mb-3 col-md-6">
            <label for="datum_od" class="form-label">
                Dátum od <span class="text-danger">*</span>
            </label>
            <input
                type="date"
                id="datum_od"
                name="datum_od"
                class="form-control <?= isset($errors['datum_od']) ? 'is-invalid' : '' ?>"
                required
                value="<?= htmlspecialchars($obdobie->getDatumOd() ?? '') ?>"
            >
            <div class="invalid-feedback">
                <?= isset($errors['datum_od']) ? htmlspecialchars($errors['datum_od']) : '' ?>
            </div>
        </div>


        <div class="mb-3 col-md-6">
            <label for="datum_do" class="form-label">
                Dátum do <span class="text-danger">*</span>
            </label>
            <input
                type="date"
                id="datum_do"
                name="datum_do"
                class="form-control <?= isset($errors['datum_do']) ? 'is-invalid' : '' ?>"
                required
                value="<?= htmlspecialchars($obdobie->getDatumDo() ?? '') ?>"
            >
            <div class="invalid-feedback">
                <?= isset($errors['datum_do']) ? htmlspecialchars($errors['datum_do']) : '' ?>
            </div>
        </div>

    </div>

    <!-- POPIS -->
    <div class="mb-3">
        <label for="popis" class="form-label">Popis</label>
        <textarea
            id="popis"
            name="popis"
            rows="3"
            maxlength="1000"
            class="form-control <?= isset($errors['popis']) ? 'is-invalid' : '' ?>"
        ><?= htmlspecialchars($obdobie->getPopis() ?? '') ?></textarea>
        <?php if (isset($errors['popis'])) { ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['popis']) ?>
            </div>
        <?php } ?>
    </div>

    <!-- TLAČIDLÁ -->
    <div class="d-flex justify-content-between">
        <a href="<?= $link->url('obdobie.index') ?>"
           class="btn btn-outline-secondary">
            Späť na zoznam
        </a>

        <button type="submit" class="btn btn-primary">
            <?= $obdobie->getId() ? 'Uložiť zmeny' : 'Vytvoriť obdobie' ?>
        </button>
    </div>
</form>

