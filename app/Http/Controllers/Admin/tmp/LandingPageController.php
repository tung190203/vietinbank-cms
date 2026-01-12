<?php

namespace App\Modules\Setting\Controllers;

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\App;


class LandingPageController extends Controller
{
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
        $this->selectedMainMenu = 'landing_page';

        parent::__construct();
    }

    public function home()
    {
        $this->authorize('landing_page_home');
        $this->selectedSubMenu('landing_page_home');
        $home_data = Setting::getSettingByKey('home_data');
        $home_data = unserialize($home_data);
        return view('admin.landing_page.home', compact('home_data'));
    }

    public function save(Request $request, $key)
    {
        $lang_code = App::getLocale();
        $keys = $request->get($key);
        $keys = serialize($keys);
        if (Setting::check_exists_skey($key)) {
            //Nếu skey đã tồn tại thì cập nhật svalue
            Setting::where('skey', $key)->where('lang_code', $lang_code)->update(['svalue' => $keys]);
        } else {
            Setting::insert(["skey" => $key, "svalue" => "$keys", "lang_code" => "$lang_code"]);
        }
        return redirect()->back();
    }
}
