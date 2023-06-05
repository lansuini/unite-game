<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class CustomerSub extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'created',
        'customer_id',
        'symbol',
        'is_lock',
        'remark',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    
    public $timestamps = false;

    protected $connection = 'Master';

    protected $table = 'customer_sub';

    public static function getCustomerSubByCache($customerId, $symbol)
    {
        $redis = Redis::connection('cache');
        $data = $redis->get('web_customer_sub:' . $customerId);
        if (empty($data)) {
            $data = self::where('customer_id', $customerId)->get();
            if (!empty($data)) {
                $data = $data->toArray();
                $redis->setex('web_customer_sub:' . $customerId, 86400, json_encode($data));
            }
        } else {
            $data = json_decode($data, true);
        }
        if (!empty($symbol)) {
            foreach ($data as $v) {
                if ($v['symbol'] == $symbol) {
                    return $v;
                }
            }
        }
        return $data;
    }

    public static function refreshCustomerSubByCache($customerId) {
        $redis = Redis::connection('cache');
        $redis->del('web_customer:' . $customerId);
    }
}
