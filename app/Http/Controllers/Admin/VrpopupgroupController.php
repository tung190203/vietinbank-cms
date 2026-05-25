<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Vrpopupgroup;
class VrpopupgroupController extends BaseController
{
    public function __construct(Vrpopupgroup $app_obj)
    {
        parent::__construct($app_obj);
        // $this->paginate = 120;
        $this->export_fields = [
            //array_key => filed_name
            "list" => [
                'id'            => 'id',
                'name'          => 'name',
                'slug'      => 'slug',                
                'order_no'      => 'order_no',                
            ],
            'file_name' => 'vr_popup_group.js'
        ];
    }
    public function index2(){
        return  $this->paginate;
    }
    public static function exportDataToJson2___($app_obj)
    {
        // $export_items = $export_items->orderBy('order_no')->paginate($paginate);
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

    public function index_order_by($index_data_list)
    {
        $index_data_list = $index_data_list
            ->leftJoin('vr_areas', 'vr_popup_groups.vr_area_id', '=', 'vr_areas.id')
            ->select('vr_popup_groups.*', 'vr_areas.name as area_name') // Lấy name của area gán vào area_name
            ->orderBy('vr_popup_groups.id', 'desc');

        return $index_data_list;
    }

    public function save($app_obj = false, Request $request, $get_last_insert_id = false)
    {
        // Chỉ xử lý tạo slug nếu là THÊM MỚI (app_obj không có hoặc <= 0)
        if (!$app_obj || $app_obj <= 0) {
            if (!$request->filled('frm_slug')) {
                $request->merge(['frm_slug' => \Str::slug($request->get('frm_name'))]);
            }
        }
        // Nếu là CẬP NHẬT
        else {
            $current = $this->app_obj::find($app_obj);
            if ($current) {
                // Nạp lại slug cũ vào request để validate unique bỏ qua chính nó
                $request->merge(['frm_slug' => $current->slug]);
            }
        }

        return parent::save($app_obj, $request, $get_last_insert_id);
    }
    public function edit0($app_obj = false)
    {
        $app_obj = $this->app_obj::find($app_obj);
        $object  = $this->object;

        // Lấy danh sách để hiển thị trong select
        $list_areas = \App\Models\Vrarea::where('state', 1)->get();

        return view('admin.' . $this->object['name'] . '.create', compact('object', 'app_obj', 'list_areas'));
    }
}