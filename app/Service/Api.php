<?php
namespace App\Service;
use GuzzleHttp\Client;

class Api
{
    private static function client()
    {
        return new Client([
            'base_uri' => config('app.api-url'),
        ]);
    }

    public static function __callStatic($func, $args)
    {
        return json_decode(self::client()->{$func}(...$args)->getBody()->getContents());
    }
}
