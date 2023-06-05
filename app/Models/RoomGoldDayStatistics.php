<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class RoomGoldDayStatistics extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_id',
        'game_id',
        'node_id',
        'gold',
        'tax',
        'platform_fee',
        'create_date',
        'create_time',
        'bet_tax',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'room_gold_day_statistics';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
