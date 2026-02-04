<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Udalost[] $udalosti */
/** @var array<int, array<int, array{id:int, nazov:string}>> $skupinyByUdalost */
?>

<h1 class="page-title">Udalosti</h1>



<div class="mb-3 d-flex gap-2">
    <a class="btn btn-primary" href="<?= $link->url('udalost.create') ?>">
        <i class="bi bi-plus-lg"></i> Nová udalosť
    </a>
</div>

<?php if (empty($udalosti)): ?>
    <div class="alert alert-secondary">Zatiaľ nie sú vytvorené žiadne udalosti.</div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Názov</th>
                <th>Typ</th>
                <th>Začiatok</th>
                <th>Koniec</th>
                <th>Skupiny</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($udalosti as $u): ?>
                <?php
                $uid = (int)($u->getId() ?? 0);
                $sk = $skupinyByUdalost[$uid] ?? [];
                ?>
                <tr>
                    <td><?= $uid ?></td>
                    <td>
                        <a href="<?= $link->url('udalost.show', ['id_udalost' => $uid]) ?>">
                            <?= htmlspecialchars((string)$u->getNazov()) ?>
                        </a>
                    </td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars((string)$u->getTyp()) ?></span></td>
                    <td><?= htmlspecialchars((string)$u->getZaciatok()) ?></td>
                    <td><?= htmlspecialchars((string)($u->getKoniec() ?? '—')) ?></td>
                    <td>
                        <?php if (empty($sk)): ?>
                            <span class="text-muted">—</span>
                        <?php else: ?>
                            <?php foreach ($sk as $one): ?>
                                <span class="badge bg-light text-dark border">
                                    <?= htmlspecialchars((string)$one['nazov']) ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary"
                           href="<?= $link->url('udalost.show', ['id_udalost' => $uid]) ?>">
                            Detail
                        </a>
                        <a class="btn btn-sm btn-outline-primary ms-2"
                           href="<?= $link->url('udalost.edit', ['id_udalost' => $uid]) ?>">
                            Upraviť
                        </a>
                        <a class="btn btn-sm btn-outline-danger ms-2"
                           href="<?= $link->url('udalost.delete', ['id_udalost' => $uid]) ?>"
                           onclick="return confirm('Naozaj chcete zmazať túto udalosť?');">
                            Zmazať
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
