<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\PrihlaskaKurz $prihlaska */
/** @var \App\Models\Kurz|null $kurz */
/** @var \App\Models\Osoba $activeOsoba */
?>

<h1 class="page-title">Detail prihlášky</h1>

<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        Aktívna osoba:
        <b><?= htmlspecialchars($activeOsoba->getMeno() . ' ' . $activeOsoba->getPriezvisko()) ?></b>
    </div>
    <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('prihlaska.index') ?>">Späť</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title mb-3">
            <?= htmlspecialchars($kurz ? (string)$kurz->getNazov() : 'Kurz') ?>
        </h5>

        <p class="mb-1"><b>Stav:</b> <?= htmlspecialchars((string)$prihlaska->getStav()) ?></p>
        <p class="mb-1"><b>Vytvorené:</b> <?= htmlspecialchars((string)($prihlaska->getCreatedAt() ?? '')) ?></p>

        <hr>

        <h6>Údaje zákonného zástupcu (snapshot)</h6>
        <p class="mb-1"><b>Meno:</b> <?= htmlspecialchars((string)($prihlaska->getZastupcaMeno() ?? '')) ?>
            <?= htmlspecialchars((string)($prihlaska->getZastupcaPriezvisko() ?? '')) ?></p>
        <p class="mb-1"><b>Email:</b> <?= htmlspecialchars((string)($prihlaska->getZastupcaEmail() ?? '')) ?></p>
        <p class="mb-1"><b>Telefón:</b> <?= htmlspecialchars((string)($prihlaska->getZastupcaTelefon() ?? '')) ?></p>
    </div>
</div>
