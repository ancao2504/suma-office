<?php

namespace App\Helpers\App;
use Illuminate\Support\Facades\Http;

class ApiRequest {
    public static function requestPost($request = null, $header = null, $body = null) {
        $url = trim(config('constants.app.url.api')).'/';
        $results = Http::withHeaders($header)
                    ->post($url.$request, $body)
                    ->body();
        return $results;
    }

    public static function requestGet($request = null, $header = null, $body = null) {
        $url = trim(config('constants.app.url.api')).'/';
        $results = Http::withHeaders($header)
                    ->get($url.$request, $body)
                    ->body();
        return $results;
    }
}
