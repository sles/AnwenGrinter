<?php

use App\DotEnv;

(new DotEnv('.env'))->load();

return [
    'database_host' => getenv('DB_HOST'),
    'database_name' => getenv('DB_NAME'),
    'database_table' => getenv('DB_TABLE'),
    'database_username' => getenv('DB_USERNAME'),
    'database_password' => getenv('DB_PASSWORD'),
];
