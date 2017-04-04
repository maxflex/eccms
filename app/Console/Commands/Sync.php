<?php

namespace App\Console\Commands;

use DB;
use App\Models\Service\Api;
use App\Models\Variable;
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
    protected $signature = 'vars {cmd}';

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
        $this->{$this->argument('cmd')}();
    }

    public function push()
    {
        $this->info('Pushing variables to server...');
        Api::exec('variables/push', [
            'variables' => Variable::all()->toArray(),
            'groups'    => VariableGroup::all()->toArray(),
        ]);
    }

    public function pull()
    {
        $this->info('Pulling variables from server...');
        list($variables, $groups) = Api::exec('variables/pull');
        Schema::disableForeignKeyConstraints();
        DB::table('variables')->truncate();
        DB::table('variable_groups')->truncate();
        Schema::enableForeignKeyConstraints();
        foreach ($groups as $group) {
            DB::table('variable_groups')->insert((array)$group);
        }
        foreach ($variables as $var) {
            DB::table('variables')->insert((array)$var);
        }
    }
}
