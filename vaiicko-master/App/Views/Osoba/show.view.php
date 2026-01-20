<?php
/** @var \App\Models\Osoba $osoba */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1 class="page-title">Detail osoby</h1>

<div class="card mb-4">
    <div class="card-header">
        Študent
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tbody>
            <tr>
                <th style="width: 220px;">Meno</th>
                <td><?= htmlspecialchars((string)$osoba->getMeno()) ?></td>
            </tr>
            <tr>
                <th>Priezvisko</th>
                <td><?= htmlspecialchars((string)$osoba->getPriezvisko()) ?></td>
            </tr>
            <tr>
                <th>Dátum narodenia</th>
                <td><?= htmlspecialchars((string)$osoba->getDatumNarodenia()) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars((string)($osoba->getEmail() ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Telefón</th>
                <td><?= htmlspecialchars((string)($osoba->getTelefon() ?? '—')) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        Zákonný zástupca
    </div>
    <div class="card-body">
        <table class="table table-sm mb-0">
            <tbody>
            <tr>
                <th style="width: 220px;">Meno</th>
                <td><?= htmlspecialchars((string)($osoba->getZastupcaMeno() ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Priezvisko</th>
                <td><?= htmlspecialchars((string)($osoba->getZastupcaPriezvisko() ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?= htmlspecialchars((string)($osoba->getZastupcaEmail() ?? '—')) ?></td>
            </tr>
            <tr>
                <th>Telefón</th>
                <td><?= htmlspecialchars((string)($osoba->getZastupcaTelefon() ?? '—')) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-between">
    <a href="<?= $link->url('osoba.index') ?>" class="btn btn-outline-secondary">
        Späť na zoznam
    </a>

    <div class="d-flex gap-2">
        <?php if (!empty($canEdit)): ?>
            <a href="<?= $link->url('osoba.edit', ['id_osoba' => $osoba->getId()]) ?>" class="btn btn-primary">
                Upraviť
            </a>

            <a href="<?= $link->url('osoba.delete', ['id_osoba' => $osoba->getId()]) ?>"
               class="btn btn-outline-danger"
               onclick="return confirm('Naozaj chcete zmazať túto osobu?');">
                Zmazať
            </a>
        <?php endif; ?>

    </div>
</div>
