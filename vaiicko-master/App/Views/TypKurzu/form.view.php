

<?php
//vygenerovane chatom GPT
/** @var \App\Models\TypKurzu $typ_kurzu */
/** @var array $errors */
/** @var string $formAction */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1 class="page-title">
    <?= $typ_kurzu->getId() ? 'Upraviť typ kurzu' : 'Pridať nový typ kurzu' ?>
</h1>

<form method="post" id="typKurzu-form" novalidate>

    <!-- Nazov -->
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
            value="<?= htmlspecialchars($typ_kurzu->getNazov() ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= htmlspecialchars($errors['nazov'] ?? '') ?>
        </div>
    </div>

    <!-- Popis -->
    <div class="mb-3">
        <label for="popis" class="form-label">Popis</label>
        <textarea
            id="popis"
            name="popis"
            rows="3"
            maxlength="1000"
            class="form-control <?= isset($errors['popis']) ? 'is-invalid' : '' ?>"
        ><?= htmlspecialchars($typ_kurzu->getPopis() ?? '') ?></textarea>

        <?php if (isset($errors['popis'])) { ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['popis']) ?>
            </div>
        <?php } ?>
    </div>

    <div class="d-flex justify-content-between">
        <a href="<?= $link->url('typKurzu.index') ?>" class="btn btn-outline-secondary">
            Späť na zoznam
        </a>

        <button type="submit" class="btn btn-primary">
            <?= $typ_kurzu->getId() ? 'Uložiť zmeny' : 'Vytvoriť typ kurzu' ?>
        </button>
    </div>
</form>
