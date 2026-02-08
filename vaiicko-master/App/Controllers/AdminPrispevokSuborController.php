<?php

namespace App\Controllers;

use App\Models\Prispevok;
use App\Models\PrispevokSubor;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminPrispevokSuborController extends AdminController
{
    private const MAX_SIZE = 10_000_000; // 10 MB

    private const ALLOWED_EXT = ['pdf','jpg','jpeg','png','docx'];
    private const ALLOWED_MIME = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];


    public function index(Request $request): Response
    {
        return $this->redirect($this->url('adminPrispevok.index'));
    }


    private function uploadDir(): string
    {
        return dirname(__DIR__, 2) . '/storage/uploads/prispevky';
    }



    public function upload(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('adminPrispevok.index'));
        }

        $idPrispevok = (int)$request->value('id_prispevok');
        $prispevok = Prispevok::getOne($idPrispevok);
        if ($prispevok === null) {
            throw new \Exception('Príspevok nenájdený.');
        }

        $returnTo = $this->getSafeReturnTo($request)
            ?: $this->url('adminPrispevok.show', ['id_prispevok' => $idPrispevok]);

        $file = $request->file('subor');
        if ($file === null || !$file->isOk()) {
            return $this->redirect($returnTo);
        }

        $tmp = $file->getFileTempPath();
        $orig = $file->getName();
        $size = $file->getSize();

        if ($size <= 0 || $size > self::MAX_SIZE) {
            return $this->redirect($returnTo);
        }

        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if ($ext === '' || !in_array($ext, self::ALLOWED_EXT, true)) {
            return $this->redirect($returnTo);
        }

// MIME detekuj zo servera (nie z browsera)
        $mime = 'application/octet-stream';
        $fi = new \finfo(FILEINFO_MIME_TYPE);
        $detected = $fi->file($tmp);
        if (is_string($detected) && $detected !== '') {
            $mime = $detected;
        }
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            return $this->redirect($returnTo);
        }

// uloženie na disk
        $dir = $this->uploadDir();
        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new \Exception('Nepodarilo sa vytvoriť upload adresár.');
        }

        $stored = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = rtrim($dir, '/') . '/' . $stored;

        if (!$file->store($dest)) {
            throw new \Exception('Nepodarilo sa uložiť súbor.');
        }


        // DB záznam
        $s = new PrispevokSubor();
        $s->setIdPrispevok($idPrispevok);
        $s->setOriginalName($orig);
        $s->setStoredName($stored);
        $s->setMimeType($mime);
        $s->setSize($size);
        $s->save();

        return $this->redirect($returnTo);
    }

    public function delete(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('adminPrispevok.index'));
        }

        $idSubor = (int)$request->value('id_prispevok_subor');
        $subor = PrispevokSubor::getOne($idSubor);
        if ($subor === null) {
            throw new \Exception('Súbor nenájdený.');
        }

        $returnTo = $this->getSafeReturnTo($request)
            ?: $this->url('adminPrispevok.show', ['id_prispevok' => (int)$subor->getIdPrispevok()]);

        // zmaž súbor z disku
        $path = rtrim($this->uploadDir(), '/') . '/' . (string)$subor->getStoredName();
        if (is_file($path)) {
            @unlink($path);
        }

        $subor->delete();

        return $this->redirect($returnTo);
    }

    private function getSafeReturnTo(Request $request): ?string
    {
        $returnTo = $request->value('return_to');
        if (!is_string($returnTo)) return null;
        $returnTo = trim($returnTo);
        if ($returnTo === '') return null;
        if (strpos($returnTo, '://') !== false) return null;
        if (strpos($returnTo, '//') === 0) return null;
        if (strpos($returnTo, '/') !== 0) return null;
        return $returnTo;
    }
}
