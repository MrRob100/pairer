<?php

// declare(strict_types=1);

namespace App\Services;

use App\Models\Trigger;
use App\Services\OrderService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class TriggerService
 * @package App\Services
 */
class TriggerService
{
    /**
     * Deletes trigger record
     */
    public function delete_trigger($id)
    {
        $trigger = new Trigger();
        $res = $trigger::where('id', $id)->delete();
        
        return 0;
    }
}
