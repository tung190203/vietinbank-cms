<?php

namespace App\Models;

use \App\Models\Vrarea;
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
        array_push(
        $clsDataGrid,
        array(
            'field' => 'area_name',
            'title' => "Thuộc khu vực",
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
            'frm_vr_area_id' => [
                'db_field_name' => 'vr_area_id',
                'validate' => 'required|not_in:0',
                'type' => 'select', // Giả sử Base của bạn hỗ trợ type select
                'data_source' => Vrarea::all(), // Truyền danh sách area vào
            ],         

        ];
        return $html_form_fields;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // TRƯỜNG HỢP THÊM MỚI
            if (!$model->exists) {
                if (empty($model->slug)) {
                    $model->slug = \Str::slug($model->name);
                }
            }
            // TRƯỜNG HỢP CẬP NHẬT (Khóa chết slug)
            else {
                $model->slug = $model->getOriginal('slug');
            }
        });
    }
    public function area()
    {
        // popup_group belongsTo Vrarea thông qua khóa ngoại vr_area_id
        return $this->belongsTo(Vrarea::class, 'vr_area_id', 'id');
    }
}