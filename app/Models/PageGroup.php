<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageGroup extends Model
{
    protected $fillable = ['title'];

    public $timestamps = false;

    const DEFAULT_TITLE = 'Новая группа';

    public static function getIds()
    {
        $groups = self::get();
        $groups[] = [
            'id'    => null,
            'title' => 'Остальные'
        ];
        return $groups;
    }

    public static function boot()
    {
        static::creating(function($model) {
            $model->title = self::DEFAULT_TITLE;
        });
    }
}
