<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Photo;
use App\Http\Controllers\Controller;


class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Photo::paginate(100);
    }

    public function show($id)
    {
        return Photo::find($id);
    }

    public function update(Request $request, $id)
    {
        Photo::find($id)->update($request->input());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return Photo::create($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->deleteFile(Photo::whereId($id)->value('filename'));
        Photo::destroy($id);
    }

    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $extension = $request->file('file')->extension();
            $filename = uniqid() . '.' . $extension;
            $request->file('file')->storeAs(Photo::UPLOAD_DIR, $filename, 'public');

            $this->deleteFile($request->old_file);

            return $filename;
        }

        return false;
    }

    public function deleteFile($filename = false)
    {
        if ($filename) {
            \Storage::disk('public')->delete(Photo::UPLOAD_DIR . $filename);
        }
    }
}
