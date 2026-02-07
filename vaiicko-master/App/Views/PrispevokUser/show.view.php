<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var \App\Models\Osoba $activeOsoba */
?>

<div class="mb-3">
    <a class="btn btn-outline-secondary" href="<?= $link->url('prispevokUser.index') ?>">← Späť</a>
</div>

<h1 class="page-title"><?= htmlspecialchars((string)$prispevok->getNazov()) ?></h1>

<?php if ($prispevok->getCreatedAt()): ?>
    <div class="text-muted mb-3">
        <?= date('d.m.Y H:i', strtotime((string)$prispevok->getCreatedAt())) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <?= nl2br(htmlspecialchars((string)$prispevok->getObsah())) ?>
    </div>
</div>
