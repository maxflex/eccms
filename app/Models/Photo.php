<?php

namespace App\Models;

use Illuminate\Contracts\Auth\StatefulGuard;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    const UPLOAD_DIR = 'images/';
    const THUMB_PREFIX = 'thumb_';

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
        return \Storage::url(static::UPLOAD_DIR . $this->filename);
    }

    protected static function boot()
    {
        static::creating(function($model) {
            $model->position = static::max('position') + 1;
        });
    }
}
