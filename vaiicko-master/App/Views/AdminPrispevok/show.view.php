<?php
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Prispevok $prispevok */
/** @var ?string $returnTo */
/** @var \App\Models\PrispevokSubor[] $subory */
?>

<h1 class="page-title">Detail príspevku</h1>

<div class="mb-3 d-flex gap-2">
    <a class="btn btn-outline-secondary"
       href="<?= !empty($returnTo) ? htmlspecialchars($returnTo) : $link->url('adminPrispevok.index') ?>">
        Späť
    </a>

    <a class="btn btn-outline-primary"
       href="<?= $link->url('adminPrispevok.edit', [
               'id_prispevok' => $prispevok->getId(),
               'return_to' => $returnTo ?: $link->url('adminPrispevok.index')
       ]) ?>">
        Upraviť
    </a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h4><?= htmlspecialchars((string)$prispevok->getNazov()) ?></h4>
        <div class="text-muted mb-2">
            Viditeľnosť: <strong><?= htmlspecialchars((string)$prispevok->getViditelnost()) ?></strong>
            <?php if ($prispevok->getCreatedAt()): ?>
                • <?= date('d.m.Y H:i', strtotime((string)$prispevok->getCreatedAt())) ?>
            <?php endif; ?>
        </div>
        <div><?= nl2br(htmlspecialchars((string)$prispevok->getObsah())) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Prílohy</h5>

        <form method="post"
              action="<?= $link->url('adminPrispevokSubor.upload') ?>"
              enctype="multipart/form-data"
              class="mb-3 d-flex gap-2 align-items-center flex-wrap">

            <input type="hidden" name="id_prispevok" value="<?= (int)$prispevok->getId() ?>">
            <?php if (!empty($returnTo)): ?>
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($link->url('adminPrispevok.show', [
                        'id_prispevok' => $prispevok->getId(),
                        'return_to' => $returnTo
                ])) ?>">
            <?php endif; ?>

            <input class="form-control" type="file" name="subor" required>
            <button class="btn btn-primary" type="submit">Nahrať</button>

            <div class="form-text">
                Povolené: pdf, jpg, png, docx (max 10 MB)
            </div>
        </form>

        <?php if (empty($subory)): ?>
            <div class="alert alert-secondary mb-0">Zatiaľ nie sú nahraté žiadne prílohy.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Názov</th>
                        <th>Veľkosť</th>
                        <th>Vytvorené</th>
                        <th class="text-end">Akcie</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subory as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$s->getOriginalName()) ?></td>
                            <td><?= number_format(((int)$s->getSize()) / 1024, 1, ',', ' ') ?> KB</td>
                            <td>
                                <?= $s->getCreatedAt() ? date('d.m.Y H:i', strtotime((string)$s->getCreatedAt())) : '' ?>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="<?= $link->url('prispevokSubor.download', ['id_prispevok_subor' => $s->getId()]) ?>">
                                    Stiahnuť
                                </a>

                                <form method="post"
                                      action="<?= $link->url('adminPrispevokSubor.delete') ?>"
                                      class="d-inline-block"
                                      onsubmit="return confirm('Naozaj zmazať prílohu?');">
                                    <input type="hidden" name="id_prispevok_subor" value="<?= (int)$s->getId() ?>">
                                    <input type="hidden" name="return_to"
                                           value="<?= htmlspecialchars($link->url('adminPrispevok.show', [
                                                   'id_prispevok' => $prispevok->getId(),
                                                   'return_to' => $returnTo ?: $link->url('adminPrispevok.index')
                                           ])) ?>">
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Zmazať</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
