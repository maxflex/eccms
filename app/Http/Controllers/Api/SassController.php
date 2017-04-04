<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class SassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = Collect(Storage::disk('web_sass')->files())->filter(function ($file) {
            return preg_match('/[\w]\.scss/', $file);
        });

        return ['data' => $files];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return [
            'id'      => $id,
            'text' => Storage::disk('web_sass')->get($id)
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Storage::disk('web_sass')->put($id, $request->text);
        $output = shell_exec("/home/egerep-web/compile-web-sass.sh 2>&1");
        Storage::disk('web_sass')->put('log.txt', $output);
    }
}
