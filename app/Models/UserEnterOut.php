<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserEnterOut extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'post_time',
        'uid',
        'client_id',
        'version',
        'ip',
        'room_id',
        'room_num',
        'type',
        'result',
        'change_gold',
        'last_gold',
        'last_bank_gold',
        'now_bank_gold',
        'now_gold',
        'enter_time',
        'nodeid',
        'game_bill',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'user_enter_out';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
