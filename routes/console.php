<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/**
 * Override migrate commands
 * 
 * this is a workaround to disable migrate commands. 
 * 
 * @link https://laravel.com/docs/8.x/artisan#programmatically-executing-commands
 * @link https://stackoverflow.com/questions/63588629/is-there-a-way-to-disable-artisan-commands
 * */ 

function disable_migrate_commands($t) {
    $t->newLine();
    $t->error(' THIS COMMAND IS DISABLED ');
    $t->comment('database of this project is not managed by laravel');
    $t->comment('use database management tools like navicat or mysql workbench to manage database');
    $t->newLine();
}   

Artisan::command('migrate', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate command');

Artisan::command('migrate:fresh', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:fresh command');

Artisan::command('migrate:install', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:install command');

Artisan::command('migrate:refresh', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:refresh command');

Artisan::command('migrate:reset', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:reset command');

Artisan::command('migrate:rollback', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:rollback command');

Artisan::command('migrate:status', function () {
    disable_migrate_commands($this);
})->purpose('Overriding migrate:status command');

