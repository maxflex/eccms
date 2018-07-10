<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCredentials;

class Background extends Model
{
    use HasCredentials;

    const UPLOAD_DIR = '/img/wallpaper/';

    protected $connection = 'egerep';

    protected $appends = [
        'image_url',
        'credentials'
    ];

    protected $attributes = [
        'status' => 0
    ];

    public function getImageUrlAttribute()
    {
        return config('app.egerep-url') . self::UPLOAD_DIR . $this->image;
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 1);
    }
}
