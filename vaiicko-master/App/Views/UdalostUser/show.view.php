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

<?php
/** @var bool|null $stavUcasti */ // null|'ucast'|'neucast'
$vyzaduje = !empty($udalost['vyzaduje_reakciu']);
?>

<hr>

<?php if ($vyzaduje): ?>
    <h5>Reakcia na pozvánku</h5>

    <?php if ($stavUcasti === null): ?>
        <div class="alert alert-warning">Zatiaľ si nereagoval/a.</div>
    <?php elseif ($stavUcasti === 'ucast'): ?>
        <div class="alert alert-success">Odpoveď: Prídem ✅</div>
    <?php elseif ($stavUcasti === 'neucast'): ?>
        <div class="alert alert-danger">Odpoveď: Neprídem ❌</div>
    <?php endif; ?>

    <div class="d-flex gap-2 flex-wrap">
        <form method="post" action="<?= $link->url('ucastUser.react') ?>">
            <input type="hidden" name="id_udalost" value="<?= (int)$udalost['id_udalost'] ?>">
            <input type="hidden" name="stav" value="ucast">
            <button class="btn btn-success" type="submit">Prídem</button>
        </form>

        <form method="post" action="<?= $link->url('ucastUser.react') ?>">
            <input type="hidden" name="id_udalost" value="<?= (int)$udalost['id_udalost'] ?>">
            <input type="hidden" name="stav" value="neucast">
            <button class="btn btn-danger" type="submit">Neprídem</button>
        </form>

        <form method="post" action="<?= $link->url('ucastUser.react') ?>">
            <input type="hidden" name="id_udalost" value="<?= (int)$udalost['id_udalost'] ?>">
            <input type="hidden" name="stav" value="clear">
            <button class="btn btn-outline-secondary" type="submit">Zrušiť reakciu</button>
        </form>
    </div>

<?php else: ?>
    <h5>Dochádzka</h5>

    <?php if ($stavUcasti === 'neucast'): ?>
        <div class="alert alert-warning">Označené ako neúčasť.</div>
    <?php endif; ?>

    <div class="d-flex gap-2 flex-wrap">
        <?php if ($stavUcasti === 'neucast'): ?>
            <form method="post" action="<?= $link->url('ucastUser.react') ?>">
                <input type="hidden" name="id_udalost" value="<?= (int)$udalost['id_udalost'] ?>">
                <input type="hidden" name="stav" value="ucast">
                <button class="btn btn-outline-success" type="submit">Zrušiť neúčasť</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?= $link->url('ucastUser.react') ?>">
                <input type="hidden" name="id_udalost" value="<?= (int)$udalost['id_udalost'] ?>">
                <input type="hidden" name="stav" value="neucast">
                <button class="btn btn-outline-danger" type="submit"
                        onclick="return confirm('Naozaj označiť neúčasť?');">
                    Neprídem
                </button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<a class="btn btn-outline-secondary mt-3 me-2"
   href="<?= $link->url('prispevokUser.index', [
           'mode' => 'udalost',
           'id_udalost' => (int)$udalost['id_udalost'],
           'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('rozvrhUser.index')
   ]) ?>">
    Príspevky udalosti
</a>



<a class="btn btn-outline-secondary mt-3" href="<?= $link->url('rozvrhUser.index') ?>">
    Späť na rozvrh
</a>
