<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Node extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'parentid',
        // 'alias_id',
        'gameid',
        'name',
        'name_mark',
        'pic_name',
        'plat',
        'plats',
        'sortid',
        'mingold',
        'maxgold',
        'optime',
        'xmlgame',
        // 'roomchargegold',
        'enabled',
        'hot_register',
        'bottom',
        // 'play_method',
        'tiyan',
        // 'tax',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'node';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
