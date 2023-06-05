<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\WebLobbyController;
use App\Http\Controllers\Api\ServerNotifyController;
use App\Http\Controllers\Api\ServerConfigController;
use App\Http\Controllers\Api\TestController;

Route::domain(env('DOMAIN_H5'))
    ->withoutMiddleware('throttle:api')
    ->middleware(['maintenance', 'throttle:600:1'])
    ->group(function () {
        Route::get('/web-lobby/login', [WebLobbyController::class, 'login']);
    });

Route::domain(env('DOMAIN_H5'))
    ->withoutMiddleware('throttle:api')
    ->middleware(['maintenance', 'throttle:2000:1'])
    ->group(function () {
        Route::post('/cf/LoginGame', [WebLobbyController::class, 'loginGame']);
        // Route::post('/cf/GetPlayerWallet', [WebLobbyController::class, 'getPlayerWallet']);
        Route::post('/cf/TransferIn', [WebLobbyController::class, 'transferIn']);
        // Route::post('/cf/TransferOut', [WebLobbyController::class, 'transferOut']);
        Route::post('/cf/GetHistory', [WebLobbyController::class, 'getHistory']);
        Route::post('/cf/GetGameDetail', [WebLobbyController::class, 'getGameDetail']);
        Route::post('/cf/GetDataReport', [WebLobbyController::class, 'getDataReport']);
    });

Route::domain(env('DOMAIN_H5'))
    ->withoutMiddleware('throttle:api')
    ->middleware(['throttle:2000:1'])
    ->group(function () {
        Route::post('/cf/GetPlayerWallet', [WebLobbyController::class, 'getPlayerWallet']);
        Route::post('/cf/TransferOut', [WebLobbyController::class, 'transferOut']);
    });

Route::domain(env('DOMAIN_H5'))->middleware(['ipWhite:TEST_IPWHITE', 'maintenance'])
->withoutMiddleware('throttle:api')
->middleware(['throttle:2000:1'])
->group(function () {
    Route::post('/test/VerifySession', [TestController::class, 'VerifySession']);
    Route::post('/test/cf/VerifySession', [TestController::class, 'CFVerifySession']);
    Route::post('/test/Cash/Get', [TestController::class, 'CashGet']);
    Route::post('/test/Cash/TransferInOut', [TestController::class, 'CashTransferInOut']);
    Route::get('/test', [TestController::class, 'test']);
    Route::get('/web-lobby/test1', [WebLobbyController::class, 'test1']);

    Route::get('/test/batchInsertTransferInout', [TestController::class, 'batchInsertTransferInout']);
});

Route::domain(env('DOMAIN_SERVER'))->middleware(['ipWhite:SERVER_IPWHITE', 'serverNotifyCheck'])->group(function () {
    Route::match(['GET', 'POST'], '/cash/get', [ServerNotifyController::class, 'CashGet']);
    // Route::match(['GET', 'POST'], '/cash/transferInOut', [ServerNotifyController::class, 'CashTransferInOut']);
    Route::match(['GET', 'POST'], '/cash/transferInOutBatch', [ServerNotifyController::class, 'CashTransferInOutBatch']);
    Route::match(['GET', 'POST'], '/verifySession', [ServerNotifyController::class, 'VerifySession']);
    Route::match(['GET', 'POST'], '/cash/transferInOutByQueue', [ServerNotifyController::class, 'CashTransferInOutByQueue']);
    Route::match(['GET', 'POST'], '/cash/transferInOut', [ServerNotifyController::class, 'CashTransferInOutByQueue']);
    
    Route::match(['GET', 'POST'], '/conf/getNodeList', [ServerConfigController::class, 'getNodeList']);
    Route::match(['GET', 'POST'], '/conf/getRoom', [ServerConfigController::class, 'getRoom']);
    Route::match(['GET', 'POST'], '/conf/getNodeByRoomID', [ServerConfigController::class, 'getNodeByRoomID']);
    Route::match(['GET', 'POST'], '/conf/getConfigGameList', [ServerConfigController::class, 'getConfigGameList']);
    Route::match(['GET', 'POST'], '/conf/getNodes', [ServerConfigController::class, 'getNodes']);
    Route::match(['GET', 'POST'], '/conf/getGameStatusConfig', [ServerConfigController::class, 'getGameStatusConfig']);

    Route::match(['GET', 'POST'], '/conf/getNodeRoomList', [ServerConfigController::class, 'getNodeRoomList']);
});
