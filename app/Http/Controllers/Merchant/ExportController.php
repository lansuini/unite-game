<?php
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Analysis\ExportController as Controller;
class ExportController extends Controller
{
    protected $model = '\App\Models\Manager\Merchant\ExportFileLog';

    protected $proj = 'Merchant';

    protected $classMaps = [
        'Role' => '\App\Models\Manager\Merchant\Role',
        'Admin' => '\App\Models\Manager\Merchant\Admin',
        'LoginLog' => '\App\Models\Manager\Merchant\LoginLog',
        'ActionLog' => '\App\Models\Manager\Merchant\ActionLog',
    ];

    protected $baseDataPath = 'merchant.selectItems';

    protected $allowExportKeys = [
        'data_report_export' => ['uri' => 'report/datareport'],
        'sub_data_report_export' => ['uri' => 'report/subdatareport'],
    ];

    protected $checkPermission = false;
}