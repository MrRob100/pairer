<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class IEXGetService {

    public function apiCall($symbol) {
        if (Cache::has($symbol)) {
            $response = Cache::get($symbol);
        } else {
            $response = array_reverse(json_decode(file_get_contents(
                "https://cloud.iexapis.com/stable/stock/$symbol/batch?types=chart&range=1y&last=350&token=sk_968d86666e1a40849a897c0a459ba87b"
            ), true));

            Cache::put($symbol, $response, 600);
        }

        return $response;
    }
}
