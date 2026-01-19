<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \App\Models\Osoba[] $osoby */

//$view->setLayout('root');
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2>Moje osoby</h2>
        <a class="btn btn-primary" href="<?= $link->url('osoba.create') ?>">Pridat osobu</a>
    </div>

    <?php if (empty($osoby)): ?>
        <div class="alert alert-info">Zatial nemate ziadne osoby.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Datum narodenia</th>
                <th>Kontakt</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($osoby as $o): ?>
                <tr>
                    <td><?= htmlspecialchars((string)$o->getMeno()) ?></td>
                    <td><?= htmlspecialchars((string)$o->getPriezvisko()) ?></td>
                    <td><?= htmlspecialchars((string)$o->getDatumNarodenia()) ?></td>
                    <td>
                        <?php
                        $kontakt = [];
                        if ($o->getEmail()) { $kontakt[] = $o->getEmail(); }
                        if ($o->getTelefon()) { $kontakt[] = $o->getTelefon(); }
                        echo htmlspecialchars(implode(', ', $kontakt));
                        ?>
                    </td>
                    <td class="text-end">
                        <a class="btn btn-sm btn-outline-secondary"
                           href="<?= $link->url('osoba.edit', ['id_osoba' => $o->getId()]) ?>">Upravit</a>

                        <a class="btn btn-sm btn-outline-danger"
                           href="<?= $link->url('osoba.delete', ['id_osoba' => $o->getId()]) ?>"
                           onclick="return confirm('Naozaj chcete zmazat tuto osobu?');">Zmazat</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
