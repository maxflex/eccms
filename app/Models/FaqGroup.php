<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqGroup extends Model
{
    protected $fillable = ['title', 'position'];

    public $timestamps = false;

    const DEFAULT_TITLE = 'Новая группа';

    public static function getIds()
    {
        $groups = self::get();
        $groups = $groups->orderBy('position', 'asc')->all();
        $groups[] = (object)[
            'id'    => null,
            'title' => 'Остальные',
        ];
        foreach($groups as $group) {
            $group->data = Faq::where('group_id', $group->id)->orderBy('position', 'asc')->get();
        }
        return json_encode($groups);
    }

    public static function boot()
    {
        static::creating(function($model) {
            $model->title = self::DEFAULT_TITLE;
        });
    }
}
