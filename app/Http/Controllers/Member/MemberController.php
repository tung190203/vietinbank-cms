<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Order;
use App\Models\Province;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mail;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:member');
    }

    public function index()
    {
        $class_body = 'customers-account';
        $member = Auth::guard('member')->user();
        $orders = Order::where('member_id', $member->id)->orderBy('id', 'desc')->get();
        $option_status_order = Order::OPTION_STATUS_ORDER;

        $setting = Setting::getAllSetting();
        $setting['meta_title'] = 'Profile';
        return view('member.index',
            compact(
                'member',
                'orders',
                'option_status_order',
                'setting',
                'class_body'
            )
        );
    }

    public function orderDetail(Request $request, $order_id)
    {
        $member = Auth::guard('member')->user();
        $order = Order::where('id', $order_id)->where('member_id', $member->id)->firstOrFail();
        $setting = Setting::getAllSetting();
        $setting['meta_title'] = 'Order detail';
        return view('member.order_detail', compact('order'));
    }

    public function profile(Request $request)
    {
        $setting = Setting::getAllSetting();
        $member = Auth::guard('member')->user();
        $province_id = $member->province_id ? $member->province_id : 1;
        $provinces = Province::makeListProvince($province_id);
        $districts = District::makeListDistrict($province_id, $member->district_id);
        $setting['meta_title'] = 'Profile';
        return view('member.profile', compact('setting', 'member', 'provinces', 'districts'));
    }

    public function updateProfile(Request $request)
    {
        $phone = $request->get('phone');
        $address = $request->get('address');
        $password = $request->get('password');
        $province_id = $request->get('province_id');
        $district_id = $request->get('district_id');
        $member = Auth::guard('member')->user();

        $member->phone = $phone;
        $member->address = $address;
        if ($password) {
            $member->password = Hash::make($password);
        }
        $member->province_id = $province_id;
        $member->district_id = $district_id;
        if ($member->save()) {
            return redirect()->route('member_profile')->with('message', 'Cập nhật thông tin thành công');
        } else {
            return redirect()->route('member_profile')->with('message', 'Đã xẩy ra lỗi vui lòng thử lại sau');
        }
    }
}
