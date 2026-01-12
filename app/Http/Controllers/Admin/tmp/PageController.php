<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class PageController extends Controller
{
    private $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->selectedMainMenu = 'page';

        parent::__construct();

        $this->middleware('can:page');
    }

    public function index()
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('page');
        $paginate = 20;
        $route_name = 'admin_page_edit';
        $option_column_button = Page::makeOptionColumnButton();

        $pages = $this->page->where('lang_code', $lang_code)->orderBy('name')->paginate($paginate);

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("url", "URL", "width='20%' nowrap");
        $clsDataGrid->addColumnImage("image", "Image", "", "width='10%' align='center' nowrap");
        $clsDataGrid->addColumnSelect("state", "Hiển thị", "width='5%' align='center'", ["Không", "Có"]);
        $clsDataGrid->addColumnText("order_no", "STT", "width='3%' align='center'");
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng", "width='5%' align='center' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' align='center' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($pages, $paginate, $pages->total());

        return view('admin.page.index', compact('pages', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Page::where('id', $key)->update($value);
        }
        return redirect()->route('admin_page')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Page $page)
    {
        return view('admin.page.create', compact('page'));
    }

    public function save(Page $page, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
//            'sapo' => 'required|string',
            'content' => 'required|string',
        ]);

        $lang_code = App::getLocale();
        $name = $request->get('name');
        $page->name = $name;
        $page->slug = Str::slug($name);
        $page->sapo = $request->get('sapo');
        $page->content = $request->get('content');
        $page->image = $request->get('image');
        $page->cat_id = $request->get('cat_id', 0);
        $page->state = (int)$request->get('state', 0);
        $page->meta_title = $request->get('meta_title');
        $page->meta_keys = $request->get('meta_keys');
        $page->meta_des = $request->get('meta_des');
        if (!$page->exists) {
            $page->lang_code = $lang_code;
        }

        $page->save();

        return redirect()->route('admin_page_edit', $page)->with('success', 'Cập nhật thông tin thành công');
    }

    public function clone(Page $page)
    {
        $page_id = data_get($page, 'id', 0);
        $page = Page::find($page_id);
        if ($page) {
            $new_page = $page->replicate();
            $new_page->name = $page->name . " copy";
            if ($new_page->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {

        $this->page->destroy($id);
        return redirect()->to(route('admin_page'));
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->page->destroy($ids);
        return $this->responseJsonOk();
    }
}

