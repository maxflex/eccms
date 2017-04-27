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
                $local = DB::table($table)->whereId($server->id)->get();
                // если запись не найдена
                if ($local === null) {

                } else {
                // если запись найдена, проверяем по каждому полю
                // @todo: сначала проверить целостно, и если равны – пропускать
                    foreach(Schema::getColumnListing($table) as $column) {
                        $local_md5 = md5($local->{$column});
                        $server_md5 = md5($server->{$column});
                        // если поля не равны
                        if ($local_md5 != $server_md5) {
                            // решаем, какую версию оставить
                            // хеш на сервере хранит последнюю версию localhost

                            // изменилось на локалхосте
                            $local_changed = $local_md5 != $server->previous_md5[$column];

                            // изменилось на сервере
                            $server_changed = $server_md5 != $server->previous_md5[$column];

                            // если изменилось в обеих системах
                            if ($local_changed && $server_changed) {

                            } else {
                                // если изменилось на локалхосте
                                if ($local_changed) {

                                }

                                // если изменилось на сервере
                                if ($server_changed) {
                                    
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
