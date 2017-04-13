<?php
namespace App\Models\Service;
use GuzzleHttp\Client;

class Api
{
    /**
     * Отправить запрос.
     *
     */
    public static function exec($function, $data = [])
    {
        $client = new GuzzleHttp\Client();
        // Добавляем API_KEY к запросу
        $data["API_KEY"] = env("API_KEY");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env("API_URL") . $function);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        print_r($server_output);
        return json_decode($server_output);
    }
}
