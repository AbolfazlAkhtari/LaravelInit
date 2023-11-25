# Laravel Init

Tired of running Laravel's command over and over again?
<br>
<br>
Do you have a set of commands which should run in order to run the application? Perhaps seeding some info into the database?
<br>

Laravel Init provides you with a simple config file which you can put these kind of commands in it, 
and instead of running them manually everytime, 
you can just run `php artisan init`.
<br>
<br>
Here is how it works:

# Table of Contents
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)


## Installation
``` bash
composer require double-a/laravel-init
```

## Configuration
Configuration file can be published using the following command:
``` bash
php artisan vendor:publish --provider="DoubleA\LaravelInit\Providers\InitServiceProvider"
```
Default configuration file located in `config/init.php` looks like this:
``` php
<?php

return [
    // Default steps that will run, regardless of the chosen option
    // put your custom commands (e.g for running the seeders) here
    "default_steps" => [
        "git pull",
        "composer install",
        "php artisan key:generate",
        "php artisan storage:link",
        "php artisan cache:clear",
        "php artisan config:clear",
    ],

    // you can define as many options as you wish
    "options" => [
        [
            "title" => "Fetch updates and start from scratch (Removes all data)",
            
            // it means a secondary confirmation will be asked from the user before proceeding
            // usefull for operations which can be dangerous like migrate:fresh
            "confirm_needed" => true,
            
            // Any extra command you wish to run only on this option and not on others
            "extra_steps" => [
                "php artisan migrate:fresh --seed"
            ],
            
            // application will not be served after
            "serve" => false,
        ],
        [
            "title" => "Fetch updates and keep going from where you were (No data will be removed)",
            "confirm_needed" => false,
            "extra_steps" => [
                "php artisan migrate"
            ],
            
            // In case of setting serve to true, serve_port can optionaly be provided
            "serve" => true,
            "serve_port" => 8000, // optional - default : 8000 - not required if serve equals false
        ],
    ],

    // false means that the command will stop in case of error
    // in case of true, regardless of any raised exception, next command will be executed
    "continue_on_error" => false,
];
```
## Usage
Simply run `php artisan init`
