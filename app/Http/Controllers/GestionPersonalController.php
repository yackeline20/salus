<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestionPersonalController extends Controller
{
    public function index()
    {
        return view('profile.gestion-personal'); 
    }
}