<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Analysis\HomeController;
use App\Http\Controllers\Analysis\ServerController;

use App\Http\Controllers\Analysis\AdminController;
use App\Http\Controllers\Analysis\PlayerController;
use App\Http\Controllers\Analysis\APIDataController;
use App\Http\Controllers\Analysis\CustomerController;
use App\Http\Controllers\Analysis\ExportController;

use App\Http\Controllers\Merchant\AdminController as MerchantAdminController;
use App\Http\Controllers\Merchant\HomeController as MerchantHomeController;

use App\Http\Controllers\Merchant\SupportController;

Route::domain(env('DOMAIN_ANALYSIS'))->middleware('ipWhite:ANALYSIS_IPWHITE')->group(function () {


    Route::get('login', [HomeController::class, 'login']);
    Route::post('login', [HomeController::class, 'doLogin']);
    Route::get('loginout', [HomeController::class, 'doLoginout']);

    Route::middleware('adminAuth:ANALYSIS')->group(function () {
        Route::get('/', [HomeController::class, 'index']);
        
        Route::get('dashboard/view', [HomeController::class, 'dashboardView']);
        Route::get('dashboard', [HomeController::class, 'dashboardList']);

        Route::get('player/account/view', [PlayerController::class, 'accountView']);
        Route::get('player/account', [PlayerController::class, 'accountList']);

        Route::get('player/loginlog/view', [PlayerController::class, 'loginLogView']);
        Route::get('player/loginlog', [PlayerController::class, 'loginLogList']);

        Route::get('player/playlog/view', [PlayerController::class, 'playLogView']);
        Route::get('player/playlog', [PlayerController::class, 'playLogList']);
        Route::get('player/playlogdetail', [PlayerController::class, 'playLogDetailList']);

        Route::get('player/goldlog/view', [PlayerController::class, 'goldlogView']);
        Route::get('player/goldlog', [PlayerController::class, 'goldLogList']);

        Route::get('player/realonlineplay/view', [PlayerController::class, 'realOnlinePlayView']);
        Route::get('player/realonlineplay', [PlayerController::class, 'realOnlinePlayList']);

        Route::get('player/livematch/view', [PlayerController::class, 'liveMatchView']);
        Route::get('player/livematch', [PlayerController::class, 'liveMatchList']);

        Route::get('player/online/view', [PlayerController::class, 'onlineView']);
        Route::get('player/online', [PlayerController::class, 'onlineList']);

        Route::get('player/roomwinlose/view', [PlayerController::class, 'roomWinLoseView']);
        Route::get('player/roomwinlose', [PlayerController::class, 'roomWinLoseList']);

        Route::get('apidata/transferinout/view', [APIDataController::class, 'transferInOutView']);
        Route::get('apidata/transferinout', [APIDataController::class, 'transferInOutList']);

        Route::get('apidata/gamedetails/view', [APIDataController::class, 'gameDetailsView']);
        Route::get('apidata/gamedetails', [APIDataController::class, 'gameDetailsList']);

        Route::get('apidata/datareport/view', [APIDataController::class, 'dataReportView']);
        Route::get('apidata/datareport', [APIDataController::class, 'dataReportList']);

        Route::get('apidata/subdatareport/view', [APIDataController::class, 'subDataReportView']);
        Route::get('apidata/subdatareport', [APIDataController::class, 'subDataReportList']);

        Route::get('customer/client/view', [CustomerController::class, 'clientView']);
        Route::get('customer/client', [CustomerController::class, 'clientList']);
        Route::get('customer/client/{id}', [CustomerController::class, 'clientDetail']);
        Route::post('customer/client', [CustomerController::class, 'clientAdd']);
        Route::patch('customer/client/{id}', [CustomerController::class, 'clientEdit']);
        Route::delete('customer/client/{id}', [CustomerController::class, 'clientDel']);
        Route::match(['GET', 'PATCH'], 'customer/client/json/{id}', [CustomerController::class, 'clientJSONEdit']);

        Route::get('customer/subclient/view', [CustomerController::class, 'subClientView']);
        Route::get('customer/subclient', [CustomerController::class, 'subClientList']);
        Route::get('customer/subclient/{id}', [CustomerController::class, 'subClientDetail']);
        Route::post('customer/subclient', [CustomerController::class, 'subClientAdd']);
        Route::patch('customer/subclient/{id}', [CustomerController::class, 'subClientEdit']);
        Route::delete('customer/subclient/{id}', [CustomerController::class, 'subClientDel']);

        Route::get('customer/serverrequestlog/view', [CustomerController::class, 'serverRequestLogView']);
        Route::get('customer/serverrequestlog', [CustomerController::class, 'serverRequestLogList']);
        Route::post('customer/serverrequestlog/{clientId}/{id}', [CustomerController::class, 'serverRequestLogAdd']);
        Route::get('customer/serverrequestlog/{clientId}/{id}', [CustomerController::class, 'serverRequestLogDetail']);
        Route::get('customer/serverpostlog/view', [CustomerController::class, 'serverPostLogView']);
        Route::get('customer/serverpostlog', [CustomerController::class, 'serverPostLogList']);
        

        Route::get('manager/account/password/view', [HomeController::class, 'passwordView']);
        Route::post('manager/account/password', [HomeController::class, 'passwordEdit']);
        Route::get('manager/account/googlecode/view', [HomeController::class, 'googleCodeView']);
        Route::post('manager/account/googlecode', [HomeController::class, 'googleCodeEdit']);


        Route::get('merchant/getbasedata', [MerchantHomeController::class, 'getBaseData']);
        Route::get('merchant/manager/account/view', [MerchantAdminController::class, 'adminView']);
        Route::get('merchant/manager/account', [MerchantAdminController::class, 'adminList']);
        Route::get('merchant/manager/account/{id}', [MerchantAdminController::class, 'adminDetail']);
        Route::post('merchant/manager/account', [MerchantAdminController::class, 'adminAdd']);
        Route::patch('merchant/manager/account/{id}', [MerchantAdminController::class, 'adminEdit']);
        Route::delete('merchant/manager/account/{id}', [MerchantAdminController::class, 'adminDel']);

        Route::get('merchant/manager/role/view', [MerchantAdminController::class, 'roleView']);
        Route::get('merchant/manager/role', [MerchantAdminController::class, 'roleList']);
        Route::get('merchant/manager/role/{id}', [MerchantAdminController::class, 'roleDetail']);
        Route::post('merchant/manager/role', [MerchantAdminController::class, 'roleAdd']);
        Route::patch('merchant/manager/role/{id}', [MerchantAdminController::class, 'roleEdit']);
        Route::delete('merchant/manager/role/{id}', [MerchantAdminController::class, 'roleDel']);

        Route::get('merchant/manager/loginlog/view', [MerchantAdminController::class, 'loginLogView']);
        Route::get('merchant/manager/loginlog', [MerchantAdminController::class, 'loginLogList']);

        Route::get('merchant/manager/actionlog/view', [MerchantAdminController::class, 'actionLogView']);
        Route::get('merchant/manager/actionlog', [MerchantAdminController::class, 'actionLogList']);
        Route::get('merchant/manager/actionlog/{id}', [MerchantAdminController::class, 'actionLogDetail']);


        Route::get('getbasedata', [HomeController::class, 'getBaseData']);

        Route::get('manager/account/view', [AdminController::class, 'adminView']);
        Route::get('manager/account', [AdminController::class, 'adminList']);
        Route::get('manager/account/{id}', [AdminController::class, 'adminDetail']);
        Route::post('manager/account', [AdminController::class, 'adminAdd']);
        Route::patch('manager/account/{id}', [AdminController::class, 'adminEdit']);
        Route::delete('manager/account/{id}', [AdminController::class, 'adminDel']);

        Route::get('manager/role/view', [AdminController::class, 'roleView']);
        Route::get('manager/role', [AdminController::class, 'roleList']);
        Route::get('manager/role/{id}', [AdminController::class, 'roleDetail']);
        Route::post('manager/role', [AdminController::class, 'roleAdd']);
        Route::patch('manager/role/{id}', [AdminController::class, 'roleEdit']);
        Route::delete('manager/role/{id}', [AdminController::class, 'roleDel']);

        Route::get('manager/loginlog/view', [AdminController::class, 'loginLogView']);
        Route::get('manager/loginlog', [AdminController::class, 'loginLogList']);

        Route::get('manager/actionlog/view', [AdminController::class, 'actionLogView']);
        Route::get('manager/actionlog', [AdminController::class, 'actionLogList']);
        Route::get('manager/actionlog/{id}', [AdminController::class, 'actionLogDetail']);

        Route::get('manager/currency/view', [AdminController::class, 'currencyView']);
        Route::get('manager/currency', [AdminController::class, 'currencyList']);
        Route::get('manager/currency/{id}', [AdminController::class, 'currencyDetail']);
        Route::post('manager/currency', [AdminController::class, 'currencyAdd']);
        Route::patch('manager/currency/{id}', [AdminController::class, 'currencyEdit']);
        Route::delete('manager/currency/{id}', [AdminController::class, 'currencyDel']);

        Route::get('server/game/room/view', [ServerController::class, 'roomView']);
        Route::get('server/game/room', [ServerController::class, 'roomList']);
        Route::get('server/game/room/{id}', [ServerController::class, 'roomDetail']);
        Route::post('server/game/room', [ServerController::class, 'roomAdd']);
        Route::patch('server/game/room/{id}', [ServerController::class, 'roomEdit']);
        Route::patch('server/game/room/enabled/{id}', [ServerController::class, 'roomEnabledEdit']);
        Route::match(['GET', 'PATCH'], '/server/game/room/json/{id}', [ServerController::class, 'roomJSONEdit']);
        Route::delete('server/game/room/{id}', [ServerController::class, 'roomDel']);
        Route::post('server/game/room/pushconfig', [ServerController::class, 'roomPushConfig']);

        Route::get('server/game/room/process/{id}', [ServerController::class, 'roomProcessDetail']);
        Route::patch('server/game/room/process/{id}', [ServerController::class, 'roomProcessEdit']);

        Route::get('server/game/room/inventory/{id}', [ServerController::class, 'roomInventoryDetail']);
        Route::patch('server/game/room/inventory/{id}', [ServerController::class, 'roomInventoryEdit']);


        Route::get('server/game/play/view', [ServerController::class, 'playView']);
        Route::get('server/game/play', [ServerController::class, 'playList']);
        Route::get('server/game/play/{id}', [ServerController::class, 'playDetail']);
        Route::post('server/game/play', [ServerController::class, 'playAdd']);
        Route::patch('server/game/play/{id}', [ServerController::class, 'playEdit']);
        Route::delete('server/game/play/{id}', [ServerController::class, 'playDel']);

        Route::get('server/game/processcontrol/view', [ServerController::class, 'processControlView']);
        Route::get('server/game/processcontrol', [ServerController::class, 'processControlList']);
        Route::get('server/game/processcontrol/{id}', [ServerController::class, 'processControlDetail']);
        Route::post('server/game/processcontrol', [ServerController::class, 'processControlAdd']);
        Route::patch('server/game/processcontrol/{id}', [ServerController::class, 'processControlEdit']);
        Route::delete('server/game/processcontrol/{id}', [ServerController::class, 'processControlDel']);

        Route::get('server/game/maintenance/view', [ServerController::class, 'maintenanceView']);
        Route::get('server/game/maintenance', [ServerController::class, 'maintenanceList']);
        Route::patch('server/game/maintenance', [ServerController::class, 'maintenanceEdit']);

        Route::get('export/view', [ExportController::class, 'exportView']);
        Route::get('export', [ExportController::class, 'exportList']);
        Route::get('export/download/{id}', [ExportController::class, 'exportDownload']);
        Route::post('export', [ExportController::class, 'exportAdd']);

        Route::get('support/apidocument/view', [SupportController::class, 'apiDocumentView']);
        Route::get('support/apidocument', [SupportController::class, 'apiDocumentList']);

        Route::post('admin/setLang/{lang}', [AdminController::class, 'setLang']);
    });
});
