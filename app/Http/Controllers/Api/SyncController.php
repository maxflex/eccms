<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class SyncController extends Controller
{
    public function getData($table)
    {
        return DB::table($table)->get()->all();
    }

    public function setData($table, Request $request)
    {
        forceTruncate($table);
        foreach($request->all() as $data) {
            DB::table($table)->insert($data);
        }
    }
}
