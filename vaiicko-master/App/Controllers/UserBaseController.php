<?php

namespace App\Controllers;

use App\Auth\PouzivatelIdentity;
use App\Models\Osoba;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;

abstract class UserBaseController extends AppController
{
    /**
     * Vráti PouzivatelIdentity alebo null (ak user nie je prihlásený alebo má inú identitu).
     */
    protected function identityOrNull(): ?PouzivatelIdentity
    {
        $identity = $this->user->getIdentity();
        return ($identity instanceof PouzivatelIdentity) ? $identity : null;
    }

    /**
     * Vráti PouzivatelIdentity, inak 401.
     */
    protected function identity(): PouzivatelIdentity
    {
        $identity = $this->identityOrNull();
        if ($identity === null) {
            throw new HttpException(401, 'Používateľ nie je prihlásený.');
        }
        return $identity;
    }

    /**
     * User sekcia: povolený je len prihlásený používateľ, ktorý nie je admin.
     */
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn() && !$this->user->isAdmin();
    }

    /**
     * Vyžaduje aktívnu osobu zo session a overí, že patrí prihlásenému používateľovi.
     */
    protected function requireActiveOsoba(): ?Osoba
    {
        $activeOsobaId = (int)$this->getActiveOsobaId();

        if ($activeOsobaId <= 0) {
            return null;
        }

        $osoba = Osoba::getOne($activeOsobaId);
        if ($osoba === null) {
            throw new HttpException(404, 'Aktívna osoba neexistuje.');
        }

        $loggedUserId = $this->identity()->getIdPouzivatel();
        if ((int)$osoba->getIdPouzivatel() !== (int)$loggedUserId) {
            throw new HttpException(403, 'K tejto osobe nemáte prístup.');
        }

        return $osoba;
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

        $loggedUserId = $this->identity()->getIdPouzivatel();
        if ((int)$osoba->getIdPouzivatel() !== (int)$loggedUserId) {
            throw new HttpException(403, 'K tejto osobe nemáte prístup.');
        }

        return $osoba;
    }
}
