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
use Storage;
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
        foreach(['pages', 'variables'] as $table) {
            $server_data = Api::get("sync/getData/{$table}");
            foreach($server_data as $server) {
                $local = DB::table($table)->whereId($server->id)->get()->first();
                // если запись не найдена
                if ($local === null) {
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
                        // если поля не равны
                        if ($local_md5 != $server_md5) {
                            // решаем, какую версию оставить
                            // хеш на сервере хранит последнюю версию localhost

                            // изменилось на локалхосте
                            $local_changed = $local_md5 != $server->previous_md5->{$column};

                            // изменилось на сервере
                            $server_changed = $server_md5 != $server->previous_md5->{$column};

                            // если изменилось в обеих системах
                            if ($local_changed && $server_changed) {
                                $this->error("$table {$local->id} $column changed on both");
                            } else {
                                // если изменилось на локалхосте, то не делать ничего
                                if ($local_changed) {
                                    $this->info("$table {$local->id} $column changed locally");
                                }

                                // если изменилось на сервере
                                if ($server_changed) {
                                    $this->info("$table {$local->id} $column changed on remotely");
                                    DB::table($table)->whereId($local->id)->update([
                                        $column => $server->{$column},
                                    ]);
                                }
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
    }
}
