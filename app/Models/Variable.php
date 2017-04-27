<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'html',
        'desc',
        'group_id',
        'position'
    ];

    protected static function boot()
    {
        parent::boot();

        // @todo: присвоение группы перенести в интерфейс
        static::creating(function($model) {
            if (! isset($model->group_id)) {
                $model->group_id = VariableGroup::orderBy('position', 'desc')->value('id');
            }

            $model->position = static::where('group_id', $model->group_id)->max('position') + 1;
        });
    }
}
