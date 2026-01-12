<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vrpopup;
use App\Models\Vrarea;
use App\Models\Vrpopupgroup;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class VrpopupController extends BaseController
{
    public function __construct(Vrpopup $app_obj)
    {
        parent::__construct($app_obj);

        $this->export_fields = [
            "list" => [
                'id'            => 'id',
                'name'          => 'name',
                'slug'          => 'slug', 
                'group'         => 'popup_group', 
                'area'          => 'area', 
                'popup_images'  => 'popup_images',  
                'popup_3ds'     => 'popup_3ds',  
                'description'   => 'description',             
                'order_no'      => 'order_no',  
                'is_show'       => 'is_show',
                'video_url'     => 'video_url'
            ],
            'file_name' => 'vr_popup.js'
        ];
    }

    // ===================== EDIT =====================
    public function edit0($app_obj = false)
    {
        if ($app_obj) {
            $app_obj = $this->app_obj::find($app_obj);
            $selectedAreas = $app_obj->area ? explode(',', $app_obj->area) : [];
            $vrgroup_id = $app_obj->popup_group;
            $obj_id = $app_obj->id;
        } else {
            $selectedAreas = [];
            $vrgroup_id = null;
            $obj_id = null;
        }

        $object = $this->object;

        // Multi-select expects associative array [id => name]
        $list_vrareas = Vrarea::get_all()->pluck('name','id')->toArray();
        $list_popup_groups = Vrpopupgroup::makeOptionsString(
            $this->get_list_by_slug(Vrpopupgroup::get_all()->addSelect('slug')),
            $vrgroup_id
        );

        return view(
            'admin.' . $this->object['name'] . '.create',
            compact('object', 'app_obj', 'list_vrareas', 'list_popup_groups', 'selectedAreas')
        );
    }

    // ===================== SAVE =====================
    public function save($app_obj = false, Request $request, $get_last_insert_id = false)
    {
        // Convert multi-select array to comma-separated string for DB
        $areas = $request->get('frm_vr_area', []);
        if (is_array($areas)) {
            $request->merge([
                'frm_vr_area' => implode(',', $areas)
            ]);
        }

        // Convert popup images array to JSON
        function array_urls_to_json($urls){
            $popup_images = [];
            foreach($urls as $v){
                if($v != null){
                    $popup_images[] = ['url' => $v];
                }
            }
            return count($popup_images) > 0 ? json_encode($popup_images) : null;
        }

        $request->merge([
            'frm_popup_images' => array_urls_to_json($request->get('frm_popup_images'))
        ]);

        return parent::save($app_obj, $request, $get_last_insert_id);
    }

    // ===================== INDEX =====================
    public function index_join($index_data_list){
        $index_data_list = $index_data_list->select($this->app_obj->table.'.*', 'vr_areas.name as area_name_create_in_controller');
        $index_data_list = $index_data_list->leftJoin('vr_areas', 'vr_areas.slug', '=', $this->app_obj->table.'.area');

        $index_data_list = $index_data_list->select($this->app_obj->table.'.*', 'vr_popup_groups.name as popupgroup_name_create_in_controller');
        $index_data_list = $index_data_list->leftJoin('vr_popup_groups', 'vr_popup_groups.slug', '=', $this->app_obj->table.'.popup_group');
        return $index_data_list;
    }

    public function index_where($index_data_list){
        if(property_exists($this, 'filter')){
            if($this->filter['area'] != 'all'){
                $index_data_list->where($this->app_obj->table.'.area', $this->filter['area']);
            }            
            if($this->filter['group'] != 'all'){
                $index_data_list->where($this->app_obj->table.'.popup_group', $this->filter['group']);
            }
            if($this->filter['search'] != null){
                $index_data_list->where($this->app_obj->table.'.slug', 'LIKE', '%'.$this->filter['search'].'%')
                                ->orWhere($this->app_obj->table.'.name', 'LIKE', '%'.$this->filter['search'].'%');
            }
        }
        return $index_data_list;
    }

    public function index_order_by($index_data_list){
        $index_data_list = $index_data_list->orderBy('vr_areas.order_no', 'ASC');
        // $index_data_list = $index_data_list->orderBy('vr_areas.id', 'ASC');
        // $index_data_list = $index_data_list->orderBy('vr_popup_groups.id', 'ASC');
        return $index_data_list;
    }

    public function index_view_2($index_data){
        $vrarea_id = null;
        $vrgroup_id = null;
        if(property_exists($this, 'filter')){
            if($this->filter['area'] != 'all'){
                $vrarea_id = $this->filter['area'];
            }            
            if($this->filter['group'] != 'all'){
                $vrgroup_id = $this->filter['group'];
            }
        }
        $index_data['list_vrareas'] = Vrarea::makeOptionsString(
            $this->get_list_by_slug(Vrarea::get_all()->addSelect('slug')),
            $vrarea_id
        );
        $index_data['list_groups'] = Vrpopupgroup::makeOptionsString(
            $this->get_list_by_slug(Vrpopupgroup::get_all()->addSelect('slug')),
            $vrgroup_id
        );
        return view($this->index_view, $index_data);
    }

    public function index_filter(Request $request, $groupslug, $areaslug)
    {
        $this->filter = [
            'area'      => $areaslug,
            'group'     => $groupslug,
            'search'    => $request->search
        ];
        return $this->index();
    }

    // ===================== EXPORT =====================
    public static function exportDataToJson2___($app_obj)
    {
        $export_items = $app_obj::where('state', 1)->orderBy('order_no','asc')->get();
        $data = [];
       
        foreach ($export_items as $export_item) {
            $this_export_item = [];  
            foreach($app_obj->export_fields['list']  as $k=>$v){
                $this_export_item[$k] = $export_item->$v;
            }   
            array_push($data,$this_export_item);                
        }
        file_put_contents('js/'.$app_obj->export_fields['file_name'], json_encode($data));
        return false;
    }
}
