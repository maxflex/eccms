<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service\VersionControl;
use DB;

class SyncController extends Controller
{
    public function getData($table)
    {
        $data = DB::table($table)->get()->all();
        foreach($data as $d) {
            $d->previous_md5 = VersionControl::get($table, $d->id);
        }
        return $data;
    }

    public function setData($table, Request $request)
    {
        forceTruncate($table);
        foreach($request->all() as $data) {
            DB::table($table)->insert($data);
        }
    }
}
