<?php

namespace App\Libs;

use Mail;

class Util
{
    public static function makeHTMLOptions($_arr, $sk = 0, $flag1 = 0, $flag2 = 0, $istext = 0)
    {
        $html = "";
        if (is_array($_arr)) {
            foreach ($_arr as $k => $v) {
                $value = $k;
                $option = $v;
                if ($flag2 == 1) {
                    $value = $option;
                }
                if ($flag1 == 0) {
                    $selected = ($value == $sk) ? "selected" : "";
                } else {
                    $selected = ($option == $sk) ? "selected" : "";
                }
                $html .= "<option value='$value' $selected >" . $option . "</option>";
                if ($selected == 'selected' && $istext == 1) {
                    return str_replace("|__", "", $option);
                }
            }
            if ($istext == 1) {
                return "";
            }
            return $html;
        } else {
            return "";
        }
    }

    public static function url_category($category)
    {
        if (!$category) {
            return '';
        } else {
            return route('category', $category->slug);
        }
    }

    public static function url_page($page)
    {
        if (!$page) {
            return '';
        } else {
            return route('page_content', $page->slug);
        }
    }

    public static function url_recruitment($recruitment): string
    {
        if (!$recruitment) {
            return '';
        } else {
            return route('recruitment_detail', [$recruitment->slug, $recruitment->id]);
        }
    }

    public static function url_product($product): string
    {
        if (!$product) {
            return '';
        } else {
            return route('product_detail', [$product->slug, $product->id]);
        }
    }

    public static function url_post($post): string
    {
        if (!$post) {
            return '';
        } else {
            return route('post_detail', [$post->slug, $post->id]);
        }
    }

    public static function url_service($service): string
    {
        if (!$service) {
            return '';
        } else {
            return route('service_detail', [$service->slug, $service->id]);
        }
    }

    public static function url_category_service_detail($category): string
    {
        if (!$category) {
            return '';
        } else {
            return route('category_service_detail', [$category->slug]);
        }
    }

    public static function getListOtherImages($str, $options = 0)
    {
        $tmp = html_entity_decode($str);
        if ($options == 0) {
            $pattern = '/src="([^"]*)"/';
            preg_match_all($pattern, $tmp, $matches);
            $arr = [];
            if (is_array($matches)) {
                foreach ($matches[1] as $key => $val) {
                    $arr[] = $val;
                }
            }
            return $arr;
        } else {
            $pattern_src = '/src="([^"]*)"/';
            $pattern_alt = '/alt="([^"]*)"/';
            //Lấy SRC của ảnh
            preg_match_all($pattern_src, $tmp, $matches_src);
            if (is_array($matches_src)) {
                foreach ($matches_src[1] as $key => $val) {
                    $arr['src'][] = $val;
                }
            }
            //Lấy ALT của ảnh
            preg_match_all($pattern_alt, $tmp, $matches_alt);
            if (is_array($matches_alt)) {
                foreach ($matches_alt[1] as $key => $val) {
                    $arr['alt'][] = $val;
                }
            }
            return $arr;
        }
    }

    public static function validatePhoneNumber($phone = '')
    {
        return preg_match('/^(0[3|5|7|8|9])+([0-9]{8})\b$/', $phone);
    }

    public static function isValidEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function getOptionOrderItem($options, $char = '/')
    {
        if (!is_array($options) || count($options) == 0) {
            return '';
        }
        return implode($char, $options);
    }

    public static function sendEmail($template, $data, $subject, $to, $cc = [])
    {
        if ($template && is_array($data) && $subject && $to) {
            Mail::send($template, $data, function ($message) use ($to, $cc, $subject) {
                $message->to($to)->cc($cc)->subject($subject);
            });
        }
    }

    public static function roundAvgRating($avg_rating = 0)
    {
        $ranges = [
            [0, 0.5],
            [0.5, 1],
            [1, 1.5],
            [1.5, 2],
            [2, 2.5],
            [2.5, 3],
            [3, 3.5],
            [3.5, 4],
            [4, 4.5],
            [4.5, 5],
        ];
        foreach ($ranges as $range) {
            if ($avg_rating >= $range[0] && $avg_rating < $range[1]) {
                return str_replace('.', '-', $range[0]);
            }
        }
        return 5;
    }

    public static function makeUniqCode($prefix, $length)
    {
        $key = $prefix;
        $specialChars = uniqid();
        $chars = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L',
            'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        if (is_array($specialChars) && count($specialChars) > 0) {
            $chars = array_merge($chars, $specialChars);
        }

        $count = count($chars) - 1;

        srand((double)microtime() * 1000000);

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, $count)];
        }

        return $key;
    }

    public static function daysBetween($dt1, $dt2)
    {
        return date_diff(
            date_create($dt2),
            date_create($dt1)
        )->format('%a');
    }

    public static function dateRange($first, $last, $step = '+1 day', $output_format = 'd/m/Y')
    {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {

            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public static function getPersonAge($birthday)
    {
        $now = time();
        $dateOfBirth = strtotime($birthday);
        $difference = $now - $dateOfBirth;

        //There are 31556926 seconds in a year.
        $age = floor($difference / 31556926);

        return $age;
    }
}
