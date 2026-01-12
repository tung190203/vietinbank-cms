<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Productcategory;

use Illuminate\Support\Facades\App;


class ProductController extends BaseController
{
    public function __construct(Product $app_obj)
    {
        parent::__construct($app_obj);
        // $this->paginate = 120;
        $this->export_fields = [
            "list" => [
                'id'            => 'id',
                'name'          => 'name',
                'excerpt'          => 'excerpt',
                'company'       => 'product_category',    
                'product_images'   => 'product_images',
                'video_urls'     => 'video_urls',                
                'description'   => 'description',                
                'order_no'      => 'order_no',                
                'slug'      => 'slug',                
            ],
            'file_name' => 'products.js'
        ];
    }
    public function index2(){
        return  $this->paginate;
    }

    public function save($app_obj = false, Request $request,$get_last_insert_id = false){
        function array_urls_to_json($request_frm){
            $items_array = array();
            foreach($request_frm as $k=>$v){
                if($v != null){
                    array_push($items_array,$v);
                }
            }
            if( count($items_array) > 0 ){
                $items_arrays = [];
                foreach($items_array as $k=>$v){
                    $items_arrays[$k] = [
                        'url' => $v
                    ]; 
                }
                $result = json_encode($items_arrays);
            }else{
                $result = null;
            }
            return $result;
        } 
        $request->merge([
            'frm_product_images' => array_urls_to_json($request->get('frm_product_images')),
        ]);
        $request->merge([
            'frm_video_urls' => array_urls_to_json($request->get('frm_video_urls')),
        ]);
       
        return parent::save($app_obj,$request,$get_last_insert_id);
    }
    public function save__type_2($app_obj = false, Request $request,$get_last_insert_id = false)
    {
        $export_fields = $this->export_fields;
        if ($app_obj && $app_obj > 0) {
            $save_type = 'update';
            $obj_id = $app_obj;
            $app_obj = $this->app_obj::find($app_obj);
        } else {
            $save_type = 'insert';
            $app_obj = $this->app_obj;
        }
        $fields = $this->app_obj::getFields();
        $validate = [];
        foreach ($fields as $k => $v) {
           
            // if( $save_type == 'update' && array_key_exists('validate_unique_update', $v)){
            //     $validate[$k] = $v['validate_unique_update'].$obj_id;
            // }else{
                
            // }
            // $validate[$k] = $v['validate'];
            if(array_key_exists('validate_unique', $v)){
                $validate_full =  $v['validate'].$v['validate_unique'];
                if( $save_type == 'update'){
                    $validate[$k] = $validate_full.$obj_id;
                }else{
                    $validate[$k] = $validate_full;
                }
            }else{
                $validate[$k] = $v['validate'];
            }

        }
        $this->validate($request, $validate);
        
        foreach ($fields as $k => $v) {
            $db_field_name = $v['db_field_name'];
            $app_obj->$db_field_name = $request->get($k);
        }
        
        $app_obj->state = (int) $request->get('state', 1);

        if (!$app_obj->exists) {
            $lang_code = App::getLocale();
            $app_obj->lang_code = $lang_code;
        }

        function array_urls_to_json($request_frm){
            $product_image = array();
            foreach($request_frm as $k=>$v){
                if($v != null){
                    array_push($product_image,$v);
                }
            }
            if( count($product_image) > 0 ){
                $product_images = [];
                foreach($product_image as $k=>$v){
                    $product_images[$k] = [
                        'url' => $v
                    ]; 
                }
                $result = json_encode($product_images);
            }else{
                $result = null;
            }
            return $result;
        }        
        
        $app_obj->product_images   = array_urls_to_json($request->get('frm_product_images'));
        $app_obj->video_urls     = array_urls_to_json($request->get('frm_video_urls'));

        $app_obj->save();
        $app_obj->export_fields =  $export_fields;

        $this->exportDataToJson2($app_obj);
        if($get_last_insert_id){
            return $app_obj;
        }
        // return redirect()->route($this->object['edit_route'], $app_obj)->with('success', 'Cập nhật thông tin thành công');
        $redirect = redirect()->route($this->object['edit_route'], $app_obj);
        return $redirect->with('success', 'Cập nhật thông tin thành công');
    }
    public function index_join($index_data_list){
        $index_data_list = $index_data_list->select($this->app_obj->table.'.*', 'product_categorys.name as product_category_name_create_in_controller');
        $index_data_list = $index_data_list->leftJoin('product_categorys', 'category', '=', 'product_categorys.slug');
        return $index_data_list;
    }

    public function index_where($index_data_list){
        // $index_data_list = $this->create_query_by_material_id($index_data_list,new Decalcategory);     
        return $index_data_list;
    }
    public function edit0($app_obj = false)
    {
        if ($app_obj){
            $app_obj = $this->app_obj::find($app_obj);
            // if( $this->app_obj->get_p_type_in_model() != "pre-made-envelope" )
            {
                $product_category_id = $app_obj->category;
                $obj_id = $app_obj->id;
            }          
           
        }else{
            $product_category_id = null;
            $obj_id             = null;
        }
            
        // return dd($app_obj);
        $lang_code = App::getLocale();
        $object = $this->object;
        
        // $query_product_category = Productcategory::where('lang_code', $lang_code);
        // $list_product_categorys = Productcategory::makeFormSelect($query_product_category,0, -1,$product_category_id);

        $query_product_category = Productcategory::get_all()->addSelect('slug');
        $wc_array = ($query_product_category->get()->toArray());
        $list_product_categorys = [];
        foreach($wc_array as $k=>$v){
            $list_product_categorys[$k] = (object) $v;
            $list_product_categorys[$k]->id = $v["slug"];
        }
        // dd( gettype($list_product_categorys[0])  );
        // dd($product_category_id);
        $list_product_categorys = Productcategory::makeOptionsString(  $list_product_categorys,$product_category_id);
        
        return view('admin.' . $this->object['name'] . '.create', compact('object', 'app_obj','list_product_categorys'));
    }
}