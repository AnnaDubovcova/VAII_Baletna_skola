<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\PrihlaskaKurz[] $prihlasky */
/** @var int $activeOsobaId */
/** @var \App\Models\Osoba $activeOsoba */
/** @var array<int, \App\Models\Kurz> $kurzById */
?>

<h1 class="page-title">Moje prihlášky</h1>

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

<?php if (empty($prihlasky)): ?>
    <div class="alert alert-secondary">
        Zatiaľ nemáte žiadne prihlášky pre aktívnu osobu.
    </div>
<?php else: ?>
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kurz</th>
            <th>Stav</th>
            <th>Vytvorené</th>
            <th>Akcie</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prihlasky as $p): ?>
            <?php $kurzId = (int)$p->getIdKurz(); ?>
            <tr>
                <td><?= (int)$p->getId() ?></td>
                <td>
                    <?php if (isset($kurzById[$kurzId])): ?>
                        <?= htmlspecialchars((string)$kurzById[$kurzId]->getNazov()) ?>
                    <?php else: ?>
                        #<?= $kurzId ?>
                    <?php endif; ?>
                </td>
                <td>
                <span class="badge bg-secondary">
                    <?= htmlspecialchars((string)$p->getStav()) ?>
                </span>
                </td>
                <td><?= htmlspecialchars((string)($p->getCreatedAt() ?? '')) ?></td>
                <td>
                    <a class="btn btn-sm btn-outline-secondary"
                       href="<?= $link->url('prihlaska.show', ['id' => $p->getId()]) ?>">
                        Detail
                    </a>

                    <?php if ($p->getStav() === 'nova'): ?>
                        <a class="btn btn-sm btn-outline-danger ms-2"
                           href="<?= $link->url('prihlaska.cancel', ['id' => $p->getId()]) ?>"
                           onclick="return confirm('Naozaj chcete zrušiť prihlášku?');">
                            Zrušiť
                        </a>
                    <?php endif; ?>
                </td>


            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
