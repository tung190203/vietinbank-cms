<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\App;
class BaseModel extends Model
{
    public static function get_lang_code(){
        return App::getLocale();
    }
    public static function makeOptionColumnButton($object_name)
    {
        return [
            'edit' => [
                'route' => 'admin_' . $object_name . '_edit',
            ],
            'clone' => [
                'route' => 'admin_' . $object_name . '_clone',
            ],
            'delete' => [
                'route' => 'admin_' . $object_name . '_delete',
            ]
        ];
    }
    public function showCategories($categories, $parent_id = 0, $char = '')
    {
        foreach ($categories as $key => $category) {
            if ($category->parent_id == $parent_id) {
                $categories1 = $categories->firstWhere('parent_id', $parent_id);
                if ($categories1) {
                    $categories1->name = $char . $categories1->name;
                    $categories[] = $categories1;
                }

                unset($categories[$key]);
                $this->showCategories($categories, data_get($category, 'id'), '&brvbar;--- ' . $char);
            }
        }
        return $categories->values();
    }
    public static function makeFormSelect($query,$parent_id = 0, $type = -1, $selected_id = "")
    {
        // $query = Papersize::where('lang_code', $lang_code);
        if ($type > -1) {
            $query = $query->where('type', $type);
        }
        $categories = $query->orderBy('order_no')->orderBy('name')->get(['id', 'name']);
        
        // $html = "";
        // $list_categories = (new self())->showCategories($categories, $parent_id);
        // foreach ($list_categories as $k => $v) {
        //     if (is_array($selected_id)) {
        //         $selected = in_array($v->id, $selected_id) ? "selected" : "";
        //     } else {
        //         $selected = ($v->id == $selected_id) ? "selected" : "";
        //     }
        //     $html .= "<option value=\"$v->id\" $selected>" . $v->name . "</option>";
        // }
        // return $html;
        $list_categories = (new self())->showCategories($categories, $parent_id);
        return (new self())->makeOptionsString($list_categories,$selected_id);
    }
    
    public static function makeOptionsString($list_categories,$selected_id = ""){
        $html = "";
        foreach ($list_categories as $k => $v) {
            if (is_array($selected_id)) {
                $selected = in_array($v->id, $selected_id) ? "selected" : "";
            } else {
                $selected = ($v->id == $selected_id) ? "selected" : "";
            }
            if( isset($v->item_code) ){
                $html .= "<option item_code = \"$v->item_code\"  value=\"$v->id\" $selected>" . $v->name . "</option>";
            }else{
                $html .= "<option value=\"$v->id\" $selected>" . $v->name . "</option>";
            }
            
        }
        return $html;
    }

    public static function makeOptionsArray($list, $selected = [])
    {
        return $list->pluck('name', 'id')->toArray();
    }


    public static function makeOptionsStringLoop($data, $parentId = 0, $prefix = '', $selectedId = null)
    {
        $html = '';
        foreach ($data as $item) {
            if ($item->parent_id == $parentId) {
                $selected = $selectedId == $item->id ? ' selected' : '';
                $html .= '<option value="' . $item->id . '"' . $selected . '>' . $prefix . $item->name . '</option>';
                $html .= BaseModel::makeOptionsStringLoop($data, $item->id, $prefix . '|-- ', $selectedId);
            }
        }
        return $html;
    }

    public static function get_all($override = false, $other_order = null, $sort = null)
    {
        $lang_code = Self::get_lang_code();
        $table = with(new static)->getTable();
        
        // if( !$override || !is_array($override) || !array_key_exists("select", $override)){
        //     $query = Self::select($table.'.id',$table.'.name');
        // } else{
        //     $query = $override['select'];
        // }  

        //SELECT no need OVERRIDE
        $query = Self::select($table.'.id',$table.'.name');     

        if( !$override || !is_array($override) || !in_array("where", $override)){
            $query = $query->where($table.'.lang_code', $lang_code); 
            $query = $query->where($table.'.state', 1);
        }
        if( (!$override || !is_array($override) || !in_array("orderBy", $override)) && $other_order == null){
            $query = $query->orderBy($table.'.order_no')->orderBy($table.'.name');
        }
        if ($other_order != null && $sort != null) {
            $query = $query->orderBy($table.$other_order, $sort);
        }
        return $query;
    }

    static function get_table_name(){
        return $table = with(new static)->getTable();
    }
}
