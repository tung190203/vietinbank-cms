<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Category;
use App\Models\District;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ProvinceController extends Controller
{
    private $province;

    public function __construct(Province $province)
    {
        $this->province = $province;
        $this->selectedMainMenu = 'province';

        parent::__construct();

        $this->middleware('can:province');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('province');

        $filter['name'] = $request->get('name', '');
        $filter['state'] = $request->get('state', -1);
        $query = $this->province->orderBy('order_no')->orderBy('name');
        if ($filter['name'] !== '') {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if ($filter['state'] > -1) {
            $query->where('state', $filter['state']);
        }

        $paginate = 20;
        $provinces = $query->paginate($paginate);
        $options['state'] = Util::makeHTMLOptions(Province::STATE_ARRAY, $filter['state']);


        $route_name = 'admin_province_edit';
        $option_column_button = Province::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnText("price_ship", "Phí vận chuyển", "width='5%' align='center'");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%' align='center'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("order_no", "STT", "width='5%' align='center'");
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' align='center' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($provinces, $paginate, $provinces->total());

        return view('admin.province.index', compact('provinces', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Province::where('id', $key)->update($value);
        }
        return redirect()->route('admin_province')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Province $province)
    {
        return view('admin.province.create', compact('province'));
    }

    public function save(Province $province, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'price_ship' => 'required|integer',
        ]);

        $province->name = $request->get('name');
        $province->name_en = $request->get('name_en');
        $province->price_ship = $request->get('price_ship');
        $province->order_no = $request->get('order_no');
        $province->state = $request->get('state', 1);
        $province->save();
        return redirect()->route('admin_province');
    }

    public function delete(Request $request, $id)
    {

        $this->province->destroy($id);
        return redirect()->to(route('admin_province'))->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->province->destroy($ids);
        return $this->responseJsonOk();
    }
}

