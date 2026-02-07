<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Udalost $udalost */
/** @var array $errors */
/** @var \App\Models\Skupina[] $skupiny */
/** @var int[] $selectedSkupiny */
/** @var string $formAction */ // 'create' alebo 'edit'
?>

<?php
$isEdit = ($formAction === 'edit');
$title = $isEdit ? 'Upraviť udalosť' : 'Nová udalosť';

$actionUrl = $isEdit
    ? $link->url('udalost.edit', ['id_udalost' => (int)$udalost->getId()])
    : $link->url('udalost.create');

// pre datetime-local: DB "YYYY-MM-DD HH:MM:SS" -> "YYYY-MM-DDTHH:MM"
function toDateTimeLocal(?string $db): string {
    if ($db === null || trim($db) === '') return '';
    $s = str_replace(' ', 'T', trim($db));
    return substr($s, 0, 16);
}

$valNazov = (string)($udalost->getNazov() ?? '');
$valTyp = (string)($udalost->getTyp() ?? 'trening');
$valZaciatok = toDateTimeLocal($udalost->getZaciatok());
$valKoniec = toDateTimeLocal($udalost->getKoniec());
$valMiesto = (string)($udalost->getMiesto() ?? '');
$valPopis = (string)($udalost->getPopis() ?? '');
?>

<h1 class="page-title"><?= htmlspecialchars($title) ?></h1>

<div class="mb-3">
    <a class="btn btn-sm btn-outline-secondary"  href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('udalost.index') ?>">Späť</a>
</div>

<?php if (!empty($errors['global'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars((string)$errors['global']) ?></div>
<?php endif; ?>

<form method="post" action="<?= $actionUrl ?>" class="needs-validation" novalidate>
    <?php if (!empty($returnTo)): ?>
        <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo) ?>">
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Názov *</label>
                <input type="text"
                       name="nazov"
                       class="form-control <?= isset($errors['nazov']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($valNazov) ?>"
                       maxlength="150"
                       required>
                <?php if (isset($errors['nazov'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['nazov']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label">Typ *</label>
                <select name="typ" class="form-select <?= isset($errors['typ']) ? 'is-invalid' : '' ?>" required>
                    <?php
                    $types = [
                        'trening' => 'Tréning',
                        'nacvik' => 'Nácvik',
                        'vystupenie' => 'Vystúpenie',
                        'ine' => 'Iné',
                    ];
                    foreach ($types as $key => $label):
                        ?>
                        <option value="<?= $key ?>" <?= $valTyp === $key ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['typ'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['typ']) ?></div>
                <?php endif; ?>
            </div>

            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Začiatok *</label>
                    <input type="datetime-local"
                           name="zaciatok"
                           class="form-control <?= isset($errors['zaciatok']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($valZaciatok) ?>"
                           required>
                    <?php if (isset($errors['zaciatok'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['zaciatok']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Koniec</label>
                    <input type="datetime-local"
                           name="koniec"
                           class="form-control <?= isset($errors['koniec']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($valKoniec) ?>">
                    <?php if (isset($errors['koniec'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars((string)$errors['koniec']) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-12 col-md-6">
                    <label class="form-label">Miesto</label>
                    <input type="text"
                           name="miesto"
                           class="form-control"
                           value="<?= htmlspecialchars($valMiesto) ?>"
                           maxlength="150">
                </div>

                <div class="col-12">
                    <label class="form-label">Popis</label>
                    <textarea name="popis" class="form-control" rows="4"><?= htmlspecialchars($valPopis) ?></textarea>
                </div>
            </div>

        </div>
    </div>

    <?php if (!$isEdit): ?>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="vyzaduje_reakciu" value="1"
                    <?= $udalost->vyzadujeReakciu() ? 'checked' : '' ?>>
            <label class="form-check-label">Vyžaduje reakciu účastníkov</label>
        </div>
    <?php else: ?>
        <div class="mb-3">
            <label class="form-label">Vyžaduje reakciu účastníkov</label>
            <div class="form-control-plaintext">
                <?= $udalost->vyzadujeReakciu() ? 'Áno' : 'Nie' ?>
            </div>
            <div class="form-text">
                Po vytvorení udalosti sa toto nastavenie nedá meniť.
            </div>
        </div>
    <?php endif; ?>


    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Skupiny *</h5>

            <?php if (isset($errors['skupiny'])): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars((string)$errors['skupiny']) ?></div>
            <?php endif; ?>

            <?php if (empty($skupiny)): ?>
                <div class="alert alert-warning mb-0">
                    Nemáte vytvorené žiadne skupiny. Najprv vytvorte skupinu.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($skupiny as $s): ?>
                        <?php $sid = (int)$s->getId(); ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="id_skupina[]"
                                       value="<?= $sid ?>"
                                       id="sk<?= $sid ?>"
                                    <?= in_array($sid, $selectedSkupiny, true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sk<?= $sid ?>">
                                    <?= htmlspecialchars((string)$s->getNazov()) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">
            <?= $isEdit ? 'Uložiť zmeny' : 'Vytvoriť udalosť' ?>
        </button>
        <a class="btn btn-outline-secondary"  href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('udalost.index') ?>">Zrušiť</a>
    </div>
</form>
