<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var array $errors */
/** @var string $formAction */
/** @var ?string $returnTo */
/** @var array $ctx */
/** @var array|null $context */
/** @var \App\Models\PrispevokSubor[] $subory */

$subory = $subory ?? [];


$ctx['obdobia'] = $ctx['obdobia'] ?? [];
$ctx['activeObdobieId'] = $ctx['activeObdobieId'] ?? null;


$isEdit = $formAction === 'edit';
$isFixed = is_array($context) && !empty($context['type']);

// nadpis
$title = 'Nový príspevok';
if ($isEdit) {
    $title = 'Upraviť príspevok';
} elseif ($isFixed && $context['type'] === 'skupina') {
    $title = 'Nový príspevok pre skupinu';
} elseif ($isFixed && $context['type'] === 'udalost') {
    $title = 'Nový príspevok pre udalosť';
}
?>

<h1 class="page-title"><?= htmlspecialchars($title) ?></h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-outline-secondary"
       href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('adminPrispevok.index') ?>">
        Späť
    </a>
</div>

<?php
// action podľa režimu
$actionUrl = $link->url('adminPrispevok.create');

if ($isEdit) {
    $actionUrl = $link->url('adminPrispevok.edit', ['id_prispevok' => $prispevok->getId()]);
} elseif ($isFixed && $context['type'] === 'skupina') {
    $actionUrl = $link->url('adminPrispevok.createForSkupina', ['id_skupina' => $context['skupina']->getId()]);
} elseif ($isFixed && $context['type'] === 'udalost') {
    $actionUrl = $link->url('adminPrispevok.createForUdalost', ['id_udalost' => $context['udalost']->getId()]);
}
?>

<form method="post" action="<?= $actionUrl ?>">

    <?php if (!empty($returnTo)): ?>
        <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo) ?>">
    <?php endif; ?>

    <?php if ($isFixed && $context['type'] === 'skupina'): ?>
        <div class="alert alert-info">
            Príspevok bude priradený ku skupine:
            <strong><?= htmlspecialchars((string)$context['skupina']->getNazov()) ?></strong>
        </div>
    <?php elseif ($isFixed && $context['type'] === 'udalost'): ?>
        <div class="alert alert-info">
            Príspevok bude priradený k udalosti:
            <strong><?= htmlspecialchars((string)$context['udalost']->getNazov()) ?></strong>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label">Názov</label>
        <input class="form-control <?= isset($errors['nazov']) ? 'is-invalid' : '' ?>"
               name="nazov"
               value="<?= htmlspecialchars((string)$prispevok->getNazov()) ?>">
        <?php if (isset($errors['nazov'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['nazov']) ?></div>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Obsah</label>
        <textarea class="form-control <?= isset($errors['obsah']) ? 'is-invalid' : '' ?>"
                  name="obsah" rows="6"><?= htmlspecialchars((string)$prispevok->getObsah()) ?></textarea>
        <?php if (isset($errors['obsah'])): ?>
            <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['obsah']) ?></div>
        <?php endif; ?>
    </div>

    <?php if (!$isFixed): ?>
        <div class="mb-3">
            <label class="form-label">Viditeľnosť</label>
            <select class="form-select <?= isset($errors['viditelnost']) ? 'is-invalid' : '' ?>" name="viditelnost">
                <?php
                $v = (string)$prispevok->getViditelnost();
                $opts = [
                        'verejny' => 'Verejný (bez prihlásenia)',
                        'obdobie' => 'Pre prihlásených (priradí sa k aktívnemu obdobiu)',
                ];
                foreach ($opts as $key => $label):
                    ?>
                    <option value="<?= $key ?>" <?= $v === $key ? 'selected' : '' ?>>
                        <?= htmlspecialchars($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['viditelnost'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['viditelnost']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-text">
            Pri možnosti „Pre prihlásených“ sa príspevok automaticky priradí k aktívnemu obdobiu.
        </div>


    <?php endif; ?>


    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">
            <?= $isEdit ? 'Uložiť zmeny' : 'Vytvoriť príspevok' ?>
        </button>

        <a class="btn btn-outline-secondary"
           href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('adminPrispevok.index') ?>">
            Zrušiť
        </a>
    </div>
</form>

<?php if ($isEdit): ?>
    <hr class="my-4">

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Prílohy</h5>

            <form method="post"
                  action="<?= $link->url('adminPrispevokSubor.upload') ?>"
                  enctype="multipart/form-data"
                  class="mb-3 d-flex gap-2 align-items-center flex-wrap">

                <input type="hidden" name="id_prispevok" value="<?= (int)$prispevok->getId() ?>">

                <?php if (!empty($returnTo)): ?>
                    <input type="hidden" name="return_to"
                           value="<?= htmlspecialchars($link->url('adminPrispevok.edit', [
                                   'id_prispevok' => $prispevok->getId(),
                                   'return_to' => $returnTo
                           ])) ?>">
                <?php endif; ?>

                <input class="form-control" type="file" name="subor" required>
                <button class="btn btn-primary" type="submit">Nahrať</button>

                <div class="form-text">
                    Povolené: pdf, jpg, png, docx (max 10 MB)
                </div>
            </form>

            <?php if (empty($subory)): ?>
                <div class="alert alert-secondary mb-0">Zatiaľ nie sú nahraté žiadne prílohy.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                        <tr>
                            <th>Názov</th>
                            <th>Veľkosť</th>
                            <th>Vytvorené</th>
                            <th class="text-end">Akcie</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($subory as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$s->getOriginalName()) ?></td>
                                <td><?= number_format(((int)$s->getSize()) / 1024, 1, ',', ' ') ?> KB</td>
                                <td>
                                    <?= $s->getCreatedAt() ? date('d.m.Y H:i', strtotime((string)$s->getCreatedAt())) : '' ?>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary"
                                       href="<?= $link->url('prispevokSubor.download', ['id_prispevok_subor' => $s->getId()]) ?>">
                                        Stiahnuť
                                    </a>

                                    <form method="post"
                                          action="<?= $link->url('adminPrispevokSubor.delete') ?>"
                                          class="d-inline-block"
                                          onsubmit="return confirm('Naozaj zmazať prílohu?');">
                                        <input type="hidden" name="id_prispevok_subor" value="<?= (int)$s->getId() ?>">
                                        <input type="hidden" name="return_to"
                                               value="<?= htmlspecialchars($link->url('adminPrispevok.edit', [
                                                       'id_prispevok' => $prispevok->getId(),
                                                       'return_to' => $returnTo ?: $link->url('adminPrispevok.index')
                                               ])) ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Zmazať</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info mt-3">
        Prílohy bude možné pridať po vytvorení príspevku (v úprave alebo detaile).
    </div>
<?php endif; ?>

