<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PhotosController extends Controller
{
    public function index()
    {
        return view('photos.index');
    }
}
