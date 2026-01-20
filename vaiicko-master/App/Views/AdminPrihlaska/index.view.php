<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\PrihlaskaKurz[] $prihlasky */
/** @var string $stav */
/** @var int $idKurz */
/** @var \App\Models\Kurz[] $kurzy */
/** @var array<int, \App\Models\Kurz> $kurzById */
/** @var array<int, \App\Models\Osoba> $osobaById */
?>

<h1 class="page-title">Prihlášky (admin)</h1>

<form class="row g-2 mb-3" method="get" action="">
    <input type="hidden" name="c" value="adminPrihlaska">
    <input type="hidden" name="a" value="index">
    <div class="col-12 col-md-4">
        <label class="form-label">Stav</label>
        <select class="form-select" name="stav">
            <option value="" <?= $stav === '' ? 'selected' : '' ?>>Všetky</option>
            <option value="nova" <?= $stav === 'nova' ? 'selected' : '' ?>>nová</option>
            <option value="schvalena" <?= $stav === 'schvalena' ? 'selected' : '' ?>>schválená</option>
            <option value="zamietnuta" <?= $stav === 'zamietnuta' ? 'selected' : '' ?>>zamietnutá</option>
            <option value="zrusena" <?= $stav === 'zrusena' ? 'selected' : '' ?>>zrušená</option>
        </select>
    </div>

    <div class="col-12 col-md-6">
        <label class="form-label">Kurz</label>
        <select class="form-select" name="id_kurz">
            <option value="0" <?= $idKurz === 0 ? 'selected' : '' ?>>Všetky</option>
            <?php foreach ($kurzy as $k): ?>
                <option value="<?= (int)$k->getId() ?>" <?= $idKurz === (int)$k->getId() ? 'selected' : '' ?>>
                    <?= htmlspecialchars((string)$k->getNazov()) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-12 col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100" type="submit">Filtrovať</button>
    </div>
</form>

<?php if (empty($prihlasky)): ?>
    <div class="alert alert-secondary">Žiadne prihlášky podľa filtra.</div>
<?php else: ?>
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Študent</th>
            <th>Kurz</th>
            <th>Stav</th>
            <th>Vytvorené</th>
            <th class="text-end">Akcie</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prihlasky as $p): ?>
            <?php
            $pid = (int)$p->getId();
            $oid = (int)$p->getIdOsoba();
            $kid = (int)$p->getIdKurz();
            $osoba = $osobaById[$oid] ?? null;
            $kurz  = $kurzById[$kid] ?? null;
            $stavP = (string)$p->getStav();
            ?>
            <tr>
                <td><?= $pid ?></td>
                <td>
                    <?php if ($osoba): ?>
                        <?= htmlspecialchars($osoba->getMeno() . ' ' . $osoba->getPriezvisko()) ?>
                    <?php else: ?>
                        #<?= $oid ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($kurz): ?>
                        <?= htmlspecialchars((string)$kurz->getNazov()) ?>
                    <?php else: ?>
                        #<?= $kid ?>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-secondary"><?= htmlspecialchars($stavP) ?></span>
                </td>
                <td><?= htmlspecialchars((string)($p->getCreatedAt() ?? '')) ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary"
                       href="<?= $link->url('adminPrihlaska.show', [
                           'id' => $pid,
                           'return_to' => $link->url('adminPrihlaska.index', ['stav' => $stav, 'id_kurz' => $idKurz])
                       ]) ?>">

                        Detail
                    </a>

                    <?php if ($stavP === 'nova'): ?>
                        <a class="btn btn-sm btn-outline-success ms-2"
                           href="<?= $link->url('adminPrihlaska.approve', [
                               'id' => $pid,
                               'return_to' => $link->url('adminPrihlaska.index', ['stav' => $stav, 'id_kurz' => $idKurz])
                           ]) ?>"

                           onclick="return confirm('Schváliť prihlášku?');">
                            Schváliť
                        </a>
                        <a class="btn btn-sm btn-outline-danger ms-2"
                           href="<?= $link->url('adminPrihlaska.reject', [
                               'id' => $pid,
                               'return_to' => $link->url('adminPrihlaska.index', ['stav' => $stav, 'id_kurz' => $idKurz])
                           ]) ?>"

                           onclick="return confirm('Zamietnuť prihlášku?');">
                            Zamietnuť
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
