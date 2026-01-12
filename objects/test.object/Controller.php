<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\M__0;
class M__0Controller extends BaseController
{
    public function __construct(M__0 $app_obj)
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
            'file_name' => 'm__1.js'
        ];
    }
    public function index2(){
        return  $this->paginate;
    }
    public static function exportDataToJson2($app_obj)
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
}