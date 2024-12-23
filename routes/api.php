<?php

use App\Http\Controllers\ArtificialIntellegenceController;
use App\Http\Controllers\AverageDailyController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CollectDataController;

Route::post('/send_collect_data', [CollectDataController::class, 'SendCollectData']);
Route::get('/get_collect_data', [CollectDataController::class, 'GetAllCollectData']);
Route::get('/get_average_daily', [AverageDailyController::class, 'GetAverageDaily']);
Route::post('/send_message', [ArtificialIntellegenceController::class, 'SendMessage']);
Route::get('/get_message_data', [ArtificialIntellegenceController::class, 'GetMessageData']);
Route::get('/get_message_latest', [ArtificialIntellegenceController::class, 'GetLatestMessageData']);
