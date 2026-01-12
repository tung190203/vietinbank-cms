<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Setting extends Model
{
    protected $table = 'settings';

    public static function check_exists_skey($skey = '')
    {
        $lang_code = App::getLocale();
        if ($skey == '') {
            return false;
        }
        $setting = self::where('lang_code', $lang_code)->where('skey', $skey)->first();
        if (isset($setting)) {
            return true;//Đã tồn tại
        }
        return false;
    }

    public static function getAllSetting()
    {
        static $cached = [];
        if (isset($cached['all_setting'])) {
            return $cached['all_setting'];
        } else {
            $lang_code = App::getLocale();
            $settings = self::where('lang_code', $lang_code)->get();
            $results = [];
            foreach ($settings as $setting) {
                $results[$setting->skey] = $setting->svalue;
            }
            $cached['all_setting'] = $results;
            return $results;
        }

    }

    public static function getSettingByKey($key, $default = '')
    {
        $lang_code = App::getLocale();
        $setting = Setting::where('lang_code', $lang_code)
            ->where('skey', $key)
            ->first();
        return data_get($setting, 'svalue', $default);
    }
}
