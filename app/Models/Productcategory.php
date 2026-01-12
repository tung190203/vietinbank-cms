<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Productcategory extends BaseModel
{

    // use HasFactory;

    public $table = 'product_categorys';
    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'name',
                'title' => "Tên chuyên mục",
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
        
        $object = array(
            'name' => 'product_category',
            'title' => 'Chuyên mục',
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
            'frm_slug' => [
                'validate' => 'required|string',
                'db_field_name' => 'slug'
            ],
            
           

        ];
        return $html_form_fields;
    }




}