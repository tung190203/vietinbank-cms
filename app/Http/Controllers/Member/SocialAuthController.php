<?php

namespace App\Http\Controllers\Member;

use App\Models\Member;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Socialite;

class SocialAuthController extends Controller
{
    public function redirect($social)
    {
        return Socialite::driver($social)->redirect();
    }

    public function callback($social)
    {
        $member = $this->createOrGetUser(Socialite::driver($social)->user(), $social);
        Auth::guard('member')->loginUsingId($member->id, true);
        return redirect()->route('member');
    }

    public function createOrGetUser(ProviderUser $providerUser, $social)
    {
        $provider_user_id = $providerUser->getId();
        $name = $providerUser->getName();
        $email = $providerUser->getEmail();
        $avatar = $providerUser->getAvatar();
        $member = Member::where('email', $email)->first();

        if ($member) {
            return $member;
        } else {
            $member = new Member();
            $member->email = $email;
            $member->name = $name;
            $member->avatar = $avatar;
            $member->provider = $social;
            $member->provider_user_id = $provider_user_id;
            $member->save();
            return $member;
        }
    }
}
