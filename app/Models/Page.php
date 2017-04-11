<?php

namespace App\Models;

use App\Traits\Exportable;
use DB;
use Schema;
use Shared\Model;

class Page extends Model
{
   use Exportable;
   protected $commaSeparated = ['subjects'];
   protected $fillable = [
        'keyphrase',
        'url',
        'title',
        'keywords',
        'desc',
        'published',
        'h1',
        'h1_bottom',
        'html',
        'seo_desktop',
        'seo_mobile',
        'variable_id',
        'useful',
        'group_id'
    ];

    protected static $hidden_on_export = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected static $selects_on_export = [
        'id',
        'keyphrase',
    ];

    protected static $long_fields = [
        'html'
    ];

    protected $attributes = [
        'seo_desktop' => 0,
        'seo_mobile' => 0,
    ];

    public function useful()
    {
        return $this->hasMany(PageUseful::class);
    }

    public function setUsefulAttribute($value)
    {
        $this->useful()->delete();
        foreach($value as $v) {
            if ($v['page_id_field']) {
                $this->useful()->create($v);
            }
        }
    }

    public function setVariableIdAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['variable_id'] = null;
        } else {
            $this->attributes['variable_id'] = $value;
        }
    }

    public static function search($search)
    {
        $query = static::query();

        // поиск по текстовым полям
        foreach(['keyphrase', 'url', 'title', 'h1', 'h1_bottom', 'keywords', 'desc', 'hidden_filter'] as $text_field) {
            if (isset($search->{$text_field}) && ! empty($search->{$text_field})) {
                $query->where($text_field, 'like', '%' . $search->{$text_field} . '%');
            }
        }

        // поиск по textarea-полям
        foreach(['html'] as $text_field) {
            if (isset($search->{$text_field}) && ! empty($search->{$text_field})) {
                $query->whereRaw("onlysymbols({$text_field}) like CONCAT('%', CONVERT(onlysymbols('" . $search->{$text_field} . "') USING utf8) COLLATE utf8_bin, '%')");
            }
        }

        // поиск по цифровым полям
        foreach(['seo_desktop', 'seo_mobile', 'station_id', 'sort', 'place', 'published'] as $numeric_field) {
            if (isset($search->{$numeric_field})) {
                $query->where($numeric_field, $search->{$numeric_field});
            }
        }

        if (isset($search->subjects)) {
            foreach($search->subjects as $subject_id) {
                $query->whereRaw("FIND_IN_SET('{$subject_id}', subjects)");
            }
        }

        return $query;
    }
}
