<?php

namespace App\Controllers;

use App\Models\Obdobie;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class ContextController extends AppController
{
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn();
    }

    public function index(Request $request): Response
    {
        throw new HttpException(404, 'Not found');
    }

    public function setActiveObdobie(Request $request): Response
    {
        $id = (int)$request->value('id_obdobie');
        if ($id <= 0 || Obdobie::getOne($id) === null) {
            // neprepínaj na neexistujúce obdobie
            throw new HttpException(400, 'Neplatné obdobie.');
        }

        $this->setActiveObdobieId($id);

        $returnUrl = (string)$request->value('return_url');
        if ($returnUrl === '') {
            $returnUrl = $this->url('home.index');
        }

        return $this->redirect($returnUrl);
    }
}
