<?php

namespace App\Http\Controllers\Admin;

use App\Libs\DataGrid;
use App\Libs\Util;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Category;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class MemberController extends Controller
{
    private $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
        $this->selectedMainMenu = 'member';

        parent::__construct();

        $this->middleware('can:member');
    }

    public function index(Request $request)
    {
        $this->selectedSubMenu('member');
        $filter['name'] = $request->get('name', '');
        $filter['state'] = $request->get('state', -1);
        $query = $this->member->orderBy('name', 'asc')->orderBy('id', 'desc');
        if ($filter['name'] !== '') {
            $query->where('name', 'like', '%' . $filter['name'] . '%');
        }
        if ($filter['state'] > -1) {
            $query->where('state', $filter['state']);
        }

        $members = $query->paginate(20);
        $options['state'] = Util::makeHTMLOptions(Member::STATE_ARRAY, $filter['state']);

        $paginate = 20;
        $route_name = 'admin_member_edit';
        $option_column_button = Member::makeOptionColumnButton();

        $clsDataGrid = new DataGrid();
        $clsDataGrid->setLinkEdit($route_name);
        $clsDataGrid->addColumnLabel("name", "Name", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("email", "Email", "width='20%' nowrap");
        $clsDataGrid->addColumnLabel("phone", "Số điện thoại", "width='20%' nowrap");
        $clsDataGrid->addColumnSelect("state", "Trạng thái", "width='5%' align='center'", Member::STATE_ARRAY);
        $clsDataGrid->addColumnDate("created_at", "Ngày đăng ký", "width='5%' align='center' nowrap ", 'd-m-Y');
        $clsDataGrid->addColumnButton('id', '&nbsp', $option_column_button, "width='5%' align='center' nowrap ");

        $dataGrid = $clsDataGrid->showDataGrid($members, $paginate, $members->total());

        return view('admin.member.index', compact('members', 'filter', 'options', 'dataGrid'));
    }

    public function saveDataIndex(Request $request)
    {
        $update = $request->get('update', []);
        foreach ($update as $key => $value) {
            Member::where('id', $key)->update($value);
        }
        return redirect()->route('admin_member')->with('success', 'Cập nhật thông tin thành công');
    }

    public function edit(Member $member)
    {
        return view('admin.member.create', compact('member'));
    }

    public function save(Member $member, Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|string|unique:members,email,' . $member->id,
            'phone' => 'required|string|unique:members,phone,' . $member->id,
        ]);
        $member->name = $request->get('name');
        $member->email = $request->get('email');
        $member->phone = $request->get('phone');
        $member->address = $request->get('address');
        $member->state = (int)$request->get('state', 0);

        $member->save();

        return redirect()->route('admin_member_edit', $member)->with('success', 'Cập nhật thành công');
    }

    public function delete(Request $request, $id)
    {
        $this->member->destroy($id);
        return redirect()->to(route('admin_member'))->with('success', 'Xóa thành công');
    }

    public function deleteCheckbox(Request $request)
    {
        $this->validate($request, ['ids' => 'required|array']);

        $ids = $request->get('ids');
        if (empty($ids)) {
            return $this->responseJsonBadRequest();
        }

        $this->member->destroy($ids);
        return $this->responseJsonOk();
    }
}

