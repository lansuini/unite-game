<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class NodeEntrance extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'game_id',
        'game_type',
        'method_name',
        'pict_url',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'node_entrance';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
