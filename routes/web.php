<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebLobbyController;


Route::domain(env('DOMAIN_H5'))->middleware('maintenance')->group(function () {
    Route::get('web-lobby/index.html', [WebLobbyController::class, 'redirect']);
    Route::get('web-lobby', [WebLobbyController::class, 'redirect']);
});