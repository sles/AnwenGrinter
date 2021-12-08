<?php

declare(strict_types=1);

use App\App;

require 'autoload.php';

/**
 * Arguments for console command:
 *   --data-source: url to the JSON file of a given structure to parse
 *   --run-queries: executes 2 predefined queries to showcase the results and prints output in console
 *
 * Example: php index.php --data-source="https://gist.githubusercontent.com/cristiberceanu/94c1539c9bd7cc0f2e3e6e12a26c1551/raw/771417ba472bf1e7c213b6684656be95898892d6/books-data-source.json" --run-queries
 */
$arguments = getopt('', ['data-source::', 'run-queries::', 'migrate::']);

$app = new App();
// create database tables structure
$app->runMigrations();

if (isset($arguments['data-source']) && $arguments['data-source']) {
    // parse the data from provided json file and populate database with unique values
    $app->parseData($arguments['data-source']);
}

if (isset($arguments['run-queries'])) {
    /* execute 2 predefined queries to showcase the results and prints output in console:
     *    1. get top 3 authors by the number of books published
     *    2. get all books by author "Jon Skeet"
     */
    $app->runQueries();
}