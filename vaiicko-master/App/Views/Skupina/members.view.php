<?php
/** @var \App\Models\Skupina $skupina */
/** @var \App\Models\Osoba[] $members */
/** @var \App\Models\Osoba[] $candidates */
/** @var string $q */
/** @var \App\Models\Kurz[] $kurzy */
/** @var int|null $idKurz */
/** @var \Framework\Support\LinkGenerator $link */
?>

<h1>Skupina: <?= htmlspecialchars((string)$skupina->getNazov()) ?></h1>

<hr>

<h3>Členovia skupiny</h3>

<?php if (empty($members)): ?>
    <p class="text-muted">Skupina zatiaľ nemá členov.</p>
<?php else: ?>
    <ul class="list-group mb-4">
        <?php foreach ($members as $m): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php $fullName = trim((string)$m->getMeno() . ' ' . (string)$m->getPriezvisko()); ?>

                <a href="<?= $link->url('osoba.show', [
                        'id_osoba' => $m->getId(),
                        'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('skupina.index')
                ]) ?>">
                    <?= htmlspecialchars($fullName) ?>
                </a>

                <form method="post"
                      action="<?= $link->url('skupina.removeMember') ?>"
                      class="m-0">
                    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
                    <input type="hidden" name="id_osoba" value="<?= (int)$m->getId() ?>">

                    <!-- zachovať filtre po PRG -->
                    <input type="hidden" name="q" value="<?= htmlspecialchars((string)$q) ?>">
                    <input type="hidden" name="id_kurz" value="<?= (int)($idKurz ?? 0) ?>">

                    <button type="submit" class="btn btn-sm btn-outline-danger">
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
    <input type="hidden" name="c" value="skupina">
    <input type="hidden" name="a" value="members">
    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">

    <div class="row g-2">
        <div class="col-md-5">
            <input type="text"
                   name="q"
                   value="<?= htmlspecialchars((string)$q) ?>"
                   class="form-control"
                   placeholder="Vyhľadať meno alebo priezvisko">
        </div>

        <div class="col-md-5">
            <select name="id_kurz" class="form-select">
                <option value="">Všetky kurzy</option>
                <?php foreach ($kurzy as $k): ?>
                    <option value="<?= (int)$k->getId() ?>"
                            <?= ((int)$k->getId() === (int)($idKurz ?? 0)) ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string)$k->getNazov()) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-secondary">
                Filtrovať
            </button>
        </div>
    </div>
</form>


<?php if (empty($candidates)): ?>
    <p class="text-muted">Žiadni vhodní kandidáti.</p>
<?php else: ?>
    <ul class="list-group">
        <?php foreach ($candidates as $c): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php $fullName = trim((string)$c->getMeno() . ' ' . (string)$c->getPriezvisko()); ?>

                <a href="<?= $link->url('osoba.show', [
                        'id_osoba' => $c->getId(),
                        'return_to' => $_SERVER['REQUEST_URI'] ?? $link->url('skupina.index')
                ]) ?>">
                    <?= htmlspecialchars($fullName) ?>
                </a>

                <form method="post"
                      action="<?= $link->url('skupina.addMember') ?>"
                      class="m-0">
                    <input type="hidden" name="id_skupina" value="<?= (int)$skupina->getId() ?>">
                    <input type="hidden" name="id_osoba" value="<?= (int)$c->getId() ?>">

                    <!-- zachovať filtre po PRG -->
                    <input type="hidden" name="q" value="<?= htmlspecialchars((string)$q) ?>">
                    <input type="hidden" name="id_kurz" value="<?= (int)($idKurz ?? 0) ?>">

                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        Pridať
                    </button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="d-flex justify-content-between mt-4">
    <a class="btn btn-sm btn-outline-secondary" href="<?= $link->url('skupina.index') ?>">
        Späť na skupiny
    </a>
</div>
