<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;
use DB;
use App\Http\Requests;
use App\Models\Page;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * $ngController [Search|Pages] – откуда перешли – с поиска или через меню
     */
    public function index(Request $request, $ngController = 'Pages')
    {
        return view('pages.index')->with(ngInit([
            'current_page'      => $request->page,
            'exportable_fields' => Page::getExportableFields(),
        ]))->with(compact('ngController'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.create')->with(ngInit([
            'model' => new Page
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
        return view('pages.edit')->with(ngInit(compact('id')));
    }

    /**
     * Экспорт данных в excel файл
     *
     */
    public function export(Request $request) {
        return Page::export($request);
    }

    /**
     * Импорт данных из excel файла
     *
     */
    public function import(Request $request) {
        Page::import($request);
    }

    /**
     * Поиск из меню
     */
     public function search(Request $request)
     {
         return $this->index($request, 'Search');
     }

}
