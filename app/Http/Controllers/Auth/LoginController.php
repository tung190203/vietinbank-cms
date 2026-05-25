<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
    protected $redirectTo = RouteServiceProvider::ADMIN;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return "username";
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        $username = $request->get('username');
        $password = $request->get('password');
        $remember = (bool)$request->get('remember');

        $user = User::where('username', $username)->first();

        if ($user) {
            if ($user->state == 0) {
                return redirect()->route('login')->withInput()->withErrors(['username' => 'Tài khoản đã bị khóa']);
            }
            if (!Hash::check($password, $user->password)) {
                return redirect()->route('login')->withInput()->withErrors(['password' => 'Mật khẩu không đúng']);
            }
            Auth::loginUsingId($user->id, $remember);
            return redirect($this->redirectPath());
        } else {
            return redirect()->route('login')->withInput()->withErrors(['username' => 'Tài khoản không tồn tại']);
        }
    }
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
