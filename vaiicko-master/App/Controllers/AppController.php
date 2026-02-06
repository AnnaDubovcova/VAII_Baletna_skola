<?php

namespace App\Controllers;

use App\Auth\PouzivatelIdentity;
use App\Models\Obdobie;
use Framework\Core\BaseController;
use Framework\Http\Session;
use Framework\Http\HttpException;
use App\Models\Osoba;
use Framework\Http\Responses\ViewResponse;

abstract class AppController extends BaseController
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

        $loggedUserId = $this->identity()->getIdPouzivatel();
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

    protected function requireActiveObdobieId(): int
    {
        $id = $this->getActiveObdobieId();
        if ($id === null) throw new \Exception('Nie je zvolené aktívne obdobie.');
        return $id;
    }


    protected function html(array $data = [], string $viewName = null): ViewResponse
    {
        // ctx pripravujeme iba pre prihláseného usera (admin aj user)
        if ($this->user->isLoggedIn()) {
            $activeId = $this->getActiveObdobieId();

            // default = najnovšie obdobie
            if ($activeId === null) {
                $obdobiaDesc = Obdobie::getAll('1=1', [], 'datum_od DESC');
                if (!empty($obdobiaDesc)) {
                    $activeId = (int)$obdobiaDesc[0]->getId();
                    $this->setActiveObdobieId($activeId);
                }
            }

            $obdobia = Obdobie::getAll('1=1', [], 'datum_od DESC');

            $data['ctx'] = [
                'activeObdobieId' => $activeId,
                'obdobia' => $obdobia,
            ];
        }

        return parent::html($data, $viewName);
    }

}
