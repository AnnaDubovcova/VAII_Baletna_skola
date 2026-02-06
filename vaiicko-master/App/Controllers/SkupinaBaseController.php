<?php

namespace App\Controllers;

use Framework\Http\Request;

abstract class SkupinaBaseController extends AppController
{
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) {
            return false;
        }

        if ($this->user->isAdmin()) {
            return true; // admin má prístup ku všetkému v skupinách
        }

        // user môže len show
        return $action === 'show';
    }
}
