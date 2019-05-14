<?php

namespace App\Console\Commands;

use DB;
use App\Models\Variable;
use App\Models\Page;
use App\Models\VariableGroup;
use Illuminate\Console\Command;


class CleanVariables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-variables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean unused variables';

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
        $this->line('Cleaning variables...');
        $variables = DB::table('variables')
            ->whereNotIn('name', ['faq-image'])
            ->get()
            ->all();

        // какие переменные удалить?
        $varsToDelete = [];
        foreach($variables as $variable) {
            $usedInVars = Variable::whereRaw($this->getCondition('html', $variable))->exists();
            $usedInPages = Page::query()
                ->where($this->getCondition('html', $variable))
                ->orWhere($this->getCondition('html_af', $variable))
                ->orWhere($this->getCondition('html_mobile', $variable))
                ->orWhere($this->getCondition('html_mobile_af', $variable))
                ->exists();
            if (! $usedInVars && ! $usedInPages) {
                $varsToDelete[] = $variable->id;
                $this->warn($variable->name . ' is not used');
            }
        }

        if ($this->confirm('Delete ' . count($varsToDelete) . ' variables?')) {
            DB::table('variables')->whereIn('id', $varsToDelete)->delete();
            $this->info(count($varsToDelete) . ' variables deleted');
        }

        $this->line('Cleaning variable groups...');
        // Clean unused variable groups
        $variableGroups = VariableGroup::all();

        foreach($variableGroups as $variableGroup) {
            if (! $variableGroup->variable()->exists()) {
                $this->error($variableGroup->title . ' deleted');
                $variableGroup->delete();
            }
        }
    }
    
    private function getCondition($field, $variable)
    {
        return "({$field} like '%[{$variable->name}|%' or {$field} = '[{$variable->name}]')";
    }
}
