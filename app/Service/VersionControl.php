<?php

namespace App\Service;

use DB;
use Schema;

class VersionControl
{
    // какие таблицы нужно синхронизировать
    const TABLES = ['variable_groups', 'page_groups', 'variables', 'pages'];

    // название таблицы в БД
    const TABLE = 'version_control';

    // поля без контроля версий
    const EXCLUDE = ['id', 'created_at', 'updated_at'];

    public static function generate()
    {
        DB::table(self::TABLE)->truncate();
        foreach(self::TABLES as $table) {
            $data = DB::table($table)->get();
            $columns = Schema::getColumnListing($table);
            foreach($data as $d) {
                $entity_id = $d->id;
                foreach(array_diff($columns, self::EXCLUDE) as $column) {
                    $md5 = md5($d->{$column});
                    DB::table(self::TABLE)->insert(compact('table', 'column', 'entity_id', 'md5'));
                }
            }
        }
    }

    public static function get($table, $entity_id)
    {
        $data = DB::table(self::TABLE)->where('table', $table)->where('entity_id', $entity_id)
            ->select('column', 'md5')->get();
        $return = [];
        foreach($data as $d) {
            $return[$d->column] = $d->md5;
        }
        return (count($return) ? (object)$return : null);
    }
}
