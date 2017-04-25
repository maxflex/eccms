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
        $this->syncGroup('variable_groups');
        $this->syncGroup('page_groups');
        $this->sync(Variable::class, 'name', 'html');
        $this->sync(Variable::class, 'name', 'html_mobile');
        $this->sync(Page::class, 'keyphrase', 'html');
    }

    private function sync($class, $id, $content)
    {
        $class_plural = explode("\\", $class);
        $class_plural = end($class_plural) . 's';
        $table = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class_plural)); // CamelCase to snake_case
        $class_plural = strtolower($class_plural);

        $this->line("\n\tSYNCING " . strtoupper($class_plural) . "\n");
        $server = Api::get("sync/getData/{$table}");
        $local = DB::table($table)->get();
        foreach($server as $s) {
            // пытаемся найти такую переменную на локалхосте
            $l = $local->where($id, $s->{$id})->first();

            // если переменная не найдена на локалхосте, добавляем её
            if ($l === null) {
                $this->info("Adding «" . $s->{$id} . "»...");
                $class::create((array)$s);
            } else {
                // если переменная найдена, проверяем на различие
                if ($this->diff($l->{$content}, $s->{$content}, $s->{$id}) == 'server') {
                    $class::where($id, $l->{$id})->first()->update((array)$s);
                }
            }
        }

        $this->info("\nPushing to server...\n");
        Api::post("sync/setData/{$table}", [
            'form_params' => DB::table($table)->get()->all()
        ]);

        $this->info("\tOK");
    }

    private function diff($local, $server, $name)
    {
        if (md5($local) !== md5($server)) {
            $this->error("Server «{$name}» differs from local");
            $local_lines = explode("\n", $local);
            $server_lines = explode("\n", $server);
            $differences = 0;
            foreach(range(0, count($local_lines) - 1) as $index) {
                if (@$local_lines[$index] != @$server_lines[$index]) {
                    $differences++;
                    $this->error("Line " . ($index + 1));
                    $this->error("Local: " . @$local_lines[$index]);
                    $this->error("Server: " . @$server_lines[$index] . "\n");
                }
                // если много различий, не засорять консоль
                if ($differences > 5) {
                    $this->error("etc...");
                    break;
                }
            }
            $choice = $this->choice('Choose action', ['server', 'local', 'abort'], 1);
            if ($choice == 'abort') {
                exit();
            }
            return $choice;
        }
        return false;
    }

    private function syncGroup($table)
    {
        forceTruncate($table);
        $this->info('Syncing «' . $table . '»...');
        $groups = Api::get('sync/getData/' . $table);
        if (count($groups)) {
            foreach ($groups as $group) {
                DB::table($table)->insert((array)$group);
            }
        }
    }
}
