<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $data = \DB::table('pages')->select('id', 'html')->get();

        foreach($data as $d) {
            $html = $d->html;
            $html = str_replace('<H2>', '<h2>', $html);
            $html = str_replace('<il>', '<li>', $html);
            $html = str_replace('<li.>', '<li>', $html);
            $html = str_replace('< il >', '<li>', $html);

            $html = str_replace('</H2>', '</h2>', $html);
            $html = str_replace('</il>', '</li>', $html);
            $html = str_replace('</lil>', '</li>', $html);
            \DB::table('pages')->whereId($d->id)->update([
                'html' => $html
            ]);
        }
    }
}
