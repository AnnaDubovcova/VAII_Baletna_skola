<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Kurz[] $kurzy */
/** @var array $stavByKurzId */
/** @var int $activeOsobaId */
?>

<h1 class="page-title">Kurzy</h1>

<div class="alert alert-info">
    Vybraná osoba ID: <b><?= (int)$activeOsobaId ?></b>
    <span class="ms-2">(neskôr zobrazíme meno osoby)</span>
</div>

<?php if (empty($kurzy)): ?>
    <div class="alert alert-secondary">
        Momentálne nie sú otvorené žiadne kurzy na prihlásenie.
    </div>
<?php else: ?>

    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>Názov</th>
            <th>Cena</th>
            <th class="text-end">Akcia</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($kurzy as $k): ?>
            <?php $kurzId = (int)$k->getId(); ?>
            <tr>
                <td><?= htmlspecialchars((string)$k->getNazov()) ?></td>
                <td>
                    <?php if ($k->getCena() === null): ?>
                        —
                    <?php else: ?>
                        <?= htmlspecialchars(number_format((float)$k->getCena(), 2, ',', ' ')) ?> €
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <?php if (isset($stavByKurzId[$kurzId])): ?>
                        <span class="badge bg-secondary">
                            <?= htmlspecialchars($stavByKurzId[$kurzId]) ?>
                        </span>
                    <?php else: ?>
                        <a class="btn btn-sm btn-success"
                           href="<?= $link->url('prihlaska.create', ['id_kurz' => $kurzId]) ?>">
                            Prihlásiť
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

<?php endif; ?>
