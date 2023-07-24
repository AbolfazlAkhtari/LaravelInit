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

        switch ($selectedOption) {
            case self::INIT_OPTIONS[0]:
                $this->RunWithFreshDB();
                $this->runServe();
                break;
            case self::INIT_OPTIONS[1]:
                $this->updateWithoutFreshDB();
                $this->runServe();
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

    public function RunWithFreshDB(): void
    {
        $this->alert("Fetching latest codes...");
        $this->runGitPull();

        $this->alert("Installing Dependencies...");
        $this->runComposerInstall();

        $this->info("Project is up to date and dependencies are installed!");
        $this->newLine();
        $this->alert("Bootstrapping Project...");

        $this->question('Generating Key');
        $this->runKeyGenerate();

        $this->question('Linking Storage');
        $this->runStorageLink();

        $this->question('Re-Cache Necessary Caches');
        $this->runCacheClear();
        $this->runConfigClear();

        $this->question('Making Database Ready');
        $this->runMigrateFreshAndSeed();
    }


    public function updateWithoutFreshDB(): void
    {
        $this->alert("Fetching latest codes...");
        $this->runGitPull();

        $this->alert("Installing Dependencies...");
        $this->runComposerInstall();

        $this->info("Project is up to date and dependencies are installed!");

        $this->alert("Bootstrapping Project...");

        $this->question('Re-Cache Necessary Caches');
        $this->runCacheClear();
        $this->runConfigClear();

        $this->question('Making Database Ready');
        $this->runMigrate();
    }


    public function runGitPull()
    {
        exec('cd ' . base_path() . ' && ' . 'git pull', $gitOutput);
        foreach ($gitOutput as $line) {
            $this->line($line);
        }
    }

    public function runComposerInstall()
    {
        exec('cd ' . base_path() . ' && ' . 'composer install', $composerOutput);
        foreach ($composerOutput as $line) {
            $this->line($line);
        }
    }

    public function runKeyGenerate()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan key:generate', $generateKeyOutput);
        foreach ($generateKeyOutput as $line) {
            $this->line($line);
        }
    }

    public function runStorageLink()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan storage:link', $storageLinkOutput);
        foreach ($storageLinkOutput as $line) {
            $this->line($line);
        }
    }

    public function runCacheClear()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan cache:clear', $cacheClearOutput);
        foreach ($cacheClearOutput as $line) {
            $this->line($line);
        }
    }

    public function runConfigClear()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan config:clear', $configClearOutput);
        foreach ($configClearOutput as $line) {
            $this->line($line);
        }
    }

    public function runMigrateFreshAndSeed()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan migrate:fresh --seed', $migrateAndSeedOutput);
        foreach ($migrateAndSeedOutput as $line) {
            $this->line($line);
        }
    }

    public function runMigrate()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan migrate', $migrateOutput);
        foreach ($migrateOutput as $line) {
            $this->line($line);
        }
    }

    public function runPassportInstall()
    {
        exec('cd ' . base_path() . ' && ' . 'php artisan passport:install', $passportInstallOutput);
        foreach ($passportInstallOutput as $line) {
            $this->line($line);
        }
    }

    public function runServe()
    {
        $this->alert('Project is Ready and available on '. config('app.url'));
        $this->info('Build Something Amazing! :D');
        exec('cd ' . base_path() . ' && ' . 'php artisan serve');
    }
}
