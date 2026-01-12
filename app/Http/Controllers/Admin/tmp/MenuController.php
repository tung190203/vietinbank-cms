<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class MenuController extends Controller
{
    private $menu;

    public $types = [
        //'top' => 'Menu đầu trang',
        'main' => 'Menu chính',
        'bottom' => 'Menu chân trang',
        'link_footer' => 'Menu link footer'
    ];

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
        $this->selectedMainMenu = 'menu';

        parent::__construct();

        $this->middleware('can:menu');
    }

    public function index(Request $request)
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('menu');
        $menu_type = $request->get('type', '');

        if ($menu_type !== '') {
            session(['mtype' => $menu_type]);
        }
        $mtype = session('mtype', 'main');
        $parent_id = $request->get('parent_id', 0);
        $menu_raw = $this->menu->where('type', $mtype)
            ->where('lang_code', $lang_code)
            ->orderBy('order_no')->get();
        $menus = $this->menu->showMenus($menu_raw);
        $option_positions = Util::makeHTMLOptions($this->types, $mtype, 0, 0, 0);
        $arr_categories = Arr::prepend(Category::makeArrayListCategory(), '', 0);
        $arr_pages = Arr::prepend(Page::makeArrayListPage(), '', 0);

        $route_name = 'admin_menu_edit';

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Tiêu đề", "width='15%' nowrap");
        //$clsDataGrid->addColumnSelect("page_id", "Trang", "width='5%' align='center'", $arr_pages);
        $clsDataGrid->addColumnSelect("cat_id", "Danh mục", "width='5%' align='center'", $arr_categories);
        $clsDataGrid->addColumnLabel("custom_link", "Custom URL", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%' align='center'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("order_no", "STT", "width='5%' align='center'");

        $dataGrid = $clsDataGrid->showDataGrid($menus);

        return view('admin.menu.index', compact('menus', 'parent_id', 'option_positions', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Menu::where('id', $key)->update($value);
        }
        return redirect()->route('admin_menu')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Request $request, Menu $menu)
    {
        $parent_id = $request->get('parent_id', 0);
        $mtype = session('mtype', 'main');
        $option_categories = Category::makeListCategory(0, -1, $menu->cat_id, 0, 5, "");
        $option_menu = Menu::makeListMenu(0, $mtype, $menu->parent_id, 0, 5, "");
        $option_pages = Page::makeListPage($menu->page_id);
        return view('admin.menu.create',
            compact('menu', 'option_menu', 'option_categories', 'option_pages', 'parent_id'));
    }

    public function save(Menu $menu, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string'
        ]);
        $parent_id = $request->get('parent_id', 0);
        if ($menu->exists && $menu->id == $parent_id) {
            return back()->withInput()->withErrors(['parent_id' => 'Danh mục cha không thể là chính nó']);
        }
        $lang_code = App::getLocale();
        $mtype = session('mtype', 'main');
        $menu->name = $request->get('name');
        $menu->page_id = $request->get('page_id', 0);
        $menu->cat_id = $request->get('cat_id');
        $menu->custom_link = $request->get('custom_link');
        $menu->parent_id = $parent_id;
        $menu->type = $mtype;
        $menu->order_no = $request->get('order_no', 999999);
        $menu->state = (int)$request->get('state', 0);
        $menu->open_in_new_tab = (int)$request->get('open_in_new_tab', 0);
        $menu->is_mega = (int)$request->get('is_mega', 0);
        $menu->lang_code = $lang_code;
        $menu->save();
        return redirect()->route('admin_menu')->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {
        $this->menu->destroy($id);
        return redirect()->route('admin_menu')->with('success', 'Xóa menu thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->menu->destroy($ids);
        return $this->responseJsonOk();
    }
}

