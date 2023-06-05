<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Account extends Model
{
    protected $primaryKey = 'uid';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'uid',
        'player_name',
        'nickname',
        'avatar',
        'account_type',
        'banned_time',
        'banned_type',
        'created',
        'client_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
    ];

    protected $table = 'account';

    protected $connection = 'Master';
    
    public $timestamps = false;

    public static function getAccountByUid($uid, $returnObject = false)
    {
        $redis = Redis::connection('cache');
        $data = $redis->get('account1:' . $uid);
        if (empty($data)) {
            $data = Account::where('uid', $uid)->first();
            if (!empty($data)) {
                $redis->setex('account1:' . $uid, 86400 * 7, json_encode($data));
            }

            if ($returnObject == false) {
                $data = (array) $data;
            }
        } else {
            $data = json_decode($data, !$returnObject);
        }
        return $data;
    }

    public static function getAccountByClientIdAndPlayerName($clientId, $playerName, $returnObject = false)
    {
        $redis = Redis::connection('cache');
        $data = $redis->get('account2:' . $clientId .':'. $playerName);
        if (empty($data)) {
            $data = Account::where('player_name', $playerName)->where('client_id', $clientId)->first();
            if (!empty($data)) {
                $redis->setex('account2:' . $clientId .':'. $playerName, 86400 * 7, json_encode($data));
            }

            if ($returnObject == false) {
                $data = (array) $data;
            }
        } else {
            $data = json_decode($data, !$returnObject);
        }
        return $data;
    }
}
