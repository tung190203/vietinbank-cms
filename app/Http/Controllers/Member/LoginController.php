<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libs\Http;
use App\Libs\Util;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::MEMBER;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:member')->except('logout');
    }

    protected function guard()
    {
        return Auth::guard('member');
    }

    public function showLoginForm()
    {
        $setting = Setting::getAllSetting();
        return view('member.login', compact('setting'));
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        $email = $request->get('email');
        $password = $request->get('password');
        $remember = (boolean)$request->get('remember');

        $member = Member::where('email', $email)->first();

        if ($member) {
            if ($member->state == 0) {
                return back()->withInput()->withErrors('Tài khoản chưa được kích hoạt');
            }
            if (!Hash::check($password, $member->password)) {
                return back()->withInput()->withErrors('Mật khẩu không đúng');
            }
            Auth::guard('member')->loginUsingId($member->id, $remember);
            return redirect()->route('member');
        } else {
            return back()->withInput()->withErrors('Tài khoản không tồn tại');
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/');
    }
}
