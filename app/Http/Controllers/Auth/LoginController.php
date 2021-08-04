<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
    protected $redirectTo = '/panel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        $seoSettings = getSeoMetas('login');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('site.login_page_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.login_page_title');
        $pageRobot = getPageRobot('login');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
        ];

        return view(getTemplate() . '.auth.login', $data);
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => 'required|numeric',
            'password' => 'required|min:6',
        ];

        if ($this->username() == 'email') {
            $rules['username'] = 'required|string|email';
        }

        $this->validate($request, $rules);

        if ($this->attemptLogin($request)) {
            return $this->afterLogged($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function username()
    {
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (empty($this->username)) {
            $this->username = 'mobile';
            if (preg_match($email_regex, request('username', null))) {
                $this->username = 'email';
            }
        }
        return $this->username;
    }

    protected function attemptLogin(Request $request)
    {
        $credentials = [
            $this->username() => $request->get('username'),
            'password' => $request->get('password')
        ];
        $remember = true;

        /*if (!empty($request->get('remember')) and $request->get('remember') == true) {
            $remember = true;
        }*/

        return $this->guard()->attempt($credentials, $remember);
    }

    public function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'username' => [trans('validation.password_or_username')],
        ]);
    }

    protected function sendBanResponse($user)
    {
        throw ValidationException::withMessages([
            'username' => [trans('auth.ban_msg', ['date' => dateTimeFormat($user->ban_end_at, 'Y M j')])],
        ]);
    }

    protected function sendNotActiveResponse($user)
    {
        $toastData = [
            'title' => trans('public.request_failed'),
            'msg' => trans('auth.login_failed_your_account_is_not_verified'),
            'status' => 'error'
        ];

        return redirect('/login')->with(['toast' => $toastData]);
    }

    public function afterLogged(Request $request, $verify = false)
    {
        $user = auth()->user();

        if ($user->ban) {
            $time = time();
            $endBan = $user->ban_end_at;
            if (!empty($endBan) and $endBan > $time) {
                $this->guard()->logout();
                $request->session()->flush();
                $request->session()->regenerate();

                return $this->sendBanResponse($user);
            } elseif (!empty($endBan) and $endBan < $time) {
                $user->update([
                    'ban' => false,
                    'ban_start_at' => null,
                    'ban_end_at' => null,
                ]);
            }
        }

        if (!$user->verified and !$verify) {
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            $verificationController = new VerificationController();
            $checkConfirmed = $verificationController->checkConfirmed($user, $this->username(), $request->get('username'));

            if ($checkConfirmed['status'] == 'send') {
                return redirect('/verification');
            }
        } elseif ($verify) {
            session()->forget('verificationId');

            $user->update([
                'verified' => true,
                'status' => User::$active,
            ]);
        }

        if ($user->status != User::$active) {
            $this->guard()->logout();
            $request->session()->flush();
            $request->session()->regenerate();

            return $this->sendNotActiveResponse($user);
        }

        if ($user->isAdmin()) {
            return redirect('/admin');
        } else {
            return redirect('/panel');
        }
    }
}
