<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \DateTime $weekStart */
/** @var \DateTime $weekEnd */
/** @var array $days */
/** @var string $prevWeek */
/** @var string $nextWeek */
/** @var string $todayWeek */

$dayNames = ['Po','Ut','St','Št','Pi','So','Ne'];

?>

<h1 class="mb-2">Rozvrh (Admin)</h1>

<div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('adminRozvrh.index', ['week' => $prevWeek]) ?>">&larr; Predošlý</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('adminRozvrh.index', ['week' => $todayWeek]) ?>">Tento týždeň</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('adminRozvrh.index', ['week' => $nextWeek]) ?>">Ďalší &rarr;</a>
</div>


<div class="week-grid">
    <?php
    $weekStr = $weekStart->format('Y-m-d');
    $returnTo = $link->url('adminRozvrh.index', ['week' => $weekStr]);
    ?>

    <?php $i = 0; foreach ($days as $dayKey => $day): ?>
        <?php $d = $day['date']; $items = $day['items']; ?>
        <div class="day-col">
            <div class="day-title"><?= $dayNames[$i] ?> <?= $d->format('d.m.') ?></div>

            <?php if (empty($items)): ?>
                <div class="muted">Žiadne udalosti</div>
            <?php else: ?>
                <?php foreach ($items as $e): ?>
                    <div class="event-card">
                        <div class="event-time">
                            <?= date('H:i', strtotime((string)$e['zaciatok'])) ?>
                            <?php if (!empty($e['koniec'])): ?>
                                – <?= date('H:i', strtotime((string)$e['koniec'])) ?>
                            <?php endif; ?>
                        </div>


                        <a href="<?= $link->url('udalost.show', ['id_udalost' => $e['id_udalost'], 'return_to' => $returnTo]) ?>">
                            <?= htmlspecialchars((string)$e['nazov']) ?>
                        </a>



                        <div class="event-meta">
                            <?php if (!empty($e['skupiny'])): ?>
                                <?= htmlspecialchars((string)$e['skupiny']) ?>
                            <?php endif; ?>
                            <?php if (!empty($e['miesto'])): ?>
                                • <?= htmlspecialchars((string)$e['miesto']) ?>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2 d-flex gap-2 flex-wrap">
                            <a class="btn btn-outline-primary btn-sm"
                               href="<?= $link->url('udalost.edit', ['id_udalost' => $e['id_udalost'], 'return_to' => $returnTo]) ?>">
                                Upraviť
                            </a>


                            <form method="post"
                                  action="<?= $link->url('udalost.delete') ?>"
                                  class="d-inline"
                                  onsubmit="return confirm('Naozaj zmazať udalosť?');">

                                <input type="hidden" name="id_udalost" value="<?= (int)$e['id_udalost'] ?>">
                                <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo) ?>">

                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Zmazať
                                </button>
                            </form>


                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php $i++; endforeach; ?>
</div>
