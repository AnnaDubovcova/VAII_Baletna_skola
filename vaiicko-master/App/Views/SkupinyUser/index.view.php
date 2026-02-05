<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Osoba $activeOsoba */
/** @var \App\Models\Skupina[] $skupiny */
?>

<h1 class="page-title">Skupiny</h1>

<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        Aktívna osoba:
        <b><?= htmlspecialchars((string)$activeOsoba->getMeno()) ?>
            <?= htmlspecialchars((string)$activeOsoba->getPriezvisko()) ?></b>
    </div>
    <a class="btn btn-sm btn-outline-primary" href="<?= $link->url('osoba.index') ?>">
        Zmeniť aktívnu osobu
    </a>
</div>

<?php if (empty($skupiny)): ?>
    <div class="alert alert-secondary">
        Táto osoba nie je v žiadnej skupine v zvolenom období.
    </div>
<?php else: ?>

    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>Názov</th>
            <th>Popis</th>
            <th class="text-end">Akcia</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($skupiny as $s): ?>
            <tr>
                <td>
                    <?= htmlspecialchars((string)$s->getNazov()) ?>
                </td>

                <td>
                    <?php $popis = trim((string)$s->getPopis()); ?>
                    <?= $popis !== '' ? nl2br(htmlspecialchars($popis)) : '<span class="text-muted">—</span>' ?>
                </td>

                <td class="text-end">
                    <!-- pripravené na budúce "show" -->
                    <a class="btn btn-sm btn-outline-secondary"
                       href="<?= $link->url('skupina.show', ['id_skupina' => $s->getId()]) ?>">
                        Detail
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>
