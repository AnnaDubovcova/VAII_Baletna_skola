<?php
/** @var \App\Models\Skupina $skupina */
/** @var \App\Models\Obdobie[] $obdobia */
/** @var array $errors */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1 class="page-title">
    <?= $skupina->getId() ? 'Upraviť skupinu' : 'Pridať novú skupinu' ?>
</h1>

<?php if (!empty($errors['global'])) { ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars($errors['global']) ?>
        <div class="mt-2">
            <a href="<?= $link->url('obdobie.create') ?>" class="btn btn-sm btn-outline-primary">
                Vytvoriť obdobie
            </a>
        </div>
    </div>
<?php } ?>

<?php if (empty($obdobia)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne obdobia. Pred vytvorením skupiny je potrebné najskôr vytvoriť obdobie.
        <div class="mt-2">
            <a href="<?= $link->url('obdobie.create') ?>" class="btn btn-sm btn-primary">
                + Pridať nové obdobie
            </a>
        </div>
    </div>
<?php } ?>

<form method="post" novalidate>

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
            value="<?= htmlspecialchars($skupina->getNazov() ?? '') ?>"
        >
        <div class="invalid-feedback">
            <?= isset($errors['nazov']) ? htmlspecialchars($errors['nazov']) : '' ?>
        </div>
    </div>

    <!-- OBDOBIE -->
    <div class="mb-3">
        <label for="id_obdobie" class="form-label">
            Obdobie <span class="text-danger">*</span>
        </label>
        <select
            id="id_obdobie"
            name="id_obdobie"
            class="form-select <?= isset($errors['id_obdobie']) ? 'is-invalid' : '' ?>"
            required
            <?= empty($obdobia) ? 'disabled' : '' ?>
        >
            <option value="">-- vyber obdobie --</option>
            <?php foreach ($obdobia as $obdobie) { ?>
                <option
                    value="<?= (int)$obdobie->getId() ?>"
                    <?= ((int)($skupina->getIdObdobie() ?? 0) === (int)$obdobie->getId()) ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($obdobie->getNazov() ?? '') ?>
                </option>
            <?php } ?>
        </select>
        <div class="invalid-feedback">
            <?= isset($errors['id_obdobie']) ? htmlspecialchars($errors['id_obdobie']) : '' ?>
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
        ><?= htmlspecialchars($skupina->getPopis() ?? '') ?></textarea>
        <?php if (isset($errors['popis'])) { ?>
            <div class="invalid-feedback">
                <?= htmlspecialchars($errors['popis']) ?>
            </div>
        <?php } ?>
    </div>

    <!-- TLAČIDLÁ -->
    <div class="d-flex justify-content-between">
        <a href="<?= $link->url('skupina.index') ?>" class="btn btn-outline-secondary">
            Späť na zoznam
        </a>

        <button type="submit" class="btn btn-primary" <?= empty($obdobia) ? 'disabled' : '' ?>>
            <?= $skupina->getId() ? 'Uložiť zmeny' : 'Vytvoriť skupinu' ?>
        </button>
    </div>
</form>

