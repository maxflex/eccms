<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhotoGroup extends Model
{
    protected $fillable = ['title', 'position'];

    public $timestamps = false;

    const DEFAULT_TITLE = 'Новая группа';

    public function photo()
    {
        return $this->hasMany(Photo::class, 'group_id')->orderBy('position');
    }

    public static function get()
    {
        return self::with('photo')->orderBy('position')->get()->all();
    }

    public static function boot()
    {
        static::creating(function($model) {
            $model->title = self::DEFAULT_TITLE;
            $model->position = static::max('position') + 1;
        });
    }
}
