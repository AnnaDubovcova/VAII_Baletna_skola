<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\PrihlaskaKurz $prihlaska */
/** @var \App\Models\Kurz|null $kurz */
/** @var \App\Models\Osoba|null $osoba */
/** @var string $returnTo */

?>

<h1 class="page-title">Detail prihlášky</h1>

<div class="mb-3">
    <a class="btn btn-sm btn-outline-secondary"
       href="<?= !empty($returnTo) ? $returnTo : $link->url('adminPrihlaska.index') ?>">Späť
    </a>

</div>

<div class="card mb-3">
    <div class="card-body">
        <p class="mb-1"><b>ID:</b> <?= (int)$prihlaska->getId() ?></p>
        <p class="mb-1"><b>Stav:</b> <?= htmlspecialchars((string)$prihlaska->getStav()) ?></p>
        <p class="mb-1"><b>Vytvorené:</b> <?= htmlspecialchars((string)($prihlaska->getCreatedAt() ?? '')) ?></p>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h5 class="card-title mb-0">Študent</h5>

            <?php if ($osoba): ?>
                <a class="btn btn-sm btn-outline-primary"
                   href="<?= $link->url('osoba.show', [
                           'id_osoba' => $osoba->getId(),
                           'return_to' => $link->url('adminPrihlaska.show', [
                                   'id' => $prihlaska->getId(),
                                   'return_to' => $returnTo
                           ])
                   ]) ?>">Zobraziť osobu
                </a>
            <?php endif; ?>
        </div>

        <hr class="my-3">

        <?php if ($osoba): ?>
            <p class="mb-1">
                <b><?= htmlspecialchars($osoba->getMeno() . ' ' . $osoba->getPriezvisko()) ?></b>
            </p>
            <p class="mb-3">
                Dátum narodenia: <?= htmlspecialchars((string)$osoba->getDatumNarodenia()) ?>
            </p>

            <h6>Aktuálne kontakty študenta (profil osoby)</h6>
            <p class="mb-1">
                <b>Email:</b> <?= htmlspecialchars((string)($osoba->getEmail() ?? '—')) ?>
            </p>
            <p class="mb-3">
                <b>Telefón:</b> <?= htmlspecialchars((string)($osoba->getTelefon() ?? '—')) ?>
            </p>

            <h6>Aktuálne kontakty zákonného zástupcu (profil osoby)</h6>
            <p class="mb-1">
                <b>Meno:</b>
                <?= htmlspecialchars((string)($osoba->getZastupcaMeno() ?? '—')) ?>
                <?= htmlspecialchars((string)($osoba->getZastupcaPriezvisko() ?? '')) ?>
            </p>
            <p class="mb-1">
                <b>Email:</b> <?= htmlspecialchars((string)($osoba->getZastupcaEmail() ?? '—')) ?>
            </p>
            <p class="mb-0">
                <b>Telefón:</b> <?= htmlspecialchars((string)($osoba->getZastupcaTelefon() ?? '—')) ?>
            </p>

        <?php else: ?>
            <p class="mb-0">Neznáma osoba (#<?= (int)$prihlaska->getIdOsoba() ?>)</p>
        <?php endif; ?>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Snapshot zákonného zástupcu</h5>
        <p class="mb-1">
            <?= htmlspecialchars((string)($prihlaska->getZastupcaMeno() ?? '')) ?>
            <?= htmlspecialchars((string)($prihlaska->getZastupcaPriezvisko() ?? '')) ?>
        </p>
        <p class="mb-1">Email: <?= htmlspecialchars((string)($prihlaska->getZastupcaEmail() ?? '')) ?></p>
        <p class="mb-0">Telefón: <?= htmlspecialchars((string)($prihlaska->getZastupcaTelefon() ?? '')) ?></p>
    </div>
</div>
