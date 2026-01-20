<?php
/** @var \App\Models\Kurz $kurz */
/** @var \App\Models\Obdobie[] $obdobia */
/** @var \App\Models\TypKurzu[] $typy */
/** @var array $errors */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1 class="page-title">
    <?= $kurz->getId() ? 'Upraviť kurz' : 'Pridať nový kurz' ?>
</h1>

<?php if (!empty($errors['global'])) { ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars($errors['global']) ?>
        <div class="mt-2">
            <a href="<?= $link->url('obdobie.create') ?>" class="btn btn-sm btn-outline-primary">Vytvoriť obdobie</a>
        </div>
    </div>
<?php } ?>

<?php if (!empty($errors['global2'])) { ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars($errors['global2']) ?>
        <div class="mt-2">
            <!-- Ak ešte nemáš TypKurzuController, toto dočasne odstráň alebo zmeň -->
            <a href="<?= $link->url('typKurzu.create') ?>" class="btn btn-sm btn-outline-primary">Vytvoriť typ kurzu</a>
        </div>
    </div>
<?php } ?>

<?php if (empty($obdobia)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne obdobia. Pred vytvorením kurzu je potrebné najskôr vytvoriť obdobie.
        <div class="mt-2">
            <a href="<?= $link->url('obdobie.create') ?>" class="btn btn-sm btn-primary">+ Pridať nové obdobie</a>
        </div>
    </div>
<?php } ?>

<?php if (empty($typy)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne typy kurzov. Pred vytvorením kurzu je potrebné najskôr vytvoriť typ kurzu.
    </div>
<?php } ?>

<form method="post" id="kurz-form" novalidate>

    <div class="mb-3">
        <label for="nazov" class="form-label">Názov <span class="text-danger">*</span></label>
        <input type="text"
               id="nazov"
               name="nazov"
               class="form-control <?= isset($errors['nazov']) ? 'is-invalid' : '' ?>"
               maxlength="100"
               required
               value="<?= htmlspecialchars($kurz->getNazov() ?? '') ?>">
        <div class="invalid-feedback"><?= htmlspecialchars($errors['nazov'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label for="id_typ_kurzu" class="form-label">Typ kurzu <span class="text-danger">*</span></label>
        <select id="id_typ_kurzu"
                name="id_typ_kurzu"
                class="form-select <?= isset($errors['id_typ_kurzu']) ? 'is-invalid' : '' ?>"
                required
            <?= empty($typy) ? 'disabled' : '' ?>>
            <option value="">-- vyber typ kurzu --</option>
            <?php foreach ($typy as $typ) { ?>
                <option value="<?= (int)$typ->getId() ?>"
                    <?= ((int)($kurz->getIdTypKurzu() ?? 0) === (int)$typ->getId()) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($typ->getNazov() ?? '') ?>
                </option>
            <?php } ?>
        </select>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['id_typ_kurzu'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label for="id_obdobie" class="form-label">Obdobie <span class="text-danger">*</span></label>
        <select id="id_obdobie"
                name="id_obdobie"
                class="form-select <?= isset($errors['id_obdobie']) ? 'is-invalid' : '' ?>"
                required
            <?= empty($obdobia) ? 'disabled' : '' ?>>
            <option value="">-- vyber obdobie --</option>
            <?php foreach ($obdobia as $obdobie) { ?>
                <option value="<?= (int)$obdobie->getId() ?>"
                    <?= ((int)($kurz->getIdObdobie() ?? 0) === (int)$obdobie->getId()) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($obdobie->getNazov() ?? '') ?>
                </option>
            <?php } ?>
        </select>
        <div class="invalid-feedback"><?= htmlspecialchars($errors['id_obdobie'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label for="cena" class="form-label">Cena</label>
        <input type="text"
               id="cena"
               name="cena"
               class="form-control <?= isset($errors['cena']) ? 'is-invalid' : '' ?>"
               placeholder="napr. 120.00"
               value="<?= htmlspecialchars($kurz->getCena() === null ? '' : number_format($kurz->getCena(), 2, '.', '')) ?>">
        <div class="invalid-feedback"><?= htmlspecialchars($errors['cena'] ?? '') ?></div>
    </div>

    <div class="mb-3">
        <label for="popis" class="form-label">Popis</label>
        <textarea id="popis"
                  name="popis"
                  rows="3"
                  maxlength="1000"
                  class="form-control <?= isset($errors['popis']) ? 'is-invalid' : '' ?>"><?= htmlspecialchars($kurz->getPopis() ?? '') ?></textarea>
        <?php if (isset($errors['popis'])) { ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['popis']) ?></div>
        <?php } ?>
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input"
               type="checkbox"
               id="prihlasovanie_otvorene"
               name="prihlasovanie_otvorene"
            <?= $kurz->isPrihlasovanieOtvorene() ? 'checked' : '' ?>>
        <label class="form-check-label" for="prihlasovanie_otvorene">
            Prihlasovanie otvorené
        </label>
    </div>

    <div class="d-flex justify-content-between">
        <a href="<?= $link->url('kurz.index') ?>" class="btn btn-outline-secondary">Späť na zoznam</a>

        <button type="submit"
                class="btn btn-primary"
            <?= (empty($obdobia) || empty($typy)) ? 'disabled' : '' ?>>
            <?= $kurz->getId() ? 'Uložiť zmeny' : 'Vytvoriť kurz' ?>
        </button>
    </div>
</form>
