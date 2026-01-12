<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Vrpopupgroup extends BaseModel
{

    // use HasFactory;

    public $table = 'vr_popup_groups';
    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'name',
                'title' => "Tên nhóm nội dung",
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
            'name' => 'vr_popup_group',
            'title' => 'Nhóm nội dung',
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