<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
        $this->selectedMainMenu = 'recruitment';

        parent::__construct();

        $this->middleware('can:application');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('application');
        $category = new Category();
        $category->getParentArray();

        $filter['name'] = $request->get('name', '');
        $filter['profession_id'] = $request->get('profession_id', 0);
        $filter['state'] = $request->get('state', -1);
        $query = $this->application->with(['profession'])
            ->orderBy('id', 'desc');
        if ($filter['name'] !== '') {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if ($filter['profession_id'] > 0) {
            $query->where('profession_id', $filter['profession_id']);
        }
        if ($filter['state'] > -1) {
            $query->where('state', $filter['state']);
        }

        $applications = $query->paginate(20);
        $options['filter']['profession'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_PROFESSION, $filter['profession_id']);
        $options['state'] = Util::makeHTMLOptions(Application::STATE_ARRAY, $filter['state']);
        $options['profession'] = Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_PROFESSION);

        $paginate = 20;
        $route_name = 'admin_application_edit';
        $option_column_button = Application::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='10%' nowrap");
        $clsDataGrid->addColumnLabel("phone", "Phone", "width='10%' nowrap");
        $clsDataGrid->addColumnLabel("state_label", "Trạng thái", "width='5%'", '', 'html');
        $clsDataGrid->addColumnSelect("profession_id", "Ngành", "width='5%'", $options['profession']);
        $clsDataGrid->addColumnDate("created_at", "Ngày ứng tuyển", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($applications, $paginate, $applications->total());

        return view('admin.application.index', compact('applications', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Application::where('id', $key)->update($value);
        }
        return redirect()->route('admin_application')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Application $application)
    {
        $this->selectedSubMenu('application');
        $options['profession'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_PROFESSION, $application->profession_id);
        $options['status'] = Util::makeHTMLOptions(Application::STATE_ARRAY, $application->state);
        return view('admin.application.create', compact('application', 'options'));
    }

    public function save(Application $application, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string',
        ]);

        $application->name = $request->get('name');
        $application->phone = $request->get('phone');
        $application->email = $request->get('email');
        $application->content = $request->get('content');
        $application->profession_id = $request->get('profession_id', 0);
        $application->state = (int)$request->get('state', 0);

        $application->save();

        return redirect()->route('admin_application_edit', $application)->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {

        $this->application->destroy($id);
        return redirect()->to(route('admin_application'))->with('success', 'Xóa thành công');
    }
}

