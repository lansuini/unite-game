<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TransferInOutServerRequestLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'transaction_id',
        'server_request_log_id',
        'queue_name',
        'created',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'transfer_inout_server_request_log';

    protected $connection = 'Master';

    public $timestamps = false;

    public function st($num)
    {
        return $this->setTable("transfer_inout_server_request_log_{$num}");
    }

    public function checkClientTable($num)
    {
        return Schema::connection($this->connection)->hasTable("transfer_inout_server_request_log_{$num}");
    }
}
