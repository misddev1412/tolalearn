<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $seoSettings = getSeoMetas('register');
        $pageTitle = !empty($seoSettings['title']) ? $seoSettings['title'] : trans('site.register_page_title');
        $pageDescription = !empty($seoSettings['description']) ? $seoSettings['description'] : trans('site.register_page_title');
        $pageRobot = getPageRobot('register');

        $data = [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageRobot' => $pageRobot,
        ];

        return view(getTemplate() . '.auth.register', $data);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $username = $this->username();

        if ($username == 'mobile') {
            $data[$username] = ltrim($data['country_code'], '+') . ltrim($data[$username], '0');
        }

        return Validator::make($data, [
            'country_code' => ($username == 'mobile') ? 'required' : 'nullable',
            $username => ($username == 'mobile') ? 'required|numeric|unique:users' : 'required|string|email|max:255|unique:users',
            'term' => 'required',
            'full_name' => 'required|string|min:3',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return
     */
    protected function create(array $data)
    {
        $username = $this->username();

        if ($username == 'mobile') {
            $data[$username] = ltrim($data['country_code'], '+') . ltrim($data[$username], '0');
        }

        $user = User::create([
            'role_name' => Role::$user,
            'role_id' => 1,//normal user
            $username => $data[$username],
            'full_name' => $data['full_name'],
            'status' => User::$pending,
            'password' => Hash::make($data['password']),
            'created_at' => time()
        ]);

        return $user;
    }

    public function username()
    {
        $email_regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";

        if (empty($this->username)) {
            $this->username = 'mobile';
            if (!empty(request('email', null))) {
                $this->username = 'email';
            }
        }
        return $this->username;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        $username = $this->username();

        $value = $request->get($username);
        if ($username == 'mobile') {
            $value = $request->get('country_code') . ltrim($request->get($username), '0');
        }

        $verificationController = new VerificationController();
        $checkConfirmed = $verificationController->checkConfirmed($user, $username, $value);

        if ($checkConfirmed['status'] == 'send') {
            return redirect('/verification');
        } elseif ($checkConfirmed['status'] == 'verified') {
            $this->guard()->login($user);

            $user->update([
                'status' => User::$active,
            ]);

            if ($response = $this->registered($request, $user)) {
                return $response;
            }

            return $request->wantsJson()
                ? new JsonResponse([], 201)
                : redirect($this->redirectPath());
        }
    }
}
