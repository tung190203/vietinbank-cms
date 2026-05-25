<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Vrarea;


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
        $object                 = $this->object;
        return view('admin.' . $this->object['name'] . '.create', compact('object', 'app_obj'));
    }

    public function save($app_obj = false, Request $request, $get_last_insert_id = false)
    {
        // TRƯỜNG HỢP THÊM MỚI (app_obj không tồn tại hoặc <= 0)
        if (!$app_obj || $app_obj <= 0) {
            $new_slug = 'slug-' . \Str::slug($request->get('frm_name'));
            $request->merge(['frm_slug' => $new_slug]);
        }
        // TRƯỜNG HỢP CẬP NHẬT
        else {
            $current_item = $this->app_obj::find($app_obj);
            if ($current_item) {
                $request->merge(['frm_slug' => $current_item->slug]);
            }
        }
        return parent::save($app_obj, $request, $get_last_insert_id);
    }
}