<?php
/** @var \App\Models\Skupina[] $skupiny */
/** @var array<int, string> $obdobiaMap */
/** @var \Framework\Support\LinkGenerator $link */
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Skupiny</h1>
    <a href="<?= $link->url('skupina.create') ?>" class="btn btn-primary">
        + Pridať novú skupinu
    </a>
</div>

<?php if (empty($skupiny)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne skupiny.
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Obdobie</th>
                <th>Popis</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($skupiny as $skupina) { ?>
                <tr>
                    <td><?= htmlspecialchars($skupina->getNazov() ?? '') ?></td>
                    <td>
                        <?php
                        $idObdobie = $skupina->getIdObdobie();
                        echo htmlspecialchars($obdobiaMap[$idObdobie] ?? ('ID: ' . (string)$idObdobie));
                        ?>
                    </td>
                    <td><?= nl2br(htmlspecialchars($skupina->getPopis() ?? '')) ?></td>
                    <td class="text-end">
                        <a href="<?= $link->url('skupina.edit', ['id_skupina' => $skupina->getId()]) ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Upraviť
                        </a>

                        <form action="<?= $link->url('skupina.delete') ?>" method="post"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj chceš zmazať túto skupinu?');">
                            <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Zmazať
                            </button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>
