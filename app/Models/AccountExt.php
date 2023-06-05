<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class AccountExt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'client_id',
        'client_id_sub',
        'game_id',
        'game_version',
        'os',
        'register_time',
        'last_logon_time',
        'prev_logon_time',
        'prev_logon_time2',
        'last_logon_ip',
        'register_ip',
        'viplevel',
        'is_risk_user',
        'province',
        'city',
        'tqvip',
        'tqvip_end_time',
        'hard_code',
        'currency',
        'lang',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'account_ext';

    protected $connection = 'Master';
    
    public $timestamps = false;
}
