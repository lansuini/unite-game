<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Versus extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'post_time',
        'game_unique_id',
        'room_id',
        'room_mode',
        'node_id',
        'desk_id',
        'players',
        'revenue',
        'duration',
        'match_id',
        'game_id',
        'game_detail',
        'suid',
        'suip',
        'control_detail',
        'union_id',        
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'versus';

    public $timestamps = false;

    protected $connection = 'Master';
}