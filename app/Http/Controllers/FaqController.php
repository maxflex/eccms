<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;
use App\Http\Requests;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        return view('faq.index')->with(ngInit([
            'current_page' => $request->page,
        ]));
    }

    public function create()
    {
        return view('faq.create')->with(ngInit([
            'model' => new Faq
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
        return view('faq.edit')->with(ngInit(compact('id')));
    }
}
