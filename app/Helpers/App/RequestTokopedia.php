<?php

namespace App\Helpers\App;

use Illuminate\Support\Facades\Http;

class RequestTokopedia
{
    // ==========================================================================================
    // API RESPONSE TOKOPEDIA
    // ==========================================================================================
    public static function requestAccount($request, $header, $body) {
        $url = config('constants.app.tokopedia.url.account').'/';
        $results = Http::withHeaders($header)
                    ->post($url.$request, $body)
                    ->body();
        return $results;
    }

    public static function requestPost($request, $header, $body) {
        $url = config('constants.app.tokopedia.url.base_url').'/';
        $results = Http::withHeaders($header)
                    ->post($url.$request, $body)
                    ->body();
        return $results;
    }

    public static function requestPostRaw($request, $header, $body) {
        $url = config('constants.app.tokopedia.url.base_url').'/';
        $results = Http::contentType("text/plain")->bodyFormat('none')
                    ->withHeaders($header)
                    ->post($url.$request,
                    [
                        'body' => $body,
                    ])->body();
        return $results;
    }

    public static function requestGet($request, $header, $body) {
        $url = config('constants.app.tokopedia.url.base_url').'/';
        $results = Http::withHeaders($header)
                    ->get($url.$request, $body)
                    ->body();
        return $results;
    }
}
