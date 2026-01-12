<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    private $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
        $this->selectedMainMenu = 'service';

        parent::__construct();

        $this->middleware('can:service');
    }

    public function index(Request $request)
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('service');
        $category = new Category();
        $category->getParentArray();

        $filter['name'] = $request->get('name', '');
        $filter['cat_id'] = $request->get('cat_id', 0);
        $filter['state'] = $request->get('state', -1);
        $query = $this->service->with(['category', 'user'])
            ->where('lang_code', $lang_code)
            ->orderBy('id', 'desc');
        if ($filter['name'] !== '') {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if ($filter['cat_id'] > 0) {
            $all_cat = $category->getAllCatStr($filter['cat_id']);
            $all_cat[] = (int)$filter['cat_id'];
            $query->whereIn('cat_id', $all_cat);
        }
        if ($filter['state'] > -1) {
            if ($filter['state'] == 2) {
                $query->onlyTrashed();
            } else {
                $query->where('state', $filter['state']);
            }
        }

        $services = $query->paginate(20)->appends($filter);
        $options['categories'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_SERVICE, $filter['cat_id']);
        $options['state'] = Util::makeHTMLOptions(Service::STATE_ARRAY, $filter['state']);
        $arr_categories = Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_SERVICE);

        $paginate = 20;
        $route_name = 'admin_service_edit';
        $option_column_button = Service::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
//        $clsDataGrid->addColumnLabel("slug", "Slug", "width='20%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%' nowrap");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%'", ["Không", "Có"]);
        $clsDataGrid->addColumnSelect("cat_id", "Danh mục", "width='5%'", $arr_categories);
        $clsDataGrid->addColumnText("order_no", "STT", "width='5%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($services, $paginate, $services->total());

        return view('admin.service.index', compact('services', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Service::where('id', $key)->update($value);
        }
        return redirect()->route('admin_service')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Service $service)
    {
        $list_categories = Category::makeListCategory(0, Category::CATEGORY_TYPE_SERVICE, $service->cat_id);
        return view('admin.service.create', compact('service', 'list_categories'));
    }

    public function save(Service $service, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            //'slug' => 'required|alpha_dash|unique:products,slug,' . $service->id,
            //'sapo' => 'required|string',
            'content' => 'required|string',
        ]);
        $lang_code = App::getLocale();
        $name = $request->get('name');
        $service->name = $name;
        $service->slug = Str::slug($name);
        $service->sapo = $request->get('sapo');
        $service->content = $request->get('content');
        $service->image = $request->get('image');
        $service->video_id = $request->get('video_id');
        $service->image_popup = $request->get('image_popup');
        $service->url = $request->get('url');
        $service->cat_id = $request->get('cat_id', 0);
        $service->state = (int)$request->get('state', 0);
        $service->meta_title = $request->get('meta_title');
        $service->meta_keys = $request->get('meta_keys');
        $service->meta_des = $request->get('meta_des');
        if (!$service->exists) {
            $service->lang_code = $lang_code;
        }

        $service->save();

        return redirect()->route('admin_service_edit', $service)->with('success', 'Cập nhật thành công');
    }

    public function clone(Service $service)
    {
        $new_id = data_get($service, 'id', 0);
        $service = Service::find($new_id);
        if ($service) {
            $new_service = $service->replicate();
            $new_service->name = $service->name . " copy";
            if ($new_service->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {

        $this->service->destroy($id);
        return redirect()->to(route('admin_service'))->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->service->destroy($ids);
        return $this->responseJsonOk();
    }
}

