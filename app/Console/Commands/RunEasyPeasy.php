<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunEasyPeasy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-easy-peasy {class} {method} {params}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the easy peasy background job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $class = $this->argument('class');
        $method = $this->argument('method');
        $params = explode(',', $this->argument('params'));
    
        runBackgroundJob($class, $method, $params);
    }
}
