<?php

namespace App\Controllers;

use App\Models\Obdobie;
use App\Models\Skupina;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\Kurz;


class SkupinaController extends SkupinaBaseController
{
    public function index(Request $request): Response
    {
        $skupiny = Skupina::getAll();
        $obdobia = Obdobie::getAll();

        // Pomocná mapa: id_obdobie => názov obdobia (aby sa vo view nevolalo getOne() v cykle)
        $obdobiaMap = [];
        foreach ($obdobia as $o) {
            $obdobiaMap[$o->getId()] = $o->getNazov();
        }

        return $this->html([
            'skupiny' => $skupiny,
            'obdobiaMap' => $obdobiaMap,
        ]);
    }

    public function create(Request $request): Response
    {
        $errors = [];
        $skupina = new Skupina();

        // Potrebujeme zoznam období na výber vo formulári
        $obdobia = Obdobie::getAll();

        if ($request->isPost()) {
            $this->fillAndValidate($request, $skupina, $errors);

            // Ak zatiaľ neexistuje žiadne obdobie, skupinu nie je kam zaradiť
            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }

            if (empty($errors)) {
                $skupina->save();
                return $this->redirect($this->url('skupina.index'));
            }
        }

        return $this->html([
            'skupina' => $skupina,
            'obdobia' => $obdobia,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form');
    }

    public function edit(Request $request): Response
    {
        $id_skupina = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($id_skupina);

        if ($skupina === null) {
            throw new \Exception('Skupina nenájdená.');
        }

        $errors = [];
        $obdobia = Obdobie::getAll();

        if ($request->isPost()) {
            $this->fillAndValidate($request, $skupina, $errors);

            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }

            if (empty($errors)) {
                $skupina->save();
                return $this->redirect($this->url('skupina.index'));
            }
        }

        return $this->html([
            'skupina' => $skupina,
            'obdobia' => $obdobia,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id_skupina = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($id_skupina);

        if ($skupina !== null) {
            $skupina->delete();
        } else {
            throw new \Exception('Skupina nenájdená.');
        }

        return $this->redirect($this->url('skupina.index'));
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_skupina');
        if ($id <= 0) {
            throw new \Framework\Http\HttpException(400, 'Neplatné ID skupiny.');
        }

        $skupina = Skupina::getOne($id);
        if ($skupina === null) {
            throw new \Framework\Http\HttpException(404, 'Skupina nebola nájdená.');
        }

        $returnTo = (string)$request->value('return_to');

        // USER: musí mať aktívnu osobu a tá musí byť členom skupiny
        if (!$this->user->isAdmin()) {
            $activeOsoba = $this->requireActiveOsoba();
            if ($activeOsoba === null) {
                return $this->redirect($this->url('osoba.index'));
            }

            if (!$skupina->hasMember((int)$activeOsoba->getId())) {
                throw new \Framework\Http\HttpException(403, 'Nemáte oprávnenie zobraziť túto skupinu.');
            }

            return $this->html([
                'skupina' => $skupina,
                'members' => $skupina->getMembers(),
                'activeOsoba' => $activeOsoba,
                'returnTo' => $returnTo,
            ]);
        }

        // ADMIN
        return $this->html([
            'skupina' => $skupina,
            'members' => $skupina->getMembers(),
            'returnTo' => $returnTo,
        ]);
    }


    /**
     * Spoločné naplnenie modelu + server-side validácia
     */
    private function fillAndValidate(Request $request, Skupina $skupina, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $idObdobieRaw = $request->value('id_obdobie');
        $popis = trim((string)$request->value('popis'));

        // --- Server-side validácia ---

        if ($nazov === '') {
            $errors['nazov'] = 'Názov je povinný.';
        } elseif (mb_strlen($nazov) > 100) {
            $errors['nazov'] = 'Názov môže mať max. 100 znakov.';
        }

        // id_obdobie musí byť číslo a musí existovať v DB
        if ($idObdobieRaw === null || $idObdobieRaw === '') {
            $errors['id_obdobie'] = 'Obdobie je povinné.';
            $idObdobie = null;
        } elseif (!ctype_digit((string)$idObdobieRaw)) {
            $errors['id_obdobie'] = 'Neplatná hodnota obdobia.';
            $idObdobie = null;
        } else {
            $idObdobie = (int)$idObdobieRaw;
            if (Obdobie::getOne($idObdobie) === null) {
                $errors['id_obdobie'] = 'Vybrané obdobie neexistuje. Obnov stránku a vyber platné obdobie.';
            }
        }

        if ($popis !== '' && mb_strlen($popis) > 1000) {
            $errors['popis'] = 'Popis môže mať max. 1000 znakov.';
        }

        // Napln model aj pri chybách (aby sa hodnoty vrátili do formulára)
        $skupina->setNazov($nazov);
        if ($idObdobie !== null) {
            $skupina->setIdObdobie($idObdobie);
        }
        $skupina->setPopis($popis === '' ? null : $popis);
    }

    /**
     * Správa členov skupiny.
     */
    public function members(Request $request): Response
    {
        $id = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($id);

        if ($skupina === null) {
            throw new \Exception('Skupina neexistuje.');
        }

        $obdobieId = (int)$skupina->getIdObdobie();
        if ($obdobieId <= 0) {
            throw new \Exception('Skupina nemá platné obdobie.');
        }

        $q = trim((string)$request->value('q'));
        $idKurz = (int)$request->value('id_kurz');
        if ($idKurz <= 0) $idKurz = null;

        // kurzy len v období skupiny (na filter)
        $kurzy = Kurz::getAll(
            'id_obdobie = :o',
            ['o' => $obdobieId],
            'nazov ASC'
        );

        return $this->html([
            'skupina'    => $skupina,
            'members'    => $skupina->getMembers(),
            'candidates' => $skupina->getCandidateOsoby($obdobieId, $q, $idKurz),
            'q'          => $q,
            'kurzy'      => $kurzy,
            'idKurz'     => $idKurz,
        ]);
    }


    public function addMember(Request $request): Response
    {
        $idSkupina = (int)$request->value('id_skupina');
        $idOsoba   = (int)$request->value('id_osoba');
        $q         = trim((string)$request->value('q'));

        $idKurz = (int)$request->value('id_kurz');
        if ($idKurz <= 0) $idKurz = null;


        $skupina = Skupina::getOne($idSkupina);
        if ($skupina === null) {
            throw new \Exception('Skupina neexistuje.');
        }

        if ($idOsoba <= 0) {
            throw new \Exception('Neplatná osoba.');
        }

        // Voliteľné, ale odporúčané: povoliť pridať len kandidáta podľa obdobia skupiny
        $obdobieId = (int)$skupina->getIdObdobie();
        if ($obdobieId <= 0) {
            throw new \Exception('Skupina nemá platné obdobie.');
        }

        if (!$skupina->isCandidateOsoba($obdobieId, $idOsoba)) {
            throw new \Exception('Osobu nie je možné pridať do skupiny (nespĺňa podmienky pre obdobie).');
        }

        $skupina->addOsoba($idOsoba);

        return $this->redirect($this->url('skupina.members', [
            'id_skupina' => $idSkupina,
            'q' => $q,
            'id_kurz' => $idKurz,
        ]));
    }

    public function removeMember(Request $request): Response
    {
        $idSkupina = (int)$request->value('id_skupina');
        $idOsoba   = (int)$request->value('id_osoba');
        $q         = trim((string)$request->value('q'));

        $skupina = Skupina::getOne($idSkupina);
        if ($skupina === null) {
            throw new \Exception('Skupina neexistuje.');
        }

        if ($idOsoba <= 0) {
            throw new \Exception('Neplatná osoba.');
        }

        $skupina->removeOsoba($idOsoba);

        return $this->redirect($this->url('skupina.members', [
            'id_skupina' => $idSkupina,
            'q' => $q,
        ]));
    }

}

