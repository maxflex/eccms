<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    const UPLOAD_DIR = 'images/';

    public $timestamps = false;

    protected $fillable = [
        'filename',
        'title'
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
        return env('EC_CMS_STORAGE') . $this->filename;
    }

    public function getThumbUrlAttribute()
    {
        return env('EC_CMS_STORAGE') . '' . $this->filename;
    }
}