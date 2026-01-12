<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// ============================================ START ==========================
use Illuminate\Support\Facades\App;
use App\Libs\DataGrid;

// ============================================ END ============================

class BaseController extends Controller
{
    public function __construct($app_obj)
    {
        $this->app_obj = $app_obj;
        $object = $app_obj::indexItemListConfig();
        $this->object = $object;
        $this->selectedMainMenu = $this->object['name'];
        $this->lang_code = App::getLocale();
        parent::__construct();
        $this->paginate = $app_obj->perPage ? $app_obj->perPage : 20;
        // $this->loadConstruct($app_obj);
        $this->clsDataGrid = $this->object['clsDataGrid'];

        $this->object['index_route'] = 'admin_' . $this->object['name'] . '';
        $this->object['save_data_index_route'] = 'admin_' . $this->object['name'] . '_save_data_index';
        $this->object['create_route'] = 'admin_' . $this->object['name'] . '_create';
        $this->object['edit_route'] = 'admin_' . $this->object['name'] . '_edit';
        $this->object['save_route'] = 'admin_' . $this->object['name'] . '_save';
        $this->object['delete_route'] = 'admin_' . $this->object['name'] . '_delete';
        $this->object['clone_route'] = 'admin_' . $this->object['name'] . '_clone';
        $this->object['delete_checkbox_route'] = 'admin_' . $this->object['name'] . '_delete_checkbox';
        $this->middleware('can:' . $this->object['name']);
            
        // $this->middleware('can:' . $this->object['name']);
        
        $this->index_view =  'admin.' . $this->object['name'] . '.index';
        $this->option_column_button = $this->app_obj::makeOptionColumnButton($this->object['name']);
        $this->edit_view = 'admin.' . $this->object['name'] . '.create';
    }
    
    public function index()
    {
        $lang_code = $this->lang_code;
        $this->selectedSubMenu('decal_category_s1');
        $paginate = $this->paginate;
        $edit_route = $this->object['edit_route'];
        $index_data_list = $this->app_obj->where($this->app_obj->table.'.lang_code', $lang_code);
        ##############################################################################################
        ############# =========> JOIN  
        if(method_exists($this,'index_where')){
            $index_data_list = $this->index_where($index_data_list);
        }
        if(method_exists($this,'index_join')){
            $index_data_list = $this->index_join($index_data_list);
        }
        ##############################################################################################
        
        if(method_exists($this,'index_order_by')){
            $index_data_list = $this->index_order_by($index_data_list);
        }else{
            $index_data_list = $index_data_list->orderBy('order_no', 'asc')
            ->orderBy('name', 'asc') ;                               
        }
        
        $index_data_list = $index_data_list->paginate($paginate);
        $option_column_button = $this->option_column_button;

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($edit_route);
        foreach ($this->clsDataGrid as $k => $v) {
            $clsDataGrid->addColumnLabel($v['field'], $v['title'], $v['style']);
        }
        ##############################################################################################
        ############# =========> COLUMN
        ##############################################################################################
        if(!isset($this->grid_no_order) || !$this->grid_no_order){
            $clsDataGrid->addColumnText("order_no", "Sắp xếp", "width='3%' align='center'");
        }

        if(!isset($this->grid_no_button) || !$this->grid_no_button){
            $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' align='center' nowrap ");
        }

        $dataGrid = $clsDataGrid->showDataGrid($index_data_list, $paginate, $index_data_list->total());
        $object = $this->object;
        
        $index_data = compact('object', 'index_data_list', 'dataGrid');
        if(method_exists($this,'index_view_2')){            
            return $this->index_view_2($index_data);
        }
        return view($this->index_view, $index_data);
    }
    public function saveDataIndex(Request $request)
    {
        if( property_exists($this,'export_fields')){
            $export_fields =  $this->export_fields;
        }else{
            dd('export_fields not found');
        }
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            $this->app_obj::where('id', $key)->update($value);
        }
        $this->app_obj->export_fields = $export_fields;
        $this->exportDataToJson2($this->app_obj);
        return redirect()->route($this->object['index_route'])->with('success', 'Cập nhật thông tin thành công');
        
    }
    function edit($app_obj = false){
        
        return $this->edit0($app_obj);
             
    } 
    public function edit0($app_obj = false)
    {
       
        if ($app_obj)
            $app_obj = $this->app_obj::find($app_obj);
        $object = $this->object;
        
        return view( $this->edit_view, compact('object', 'app_obj'));
    }
    public function save___($app_obj = false, Request $request,$get_last_insert_id = false)
    {
        // $request->request->add(['variable' => 'value']);
        
        
        // dd($request->request);
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
        // dd($app_obj);
        // return $request;
        $app_obj->save();
        if($get_last_insert_id){
            return $app_obj;
        }
        // return redirect()->route($this->object['edit_route'], $app_obj)->with('success', 'Cập nhật thông tin thành công');
        $redirect = redirect()->route($this->object['edit_route'], $app_obj);
        return $redirect->with('success', 'Cập nhật thông tin thành công');
    }
    public function save($app_obj = false, Request $request,$get_last_insert_id = false)
    {
        if ($app_obj && $app_obj > 0) {
            $save_type = 'update';
            $obj_id = $app_obj;
            $app_obj = $this->app_obj::find($app_obj);
        } else {
            $save_type = 'insert';
            $app_obj = $this->app_obj;
        }
        if( property_exists($this,'export_fields')){
            $export_fields =  $this->export_fields;
        }else{
            dd('export_fields not found');
        }
        $fields = $this->app_obj::getFields();
        $validate = [];
        foreach ($fields as $k => $v) {
           
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
            if ($v['db_field_name'] == 'video_url' && $request->frm_videourl != null) {
                $path           = $request->file('frm_videourl')->store('videos', 'public');
                $url            = Storage::url($path);
                $db_field_name  = $v['db_field_name'];
                $app_obj->$db_field_name = $url;
            } else {
                $db_field_name = $v['db_field_name'];
                $app_obj->$db_field_name = $request->get($k);
            }
            
        }
        
        $app_obj->state = (int) $request->get('state', 1);

        if (!$app_obj->exists) {
            $lang_code = App::getLocale();
            $app_obj->lang_code = $lang_code;
        }        

        $app_obj->save();
        $app_obj->export_fields = $export_fields;
        $this->exportDataToJson2($app_obj);
        if($get_last_insert_id){
            return $app_obj;
        }
        // return redirect()->route($this->object['edit_route'], $app_obj)->with('success', 'Cập nhật thông tin thành công');
        $redirect = redirect()->route($this->object['edit_route'], $app_obj);
        return $redirect->with('success', 'Cập nhật thông tin thành công');
    }
    public function clone ($app_obj)
    {
        if( property_exists($this,'export_fields')){
            $export_fields =  $this->export_fields;
        }else{
            dd('export_fields not found');
        }
        $app_obj = $this->app_obj::find($app_obj);
        // $item_id = data_get($app_obj, 'id', 0);

        if ($app_obj) {
            $new_item = $app_obj->replicate();
            $new_item->name = $app_obj->name . " copy";
            if ($new_item->save()) {
                $app_obj->export_fields = $export_fields;
                $this->exportDataToJson2($app_obj);
                return back()->with('success', 'Sao chép thành công');
            }
        }
        
        return back()->with('error', 'Sao chép không thành công');
    }
    public function delete(Request $request, $id)
    {
        if( property_exists($this,'export_fields')){
            $export_fields =  $this->export_fields;
        }else{
            dd('export_fields not found');
        }

        $this->app_obj->destroy($id);

        $this->app_obj->export_fields = $export_fields;
        $this->exportDataToJson2($this->app_obj);
        return redirect()->to( route('admin_' . $this->object['name'])  );
    }
    public function deleteCheckbox(Request $request)
    {
        if( property_exists($this,'export_fields')){
            $export_fields =  $this->export_fields;
        }else{
            dd('export_fields not found');
        }
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->app_obj->destroy($ids);
        if(method_exists($this,'deleteCheckbox_to_other_table')){
            $this->deleteCheckbox_to_other_table($ids);
        }
        $this->app_obj->export_fields = $export_fields;
        $this->exportDataToJson2($this->app_obj);
        return $this->responseJsonOk();
    }

    // ===================================================================
  

    function IsNullOrEmptyString($str){
        // $str = str_replace(' ','',$str);
        return ($str === null || trim($str) === '');
    }
    function get_list_by_slug($query){
        $wc_array = ($query->get()->toArray());
        $result = [];
        foreach($wc_array as $k=>$v){
            $result[$k] = (object) $v;
            $result[$k]->id = $v["slug"];
        }
        return $result;
    }
    // ====================================================

    public static function exportDataToJson2($app_obj)
    {
        // $products = $products->orderBy('order_no')->paginate($paginate);
        $products = $app_obj::where('state', 1)->orderBy('order_no','asc')->get();
        $data = [];
       
        foreach ($products as $product) {
            // $data[$product->id] = Product::json_structure($product);
            // $data[$product->id] = 
            // $this_product = 
            // [
            //     'id'            => $product->id,
            //     'name'          => $product->name,
            //     'excerpt'          => $product->excerpt,
            //     'lat'           => $product->food_latitude,
            //     'lon'           => $product->food_longitude,
            //     'product_images'   => $product->product_images,
            //     'video_urls'     => $product->video_urls,                
            //     'description'   => $product->description,                
            //     'order_no'      => $product->order_no,                
            //     'slug'      => $product->slug,                
            // ]; 
            $this_product = [];  
            foreach($app_obj->export_fields['list']  as $k=>$v){
                $this_product[$k] = $product->$v;
            }    

            array_push($data,$this_product); 
               
        }
        file_put_contents('js/'.$app_obj->export_fields['file_name'], json_encode($data));
        return false;
	}
    
}