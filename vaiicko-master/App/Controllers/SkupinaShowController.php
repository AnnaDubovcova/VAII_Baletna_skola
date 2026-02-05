<?php

namespace App\Controllers;

use App\Models\Skupina;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class SkupinaShowController extends AppController
{
    public function authorize(Request $request, string $action): bool
    {
        // show je pre prihlásených (admin aj user)
        return $this->user->isLoggedIn() && $action === 'show';
    }

    public function index(Request $request): Response
    {
        throw new HttpException(404, 'Not found');
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_skupina');
        if ($id <= 0) {
            throw new HttpException(400, 'Neplatné ID skupiny.');
        }

        $skupina = Skupina::getOne($id);
        if ($skupina === null) {
            throw new HttpException(404, 'Skupina nebola nájdená.');
        }

        $returnTo = (string)$request->value('return_to');

        // ADMIN: vidí všetko
        if ($this->user->isAdmin()) {
            return $this->html([
                'skupina' => $skupina,
                'members' => $skupina->getMembers(),
                'returnTo' => $returnTo,
            ]);
        }

        // USER: musí mať aktívnu osobu a tá musí byť členom skupiny
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }

        if (!$skupina->hasMember((int)$activeOsoba->getId())) {
            throw new HttpException(403, 'Nemáte oprávnenie zobraziť túto skupinu.');
        }

        return $this->html([
            'skupina' => $skupina,
            'members' => $skupina->getMembers(),
            'activeOsoba' => $activeOsoba,
            'returnTo' => $returnTo,
        ]);
    }
}
