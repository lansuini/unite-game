<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AccountsToday extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'uid',
        'roomid',
        'room_name',
        'gameid',
        'all_result',
        'update_time',
        'nodeid',
        'today_result',
        'history_result'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'accounts_today';

    
    public $timestamps = false;

    protected $connection = 'Master';
}


