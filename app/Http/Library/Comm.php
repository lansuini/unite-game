<?php

namespace App\Http\Library;

use Illuminate\Http\Request;

class Comm
{
    static function getIP(Request $request)
    {
        $ip1 = $request->ip();
        $ip2 = $request->header('X_FORWARDED_FOR');
        $getIPAction = env('GET_IP_ACTION', 0);
        if ($getIPAction == 0) {
            return !empty($ip2) ? $ip2 : $ip1;
        } else if ($getIPAction == 1) {
            return !empty($ip1) ? $ip1 : $ip2;
        }
        return !empty($ip2) ? $ip2 : $ip1;
    }
}
