<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Models\Productcategory;
class ProductcategoryController extends BaseController
{
    public function __construct(Productcategory $app_obj)
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
            'file_name' => 'product_category.js'
        ];
    }
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
                if($k=='logo_image'){
                    // $logo_image_url  = url($product->$v);  
                    // $postParameter = array();
                    
                    // $curlHandle = curl_init(   url( '/js/image_to_base64.php?image_path='.$logo_image_url)   );
                    // curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postParameter);
                    // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);                    
                    // $curlResponse = curl_exec($curlHandle);
                    // curl_close($curlHandle);
                    // $this_product[$k] = ( $curlResponse );
                    $this_product[$k] = $product->$v;
                }else{
                    $this_product[$k] = $product->$v;
                }
                
            }    

            array_push($data,$this_product); 
               
        }
        file_put_contents('js/'.$app_obj->export_fields['file_name'], json_encode($data));
        return false;
	}
    
}