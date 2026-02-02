<?php

namespace App\Controllers;

use App\Models\Skupina;
use App\Models\Udalost;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\DB\Connection;

class UdalostController extends AdminController
{
    public function index(Request $request): Response
    {
        $udalosti = Udalost::getAll('', [], 'zaciatok DESC');

        $con = Connection::getInstance();
        $stmt = $con->prepare("
        SELECT us.id_udalost, s.id_skupina, s.nazov
        FROM udalost_skupina us
        JOIN skupina s ON s.id_skupina = us.id_skupina
    ");
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $skupinyByUdalost = [];
        foreach ($rows as $r) {
            $skupinyByUdalost[(int)$r['id_udalost']][] = [
                'id' => (int)$r['id_skupina'],
                'nazov' => $r['nazov']
            ];
        }

        return $this->html([
            'udalosti' => $udalosti,
            'skupinyByUdalost' => $skupinyByUdalost
        ]);
    }


    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');
        $udalost = Udalost::getOne($id);

        $con = Connection::getInstance();
        $stmt = $con->prepare("
        SELECT s.id_skupina, s.nazov
        FROM udalost_skupina us
        JOIN skupina s ON s.id_skupina = us.id_skupina
        WHERE us.id_udalost = :id
    ");
        $stmt->execute(['id'=>$id]);

        return $this->html([
            'udalost'=>$udalost,
            'skupiny'=>$stmt->fetchAll(\PDO::FETCH_ASSOC)
        ]);
    }


    public function create(Request $request): Response
    {
        $udalost = new Udalost();
        $errors = [];

        $skupiny = Skupina::getAll();

        if ($request->isPost()) {
            $this->fillAndValidate($request, $udalost, $errors);

            $ids = $request->value('id_skupina') ?? [];

            if (empty($ids)) {
                $errors['skupiny'] = 'Vyberte aspoň jednu skupinu.';
            }

            if (empty($errors)) {
                $udalost->save();

                $con = Connection::getInstance();
                foreach ($ids as $sid) {
                    $stmt = $con->prepare(
                        "INSERT INTO udalost_skupina (id_udalost, id_skupina) VALUES (:u,:s)"
                    );
                    $stmt->execute([
                        'u' => $udalost->getId(),
                        's' => (int)$sid
                    ]);
                }

                return $this->redirect($this->url('udalost.index'));
            }
        }

        return $this->html([
            'udalost' => $udalost,
            'errors' => $errors,
            'skupiny' => $skupiny,
            'selectedSkupiny' => [],
            'formAction' => 'create'
        ], 'form');
    }


    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');
        $udalost = Udalost::getOne($id);
        if (!$udalost) throw new \Exception('Udalosť nenájdená.');

        $errors = [];
        $skupiny = Skupina::getAll();

        $con = Connection::getInstance();
        $stmt = $con->prepare("SELECT id_skupina FROM udalost_skupina WHERE id_udalost = :id");
        $stmt->execute(['id'=>$id]);
        $selected = array_column($stmt->fetchAll(\PDO::FETCH_ASSOC),'id_skupina');

        if ($request->isPost()) {
            $this->fillAndValidate($request, $udalost, $errors);
            $ids = $request->value('id_skupina') ?? [];

            if (empty($ids)) {
                $errors['skupiny'] = 'Vyberte aspoň jednu skupinu.';
            }

            if (empty($errors)) {
                $udalost->save();

                $con->prepare("DELETE FROM udalost_skupina WHERE id_udalost = :id")
                    ->execute(['id'=>$id]);

                foreach ($ids as $sid) {
                    $con->prepare(
                        "INSERT INTO udalost_skupina (id_udalost,id_skupina) VALUES (:u,:s)"
                    )->execute([
                        'u'=>$id,
                        's'=>(int)$sid
                    ]);
                }

                return $this->redirect($this->url('udalost.index'));
            }
        }

        return $this->html([
            'udalost'=>$udalost,
            'errors'=>$errors,
            'skupiny'=>$skupiny,
            'selectedSkupiny'=>$selected,
            'formAction'=>'edit'
        ],'form');
    }


    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');

        $con = Connection::getInstance();
        $con->prepare("DELETE FROM udalost_skupina WHERE id_udalost = :id")
            ->execute(['id'=>$id]);

        $u = Udalost::getOne($id);
        if ($u) $u->delete();

        return $this->redirect($this->url('udalost.index'));
    }


    // --------------------------------------------------------
    // Helpers (bezpečné SQL cez Connection::getInstance + prepare)
    // --------------------------------------------------------

    private function selectedSkupinyFromRequest(Request $request): array
    {
        // očakávame checkboxy name="id_skupina[]"
        $raw = $request->value('id_skupina');

        if (is_array($raw)) {
            $out = [];
            foreach ($raw as $v) {
                $out[] = (int)$v;
            }
            $out = array_values(array_unique(array_filter($out, fn($x) => $x > 0)));
            return $out;
        }

        if ($raw === null || $raw === '') {
            return [];
        }

        $id = (int)$raw;
        return $id > 0 ? [$id] : [];
    }

    private function fillAndValidate(Request $request, Udalost $udalost, array $selectedSkupiny, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $typ = trim((string)$request->value('typ'));
        $zaciatok = trim((string)$request->value('zaciatok'));
        $koniec = trim((string)$request->value('koniec'));
        $miesto = trim((string)$request->value('miesto'));
        $popis = trim((string)$request->value('popis'));

        if ($nazov === '' || mb_strlen($nazov) > 150) {
            $errors['nazov'] = 'Názov je povinný (max 150 znakov).';
        } else {
            $udalost->setNazov($nazov);
        }

        $allowedTypes = ['trening', 'nacvik', 'vystupenie', 'ine'];
        if ($typ === '' || !in_array($typ, $allowedTypes, true)) {
            $errors['typ'] = 'Neplatný typ udalosti.';
        } else {
            $udalost->setTyp($typ);
        }

        if ($zaciatok === '') {
            $errors['zaciatok'] = 'Začiatok je povinný.';
        } else {
            $udalost->setZaciatok($this->toDbDateTime($zaciatok));
        }

        if ($koniec === '') {
            $udalost->setKoniec(null);
        } else {
            $udalost->setKoniec($this->toDbDateTime($koniec));
        }

        $udalost->setMiesto($miesto === '' ? null : $miesto);
        $udalost->setPopis($popis === '' ? null : $popis);

        // validácia času: koniec >= začiatok
        if ($zaciatok !== '' && $koniec !== '') {
            try {
                $z = new \DateTime($this->toDbDateTime($zaciatok));
                $k = new \DateTime($this->toDbDateTime($koniec));
                if ($k < $z) {
                    $errors['koniec'] = 'Koniec nemôže byť pred začiatkom.';
                }
            } catch (\Throwable $e) {
                $errors['zaciatok'] = $errors['zaciatok'] ?? 'Neplatný dátum začiatku.';
                $errors['koniec'] = $errors['koniec'] ?? 'Neplatný dátum konca.';
            }
        }

        if (empty($selectedSkupiny)) {
            $errors['skupiny'] = 'Vyberte aspoň jednu skupinu.';
        }
    }

    private function toDbDateTime(string $datetimeLocal): string
    {
        // "2026-01-21T17:30" -> "2026-01-21 17:30:00"
        $s = str_replace('T', ' ', $datetimeLocal);
        if (strlen($s) === 16) {
            $s .= ':00';
        }
        return $s;
    }

    private function getSkupinyForUdalost(int $idUdalost): array
    {
        $con = Connection::getInstance();
        $stmt = $con->prepare(
            "SELECT s.id_skupina, s.nazov
             FROM udalost_skupina us
             JOIN skupina s ON s.id_skupina = us.id_skupina
             WHERE us.id_udalost = :u
             ORDER BY s.nazov ASC"
        );
        $stmt->execute(['u' => $idUdalost]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getSkupinaIdsForUdalost(int $idUdalost): array
    {
        $con = Connection::getInstance();
        $stmt = $con->prepare("SELECT id_skupina FROM udalost_skupina WHERE id_udalost = :u");
        $stmt->execute(['u' => $idUdalost]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $ids = [];
        foreach ($rows as $r) {
            $ids[] = (int)$r['id_skupina'];
        }
        return $ids;
    }

    private function saveUdalostSkupiny(int $idUdalost, array $selectedSkupiny): void
    {
        $con = Connection::getInstance();
        $con->beginTransaction();

        try {
            $stmtDel = $con->prepare("DELETE FROM udalost_skupina WHERE id_udalost = :u");
            $stmtDel->execute(['u' => $idUdalost]);

            if (!empty($selectedSkupiny)) {
                $stmtIns = $con->prepare(
                    "INSERT INTO udalost_skupina (id_udalost, id_skupina) VALUES (:u, :s)"
                );

                foreach ($selectedSkupiny as $sid) {
                    $stmtIns->execute([
                        'u' => $idUdalost,
                        's' => (int)$sid
                    ]);
                }
            }

            $con->commit();
        } catch (\Throwable $e) {
            $con->rollBack();
            throw $e;
        }
    }


    private function getSkupinyByUdalostIds(array $udalostIds): array
    {
        if (empty($udalostIds)) return [];

        $con = Connection::getInstance();

        $placeholders = [];
        $params = [];
        foreach ($udalostIds as $i => $id) {
            $key = 'u' . $i;
            $placeholders[] = ':' . $key;
            $params[$key] = (int)$id;
        }

        $sql = "SELECT us.id_udalost, s.id_skupina, s.nazov
                FROM udalost_skupina us
                JOIN skupina s ON s.id_skupina = us.id_skupina
                WHERE us.id_udalost IN (" . implode(',', $placeholders) . ")
                ORDER BY s.nazov ASC";

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $out = [];
        foreach ($rows as $r) {
            $uid = (int)$r['id_udalost'];
            $out[$uid][] = [
                'id' => (int)$r['id_skupina'],
                'nazov' => (string)$r['nazov'],
            ];
        }

        return $out;
    }
}
