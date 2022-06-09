<?php

namespace App\Http\Controllers\Admin\Gallery;

use App\Http\Controllers\_Templates\Controller;

class Gallery_MainController extends Controller
{

    public function get()
    {
        return view('admin.galleries');
    }
}
