<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class GameDetails extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'parent_bet_id',
        'bet_id',
        'create_time',
        'game_id',
        'detail',
        'uid',
        'player_name',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'game_details';

    protected $connection = 'Master';

    public $timestamps = false;

    public function checkClientTable($num)
    {
        return Schema::connection($this->connection)->hasTable("transfer_inout_{$num}");
    }
}
