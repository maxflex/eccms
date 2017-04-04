<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteUrl extends Command
{
    const HOURLY_LIMIT = 14;

    private $debug = true;

    private $ch = null;
    private $keys= [];
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete url from yandex index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_POST,1);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($this->ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->hasUrlToDelete()) {
            $this->login();
            $this->getKeys();
            $this->deleteUrls();
        }
    }

    private function login()
    {
        $loginUrl    = 'https://passport.yandex.ru/auth';
        $loginParams = [
            "login" => "shamshod.zhamolov",
            "passwd" => "zulayxo21"
        ];

        curl_setopt($this->ch, CURLOPT_URL, $loginUrl);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($loginParams));
        if (curl_exec($this->ch) === false) {
            $this->log('err: cant init curl');
        }
    }

    private function getKeys()
    {
        $dataUrl = 'https://webmaster.yandex.ru/site/https:ege-repetitor.ru:443/tools/del-url/';
        curl_setopt($this->ch, CURLOPT_URL, $dataUrl);
        $page = curl_exec($this->ch);
        if (preg_match('/balancerRequestId.{3}(\d+\-\d+)/', $page, $matches)) {
            $this->keys['b'] = $matches[1];
        }

        if (preg_match('/sk=([a-z0-9]+)"/', $page, $matches)) {
            $this->keys['crc'] = $matches[1];
        }

        if (count($this->keys) != 2) {
            $this->log('err: cant extract keys');
        } else {
            $this->log('used keys: ' . $this->keys['b'] . '::' . $this->keys['crc']);
        }
    }

    private function deleteUrls()
    {
        curl_setopt($this->ch, CURLOPT_URL, 'https://webmaster.yandex.ru/gate/del-url/del-url/');
        $params = [
            "params" => [
                "hostId" => "https:ege-repetitor.ru:443",
                "balancerParentRequestId" => $this->keys['b'],
            ],
            "crc" => $this->keys['crc']
        ];

        $urls_to_delete = \DB::table('temp')->where('deleted', false)->take(static::HOURLY_LIMIT)->get();
        foreach ($urls_to_delete as $url_to_delete) {
            $params['params']['url'] = $url_to_delete->url;

            curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($params));
            if (($json_response = curl_exec($this->ch)) === false) {
                $this->log('err: cant request deletion');
            } else {
                $response = json_decode($json_response);
                if (isset($response->error)) {
                    $this->log('err: ' . $json_response);
                } else {
                    $state = $response->response->response->delurlRequest->state;
                    if ($state == 'ACCEPTED') {
                        \DB::table('temp')->where('url', $url_to_delete->url)->update(['deleted' => true]);
                        $this->log($url_to_delete->url . ' ...... ' . $state);
                    }
                }
            }
        }
    }

    private function log($msg)
    {
        if ($this->debug) {
            \Storage::append('yandex.log', date('[d-m-Y H:i:s] ') . $msg);
        }
    }

    private function hasUrlToDelete()
    {
        return \DB::table('temp')->where('deleted', false)->count();
    }
}
