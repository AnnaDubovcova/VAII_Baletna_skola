<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok[] $prispevky */
?>

<h1 class="page-title">Oznamy</h1>

<?php if (empty($prispevky)): ?>
    <p class="text-muted">Zatiaľ nie sú žiadne verejné príspevky.</p>
<?php else: ?>
    <div class="list-group">
        <?php foreach ($prispevky as $p): ?>
            <a class="list-group-item list-group-item-action"
               href="<?= $link->url('prispevokPublic.show', ['id_prispevok' => $p->getId()]) ?>">
                <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars((string)$p->getNazov()) ?></strong>
                    <small class="text-muted">
                        <?= $p->getCreatedAt() ? date('d.m.Y H:i', strtotime((string)$p->getCreatedAt())) : '' ?>
                    </small>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
