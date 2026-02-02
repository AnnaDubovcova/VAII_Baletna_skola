<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminController extends BaseController
{
    /**
     * Admin sekcia: povolený je len prihlásený admin.
     */
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn() && $this->user->isAdmin();
    }

    public function index(Request $request): Response
    {
        return $this->html();
    }
}
