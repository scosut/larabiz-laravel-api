<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{

    use ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
      $data = json_decode($request->getContent(), true);    
      $data = filter_var_array($data, FILTER_SANITIZE_STRING);

      // Validate user input
      $validator = Validator::make($data, [
        'token' => 'required|string',
        'email' => 'required|string|email',
        'password' => 'required|string|min:6|confirmed',
        'password_confirmation' => 'required|string|min:6'
      ]);

      if ($validator->fails()) {
          return response()->json(["success" => false, "errors" => $validator->errors(), "message" => "failed validation", "data" => $data]);
      }

      // Here we will attempt to reset the user's password. If it is successful we
      // will update the password on an actual user model and persist it to the
      // database. Otherwise we will parse the error and return the response.

      $response = $this->broker()->reset($data, function ($user, $password) {
          $this->resetPassword($user, $password);
        }
      );

      return $response == Password::PASSWORD_RESET
        ? $this->sendResetResponse($data, $response)
        : $this->sendResetFailedResponse($data, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password) 
    { 
        $user->password = bcrypt($password);
        $user->save();
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse($data, $response)
    {
      return response()->json(["success" => true, "errors" => null, "message" => trans($response), "data" => $data]);                       
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse($data, $response)
    {
      return response()->json(["success" => false, "errors" => null, "message" => trans($response), "data" => $data]);            
    }
}