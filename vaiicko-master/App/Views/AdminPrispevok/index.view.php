<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var array<\App\Models\Prispevok> $prispevky */
/** @var string $mode */
/** @var array $contextParams */
/** @var ?\App\Models\Udalost $udalost */
/** @var ?\App\Models\Skupina $skupina */
/** @var ?string $returnTo */
/** @var array<int,string> $obdobiaMap */
/** @var array<int,string> $skupinyMap */
/** @var array<int,string> $udalostiMap */


$selfUrlParams = $contextParams;
if (!empty($returnTo)) {
    $selfUrlParams['return_to'] = $returnTo;
}
$selfUrl = $link->url('adminPrispevok.index', $selfUrlParams);
?>

<?php
function prispevokViditelnostLabel(
        \App\Models\Prispevok $p,
        array $obdobiaMap,
        array $skupinyMap,
        array $udalostiMap
): string {
    $v = (string)$p->getViditelnost();

    if ($v === 'verejny') {
        return 'Verejný';
    }
    if ($v === 'obdobie') {
        $id = (int)$p->getIdObdobie();
        return 'Obdobie: ' . ($obdobiaMap[$id] ?? ('ID ' . $id));
    }
    if ($v === 'skupina') {
        $id = (int)$p->getIdSkupina();
        return 'Skupina: ' . ($skupinyMap[$id] ?? ('ID ' . $id));
    }
    if ($v === 'udalost') {
        $id = (int)$p->getIdUdalost();
        return 'Udalosť: ' . ($udalostiMap[$id] ?? ('ID ' . $id));
    }
    return $v;
}

function prispevokSnippet(?string $text, int $max = 140): string {
    $s = trim(strip_tags((string)$text));
    if ($s === '') return '';
    return mb_strlen($s) > $max ? (mb_substr($s, 0, $max) . '…') : $s;
}
?>


<?php if (!empty($returnTo)): ?>
    <div class="mb-3">
        <a class="btn btn-outline-secondary"
           href="<?= htmlspecialchars($returnTo) ?>">
            ← Späť
        </a>
    </div>
<?php endif; ?>

<h1 class="page-title">Príspevky</h1>

<?php if ($mode === 'udalost'): ?>
    <div class="text-muted mb-2">
        Udalosť: <strong><?= htmlspecialchars($udalost->getNazov()) ?></strong>
    </div>
<?php elseif ($mode === 'skupina'): ?>
    <div class="text-muted mb-2">
        Skupina: <strong><?= htmlspecialchars($skupina->getNazov()) ?></strong>
    </div>
<?php endif; ?>

<div class="mb-3 d-flex gap-2">
    <?php
    if ($mode === 'udalost') {
        $newUrl = $link->url('adminPrispevok.createForUdalost', [
                'id_udalost' => $udalost->getId(),
                'return_to' => $selfUrl
        ]);
    } elseif ($mode === 'skupina') {
        $newUrl = $link->url('adminPrispevok.createForSkupina', [
                'id_skupina' => $skupina->getId(),
                'return_to' => $selfUrl
        ]);
    } else {
        $newUrl = $link->url('adminPrispevok.create', [
                'return_to' => $selfUrl
        ]);
    }
    ?>
    <a class="btn btn-primary" href="<?= $newUrl ?>">Nový príspevok</a>
</div>

<?php if (empty($prispevky)): ?>
    <p class="text-muted">Zatiaľ nie sú žiadne príspevky.</p>
<?php else: ?>
    <div class="table-responsive table-sm-scroll">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Viditeľnosť</th>
                <th>Obsah</th>
                <th>Vytvorené</th>
                <th class="text-end">Akcie</th>
            </tr>

            </thead>
            <tbody>
            <?php foreach ($prispevky as $p): ?>
                <tr>
                    <td>

                        <div class="fw-semibold"><?= htmlspecialchars((string)$p->getNazov()) ?></div>
                    </td>


                    <td>
        <span class="badge bg-light text-dark border">
            <?= htmlspecialchars(prispevokViditelnostLabel($p, $obdobiaMap, $skupinyMap, $udalostiMap)) ?>
        </span>
                    </td>

                    <td>
                        <div class="text-muted" style="font-size: 0.95em;">
                            <?= htmlspecialchars(prispevokSnippet($p->getObsah())) ?>
                        </div>
                    </td>

                    <td>
                        <?= $p->getCreatedAt()
                                ? date('d.m.Y H:i', strtotime((string)$p->getCreatedAt()))
                                : '' ?>
                    </td>

                    <td class="text-end">
                        <a class="btn btn-outline-secondary btn-sm"
                           href="<?= $link->url('adminPrispevok.show', [
                                   'id_prispevok' => $p->getId(),
                                   'return_to' => $selfUrl
                           ]) ?>">
                            Detail
                        </a>
                        <a class="btn btn-outline-primary btn-sm"
                           href="<?= $link->url('adminPrispevok.edit', [
                                   'id_prispevok' => $p->getId(),
                                   'return_to' => $selfUrl
                           ]) ?>">
                            Upraviť
                        </a>
                        <form method="post"
                              action="<?= $link->url('adminPrispevok.delete') ?>"
                              class="d-inline"
                              onsubmit="return confirm('Naozaj zmazať príspevok?');">

                            <input type="hidden" name="id_prispevok" value="<?= (int)$p->getId() ?>">
                            <input type="hidden" name="return_to" value="<?= htmlspecialchars($selfUrl) ?>">

                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                Zmazať
                            </button>
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
