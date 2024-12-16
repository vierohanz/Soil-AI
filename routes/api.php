<?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CollectDataController;

Route::post('/send_collect_data', [CollectDataController::class, 'SendCollectData']);
Route::get('/get_collect_data', [CollectDataController::class, 'GetAllCollectData']);
