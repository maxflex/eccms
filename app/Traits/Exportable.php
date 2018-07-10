<?php

namespace App\Traits;

use Schema;
use Excel;
use DB;


/**
 *
 * using Exportable trait obliges defining $selects_on_export field in classes.
 *
*/
trait Exportable
{
    public static function getExportableFields()
    {
        return array_values(
                    array_merge(
                        array_diff(
                            collect(Schema::getColumnListing((new static)->getTable()))->sort()->all(),
                            isset(static::$hidden_on_export) ? static::$hidden_on_export : []
                        ),
                        isset(static::$with_comma_on_export) ? static::$with_comma_on_export : []
                    )
        );
    }

    /**
     * Экспорт данных в excel файл
     *
     */
    public static function export($request) {
        $table_name = (new static)->getTable();
        return Excel::create($table_name . '_' . date('Y-m-d_H-i-s'), function($excel) use ($request, $table_name) {
            $excel->sheet($table_name, function($sheet) use ($request, $table_name) {
                $groups_table_name = mb_strimwidth($table_name, 0, strlen($table_name) - 1) . '_groups'; // variables => variable_groups

                $export_fields = explode(',', $request->fields);
                array_unshift($export_fields, 'id');
                $export_fields_prefixed = array_map(function($field) use ($table_name) {
                    return self::tableField($table_name, $field);
                }, $export_fields);

                $data = DB::table($table_name)
                    ->select($export_fields_prefixed)
                    ->whereNull('deleted_at')
                    ->join($groups_table_name, $groups_table_name . '.id', '=', $table_name . '.group_id')
                    ->orderBy(\DB::raw($groups_table_name . '.position, ' . $table_name . '.position'))
                    ->get();

                $exportData = [];
                foreach($data as $index => $d) {
                    foreach($d as $field => $value) {
                        if (in_array($field, static::$long_fields)) {
                            $exportData[$index][$field] = strlen($value);
                        } else {
                            $exportData[$index][$field] = $value;
                        }
                    }
                }

                $sheet->fromArray($exportData, null, 'A1', true);
            });
        })->download('xls');
    }

    /**
     * Импорт данных из excel файла
     *
     */
    public static function import($request) {
        if ($request->hasFile('imported_file')) {
            Excel::load($request->file('imported_file'), function($reader){
                foreach ($reader->all()->toArray() as $model) {
                    if (isset(static::$long_fields)) {
                        foreach (static::$long_fields as $field) {
                            unset($model[$field]);
                        }
                    }

                    if (isset(static::$with_comma_on_export) && $elem = static::find($model['id'])) {
                        foreach (static::$with_comma_on_export as $field) {
                            if (array_key_exists($field, $model)) {
                                $model[$field] && $elem->$field()->sync(explode(',', str_replace('.', ',', $model[$field]))); // 5,6 => 5.6 fix
                                unset($model[$field]);
                            }
                        }
                    }

                    foreach ($model as $key => $field) { // numbers app fix
                        if (! $key) {
                            unset($model[$key]);
                        }
                    }

                    static::whereId($model['id'])->update($model);
                }
            });
        } else {
            abort(400);
        }
    }

    /**
     * Обратиться к полю таблицы
     */
    public static function tableField($table_name, $field)
    {
        return $table_name . '.' . $field;
    }
}
