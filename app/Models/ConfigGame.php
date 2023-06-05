<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ConfigGame extends Model
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
        'xml',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'config_game';

    protected $connection = 'Master';

    
    public $timestamps = false;
}
