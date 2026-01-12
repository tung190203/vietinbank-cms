<?php

namespace App\Http\Controllers;

use App\Libs\Http;
use App\Models\Category;
use App\Models\District;
use App\Models\Post;
use App\Models\Province;
use App\Models\Service;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class AjaxController extends Controller
{
    public function getDistrict(Request $request)
    {
        $province_id = $request->get('province_id', 0);
        $province = Province::find($province_id);
        $data['districts'] = District::makeListDistrict($province_id, '');
        $data['price_ship'] = data_get($province, 'price_ship', 30000);
        return $this->responseJsonOk($data);
    }

    public function getWard(Request $request)
    {
        $district_code = $request->get('district_code');
        $data['wards'] = Ward::makeListWard($district_code);
        return $this->responseJsonOk($data);
    }

    public function loadMore(Request $request)
    {
        $lang_code = App::getLocale();
        $clsCategory = new Category();
        $page = intval($request->get('page', 2));
        $cat_id = intval($request->get('cat_id'));
        $type = $request->get('type');

        $clsCategory->getParentArray();
        $cat_ids = $clsCategory->getAllCatStr($cat_id);
        $cat_ids[] = $cat_id;

        if ($page < 0 || $page > 100) {
            $page = 2;
        }

        if ($type == 'service') {
            $limit = 9;
            $services = Service::where('lang_code', $lang_code)
                ->where('state', 1)
                ->whereIn('cat_id', $cat_ids)
                ->orderBy('order_no')
                ->orderBy('id', 'desc')
                ->offset($limit * ($page - 1))
                ->limit($limit)
                ->get();
            $html = view('service.service_item', compact('services'))->render();
            $next_page = ($services->count() < $limit) ? 0 : $page + 1;
        } elseif ($type == 'post') {
            $limit = 12;
            $posts = Post::with('category')
                ->where('lang_code', $lang_code)
                ->where('state', 1)
                ->whereIn('cat_id', $cat_ids)
                ->orderBy('order_no')
                ->orderBy('id', 'desc')
                ->offset($limit * ($page - 1))
                ->limit($limit)
                ->get();
            $html = view('post.post_item', compact('posts'))->render();
            $next_page = ($posts->count() < $limit) ? 0 : $page + 1;
        } else {
            return Http::responseError('Type invalid!');
        }

        return Http::responseData([
            'next_page' => $next_page,
            'html' => $html,
        ]);
    }
}
