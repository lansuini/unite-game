<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Library\MerchantCB;
use App\Models\TransferInOut;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Hashids\Hashids;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TestController extends Controller
{
    // use DispatchesJobs;

    public function VerifySession(Request $request)
    {
        $session = $request->input('operator_player_session');
        // sleep(10);
        // throw new Exception();
        $successData = ['data' => ['player_name'  => $session, 'nickname' => $session, 'currency' => 'PHP', 'avatar' => ''], 'error' => null];
        return $successData;
    }

    public function CFVerifySession(Request $request)
    {
        $session = $request->input('operator_player_session');
        // sleep(10);
        // throw new Exception();
        // abort(404);
        // abort(500);
        // $redis = Redis::connection();
        // $hashids = new Hashids(env('SERVER_HASH_IDS_SALT'), 32, env('SERVER_HASH_IDS_STR_TABLE'));
        // $uidStr = substr($session, 0, 32);
        // $uid = $hashids->decode($uidStr);
        // $uid = !empty($uid) ? current($uid) : 0;
        // $playerName = $redis->hset($uid, 'player_name');

        $successData = ['data' => ['player_name'  => $session, 'nickname' => $session, 'currency' => 'PHP', 'avatar' => ''], 'error' => null];
        return $successData;
    }

    public function CashGet(Request $request)
    {
        $redis = Redis::connection('cache');
        $playerName = $request->input('player_name');
        if ($redis->exists($playerName) == 0) {
            $redis->setex($playerName, 30 * 86400, 10000000);
        }

        $a = $redis->get($playerName);
        // $redis->expired($playerName, 30 * 86400);
        $successData = ['data' => ['balance_amount'  => intval($a), 'updated_time' => date('Y-m-d H:i:s'), 'currency' => 'PHP'], 'error' => null];
        return $successData;
    }

    public function CashTransferInOut(Request $request)
    {
        // abort(500);
        $r = rand(0, 1000);
        if ($r > 900) {
            abort(500);
        }
        usleep(rand(100000, 3000000));
        $redis = Redis::connection('cache');
        $playerName = $request->input('player_name');
        $transferAmount = $request->input('transfer_amount');

        if (empty($playerName) || empty($transferAmount)) {
            return ['data' => null, 'error' => ['code' => 11015, 'message' => 'data is empty']];
        }

        if ($redis->incr($playerName . ':lock') == 1) {

            if ($redis->exists($playerName) == 0) {
                $redis->setex($playerName, 30 * 86400, 1000000);
            }

            // $redis->incr($playerName, $transferAmount);
            // $redis->expired($playerName, 30 * 86400);
            $a = $redis->get($playerName);
            if ($a + $transferAmount < 0) {
                return ['data' => null, 'error' => ['code' => 11017, 'message' => 'Insufficient player balance']];
            }

            $redis->setex($playerName, 30 * 86400, $a + $transferAmount);
            $successData = ['data' => [
                'balance_amount'  => intval($a + $transferAmount),
                'updated_time' => date('Y-m-d H:i:s'), 'currency' => 'PHP', 'a' => $a, 't' => $transferAmount, 'p' => $playerName
            ], 'error' => null];
            $redis->decr($playerName . ':lock');
            $redis->expire($playerName . ':lock', 15);
            return $successData;
        } else {
            $redis->expire($playerName . ':lock', 15);
        }

        return ['data' => null, 'error' => [
            'code' => 11016,
            'message' => 'user money is locked:' . $redis->get($playerName . ':lock') . ':' . $redis->ttl($playerName . ':lock')
        ]];
    }

    public function test(Request $request)
    {
        // php /data/www/tongits-php/artisan queue:work redis --sleep=1 --tries=1 --queue=test
        \App\Jobs\TestJobs::dispatch([])->onQueue("test");
        exit;
        // usleep(rand(100000, 3000000));
        // echo 1;exit;
        // $r = \Artisan::queue('CashTransferInOutNotify', [
        //     'pid' => 915,
        // ])->onConnection('redis')->onQueue('failtask')->delay(3);

        // $traceId1 = $this->getTraceId(2);
        // $traceId2 = $this->getTraceId('e86joBOVx0qbMm5p2Wn9RYy7gPGp43da');

        // $traceId3 = $this->getTraceId2(2);
        // $traceId4 = $this->getTraceId2("e86joBOV-x0qb-Mm5p-2Wn9-RYy7gPGp43da");


        // $data = [$traceId1, $traceId2, $traceId3, $traceId4];
        // $data = ['trace_id' => "e86joBOV-x0qb-Mm5p-2Wn9-RYy7gPGp43da"];
        // $data = ['trace_id' => Str::uuid()];

        // $validator = Validator::make($request->all(), [
        //     'trace_id' => ['required', 'uuid'],
        // ]);

        // if ($validator->fails()) {
        //     return $validator->errors();
        // }
        // return [$request->all()];
        // echo '<pre>';
        // print_r($_SERVER);
        // echo PHP_EOL;
        // echo "IP:".$request->header('CF_CONNECTING_IP');
        // echo PHP_EOL;

        // $imageInfo = getimagesize('https://update.lodi291.ph/avatar/1.png');
        // print_r($imageInfo);

        // $base64 = "".chunk_split(base64_encode(file_get_contents('https://update.lodi291.ph/avatar/1.png')));
        // return 'data:'.$imageInfo['mime'].';base64,'.$base64;



        // $this->dispatch(new \App\Jobs\CashTransferInOutNotifyBatch(['foo' =>'bar']));
        // echo '111'. PHP_EOL;
        // \App\Jobs\CashTransferInOutNotifyBatch::dispatch(['foo' =>'bar'])->onQueue('hk');
        // echo '111'. PHP_EOL;
        // echo 1;exit;
        phpinfo();
        // \Illuminate\Support\Facades\Log::info('test');
        // $hostname = gethostname();
        // \Artisan::queue('SendMessage', [
        //     'text' => '[ERROR][' . $hostname . '] An error occurred on the server, and the WEB service automatically closed the API service！',
        // ])->onConnection('redis')->onQueue('default');

        // $mc = new MerchantCB();
        // $mc->stopApiService();

        // $transferInOut = new TransferInOut();
        // $transferInOut->setConnection('Master');
        // $transferInOut = $transferInOut->setTable('transfer_inout_4');
        // $data = $transferInOut->where('id','2450141')->first();
        // print_r($data);
        // $transferInOut->createTable(99);
    }

    public function getTraceId($id = '')
    {


        $hashids = new Hashids(env('SERVER_REQUEST_HASH_IDS_SALT'), 32, env('SERVER_REQUEST_HASH_IDS_STR_TABLE'));
        if (is_numeric($id)) {
            $ns = $hashids->encode($id);
        } else {
            $ns = $hashids->decode($id);
        }
        return $ns;
    }

    public function getTraceId2($id = '')
    {


        $hashids = new Hashids(env('SERVER_REQUEST_HASH_IDS_SALT'), 32, env('SERVER_REQUEST_HASH_IDS_STR_TABLE'));
        if (is_numeric($id)) {
            $ns = $hashids->encode($id);
            $ns = substr($ns, 0, 8) . '-' .  substr($ns, 8, 4) . '-' . substr($ns, 12, 4) . '-' . substr($ns, 16, 4) . '-' . substr($ns, 20, 12);
        } else {
            $id = str_replace('-', '', $id);
            $ns = $hashids->decode($id);
        }
        return $ns;
    }

    public function batchInsertTransferInout(Request $request)
    {
        $r = '{"api_mode":0,"bet_amount":100,"bet_id":"0","bill_type":54,
            "client_id":4,"game_id":4440,"is_end":1,"parent_bet_id":"B6-88-D2-88-FE-A3-4B-53-9B-29-E7-0A-31-3F-4D-3F",
            "player_uid":200506,"status":0,"token":"",
            "transaction_id":"{0}-{B6-88-D2-88-FE-A3-4B-53-9B-29-E7-0A-31-3F-4D-3F}-{54}-{bd88762378add03c7b0472bb4dbebb15}",
            "transfer_amount":-100}';

        $baseData = json_decode($r, true);

        // $baseData['balanceAfter'] = $baseData['last_gold'];
        // $baseData['balanceBefore'] = $baseData['now_gold'];


        $redis = Redis::connection('cache');
        $data = [];
        // $users = [];
        // for ($i = 0; $i < 30;$i++) {
        //     $users[] = ['player_name' => 'TEST-A123456-'.$i, 'uid' => 36 + $i];
        // }


        for ($i = 0; $i < 200; $i++) {
            $id = rand(0, 29);
            $playerName = 'TEST-A123456-' . $id;
            $uid = 36 + $id;
            $balanace = $redis->get($playerName);
            $r = rand(0, 1000);
            if ($r > 900) {
                $transferAmount = -100;
            } else {
                $transferAmount = 100;
            }

            if ($transferAmount > 0) {
                $baseData['bill_type'] = 70;
            } else {
                $baseData['bill_type'] = 54;
            }

            $baseData['transaction_id'] = 'PHPUnit-id-' . uniqid();
            $baseData['parent_bet_id'] = 'PHPUnit-parent-' . uniqid();
            $baseData['balanceAfter'] = $balanace + $transferAmount;
            $baseData['balanceBefore'] = $balanace;
            $baseData['player_uid'] = $uid;
            $baseData['client_id'] = 0;
            $baseData['transfer_amount'] = $transferAmount;
            $baseData['create_time'] = date('Y-m-d H:i:s');

            $data[] = $baseData;

            if ($transferAmount > 0) {
                $transferAmount = -10;
                $baseData['bill_type'] = 63;
                $baseData['transaction_id'] .= '-' . uniqid();
                $baseData['transfer_amount'] = -10;
                $baseData['balanceBefore'] = $baseData['balanceAfter'];
                $baseData['balanceAfter'] = $baseData['balanceAfter'] + $transferAmount;
                $data[] = $baseData;
            }
        }
        $transferInOut = new  \App\Models\TransferInOut;
        $transferInOut->setTable('transfer_inout_4')->insert($data);
        return $data;
    }
}
