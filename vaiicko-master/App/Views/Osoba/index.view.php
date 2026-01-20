<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Osoba[] $osoby */
/** @var int|null $activeOsobaId */
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2>Moje osoby</h2>
        <a class="btn btn-primary" href="<?= $link->url('osoba.create') ?>">Pridať osobu</a>
    </div>

    <?php if (empty($osoby)): ?>
        <div class="alert alert-info">Zatiaľ nemáte žiadne osoby.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Dátum narodenia</th>
                <th>Kontakt</th>
                <th class="text-end">Akcie</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($osoby as $o): ?>
                <tr>

                    <td>
                        <?= htmlspecialchars((string)$o->getMeno()) ?>

                        <?php if (!empty($activeOsobaId) && (int)$activeOsobaId === (int)$o->getId()): ?>
                            <span class="badge bg-primary ms-2">Aktívna</span>
                        <?php endif; ?>
                    </td>

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

                        <?php if (!empty($activeOsobaId) && (int)$activeOsobaId === (int)$o->getId()): ?>
                            <span class="btn btn-sm btn-outline-primary disabled">Aktívna</span>
                        <?php else: ?>
                            <a class="btn btn-sm btn-success"
                               href="<?= $link->url('osoba.select', ['id_osoba' => $o->getId()]) ?>">
                                Použiť
                            </a>
                        <?php endif; ?>

                        <a class="btn btn-sm btn-outline-primary"
                           href="<?= $link->url('osoba.show', ['id_osoba' => $o->getId()]) ?>">
                            Detail
                        </a>

                        <a class="btn btn-sm btn-outline-secondary"
                           href="<?= $link->url('osoba.edit', ['id_osoba' => $o->getId()]) ?>">
                            Upraviť
                        </a>

                        <a class="btn btn-sm btn-outline-danger"
                           href="<?= $link->url('osoba.delete', ['id_osoba' => $o->getId()]) ?>"
                           onclick="return confirm('Naozaj chcete zmazať túto osobu?');">
                            Zmazať
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    <?php endif; ?>
</div>
