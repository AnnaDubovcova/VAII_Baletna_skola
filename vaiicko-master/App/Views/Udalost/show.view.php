<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Udalost $udalost */
/** @var array $skupiny */ // rows ['id_skupina'=>..,'nazov'=>..]
?>

<h1 class="page-title">Detail udalosti</h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('udalost.index') ?>">Späť</a>

    <a class="btn btn-sm btn-outline-primary"
       href="<?= $link->url('udalost.edit', ['id_udalost' => (int)$udalost->getId()]) ?>">
        Upraviť
    </a>

    <a class="btn btn-sm btn-outline-danger"
       href="<?= $link->url('udalost.delete', ['id_udalost' => (int)$udalost->getId()]) ?>"
       onclick="return confirm('Naozaj chcete zmazať túto udalosť?');">
        Zmazať
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <p class="mb-1"><b>ID:</b> <?= (int)$udalost->getId() ?></p>
        <p class="mb-1"><b>Názov:</b> <?= htmlspecialchars((string)$udalost->getNazov()) ?></p>
        <p class="mb-1"><b>Typ:</b> <?= htmlspecialchars((string)$udalost->getTyp()) ?></p>
        <p class="mb-1"><b>Začiatok:</b> <?= htmlspecialchars((string)$udalost->getZaciatok()) ?></p>
        <p class="mb-1"><b>Koniec:</b> <?= htmlspecialchars((string)($udalost->getKoniec() ?? '—')) ?></p>
        <p class="mb-1"><b>Miesto:</b> <?= htmlspecialchars((string)($udalost->getMiesto() ?? '—')) ?></p>
        <p class="mb-0"><b>Popis:</b><br><?= nl2br(htmlspecialchars((string)($udalost->getPopis() ?? '—'))) ?></p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Skupiny</h5>

        <?php if (empty($skupiny)): ?>
            <div class="alert alert-secondary mb-0">Táto udalosť nemá priradené skupiny.</div>
        <?php else: ?>
            <ul class="mb-0">
                <?php foreach ($skupiny as $s): ?>
                    <li><?= htmlspecialchars((string)$s['nazov']) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
