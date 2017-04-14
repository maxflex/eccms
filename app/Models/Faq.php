<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'question',
        'answer',
        'group_id',
        'position'
    ];

    protected static function boot()
    {
        // @todo: присвоение группы перенести в интерфейс
        static::creating(function($model) {
            $model->group_id = FaqGroup::orderBy('position', 'desc')->value('id');
        });
    }
}
