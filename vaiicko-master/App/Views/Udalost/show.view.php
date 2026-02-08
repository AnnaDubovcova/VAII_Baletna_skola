<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Udalost $udalost */
/** @var array $skupiny */ // rows ['id_skupina'=>..,'nazov'=>..]
/** @var array $ucast */   // rows ['id_osoba'=>..,'meno'=>..,'priezvisko'=>..,'stav'=>..,'updated_at'=>..]
/** @var ?string $returnTo */
?>

<h1 class="page-title">Detail udalosti</h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-outline-secondary"
       href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('udalost.index') ?>">
        Späť
    </a>

    <a class="btn btn-sm btn-outline-secondary"
       href="<?= $link->url('adminPrispevok.index', [
               'mode' => 'udalost',
               'id_udalost' => (int)$udalost->getId(),
               'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('udalost.index')
       ]) ?>">
        Príspevky
    </a>


    <a class="btn btn-sm btn-outline-primary"
       href="<?= $link->url('udalost.edit', [
               'id_udalost' => (int)$udalost->getId(),
               'return_to' => !empty($returnTo) ? $returnTo : null
       ]) ?>">
        Upraviť
    </a>

</div>

<div class="card mb-3">
    <div class="card-body">
        <p class="mb-1"><b>ID:</b> <?= (int)$udalost->getId() ?></p>
        <p class="mb-1"><b>Názov:</b> <?= htmlspecialchars((string)$udalost->getNazov()) ?></p>
        <p class="mb-1"><b>Typ:</b> <?= htmlspecialchars((string)$udalost->getTyp()) ?></p>

        <p class="mb-1">
            <b>Pozvánka (vyžaduje reakciu):</b>
            <?= $udalost->vyzadujeReakciu() ? 'Áno' : 'Nie' ?>
        </p>

        <p class="mb-1"><b>Začiatok:</b> <?= htmlspecialchars((string)$udalost->getZaciatok()) ?></p>
        <p class="mb-1"><b>Koniec:</b> <?= htmlspecialchars((string)($udalost->getKoniec() ?? '—')) ?></p>
        <p class="mb-1"><b>Miesto:</b> <?= htmlspecialchars((string)($udalost->getMiesto() ?? '—')) ?></p>
        <p class="mb-0"><b>Popis:</b><br><?= nl2br(htmlspecialchars((string)($udalost->getPopis() ?? '—'))) ?></p>
    </div>
</div>

<div class="card mb-3">
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

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Účasť</h5>

        <?php if (empty($ucast)): ?>
            <div class="alert alert-secondary mb-0">
                Pre túto udalosť nie sú žiadni účastníci (skupiny nemajú členov alebo nie sú priradené).
            </div>
        <?php else: ?>
            <?php
            $cntTotal = 0;
            $cntUcast = 0;
            $cntNeucast = 0;
            $cntNoResponse = 0;

            $vyzaduje = $udalost->vyzadujeReakciu();

            foreach ($ucast as $r) {
                $cntTotal++;
                $stavDb = $r['stav'] ?? null;

                if ($vyzaduje) {
                    // pozvánka: null = bez reakcie
                    if ($stavDb === 'ucast') {
                        $cntUcast++;
                    } elseif ($stavDb === 'neucast') {
                        $cntNeucast++;
                    } else {
                        $cntNoResponse++;
                    }
                } else {
                    // tréning: default = účasť, evidujeme len neúčasť
                    if ($stavDb === 'neucast') {
                        $cntNeucast++;
                    } else {
                        $cntUcast++; // default
                    }
                }
            }
            ?>

            <div class="mb-3 d-flex flex-wrap gap-2">
                <span class="badge bg-secondary">Spolu: <?= (int)$cntTotal ?></span>

                <span class="badge bg-success">Účasť: <?= (int)$cntUcast ?></span>
                <span class="badge bg-danger">Neúčasť: <?= (int)$cntNeucast ?></span>

                <?php if ($udalost->vyzadujeReakciu()): ?>
                    <span class="badge bg-warning text-dark">Bez reakcie: <?= (int)$cntNoResponse ?></span>
                <?php endif; ?>
            </div>


            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Osoba</th>
                        <th>Stav</th>
                        <th class="text-muted">Aktualizované</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ucast as $r): ?>
                        <?php
                        $stavDb = $r['stav'] ?? null;

                        if ($udalost->vyzadujeReakciu()) {
                            // pozvánka: default = bez reakcie
                            if ($stavDb === 'ucast') {
                                $stavLabel = 'Príde';
                            } elseif ($stavDb === 'neucast') {
                                $stavLabel = 'Nepríde';
                            } else {
                                $stavLabel = 'Bez reakcie';
                            }
                        } else {
                            // tréning: default = príde, evidujeme len výnimky
                            if ($stavDb === 'neucast') {
                                $stavLabel = 'Nepríde';
                            } else {
                                $stavLabel = 'Príde (default)';
                            }
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$r['meno'] . ' ' . (string)$r['priezvisko']) ?></td>
                            <td><?= htmlspecialchars($stavLabel) ?></td>
                            <td class="text-muted">
                                <?php if (!empty($r['updated_at'])): ?>
                                    <?= date('d.m.Y H:i', strtotime((string)$r['updated_at'])) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
