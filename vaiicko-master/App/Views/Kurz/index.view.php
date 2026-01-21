<?php
/** @var \App\Models\Kurz[] $kurzy */
/** @var array<int, string> $obdobiaMap */
/** @var array<int, string> $typyMap */
/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Kurzy</h1>
    <a href="<?= $link->url('kurz.create') ?>" class="btn btn-primary">+ Pridať nový kurz</a>
</div>

<?php if (empty($kurzy)) { ?>
    <div class="alert alert-info">Zatiaľ nemáš vytvorené žiadne kurzy.</div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Typ kurzu</th>
                <th>Obdobie</th>
                <th>Cena</th>
                <th>Prihlasovanie</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($kurzy as $kurz) { ?>
                <tr>
                    <td class="cell-truncate" title="<?= htmlspecialchars((string)$kurz->getNazov()) ?>">
                        <?= htmlspecialchars((string)$kurz->getNazov()) ?>
                    </td>

                    <td>
                        <?php
                        $idTyp = $kurz->getIdTypKurzu();
                        echo htmlspecialchars($typyMap[$idTyp] ?? ('ID: ' . (string)$idTyp));
                        ?>
                    </td>
                    <td>
                        <?php
                        $idObd = $kurz->getIdObdobie();
                        echo htmlspecialchars($obdobiaMap[$idObd] ?? ('ID: ' . (string)$idObd));
                        ?>
                    </td>
                    <td>
                        <?php
                        $cena = $kurz->getCena();
                        echo $cena === null ? '' : htmlspecialchars(number_format($cena, 2, '.', ''));
                        ?>
                    </td>
                    <td><?= $kurz->isPrihlasovanieOtvorene() ? 'Otvorené' : 'Zatvorené' ?></td>
                    <td class="text-end">
                        <a href="<?= $link->url('kurz.edit', ['id_kurz' => $kurz->getId()]) ?>"
                           class="btn btn-sm btn-outline-secondary">Upraviť</a>

                        <form action="<?= $link->url('kurz.delete') ?>" method="post"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj chceš zmazať tento kurz?');">
                            <input type="hidden" name="id_kurz" value="<?= (int)$kurz->getId() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Zmazať</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
