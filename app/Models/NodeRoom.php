<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NodeRoom extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'nodeid',
        'playid',
        'roomid',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'node_room';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
