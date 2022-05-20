<?php

namespace App\Helpers;

class PairHelper
{
    public const PURES = ['BTC', 'ETH', 'BNB'];

    public function isPure($symbol1, $symbol2) {
        return in_array($symbol1, $this::PURES) || in_array($symbol2, $this::PURES);
    }
}
