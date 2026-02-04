<?php
/** @var \App\Models\Skupina $skupina */
/** @var array $members */
/** @var array $candidates */
/** @var string $q */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1>Skupina: <?= htmlspecialchars($skupina->getNazov()) ?></h1>

<hr>

<h3>Členovia skupiny</h3>

<?php if (empty($members)): ?>
    <p class="text-muted">Skupina zatiaľ nemá členov.</p>
<?php else: ?>
    <ul class="list-group mb-4">
        <?php foreach ($members as $o): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($o->getPriezvisko() . ' ' . $o->getMeno()) ?>
                <form method="post"
                      action="<?= $link->url('skupina.removeMember') ?>"
                      class="m-0">
                    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
                    <input type="hidden" name="id_osoba" value="<?= (int)$o->getId() ?>">
                    <button class="btn btn-sm btn-outline-danger">
                        Odobrať
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<hr>

<h3>Pridať člena</h3>

<form method="get" class="mb-3">
    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
    <input type="text"
           name="q"
           value="<?= htmlspecialchars($q) ?>"
           class="form-control"
           placeholder="Vyhľadať meno alebo priezvisko">
</form>

<?php if (empty($candidates)): ?>
    <p class="text-muted">Žiadni vhodní kandidáti.</p>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($candidates as $o): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($o->getPriezvisko() . ' ' . $o->getMeno()) ?>
                <form method="post"
                      action="<?= $link->url('skupina.addMember') ?>"
                      class="m-0">
                    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
                    <input type="hidden" name="id_osoba" value="<?= (int)$o->getId() ?>">
                    <button class="btn btn-sm btn-outline-primary">
                        Pridať
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
