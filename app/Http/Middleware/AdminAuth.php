<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class AdminAuth
{
    protected $maps = [
        'GM' => [
            'Role' => '\App\Models\Manager\Role',
            'Admin' => '\App\Models\Manager\Admin',
            'LoginLog' => '\App\Models\Manager\LoginLog',
            'ActionLog' => '\App\Models\Manager\ActionLog',
        ],
        'ANALYSIS' => [
            'Role' => '\App\Models\Manager\Analysis\Role',
            'Admin' => '\App\Models\Manager\Analysis\Admin',
            'LoginLog' => '\App\Models\Manager\Analysis\LoginLog',
            'ActionLog' => '\App\Models\Manager\Analysis\ActionLog',
        ],
        'MERCHANT' => [
            'Role' => '\App\Models\Manager\Merchant\Role',
            'Admin' => '\App\Models\Manager\Merchant\Admin',
            'LoginLog' => '\App\Models\Manager\Merchant\LoginLog',
            'ActionLog' => '\App\Models\Manager\Merchant\ActionLog',
        ],
    ];

    public function handle(Request $request, Closure $next, $type)
    {
        if (
            $request->method() == 'GET'
            && $request->ip() == '127.0.0.1'
            && $request->get('_export') == 1
        ) {
            // $request->merge(['_export' => 1]);
            if (env('EXPORT_LOCALHOST_PROTECT', false)) {
                $redis = Redis::connection('cache');
                $random = $request->get('_random');
                if (
                    $request->header('User-Agent') == env('API_REQUEST_NAME', 'IG GAME') 
                    && $redis->get('export_random:' . $random) == 1
                ) {
                    return $next($request);
                } else {
                    return response('Localhost Unauthorized.', 401);
                }
            } else {
                return $next($request);
            }
        }

        $model = new $this->maps[$type]['Admin']();
        $role = new $this->maps[$type]['Role']();

        if (!$model->isLogin($request)) {

            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('/login');
            }
        }

        if (!$model->isPermission($request, $role)) {
            return response('Permission deneid.', 403);
        }

        if (env('UNIQUE_LOGIN', false) && !$model->isUniqueLogin($request)) {
            $model->loginOut($request);
            return response('Your account is already logged in elsewhere. Please refresh current page!', 401);
        }
        return $next($request);
    }
}
