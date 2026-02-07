<?php

namespace App\Controllers;

use App\Models\Prispevok;
use App\Models\Skupina;
use App\Models\Udalost;
use App\Models\Obdobie;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminPrispevokController extends AdminController
{
    public function index(Request $request): Response
    {
        $prispevky = Prispevok::getAll('', [], 'created_at DESC');

        return $this->html([
            'prispevky' => $prispevky,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_prispevok');
        $p = Prispevok::getOne($id);

        if ($p === null) {
            throw new \Exception('Príspevok nenájdený.');
        }

        $returnTo = $this->getSafeReturnTo($request);

        return $this->html([
            'prispevok' => $p,
            'returnTo' => $returnTo,
        ]);
    }

    /**
     * Univerzálny create: iba VEREJNÝ alebo OBDOBIE
     */
    public function create(Request $request): Response
    {
        $prispevok = new Prispevok();
        $errors = [];

        $returnTo = $this->getSafeReturnTo($request);
        $ctx = $this->getContextOptions();

        // default: nový príspevok = verejný
        if ($prispevok->getViditelnost() === null) {
            $prispevok->setViditelnost('verejny');
        }

        if ($request->isPost()) {
            $this->fillAndValidateGeneral($request, $prispevok, $errors, (int)($ctx['activeObdobieId'] ?? 0));

            if (empty($errors)) {
                $prispevok->save();

                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
            }
        }

        return $this->html([
            'prispevok' => $prispevok,
            'errors' => $errors,
            'formAction' => 'create',
            'returnTo' => $returnTo,
            'ctx' => $ctx,
            'context' => null, // pre view
        ], 'form');
    }

    /**
     * Edit: dovoľujeme editovať len verejný/obdobie cez tento formulár.
     * Skupina/udalosť sa tvoria cez createFor... a neskôr môžeme spraviť špeciálny edit.
     */
    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id_prispevok');
        $prispevok = Prispevok::getOne($id);

        if ($prispevok === null) {
            throw new \Exception('Príspevok nenájdený.');
        }

        $errors = [];
        $returnTo = $this->getSafeReturnTo($request);
        $ctx = $this->getContextOptions();

        // Ak je to skupina/udalosť, nepustíme to do univerzálneho edit formulára
        $v = (string)$prispevok->getViditelnost();
        if ($v === 'skupina' || $v === 'udalost') {
            // aby admin neskončil v “nesprávnom” formulári
            return $this->redirect($this->url('adminPrispevok.show', ['id_prispevok' => $prispevok->getId()]));
        }

        if ($request->isPost()) {
            $this->fillAndValidateGeneral($request, $prispevok, $errors, (int)($ctx['activeObdobieId'] ?? 0));

            if (empty($errors)) {
                $prispevok->save();

                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
            }
        }

        return $this->html([
            'prispevok' => $prispevok,
            'errors' => $errors,
            'formAction' => 'edit',
            'returnTo' => $returnTo,
            'ctx' => $ctx,
            'context' => null,
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id_prispevok');
        $p = Prispevok::getOne($id);

        if ($p === null) {
            throw new \Exception('Príspevok nenájdený.');
        }

        $returnTo = $this->getSafeReturnTo($request);

        $p->delete();

        return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
    }

    // ====== FIXNÉ create z detailov ======

    public function createForSkupina(Request $request): Response
    {
        $idSkupina = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($idSkupina);

        if ($skupina === null) {
            throw new \Exception('Skupina nenájdená.');
        }

        $p = new Prispevok();
        $p->setViditelnost('skupina');
        $p->setIdSkupina($idSkupina);

        $errors = [];
        $returnTo = $this->getSafeReturnTo($request);

        if ($request->isPost()) {
            $this->fillAndValidateFixed($request, $p, $errors, 'skupina', $idSkupina);

            if (empty($errors)) {
                $p->save();
                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
            }
        }

        return $this->html([
            'prispevok' => $p,
            'errors' => $errors,
            'formAction' => 'createForSkupina',
            'returnTo' => $returnTo,
            'ctx' => $this->getContextOptions(), // aby view malo obdobia (hoci ich tu nepoužije)
            'context' => [
                'type' => 'skupina',
                'skupina' => $skupina,
            ],
        ], 'form');
    }

    public function createForUdalost(Request $request): Response
    {
        $idUdalost = (int)$request->value('id_udalost');
        $udalost = Udalost::getOne($idUdalost);

        if ($udalost === null) {
            throw new \Exception('Udalosť nenájdená.');
        }

        $p = new Prispevok();
        $p->setViditelnost('udalost');
        $p->setIdUdalost($idUdalost);

        $errors = [];
        $returnTo = $this->getSafeReturnTo($request);

        if ($request->isPost()) {
            $this->fillAndValidateFixed($request, $p, $errors, 'udalost', $idUdalost);

            if (empty($errors)) {
                $p->save();
                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
            }
        }

        return $this->html([
            'prispevok' => $p,
            'errors' => $errors,
            'formAction' => 'createForUdalost',
            'returnTo' => $returnTo,
            'ctx' => $this->getContextOptions(),
            'context' => [
                'type' => 'udalost',
                'udalost' => $udalost,
            ],
        ], 'form');
    }

    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Univerzálna validácia pre admin formulár (len verejny/obdobie)
     */
    private function fillAndValidateGeneral(Request $request, Prispevok $p, array &$errors, int $activeObdobieId): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $obsah = trim((string)$request->value('obsah'));
        $vid = trim((string)$request->value('viditelnost'));

        if ($nazov === '' || mb_strlen($nazov) > 150) {
            $errors['nazov'] = 'Názov je povinný (max 150 znakov).';
        } else {
            $p->setNazov($nazov);
        }

        if ($obsah === '') {
            $errors['obsah'] = 'Obsah je povinný.';
        } else {
            $p->setObsah($obsah);
        }

        // povolíme iba tieto 2 v univerzálnom formulári
        $allowed = ['verejny', 'obdobie'];
        if (!in_array($vid, $allowed, true)) {
            $errors['viditelnost'] = 'V tomto formulári môžeš vytvoriť iba verejný príspevok alebo príspevok pre obdobie.';
            // bezpečný default
            $vid = 'verejny';
        }

        $p->setViditelnost($vid);
        $p->setIdSkupina(null);
        $p->setIdUdalost(null);

        if ($vid === 'verejny') {
            $p->setIdObdobie(null);
            return;
        }

        // vid === 'obdobie'
        $oidRaw = $request->value('id_obdobie');
        $oid = (int)$oidRaw;

        // ak admin nič nevybral, default na active obdobie (ak existuje)
        if ($oid <= 0 && $activeObdobieId > 0) {
            $oid = $activeObdobieId;
        }

        if ($oid <= 0) {
            $errors['id_obdobie'] = 'Vyber obdobie.';
        } else {
            // voliteľné: over existenciu
            if (Obdobie::getOne($oid) === null) {
                $errors['id_obdobie'] = 'Zvolené obdobie neexistuje.';
            } else {
                $p->setIdObdobie($oid);
            }
        }
    }

    private function getContextOptions(): array
    {
        $activeObdobieId = $this->getActiveObdobieId();
        $obdobia = Obdobie::getAll('', [], 'datum_od DESC');

        return [
            'activeObdobieId' => $activeObdobieId,
            'obdobia' => $obdobia,
        ];
    }

    private function getSafeReturnTo(Request $request): ?string
    {
        $returnTo = $request->value('return_to');

        if (!is_string($returnTo)) {
            return null;
        }
        $returnTo = trim($returnTo);
        if ($returnTo === '') {
            return null;
        }

        if (strpos($returnTo, '://') !== false) {
            return null;
        }
        if (strpos($returnTo, '//') === 0) {
            return null;
        }

        return $returnTo;
    }

    /**
     * Fixný kontext: skupina/udalosť
     */
    private function fillAndValidateFixed(
        Request $request,
        Prispevok $p,
        array &$errors,
        string $type,
        int $id
    ): void {
        $nazov = trim((string)$request->value('nazov'));
        $obsah = trim((string)$request->value('obsah'));

        if ($nazov === '' || mb_strlen($nazov) > 150) {
            $errors['nazov'] = 'Názov je povinný (max 150 znakov).';
        } else {
            $p->setNazov($nazov);
        }

        if ($obsah === '') {
            $errors['obsah'] = 'Obsah je povinný.';
        } else {
            $p->setObsah($obsah);
        }

        $p->setViditelnost($type);
        $p->setIdObdobie(null);
        $p->setIdSkupina(null);
        $p->setIdUdalost(null);

        if ($type === 'skupina') {
            $p->setIdSkupina($id);
        } elseif ($type === 'udalost') {
            $p->setIdUdalost($id);
        } else {
            $errors['viditelnost'] = 'Neplatný fixný kontext.';
        }
    }
}
