<?php
//vygenerovane chatom GPT

/** @var \App\Models\TypKurzu[] $typy_kurzu */
/** @var \Framework\Support\LinkGenerator $link */
/** @var string|null $error */

?>


<?php if (!empty($error)): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars((string)$error) ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Typy kurzov</h1>
    <a href="<?= $link->url('typKurzu.create') ?>" class="btn btn-primary">
        + Pridať nový typ kurzu
    </a>
</div>

<?php if (empty($typy_kurzu)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne typy kurzov.
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Popis</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($typy_kurzu as $typ) { ?>
                <tr>
                    <td><?= htmlspecialchars($typ->getNazov() ?? '') ?></td>
                    <td><?= nl2br(htmlspecialchars($typ->getPopis() ?? '')) ?></td>
                    <td class="text-end">
                        <a href="<?= $link->url('typKurzu.edit', ['id_typ_kurzu' => $typ->getId()]) ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Upraviť
                        </a>

                        <form action="<?= $link->url('typKurzu.delete') ?>"
                              method="post"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj chces zmazat tento typ kurzu?');">
                            <input type="hidden" name="id_typ_kurzu" value="<?= (int)$typ->getId() ?>">
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
