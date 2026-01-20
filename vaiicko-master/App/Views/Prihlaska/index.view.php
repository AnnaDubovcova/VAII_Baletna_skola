<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\PrihlaskaKurz[] $prihlasky */
/** @var int $activeOsobaId */
?>

<h1 class="page-title">Moje prihlášky</h1>

<div class="alert alert-info">
    Vybraná osoba ID: <b><?= (int)$activeOsobaId ?></b>
</div>

<?php if (empty($prihlasky)): ?>
    <div class="alert alert-secondary">
        Zatiaľ nemáte žiadne prihlášky pre aktívnu osobu.
    </div>
<?php else: ?>
    <table class="table table-striped align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Kurz (ID)</th>
            <th>Stav</th>
            <th>Vytvorené</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($prihlasky as $p): ?>
            <tr>
                <td><?= (int)$p->getId() ?></td>
                <td><?= (int)$p->getIdKurz() ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars((string)$p->getStav()) ?></span></td>
                <td><?= htmlspecialchars((string)($p->getCreatedAt() ?? '')) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
