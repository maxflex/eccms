<?php
namespace App\Service;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class ImageResize implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        return $image->widen(\App\Models\Photo::THUMB_WIDTH);
    }
}
