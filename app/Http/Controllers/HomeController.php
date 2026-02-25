<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() 
    {
        $users = User::all();
        return view('admin.index')->with('users',$users);
    }

    public function home()
    {
        return view ('home.index');
    }
    // public function contact()
    // {
    //     return view ('home.contactus');
    // }
}
