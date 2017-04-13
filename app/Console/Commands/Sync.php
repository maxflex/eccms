<?php

namespace App\Console\Commands;

use App\Models\PageUseful;
use DB;
use App\Models\Service\Api;
use App\Models\Variable;
use App\Models\VariableGroup;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

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
        // $this->{$this->argument('cmd')}();
        $this->line("\n\tSYNCING VARIABLES\n");
        $server_variables = Api::exec('sync/getData/variables');
        $local_variables = DB::table('variables')->get();
        foreach($server_variables as $server_variable) {
            // пытаемся найти такую переменную на локалхосте
            $local_variable = $local_variables->where('name', $server_variable->name)->first();

            // если переменная найдена, проверяем на различие
            if ($local_variable !== null) {
                // если переменные отличаются, добавляем в массив отличий
                if (md5($local_variable) !== md5($server_variable)) {
                    $this->error("Server variable {$local_variable->name} differs from local");
                }
            }
        }
    }

    public function push()
    {
        $this->info('pushing db data ...');
        Api::exec('variables/push', [
            'variables' => Variable::all()->toArray(),
            'groups'    => VariableGroup::all()->toArray(),
            'pages'     => Page::all()->toArray(),
            'useful'    => PageUseful::all()->toArray()
        ]);
    }

    public function pull()
    {
        $this->info('pulling db data ...');
        list($variables, $groups, $pages, $useful) = Api::exec('variables/pull');
        Schema::disableForeignKeyConstraints();
        DB::table('variables')->truncate();
        DB::table('variable_groups')->truncate();
        DB::table('pages')->truncate();
        DB::table('page_useful')->truncate();
        Schema::enableForeignKeyConstraints();
        if (count($groups)) {
            foreach ($groups as $group) {
                DB::table('variable_groups')->insert((array)$group);
            }
        }

        if (count($variables)) {
            foreach ($variables as $var) {
                DB::table('variables')->insert((array)$var);
            }
        }

        if (count($pages)) {
            foreach ($pages as $var) {
                DB::table('pages')->insert((array)$var);
            }
        }
        if (count($useful)) {
            foreach ($useful as $var) {
                DB::table('page_useful')->insert((array)$var);
            }
        }
    }
}
