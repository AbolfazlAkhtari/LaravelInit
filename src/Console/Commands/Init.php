<?php

namespace DoubleA\LaravelInit\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Env;
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
        $selectedOption = $this->askHowTheInitShouldBe();
dd($selectedOption);
        $this->alert('Running default steps');
        $this->execCommands(config('init.default_steps'));

        $this->newLine();

        $this->alert('Running extra steps');
        $this->execCommands($selectedOption['extra_steps']);

        if ($selectedOption['serve'] ?? false) {
            $this->serve($selectedOption['serve_port'] ?? Env::get('SERVER_PORT', 8000));
        }

        return CommandAlias::SUCCESS;
    }

    public function askHowTheInitShouldBe(): array|string
    {
        $options = collect(config('init.options'));

        $answer = $this->choice(
            'Welcome Developer, What do want to do?',
            $options->pluck('title')->toArray()
        );

        $selectedOption = $options->where('title', $answer)->first();

        if ($selectedOption['confirm_needed']) {
            $confirm = $this->confirm('Are you sure about this?');
            if (!$confirm) {
                return $this->askHowTheInitShouldBe();
            }
        }

        return $selectedOption;
    }

    public function execCommands(array $commands): void
    {
        foreach ($commands as $command) {

            $output = [];
            $this->info('Running ' . $command . ' ...');
            exec('cd ' . base_path() . ' && ' . $command, $output, $return);
            if ($return !== 0) {
                $this->error('Error in running command: ' . $command);
            }
            foreach ($output as $line) {
                $this->line($line);
            }
        }
    }

    public function serve($port): void
    {
        $host = Env::get('SERVER_HOST', '127.0.0.1');
        $this->alert("Project is Ready and available on [http://{$host}:{$port}]");
        $this->info('Build Something Amazing! :D');
        exec('cd ' . base_path() . ' && ' . 'php artisan serve --port=' . $port);
    }
}
