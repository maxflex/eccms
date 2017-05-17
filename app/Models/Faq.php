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
        parent::boot();

        // @todo: присвоение группы перенести в интерфейс
        static::creating(function($model) {
            if (! isset($model->group_id)) {
                $model->group_id = FaqGroup::orderBy('position', 'desc')->value('id');
            }

            $model->position = static::where('group_id', $model->group_id)->max('position') + 1;
        });
    }
}
