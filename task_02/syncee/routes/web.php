<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SyncController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sync-webshop', [SyncController::class, 'syncWebshop']);
Route::get('/sync-connect', [SyncController::class, 'syncConnect']);
Route::get('/sync-status', [SyncController::class, 'syncStatus']);
Route::get('/sync', [SyncController::class, 'sync']);
