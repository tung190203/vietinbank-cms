<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Guestbook;

class GuestBookController extends BaseController
{
    public function __construct(Guestbook $app_obj)
    {
        parent::__construct($app_obj);
        $this->export_fields = [
            "list" => [
                'id'                => 'id',
                'email'             => 'email',
                'phone'             => 'phone',      
                'content'           => 'content',    
                'is_read'           => 'is_read',                          
            ],
            'file_name' => 'guestbook.js'
        ];
        $this->grid_no_order = true;
        $this->grid_no_button = true;
    }

    public function index2(){
        return  $this->paginate;
    }

    public function index_order_by($index_data_list){
        $index_data_list = $index_data_list->orderBy('created_at', 'desc');
        return $index_data_list;
    }

    public static function exportDataToJson2___($app_obj)
    {
        $export_items = $app_obj::orderBy('created_at','desc')->get();
        $data = [];
       
        foreach ($export_items as $export_item) {
            $this_export_item = [];  
            foreach($app_obj->export_fields['list']  as $k=>$v){
                $this_export_item[$k] = $export_item->$v;               
            }   
            array_push($data,$this_export_item);                
        }
        file_put_contents('js/'.$app_obj->export_fields['file_name'], json_encode($data));
        return false;
	}
}