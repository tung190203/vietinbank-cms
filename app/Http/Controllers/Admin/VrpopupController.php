<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Vrpopup;
use App\Models\Vrarea;
use App\Models\Vrpopupgroup;

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
            $vrgroup_id = $app_obj->popup_group;
            $current_area_slug = $app_obj->area; // Lấy slug hiện tại của area
        } else {
            $vrgroup_id = null;
            $current_area_slug = null;
        }

        $object = $this->object;

        // 1. Tạo danh sách Khu vực (chọn 1) - Dùng slug làm value
        $list_vrareas = Vrarea::where('state', 1)->get();
        $area_options = "";
        foreach ($list_vrareas as $area) {
            $selected = ($current_area_slug == $area->slug) ? 'selected="selected"' : '';
            $area_options .= '<option value="' . $area->slug . '" ' . $selected . '>' . $area->name . '</option>';
        }

        // 2. Danh sách Nhóm nội dung (giữ nguyên logic cũ của bạn)
        $list_popup_groups = Vrpopupgroup::makeOptionsString(
            $this->get_list_by_slug(Vrpopupgroup::get_all()->addSelect('slug')),
            $vrgroup_id
        );

        return view(
            'admin.' . $this->object['name'] . '.create',
            compact('object', 'app_obj', 'area_options', 'list_popup_groups')
        );
    }

    // ===================== SAVE =====================
    public function save($app_obj = false, Request $request, $get_last_insert_id = false)
    {
        $area = $request->get('frm_vr_area');
        $urls = $request->get('frm_popup_images', []);
        $popup_images = [];

        if (is_array($urls)) {
            foreach ($urls as $v) {
                if (!empty($v)) {
                    $popup_images[] = ['url' => $v];
                }
            }
        }
        // Chỉ xử lý tạo slug nếu là THÊM MỚI (app_obj không có hoặc <= 0)
        if (!$app_obj || $app_obj <= 0) {
            if (!$request->filled('frm_slug')) {
                $request->merge(['frm_slug' => \Str::slug($request->get('frm_name'))]);
            }
        }
        // Nếu là CẬP NHẬT
        else {
            $current = $this->app_obj::find($app_obj);
            if ($current) {
                $request->merge(['frm_slug' => $current->slug]);
            }
        }
        $request->merge([
            'frm_vr_area'      => $area,
            'frm_popup_images' => count($popup_images) > 0 ? json_encode($popup_images) : null
        ]);
        return parent::save($app_obj, $request, $get_last_insert_id);
    }

    // ===================== INDEX =====================
    public function index_join($index_data_list)
    {
        $table = $this->app_obj->table;
        $index_data_list = $index_data_list
            ->leftJoin('vr_areas', $table . '.area', '=', 'vr_areas.slug')
            ->leftJoin('vr_popup_groups', $table . '.popup_group', '=', 'vr_popup_groups.slug')
            ->select(
                $table . '.*',
                'vr_areas.name as area_name',
                'vr_popup_groups.name as popupgroup_name_create_in_controller'
            );
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
        return $index_data_list;
    }

    public function index_view_2($index_data)
    {
        $vrarea_id = null;
        $vrgroup_id = null;

        if (property_exists($this, 'filter')) {
            if ($this->filter['area'] != 'all') {
                $vrarea_id = $this->filter['area'];
            }
            if ($this->filter['group'] != 'all') {
                $vrgroup_id = $this->filter['group'];
            }
        }

        // 1. Tạo chuỗi option cho Khu vực
        $index_data['list_vrareas'] = Vrarea::makeOptionsString(
            $this->get_list_by_slug(Vrarea::get_all()->addSelect('slug')),
            $vrarea_id
        );

        // 2. LOGIC ĐƯỢC THAY ĐỔI: Lọc nhóm nội dung theo khu vực được chọn
        $group_query = Vrpopupgroup::get_all()->addSelect('slug');
        if (!empty($vrarea_id) && $vrarea_id != 'all') {
            $current_area = Vrarea::where('slug', $vrarea_id)->first();
            if ($current_area) {
                $group_query = $group_query->where('vr_area_id', $current_area->id);
            }
        }
        // 3. Tạo chuỗi option cho Nhóm (lúc này danh sách đã được lọc tự động)
        $index_data['list_groups'] = Vrpopupgroup::makeOptionsString(
            $this->get_list_by_slug($group_query),
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
    public static function exportDataToJson2($app_obj)
    {
        $export_items = $app_obj::where([
            'state'=> 1,
            'is_show' => 1
        ])->orderBy('order_no','asc')->get();
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
