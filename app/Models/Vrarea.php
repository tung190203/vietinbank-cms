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
        return [
            'frm_name' => [
                'db_field_name' => 'name',
                'validate' => 'required|string',
                'validate_unique' => '|unique:vr_areas,name,',
            ],
            'frm_slug' => [
                'db_field_name' => 'slug',
                'validate' => 'required|string',
                'validate_unique' => '|unique:vr_areas,slug,',
            ],
        ];
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

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Nếu là thêm mới (bản ghi chưa tồn tại id)
            if (!$model->exists) {
                if (!\Str::startsWith($model->slug, 'slug-')) {
                    $model->slug = 'slug-' . \Str::slug($model->name);
                }
            }
            // Nếu là cập nhật
            else {
                if (empty($model->slug)) {
                    $model->slug = $model->getOriginal('slug');
                }
            }
        });
    }
}