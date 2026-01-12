<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use App\Models\Filter;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    private $category;
    public $category_types = Category::OPTIONS_CATEGORY;

    public function __construct(Category $category)
    {
        $this->category = $category;
        $this->selectedMainMenu = 'category';

        parent::__construct();

        $this->middleware('can:category');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('category');
        $lang_code = App::getLocale();
        $category_type = $request->get('type', '');
        $view_type = $request->get('view', '');
        if ($category_type !== '') {
            session(['category_type' => $category_type]);
        }
        if ($view_type !== '') {
            session(['view_type' => $view_type]);
        }
        $ctype = session('category_type', Category::CATEGORY_TYPE_POST);
        $filter['name'] = $request->get('name', '');

        $query = $this->category->where('lang_code', $lang_code)->orderBy('name');
        if (!empty($filter['name'])) {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }

        $query->where('type', $ctype);
        $categories = $this->category->showCategories($query->get());
        $option_category_types = Util::makeHTMLOptions($this->category_types, $ctype, 0, 0, 0);

        $view_type = session('view_type', 'list');

        $route_name = 'admin_category_edit';
        $option_column_button = Category::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        //$clsDataGrid->addColumnLabel("slug", "Slug", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%'", ["Không", "Có"]);
        if ($ctype == Category::CATEGORY_TYPE_POST || $ctype == Category::CATEGORY_TYPE_SERVICE) {
            $clsDataGrid->addColumnSelect("at_home", "Hiển thị trang chủ", "width='10%'", ["Không", "Có"]);
        }
        $clsDataGrid->addColumnText("order_no", "STT", "width='5%'");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($categories);

        return view('admin.category.index',
            compact(
                'categories',
                'option_category_types',
                'filter',
                'view_type',
                'dataGrid'
            )
        );
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Category::where('id', $key)->update($value);
        }
        return redirect()->route('admin_category')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Request $request, Category $category)
    {
        $ctype = session('category_type', Category::CATEGORY_TYPE_POST);
        $list_categories = Category::makeListCategory(0, $ctype, $category->parent_id);
        return view('admin.category.create',
            compact('category', 'list_categories', 'ctype'));
    }

    public function save(Category $category, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'slug' => 'required|alpha_dash|unique:categories,slug,' . $category->id,
            'order_no' => 'integer'
        ]);

        $parent_id = $request->get('parent_id', 0);
        if ($category->exists && $category->id == $parent_id) {
            return back()->withInput()->withErrors(['parent_id' => 'Danh mục cha không thể là chính nó']);
        }

        $lang_code = App::getLocale();

        $category->name = $request->get('name');
        $category->name_2 = $request->get('name_2');
        $category->name_3 = $request->get('name_3');
        $category->slug = $request->get('slug');
        $category->sapo = $request->get('sapo');
        $category->content = $request->get('content');
        $category->image = $request->get('image');
        $category->icon = $request->get('icon');
        $category->banner = $request->get('banner');
        $category->parent_id = $parent_id;
        $category->order_no = $request->get('order_no', 99);
        $category->state = (int)$request->get('state', 0);
        $category->at_home = (int)$request->get('at_home', 0);
        $category->hide_bottom = (int)$request->get('hide_bottom', 0);
        if (!$category->exists) {
            $category->lang_code = $lang_code;
            $category->type = session('category_type', Category::CATEGORY_TYPE_POST);
        }
        $category->meta_title = $request->get('meta_title');
        $category->meta_keys = $request->get('meta_keys');
        $category->meta_des = $request->get('meta_des');
        $category->save();

        return redirect()->route('admin_category_edit', $category)->with('success', 'Cập nhật thông tin thành công');
    }

    public function delete(Request $request, $id)
    {
        $this->category->destroy($id);
        return redirect()->route('admin_category')->with('success', 'Xóa danh mục thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->category->destroy($ids);
        return $this->responseJsonOk();
    }

    public function find(Request $request)
    {
        $query = $request->get('q');
        if (!Str::length($query)) {
            return response()->json(
                [
                    'results' => [],
                ]);
        }

        $results = [];
        $product_types = Category::where('name', 'LIKE', '%' . $query . '%')->take(15)->get();
        foreach ($product_types as $product_type) {
            $results[] = [
                'value' => $product_type['id'],
                'hint' => $product_type['slug'],
                'label' => $product_type['name']
            ];
        }
        return response()->json(['results' => $results]);
    }
}

