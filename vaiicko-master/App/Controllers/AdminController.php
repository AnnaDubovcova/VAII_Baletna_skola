<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

use App\Models\Obdobie;

class AdminController extends AppController
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



    protected function html(array $data = [], string $viewName = null): ViewResponse
    {
        $activeId = $this->getActiveObdobieId();

        // Ak nie je zvolené, nastav default = najnovšie obdobie (datum_od DESC)
        if ($activeId === null) {
            $obdobiaDesc = Obdobie::getAll('1=1', [], 'datum_od DESC');
            if (!empty($obdobiaDesc)) {
                $activeId = (int)$obdobiaDesc[0]->getId();
                $this->setActiveObdobieId($activeId);
            }
        }

        // Zoznam období pre dropdown (podľa dátumu zostupne)
        $obdobia = Obdobie::getAll('1=1', [], 'datum_od DESC');

        // Globálny kontext pre layout (žiadne DB vo view)
        $data['ctx'] = [
            'activeObdobieId' => $activeId,
            'obdobia' => $obdobia,
        ];

        return parent::html($data, $viewName);
    }

}
