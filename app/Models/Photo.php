<?php

namespace App\Models;

use Illuminate\Contracts\Auth\StatefulGuard;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    const UPLOAD_DIR = 'images/';

    const THUMB_WIDTH = 300;
    const THUMB_ROUTE = 'resize';
    const THUMB_FILTER = 'small';

    public $timestamps = false;

    protected $fillable = [
        'filename',
        'group_id',
        'position'
    ];

    protected $appends = [
        'url',
        'thumbUrl',
        'info'
    ];

    public function scopeGallery($query, $args)
    {
        if ($args != 'all') {
            $query->whereIn('id', explode(',', $args));
        }
        return $query;
    }

    public function getUrlAttribute()
    {
        return \Storage::url(static::UPLOAD_DIR . $this->filename);
    }

    public function getThumbUrlAttribute()
    {
        return static::THUMB_ROUTE . '/' . static::THUMB_FILTER . '/' . $this->filename;
    }

    public function getInfoAttribute()
    {
        $sizes = [];
        if ($this->id) {
            $dimensions = getimagesize(public_path() . '/storage/' . static::UPLOAD_DIR . $this->filename);
            $sizes = [
                'width'     => $dimensions[0],
                'height'    => $dimensions[1],
                'size'      => \Storage::disk('public')->size(static::UPLOAD_DIR . $this->filename),
            ];
        }
        return $sizes;
    }

    protected static function boot()
    {
        static::creating(function($model) {
            if (! isset($model->group_id)) {
                $model->group_id = PhotoGroup::orderBy('position', 'desc')->value('id');
            }

            $model->position = static::where('group_id', $model->group_id)->max('position') + 1;
        });
    }
}
