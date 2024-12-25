<?php

use App\Http\Controllers\ArtificialIntellegenceController;
use App\Http\Controllers\AverageDailyController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CollectDataController;

Route::post('/send_collect_data', [CollectDataController::class, 'SendCollectData']);
Route::get('/get_collect_data', [CollectDataController::class, 'GetAllCollectData']);
Route::get('/get_today_collect_data', [CollectDataController::class, 'GetTodayCollectData']);
Route::get('/get_latest_collect_data', [CollectDataController::class, 'GetlatestCollectData']);
Route::get('/get_range_average_daily', [AverageDailyController::class, 'GetRangeAverageDaily']);
Route::get('/get_all_average_daily', [AverageDailyController::class, 'GetAllAverageDaily']);
Route::post('/send_message', [ArtificialIntellegenceController::class, 'SendMessage']);
Route::get('/get_message_data', [ArtificialIntellegenceController::class, 'GetMessageData']);
Route::get('/get_message_latest', [ArtificialIntellegenceController::class, 'GetLatestMessageData']);
