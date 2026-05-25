<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Vrpopup extends BaseModel
{

    // use HasFactory;

    public $table = 'vr_popups';
    public static function indexItemListConfig()
    {
        $clsDataGrid = array();
        array_push(
            $clsDataGrid,
            array(
                'field' => 'name',
                'title' => "Tên nội dung",
                'style' => "width='15%'"
            )
        );
        array_push($clsDataGrid, array(
            'field' => 'area_name', // Tên trường ảo sau khi Join
            'title' => "Khu vực",
            'style' => "width='15%' align='center'"
        ));
        array_push(
            $clsDataGrid,
            array(
                'field' => 'slug',
                'title' => "Mã code",
                'style' => "width='15%'"
            )
        );
        array_push(
            $clsDataGrid,
            array(
                'field' => 'description',
                'title' => "Thông tin",
                'style' => "width='25%'"
            )
        );
        array_push(
            $clsDataGrid,
            array(
                'field' => 'popupgroup_name_create_in_controller',
                'title' => "Nhóm nội dung",
                'style' => "width='15%'"
            )
        );
        $object = array(
            'name' => 'vr_popup',
            'title' => 'Nội dung',
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
                'validate' => 'required|string', // Gộp chung vào đây
                'db_field_name' => 'slug',
                'validate_unique' => '|unique:vr_areas,slug,',
            ],
            'frm_description' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'description'
            ],
            'frm_popup_images' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'popup_images'
            ],
            'frm_3ds' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'popup_3ds'
            ],
            'frm_vr_area' => [
                'validate' => 'required|string',
                'db_field_name' => 'area'
            ],
            'frm_popup_group' => [
                'validate' => 'required|string',
                'db_field_name' => 'popup_group'
            ],
            'frm_is_show' => [
                'validate' => 'integer',
                'db_field_name' => 'is_show'
            ],
            'frm_videourl' => [
                'validate' => 'string|nullable',
                'db_field_name' => 'video_url'
            ]
        ];
        return $html_form_fields;
    }
}