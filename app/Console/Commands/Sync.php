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

            // добавление отсутствующих страниц
            foreach($server_data as $server) {
                $local = DB::table($table)->whereId($server->id)->get()->first();

                // если запись найдена
                if ($local !== null) {
                    $local->previous_md5 = VersionControl::get($table, $local->id);

                    // если запись найдена, проверяем по каждому полю
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

            // Обновление данных на продакшн
            if (count($production_update_data)) {
                Api::post("sync/update/{$table}", [
                    'form_params' => $production_update_data
                ]);
            }

            /**
             * Добавление страниц
             */

            $server_data = collect($server_data);
            $server_ids = $server_data->pluck('id')->all();
            $local_ids = DB::table($table)->pluck('id')->all();

            // добавляем на локалхост новые сущности
            foreach(array_diff($server_ids, $local_ids) as $id) {
                $this->info("Adding to localhost $table " . $id);
                $data = $server_data->where('id', $id)->first();
                unset($data->previous_md5);
                DB::table($table)->insert((array)$data);
            }

            // добавляем на продакшн новые сущности
            $production_insert_data = [];
            foreach(array_diff($local_ids, $server_ids) as $id) {
                $this->info("Adding to server $table " . $id);
                $production_insert_data[] = DB::table($table)->whereId($id)->first();
            }

            if (count($production_insert_data)) {
                Api::post("sync/insert/{$table}", [
                    'form_params' => $production_insert_data
                ]);
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
}
