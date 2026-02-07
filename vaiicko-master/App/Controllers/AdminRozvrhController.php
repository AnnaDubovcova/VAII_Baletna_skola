<?php

namespace App\Controllers;

use App\Models\Udalost;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminRozvrhController extends AdminController
{
    public function index(Request $request): Response
    {
        $idObdobie = (int)$this->requireActiveObdobieId();

        [$weekStart, $weekEnd] = $this->resolveWeekRange($request->get('week'));

        $rows = Udalost::getWeekForAdmin(
            $idObdobie,
            $weekStart->format('Y-m-d H:i:s'),
            $weekEnd->format('Y-m-d H:i:s')
        );

        $days = $this->buildWeekDays($weekStart, $rows);

        $prevWeek = (clone $weekStart)->modify('-7 days')->format('Y-m-d');
        $nextWeek = (clone $weekStart)->modify('+7 days')->format('Y-m-d');
        $todayWeek = (new \DateTime('today'))->modify('monday this week')->format('Y-m-d');

        return $this->html([
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'days' => $days,
            'prevWeek' => $prevWeek,
            'nextWeek' => $nextWeek,
            'todayWeek' => $todayWeek,
        ]);

    }

    private function resolveWeekRange(?string $weekParam): array
    {
        if ($weekParam !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekParam)) {
            $weekStart = new \DateTime($weekParam);
        } else {
            $weekStart = new \DateTime('today');
        }

        $weekStart->modify('monday this week');
        $weekStart->setTime(0, 0, 0);

        $weekEnd = (clone $weekStart)->modify('+7 days');

        return [$weekStart, $weekEnd];
    }

    private function buildWeekDays(\DateTime $weekStart, array $rows): array
    {
        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $d = (clone $weekStart)->modify("+{$i} days");
            $key = $d->format('Y-m-d');
            $days[$key] = [
                'date' => $d,
                'items' => [],
            ];
        }

        foreach ($rows as $r) {
            $key = date('Y-m-d', strtotime((string)$r['zaciatok']));
            if (isset($days[$key])) {
                $days[$key]['items'][] = $r;
            }
        }

        return $days;
    }
}
