<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vrarea;
use Illuminate\Support\Facades\App;

class VrareaController extends BaseController
{
    public function __construct(Vrarea $app_obj)
    {
        parent::__construct($app_obj);
        $this->export_fields = [
            "list" => [
                'id'            => 'id',
                'name'          => 'name',
                'slug'          => 'slug',                
                'order_no'      => 'order_no',                
            ],
            'file_name' => 'vr_area.js'
        ];
    }

    public function index_order_by($index_data_list){
        $index_data_list = $index_data_list->orderBy('id', 'ASC');
        return $index_data_list;
    }

    public static function exportDataToJson2($app_obj)
    {
        $export_items = $app_obj::where('state', 1)->orderBy('order_no','asc')->get();
        $data = [];
       
        foreach ($export_items as $export_item) {
            $this_export_item = [];  
            foreach($app_obj->export_fields['list']  as $k=>$v){
                if($k=='logo_image'){
                    $this_export_item[$k] = $export_item->$v;
                }else{
                    $this_export_item[$k] = $export_item->$v;
                }                
            }   
            array_push($data,$this_export_item);                
        }
        file_put_contents('js/'.$app_obj->export_fields['file_name'], json_encode($data));
        return false;
	}

    public function edit0($app_obj = false)
    {
        $app_obj                = $this->app_obj::find($app_obj);
        $lang_code              = App::getLocale();
        $object                 = $this->object;
        $query_area_category    = Vrarea::get_all(null, '.id', 'asc')->addSelect('parent_id');
        $wc_array               = ($query_area_category->get()->toArray());
        $list_area              = [];

        foreach($wc_array as $k=>$v){
            $list_area[$k]              = (object) $v;
            $list_area[$k]->parent_id   = $v["parent_id"];
        }
        $list_area = Vrarea::makeOptionsStringLoop($list_area, 0, '', $app_obj != null ? $app_obj->parent_id : null);

        return view('admin.' . $this->object['name'] . '.create', compact('object', 'app_obj','list_area'));
    }
}