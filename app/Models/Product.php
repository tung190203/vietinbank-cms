<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Product extends BaseModel
{

    // use HasFactory;

    public $table = 'products';
    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'name',
                'title' => "Tên sản phẩm",
                'style' => "width='15%'"
            )
        );
        array_push(
            $clsDataGrid,
            array(
                'field' => 'slug',
                'title' => "Slug",
                'style' => "width='15%'"
            )
        );
        
        array_push(
            $clsDataGrid,
            array(
                'field' => 'excerpt',
                'title' => "Mô tả ngắn",
                'style' => "width='15%'"
            )
        );
        array_push(
            $clsDataGrid,
            array(
                'field' => 'product_category_name_create_in_controller',
                'title' => "Phân loại",
                'style' => "width='15%'"
            )
        );
        
       
        array_push(
            $clsDataGrid,
            array(
                'field' => 'video_urls',
                'title' => "Youtube Video ID",
                'style' => "width='15%'"
            )
        );
        
        $object = array(
            'name' => 'product',
            'title' => 'Danh sản phẩm',
            'clsDataGrid' => $clsDataGrid
        );
        return $object;
    }
    public static function getFields()
    {
        $html_form_fields = [
            'frm_name' => [
                'validate' => 'required|string',
                'db_field_name' => 'name'
            ],            
            'frm_excerpt' => [
                'validate' => 'string',
                'db_field_name' => 'excerpt'
            ],            
            'frm_product_category' => [
                'validate' => 'string',
                'db_field_name' => 'category'
            ],            
           
           

            // 'frm_product_images[]' 
            'frm_product_images' 
            => [
                'validate' => 'string|nullable',
                'db_field_name' => 'product_images'
            ],
            // 'frm_video_urls[]' 
            'frm_video_urls' 
            => [
                'validate' => 'string|nullable',
                'db_field_name' => 'video_urls'
            ],
            'frm_description' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'description'
            ],
            'frm_slug' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'slug'
            ],           

        ];
        return $html_form_fields;
    }

    // Product::exportDataToJson2();
    // saveDataIndex
    // save
    // clone
    // delete
    // deleteCheckbox

    public static function exportDataToJson2___()
    {
        // $products = $products->orderBy('order_no')->paginate($paginate);
        $products = Product::where('state', 1)->orderBy('order_no','asc')->get();
        $data = [];
       
        foreach ($products as $product) {
            // $data[$product->id] = Product::json_structure($product);
            $data[$product->id] = [
                'title'          => array(
                    'en-US' => $product->name_en,
                    'vi-VN' => $product->name
                ),
                'product_id'    => $product->id,
                'description'   => array(
                    'en-US' => $product->description_en,
                    'vi-VN' => $product->description
                ),
                'imageUrl'      => $product->image,
                'cat_id'        => $product->cat_id,
                'order_no'        => $product->order_no
            ];       
        }
        file_put_contents('js/products.js', json_encode($data));
        return false;
	}

    public function save_____(Product $product, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            
        ]);
        $lang_code = App::getLocale();
        $name = $request->get('name');
        $product->name = $name;
        $product->name_en = $request->get('name_en');
        
        $product->cat_id = $request->get('cat_id',8);
        if($product->id == 50){
            // PDF
            $product->cat_id = $request->get('cat_id',38);
        }
        if($product->id == 48){
            // VIDEO
            $product->cat_id = $request->get('cat_id',37);
        }

        $product->description = $request->get('description');
        $product->description_en = $request->get('description_en');
        $product_image = array();
        foreach($request->get('image') as $k=>$v){
            if($v != null){
                array_push($product_image,$v);
            }
        }
        if( count($product_image) > 0 ){
            $product->image = json_encode($product_image);
        }else{
            $product->image = null;
        }
        
        $product->image_360 = $request->get('image_360');
        $product->state = (int)$request->get('state', 1);
        if (!$product->exists) {
            $product->lang_code = $lang_code;
        }

        // $product->save();
        // Product::exportDataToJson2();
        // $r = json_encode($request);
        // return redirect()->route('admin_product_edit', $product)->with('success', 'Cập nhật thông tin thành công');
        if ($request->get('mode') && $request->get('mode') == 'preview') {
            $p2 = htmlspecialchars(json_encode(Product::json_structure($product)));
            $data = array(
                'mode'      => $request->mode,
                'product'   => $p2
            );
            $data = json_encode($data);
            $data = base64_encode($data);
                        
            return redirect()->to( env('VRTOUR_URL') . '/?mode=preview&popup_id=' . $product->id . '&data=' . $data . '&skip-loading');
            
        } else {
            $product->save();
            Product::exportDataToJson2();
            // $r = json_encode($request);
            return redirect()->route('admin_product_edit', $product)->with('success', 'Cập nhật thông tin thành công');
        }
    }
}