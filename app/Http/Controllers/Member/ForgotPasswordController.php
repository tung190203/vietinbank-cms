<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Libs\Util;
use App\Models\Member;
use App\Models\Setting;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:member');
    }

    public function showFormReset()
    {
        $setting = Setting::getAllSetting();
        $setting['meta_title'] = __('member.reset_password');

        return view('member.reset_password', compact('setting'));
    }

    public function sendMailReset(Request $request)
    {
        $setting = Setting::getAllSetting();
        $email = $request->get('email');
        $member = Member::where('email', $email)->first();

        if (!$member) {
            return redirect()->route('member_reset_password')->with('message', __('member.account_notfound'));
        }

        $new_password = Str::random(10);
        $member->password = Hash::make($new_password);
        $member->save();

        $data = [
            'setting' => $setting,
            'new_password' => $new_password,
        ];
        $subject = __('email.subject_reset_email');
        Util::sendEmail('email.member.reset_password', $data, $subject, $email);
        return redirect()->route('member_reset_password')->with('message', __('email.reset_success'));
    }
}
