<?php

namespace App\Controllers;

use App\Models\Prispevok;
use App\Models\PrispevokSubor;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class PrispevokSuborController extends AppController
{

    public function index(Request $request): Response
    {
        return $this->redirect($this->url('prispevokPublic.index'));
    }


    private function uploadDir(): string
    {
        return dirname(__DIR__, 2) . '/storage/uploads/prispevky';
    }


    public function download(Request $request): Response
    {
        $idSubor = (int)$request->value('id_prispevok_subor');
        $subor = PrispevokSubor::getOne($idSubor);
        if ($subor === null) {
            throw new \Exception('Súbor nenájdený.');
        }

        $prispevok = Prispevok::getOne((int)$subor->getIdPrispevok());
        if ($prispevok === null) {
            throw new \Exception('Príspevok nenájdený.');
        }

// --- AUTORIZÁCIA ---
        if ($prispevok->getViditelnost() !== 'verejny') {

            // admin môže vždy (bez active osoby)
            if ($this->user->isLoggedIn() && $this->user->isAdmin()) {
                // OK
            } else {
                // user musí mať active osobu + obdobie a prístup k príspevku
                $activeOsoba = $this->requireActiveOsoba();
                if ($activeOsoba === null) {
                    return $this->redirect($this->url('prispevokUser.index'));
                }

                $idObdobie = (int)$this->requireActiveObdobieId();

                $allowed = Prispevok::getOneForOsoba(
                    (int)$prispevok->getId(),
                    (int)$activeOsoba->getId(),
                    $idObdobie
                );

                if ($allowed === null) {
                    return $this->redirect($this->url('prispevokUser.index'));
                }
            }
        }



        $path = rtrim($this->uploadDir(), '/') . '/' . (string)$subor->getStoredName();
        if (!is_file($path)) {
            throw new \Exception('Súbor na disku neexistuje.');
        }

        // --- SEND FILE ---
        header('Content-Type: ' . $subor->getMimeType());
        header('Content-Length: ' . (string)$subor->getSize());
        $fn = basename((string)$subor->getOriginalName());
        $fn = str_replace(["\r", "\n", '"'], '', $fn);
        header('Content-Disposition: attachment; filename="' . $fn . '"');

        readfile($path);
        exit;
    }
}
