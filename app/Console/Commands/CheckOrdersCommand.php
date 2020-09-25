<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CheckOrdersCommand extends Command
{
    /**
     * To be ran every 30 mins and checks if there should be an order 
     * placed at that time.
     *
     * @var string
     */
    protected $signature = 'kucoin:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $check = App::make('App\Services\CheckService');

        $check->checkDB();

        return 0;
    }
}
