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
        'position',
        'sort',
        'place',
        'subjects',
        'station_id',
        'seo_desktop',
        'seo_mobile',
        'variable_id',
        'hidden_filter',
        'useful'
    ];

    protected static $hidden_on_export = [
        'id',
        'position',
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
        'sort' => 1,
        'place' => 1
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

    private static function _getNextPosition()
    {
        return DB::table('pages')->max('position') + 1;
    }

    protected static function boot()
    {
        static::creating(function($model) {
            $model->position = static::_getNextPosition();
        });
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

/**
 * Функция поиска БД
 *
 DROP FUNCTION IF EXISTS onlysymbols;
 DELIMITER $$
 CREATE FUNCTION `onlysymbols`( str TEXT ) RETURNS TEXT CHARSET utf8
 BEGIN
   DECLARE i, len SMALLINT DEFAULT 1;
   DECLARE ret TEXT DEFAULT '';
   DECLARE c CHAR(1);
   SET str = LOWER(REPLACE(str, ' ', ''));
   SET len = CHAR_LENGTH( str );
   REPEAT
     BEGIN
       SET c = MID( str, i, 1 );
       IF c REGEXP '[а-я]+' THEN
         SET ret=CONCAT(ret,c);
       ELSEIF  c = ' ' THEN
           SET ret=CONCAT(ret," ");
       END IF;
       SET i = i + 1;
     END;
   UNTIL i > len END REPEAT;
   SET ret = lower(ret);
   RETURN ret;
   END $$
   DELIMITER ;
 */
