<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Session;
use Framework\Http\HttpException;
use App\Models\Osoba;

abstract class AppController extends BaseController
{
    /* =========================
       ACTIVE OSOBA (SESSION)
       ========================= */

    protected function getActiveOsobaId(): ?int
    {
        $session = new Session();
        $id = (int)$session->get('active_osoba_id');
        return $id > 0 ? $id : null;
    }

    /**
     * Vyžaduje aktívnu osobu zo session a overí vlastníctvo.
     * Používa sa v user workflow (kurzy, udalosti, prihlášky).
     */
    protected function requireActiveOsoba(): ?Osoba
    {
        $activeOsobaId = $this->getActiveOsobaId();
        if ($activeOsobaId === null) {
            return null;
        }

        $osoba = Osoba::getOne($activeOsobaId);
        if ($osoba === null) {
            throw new HttpException(404, 'Aktívna osoba neexistuje.');
        }

        $loggedUserId = $this->user->getIdentity()->getIdPouzivatel();
        if ((int)$osoba->getIdPouzivatel() !== (int)$loggedUserId) {
            throw new HttpException(403, 'K tejto osobe nemáte prístup.');
        }

        return $osoba;
    }

    /* =========================
       ACTIVE OBDOBIE (SESSION)
       ========================= */

    protected function getActiveObdobieId(): ?int
    {
        $session = new Session();
        $id = (int)$session->get('active_obdobie_id');
        return $id > 0 ? $id : null;
    }

    protected function setActiveObdobieId(int $id): void
    {
        (new Session())->set('active_obdobie_id', $id);
    }
}
