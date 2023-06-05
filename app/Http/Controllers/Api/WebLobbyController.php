<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Library\MerchantCB;
use App\Http\Library\MerchantCF;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use App;

class WebLobbyController extends Controller
{
    public function login(Request $request)
    {
        // App::abort(500);
        // throw new \Exception();
        // $a = encrypt('123456');
        // $b = decrypt($a);

        // $b = decrypt('eyJpdiI6IllFd2pPTjZmNTMxaTZkOWNNbnlhdWc9PSIsInZhbHVlIjoiaW1Wb2wwajVrdHc4RHA4WERoQzl2SnZCTE9JczFYdkQ1SVRsb0o0UXZuNnVUT0RSNHVwb0xBTjEvekVQeVY1TSIsIm1hYyI6IjkxYTZmYmEyMTk5ZGNlMzJjOGNmNDAwMDc4ZTc4MjhjODY2ODU3ZjhlNzViMDNlNWY1YjU5YjA5MzE1MDllMWUiLCJ0YWciOiIifQ==');
        // dd($b);
        // dd($a, $b, strlen($a));
        // $operator_token = $request->query->get('operator_token');
        // $operator_player_session = $request->query->get('operator_player_session');
        // $operator_player_param = $request->query->get('operator_player_param');
        // $game_id = $request->query->get('game_id');

        // $params = $request->query;
        if ($request->exists('player_session')) {
            $mcf = new MerchantCF();
            $params = $request->only(['player_session', 'operator_player_param', 'game_id', 'lang', 'broswer']);
            $params['ip'] = $this->ip($request);
            return $mcf->getVerifyPlayerSession($params);
        } else {
            $mcb = new MerchantCB();
            $params = $request->only(['operator_token', 'operator_player_session', 'operator_player_param', 'game_id', 'lang', 'client', 'broswer']);
            $params['ip'] = $this->ip($request);
            return $mcb->getVerifySession($params);
        }
        // return ['error' => null, $operator_token];
        // return ['error' => ['code' => 1000, 'message' => 'xx error'], 'data' => null];

        // return ["error" => null, "data" => ['token' => '']];
    }

    /**
     {"operator_token": "tongits", 
"secret_key": "wF6i8NpJRYZjxcrRdCtQzGAyycjDAtAz", 
"player_session": "1", 
"operator_player_session": "1", 
"player_name": "player123", 
"currency": "PHP", 
"nickname": "player123_nickname"}
     */
    public function loginGame(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'player_session', 'operator_player_session',
            'player_name', 'currency', 'nickname', 'avatar', 'trace_id', 'lang', 'client'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->loginGame($data);
    }

    /**
     {
"operator_token": "tongits", 
"secret_key": "wF6i8NpJRYZjxcrRdCtQzGAyycjDAtAz", 
"player_name": "player124"
}
     */
    public function getPlayerWallet(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'player_name', 'trace_id', 'lang'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->getPlayerWallet($data);
    }

    /**
     {
"operator_token": "tongits", 
"secret_key": "wF6i8NpJRYZjxcrRdCtQzGAyycjDAtAz", 
"player_name": "player124",
"amount": 100,
"currency": "PHP",
"transfer_reference": "123456ba"
}
     */
    public function transferIn(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'player_name', 'amount',
            'transfer_reference', 'currency', 'trace_id', 'lang'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->transferIn($data);
    }

    /**
     {
"operator_token": "tongits", 
"secret_key": "wF6i8NpJRYZjxcrRdCtQzGAyycjDAtAz", 
"player_name": "player124",
"amount": 100,
"currency": "PHP",
"transfer_reference": "12345b789a"
}
     */
    public function transferOut(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'player_name', 'amount',
            'transfer_reference', 'currency', 'trace_id', 'lang'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->transferOut($data);
    }

    public function getHistory(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'start_time', 'end_time',
            'trace_id', 'client'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->getHistory($data);
    }

    public function getGameDetail(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'start_time', 'end_time',
            'trace_id', 'client'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->getGameDetail($data);
    }

    public function getDataReport(Request $request)
    {
        $mcf = new MerchantCF();
        $data = $request->only([
            'operator_token', 'secret_key',
            'start_date', 'end_date',
            'trace_id', 'client'
        ]);
        $data['ip'] = $this->ip($request);
        return $mcf->getDataReport($data);
    }

    public function redirect(Request $request)
    {
        if ($request->exists('player_session')) {
            $mcf = new MerchantCF();
            $params = $request->only(['player_session', 'operator_player_param', 'game_id', 'browser']);
            $params['ip'] = $this->ip($request);
            $res = $mcf->redirect($params);
        } else {
            $mcb = new MerchantCB();
            $params = $request->only(['operator_token', 'operator_player_session', 'operator_player_param', 'game_id', 'browser']);
            $params['ip'] = $this->ip($request);
            $res = $mcb->redirect($params);
        }

        $newRedirect = env('NEW_REDIRECT', 0);
        if ($res['error'] === null && $newRedirect == 0) {
            $gamePaths = config('gamepaths');
            $gameId = $request->get('game_id', 0);
            $path = $gamePaths[$gameId] ?? $gamePaths[0];
            $uri = str_replace('/web-lobby', '/' . $path, $_SERVER['REQUEST_URI']);
            $url = env('DOMAIN_GAME') . $uri;
            return redirect($url);
        }

        if ($res['error'] === null && $newRedirect == 1) {
            $gameId = $request->get('game_id', 0);
            $browser = strtolower($request->get('browser', 'h5'));
            $uri = str_replace('/web-lobby', '/' . $browser .'/' . $gameId, $_SERVER['REQUEST_URI']);
            $url = env('DOMAIN_GAME') . $uri;
            return redirect($url);
        }
        return $res['error'];
    }

    // public function 
    public function test1(Request $request)
    {
        // return ['data' => [
        //     'player_name' => '200001',
        //     'nickname' => 'bb999',
        //     'currency' => 'PHP',
        // ], 'error' => null];

        // return ['uuid1' => Str::uuid(), 'uuid2' => Str::uuid(), 'uuid3' => Str::uuid(), 'uuid4' => Str::uuid()];
        return ['test' => Str::uuid()];
    }
}
