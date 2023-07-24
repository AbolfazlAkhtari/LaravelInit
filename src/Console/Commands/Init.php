<?php

namespace DoubleA\LaravelInit\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class Init extends Command
{
    const INIT_OPTIONS = [
        "Fetch updates and start from scratch (Removes all data)",
        "Fetch updates and keep going from where you were (No data will be removed)",
    ];

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

        $this->updateProject();
        $this->preServeConfigurations();
        switch ($selectedOption) {
            case self::INIT_OPTIONS[0]:
                $this->question('Making Database Ready');
                $this->execCommand('php artisan migrate:fresh --seed');
                $this->serve();
                break;
            case self::INIT_OPTIONS[1]:
                $this->question('Making Database Ready');
                $this->execCommand('php artisan migrate');
                $this->serve();
                break;
        }

        return CommandAlias::SUCCESS;
    }

    public function askHowTheInitShouldBe(): array|string
    {
        $answer = $this->choice('Welcome Developer, What do want to do?', self::INIT_OPTIONS);

        if ($answer == self::INIT_OPTIONS[0]) {
            $confirm = $this->confirm('This will delete everything on database, Are you sure about this?');
            if (!$confirm) {
                $this->askHowTheInitShouldBe();
            }
        }

        return $answer;
    }

    public function updateProject()
    {
        $this->alert("Fetching latest codes...");
        $this->execCommand('git pull');

        $this->alert("Installing Dependencies...");
        $this->execCommand('composer install');
        $this->info("Project is up to date and dependencies are installed!");
        $this->newLine();
    }

    public function preServeConfigurations()
    {
        $this->alert("Bootstrapping Project...");

        $this->question('Generating Key');
        $this->execCommand('php artisan key:generate');

        $this->question('Linking Storage');
        $this->execCommand('php artisan storage:link');

        $this->question('Re-Cache Necessary Configs');
        $this->execCommand('php artisan cache:clear');
        $this->execCommand('php artisan config:clear');
    }

    public function execCommand(string $command)
    {
        exec('cd ' . base_path() . ' && ' . $command, $output);
        foreach ($output as $line) {
            $this->line($line);
        }
    }

    public function serve()
    {
        $this->alert('Project is Ready and available on '. config('app.url'));
        $this->info('Build Something Amazing! :D');
        exec('cd ' . base_path() . ' && ' . 'php artisan serve');
    }
}
