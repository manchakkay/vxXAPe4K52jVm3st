<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\_Templates\Controller;

class Users_MainController extends Controller
{
    public function get()
    {
        return view('admin.users');
    }
}
