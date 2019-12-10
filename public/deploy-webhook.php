<?php
// PREREQUISITE: Set the following key in your Laravel .env file, and give it a value (a not-easy-to-guess word will do). Best not to use spaces or html-encodable-entities/chars:
//  DEPLOY_SECRET_KEY=
// This value must be passed as &key= when setting up a github deploy call, to verify authorization to run
// So the webhook to call will be: https://your_domain.com/deploy-webhook.php&key=foo where foo is the secret key described above

// since this file is in Laravel's "public" directory, we go up one level to access the required files
chdir('..');

if (file_exists('.env')) {
    $rows = file('.env');
    foreach ($rows as $row) {
        if (strpos($row, '=') < 1) {
            continue;
        }
        [$key, $val] = explode('=', $row);
        if ($key == 'DEPLOY_SECRET_KEY') {
            define('DEPLOY_SECRET_KEY', trim($val));
        }
    }
}

if (!defined('DEPLOY_SECRET_KEY')) {
    die('error: could not find deploy key in .env');
}

if (!isset($_GET['key'])) {
    die('error: deploy key not passed');
}

if ($_GET['key'] !== DEPLOY_SECRET_KEY) {
    die('error: could not verify deploy key');
}

if (!file_exists('deploy-laravel.sh')) {
    die('error: deploy-laravel.sh file not found, or permissions make it not readable');
}

$output = [];
exec('sh deploy-laravel.sh', $output);
foreach ($output as $line) {
    echo $line . "\n";
}
