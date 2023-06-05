<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserRoomExt extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'tbus',
        'node_id',
        'game_id',
        'online_num',
        'op_time',
        'client_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'user_room_ext';

    protected $connection = 'Master';

    public $timestamps = false;
}
