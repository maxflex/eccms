<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SassController extends Controller
{
    public function index($directory = null)
    {
        return view('sass.index')->with(ngInit([
            'current_path' => $_SERVER['REQUEST_URI'],
        ]));
    }

    public function edit($file)
    {
        return view('sass.edit')->with(ngInit(compact('file')));
    }
}
