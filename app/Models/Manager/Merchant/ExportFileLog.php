<?php

namespace App\Models\Manager\Merchant;

use App\Models\Manager\Analysis\ExportFileLog as Model;

class ExportFileLog extends Model
{
    protected $table = 'merchant_export_file_log';

    protected $connection = 'Master';
}
