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
            $server_data = Api::get("sync/getData/{$table}");
            foreach($server_data as $server) {
                $local = DB::table($table)->whereId($server->id)->get()->first();
                $local->previous_md5 = VersionControl::get($table, $local->id);
                // если запись не найдена
                if ($local === null) {
                    $this->info("Adding $table " . $server->id);
                    DB::table($table)->insert((array)$server);
                } else {
                    // если запись найдена, проверяем по каждому полю
                    // сначала проверить целостно, и если равны – пропускать
                    if (md5(json_encode($local)) == md5(json_encode($server))) {
                        continue;
                    }
                    // проверяем различия по колонкам
                    foreach(array_diff(Schema::getColumnListing($table), VersionControl::EXCLUDE) as $column) {
                        $local_md5 = md5($local->{$column});
                        $server_md5 = md5($server->{$column});

                        // проверяем последние синхронизированные версии
                        if ($local->previous_md5->{$column} == $server->previous_md5->{$column}) {
                            // если последние синхронизированные версии равны

                            // изменилось на локалхосте
                            $local_changed = $local_md5 != $local->previous_md5->{$column};

                            // изменилось на сервере
                            $server_changed = $server_md5 != $server->previous_md5->{$column};

                            if ($local_changed) {
                                if ($server_changed) {
                                    $this->error("SKIP: $table {$local->id} $column");
                                } else {
                                    $this->info("$table {$local->id} $column changed locally");
                                }
                            } else {
                                if ($server_changed) {
                                    $this->info("$table {$local->id} $column changed on remotely");
                                    DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                }
                            }
                        } else {
                            // если последние синхронизированные версии не равны, то проверяем изменился ли локалхост
                            // если локалхост не изменился, то всегда подтягиваем версию с продакшн
                            if ($local_md5 == $local->previous_md5->{$column}) {
                                DB::table($table)->whereId($local->id)->update([$column => $server->{$column}]);
                                $this->info("$table {$local->id} $column changed on remotely (2)");
                            } else {
                                $this->error("SKIP (2): $table {$local->id} $column");
                            }
                        }
                    }
                }
            }
            Api::post("sync/setData/{$table}", [
               'form_params' => DB::table($table)->get()->all()
           ]);
        }
        shell_exec('envoy run generate:version_control');
        shell_exec('php artisan generate:version_control');
    }
}
