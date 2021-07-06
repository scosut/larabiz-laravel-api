<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;
    
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
      $data = json_decode($request->getContent(), true);    
      $data = filter_var_array($data, FILTER_SANITIZE_STRING);

      // Validate user input
      $validator = Validator::make($data, [
          'email' => 'required|string|email'
      ]);

      if ($validator->fails()) {
          return response()->json(["success" => false, "errors" => $validator->errors(), "message" => "failed validation", "data" => $data]);
      }
      
      // Attempt to send the password reset email to user.  
      $response = $this->broker()->sendResetLink($data);
        
      // After attempting to send the link, we can examine the response to see 
      // the message we need to show to the user and then send out a 
      // proper response.

      return $response == Password::RESET_LINK_SENT
        ? $this->sendResetLinkResponse($data, $response)
        : $this->sendResetLinkFailedResponse($data, $response);
    }
    /**
     * Send the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse($data, $response)
    {
        
      // On success, a string $response is returned with value of RESET_LINK_SENT 
      // from the Password facade (the default is "passwords.sent") 
      // Laravel trans() function translates this response to the text  
      // designated in resources/lang/en/passwords.php

      return response()->json(["success" => true, "errors" => null, "message" => trans($response), "data" => $data]);
    }
    /**
     * Send the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkFailedResponse($data, $response)
    {
      return response()->json(["success" => false, "errors" => null, "message" => trans($response), "data" => $data]);
    }
}
