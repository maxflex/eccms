<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SyncController extends Controller
{
    public function getData($table)
    {
        return DB::table($table)->get()->all();
    }
}
