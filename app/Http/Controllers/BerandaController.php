<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $username = Auth::user()->username; 
        return view('admin.beranda.index', compact('username'));
    }
}
