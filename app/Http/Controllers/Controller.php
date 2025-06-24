<?php

namespace App\Http\controlles\controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
  public function showUsers(){
    return"bbb";

  }

  
  public function createUsers()   {
    return view('userscreate');
  }
}
