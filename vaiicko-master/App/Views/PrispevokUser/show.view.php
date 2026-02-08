<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var \App\Models\Osoba $activeOsoba */
/** @var \App\Models\PrispevokSubor[] $subory */
?>

<a class="btn btn-outline-secondary" href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('prispevokUser.index') ?>">
    ← Späť
</a>


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
                        <?php
                        $mime = (string)$s->getMimeType();
                        $isImg = in_array($mime, ['image/jpeg','image/png'], true);
                        ?>

                        <?php if ($isImg): ?>
                            <div class="mb-2">
                                <img
                                        src="<?= $link->url('prispevokSubor.preview', ['id_prispevok_subor' => $s->getId()]) ?>"
                                        alt="<?= htmlspecialchars((string)$s->getOriginalName()) ?>"
                                        class="img-fluid rounded border"
                                        style="max-height: 320px;"
                                >
                            </div>
                        <?php endif; ?>

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

