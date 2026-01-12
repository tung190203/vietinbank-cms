<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Vrarea extends BaseModel
{

    // use HasFactory;

    public $table       = 'vr_areas';
    public $perPage     = 1000;

    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'name',
                'title' => "Tên khu vực",
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
            'name' => 'vr_area',
            'title' => 'Khu vực',
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
            'frm_parent_id' => [
                'validate' => 'required',
                'db_field_name' => 'parent_id'
            ],          

        ];
        return $html_form_fields;
    }

    public static function makeOptionColumnButton($object_name)
    {
        $buttons = parent::makeOptionColumnButton($object_name);
        $buttons['view'] = [
            'route' => 'admin_vrpopup_filter',
            'params' => function($id) {
                $item = \App\Models\VrArea::find($id);
                return [
                    'groupslug' => 'all',
                    'areaslug'  => $item ? $item->slug : '',
                ];
            }
        ];
        return $buttons;
    }
}