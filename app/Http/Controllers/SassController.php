<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SassController extends Controller
{
    public function index()
    {
        return view('sass.index');
    }

    public function edit($id)
    {
        return view('sass.edit')->with(ngInit(compact('id')));
    }
}
