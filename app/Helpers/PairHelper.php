<?php

namespace App\Helpers;

class PairHelper
{
    public $pures = ['BTC', 'ETH', 'BNB'];

    public function isPure($symbol1, $symbol2) {
        return in_array($symbol1, $this->pures) || in_array($symbol2, $this->pures);
    }
}
