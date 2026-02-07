<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array<\App\Models\Prispevok> $prispevky */
?>

<h1 class="page-title">Oznamy</h1>

<?php if (empty($prispevky)): ?>
    <p class="text-muted">Zatiaľ nie sú žiadne verejné oznamy.</p>
<?php else: ?>
    <div class="d-flex flex-column gap-3">
        <?php foreach ($prispevky as $p): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-1"><?= htmlspecialchars((string)$p->getNazov()) ?></h5>
                    <div class="text-muted mb-2" style="font-size: 0.95em;">
                        <?php if ($p->getCreatedAt()): ?>
                            <?= date('d.m.Y H:i', strtotime((string)$p->getCreatedAt())) ?>
                        <?php endif; ?>
                    </div>
                    <div class="card-text">
                        <?= nl2br(htmlspecialchars((string)$p->getObsah())) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
