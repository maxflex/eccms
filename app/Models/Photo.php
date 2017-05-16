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
        'position'
    ];

    protected $appends = [
        'url',
        'thumbUrl'
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

    protected static function boot()
    {
        static::creating(function($model) {
            $model->position = static::max('position') + 1;
        });
    }
}
