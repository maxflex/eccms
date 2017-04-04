<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    protected $fillable = [
        'name',
        'html',
        'desc',
        'group_id'
    ];

    public static function getLight()
    {
        return self::select('id', 'name')->get();
    }
}
