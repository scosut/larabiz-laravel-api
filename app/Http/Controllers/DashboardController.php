<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class DashboardController extends Controller
{
  public function index($userId)
  {
    $user = User::find($userId);
      
    if (!is_null($user)) {
      return response()->json(["success" => true, "data" => $user->listings]);
    }
    else {
      return response()->json(["success" => false, "data" => []]);
    }
  }
}
