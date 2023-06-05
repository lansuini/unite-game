<?php

namespace App\Models\Manager\Analysis;

use Illuminate\Database\Eloquent\Model;


class ExportFileLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'cost_time',
        'created',
        'admin_id',
        'admin_username',
        'key',
        'errors',
        'size',
        'ext',
        'filename',
        'is_success',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'analysis_export_file_log';

    protected $connection = 'Master';

    public $timestamps = false;
}
