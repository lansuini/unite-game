<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class VersusList extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'pid',
        'uid',
        'nickname',
        'client_id',
        'result',
        'times',
        'score1',
        'score2',
        'point1',
        'point2',
        'version',
        'poker_detail',
        'revenue_person',
        'user_type',
        'create_time',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'versus_list';

    
    public $timestamps = false;

    protected $connection = 'Master';
}