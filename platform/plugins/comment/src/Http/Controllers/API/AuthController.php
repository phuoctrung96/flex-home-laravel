<?php

namespace Botble\Comment\Http\Controllers\API;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Comment\Models\CommentUser;
use Botble\Comment\Repositories\Interfaces\CommentUserInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;


class AuthController extends BaseController
{
    public function __construct()
    {

    }

    public function SocialSignup($provider)
    {
        // Socialite will pick response data automatic 
        $user = Socialite::driver($provider)->stateless()->user();

        return response()->json($user);
    }

    public function callbackFromSocialAuth()
    {
        try {
            $user = Socialite::driver('facebook')->user();

            $saveUser = User::updateOrCreate([
                'facebook_id' => $user->getId(),
            ],[
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getName().'@'.$user->getId())
                    ]);

            Auth::loginUsingId($saveUser->id);

            return redirect()->route('home');
        } catch (\Throwable $th) {
                throw $th;
        }

        
    }
}
