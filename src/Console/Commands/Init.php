<?php

namespace DoubleA\LaravelInit\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Init extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializing Project';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        return CommandAlias::SUCCESS;
    }
}
