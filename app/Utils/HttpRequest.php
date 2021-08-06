<?php

namespace App\Utils;

use Illuminate\Support\Facades\Http;

class HttpRequest {
    Public function SendHttpGetRequest(string $url, array $query = null){
        return Http::get($url, $query);
    }
}
