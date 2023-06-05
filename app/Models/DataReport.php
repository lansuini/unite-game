<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DataReport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_id',
        'count_date',
        'bet_count',
        'bet_amount',
        'transfer_amount',
        'game_id',
        'updated_time',
        'valid_user_cnt',
        'login_user_cnt',
        'tax'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'data_report';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
