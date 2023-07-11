<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class JobTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command para realizar el despacho del job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("dispach job: inicio");
        \App\Jobs\ConnectToFilemaker::dispatch();
        $this->info("dispach job: fin");
    }
}
