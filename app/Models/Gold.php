<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Gold extends Model
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
        'game_id',
        'node_id',
        'type_id',
        'type_id_sub',
        'quantity1',
        'quantity2',
        'quantity',
        'room_id',
        'version',
        'kind_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'gold';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
