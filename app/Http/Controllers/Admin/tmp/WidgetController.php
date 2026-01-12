<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Widget;
use Illuminate\Support\Facades\App;

class WidgetController extends Controller
{
    private $widget;
    public $positions = [
        'PARTNER' => 'Logo đối tác',
        'TESTIMONIAL' => 'Khách hàng nói về chúng tôi'
    ];

    public function __construct(Widget $widget)
    {
        $this->widget = $widget;
        $this->selectedMainMenu = 'widget';

        parent::__construct();

        $this->middleware('can:widget');
    }

    public function index(Request $request)
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('widget');

        $filter['position'] = $request->get('position', 'all');
        $query = $this->widget->where('lang_code', $lang_code)->orderBy('position');
        if ($filter['position'] !== 'all') {
            $query->where('position', $filter['position']);
        }
        $widgets = $query->paginate(20);
        $option_positions = Util::makeHTMLOptions($this->positions, $filter['position'], 0, 0, 0);

        $paginate = 20;
        $route_name = 'admin_widget_edit';
        $option_column_button = Widget::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Tiêu đề", "width='20%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%' nowrap");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("order_no", "STT", "width='5%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' align='center' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($widgets, $paginate, $widgets->total());

        return view('admin.widget.index', compact('widgets', 'option_positions', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Widget::where('id', $key)->update($value);
        }
        return redirect()->route('admin_widget')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Widget $widget)
    {
        $options['positions'] = Util::makeHTMLOptions($this->positions, $widget->position);
        return view('admin.widget.create', compact('widget', 'options'));
    }

    public function save(Widget $widget, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        $lang_code = App::getLocale();
        $widget->name = $request->get('name');
        $widget->name_2 = $request->get('name_2');
        $widget->sapo = $request->get('sapo');
        $widget->image = $request->get('image');
        $widget->image_2 = $request->get('image_2');
        $widget->order_no = $request->get('order_no');
        $widget->link = $request->get('link');
        $position = $request->get('position');
        $widget->position = $position;
        $widget->state = (int)$request->get('state', 0);
        if (!$widget->exists) {
            $widget->lang_code = $lang_code;
        }

        $widget->save();

        $url_back = route('admin_widget') . '?position=' . $position;
        return redirect()->to(url($url_back))->with('success', 'Cập nhật thông tin thành công');
    }

    public function clone(Widget $widget)
    {
        $widget_id = data_get($widget, 'id', 0);
        $widget = Widget::find($widget_id);
        if ($widget) {
            $new_widget = $widget->replicate();
            $new_widget->name = $widget->name . " copy";
            if ($new_widget->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {
        $this->widget->destroy($id);
        return redirect()->route('admin_widget')->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->widget->destroy($ids);
        return $this->responseJsonOk();
    }
}

