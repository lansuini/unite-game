<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Merchant\HomeController;
use App\Http\Controllers\Merchant\UserController;
use App\Http\Controllers\Merchant\ReportController;
use App\Http\Controllers\Merchant\SupportController;
use App\Http\Controllers\Merchant\SettingsController;
use App\Http\Controllers\Merchant\ExportController;
use App\Http\Controllers\GM\AdminController;

Route::domain(env('DOMAIN_MERCHANT'))->middleware('ipWhite:MERCHANT_IPWHITE')->group(function () {


    Route::get('login', [HomeController::class, 'login']);
    Route::post('login', [HomeController::class, 'doLogin']);
    Route::get('loginout', [HomeController::class, 'doLoginout']);

    Route::middleware('adminAuth:MERCHANT')->group(function () {
        Route::get('/', [HomeController::class, 'index']);

        Route::get('manager/account/password/view', [HomeController::class, 'passwordView']);
        Route::post('manager/account/password', [HomeController::class, 'passwordEdit']);
        Route::get('manager/account/googlecode/view', [HomeController::class, 'googleCodeView']);
        Route::post('manager/account/googlecode', [HomeController::class, 'googleCodeEdit']);

        Route::get('getbasedata', [HomeController::class, 'getBaseData']);

        Route::get('user/enterexitroomwinlose/view', [UserController::class, 'userEnterExitRoomWinLoseView']);
        Route::get('user/enterexitroomwinlose', [UserController::class, 'userEnterExitRoomWinLoseList']);

        Route::get('user/account/view', [UserController::class, 'accountView']);
        Route::get('user/account', [UserController::class, 'accountList']);

        Route::get('report/total/view', [ReportController::class, 'totalView']);
        Route::get('report/total', [ReportController::class, 'totalList']);

        Route::get('report/day/view', [ReportController::class, 'dayView']);
        Route::get('report/day', [ReportController::class, 'dayList']);

        Route::get('report/datareport/view', [ReportController::class, 'dataReportView']);
        Route::get('report/datareport', [ReportController::class, 'dataReportList']);

        Route::get('report/subdatareport/view', [ReportController::class, 'subDataReportView']);
        Route::get('report/subdatareport', [ReportController::class, 'subDataReportList']);

        Route::get('support/apidocument/view', [SupportController::class, 'apiDocumentView']);
        Route::get('support/apidocument', [SupportController::class, 'apiDocumentList']);

        Route::post('admin/setLang/{lang}', [AdminController::class, 'setLang']);

        Route::get('settings/subclient/view', [SettingsController::class, 'subClientView']);
        Route::get('settings/subclient', [SettingsController::class, 'subClientList']);
        Route::get('settings/subclient/{id}', [SettingsController::class, 'subClientDetail']);
        Route::post('settings/subclient', [SettingsController::class, 'subClientAdd']);
        Route::patch('settings/subclient/{id}', [SettingsController::class, 'subClientEdit']);
        Route::delete('settings/subclient/{id}', [SettingsController::class, 'subClientDel']);

        Route::get('export/view', [ExportController::class, 'exportView']);
        Route::get('export', [ExportController::class, 'exportList']);
        Route::get('export/download/{id}', [ExportController::class, 'exportDownload']);
        Route::post('export', [ExportController::class, 'exportAdd']);
    });
});
