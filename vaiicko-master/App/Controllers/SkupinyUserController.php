<?php

namespace App\Controllers;

use Framework\Http\Request;
use Framework\Http\Responses\Response;

class SkupinyUserController extends UserBaseController
{
    public function index(Request $request): Response
    {
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }

        $obdobieId = $this->getActiveObdobieId();
        if ($obdobieId === null) {
            // default by sa mal nastaviť v AppController::html(),
            // ale pre istotu fallback:
            return $this->redirect($this->url('home.index'));
        }

        $skupiny = $activeOsoba->getSkupiny((int)$obdobieId);

        return $this->html([
            'activeOsoba' => $activeOsoba,
            'skupiny' => $skupiny,
        ]);
    }
}
