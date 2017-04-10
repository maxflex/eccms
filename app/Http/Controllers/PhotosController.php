<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Photo;
use App\Http\Requests;

class PhotosController extends Controller
{
    public function index()
    {
        return view('photos.index');
    }

    public function create()
    {
        return view('photos.create')->with(ngInit([
            'model' => new Photo
        ]));
    }

    public function edit($id)
    {
        return view('photos.edit')->with(ngInit(compact('id')));
    }
}
