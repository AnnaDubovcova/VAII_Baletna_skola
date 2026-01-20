<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Kurz $kurz */
/** @var \App\Models\Osoba $activeOsoba */
/** @var string|null $stav */
?>

<h1 class="page-title"><?= htmlspecialchars((string)$kurz->getNazov()) ?></h1>

<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        Aktívna osoba:
        <b><?= htmlspecialchars($activeOsoba->getMeno() . ' ' . $activeOsoba->getPriezvisko()) ?></b>
    </div>
    <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('kurzyUser.index') ?>">
        Späť na zoznam
    </a>
</div>

<div class="card">
    <div class="card-body">
        <p class="mb-2"><b>Cena:</b>
            <?php if ($kurz->getCena() === null): ?>—
            <?php else: ?><?= htmlspecialchars(number_format((float)$kurz->getCena(), 2, ',', ' ')) ?> €
            <?php endif; ?>
        </p>

        <p class="mb-2"><b>Popis:</b><br>
            <?= nl2br(htmlspecialchars((string)($kurz->getPopis() ?? ''))) ?>
        </p>

        <hr>

        <?php if ($stav === null): ?>
            <a class="btn btn-primary"
               href="<?= $link->url('prihlaska.create', ['id_kurz' => $kurz->getId()]) ?>">
                Prihlásiť
            </a>
        <?php elseif ($stav === 'zrusena'): ?>
            <a class="btn btn-primary"
               href="<?= $link->url('prihlaska.create', ['id_kurz' => $kurz->getId()]) ?>"
               onclick="return confirm('Chcete podať prihlášku znova?');"
                Prihlásiť znova
            </a>
        <?php else: ?>
            <span class="badge bg-secondary">Stav prihlášky: <?= htmlspecialchars($stav) ?></span>
        <?php endif; ?>
    </div>
</div>
