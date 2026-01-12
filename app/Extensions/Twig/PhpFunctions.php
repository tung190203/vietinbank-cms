<?php

namespace App\Extensions\Twig;

use App\Libs\Util;
use App\Models\MemberNotification;
use Illuminate\Support\Facades\Auth;
use Twig\TwigFunction;
use Cart;
use Twig\Extension\AbstractExtension;

class PhpFunctions extends AbstractExtension
{

    public function getName()
    {
        return 'TwigBridge_Extension_App_Function';
    }

    public function convertDuration($duration)
    {
        $hours = floor($duration / 3600);
        $minutes = floor(($duration - $hours * 3600) / 60);
        $seconds = $duration - $hours * 3600 - $minutes * 60;

        if ($hours) {
            echo $hours . ' giờ ' . $minutes . ' phút ' . $seconds . ' giây';
        } else {
            echo $minutes . ' phút ' . $seconds . ' giây';
        }
    }

    public function resizeImage($image, $width, $height)
    {
        $str_replace = (substr($image, 1, 6) == 'public') ? 'public/uploads/' : '/uploads/';
        $pic = base64_encode(str_replace($str_replace, '', $image));
        return url('/') . "/img.php?pic=$pic&w=$width&h=$height&encode=1";
    }

    public function config($key, $default = '')
    {
        return config($key, $default);
    }

    public function env($key, $default = '')
    {
        return env($key, $default);
    }

    public function member()
    {
        return Auth::guard('member')->user();
    }

    public function url_category($category)
    {
        return Util::url_category($category);
    }

    public function url_post($post)
    {
        return Util::url_post($post);
    }

    public function url_service($service)
    {
        return Util::url_service($service);
    }

    public function url_category_service_detail($category)
    {
        return Util::url_category_service_detail($category);
    }

    public function url_product($product)
    {
        return Util::url_product($product);
    }

    public function url_page($page)
    {
        return Util::url_page($page);
    }

    public function url_recruitment($recruitment): string
    {
        return Util::url_recruitment($recruitment);
    }

    public function getDefaultAvatar($name, $size = 200)
    {
        return "https://ui-avatars.com/api/?name={$name}&size={$size}&background=random&color=fff";
    }

    public function __($key)
    {
        return __($key);
    }

    public function getNameDayOfWeek($number)
    {
        switch ($number) {
            case 1 :
                return 'Thứ 2';
                break;
            case 2 :
                return 'Thứ 3';
                break;
            case 3 :
                return 'Thứ 4';
                break;
            case 4 :
                return 'Thứ 5';
                break;
            case 5 :
                return 'Thứ 6';
                break;
            case 6 :
                return 'Thứ 7';
                break;
            case 7 :
                return 'Chủ nhật';
                break;
            default:
                break;
        }
    }


    public function getFunctions()
    {
        return [
            new TwigFunction('json_encode', 'json_encode'),
            new TwigFunction('json_decode', 'json_decode'),
            new TwigFunction('str_repeat', 'str_repeat'),
            new TwigFunction('in_array', 'in_array'),
            new TwigFunction('var_dump', 'var_dump'),
            new TwigFunction('is_checked', [$this, 'isCheckedAttribute']),
            new TwigFunction('is_selected', [$this, 'isSelectedAttribute']),
            new TwigFunction('config', [$this, 'config']),
            new TwigFunction('env', [$this, 'env']),
            new TwigFunction('member', [$this, 'member']),
            new TwigFunction('url_category', [$this, 'url_category']),
            new TwigFunction('url_post', [$this, 'url_post']),
            new TwigFunction('url_service', [$this, 'url_service']),
            new TwigFunction('url_category_service_detail', [$this, 'url_category_service_detail']),
            new TwigFunction('url_product', [$this, 'url_product']),
            new TwigFunction('url_page', [$this, 'url_page']),
            new TwigFunction('url_recruitment', [$this, 'url_recruitment']),
            new TwigFunction('resizeImage', [$this, 'resizeImage']),
            new TwigFunction('getNameDayOfWeek', [$this, 'getNameDayOfWeek']),
            new TwigFunction('getDefaultAvatar', [$this, 'getDefaultAvatar']),
            new TwigFunction('__', [$this, '__'])
        ];
    }
}
