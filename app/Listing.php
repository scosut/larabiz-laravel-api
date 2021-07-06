<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
  protected $fillable = [
    "name", "website", "email", "phone", "address", "bio", "user_id"
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }
}
