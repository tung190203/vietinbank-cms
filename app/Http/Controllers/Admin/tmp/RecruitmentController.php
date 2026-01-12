<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Recruitment;
use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class RecruitmentController extends Controller
{
    private $recruitment;

    public function __construct(Recruitment $recruitment)
    {
        $this->recruitment = $recruitment;
        $this->selectedMainMenu = 'recruitment';

        parent::__construct();

        $this->middleware('can:recruitment');
    }

    public function index(Request $request)
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('recruitment');
        $category = new Category();
        $category->getParentArray();

        $filter['name'] = $request->get('name', '');
        $filter['cat_id'] = $request->get('cat_id', 0);
        $filter['state'] = $request->get('state', -1);
        $query = $this->recruitment->with(['category', 'user'])
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

        $recruitments = $query->paginate(20);
        $options['categories'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_RECRUITMENT, $filter['cat_id']);
        $options['state'] = Util::makeHTMLOptions(Recruitment::STATE_ARRAY, $filter['state']);
        $arr_categories = Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_RECRUITMENT);

        $paginate = 20;
        $route_name = 'admin_recruitment_edit';
        $option_column_button = Recruitment::makeOptionColumnButton();

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

        $dataGrid = $clsDataGrid->showDataGrid($recruitments, $paginate, $recruitments->total());

        return view('admin.recruitment.index', compact('recruitments', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Recruitment::where('id', $key)->update($value);
        }
        return redirect()->route('admin_recruitment')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Recruitment $recruitment)
    {
        $options['category'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_RECRUITMENT, $recruitment->cat_id);
        return view('admin.recruitment.create', compact('recruitment', 'options'));
    }

    public function save(Recruitment $recruitment, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'description' => 'required|string',
            'job_requirement' => 'required|string',
        ]);

        $date_expired = explode('/', $request->get('date_expired'));
        if (count($date_expired) < 3) {
            return back()->withInput()->withErrors(['date_expired' => 'Date expired invalid']);
        }

        $date_expired = mktime(0, 0, 0, $date_expired['1'], $date_expired[0], $date_expired['2']);
        $lang_code = App::getLocale();
        $name = $request->get('name');
        $recruitment->name = $name;
        $recruitment->slug = Str::slug($name);
        $recruitment->quantity = $request->get('quantity') ?: 0;
        $recruitment->salary = $request->get('salary');
        $recruitment->description = $request->get('description');
        $recruitment->job_requirement = $request->get('job_requirement');
        $recruitment->benefit = $request->get('benefit');
        $recruitment->image = $request->get('image');
        $recruitment->experience_required = $request->get('experience_required');
        $recruitment->working_type = $request->get('working_type');
        $recruitment->working_location = $request->get('working_location');
        $recruitment->cat_id = $request->get('cat_id', 0);
        $recruitment->state = (int)$request->get('state', 0);
        $recruitment->date_expired = Carbon::createFromTimestamp($date_expired);
        $recruitment->meta_title = $request->get('meta_title');
        $recruitment->meta_keys = $request->get('meta_keys');
        $recruitment->meta_des = $request->get('meta_des');
        if (!$recruitment->exists) {
            $recruitment->lang_code = $lang_code;
        }

        $recruitment->save();

        return redirect()->route('admin_recruitment_edit', $recruitment)->with('success', 'Cập nhật thành công');
    }

    public function clone(Recruitment $recruitment)
    {
        $new_id = data_get($recruitment, 'id', 0);
        $recruitment = Recruitment::find($new_id);
        if ($recruitment) {
            $new_recruitment = $recruitment->replicate();
            $new_recruitment->name = $recruitment->name . " copy";
            if ($new_recruitment->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {

        $this->recruitment->destroy($id);
        return redirect()->to(route('admin_recruitment'))->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->recruitment->destroy($ids);
        return $this->responseJsonOk();
    }

    public function restore(Request $request, $id)
    {
        $recruitment = Recruitment::withTrashed()->findOrFail($id);
        $recruitment->restore();
        return redirect()->route('admin_recruitment')->with('success', 'Khôi phục bài viết thành công');
    }

    public function forceDelete(Request $request, $id)
    {
        $recruitment = Recruitment::withTrashed()->findOrFail($id);
        $recruitment->forceDelete();
        return redirect()->route('admin_recruitment', 'state=2')->with('success', 'Xóa bài viết thành công');
    }
}

