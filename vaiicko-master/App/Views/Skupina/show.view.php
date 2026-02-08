<?php
/** @var \App\Models\Skupina $skupina */
/** @var \App\Models\Osoba[] $members */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var \App\Models\Osoba|null $activeOsoba */
/** @var string|null $returnTo */
?>

<h1 class="page-title">Detail skupiny</h1>

<div class="card mb-4">
    <div class="card-header">
        Skupina
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tbody>
            <tr>
                <th style="width: 220px;">Názov</th>
                <td><?= htmlspecialchars((string)$skupina->getNazov()) ?></td>
            </tr>
            <tr>
                <th>Popis</th>
                <td>
                    <?php $popis = trim((string)$skupina->getPopis()); ?>
                    <?= $popis !== '' ? nl2br(htmlspecialchars($popis)) : '—' ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Členovia skupiny
    </div>

    <div class="card-body">
        <?php if (empty($members)): ?>
            <div class="text-muted">Skupina zatiaľ nemá žiadnych členov.</div>
        <?php else: ?>
            <table class="table table-sm table-striped align-middle mb-0">
                <thead>
                <tr>
                    <th>Meno</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($members as $m): ?>
                    <tr>
                        <td>
                            <?php $fullName = trim((string)$m->getMeno() . ' ' . (string)$m->getPriezvisko()); ?>

                            <?php if ($user->isAdmin()): ?>
                                <a href="<?= $link->url('osoba.show', [
                                        'id_osoba' => $m->getId(),
                                        'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('skupina.index')
                                ]) ?>">
                                    <?= htmlspecialchars($fullName) ?>
                                </a>
                            <?php else: ?>
                                <?= htmlspecialchars($fullName) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>


<div class="d-flex justify-content-between">
    <?php if (!empty($returnTo)): ?>
        <a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars($returnTo) ?>">
            Späť
        </a>
    <?php else: ?>
        <?php if ($user->isAdmin()): ?>
            <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('skupina.index') ?>">
                Späť na skupiny
            </a>
        <?php else: ?>
            <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('skupinyUser.index') ?>">
                Späť na moje skupiny
            </a>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($user->isAdmin()): ?>
        <a class="btn btn-outline-secondary"
           href="<?= $link->url('adminPrispevok.index', [
                   'mode' => 'skupina',
                   'id_skupina' => (int)$skupina->getId(),
                   'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('skupina.index')
           ]) ?>">
            Správa príspevkov
        </a>
        <a class="btn btn-primary" href="<?= $link->url('skupina.edit', ['id_skupina' => $skupina->getId()]) ?>">
            Upraviť
        </a>
    <?php endif; ?>

    <?php if (!$user->isAdmin()): ?>
        <a class="btn btn-sm btn-outline-primary"
           href="<?= $link->url('prispevokUser.index') ?>">
            Príspevky
        </a>
    <?php endif; ?>


</div>
