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
        $ctx = $this->detectContext($request);
        $returnTo = $this->getSafeReturnTo($request);


        if ($ctx['type'] === 'udalost') {
            $id = (int)$ctx['id'];
            $udalost = Udalost::getOne($id);
            if ($udalost === null) {
                throw new \Exception('Udalosť nenájdená.');
            }

            $prispevky = Prispevok::getAll(
                'viditelnost = :v AND id_udalost = :id',
                ['v' => 'udalost', 'id' => $id],
                'created_at DESC'
            );

            return $this->html([
                'prispevky' => $prispevky,
                'mode' => 'udalost',
                'udalost' => $udalost,
                'contextParams' => $this->contextParams($ctx),
                'returnTo' => $returnTo,
            ]);
        }

        if ($ctx['type'] === 'skupina') {
            $id = (int)$ctx['id'];
            $skupina = Skupina::getOne($id);
            if ($skupina === null) {
                throw new \Exception('Skupina nenájdená.');
            }

            $prispevky = Prispevok::getAll(
                'viditelnost = :v AND id_skupina = :id',
                ['v' => 'skupina', 'id' => $id],
                'created_at DESC'
            );

            return $this->html([
                'prispevky' => $prispevky,
                'mode' => 'skupina',
                'skupina' => $skupina,
                'contextParams' => $this->contextParams($ctx),
                'returnTo' => $returnTo,
            ]);
        }

        // GLOBAL: verejný + obdobie pre aktívne obdobie
        $activeObdobieId = (int)$this->requireActiveObdobieId();

        $prispevky = Prispevok::getAll(
            '(viditelnost = :v1) OR (viditelnost = :v2 AND id_obdobie = :o)',
            ['v1' => 'verejny', 'v2' => 'obdobie', 'o' => $activeObdobieId],
            'created_at DESC'
        );

        return $this->html([
            'prispevky' => $prispevky,
            'mode' => 'global',
            'activeObdobieId' => $activeObdobieId,
            'contextParams' => [],
            'returnTo' => $returnTo,

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

        if ($request->isPost()) {
            // rozhodni podľa typu
            $v = (string)$prispevok->getViditelnost();

            if ($v === 'verejny' || $v === 'obdobie') {
                $this->fillAndValidateGeneral(
                    $request,
                    $prispevok,
                    $errors,
                    (int)($ctx['activeObdobieId'] ?? 0)
                );
            } else {
                // skupina / udalosť – kontext je fixný
                $this->fillAndValidateFixed(
                    $request,
                    $prispevok,
                    $errors,
                    $v,
                    $v === 'skupina'
                        ? (int)$prispevok->getIdSkupina()
                        : (int)$prispevok->getIdUdalost()
                );
            }

            if (empty($errors)) {
                $prispevok->save();
                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index'));
            }
        }

        // priprav context pre view (len informačný box)
        $context = null;
        if ($prispevok->getViditelnost() === 'skupina') {
            $context = [
                'type' => 'skupina',
                'skupina' => Skupina::getOne((int)$prispevok->getIdSkupina()),
            ];
        } elseif ($prispevok->getViditelnost() === 'udalost') {
            $context = [
                'type' => 'udalost',
                'udalost' => Udalost::getOne((int)$prispevok->getIdUdalost()),
            ];
        }

        return $this->html([
            'prispevok' => $prispevok,
            'errors' => $errors,
            'formAction' => 'edit',
            'returnTo' => $returnTo,
            'ctx' => $ctx,
            'context' => $context,
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
                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index', ['id_skupina' => $idSkupina]));

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
                return $this->redirect($returnTo ?: $this->url('adminPrispevok.index', ['id_udalost' => $idUdalost]));

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

        $active = (int)$this->requireActiveObdobieId();
        $p->setIdObdobie($active);

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

    private function detectContext(Request $request): array
    {
        $idUdalost = (int)$request->value('id_udalost');
        $idSkupina = (int)$request->value('id_skupina');

        // pravidlo: len jeden kontext
        if ($idUdalost > 0 && $idSkupina > 0) {
            throw new \Exception('Neplatný kontext: udalosť aj skupina naraz.');
        }

        if ($idUdalost > 0) {
            return ['type' => 'udalost', 'id' => $idUdalost];
        }
        if ($idSkupina > 0) {
            return ['type' => 'skupina', 'id' => $idSkupina];
        }

        return ['type' => 'global', 'id' => null];
    }

    private function contextParams(array $ctx): array
    {
        if ($ctx['type'] === 'udalost') {
            return ['id_udalost' => (int)$ctx['id']];
        }
        if ($ctx['type'] === 'skupina') {
            return ['id_skupina' => (int)$ctx['id']];
        }
        return [];
    }

}
