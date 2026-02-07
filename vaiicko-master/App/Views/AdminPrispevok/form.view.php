<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var array $errors */
/** @var string $formAction */
/** @var ?string $returnTo */
/** @var array $ctx */
/** @var array|null $context */

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
                        'obdobie' => 'Pre obdobie (prihlásení)',
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

        <div class="mb-3">
            <label class="form-label">Obdobie (pre typ „obdobie“)</label>
            <select class="form-select <?= isset($errors['id_obdobie']) ? 'is-invalid' : '' ?>" name="id_obdobie">
                <option value="">— vyber —</option>

                <?php
                $selectedObdobieId = $prispevok->getIdObdobie();
                if ($selectedObdobieId === null && !empty($ctx['activeObdobieId'])) {
                    $selectedObdobieId = (int)$ctx['activeObdobieId'];
                }
                ?>

                <?php foreach ($ctx['obdobia'] as $o): ?>
                    <option value="<?= (int)$o->getId() ?>" <?= ((int)$selectedObdobieId === (int)$o->getId()) ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string)$o->getNazov()) ?>
                        (<?= htmlspecialchars((string)$o->getDatumOd()) ?> – <?= htmlspecialchars((string)$o->getDatumDo()) ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <?php if (isset($errors['id_obdobie'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['id_obdobie']) ?></div>
            <?php endif; ?>
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
