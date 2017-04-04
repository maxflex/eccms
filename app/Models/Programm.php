<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programm extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'content',
    ];

    protected $casts = [
        'content' => 'collection',
    ];
}
