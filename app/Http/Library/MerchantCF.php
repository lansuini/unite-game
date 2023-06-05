<?php

namespace App\Http\Library;

use App\Models\Account;
use App\Models\AccountExt;
use App\Models\Customer;
use App\Models\CustomerSub;
use App\Models\TransferInOut;
use App\Models\ServerPostLog;
use App\Models\ServerPostSubLog;
use App\Models\DataReport;
use App\Models\DataReportSub;
use App\Models\Logon;
use Hashids\Hashids;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use App\Models\ServerRequestLog;
use App\Models\GameDetails;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class MerchantCF extends MerchantCB
{
    protected $errors = [
        1000 => 'Merchant does not exist',
        1001 => 'Data is not legal',
        1002 => 'Player wallet does not exist',
        1003 => 'transfer failed, player is playing',
        1004 => 'The transfer request is in progress, please try again to check the latest status',
        1005 => 'Insufficient balance',
        1006 => 'Secret key error',
        1007 => 'Request limit, please try again in 15 seconds',
        1008 => 'Request limit, please try again in 1 minutes',
        1009 => 'IP limit',
        1010 => 'merchant is locked',
        1011 => 'api mode exception',
        1012 => 'The game doesn\'t start',
        1013 => 'Request limit, please try again in 30 seconds',
        1014 => 'sub-client is locked',
        1015 => 'sub-client is undefined',
        701 => 'player exception',
        702 => 'player session exception',
        703 => 'player operator token exception',
        704 => 'player name exception',
        900 => 'Merchant Interface error',
        901 => 'Merchant Interface network exception #1',
        902 => 'Merchant Interface network exception #2',
    ];

    public function redirect($data)
    {
        $start = microtime(true);
        $errorCode = null;
        $errorText = null;
        $sClientId = 0;
        $redis = Redis::connection();

        $validator = Validator::make($data, [
            'player_session' => ['required', 'max:64', 'min:64'],
            'operator_player_param' => ['max:512'],
            'game_id' => ['integer'],
            'browser' => [Rule::in(['h5', 'pc'])],
            'ip' => ['required'],
        ]);

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode == null) {
            $hashids = new Hashids(env('SERVER_HASH_IDS_SALT'), 32, env('SERVER_HASH_IDS_STR_TABLE'));
            $uidStr = substr($data['player_session'], 0, 32);
            $tokenStr = substr($data['player_session'], 32, 32);
            $uid = $hashids->decode($uidStr);
            $uid = !empty($uid) ? current($uid) : 0;
            if ($uid == 0) {
                $errorCode = 701;
            } else {
                $uniqueString = $redis->get('web_player_token:' . $uid);
                if ($uniqueString != $tokenStr) {
                    $errorCode = 702;
                }
            }
        }

        if ($errorCode == null) {
            $sClientId = $redis->hget($uid, 'play_type_id');
            $sUrl = $data['player_session'];
        }

        if (!empty($sClientId)) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostLog = new ServerPostLog();
            $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['player_session'],
                'uid' => $uid,
                'client_id' => (int) $redis->hget($uid, 'play_type_sub_id'),
                'type' => 4,
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
            Log::warning("CF|redirect", [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getVerifyPlayerSession($data)
    {
        $errorCode = null;
        $errorText = null;
        $redis = Redis::connection();

        $validator = Validator::make($data, [
            'player_session' => ['required', 'max:64', 'min:64'],
            'operator_player_param' => ['max:512'],
            'game_id' => ['integer'],
            'browser' => [Rule::in(['h5', 'pc'])],
            'ip' => ['required'],
        ]);

        $data['game_id'] = $data['game_id'] ?? 0;

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode == null) {
            $hashids = new Hashids(env('SERVER_HASH_IDS_SALT'), 32, env('SERVER_HASH_IDS_STR_TABLE'));
            $uidStr = substr($data['player_session'], 0, 32);
            $tokenStr = substr($data['player_session'], 32, 32);
            $uid = $hashids->decode($uidStr);
            $uid = !empty($uid) ? current($uid) : 0;
            if ($uid == 0) {
                $errorCode = 701;
            } else {
                $uniqueString = $redis->get('web_player_token:' . $uid);
                if ($uniqueString != $tokenStr) {
                    $errorCode = 702;
                }
            }
        }

        if ($errorCode == null) {
            $operatorToken = $redis->hget($uid, 'play_type');
            $customer = Customer::getCustomerByOperatorToken($operatorToken);
            if (empty($customer)) {
                $errorCode = 1000;
            }
        }

        if ($errorCode === null) {
            $gameIds = $customer['game_oc'] ?? '4201';
            $gameIds = !empty($gameIds) ? explode(',', $gameIds) : [];
        }

        if ($errorCode === null && $data['game_id'] != 0 && !in_array($data['game_id'], $gameIds)) {
            $errorCode = 1012;
        }

        if ($errorCode == null) {
            $redis->hset($uid, 'operator_player_param', $data['operator_player_param'] ?? '');
            $redis->hset($uid, 'ip', $data['ip'] ?? '');
            $res = $this->verifySession($uid);
            if ($res['error'] != null) {
                $errorCode = $res['error']['code'];
                $errorText = $res['error']['message'];
            }
        }

        if ($errorCode == null) {
            $return = [
                'token' => $data['player_session'],
                'nickname' => $redis->hget($uid, 'nickname'),
                'avatar'  => $redis->hget($uid, 'avatar'),
                'ip' => $data['ip'] ?? '',
                'client_id' => $redis->hget($uid, 'play_type_id'),
                'client_id_sub' => $redis->hget($uid, 'play_type_sub_id'),
            ];

            if ($data['game_id'] == 0) {
                $return['game_oc'] = $gameIds;
                $return['gold'] = (int) $redis->hget($uid, 'gold');
            }
        }

        if ($errorCode == null) {
            AccountExt::where('uid', $uid)->update(['last_logon_ip' => $data['ip'] ?? '']);
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            Log::warning('CF|getVerifyPlayerSession', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function verifySession($uid, $pid = 0)
    {
        $serverRequestLog = new ServerRequestLog;
        $errorCode = null;
        $errorText = '';
        $sLevel = ServerRequestLog::DEBUG;
        $sClientId = 0;
        $sType = 4;
        $sUrl = 'VerifySession';
        $sMethod = 'POST';
        $sParams = '';
        $sResponse = '';
        $sCode = 0;
        // $uid = 0;

        $sLevel = ServerRequestLog::INFO;
        $redis = Redis::connection();

        $client = new Client([
            'timeout'  => env('API_REQUEST_TIME_OUT', 8.0),
            'headers' => [
                'User-Agent' => env('API_REQUEST_NAME', 'IG GAME'),
            ]
        ]);

        $clientId = $redis->hget($uid, 'play_type_id');
        $sClientIdSub = $redis->hget($uid, 'play_type_sub_id');
        if (empty($clientId)) {
            $errorCode = 701;
        }

        if ($errorCode == null) {
            $customer = Customer::where('id', $clientId)->first()->toArray();
            $sClientId = $customer['id'];
            $operatorPlayerSession = $redis->get('web_player_session:' . $uid);

            if (empty($operatorPlayerSession)) {
                $errorCode = 703;
            }
        }

        $ip = $redis->hget($uid, 'ip');
        if ($errorCode === null && $pid == 0 && $this->requestLimit($ip, 'limit:', 60, 60) == false) {
            $errorCode = 1008;
        }

        if ($errorCode == null) {
            $d = $serverRequestLog->start($sClientId, 't1');
            $tid = $pid == 0 ? $d : $pid;
            try {
                // $traceId = $redis->incr('traceidplus');
                $sParams = [
                    'form_params' => [
                        // 'traceId' => $traceId,
                        'traceId' => $serverRequestLog->getTraceId($tid),
                        'operator_token' => $customer['operator_token'],
                        'secret_key' => decrypt($customer['secret_key']),
                        'operator_player_session' => $operatorPlayerSession,
                        'custom_parameter' => $redis->hget($uid, 'operator_player_param'),
                        'lang' => $redis->hget($uid, 'lang'),
                        'ip' => $ip,
                    ]
                ];

                if (!empty($sClientIdSub)) {
                    $d = CustomerSub::where('id', $sClientIdSub)->first();
                    if (!empty($d)) {
                        $sParams['form_params']['client'] = $d->symbol;
                    }
                }

                $sUrl = $customer['merchant_addr'] . 'VerifySession';
                // 1
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
                        'player_name' => ['required', 'max:50'],
                        'nickname' => ['required', 'max:50'],
                        'currency' => ['required', 'max:20'],
                    ]);

                    if ($validator->fails()) {
                        $errorCode = 1001;
                        $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
                    }

                    if ($errorCode === null) {
                        $playerName = $redis->hget($uid, 'player_name');
                        if ($playerName != $return['player_name']) {
                            $errorCode = 704;
                        }
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
                [$uid]
            );
        }

        if ($errorCode === null) {
            $redis->expire('web_player_session:' . $uid, 7 * 86400);
            return $this->success($res['data']);
        } else {
            Log::warning('CF|verifySession', [$uid, $pid, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function loginGame($data)
    {
        $start = microtime(true);
        $errorCode = null;
        $errorText = null;
        $sClientId = 0;
        $sClient = $data['client'] ?? '';
        $sClientIdSub = 0;

        $validator = Validator::make($data, [
            'operator_token' => ['required', 'string', 'max:32'],
            'secret_key' => ['required', 'string', 'max:32'],
            'operator_player_session' => ['required', 'string', 'max:32'],
            'player_name' => ['required', 'string', 'max:32'],
            'currency' => ['required', 'string', 'max:20', Rule::in($this->currencys)],
            'nickname' => ['required', 'string', 'max:32'],
            'avatar' => ['max:300'],
            'trace_id' => ['required', 'string', 'max:128', 'uuid'],
            'lang' => ['string', 'max:10', Rule::in($this->langs)],
            'client' => ['string', 'max:10'],
            'ip' => ['required'],
        ]);

        $lang = $this->setLang($data['lang'] ?? 'en');

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
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 1) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
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

        if ($errorCode === null && $this->requestLimit($customer['id'] . ':' . $data['player_name'], 'playerlimit:', 60, 60) == false) {
            $errorCode = 1008;
        }

        if ($errorCode == null) {

            $account = new Account();
            $accountExt = new AccountExt();
            // $accountData = $account->where('player_name', $data['player_name'])
            //     ->where('client_id', $customer['id'])->first();
            $accountData = Account::getAccountByClientIdAndPlayerName($customer['id'], $data['player_name'], true);

            if (empty($accountData)) {
                $accountData = $account->create([
                    // 'uid' => $uid,
                    'player_name' => $data['player_name'],
                    'nickname' => $data['nickname'] ?? '',
                    'avatar' => $data['avatar'] ?? '',
                    'account_type' => 2,
                    'client_id' => $customer['id'],
                ]);
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

            $redis = Redis::connection();
            $uniqueString = md5(microtime() . rand(0, 99999999));
            $redis->hset($accountData->uid, 'player_name', $data['player_name']);
            $redis->hset($accountData->uid, 'nickname', $data['nickname'] ?? '');
            $redis->hset($accountData->uid, 'avatar', $data['avatar'] ?? '');
            $redis->hset($accountData->uid, 'play_type', $customer['operator_token']);
            $redis->hset($accountData->uid, 'play_type_id', $customer['id']);
            $redis->hset($accountData->uid, 'play_type_sub_id', $sClientIdSub);
            $redis->hset($accountData->uid, 'api_mode', $customer['api_mode']);
            $redis->hset($accountData->uid, 'currency', $data['currency'] ?? 'PHP');
            $redis->hset($accountData->uid, 'lang', $lang);
            $redis->setex('web_player_session:' . $accountData->uid, 7 * 86400, $data['operator_player_session']);
            $redis->setex('web_player_token:' . $accountData->uid, 7 * 86400, $uniqueString);
            $token = $hid . $uniqueString;

            $return = [
                'player_name' => $data['player_name'],
                'player_session' => $token,
                'game_domain' => !empty($customer['game_domain']) ? $customer['game_domain'] : env('DOMAIN_GAME')
            ];
        }

        if (!empty($sClientId)) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostLog = new ServerPostLog();
            $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['trace_id'] ?? uniqid(),
                'uid' => !empty($accountData) ? $accountData->uid : 0,
                'client_id' => !empty($accountData) ? intval($redis->hget($accountData->uid, 'play_type_sub_id')) : 0,
                'type' => 0,
                'arg' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'return' => json_encode($return ?? [], JSON_UNESCAPED_UNICODE),
                'ip' => $data['ip'],
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'cost_time' => intval((microtime(true) - $start) * 1000),
            ]);
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|loginGame', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getPlayerWallet($data)
    {
        $redis = Redis::connection();
        $errorCode = null;
        $errorText = null;
        $start = microtime(true);
        // $traceId = 0;
        // $data['operator_token'];
        // $data['secret_key'];
        // $data['player_name'];
        $sClientId = 0;
        $validator = Validator::make($data, [
            'operator_token' => ['required', 'string', 'max:32'],
            'secret_key' => ['required', 'string', 'max:32'],
            'player_name' => ['required', 'string', 'max:32'],
            'lang' => ['string', 'max:10', Rule::in($this->langs)],
            'trace_id' => ['required', 'string', 'max:128', 'uuid'],
            'ip' => ['required'],
        ]);

        $this->setLang($data['lang'] ?? 'en');

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
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 1) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
            }
        }
        // if ($errorCode === null) {
        //     $spl = ServerPostLog::where('client_id', $sClientId)->where('trace_id', $data['trace_id'])->first();
        //     if (!empty($spl)) {
        //         $errorCode = -1;
        //     }
        // }

        if ($errorCode === null) {
            // $account = new Account();
            // $accountData = $account->where('player_name', $data['player_name'])
            //     ->where('client_id', $customer['id'])->first();
            $accountData = Account::getAccountByClientIdAndPlayerName($customer['id'], $data['player_name'], true);
            if (empty($accountData)) {
                $errorCode = 1002;
            }
        }

        if ($errorCode === null) {
            $balance = (int) $redis->hget($accountData->uid, 'gold');
            $currency = $redis->hget($accountData->uid, 'currency');
            $return = [
                'currency' => $currency ?? 'PHP',
                'totalBalance' => $balance,
            ];
        }

        if (!empty($sClientId)) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostLog = new ServerPostLog();
            $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['trace_id'] ?? uniqid(),
                'uid' => !empty($accountData) ? $accountData->uid : 0,
                'client_id' => !empty($accountData) ? intval($redis->hget($accountData->uid, 'play_type_sub_id')) : 0,
                'type' => 1,
                'arg' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'return' => json_encode($return ?? [], JSON_UNESCAPED_UNICODE),
                'ip' => $data['ip'],
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'cost_time' => intval((microtime(true) - $start) * 1000),
            ]);
        }

        // if ($errorCode === -1) {
        //     $errorCode = null;
        //     $return = json_decode($spl->return, true);
        // }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|getPlayerWallet', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function transferIn($data)
    {
        $errorCode = null;
        $errorText = null;
        $start = microtime(true);
        $sClientId = 0;
        $redis = Redis::connection();
        $validator = Validator::make($data, [
            'operator_token' => ['required', 'string', 'max:32'],
            'secret_key' => ['required', 'string', 'max:32'],
            'player_name' => ['required', 'string', 'max:32'],
            'amount' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'transfer_reference' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'max:20', Rule::in($this->currencys)],
            'trace_id' => ['required', 'string', 'max:128', 'uuid'],
            'lang' => ['string', 'max:10', Rule::in($this->langs)],
            'ip' => ['required', 'ip'],
        ]);

        $this->setLang($data['lang'] ?? 'en');

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
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null) {
            if ($customer['api_mode'] != 1) {
                $errorCode = 1011;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
            }
        }

        if ($errorCode === null) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostSubLogTableName = 'server_post_sub_log_' . $sClientId;
            $serverPostLog = new ServerPostLog();
            $spl = $serverPostLog->setTable($serverPostLogTableName)->select('return')
                // ->where($serverPostLogTableName . '.client_id', $sClientId)
                // ->where('trace_id', $data['trace_id'])
                ->where($serverPostSubLogTableName . '.transfer_reference', $data['transfer_reference'])
                ->leftjoin($serverPostSubLogTableName, $serverPostSubLogTableName . '.pid', $serverPostLogTableName . '.id')
                ->first();

            if (!empty($spl)) {
                $errorCode = -1;
            }
        }

        if ($errorCode === null) {
            $account = new Account();
            // $accountData = $account->where('player_name', $data['player_name'])
            //     ->where('client_id', $customer['id'])->first();
            $accountData = Account::getAccountByClientIdAndPlayerName($customer['id'], $data['player_name'], true);
            if (empty($accountData)) {
                $errorCode = 1002;
            }
        }

        if ($errorCode === null && $this->requestLimit($accountData->uid, 'walletlimit:', 5, 1) == false) {
            $errorCode = 1004;
        }


        if ($errorCode === null) {
            $balanceAmountBefore = (int) $redis->hget($accountData->uid, 'gold');
        }

        if ($errorCode === null) {
            $server = new Server;
            $postData = [
                'uid' => $accountData->uid,
                'type' => 8001,
                'count' => $data['amount'],
                'diamond' => 0,
                'now_diamond' => 0,
                'plat' => 0,
            ];
            $res = $server->addMoney(['recordset' => [$postData]]);

            if (isset($res['success']) && $res['success'] != 1) {
                $this->stopApiService();
            }
        }

        $return = [];
        if ($errorCode === null) {
            $balanceAmount = (int) $redis->hget($accountData->uid, 'gold');
            $return = [
                // "transactionId" => 35509540,
                "balanceAmountBefore" => $balanceAmountBefore,
                "balanceAmount" => $balanceAmount,
                "amount" => $data['amount']
            ];
        }

        if (!empty($sClientId) && $errorCode !== -1) {
            $serverPostLog = new ServerPostLog();
            $s = $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['trace_id'] ?? uniqid(),
                'uid' => !empty($accountData) ? $accountData->uid : 0,
                'client_id' => !empty($accountData) ? intval($redis->hget($accountData->uid, 'play_type_sub_id')) : 0,
                'type' => 2,
                'arg' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'return' => json_encode($return, JSON_UNESCAPED_UNICODE),
                'ip' => $data['ip'],
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'cost_time' => intval((microtime(true) - $start) * 1000),
            ]);

            if (!empty($data['transfer_reference'])) {
                $serverPostSubLog = new ServerPostSubLog();
                $serverPostSubLog->setTable($serverPostSubLogTableName)->create([
                    'pid' => $s->id,
                    'transfer_reference' => $data['transfer_reference']
                ]);
            }
        }

        if ($errorCode === null) {
            $this->clearLimit($accountData->uid, 'walletlimit2:');
        }

        if ($errorCode === -1) {
            $errorCode = $spl->error_code;
            $errorText = $spl->error_text;
            if ($errorCode === null) {
                $return = json_decode($spl->return, true);
                $return['isRetryRequest'] = 1;
            }
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|transferIn', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function transferOut($data)
    {
        $errorCode = null;
        $errorText = null;
        $start = microtime(true);
        $sClientId = 0;
        $redis = Redis::connection();
        $validator = Validator::make($data, [
            'operator_token' => ['required', 'string', 'max:32'],
            'secret_key' => ['required', 'string', 'max:32'],
            'player_name' => ['required', 'string', 'max:32'],
            'amount' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'transfer_reference' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'max:20', Rule::in($this->currencys)],
            'trace_id' => ['required', 'string', 'max:128', 'uuid'],
            'lang' => ['string', 'max:10', Rule::in($this->langs)],
            'ip' => ['required', 'ip'],
        ]);

        $this->setLang($data['lang'] ?? 'en');

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
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
            }
        }

        if ($errorCode === null) {
            $serverPostLogTableName = 'server_post_log_' . $sClientId;
            $serverPostSubLogTableName = 'server_post_sub_log_' . $sClientId;

            $serverPostLog = new ServerPostLog();
            $spl = $serverPostLog->setTable($serverPostLogTableName)->select('return')
                // ->where($serverPostLogTableName . '.client_id', $sClientId)
                // ->where('trace_id', $data['trace_id'])
                ->where($serverPostSubLogTableName . '.transfer_reference', $data['transfer_reference'])
                ->leftjoin($serverPostSubLogTableName, $serverPostSubLogTableName . '.pid', $serverPostLogTableName . '.id')
                ->first();

            if (!empty($spl)) {
                $errorCode = -1;
            }
        }

        if ($errorCode === null) {
            $account = new Account();
            // $accountData = $account->where('player_name', $data['player_name'])
            //     ->where('client_id', $customer['id'])->first();
            $accountData = Account::getAccountByClientIdAndPlayerName($customer['id'], $data['player_name'], true);
            if (empty($accountData)) {
                $errorCode = 1002;
            }
        }

        if ($errorCode == null && $this->gameLock($accountData->uid) == false) {
            $errorCode = 1003;
        }

        if ($errorCode === null && $this->requestLimit($accountData->uid, 'walletlimit2:', 5, 1) == false) {
            $errorCode = 1004;
        }

        if ($errorCode === null) {
            $balanceAmountBefore = (int) $redis->hget($accountData->uid, 'gold');

            if ($data['amount'] > $balanceAmountBefore) {
                $errorCode = 1005;
                $balanceAmountBeforeRecord = round($balanceAmountBefore / 100, 2);
                $errorText = "Insufficient balance (balance: {$balanceAmountBeforeRecord})";
            }
        }

        if ($errorCode === null) {
            $server = new Server;
            $postData = [
                'uid' => $accountData->uid,
                'type' => 8002,
                'count' => $data['amount'] * -1,
                'diamond' => 0,
                'now_diamond' => 0,
                'plat' => 0,
            ];
            $res = $server->addMoney(['recordset' => [$postData]]);

            if (isset($res['success']) && $res['success'] != 1) {
                $this->stopApiService();
            }
        }

        $return = [];
        if ($errorCode === null) {
            $balanceAmount = (int) $redis->hget($accountData->uid, 'gold');
            $return = [
                "balanceAmountBefore" => $balanceAmountBefore,
                "balanceAmount" => $balanceAmount,
                "amount" => $data['amount'],
            ];
        }

        if (!empty($sClientId) && $errorCode !== -1) {
            $serverPostLog = new ServerPostLog();
            $s = $serverPostLog->setTable($serverPostLogTableName)->create([
                'trace_id' => $data['trace_id'] ?? uniqid(),
                'uid' => !empty($accountData) ? $accountData->uid : 0,
                'client_id' => !empty($accountData) ? intval($redis->hget($accountData->uid, 'play_type_sub_id')) : 0,
                'type' => 3,
                'arg' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'return' => json_encode($return, JSON_UNESCAPED_UNICODE),
                'ip' => $data['ip'],
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'cost_time' => intval((microtime(true) - $start) * 1000),
            ]);

            if (!empty($data['transfer_reference'])) {
                $serverPostSubLog = new ServerPostSubLog();
                $serverPostSubLog->setTable('server_post_sub_log_' . $sClientId)->create([
                    'pid' => $s->id,
                    'transfer_reference' => $data['transfer_reference']
                ]);
            }
        }

        if ($errorCode === -1) {
            $errorCode = $spl->error_code;
            $errorText = $spl->error_text;
            if ($errorCode === null) {
                $return = json_decode($spl->return, true);
                $return['isRetryRequest'] = 1;
            }
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|transferOut', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getHistory($data)
    {
        $errorCode = null;
        $errorText = null;
        $sClient = $data['client'] ?? '';
        $sClientIdSub = 0;

        $validator = Validator::make($data, [
            'operator_token' => ['required', 'max:32'],
            'secret_key' => ['required', 'max:32'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'trace_id' => ['required', 'max:128', 'uuid'],
            'client' => ['string', 'max:10'],
            'ip' => ['required', 'ip'],
        ]);

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode === null && time() - strtotime($data['start_time']) > 86400 * 60) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'start_time more than 60 days';
        }

        if ($errorCode === null && strtotime($data['start_time']) > strtotime($data['end_time'])) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'start_time more than end_time';
        }

        if ($errorCode === null && strtotime($data['end_time']) - strtotime($data['start_time']) > 86400) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'end_time - start_time more than 24 hours';
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($data['operator_token']);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                // $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
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

        if ($errorCode === null && $this->requestLimit($data['operator_token'] . '_' . $sClient, 'historylimit:', 30, 1) == false) {
            $errorCode = 1013;
        }

        if ($errorCode == null) {
            $transferInOut = new TransferInOut;

            if ($transferInOut->checkClientTable($customer['id'])) {
                $transferInOutTableName = 'transfer_inout_' . $customer['id'];
                $transferInOut = $transferInOut->setTable($transferInOutTableName);
            } else {
                $transferInOutTableName = 'transfer_inout';
            }

            $transferInOut = $transferInOut->select(
                $transferInOutTableName . '.parent_bet_id',
                $transferInOutTableName . '.bet_id',
                'account.player_name',
                'customer_sub.symbol as client',
                $transferInOutTableName . '.game_id',
                $transferInOutTableName . '.bill_type',
                $transferInOutTableName . '.bet_amount',
                $transferInOutTableName . '.transfer_amount',
                $transferInOutTableName . '.balanceBefore as balance_before',
                $transferInOutTableName . '.balanceAfter as balance_after',
                $transferInOutTableName . '.create_time',
                $transferInOutTableName . '.transaction_id',
            );
            $transferInOut = $transferInOut->leftjoin('account', 'account.uid', '=', $transferInOutTableName . '.player_uid');
            $transferInOut = $transferInOut->leftjoin('customer_sub', 'customer_sub.id', '=', $transferInOutTableName . '.client_id');
            $transferInOut = $transferInOut->orderBy($transferInOutTableName . '.id', 'asc');
            $sClientIdSub && $transferInOut = $transferInOut->where($transferInOutTableName . '.client_id', $sClientIdSub);
            $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$data['start_time'], $data['end_time']]);
            $transferInOut = $transferInOut->limit(1500);
            $return = $transferInOut->get();
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|getHistory', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getGameDetail($data)
    {
        $errorCode = null;
        $errorText = null;
        $validator = Validator::make($data, [
            'operator_token' => ['required', 'max:32'],
            'secret_key' => ['required', 'max:32'],
            'start_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'end_time' => ['required', 'date_format:Y-m-d H:i:s'],
            'trace_id' => ['required', 'max:128', 'uuid'],
            'client' => ['string', 'max:10'],
            'ip' => ['required', 'ip'],
        ]);

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode === null && time() - strtotime($data['start_time']) > 86400 * 15) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'start_time more than 15 days';
        }

        if ($errorCode === null && strtotime($data['start_time']) > strtotime($data['end_time'])) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'start_time more than end_time';
        }

        if ($errorCode === null && strtotime($data['end_time']) - strtotime($data['start_time']) > 86400) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'end_time - start_time more than 24 hours';
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($data['operator_token']);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                // $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
            }
        }

        if ($errorCode === null && $this->requestLimit($data['operator_token'], 'gameDetaillimit:', 15, 1) == false) {
            $errorCode = 1007;
        }

        if ($errorCode == null) {
            $gameDetails = new GameDetails;
            $gameDetailsTableName = 'game_details_' . $customer['id'];
            $gameDetails = $gameDetails->setTable($gameDetailsTableName);

            $gameDetails = $gameDetails->select(
                $gameDetailsTableName . '.parent_bet_id',
                $gameDetailsTableName . '.bet_id',
                $gameDetailsTableName . '.player_name',
                $gameDetailsTableName . '.game_id',
                $gameDetailsTableName . '.detail',
                $gameDetailsTableName . '.create_time',
            );
            $gameDetails = $gameDetails->orderBy($gameDetailsTableName . '.id', 'asc');
            $gameDetails = $gameDetails->whereBetween($gameDetailsTableName . '.create_time', [$data['start_time'], $data['end_time']]);
            $gameDetails = $gameDetails->limit(1500);
            $return = $gameDetails->get();
        }

        if ($errorCode === null) {
            return $this->success($return);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|getGameDetail', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function getDataReport($data)
    {
        $errorCode = null;
        $errorText = null;
        // $start = microtime(true);
        // $sClientId = 0;
        $sClient = $data['client'] ?? '';
        $sClientIdSub = 0;
        $page = $data['page'] ?? 0;
        $limit = 1500;
        $validator = Validator::make($data, [
            'operator_token' => ['required', 'max:32'],
            'secret_key' => ['required', 'max:32'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d'],
            'client' => ['string', 'max:10'],
            'page' => ['integer'],
            'trace_id' => ['required', 'max:128', 'uuid'],
            'ip' => ['required', 'ip'],
        ]);

        if ($validator->fails()) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . $validator->errors()->keys()[0] . ' ' . $validator->errors()->first();
        }

        if ($errorCode === null && strtotime(date('Y-m-d')) - strtotime($data['end_date']) > 0) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'end_date more than today';
        }

        if ($errorCode === null && strtotime($data['start_date']) > strtotime($data['end_date'])) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'start_time more than end_time';
        }

        if ($errorCode === null && strtotime($data['end_date']) - strtotime($data['start_date']) > 31 * 86400) {
            $errorCode = 1001;
            $errorText = 'Data is not legal' . ':' . 'end_time - start_time more than 31 days';
        }

        if ($errorCode === null) {
            $customer = Customer::getCustomerByOperatorToken($data['operator_token']);
            if (empty($customer)) {
                $errorCode = 1000;
            } else {
                // $sClientId = $customer['id'];
            }
        }

        if ($errorCode === null) {
            $secretKey = decrypt($customer['secret_key']);
            if ($secretKey != $data['secret_key']) {
                $errorCode = 1006;
            }
        }

        if ($errorCode === null) {
            if ($customer['is_lock'] == 1) {
                $errorCode = 1010;
            }
        }

        // if ($errorCode === null) {
        //     if ($customer['api_mode'] != 1) {
        //         $errorCode = 1011;
        //     }
        // }

        if ($errorCode === null && !empty($customer['api_ip_white'])) {
            $ips = explode(',', $customer['api_ip_white']);
            if (!in_array($data['ip'], $ips)) {
                $errorCode = 1009;
                $errorText = 'IP limit' . ':' . $data['ip'];
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

        if ($errorCode === null && $this->requestLimit($data['operator_token'] . '_' . $sClient, 'datareportlimit:', 15) == false) {
            $errorCode = 1007;
        }

        $page = 0;
        if ($errorCode == null && empty($sClient)) {
            // DB::enableQueryLog();
            $dataReport = new DataReport;
            $dataReport = $dataReport->select(
                'data_report.game_id',
                'data_report.bet_count',
                'data_report.bet_amount',
                'data_report.transfer_amount',
                'data_report.valid_user_cnt',
                'data_report.login_user_cnt',
                'data_report.tax',
                'data_report.count_date',
            );
            $dataReport = $dataReport->orderBy('data_report.count_date', 'asc');
            $dataReport = $dataReport->where('data_report.client_id', $customer['id']);
            $dataReport = $dataReport->whereBetween('data_report.count_date', [$data['start_date'], $data['end_date']]);
            $dataReport = $dataReport->offset($page * $limit)->limit($limit);
            $return = $dataReport->get();

            $dataReport = $dataReport->orderBy('data_report.count_date', 'asc');
            $dataReport = $dataReport->where('data_report.client_id', $customer['id']);
            $dataReport = $dataReport->whereBetween('data_report.count_date', [$data['start_date'], $data['end_date']]);
            $num = $dataReport->count();
            $pages = ceil($num / $limit);
            // $sqlQuery = \Str::replaceArray(
            //     '?',
            //     collect($dataReport->getBindings())
            //         ->map(function ($i) {
            //             if (is_object($i)) {
            //                 $i = (string)$i;
            //             }
            //             return (is_string($i)) ? "'$i'" : $i;
            //         })->all(),
            //     $dataReport->toSql());
            // echo $sqlQuery;exit;    
        }

        if ($errorCode == null && !empty($client)) {
            $dataReportSub = new DataReportSub;
            $dataReportSub = $dataReportSub->select(
                'data_report_sub.game_id',
                'data_report_sub.bet_count',
                'data_report_sub.bet_amount',
                'data_report_sub.transfer_amount',
                'data_report_sub.valid_user_cnt',
                'data_report_sub.login_user_cnt',
                'data_report_sub.tax',
                'data_report_sub.bet_amount',
                'data_report_sub.transfer_amount',
                'data_report_sub.count_date',
            );
            $dataReportSub = $dataReportSub->orderBy('data_report_sub.count_date', 'asc');
            $dataReportSub = $dataReportSub->where('data_report_sub.client_id', $customer['id']);
            $dataReportSub = $dataReportSub->where('data_report_sub.client_id_sub', $sClientIdSub);
            $dataReportSub = $dataReportSub->whereBetween('data_report_sub.count_date', [$data['start_date'], $data['end_date']]);
            $dataReportSub = $dataReportSub->offset($page * $limit)->limit($limit);
            $return = $dataReportSub->get();

            $dataReportSub = new DataReportSub;
            $dataReportSub = $dataReportSub->where('data_report_sub.client_id', $customer['id']);
            $dataReportSub = $dataReportSub->where('data_report_sub.client_id_sub', $sClientIdSub);
            $dataReportSub = $dataReportSub->whereBetween('data_report_sub.count_date', [$data['start_date'], $data['end_date']]);
            $num = $dataReportSub->count();
            $pages = ceil($num / $limit);
        }

        if ($errorCode === null) {
            return $this->successAttr($return, ['total_page' => $pages, 'page' => $page]);
        } else {
            $data['secret_key'] = empty($data['secret_key']) ? '' : '***';
            Log::warning('CF|getDataReport', [$data, $errorCode, $errorText]);
            return $this->error($errorCode, $errorText);
        }
    }

    public function calDataReport($day)
    {
        $c = Customer::get();
        foreach ($c as $v) {
            $this->_calDataReport($day, $v->id);
            $cs = CustomerSub::where('customer_id', $v->id)->get();
            foreach ($cs as $vv) {
                $this->_calDataReportSub($day, $v->id, $vv->id);
            }
        }
    }

    protected function _calDataReport($day, $clientId)
    {
        $transferInOut = new TransferInOut;
        $transferInOutTableName = 'transfer_inout_' . $clientId;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);
        $transferInOut = $transferInOut->select(
            'game_id',
            'client_id',
            $transferInOut->raw('date(create_time) as count_date'),
            $transferInOut->raw('sum(bet_amount) as bet_amount'),
            $transferInOut->raw('sum(transfer_amount) as transfer_amount'),
            $transferInOut->raw('count(distinct(player_uid)) as valid_user_cnt'),
        );
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);
        $transferInOut = $transferInOut->groupBy('game_id');
        $data1 = $transferInOut->get();

        $transferInOut = new TransferInOut;
        $transferInOutTableName = 'transfer_inout_' . $clientId;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);

        $transferInOut = $transferInOut->select(
            'game_id',
            'client_id',
            $transferInOut->raw('count(*) as bet_count'),
        );
        $transferInOut = $transferInOut->whereIn('bill_type', [54, 62]);
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);

        $transferInOut = $transferInOut->groupBy('game_id');
        $data2 = $transferInOut->get();

        $transferInOut = new TransferInOut;
        $transferInOutTableName = 'transfer_inout_' . $clientId;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);
        $transferInOut = $transferInOut->select(
            'game_id',
            'client_id',
            $transferInOut->raw('sum(transfer_amount) as tax'),
        );
        $transferInOut = $transferInOut->whereIn('bill_type', [63]);
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);

        $transferInOut = $transferInOut->groupBy('game_id');
        $data3 = $transferInOut->get();

        $d2 = [];
        foreach ($data2 as $v) {
            $d2[$v->game_id] = $v;
        }

        $d3 = [];
        foreach ($data3 as $v) {
            $d3[$v->game_id] = $v;
        }

        $data = [];
        foreach ($data1 as $v) {
            $tax = 0;
            if (isset($d3[$v->game_id])) {
                $tax = $d3[$v->game_id]->tax;
            }

            if (isset($d2[$v->game_id])) {
                $nv = $d2[$v->game_id];
                $logon = new Logon();
                $userLoginCnt = $logon->where('client_id', $clientId)
                    ->where('game_id', $v->game_id)
                    ->where('post_time', '>=', strtotime($day . ' 00:00:00'))
                    ->where('post_time', '<=', strtotime($day . ' 23:59:59'))
                    ->select($logon->raw('count(distinct uid) as num'))
                    ->first();

                $data[] = [
                    'game_id' => $v->game_id,
                    'bet_amount' => $v->bet_amount,
                    'transfer_amount' => $v->transfer_amount,
                    'bet_count' => $nv->bet_count,
                    'count_date' => $day,
                    'client_id' => $clientId,
                    'updated_time' => date('Y-m-d H:i:s'),
                    'valid_user_cnt' => $v->valid_user_cnt,
                    'login_user_cnt' => $userLoginCnt->num,
                    'tax' => abs($tax),
                ];
            }
        }

        foreach ($data as $d) {
            $res = DataReport::where('game_id', $d['game_id'])
                ->where('client_id', $clientId)
                ->where('count_date', $d['count_date'])
                ->first();
            if ($res) {
                DataReport::where('game_id', $d['game_id'])
                    ->where('client_id', $clientId)
                    ->where('count_date', $d['count_date'])->update($d);
            } else {
                DataReport::create($d);
            }
        }
    }

    protected function _calDataReportSub($day, $clientId, $sClientIdSub)
    {
        $transferInOutTableName = 'transfer_inout_' . $clientId;
        $transferInOut = new TransferInOut;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);
        $transferInOut = $transferInOut->select(
            'game_id',
            $transferInOut->raw('date(create_time) as count_date'),
            $transferInOut->raw('sum(bet_amount) as bet_amount'),
            $transferInOut->raw('sum(transfer_amount) as transfer_amount'),
            $transferInOut->raw('count(distinct(player_uid)) as valid_user_cnt'),
        );
        $transferInOut = $transferInOut->where('client_id', $sClientIdSub);
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);
        $transferInOut = $transferInOut->groupBy('game_id');
        $data1 = $transferInOut->get();

        $transferInOut = new TransferInOut;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);

        $transferInOut = $transferInOut->select(
            'game_id',
            $transferInOut->raw('count(*) as bet_count'),
        );
        $transferInOut = $transferInOut->where('client_id', $sClientIdSub);
        $transferInOut = $transferInOut->whereIn('bill_type', [54, 62]);
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);

        $transferInOut = $transferInOut->groupBy('game_id');
        $data2 = $transferInOut->get();

        $transferInOut = new TransferInOut;
        $transferInOut = $transferInOut->setTable($transferInOutTableName);
        $transferInOut = $transferInOut->select(
            'game_id',
            $transferInOut->raw('sum(transfer_amount) as tax'),
        );
        $transferInOut = $transferInOut->where('client_id', $sClientIdSub);
        $transferInOut = $transferInOut->whereIn('bill_type', [63]);
        $transferInOut = $transferInOut->whereBetween($transferInOutTableName . '.create_time', [$day . ' 00:00:00', $day . ' 23:59:59']);
        $transferInOut = $transferInOut->groupBy('game_id');
        $data3 = $transferInOut->get();

        $d2 = [];
        foreach ($data2 as $v) {
            $d2[$v->game_id] = $v;
        }

        $d3 = [];
        foreach ($data3 as $v) {
            $d3[$v->game_id] = $v;
        }

        $data = [];
        foreach ($data1 as $v) {
            $tax = 0;
            if (isset($d3[$v->game_id])) {
                $tax = $d3[$v->game_id]->tax;
            }

            if (isset($d2[$v->game_id])) {
                $nv = $d2[$v->game_id];
                $logon = new Logon();
                $userLoginCnt = $logon->where('client_id', $clientId)
                    ->where('client_id_sub', $sClientIdSub)
                    ->where('game_id', $v->game_id)
                    ->where('post_time', '>=', strtotime($day . ' 00:00:00'))
                    ->where('post_time', '<=', strtotime($day . ' 23:59:59'))
                    ->select($logon->raw('count(distinct uid) as num'))
                    ->first();

                $data[] = [
                    'game_id' => $v->game_id,
                    'bet_amount' => $v->bet_amount,
                    'transfer_amount' => $v->transfer_amount,
                    'bet_count' => $nv->bet_count,
                    'count_date' => $day,
                    'client_id' => $clientId,
                    'client_id_sub' => $sClientIdSub,
                    'updated_time' => date('Y-m-d H:i:s'),
                    'valid_user_cnt' => $v->valid_user_cnt,
                    'login_user_cnt' => $userLoginCnt->num,
                    'tax' => abs($tax),
                ];
            }
        }

        foreach ($data as $d) {
            $res = DataReportSub::where('game_id', $d['game_id'])
                ->where('client_id', $clientId)
                ->where('client_id_sub', $sClientIdSub)
                ->where('count_date', $d['count_date'])
                ->first();
            if ($res) {
                DataReportSub::where('game_id', $d['game_id'])
                    ->where('client_id', $clientId)
                    ->where('client_id_sub', $sClientIdSub)
                    ->where('count_date', $d['count_date'])
                    ->update($d);
            } else {
                DataReportSub::create($d);
            }
        }
    }
}
