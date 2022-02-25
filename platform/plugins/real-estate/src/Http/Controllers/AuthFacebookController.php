<?php

namespace Botble\RealEstate\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//use Socialite;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Illuminate\Support\Facades\DB;

class AuthFacebookController extends Controller
{
  /**
   * Create a redirect method to facebook api.
   *
   * @return void
   */
    protected $accountRepository;

    public function __construct(AccountInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;    
    }

    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    protected function guard()
    {
        return auth('account');
    }

    public function callback()
    {       
        try { 
            $user = Socialite::driver('facebook')->user();
            $saveUser = [
                'facebook_id' => $user->getId(),
                'first_name' => $user->getName(),
                'last_name' => $user->getName(),
                'username' => $user->getName(),
                'email' => $user->getEmail(),
                'password' => Hash::make($user->getName().'@'.$user->getId())
            ];
            //$this->accountRepository->createOrUpdate($saveUser,['facebook_id' => $user->getId()]); 
            //$this->accountRepository->createOrUpdate($saveUser); 
            DB::table('re_accounts')->updateOrCreate(['facebook_id' => $user->getId()], $saveUser);
            $this->guard()->login($saveUser);           
            //auth()->login($saveUser);
            return redirect()->to('/projects');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}