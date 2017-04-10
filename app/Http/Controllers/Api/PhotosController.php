<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Photo;

class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Photo::get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('photos')) {
            $extension = $request->file('photos')->extension();
            $photo = uniqid() . '.' . $extension;
            $request->file('photos')->storeAs(Photo::UPLOAD_DIR, $photo);
            return Photo::create($request->all());
        } else {
            abort(400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        Storage::delete(Photo::UPLOAD_DIR . $request->photo);
    }
}
