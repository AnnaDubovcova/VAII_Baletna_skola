<?php

namespace App\Controllers;

use App\Models\Osoba;
use Framework\Http\HttpException;
use Framework\Http\Request;

abstract class OsobaBaseController extends AppController
{


    /**
     * OsobaController:
     * - admin má prístup len na show
     * - user má prístup na vlastné osoby (index/create/edit/delete/show/select)
     */
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        if ($this->user->isAdmin()) {
            return $action === 'show';
        }

        return in_array($action, ['index', 'create', 'edit', 'delete', 'show', 'select'], true);
    }

    /**
     * Overí, že osoba patrí prihlásenému používateľovi.
     */
    protected function requireOwnedOsoba(int $idOsoba): Osoba
    {
        $osoba = Osoba::getOne($idOsoba);

        if ($osoba === null) {
            throw new HttpException(404, 'Osoba nebola nájdená.');
        }

        if ((int)$osoba->getIdPouzivatel() !== (int)$this->identity()->getIdPouzivatel()) {
            throw new HttpException(403, 'K tejto osobe nemáte prístup.');
        }

        return $osoba;
    }
}
