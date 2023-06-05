<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;

class ServerRequestLog extends Model
{
    const DEBUG = 0;
    const INFO = 1;
    const WARINNING = 2;
    const ERROR = 3;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'pid',
        'client_id',
        'uid',
        'type',
        'url',
        'cost_time',
        'response',
        'error_code',
        'error_text',
        'params',
        'method',
        'code',
        'args',
        'created',
        'is_success',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    protected $table = 'server_request_log';


    public $timestamps = false;

    protected $connection = 'Master';

    protected $times = [];

    protected $_id = 0;

    public function start($clientId, $name = 't1')
    {
        $this->times[$name] = microtime(true);
        $model = new self;
        $serverRequestLogTableName = 'server_request_log_' . $clientId;
        $data = $model->setTable($serverRequestLogTableName)->create();
        $this->_id = $data->id;
        return $this->_id;
    }

    public function getTraceId($id = '')
    {
        if (empty($id)) {
            $id = $this->_id;
        }

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


    public function record($name, $level, $pid, $clientId, $clientIdSub, $uid, $type, $url, $method, $params, $response, $code, $errorCode, $errorText, $args)
    {
        if ($level < env('SERVER_REQUEST_LOG_LEVEL', 0)) {
            return;
        }

        $costTime = isset($this->times[$name]) ? intval((microtime(true) - $this->times[$name]) * 1000) : 0;

        if (isset($params['form_params']) && isset($params['form_params']['secret_key'])) {
            $params['form_params']['secret_key'] = encrypt($params['form_params']['secret_key']);
        }

        $isSuccess = $errorCode == 0 ? 1 : 0;
        $serverRequestLogTableName = 'server_request_log_' . $clientId;
        if ($this->_id == 0) {
            $model = new self;
            $res = $model->setTable($serverRequestLogTableName)->create([
                'pid' => $pid,
                'client_id' => $clientIdSub,
                'type' => $type,
                'url' => $url,
                'cost_time' => $costTime,
                // 'response' => json_encode($response, JSON_UNESCAPED_UNICODE),
                'response' => $response,
                'code' => $code,
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'params' => json_encode($params, JSON_UNESCAPED_UNICODE),
                'method' => $method,
                'args' => json_encode($args, JSON_UNESCAPED_UNICODE),
                'uid' => $uid,
                'is_success' => $isSuccess,
            ]);
        } else {
            $model = new self;
            $res = $model->setTable($serverRequestLogTableName)->where('id', $this->_id)->update([
                'pid' => $pid,
                'client_id' => $clientIdSub,
                'type' => $type,
                'url' => $url,
                'cost_time' => $costTime,
                // 'response' => json_encode($response, JSON_UNESCAPED_UNICODE),
                'response' => $response,
                'code' => $code,
                'error_code' => $errorCode,
                'error_text' => $errorText,
                'params' => json_encode($params, JSON_UNESCAPED_UNICODE),
                'method' => $method,
                'args' => json_encode($args, JSON_UNESCAPED_UNICODE),
                'uid' => $uid,
                'is_success' => $isSuccess,
            ]);
        }

        if ($pid > 0) {
            $model = new self;
            $model->setTable($serverRequestLogTableName)
                ->where('id', $pid)
                ->orWhere('pid', $pid)
                ->update(['is_success' => $isSuccess]);
        }
        return $res;
    }

    public function getId()
    {
        return $this->_id;
    }
}
