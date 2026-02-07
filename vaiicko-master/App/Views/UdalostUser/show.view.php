<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array $udalost */
/** @var \App\Models\Osoba $activeOsoba */
?>

<h1><?= htmlspecialchars($udalost['nazov']) ?></h1>

<div class="mb-2 text-muted">
    <?= htmlspecialchars($activeOsoba->getMeno() . ' ' . $activeOsoba->getPriezvisko()) ?>
</div>

<ul class="list-unstyled">
    <li><strong>Typ:</strong> <?= htmlspecialchars($udalost['typ']) ?></li>
    <li><strong>Čas:</strong>
        <?= date('d.m.Y H:i', strtotime($udalost['zaciatok'])) ?>
        <?php if ($udalost['koniec']): ?>
            – <?= date('H:i', strtotime($udalost['koniec'])) ?>
        <?php endif; ?>
    </li>
    <?php if ($udalost['miesto']): ?>
        <li><strong>Miesto:</strong> <?= htmlspecialchars($udalost['miesto']) ?></li>
    <?php endif; ?>
    <?php if ($udalost['skupiny']): ?>
        <li><strong>Skupina:</strong> <?= htmlspecialchars($udalost['skupiny']) ?></li>
    <?php endif; ?>
</ul>

<?php if (!empty($udalost['popis'])): ?>
    <hr>
    <p><?= nl2br(htmlspecialchars($udalost['popis'])) ?></p>
<?php endif; ?>

<a class="btn btn-outline-secondary mt-3" href="<?= $link->url('rozvrhUser.index') ?>">
    Späť na rozvrh
</a>
