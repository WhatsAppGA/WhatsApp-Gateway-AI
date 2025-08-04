<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Artisan;

Route::get('generate', function () {
    return \Illuminate\Support\Facades\Artisan::call('storage:link');
})->name('generate');


Route::get('storage-link', function () {
    return Artisan::call('storage:link');
})->name('storage-link');

Route::get('/schedule-run', function () {
    return Artisan::call('schedule:run');
})->name('schedule-run');

Route::get('/blast-start', function () {
    return Artisan::call('start:blast');
})->name('blast-start');

Route::get('/subscription-check', function () {
    return Artisan::call('subscription:check');
})->name('subscription-check');

Route::get('/schedule-cron', function () {
    return Artisan::call('schedule:cron');
})->name('schedule-cron');

Route::get('/migrate', function () {
    return Artisan::call('migrate');
})->name('migrate');

Route::get('/view-clear', function () {
    return Artisan::call('view:clear');
})->name('view-clear');

Route::get('/clear-cache', function () {
    return Artisan::call('optimize:clear');
})->name("cache.clear");
