<?php

namespace App\Http\Controllers;

use App\Models\Programm;
use Illuminate\Http\Request;
use App\Http\Requests;

class ProgrammsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('programms.index')->with(ngInit([
            'current_page' => $request->page
        ]));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('programms.create')->with(ngInit([
            'model' => new Programm(),
        ]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('programms.edit')->with(ngInit(compact('id')));
    }
}
