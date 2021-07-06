<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;
use Auth;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);
    }

    public function login(Request $request) {
      $data = json_decode($request->getContent(), true);    
      $data = filter_var_array($data, FILTER_SANITIZE_STRING);

      // validate inputs
      $validator = $this->validator($data);

      if($validator->fails()) {
        return response()->json(["success" => false, "errors" => $validator->errors(), "message" => "failed validation", "data" => $data]);
      }

      if (Auth::attempt([
        'email' => $data['email'], 
        'password' => $data['password']
      ])) {
        $user = User::where('email', $data['email'])->first();

        return response()->json(["success" => true, "errors" => null, "message" => "$user->name is now logged in", "data" => $user]);
      }
      else {        
        return response()->json(["success" => false, "errors" => null, "message" => "user with those credentials not found", "data" => ""]);
      }
    }
}
