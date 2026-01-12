<?php

namespace App\Http\Controllers\Member;

use App\Libs\Util;
use App\Models\Member;
use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:member')->except(['logout']);
    }

    protected function guard()
    {
        return Auth::guard('member');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:members'],
            'password' => ['required', 'string', 'min:8'],
        ]);
    }

    public function showRegistrationForm()
    {
        $setting = Setting::getAllSetting();
        return view('member.register', compact('setting'));
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
        $password = $request->get('password');
        $email = $request->get('email');

        $member = new Member();
        $member->name = $request->get('name');
        $member->email = $email;
        $member->password = Hash::make($password);
        $member->state = Member::STATE_INACTIVE;
        if ($member->save()) {
            //Send email active
            $data = [
                'setting' => Setting::getAllSetting(),
                'url_active' => route('member_verify', [$member->id, strtotime($member->created_at)])
            ];
            Util::sendEmail('email.member.verify', $data, 'Xác thực tài khoản từ website ' . url('/'), $email);
            return redirect()->route('member_register')->with('error', __('member.register_success'));
        } else {
            return redirect()->route('member_register')->with('error', 'Register error');
        }
    }

    public function active(Request $request)
    {

    }
}
