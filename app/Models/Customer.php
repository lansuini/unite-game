<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'company_name',
        'operator_token',
        'secret_key',
        'merchant_addr',
        'is_lock',
        'api_ip_white',
        'api_mode',
        'created',
        'game_domain',
        'game_oc',
        'game_mc',
        'configs',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    
    public $timestamps = false;

    protected $connection = 'Master';

    protected $table = 'customer';

    public static function getCustomerByOperatorToken($operatorToken)
    {
        $redis = Redis::connection('cache');
        $data = $redis->get('web_customer:' . $operatorToken);
        if (empty($data)) {
            $data = Customer::where('operator_token', $operatorToken)->first();
            if (!empty($data)) {
                $data = $data->toArray();
                
                $redis->setex('web_customer:' . $operatorToken, 7 * 86400, json_encode($data));
            }
        } else {
            $data = json_decode($data, true);
        }
        return $data;
    }

    public static function refreshCustomerByOperatorToken($operatorToken) {
        $redis = Redis::connection('cache');
        $redis->del('web_customer:' . $operatorToken);
    }

    public static function getCustomerById($id)
    {
        $redis = Redis::connection('cache');
        $data = $redis->get('web_customer:' . $id);
        if (empty($data)) {
            $data = Customer::where('id', $id)->first();
            if (!empty($data)) {
                $data = $data->toArray();
                
                $redis->setex('web_customer:' . $id, 7 * 86400, json_encode($data));
            }
        } else {
            $data = json_decode($data, true);
        }
        return $data;
    }

    public static function refreshCustomerById($id) {
        $redis = Redis::connection('cache');
        $redis->del('web_customer:' . $id);
    }
}
