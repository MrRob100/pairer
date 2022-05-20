<?php

namespace App\Console\Commands;

use App\Helpers\PairHelper;
use App\Models\Pair;
use App\Services\AccountService;
use Illuminate\Console\Command;

class CheckFillsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:fills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public $pairHelper;

    public $accountService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PairHelper $pairHelper, AccountService $accountService)
    {
        $this->pairHelper = $pairHelper;
        $this->accountService = $accountService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pairs = Pair::where('state', 'active')->wherePure()->get();

        $this->accountService->checkForFills($pairs->toArray());

        return Command::SUCCESS;
    }
}
