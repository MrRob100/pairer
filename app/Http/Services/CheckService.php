<?php

// declare(strict_types=1);

namespace App\Services;

use App\Models\Trigger;
use App\Services\OrderService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class CheckService
 * @package App\Services
 */
class CheckService
{
    /**
     * Checks DB for orders to be made
     * Logic:
     * if 'when' ~= now then make order
     */
    public function checkDB()
    {
        $trigger = new Trigger;

        $nowish = date('h');

        $to_action = $trigger->where("when", "=", $nowish)->get();

        $order_service = App::make('App\Services\OrderService');

        $order_service->action($to_action);

        return 0;
    }
}
