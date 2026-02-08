<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var \App\Models\PrispevokSubor[] $subory */
?>

<div class="mb-3">
    <a class="btn btn-outline-secondary" href="<?= $link->url('prispevokPublic.index') ?>">← Späť</a>
</div>

<h1 class="page-title"><?= htmlspecialchars((string)$prispevok->getNazov()) ?></h1>

<?php if ($prispevok->getCreatedAt()): ?>
    <div class="text-muted mb-3">
        <?= date('d.m.Y H:i', strtotime((string)$prispevok->getCreatedAt())) ?>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <?= nl2br(htmlspecialchars((string)$prispevok->getObsah())) ?>
    </div>

    <div class="card-body">
        <h5 class="card-title">Prílohy</h5>

        <?php if (empty($subory)): ?>
            <div class="alert alert-secondary mb-0">K tomuto príspevku nie sú prílohy.</div>
        <?php else: ?>
            <ul class="mb-0">
                <?php foreach ($subory as $s): ?>
                    <li>
                        <a href="<?= $link->url('prispevokSubor.download', ['id_prispevok_subor' => $s->getId()]) ?>">
                            <?= htmlspecialchars((string)$s->getOriginalName()) ?>
                        </a>
                        <span class="text-muted">
                            (<?= number_format(((int)$s->getSize()) / 1024, 1, ',', ' ') ?> KB)
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

