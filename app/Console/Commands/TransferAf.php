<?php

namespace App\Console\Commands;

use DB;
use App\Models\Page;
use Illuminate\Console\Command;


class TransferAf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer-af';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer from html_af to html';

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
        $this->line('Transfering AF...');

        $pages = DB::table('pages')->get()->all();

        $bar = $this->output->createProgressBar(count($pages));

        foreach($pages as $page) {
            if (! empty(trim($page->html_af))) {
                DB::table('pages')->whereId($page->id)->update([
                    'html' => $page->html_af,
                    'html_af' => ''
                ]);
            }
            if (! empty(trim($page->html_mobile_af))) {
                DB::table('pages')->whereId($page->id)->update([
                    'html_mobile' => $page->html_mobile_af,
                    'html_mobile_af' => '',
                ]);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->line("\n");
    }

}
