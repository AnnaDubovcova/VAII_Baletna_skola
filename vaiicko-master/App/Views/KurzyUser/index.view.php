<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Kurz[] $kurzy */
/** @var array $stavByKurzId */
/** @var int $activeOsobaId */
/** @var \App\Models\Osoba $activeOsoba */
/** @var array<int, \App\Models\TypKurzu> $typById */
/** @var array<int, \App\Models\Obdobie> $obdobieById */

?>

<h1 class="page-title">Kurzy</h1>

<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        Aktívna osoba:
        <b><?= htmlspecialchars((string)$activeOsoba->getMeno()) ?>
            <?= htmlspecialchars((string)$activeOsoba->getPriezvisko()) ?></b>
    </div>
    <a class="btn btn-sm btn-outline-primary" href="<?= $link->url('osoba.index') ?>">
        Zmeniť aktívnu osobu
    </a>
</div>


<?php if (empty($kurzy)): ?>
    <div class="alert alert-secondary">
        Momentálne nie sú otvorené žiadne kurzy na prihlásenie.
    </div>
<?php else: ?>

    <div class="table-responsive table-sm-scroll">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>Názov</th>
                <th>Typ</th>
                <th>Obdobie</th>
                <th>Cena</th>
                <th class="text-end">Akcia</th>
            </tr>

            </thead>
            <tbody>

            <?php foreach ($kurzy as $k): ?>
                <?php $kurzId = (int)$k->getId(); ?>
                <tr>
                    <td>
                        <a href="<?= $link->url('kurzyUser.show', ['id_kurz' => $k->getId()]) ?>">
                            <?= htmlspecialchars((string)$k->getNazov()) ?>
                        </a>
                    </td>

                    <td>
                        <?php $typId = (int)$k->getIdTypKurzu(); ?>
                        <?= isset($typById[$typId]) ? htmlspecialchars((string)$typById[$typId]->getNazov()) : ('#' . $typId) ?>
                    </td>

                    <td>
                        <?php $obdId = (int)$k->getIdObdobie(); ?>
                        <?= isset($obdobieById[$obdId]) ? htmlspecialchars((string)$obdobieById[$obdId]->getNazov()) : ('#' . $obdId) ?>
                    </td>

                    <td>
                        <?php if ($k->getCena() === null): ?>
                            —
                        <?php else: ?>
                            <?= htmlspecialchars(number_format((float)$k->getCena(), 2, ',', ' ')) ?> €
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <?php $stav = $stavByKurzId[(int)$k->getId()] ?? null; ?>

                        <?php if ($stav === null): ?>
                            <form method="post"
                                  action="<?= $link->url('prihlaska.create') ?>"
                                  class="d-inline">
                                <input type="hidden" name="id_kurz" value="<?= (int)$k->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Prihlásiť
                                </button>
                            </form>


                        <?php elseif ($stav === 'zrusena'): ?>
                            <form method="post"
                                  action="<?= $link->url('prihlaska.create') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Chcete podať prihlášku znova?');">
                                <input type="hidden" name="id_kurz" value="<?= (int)$k->getId() ?>">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    Prihlásiť znova
                                </button>
                            </form>


                        <?php else: ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($stav) ?></span>
                        <?php endif; ?>

                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

<?php endif; ?>
