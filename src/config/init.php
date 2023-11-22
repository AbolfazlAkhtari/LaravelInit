<?php

return [
    "default_steps" => [
        "git pull",
        "composer install",
        "php artisan key:generate",
        "php artisan storage:link",
        "php artisan cache:clear",
        "php artisan config:clear",
    ],

    "options" => [
        [
            "title" => "Fetch updates and start from scratch (Removes all data)",
            "confirm_needed" => true,
            "extra_steps" => [
                "php artisan migrate:fresh --seed"
            ],
            "serve" => false,
        ],
        [
            "title" => "Fetch updates and keep going from where you were (No data will be removed)",
            "confirm_needed" => false,
            "extra_steps" => [
                "php artisan migrate"
            ],
            "serve" => true,
            "serve_port" => 8000, // optional - default : 8000 - not required if serve equals false
        ],
    ],

    "continue_on_error" => false,
];
