<?php

namespace App\Http\Library;

use App\Models\Account;
use App\Models\AccountExt;
use App\Models\AccountsToday;
use App\Models\Customer;
use App\Models\CustomerSub;
use App\Models\TransferInOut;
use App\Models\ServerRequestLog;
use App\Models\ServerPostLog;
use App\Models\TransferInOutServerRequestLog;
use Illuminate\Validation\Rule;
use Hashids\Hashids;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\GuzzleException;
use Artisan;
use Illuminate\Support\Facades\Log;
use App;

class MerchantCB
{
    protected $errors = [
        800 => 'Query exception',
        1000 => 'Merchant does not exist',
        1001 => 'Data is not legal',
        900 => 'Merchant Interface error',
        901 => 'Merchant Interface network exception #1',
        902 => 'Merchant Interface network exception #2',
        1007 => 'Request limit, please try again in 15 seconds',
        1008 => 'Request limit, please try again in 1 minutes',
        1010 => 'merchant is locked',
        1011 => 'api mode exception',
        1012 => 'The game doesn\'t start',
        1013 => 'Request limit, please try again in 30 seconds',
        1014 => 'sub-client is locked',
        1015 => 'sub-client is undefined',
        701 => 'player exception',
        702 => 'player session exception',
        703 => 'player operator token exception',
    ];

    protected $langs = ['en', 'es', 'th'];

    protected $currencys = ['INR', 'THB', 'PHP'];

    private $type = '';

    protected $serverRequestLog;

    protected $queueName = '-';

    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function error($code, $msg)
    {
        if (empty($msg) && isset($this->errors[$code])) {
            // $msg = $this->errors[$code];
            $msg = __('api.' . $code);
        }

        if ($this->type == 'server') {
            return $this->serverError($code, $msg);
        }

        return ['error' => ['code' => $code, 'message' => $msg], 'data' => null];
    }

    public function success($data)
    {
        if ($this->type == 'server') {
            return $this->serverSuccess($data);
        }
        return ['error' => null, 'data' => $data];
    }

    public function successAttr($data, $attr = [])
    {
        return ['error' => null, 'data' => $data, 'attr' => $attr];
    }

    private function serverError($code, $msg)
    {
        return ['status' => 1, 'data' => [], 'desc' => '', 'error' => ['code' => $code, 'message' => $msg]];
    }

    private function serverSuccess($data)
    {
        return ['status' => 0, 'data' => $data, 'desc' => 'success', 'error' => null];
    }

    public function redirect($data)
    {
        $start = microtime(true);
        $errorCode = null;
        $errorText = null;
        $sClientId = 0;
        $redis = Redis::connection();

        $validator = Validator::make($data, [
            'operator_token' => ['required', 'max:32'],
            'operator_player_session' => ['required', 'max:32'],
            'operator_player_param' => ['max:512'],
            'game_id' => ['integer'],
            'browser' => [Rule::in(['h5', 'pc'])],
            'ip' => ['required', 'ip'],
        ]);

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($data['operator_token']);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 0) {
                $errorCode = 1011;
            }
        }

        if (!empty($sClientId)) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostLog = new ServerPostLog();
            $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['operator_player_session'],
                'uid' => 0,
                'client_id' => 0,
                'type' => 5,
                'arg' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'ip' => $data['ip'],
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'cost_time' => intval((microtime(true) - $start) * 1000),
            ]);
        }

        if ($errorCode === null) {
            return $this->success([]);
        } else {
            Log::warning("CB|redirect", [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }


    /**
     * post @merchantAddr/VerifySession
     * 
     * data [ operator_token \ secret_key \ operator_player_session \ custom_parameter \ ip]
     * @return void
     */
    public function getVerifySession($data, $pid = 0)
    {
        $serverRequestLog = new ServerRequestLog;
        $errorCode = null;
        $errorText = '';
        $sLevel = ServerRequestLog::DEBUG;
        $sClientId = 0;
        $sType = 1;
        $sUrl = 'getVerifySession';
        $sMethod = 'POST';
        $sParams = '';
        $sResponse = '';
        $sCode = 0;
        $uid = 0;

        $sClient = $data['client'] ?? '';
        $sClientIdSub = 0;

        $validator = Validator::make($data, [
            'operator_token' => ['required', 'max:32'],
            'operator_player_session' => ['required', 'max:32'],
            'operator_player_param' => ['max:512'],
            'game_id' => ['integer'],
            'lang' => ['string', 'max:10', Rule::in($this->langs)],
            'client' => ['string', 'max:10'],
            'browser' => [Rule::in(['h5', 'pc'])],
            'ip' => ['required', 'ip'],
        ]);

        $lang = $this->setLang($data['lang'] ?? 'en');
        $data['game_id'] = $data['game_id'] ?? 0;

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($data['operator_token']);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 0) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null && !empty($sClient)) {
            $customerSub = CustomerSub::getCustomerSubByCache($customer['id'], $sClient);
            if (empty($customerSub)) {
                $errorCode = 1015;
            } else {
                $sClientIdSub = $customerSub['id'];
            }
        }

        if ($errorCode === null && !empty($sClient)) {
            if ($customerSub['is_lock'] == 1) {
                $errorCode = 1014;
            }
        }

        if ($errorCode === null) {
            $gameIds = $customer['game_oc'] ?? '4201';
            $gameIds = !empty($gameIds) ? explode(',', $gameIds) : [];
        }

        if ($errorCode === null && $data['game_id'] != 0 && !in_array($data['game_id'], $gameIds)) {
            $errorCode = 1012;
        }

        if ($errorCode === null && $pid == 0 && $this->requestLimit($data['ip'], 'limit:', 60, 60) == false) {
            $errorCode = 1008;
        }

        if ($errorCode === null) {
            $sLevel = ServerRequestLog::INFO;
            $redis = Redis::connection();

            $client = new Client([
                'timeout'  => env('API_REQUEST_TIME_OUT', 8.0),
                'headers' => [
                    'User-Agent' => env('API_REQUEST_NAME', 'IG GAME'),
                ]
            ]);

            $d = $serverRequestLog->start($sClientId, 't1');
            $tid = $pid == 0 ? $d : $pid;
            try {
                // $traceId = $redis->incr('traceidplus');
                $sParams = [
                    'form_params' => [
                        // 'traceId' => $traceId,
                        // 'traceId' => $serverRequestLog->getTraceId($tid),
                        'operator_token' => $customer['operator_token'],
                        'secret_key' => decrypt($customer['secret_key']),
                        'operator_player_session' => $data['operator_player_session'],
                        'custom_parameter' => $data['operator_player_param'] ?? '',
                        'lang' => $lang,
                        'ip' => $data['ip'],
                    ]
                ];

                if (!empty($sClient)) {
                    $sParams['form_params']['client'] = $sClient;
                }

                $sUrl = $customer['merchant_addr'] . 'VerifySession';

                $response = $client->request($sMethod, $sUrl, $sParams);
                $sCode = $response->getStatusCode();
                $sResponse = (string) $response->getBody()->getContents();

                $res = json_decode($sResponse, true);
                $error = ($res['error'] ?? null);
                if ($error != null) {
                    // return $this->error(900, 'Interface error:' . $customer['merchant_addr'] . 'VerifySession' . $error . $response->getBody()->getContents());
                    $errorCode = 900;
                }

                if ($errorCode === null) {
                    $return = $res['data'] ?? [];
                    // if (empty($return['player_name']) || empty($return['nickname']) || empty($return['currency'])) {
                    //     $errorCode = 1001;
                    // }
                    $validator = Validator::make($return, [
                        'player_name' => ['required', 'string', 'max:50'],
                        'nickname' => ['required', 'string', 'max:50'],
                        'currency' => ['required', 'string', 'max:20', Rule::in($this->currencys)],
                    ]);

                    if ($validator->fails()) {
                        $errorCode = 1001;
                        $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
                    }
                }
            } catch (RequestException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = Psr7\Message::toString($e->getResponse());
                $errorCode = 901;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            } catch (GuzzleException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = $e->getMessage();
                $errorCode = 902;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            }
        }

        if ($errorCode === null) {
            $account = new Account();
            $accountExt = new AccountExt();
            // $accountData = $account->where('player_name', $return['player_name'])
            //     ->where('client_id', $customer['id'])->first();
            $accountData = Account::getAccountByClientIdAndPlayerName($customer['id'], $return['player_name'], true);
            if (empty($accountData)) {

                // $uid = $redis->incr('uidplus');
                $accountData = $account->create([
                    // 'uid' => $uid,
                    'player_name' => $return['player_name'],
                    'nickname' => $return['nickname'] ?? '',
                    'avatar' => $return['avatar'] ?? '',
                    'account_type' => 2,
                    'client_id' => $customer['id'],
                ]);
                // dd($accountData);

                $accountExt->create([
                    'uid' => $accountData->uid,
                    'client_id' => $customer['id'],
                    'client_id_sub' => $sClientIdSub,
                    'game_id' => $data['game_id'] ?? 0,
                    'game_version' => $data['game_version'] ?? '',
                    'os' => $data['os'] ?? 0, // 2安卓、3IOS、255PC
                    'register_time' => date('Y-m-d H:i:s'),
                    'last_logon_time' => date('Y-m-d H:i:s'),
                    'last_logon_ip' => $data['ip'],
                    'register_ip' => $data['ip'],
                    'currency' => $data['currency'] ?? 'PHP',
                    'lang' => $lang,
                ]);
            } else {
                $db = DB::reconnect('Master');
                $sql = "UPDATE account_ext SET
                prev_logon_time2 = CASE WHEN DATE(last_logon_time) != ? THEN prev_logon_time ELSE prev_logon_time2 END
                ,prev_logon_time = CASE WHEN DATE(last_logon_time) != ? THEN last_logon_time ELSE prev_logon_time END
                ,last_logon_time = ?, last_logon_ip = ?, client_id_sub = ?, currency = ?, lang = ?
                WHERE uid = ?";
                $db->update($sql, [
                    date('Y-m-d'),
                    date('Y-m-d'),
                    date('Y-m-d H:i:s'),
                    $data['ip'],
                    $sClientIdSub,
                    $data['currency'] ?? 'PHP',
                    $lang,
                    $accountData->uid,
                ]);
            }

            if ($accountData) {
                $hashids = new Hashids(env('SERVER_HASH_IDS_SALT'), 32, env('SERVER_HASH_IDS_STR_TABLE'));
                $hid = $hashids->encode($accountData->uid);
            }

            $uniqueString = md5(microtime() . rand(0, 99999999));
            $uid = $accountData->uid;
            $redis->hset($accountData->uid, 'player_name', $return['player_name']);
            $redis->hset($accountData->uid, 'nickname', $data['nickname'] ?? '');
            $redis->hset($accountData->uid, 'play_type', $customer['operator_token']);
            $redis->hset($accountData->uid, 'avatar', $data['avatar'] ?? '');
            $redis->hset($accountData->uid, 'play_type_id', $customer['id']);
            $redis->hset($accountData->uid, 'play_type_sub_id', $sClientIdSub);
            $redis->hset($accountData->uid, 'api_mode', $customer['api_mode']);
            $redis->hset($accountData->uid, 'ip', $data['ip'] ?? '');
            $redis->hset($accountData->uid, 'currency', $data['currency'] ?? 'PHP');
            $redis->hset($accountData->uid, 'lang', $lang);
            $redis->setex('web_player_session:' . $accountData->uid, 7 * 86400, $data['operator_player_session']);
            $redis->setex('web_player_token:' . $accountData->uid, 7 * 86400, $uniqueString);
            $token = $hid . $uniqueString;
            // return $this->success(['token' => $token, 'uid' => $accountData->uid, 'hid' => $hid, 'uniqueString' => $uniqueString]);
        }

        if ($sClientId > 0) {
            $serverRequestLog->record(
                't1',
                $sLevel,
                $pid,
                $sClientId,
                $sClientIdSub,
                $uid,
                $sType,
                $sUrl,
                $sMethod,
                $sParams,
                $sResponse,
                $sCode,
                (int) $errorCode,
                $errorText,
                [$data]
            );
        }

        if ($errorCode == null) {
            $return = [
                'token' => $token,
                'avatar' => $return['avatar'] ?? '',
                'nickname' => $return['nickname'],
                'ip' => $data['ip'],
                'client_id' => $sClientId,
                'client_id_sub' => $sClientIdSub,
            ];

            if ($data['game_id'] == 0) {
                $return['game_oc'] = $gameIds;
                // $return['gold'] = (int) $redis->hget($uid, 'gold');
                $res = $this->getCashGet($uid, $data['game_id']);
                if ($res['error'] === null) {
                    $return['gold'] = (int) $res['data']['balance_amount'];
                } else {
                    $return['gold'] = 0;
                }
            }
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            Log::warning("CF|getVerifySession", [$data, $pid, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    /**
     * 取钱包
     * post @merchantAddr/Cash/Get
     */
    public function getCashGet($uid, $gameId, $pid = 0)
    {
        $serverRequestLog = new ServerRequestLog;
        $errorCode = null;
        $errorText = '';
        $sLevel = ServerRequestLog::DEBUG;
        $sClientId = 0;
        $sType = 2;
        $sUrl = 'Cash/Get';
        $sMethod = 'POST';
        $sParams = '';
        $sResponse = '';
        $sCode = 0;

        $redis = Redis::connection();
        $playerName = $redis->hget($uid, 'player_name');
        $operatorToken = $redis->hget($uid, 'play_type');
        $lang =  $redis->hget($uid, 'lang');
        $operatorPlayerSession = $redis->get('web_player_session:' . $uid);
        $sClientIdSub = (int) $redis->hget($uid, 'play_type_sub_id');

        if (empty($playerName)) {
            $errorCode = 701;
        }

        if ($errorCode == null && empty($operatorPlayerSession)) {
            $errorCode = 702;
        }

        if ($errorCode == null && empty($operatorToken)) {
            $errorCode = 703;
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($operatorToken);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 0) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null) {
            $sLevel = ServerRequestLog::INFO;
            $client = new Client([
                'timeout'  => env('API_REQUEST_TIME_OUT', 8.0),
                'headers' => [
                    'User-Agent' => env('API_REQUEST_NAME', 'IG GAME'),
                ]
            ]);

            $redis = Redis::connection();
            try {
                $d = $serverRequestLog->start($sClientId, 't1');
                $tid = $pid == 0 ? $d : $pid;
                $traceId = $serverRequestLog->getTraceId($tid);
                $sUrl = $customer['merchant_addr'] . 'Cash/Get?trace_id=' . $traceId;
                $sParams = [
                    'form_params' => [
                        // 'traceId' => $traceId,
                        'operator_token' => $customer['operator_token'],
                        'secret_key' => decrypt($customer['secret_key']),
                        'operator_player_session' => $operatorPlayerSession,
                        'player_name' => $playerName,
                        'game_id' => $gameId,
                        'lang' => $lang ?? 'en',
                    ]
                ];

                $response = $client->request($sMethod, $sUrl, $sParams);

                $sCode = $response->getStatusCode();
                $sResponse = (string) $response->getBody()->getContents();
                $response = json_decode($sResponse, true);

                $error = ($response['error'] ?? null);
                if ($error != null) {
                    $errorCode = 900;
                }

                if ($errorCode === null) {
                    $return = $response['data'] ?? [];

                    $validator = Validator::make($return, [
                        'updated_time' => ['required', 'string', 'date_format:Y-m-d H:i:s'],
                        'balance_amount' => ['required', 'integer'],
                        'currency' => ['required', 'string', 'max:20', Rule::in($this->currencys)],
                    ]);

                    if ($validator->fails()) {
                        $errorCode = 1001;
                        $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
                    }
                }
            } catch (RequestException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = Psr7\Message::toString($e->getResponse());
                $errorCode = 901;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            } catch (GuzzleException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = $e->getMessage();
                $errorCode = 902;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            }
        }

        if ($sClientId > 0) {
            $serverRequestLog->record(
                't1',
                $sLevel,
                $pid,
                $sClientId,
                $sClientIdSub,
                $uid,
                $sType,
                $sUrl,
                $sMethod,
                $sParams,
                $sResponse,
                $sCode,
                (int) $errorCode,
                $errorText,
                [$uid, $gameId]
            );
        }

        if ($errorCode === null) {
            $return['uid'] = $uid;
            return $this->success($return);
        } else {
            Log::warning("CB|getCashGet", [$uid, $gameId, $pid, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function isNeedToRetry($return)
    {
        $code = isset($return['error']) && isset($return['error']['code']) ? $return['error']['code'] : -1;
        if (in_array($code, $this->getRetryStatusCode())) {
            return true;
        }
        return false;
    }


    protected function getRetryStatusCode()
    {
        return [900, 901, 902];
    }

    public function getQueueName($clientId, $uid)
    {
        $customer = Customer::getCustomerById($clientId);
        $configs = json_decode($customer['configs'] ?? '', true);
        $num = 0;
        if (
            $clientId > 0 && $uid > 0 && !empty($configs) &&
            isset($configs['Queue']) &&
            isset($configs['Queue']['normal'])
        ) {
            $qs = explode(',', $configs['Queue']['normal']);
            $len = count($qs);
            $n = $uid % $len;
            $num = intval($qs[$n]);
            // dd([$uid, $qs, $num]);
        }
        return env('QUEUE_NAME_PREFIX') . 'notify_' . $num;
    }

    public function getQueueNameByFailTask($clientId, $uid)
    {
        $customer = Customer::getCustomerById($clientId);
        $configs = json_decode($customer['configs'] ?? '', true);
        $num = 0;
        if (
            $clientId > 0 && $uid > 0 && !empty($configs) &&
            isset($configs['Queue']) &&
            isset($configs['Queue']['fail'])
        ) {
            $qs = explode(',', $configs['Queue']['fail']);
            $len = count($qs);
            $n = $uid % $len;
            $num = intval($qs[$n]);
        }
        return env('QUEUE_NAME_PREFIX') . 'fail_notify_' . $num;
    }

    public function getCashTransferInOutByWait($data)
    {
        $errorCode = 801;
        $redis = Redis::connection();
        $uid = $data['player_uid'] ?? 0;
        $clientId = (int) $redis->hget($uid, 'play_type_id');
        $queue = $this->getQueueName($clientId, $uid);
        $errorText = 'queue timeout error:' . md5($data['transaction_id']) . ':' . $queue;
        $redis = Redis::connection('cache');
        $timeout = intval(env('API_REQUEST_TIME_OUT', 8.0) + 5);
        $time = time();
        while (true) {
            // sleep(0.15);
            usleep(10000); // 0.15s
            $return = $redis->get(md5($data['transaction_id']));
            if (!empty($return)) {
                return json_decode($return, true);
            }

            if (time() - $time > $timeout) {
                Log::error('CB|getCashTransferInOutByWait', [$data, $errorCode, $errorText]);
                return $this->error($errorCode, $errorText);
            }
        }
    }

    /**
     * 投付
     * post @merchantAddr/Cash/TransferInOut
     */
    public function getCashTransferInOut($data, $pid = 0)
    {
        $isValidateBet = $pid == 0 ? false : true;
        $serverRequestLog = new ServerRequestLog;
        $errorCode = null;
        $errorText = '';
        $sLevel = ServerRequestLog::DEBUG;
        $sClientId = 0;
        $sType = 3;
        $sUrl = 'getCashTransferInOut';
        $sMethod = 'POST';
        $sParams = '';
        $sResponse = '';
        $sCode = 0;

        $uid = $data['player_uid'];
        // $return = [
        //     "currency_code" => "CNY",
        //     "balance_amount" => "{{AMOUNT}}",
        //     "updated_time" => time() * 1000,
        // ];

        $redis = Redis::connection();

        $client = new Client([
            'timeout'  => env('API_REQUEST_TIME_OUT', 8.0),
            'headers' => [
                'User-Agent' => env('API_REQUEST_NAME', 'IG GAME'),
            ]
        ]);

        $playerName = $redis->hget($uid, 'player_name');
        $operatorToken = $redis->hget($uid, 'play_type');
        $sClientIdSub = (int) $redis->hget($uid, 'play_type_sub_id');
        $currency = $redis->hget($uid, 'currency');
        $lang = $redis->hget($uid, 'lang');
        $operatorPlayerSession = $redis->get('web_player_session:' . $uid);

        if (empty($playerName)) {
            $errorCode = 701;
        }

        if ($errorCode == null && empty($operatorPlayerSession)) {
            $errorCode = 702;
        }

        if ($errorCode == null && empty($operatorToken)) {
            $errorCode = 703;
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($operatorToken);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 0) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null) {
            try {
                $d = $serverRequestLog->start($sClientId, 't1');
                $tid = $pid == 0 ? $d : $pid;
                $traceId = $serverRequestLog->getTraceId($tid);
                // $traceId = $redis->incr('traceidplus');
                $sUrl = $customer['merchant_addr'] . 'Cash/TransferInOut?trace_id=' . $traceId;
                $sParams = [
                    'form_params' => [
                        'operator_token' => $customer['operator_token'],
                        'secret_key' => decrypt($customer['secret_key']),
                        'operator_player_session' => $operatorPlayerSession,
                        'player_name' => $playerName,
                        'game_id' => $data['game_id'],
                        'currency' => $currency ?? 'PHP',

                        'bet_amount' => $data['bet_amount'],  // 投注金额
                        // 'win_amount' => 0, // 赢的金额
                        'transfer_amount' => $data['transfer_amount'], // 输赢金额
                        'transaction_id' =>  $data['transaction_id'], // 交易的唯一标识符 {BetId}-{ParentBetId}-{transactionType}-{balanceId}
                        'bet_type' => $data['bill_type'], // 投注类型
                        'parent_bet_id' =>  $data['parent_bet_id'], // 母注单号
                        'bet_id' => $data['bet_id'], // 子投注的唯一标识符
                        // 'updated_time' => '', // 投注最近更新的时间
                        'is_validate_bet' => $isValidateBet, // 表示该请求是否是为进行验证而重新发送的交易 True: 重新发送的交易 False: 正常交易
                        'is_wager' => $data['bet_amount'] > 0 ? true : false, // 表示该交易是否为投注 True: bet_amount > 0 False: bet_amount = 0
                        'is_adjustment' => (bool) ($data['is_adjustment'] ?? false), // 表示该请求是否是待处理投注的调整或正常交易 True: 调整 False: 正常交易
                        'lang' => $lang ?? 'en',

                    ]
                ];
                $response = $client->request($sMethod, $sUrl, $sParams);
                $sCode = $response->getStatusCode();
                $sResponse = (string) $response->getBody()->getContents();
                $response = json_decode($sResponse, true);

                $error = ($response['error'] ?? null);
                if ($error != null) {
                    $errorCode = 900;
                }

                if ($errorCode === null) {
                    $return = $response['data'] ?? [];

                    $validator = Validator::make($return, [
                        'updated_time' => ['required', 'date_format:Y-m-d H:i:s'],
                        'balance_amount' => ['required', 'integer'],
                        'currency' => ['required', Rule::in($this->currencys)],
                    ]);

                    if ($validator->fails()) {
                        $errorCode = 1001;
                        $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
                    }
                }
            } catch (RequestException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = Psr7\Message::toString($e->getResponse());
                $errorCode = 901;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            } catch (GuzzleException $e) {
                $sLevel = ServerRequestLog::ERROR;
                $m1 = Psr7\Message::toString($e->getRequest());
                $m2 = $e->getMessage();
                $errorCode = 902;
                $sCode = 500;
                $sResponse = $m1 . '|' . $m2;
            }
        }

        if ($sClientId > 0) {
            $serverRequestLog->record(
                't1',
                $sLevel,
                $pid,
                $sClientId,
                $sClientIdSub,
                $uid,
                $sType,
                $sUrl,
                $sMethod,
                $sParams,
                $sResponse,
                $sCode,
                (int) $errorCode,
                $errorText,
                [$data]
            );
        }
        $this->serverRequestLog = $serverRequestLog;
        if ($errorCode === null) {
            $transferInOutServerRequestLogTableName = 'transfer_inout_server_request_log_' . $sClientId;
            $transferInOutServerRequestLog = new TransferInOutServerRequestLog;
            $transferInOutServerRequestLog->setTable($transferInOutServerRequestLogTableName)->create([
                'transaction_id' => $data['transaction_id'],
                'server_request_log_id' => $d,
                'queue_name' => $this->queueName,
            ]);
        }

        if ($errorCode === null) {
            $transferInOutTableName = 'transfer_inout_' . $sClientId;
            $transferInOut = new TransferInOut();
            $transferInOut->setTable($transferInOutTableName)->where('transaction_id', $data['transaction_id'])->update(['status' => 1]);
            return $this->success($return);
        } else {
            Log::warning('CB|getCashTransferInOut', [$data, $pid, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getCashTransferInOutServerRequestLog()
    {
        return $this->serverRequestLog;
    }

    protected function requestLimit($uid, $prefix = 'requestLimit:', $time = 15, $cnt = 1)
    {
        $redis = Redis::connection('cache');
        $num = $redis->incr($prefix . $uid);
        $redis->expire($prefix . $uid, $time);
        return $num <= $cnt ? true : false;
    }

    protected function clearLimit($uid, $prefix = 'requestLimit:')
    {
        $redis = Redis::connection('cache');
        $redis->del($prefix . $uid);
    }

    protected function gameLock($uid)
    {
        // $accountsToday = new AccountsToday;
        // $res = $accountsToday->where('uid', $uid)->first();

        // if (!empty($res) && $res->roomid != 0) {
        //     return false;
        // }

        $redis = Redis::connection();
        // $roomId = (int) $redis->hget($uid, 'room_id');
        // if ($roomId > 0) {
        //     return false;
        // }
        $lock = $redis->hget($uid, 'game_status');
        return $lock >= 4  ? false : true;
    }

    protected function stopApiService()
    {
        if (env('AUTO_STOP_API_SERVICE', 1) == 1) {
            $redis = Redis::connection('cache');
            $redis->setex('stop_api_service', 86400, 1);
            $date = date('Y-m-d H:i:s');
            $hostname = gethostname();
            Artisan::queue('SendMessage', [
                'text' => '[ERROR][' . $date . '][' . $hostname . '] An error occurred on the server, and the WEB service automatically closed the API service！',
            ])->onConnection('redis')->onQueue('default');
        }
    }

    protected function setLang($lang)
    {
        $lang = empty($lang) ? 'en' : $lang;
        $lang = strtolower($lang);
        if (!in_array($lang, $this->langs)) {
            $lang = current($this->langs);
        }
        // echo $lang;exit;
        App::setLocale($lang);
        return $lang;
    }

    protected function getCurreny($currency)
    {
        if (!in_array($currency, $this->currencys)) {
            $currency = current($this->currencys);
        }
        return $currency;
    }
}
