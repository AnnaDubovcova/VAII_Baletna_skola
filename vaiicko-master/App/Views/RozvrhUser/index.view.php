<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \DateTime $weekStart */
/** @var \DateTime $weekEnd */
/** @var array $days */
/** @var string $prevWeek */
/** @var string $nextWeek */
/** @var string $todayWeek */
/** @var \App\Models\Osoba $activeOsoba */

$dayNames = ['Po','Ut','St','Št','Pi','So','Ne'];

?>

<h1 class="mb-2">Rozvrh</h1>
<div class="text-muted mb-3">
    Aktívna osoba: <strong><?= htmlspecialchars($activeOsoba->getMeno() . ' ' . $activeOsoba->getPriezvisko()) ?></strong>
</div>

<div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('rozvrhUser.index', ['week' => $prevWeek]) ?>">&larr; Predošlý</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('rozvrhUser.index', ['week' => $todayWeek]) ?>">Tento týždeň</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= $link->url('rozvrhUser.index', ['week' => $nextWeek]) ?>">Ďalší &rarr;</a>
</div>


<div class="week-grid">
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

                        <div class="event-name">
                            <a href="<?= $link->url('udalostUser.show', ['id_udalost' => $e['id_udalost']]) ?>">
                                <?= htmlspecialchars((string)$e['nazov']) ?>
                            </a>
                        </div>


                        <div class="event-meta">
                            <?= htmlspecialchars((string)$e['skupina_nazov']) ?>
                            <?php if (!empty($e['miesto'])): ?>
                                • <?= htmlspecialchars((string)$e['miesto']) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <?php $i++; endforeach; ?>
</div>
