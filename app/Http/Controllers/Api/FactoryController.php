<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FactoryController extends Controller
{
    public function get(Request $request)
    {
        return fact($request->table,
            (isset($request->select) ? $request->select : null),
            (isset($request->orderBy) ? $request->orderBy : null)
        );
    }
}
