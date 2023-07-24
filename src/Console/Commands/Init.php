<?php

namespace DoubleA\LaravelInit\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
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

        $this->alert('Running default steps');
        $this->execCommands(config('init.default_steps'));

        $this->newLine();

        $this->alert('Running extra steps');
        $this->execCommands($selectedOption['extra_steps']);

        $this->serve();

        return CommandAlias::SUCCESS;
    }

    public function askHowTheInitShouldBe(): array|string
    {
        $options = config('init.options');

        $answer = $this->choice(
            'Welcome Developer, What do want to do?',
            Arr::pluck($options, 'title')
        );

        $options = collect(config('init.options'));
        $selectedOption = $options->where('title', $answer)->first();

        if ($selectedOption['confirm_needed']) {
            $confirm = $this->confirm('Are you sure about this?');
            if (!$confirm) {
                $this->askHowTheInitShouldBe();
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

    public function serve(): void
    {
        $this->alert('Project is Ready and available on '. config('app.url'));
        $this->info('Build Something Amazing! :D');
        exec('cd ' . base_path() . ' && ' . 'php artisan serve');
    }
}
