<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Library\MerchantCB;
use App\Http\Library\MerchantCF;
use Hashids\Hashids;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Jobs\CashTransferInOutNotify;
use Artisan;
use Illuminate\Support\Facades\Redis;

class ServerNotifyController extends Controller
{
    public function CashGet(Request $request)
    {
        $uid = $request->query->get('uid');
        $gameId = $request->query->get('game_id');
        // $t = $request->query->get('t');
        // $s = $request->query->get('s');

        // $hashids = new Hashids(env('SERVER_HASH_IDS_SALT'), 32, env('SERVER_HASH_IDS_STR_TABLE'));
        // $t = $hashids->encode($t);
        $mcb = new MerchantCB();
        $mcb->setType('server');
        return $mcb->getCashGet($uid, $gameId);
    }
    
    /**
     * {"bet_amount":100,"bet_id":"0","bill_type":502,"game_id":3001,
     * "is_end":1,"parent_bet_id":"BC344A67-E62D-4091-BC37-CCAD4A4803DA","player_uid":120,"token":0,
     * "transaction_id":"{0}-{BC344A67-E62D-4091-BC37-CCAD4A4803DA}-{502}-{0}","transfer_amount":-100}
     *
     * @param Request $request
     * @return void
     */
    public function CashTransferInOutByQueue(Request $request)
    {
        $data = file_get_contents('php://input');
        $data = (array) json_decode($data, true);

        if (empty($data)) {
            $data = $request->all();
        }
        $redis = Redis::connection();
        $validator = Validator::make(
            $data,
            [
                'bet_amount' => ['required'],
                'bet_id' => ['required'],
                'bill_type' => ['required'],
                'game_id' => ['required'],
                'is_end' => ['required'],
                'parent_bet_id' => ['required'],
                'player_uid' => ['required'],
                // 'token' => ['required'],
                'transaction_id' => ['required'],
                'transfer_amount' => ['required'],
            ]
        );

        $queue = '';
        if ($validator->fails()) {
            $return = ['status' => 1, 'data' => [], 'desc' => $validator->errors()->keys()[0] . ' ' . $validator->errors()->first()];
        } else {
            $mcb = new MerchantCB();
            $mcb->setType('server');
            $data['getResult'] = 1;
            $uid = $data['player_uid'] ?? 0;
            $clientId = (int) $redis->hget($uid, 'play_type_id');
            $data['client_id'] = $clientId;
            $queue = $mcb->getQueueName($clientId, $uid);
            if ($clientId > 0) {
                \App\Jobs\CashTransferInOutNotifyBatch::dispatch($data)->onQueue($queue);
                $return = $mcb->getCashTransferInOutByWait($data);
            } else {
                $return = ['status' => 1, 'data' => [], 'desc' => "client_id: {$clientId} uid: {$uid}"];
            }
        }
        // Log::info('CashTransferInOutByQueue', [$data, $return, $this->ip($request), $queue]);
        return $return;
    }

    public function CashTransferInOutBatch(Request $request)
    {
        $data = file_get_contents('php://input');
        $data = (array) json_decode($data, true);

        if (empty($data)) {
            $data = $request->all();
        }

        $redis = Redis::connection();
        if (empty($data) || !is_array($data)) {
            return ['status' => 1, 'data' => [], 'desc' => 'data error'];
        }
        $mcb = new MerchantCB;
        foreach ($data as $v) {
            $validator = Validator::make(
                $v,
                [
                    'bet_amount' => ['required'],
                    'bet_id' => ['required'],
                    'bill_type' => ['required'],
                    'game_id' => ['required'],
                    'is_end' => ['required'],
                    'parent_bet_id' => ['required'],
                    'player_uid' => ['required'],
                    // 'token' => ['required'],
                    'transaction_id' => ['required'],
                    'transfer_amount' => ['required'],
                ]
            );

            if ($validator->fails()) {
                continue;
            }

            $clientId = (int) $redis->hget($v['player_uid'], 'play_type_id');
            $uid = $v['player_uid'] ?? 0;
            $queue = $mcb->getQueueName($clientId, $uid);
            \App\Jobs\CashTransferInOutNotifyBatch::dispatch($v)->onQueue($queue);
        }
        return ['status' => 0, 'data' => [], 'desc' => 'task running'];
    }

    public function VerifySession(Request $request)
    {
        $uid = (int) $request->get('uid', 0);
        $mcf = new MerchantCF();
        $mcf->setType('server');
        return $mcf->verifySession($uid);
    }
}
