<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DataReportSub extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_id',
        'client_id_sub',
        'count_date',
        'bet_count',
        'bet_amount',
        'transfer_amount',
        'game_id',
        'updated_time',
        'valid_user_cnt',
        'tax'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'data_report_sub';

    protected $connection = 'Master';

    public $timestamps = false;
}
