<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Logon extends Model {
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
        'client_id_sub',
        'version',
        'os',
        'os_version',
        'sp_id',
        'brand',
        'model',
        'imsi',
        'imei',
        'ip',
        'game_id',
        'pack_version',
        // 'third_party',
        // 'third_nick',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'logon';

    
    public $timestamps = false;

    protected $connection = 'Master';
}