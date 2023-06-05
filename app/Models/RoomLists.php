<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RoomLists extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'room_id',
        'channel_id',
        'status',
        'desc',
        'c_adminid',
        'c_time',
        'last_m_adminid',
        'm_time',
        'uid_tails',
        'max_num',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'room_lists';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
