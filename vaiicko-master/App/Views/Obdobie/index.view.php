<?php
/** @var \App\Models\Obdobie[] $obdobia */
/** @var \Framework\Support\LinkGenerator $link */
/** @var string|null $error */

?>

<?php if (!empty($error)): ?>
    <div class="alert alert-warning">
        <?= htmlspecialchars((string)$error) ?>
    </div>
<?php endif; ?>


<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Obdobia kurzov</h1>
    <a href="<?= $link->url('obdobie.create') ?>" class="btn btn-primary">
        + Pridať nové obdobie
    </a>
</div>

<?php if (empty($obdobia)) { ?>
    <div class="alert alert-info">
        Zatiaľ nemáš vytvorené žiadne obdobia.
    </div>
<?php } else { ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Od</th>
                <th>Do</th>
                <th>Popis</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($obdobia as $obdobie) { ?>
                <tr>
                    <td><?= htmlspecialchars($obdobie->getNazov()) ?></td>
                    <td><?= htmlspecialchars($obdobie->getDatumOd()) ?></td>
                    <td><?= htmlspecialchars($obdobie->getDatumDo()) ?></td>
                    <td><?= nl2br(htmlspecialchars($obdobie->getPopis() ?? '')) ?></td>
                    <td class="text-end">
                        <a href="<?= $link->url('obdobie.edit', ['id_obdobie' => $obdobie->getId()]) ?>"
                           class="btn btn-sm btn-outline-secondary">
                            Upraviť
                        </a>

                        <form action="<?= $link->url('obdobie.delete') ?>" method="post"
                              class="d-inline-block"
                              onsubmit="return confirm('Naozaj chceš zmazať toto obdobie?');">
                            <input type="hidden" name="id_obdobie" value="<?= $obdobie->getId() ?>">
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
