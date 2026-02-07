<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok[] $prispevky */
/** @var \App\Models\Osoba $activeOsoba */
/** @var array<int,string> $skupinaMap */
/** @var array<int,string> $udalostMap */
?>

<h1 class="page-title">Oznamy</h1>

<div class="text-muted mb-3">
    Pre osobu: <strong><?= htmlspecialchars((string)$activeOsoba->getMeno() . ' ' . (string)$activeOsoba->getPriezvisko()) ?></strong>
</div>

<?php if (empty($prispevky)): ?>
    <p class="text-muted">Zatiaľ nemáš žiadne príspevky.</p>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($prispevky as $p): ?>
            <?php
            $badge = '';
            $v = (string)$p->getViditelnost();

            if ($v === 'verejny') {
                $badge = 'Verejné';
            } elseif ($v === 'obdobie') {
                $badge = 'Obdobie';
            } elseif ($v === 'skupina') {
                $sid = (int)$p->getIdSkupina();
                $badge = 'Skupina: ' . ($skupinaMap[$sid] ?? ('ID ' . $sid));
            } elseif ($v === 'udalost') {
                $uid = (int)$p->getIdUdalost();
                $badge = 'Udalosť: ' . ($udalostMap[$uid] ?? ('ID ' . $uid));
            }
            ?>

            <a class="list-group-item list-group-item-action"
               href="<?= $link->url('prispevokUser.show', ['id_prispevok' => $p->getId()]) ?>">
                <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars((string)$p->getNazov()) ?></strong>
                    <small class="text-muted">
                        <?= $p->getCreatedAt() ? date('d.m.Y H:i', strtotime((string)$p->getCreatedAt())) : '' ?>
                    </small>
                </div>
                <div class="text-muted small mt-1"><?= htmlspecialchars($badge) ?></div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
