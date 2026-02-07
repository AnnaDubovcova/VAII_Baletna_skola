<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array<\App\Models\Prispevok> $prispevky */
?>

<h1 class="page-title">Príspevky</h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-primary" href="<?= $link->url('adminPrispevok.create') ?>">Nový príspevok</a>
</div>

<?php if (empty($prispevky)): ?>
    <p class="text-muted">Zatiaľ nie sú žiadne príspevky.</p>
<?php else: ?>
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>Názov</th>
            <th>Viditeľnosť</th>
            <th>Vytvorené</th>
            <th class="text-end">Akcie</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prispevky as $p): ?>
            <tr>
                <td><?= htmlspecialchars((string)$p->getNazov()) ?></td>
                <td><?= htmlspecialchars((string)$p->getViditelnost()) ?></td>
                <td>
                    <?php if ($p->getCreatedAt()): ?>
                        <?= date('d.m.Y H:i', strtotime((string)$p->getCreatedAt())) ?>
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <a class="btn btn-outline-secondary btn-sm"
                       href="<?= $link->url('adminPrispevok.show', ['id_prispevok' => $p->getId()]) ?>">
                        Detail
                    </a>
                    <a class="btn btn-outline-primary btn-sm"
                       href="<?= $link->url('adminPrispevok.edit', ['id_prispevok' => $p->getId()]) ?>">
                        Upraviť
                    </a>
                    <a class="btn btn-outline-danger btn-sm"
                       href="<?= $link->url('adminPrispevok.delete', ['id_prispevok' => $p->getId()]) ?>"
                       onclick="return confirm('Naozaj zmazať príspevok?');">
                        Zmazať
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
