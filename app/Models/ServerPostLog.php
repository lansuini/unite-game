<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;
class ServerPostLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'trace_id',
        'uid',
        'client_id',
        'type',
        'arg',
        'return',
        'ip',
        'error_code',
        'error_text',
        'cost_time',
        'created',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'server_post_log';

    
    public $timestamps = false;

    protected $connection = 'Master';    
}
