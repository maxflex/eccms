<?php

namespace App\Console\Commands;

use App\Models\PageUseful;
use DB;
use App\Service\Api;
use App\Models\Variable;
use App\Models\VariableGroup;
use App\Models\PageGroup;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Artisan;
use App\Service\VersionControl;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'syncs local records with remote db';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach(VersionControl::TABLES as $table) {
            $this->line("\n************** " . strtoupper($table) . " ************** \n");
            $server_data = Api::get("sync/get/{$table}");

            // какие данные на продакшене ОБНОВИТЬ
            $production_update_data = [];

            // если добавилось в обеих местах с одинаковым ID
            $same_ids = [];

            foreach($server_data as $server) {
                $local = DB::table($table)->whereId($server->id)->get()->first();

                // если запись найдена
                if ($local !== null) {
                    $local->previous_md5 = VersionControl::get($table, $local->id);


                    // если local->previous_md5 не установлен, значит,
                    // локально это новодобавленная страница и на сервере она тоже создана
                    //
                    //
                    // (previous_md5 === null) = новодобавленная страница
                    if ($local->previous_md5 === null) {
                        // полностью передобавляем страницы
                        $same_ids[] = (object)[
                            'server' => clone $server,
                            'local'  => clone $local,
                        ];
                        continue;
                    }

                    /* проверяем по каждому полю */

                    // сначала проверить целостно, и если равны – пропускать
                    if (md5(json_encode($local)) == md5(json_encode($server))) {
                        continue;
                    }
                    // проверяем различия по колонкам
                    foreach(array_diff(Schema::getColumnListing($table), VersionControl::EXCLUDE) as $column) {
                        // if ($column == 'seo_text') {
                        //     continue;
                        // }

                        $local_md5 = md5($local->{$column});
                        $server_md5 = md5($server->{$column});

                        if (! isset($local->previous_md5->{$column})) {
                            $this->error("Local $table {$local->id} $column not set");
                            exit();
                        }

                        if (! isset($server->previous_md5->{$column})) {
                            $this->error("Server $table {$server->id} $column not set");
                            exit();
                        }

                        // проверяем последние синхронизированные версии
                        if ($local->previous_md5->{$column} == $server->previous_md5->{$column}) {
                            // если последние синхронизированные версии равны

                            // изменилось на локалхосте
                            $local_changed = $local_md5 != $local->previous_md5->{$column};

                            // изменилось на сервере
                            $server_changed = $server_md5 != $server->previous_md5->{$column};

                            if ($local_changed) {
                                if ($server_changed) {
                                    $this->error("$table {$local->id} $column");
                                    switch ($this->diff($local->{$column}, $server->{$column})) {
                                        case 'local':
                                            $production_update_data[$local->id][$column] = $local->{$column};
                                            break;
                                        case 'server':
                                            DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                            break;
                                    }
                                } else {
                                    $production_update_data[$local->id][$column] = $local->{$column};
                                    $this->info("$table {$local->id} $column changed locally");
                                }
                            } else {
                                if ($server_changed) {
                                    $this->info("$table {$local->id} $column changed remotely");
                                    DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                }
                            }
                        } else {
                            // если последние синхронизированные версии не равны, то проверяем изменился ли локалхост
                            // если локалхост не изменился, то всегда подтягиваем версию с продакшн
                            if ($local_md5 == $local->previous_md5->{$column}) {
                                DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                $this->info("$table {$local->id} $column changed remotely (2)");
                            } else {
                                $this->error("$table {$local->id} $column");
                                switch ($this->diff($local->{$column}, $server->{$column})) {
                                    case 'local':
                                        $production_update_data[$local->id][$column] = $local->{$column};
                                        break;
                                    case 'server':
                                        DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                        break;
                                }
                            }
                        }
                    }
                }
            }

            // Обновить данные полей на production
            $this->productionUpdateData($table, $production_update_data);

            /**
             * Добавление страниц
             */
            $this->syncNewRecords($table, $server_data);

            /**
             * Передобавление новых записей с одинаковыми ID
             */
            if (count($same_ids)) {
                $this->line("\n************** RE-ADDING RECORDS WITH SAME IDS ************** \n");
                foreach($same_ids as $data) {
                    // удаление на сервере/локалхосте
                    $production_update_data[$data->local->id] = [
                        'deleted_at' => now(),
                        'url' => uniqid(),
                    ];
                    DB::table($table)->whereId($data->local->id)->delete();

                    // удаляем из server_data тоже, иначе будет пытаться добавить на локалхост в syncNewRecords
                    $server_data = array_filter($server_data, function($e) use ($data) {
                        return $e->id != $data->local->id;
                    });

                    // удаляем ненужные для добавления поля в обоих
                    foreach(['local', 'server'] as $location) {
                        foreach(['id', 'previous_md5'] as $field) {
                            unset($data->{$location}->{$field});
                        }
                    }

                    // Передобавление
                    // достаточно добавить данные на локалхост, тогда на сервер
                    // данные передобавятся автоматически потому что
                    // $local_ids = DB::table($table)->pluck('id')->all()
                    DB::table($table)->insert((array)$data->local);
                    DB::table($table)->insert((array)$data->server);
                }
                $this->productionUpdateData($table, $production_update_data);
                $this->syncNewRecords($table, $server_data);
            }
        }
        $this->line("\n************** RE-GENERATE PRODUCTION TABLE ************** \n");
        shell_exec('envoy run generate:version_control');

        $this->line("\n************** RE-GENERATE LOCALHOST TABLE ************** \n");
        shell_exec('php artisan generate:version_control');
    }


    public function diff($local, $server)
    {
        $server = explode("\n", $server);
        $local = explode("\n", $local);
        $length = max(count($server), count($local));

        foreach(range(0, $length) as $i) {
            if (@$server[$i] != @$local[$i]) {
                $this->error("Local: " . @$local[$i]);
                $this->error("Server: " . @$server[$i]);
                return $this->choice('Choose version', ['local', 'server']);
            }
        }
    }

    /**
     * Обновить данные полей на production
     */
    private function productionUpdateData($table, $production_update_data)
    {
        if (count($production_update_data)) {
            Api::post("sync/update/{$table}", [
                'form_params' => $production_update_data
            ]);
        }
    }

    /**
     * Синхронизировать новые записи
     */
    private function syncNewRecords($table, $server_data)
    {
        $server_data_collection = collect($server_data);
        $server_ids = $server_data_collection->pluck('id')->all();
        $local_ids = DB::table($table)->pluck('id')->all();

        // добавление записей в БД локалхоста
        foreach(array_diff($server_ids, $local_ids) as $id) {
            $this->info("Adding to localhost $table " . $id);
            $data = $server_data_collection->where('id', $id)->first();
            unset($data->previous_md5);
            DB::table($table)->insert((array)$data);
        }

        // добавляем на продакшн новые сущности
        $production_insert_data = [];

        // генерируем массив данных для добавления новых записей на продакшн
        foreach(array_diff($local_ids, $server_ids) as $id) {
            $this->info("Adding to server $table " . $id);
            $production_insert_data[] = DB::table($table)->whereId($id)->first();
        }

        // добавление записей в БД продакшн
        if (count($production_insert_data)) {
            Api::post("sync/insert/{$table}", [
                'form_params' => $production_insert_data
            ]);
        }
    }

}
