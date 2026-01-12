<?php

namespace App\Http\Controllers;



use App\Models\Setting;

use App\Models\Wishlist;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SELECTED_MAIN_MENU = 'selectedMainMenu';
    const SELECTED_SUB_MENU = 'selectedSubMenu';

    protected $selectedMainMenu;

    public function __construct()
    {
        $current_locale = App::currentLocale();
        View::share('current_locale', $current_locale);
        View::share(self::SELECTED_MAIN_MENU, $this->selectedMainMenu);
        View::share('sidebarCollapsed', Arr::get($_COOKIE, 'sidebar-collapsed') === 'true');

        //Code dự án
        $setting = Setting::getAllSetting();
       

        
        View::share('setting', $setting);
        //End code dự án

    }

    protected function selectedSubMenu($menuId)
    {
        View::share(self::SELECTED_SUB_MENU, $menuId);
    }

    public function responseJsonBadRequest($data = [], $message = 'BadRequest')
    {
        return $this->responseCommonJson(400, $message, $data);
    }

    public function responseJsonOk($data = [], $message = 'ok')
    {
        return $this->responseCommonJson(200, $message, $data);
    }

    public function responseJsonNotAllowed($data = [], $message = 'NotAllowed')
    {
        return $this->responseCommonJson(403, $message, $data);
    }

    protected function responseCommonJson($code, $message, $data)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data
        ], $code, [], JSON_PRETTY_PRINT);
    }
}
