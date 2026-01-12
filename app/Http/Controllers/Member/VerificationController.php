<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VerificationController extends Controller
{

    protected $redirectTo = RouteServiceProvider::MEMBER;

    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('member.verify');
    }

    public function verify(Request $request, $member_id, $created_at)
    {
        $member = Member::findOrFail($member_id);
        if ($member->email_verified_at || intval($created_at) !== strtotime($member->created_at)) {
            return abort(404);
        }

        $member->email_verified_at = Carbon::now();
        $member->state = Member::STATE_ACTIVE;
        $member->save();

        return redirect()->route('member_login')->with('error', __('member.active_success'));
    }

}
