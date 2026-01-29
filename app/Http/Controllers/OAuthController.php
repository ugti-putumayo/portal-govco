<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();
        return $this->loginOrCreateUser($googleUser, 'google');
    }

    public function redirectToOutlook()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function handleOutlookCallback()
    {
        $outlookUser = Socialite::driver('microsoft')->user();
        return $this->loginOrCreateUser($outlookUser, 'microsoft');
    }

    protected function loginOrCreateUser($oauthUser, $provider)
    {
        $user = User::firstOrCreate(
            ['email' => $oauthUser->getEmail()],
            [
                'name'     => $oauthUser->getName() ?? $oauthUser->getNickname(),
                'password' => bcrypt(uniqid()),
            ]
        );
        Auth::login($user);
        if (method_exists($user, 'modules')) {
            session()->put('modules', $user->modules);
        }
        return redirect()->route('dashboard');
    }
}