<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserRoom extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'tbus',
        'game_id',
        'online_num',
        'offline_num',
        'op_time',
        'client_id',
        // 'node_id_junior',
        // 'node_id_midle',
        // 'node_id_senior',
        // 'node_id_super_junior',
        // 'node_id_super_midle',
        // 'node_id_super_senior',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'user_room';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
