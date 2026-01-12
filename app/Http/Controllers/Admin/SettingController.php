<?php

namespace App\Modules\Setting\Controllers;

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\App;


class SettingController extends Controller
{
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
        $this->selectedMainMenu = 'setting';

        parent::__construct();
    }

    public function general()
    {
        $this->authorize('setting_general');
        $this->selectedSubMenu('setting_general');
        $settings = Setting::getAllSetting();
        return view('admin.setting.general', compact('settings'));
    }

    public function product()
    {
        $this->authorize('setting_product');
        $this->selectedSubMenu('setting_product');
        $settings = Setting::getAllSetting();
        return view('admin.setting.product', compact('settings'));
    }

    public function social()
    {
        $this->authorize('setting_social');
        $this->selectedSubMenu('setting_social');
        $settings = Setting::getAllSetting();
        return view('admin.setting.social', compact('settings'));
    }

    public function seo()
    {
        $this->authorize('setting_seo');
        $this->selectedSubMenu('setting_seo');
        $settings = Setting::getAllSetting();
        return view('admin.setting.seo', compact('settings'));
    }

    public function save(Request $request)
    {
        $lang_code = App::getLocale();
        $arrListKey = $request->settings;
        if (!isset($arrListKey['noindex'])) {
            $arrListKey['noindex'] = 0;
        }
        if (!isset($arrListKey['scheduler_publish_d_mobile'])) {
            $arrListKey['scheduler_publish_d_mobile'] = 0;
        }
        foreach ($arrListKey as $skey => $svalue) {
            if (Setting::check_exists_skey($skey)) {
                //Nếu skey đã tồn tại thì cập nhật svalue
                Setting::where('skey', $skey)->where('lang_code', $lang_code)->update(['svalue' => $svalue]);
            } else {
                Setting::insert(["skey" => "$skey", "svalue" => "$svalue", "lang_code" => "$lang_code"]);
            }
        }

        return redirect()->back();

    }
}
