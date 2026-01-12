<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
        $this->selectedMainMenu = 'post';

        parent::__construct();

        $this->middleware('can:post');
    }

    public function index(Request $request)
    {
        $lang_code = App::getLocale();
        $this->selectedSubMenu('post');
        $category = new Category();
        $category->getParentArray();

        $filter['name'] = $request->get('name', '');
        $filter['cat_id'] = $request->get('cat_id', 0);
        $filter['state'] = $request->get('state', -1);
        $query = $this->post->with(['category', 'user'])
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

        $posts = $query->paginate(20);
        $options['categories'] = Category::makeListCategory(0, Category::CATEGORY_TYPE_POST, $filter['cat_id']);
        $options['state'] = Util::makeHTMLOptions(Post::STATE_ARRAY, $filter['state']);
        $arr_categories = Category::makeArrayListCategory(0, Category::CATEGORY_TYPE_POST);

        $paginate = 20;
        $route_name = 'admin_post_edit';
        $option_column_button = Post::makeOptionColumnButton();

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

        $dataGrid = $clsDataGrid->showDataGrid($posts, $paginate, $posts->total());

        return view('admin.post.index', compact('posts', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Post::where('id', $key)->update($value);
        }
        return redirect()->route('admin_post')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Post $post)
    {
        $list_categories = Category::makeListCategory(0, Category::CATEGORY_TYPE_POST, $post->cat_id);
        return view('admin.post.create', compact('post', 'list_categories'));
    }

    public function save(Post $post, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            //'slug' => 'required|alpha_dash|unique:products,slug,' . $post->id,
            'sapo' => 'required|string',
            'content' => 'required|string',
        ]);
        $lang_code = App::getLocale();
        $name = $request->get('name');
        $created_at = $request->get('created_at');
        $post->name = $name;
        $post->slug = Str::slug($name);
        $post->sapo = $request->get('sapo');
        $post->content = $request->get('content');
        $post->image = $request->get('image');
        $post->cat_id = $request->get('cat_id', 0);
        $post->state = (int)$request->get('state', 0);
        $post->is_hot = (int)$request->get('is_hot', 0);
        $post->created_at = Carbon::createFromDate($created_at);
        $post->meta_title = $request->get('meta_title');
        $post->meta_keys = $request->get('meta_keys');
        $post->meta_des = $request->get('meta_des');
        if (!$post->exists) {
            $post->lang_code = $lang_code;
        }

        $post->save();

        return redirect()->route('admin_post_edit', $post)->with('success', 'Cập nhật thành công');
    }

    public function clone(Post $post)
    {
        $new_id = data_get($post, 'id', 0);
        $post = Post::find($new_id);
        if ($post) {
            $new_post = $post->replicate();
            $new_post->name = $post->name . " copy";
            if ($new_post->save()) {
                return back()->with('success', 'Sao chép thành công');
            }
        }
        return back()->with('error', 'Sao chép không thành công');
    }

    public function delete(Request $request, $id)
    {

        $this->post->destroy($id);
        return redirect()->to(route('admin_post'))->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->post->destroy($ids);
        return $this->responseJsonOk();
    }

    public function restore(Request $request, $id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        return redirect()->route('admin_post')->with('success', 'Khôi phục bài viết thành công');
    }

    public function forceDelete(Request $request, $id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->forceDelete();
        return redirect()->route('admin_post', 'state=2')->with('success', 'Xóa bài viết thành công');
    }
}

