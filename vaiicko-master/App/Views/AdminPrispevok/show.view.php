<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var ?string $returnTo */
?>

<h1 class="page-title">Detail príspevku</h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-outline-secondary"
       href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('adminPrispevok.index') ?>">
        Späť
    </a>

    <a class="btn btn-outline-primary"
       href="<?= $link->url('adminPrispevok.edit', ['id_prispevok' => $prispevok->getId(), 'return_to' => $returnTo ?: $link->url('adminPrispevok.index')]) ?>">
        Upraviť
    </a>
</div>

<div class="card">
    <div class="card-body">
        <h4><?= htmlspecialchars((string)$prispevok->getNazov()) ?></h4>
        <div class="text-muted mb-2">
            Viditeľnosť: <strong><?= htmlspecialchars((string)$prispevok->getViditelnost()) ?></strong>
            <?php if ($prispevok->getCreatedAt()): ?>
                • <?= date('d.m.Y H:i', strtotime((string)$prispevok->getCreatedAt())) ?>
            <?php endif; ?>
        </div>
        <div><?= nl2br(htmlspecialchars((string)$prispevok->getObsah())) ?></div>
    </div>
</div>
