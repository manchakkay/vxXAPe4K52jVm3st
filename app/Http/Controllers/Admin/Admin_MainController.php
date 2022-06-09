<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\_Templates\Controller;

class Admin_MainController extends Controller
{
    public function get()
    {
        return view('admin');
    }
}
