<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Library\Comm;
use Illuminate\Support\Facades\Redis;

class IPWhite
{
    public function handle(Request $request, Closure $next, $type)
    {
        $ipWhite = env($type);
        if (empty($ipWhite)) {
            return $next($request);
        }

        $ipWhite = explode(',', $ipWhite);
        $ip = Comm::getIP($request);

        $redis = Redis::connection('cache');
        $r = $redis->sismember('IP_WHITE:' . $type, $ip);
        if (!in_array($ip, $ipWhite) && $r == 0) {
            return response('IP Limited. your ip address: ' . $ip, 403);
        }
        return $next($request);
    }
}
