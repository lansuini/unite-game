<?php

namespace App\Http\Controllers\Analysis;

use Illuminate\Http\Request;

use App\Models\ServerRequestLog;
use App\Models\ServerPostLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Models\CustomerSub;
use App\Models\ConfigAttribute;
use Illuminate\Validation\Rule;
use App\Rules\MultipleGames;
use App\Rules\MultipleIPs;
use Illuminate\Support\Facades\Hash;
use App\Http\Library\MerchantCB;
use App\Http\Library\MerchantCF;
use App\Models\TransferInOut;
use GuzzleHttp\Client;
use App\Http\Library\DynamicJsonForm;
use App\Http\Library\Server;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Schema;

class CustomerController extends AnalysisController
{
    public function clientView(Request $request)
    {
        return view('Analysis/Customer/clientView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function clientList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $companyName = $request->query->get('company_name');
        $operatorToken = $request->query->get('operator_token');
        $isLock = $request->query->get('is_lock');

        $model = new Customer();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $companyName && $model = $model->where('company_name', 'like', '%' . $companyName . '%');
        $operatorToken && $model = $model->where('operator_token', $operatorToken);
        $isLock && $model = $model->where('is_lock', $isLock);

        $model = $model->select(
            'id',
            'company_name',
            'operator_token',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'created',
            'api_mode',
            'game_domain',
        );
        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function clientDetail(Request $request, $id)
    {
        $data = Customer::select(
            'id',
            'company_name',
            'operator_token',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'api_mode',
            'created',
            'game_domain',
            'game_oc',
            'game_mc',
        )
            ->where('id', $id)->first();
        return ['success' => 1, 'data' => $data];
    }

    public function clientAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string', 'max:256', 'unique:\App\Models\Customer,company_name'],
            'operator_token' => ['required', 'string', 'min:32', 'max:32', 'unique:\App\Models\Customer,operator_token', 'alpha_dash'],
            'secret_key' => ['required', 'string', 'min:32', 'max:32', 'alpha_dash'],
            'merchant_addr' => ['required', 'string', 'max:512'],
            'is_lock' => ['required', 'integer', Rule::in([0, 1])],
            'api_mode' => ['required', 'integer', Rule::in([0, 1])],
            'api_ip_white' => ['string', 'max:2048', new MultipleIPS],
            'game_oc' => ['string', 'max:1024', new MultipleGames],
            // 'game_mc' => ['string', 'max:1024', new MultipleGames],
        ]);

        if ($validator->fails()) {
            return ['success' => 0, 'result' => $validator->errors()->first(), 'validator' => $validator->errors()];
        }

        $data = $request->only(
            'company_name',
            'operator_token',
            'secret_key',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'api_mode',
            'game_domain',
            'game_oc',
        );
        $data['secret_key'] = encrypt($data['secret_key']);
        $after = Customer::create($data);
        $transferInOut = new TransferInOut;
        $transferInOut->createTable($after->id);
        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'CUSTOMER_CLIENT_CREATE',
            'is_success' => 1,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => $after->username,
            'target_id' => $after->id,
            'after' =>  $after->toJson(),
            'params' => json_encode($request->only(
                'company_name',
                'operator_token',
                'secret_key',
                'merchant_addr',
                'is_lock',
                'api_ip_white',
                'api_mode',
                'game_domain',
                'game_oc',
            )),
            'method' => $request->method()
        ]);
        return ['success' => 1, 'result' => __('ts.create success')];
    }

    public function clientEdit(Request $request, $id)
    {
        $isInputSecretKey = !empty($request->input('secret_key'));

        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string', 'max:256'],
            // 'operator_token' => ['required', 'string', 'min:32', 'max:32', 'unique:\App\Models\Customer,operator_token', 'alpha_dash'],
            // 'secret_key' => ['required', 'string', 'min:32', 'max:32', 'alpha_dash'],
            'merchant_addr' => ['required', 'string', 'max:512'],
            'game_domain' => ['required', 'string', 'max:64'],
            'is_lock' => ['required', 'integer', Rule::in([0, 1])],
            'api_mode' => ['required', 'integer', Rule::in([0, 1])],
            'api_ip_white' => ['string', 'max:2048', new MultipleIPS],
            'game_oc' => ['string', 'max:1024', new MultipleGames],
            'game_mc' => ['string', 'max:1024', new MultipleGames],
        ]);

        if ($validator->fails()) {
            return ['success' => 0, 'result' => $validator->errors()->first(), 'validator' => $validator->errors()];
        }

        $data = $request->only(
            'company_name',
            // 'operator_token',
            'secret_key',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'api_mode',
            'game_domain',
            'game_oc',
            'game_mc',
        );

        $data['game_domain'] = $data['game_domain'] ?? '';
        $data['api_ip_white'] = $data['api_ip_white'] ?? '';
        $data['game_oc'] = $data['game_oc'] ?? '';
        $data['game_mc'] = $data['game_mc'] ?? '';
        if ($isInputSecretKey) {
            $validator = Validator::make($request->all(), [
                'secret_key' => ['required', 'string', 'min:32', 'max:32', 'alpha_dash'],
            ]);

            if ($validator->fails()) {
                return ['success' => 0, 'result' => $validator->errors()->first(), 'validator' => $validator->errors()];
            }

            $data['secret_key'] = encrypt($data['secret_key']);
        }

        $before = Customer::select(
            'company_name',
            'operator_token',
            'secret_key',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'api_mode',
            'game_domain',
            'game_oc',
            'game_mc',
            'id',
        )->where('id', $id)->first();
        Customer::where('id', $id)->update($data);
        $after = Customer::select(
            'company_name',
            'operator_token',
            'secret_key',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'api_mode',
            'game_domain',
            'game_oc',
            'game_mc',
            'id',
        )->where('id', $id)->first();
        $collection = collect($before);
        $diffItems = $collection->diff($after);
        if (!$diffItems->isEmpty()) {
            $this->actionLog->create([
                'admin_id' => $this->admin->getLoginID($request),
                'admin_username' => $this->admin->getLoginUsername($request),
                'browser' => $request->header('User-Agent'),
                'key' => 'CUSTOMER_CLIENT_EDIT',
                'is_success' => 1,
                'url' => $request->url(),
                'ip' => $this->ip($request),
                'desc' => $before->company_name,
                'target_id' => $before->id,
                'before' => $before->toJson(),
                'after' =>  $after->toJson(),
                'params' => json_encode($request->only(
                    'company_name',
                    'operator_token',
                    'merchant_addr',
                    'is_lock',
                    'api_ip_white',
                    'api_mode',
                    'game_domain',
                    'game_oc',
                    'game_mc',
                )),
                'method' => $request->method()
            ]);
        }

        if ($isInputSecretKey) {
            $this->actionLog->create([
                'admin_id' => $this->admin->getLoginID($request),
                'admin_username' => $this->admin->getLoginUsername($request),
                'browser' => $request->header('User-Agent'),
                'key' => 'CUSTOMER_CLIENT_EDIT_SECRET_KEY',
                'is_success' => 1,
                'url' => $request->url(),
                'ip' => $this->ip($request),
                'desc' => $before->company_name,
                'target_id' => $before->id,
                'method' => $request->method()
            ]);
        }

        if ($before->game_mc != $after->game_mc) {
            $this->pushConfigServer();
        }

        Customer::refreshCustomerByOperatorToken($before->operator_token);
        Customer::refreshCustomerById($before->id);
        return ['success' => 1, 'result' => __('ts.update success'), 'a' => 1];
    }

    protected function pushConfigServer()
    {
        $gameStatus = ConfigAttribute::where('v_key_name', 'GAME_STATUS')->first();
        $data = json_decode($gameStatus->t_key_value);
        $customer = Customer::whereNotNull('game_mc')->where('game_mc', '!=', '')->get();
        foreach ($customer as $c) {
            $data[] = ['plat' => $c->id, 'gameid' => explode(',', $c->game_mc)];
        }

        $server = new Server();
        $server->refreshMaintenanceConfig($data);
    }

    public function clientDel(Request $request, $id)
    {
        $before = Customer::select(
            'company_name',
            'operator_token',
            'secret_key',
            'merchant_addr',
            'is_lock',
            'api_ip_white',
            'created',
            'api_mode',
            'game_domain',
            'game_oc',
            'game_mc',
            'configs',
        )->where('id', $id)->first();
        if (empty($before)) {
            return ['success' => 0, 'result' => __('ts.id error')];
        }
        Customer::where('id', $id)->delete();
        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'CUSTOMER_CLIENT_DELETE',
            'is_success' => 1,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => $before->company_name,
            'target_id' => $before->id,
            'before' => $before->toJson(),
            'params' => json_encode($request->all()),
            'method' => $request->method()
        ]);

        Customer::refreshCustomerByOperatorToken($before->operator_token);
        Customer::refreshCustomerById($before->id);
        return ['success' => 1, 'result' => __('ts.delete success')];
    }

    public function clientJSONEdit(Request $request, $id)
    {
        $model = new Customer();
        $data = $model->where('id', $id)->first();
        $cn = $data->api_mode == 0 ? 'single' : 'transfer';
        $configData = config('analysis.json_form_config.client.' . $cn);

        $actionButton = $request->input('actionButton');
        $actionField = $request->input('actionField');
        $json = $request->input('json', []);
        // $json = !empty($json) ? $json : json_decode($data['configs'], true);
        if (empty($json)) {
            $json =  json_decode($data['configs'], true);
            $json = (new DynamicJsonForm($configData))->startPreprocessing($json);
        }

        if ($request->query('autoFixed') == 1) {
            $json = json_decode(config('analysis.json_form_config.client.def_value')[$cn], true);
            $json = (new DynamicJsonForm($configData))->startPreprocessing($json);
        }

        if (empty($configData)) {
            return 'this game not support json config module';
        }

        if ($actionButton) {
            $forms = (new DynamicJsonForm($configData))->$actionButton($actionField)->fill($json, false)->create();
        } else {
            $forms = (new DynamicJsonForm($configData))->fill($json, false);
            if ($request->method() == 'PATCH') {
                if ($forms->isValid()) {
                    $json = (new DynamicJsonForm($configData))->cancelPreprocessing($json);
                    $model->where('id', $id)->update([
                        'configs' => json_encode($json)
                    ]);

                    if ($data['configs'] != json_encode($json)) {
                        $this->actionLog->create([
                            'admin_id' => $this->admin->getLoginID($request),
                            'admin_username' => $this->admin->getLoginUsername($request),
                            'browser' => $request->header('User-Agent'),
                            'key' => 'CUSTOMER_CLIENT_JSON_EDIT',
                            'is_success' => 1,
                            'url' => $request->url(),
                            'ip' => $this->ip($request),
                            'desc' => $id,
                            'target_id' => $id,
                            'before' => $data['configs'],
                            'after' => json_encode($json),
                            'params' => json_encode($request->all()),
                            'method' => $request->method()
                        ]);
                    }

                    Customer::refreshCustomerById($id);
                    $c = Customer::getCustomerById($id);
                    Customer::refreshCustomerByOperatorToken($c['operator_token']);
                    return 'success';
                }
            }
            $forms->setType(1);
            $forms = $forms->create();
        }
        return view('GM/Game/gameWinLoseControlJSONView', ['forms' => $forms]);
    }

    public function subClientView(Request $request)
    {
        return view('Analysis/Customer/subClientView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function subClientList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $symbol = $request->query->get('symbol');
        $customerId = $request->query->get('client_id');
        $isLock = $request->query->get('is_lock');

        $model = new CustomerSub();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $symbol && $model = $model->where('symbol', 'like', '%' . $symbol . '%');
        $customerId && $model = $model->where('customer_id', $customerId);
        $isLock && $model = $model->where('is_lock', $isLock);

        $model = $model->select(
            'id',
            'symbol',
            'remark',
            'customer_id',
            'is_lock',
            'created',
        );

        $total = $model->count();
        $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function subClientDetail(Request $request, $id)
    {
        $data = CustomerSub::select(
            'id',
            'symbol',
            'remark',
            'customer_id',
            'is_lock',
            'created',
        )
            ->where('id', $id)->first();
        return ['success' => 1, 'data' => $data];
    }

    public function subClientAdd(Request $request)
    {
        $customerId = $request->get('customer_id');
        $symbol = $request->get('symbol');
        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer', 'exists:\App\Models\Customer,id'],
            'symbol' => [
                'required', 'string', 'max:10', 'alpha_dash',
                // 'unique:\App\Models\CustomerSub,customer_id,symbol'
                Rule::unique('\App\Models\CustomerSub')->where(function ($query) use ($customerId, $symbol) {
                    return $query->where('customer_id', $customerId)
                        ->where('symbol', $symbol);
                }),
            ],
            'remark' => ['required', 'string', 'min:0', 'max:128'],
            'is_lock' => ['required', 'integer', Rule::in([0, 1])],
        ]);

        if ($validator->fails()) {
            return ['success' => 0, 'result' => $validator->errors()->first(), 'validator' => $validator->errors()];
        }

        $data = $request->only(
            'symbol',
            'remark',
            'customer_id',
            'is_lock',
        );

        $after = CustomerSub::create($data);
        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'CUSTOMER_CLIENT_SUB_CREATE',
            'is_success' => 1,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => $after->symbol,
            'target_id' => $after->id,
            'after' =>  $after->toJson(),
            'params' => json_encode($request->only(
                'symbol',
                'remark',
                'customer_id',
                'is_lock',
            )),
            'method' => $request->method()
        ]);

        CustomerSub::refreshCustomerSubByCache($after->customer_id);
        return ['success' => 1, 'result' => __('ts.create success')];
    }

    public function subClientEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remark' => ['required', 'string', 'min:0', 'max:128'],
            'is_lock' => ['required', 'integer', Rule::in([0, 1])],
        ]);

        if ($validator->fails()) {
            return ['success' => 0, 'result' => $validator->errors()->first(), 'validator' => $validator->errors()];
        }

        $data = $request->only(
            'remark',
            'is_lock',
        );

        $before = CustomerSub::select(
            'remark',
            'is_lock',
            'symbol',
            'customer_id',
            'id',
        )->where('id', $id)->first();
        CustomerSub::where('id', $id)->update($data);
        $after = CustomerSub::select(
            'remark',
            'is_lock',
            'symbol',
            'customer_id',
            'id',
        )->where('id', $id)->first();
        $collection = collect($before);
        $diffItems = $collection->diff($after);
        if (!$diffItems->isEmpty()) {
            $this->actionLog->create([
                'admin_id' => $this->admin->getLoginID($request),
                'admin_username' => $this->admin->getLoginUsername($request),
                'browser' => $request->header('User-Agent'),
                'key' => 'CUSTOMER_CLIENT_SUB_EDIT',
                'is_success' => 1,
                'url' => $request->url(),
                'ip' => $this->ip($request),
                'desc' => $before->symbol,
                'target_id' => $before->id,
                'before' => $before->toJson(),
                'after' =>  $after->toJson(),
                'params' => json_encode($request->only(
                    'remark',
                    'is_lock',
                    'symbol',
                    'customer_id',
                    'id',
                )),
                'method' => $request->method()
            ]);
        }
        CustomerSub::refreshCustomerSubByCache($before->customer_id);
        return ['success' => 1, 'result' => __('ts.update success')];
    }

    public function subClientDel(Request $request, $id)
    {
        $before = CustomerSub::select(
            'symbol',
            'remark',
            'customer_id',
            'is_lock',
        )->where('id', $id)->first();
        if (empty($before)) {
            return ['success' => 0, 'result' => __('ts.id error')];
        }
        CustomerSub::where('id', $id)->delete();
        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'CUSTOMER_CLIENT_SUB_DELETE',
            'is_success' => 1,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => $before->symbol,
            'target_id' => $before->id,
            'before' => $before->toJson(),
            'params' => json_encode($request->all()),
            'method' => $request->method()
        ]);

        CustomerSub::refreshCustomerSubByCache($before->customer_id);
        return ['success' => 1, 'result' => __('ts.delete success')];
    }

    public function serverRequestLogView(Request $request)
    {
        return view('Analysis/Customer/serverRequestLogView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function serverRequestLogDetail(Request $request, $clientId, $id)
    {
        $serverRequestLogTableName = 'server_request_log_' . $clientId;
        $model = new ServerRequestLog();
        $model = $model->setTable($serverRequestLogTableName);
        $data = $model->select(
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
        )
            ->where('id', $id)->first();
        if (empty($data->error_text)) {
            $mcb = new MerchantCB;
            $errors = $mcb->getErrors();
            $data->error_text = $errors[$data->error_code] ?? '';
        }
        return ['success' => 1, 'data' => $data];
    }

    public function serverRequestLogList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $id = $request->query->get('id', 0);
        $clientId = $request->query->get('client_id');
        $uid = $request->query->get('uid');
        $type = $request->query->get('type', -1);
        $costTime = $request->query->get('cost_time', -1);
        $isSuccess = $request->query->get('is_success', -1);
        $created = $request->query->get('created');
        $transactionId = $request->query->get('transaction_id');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d 23:59:59');

        $serverRequestLogTableName = 'server_request_log_' . $clientId;
        $transferInoutServerRequestLogTableName = 'transfer_inout_server_request_log_' . $clientId;

        $sorts = [
            'id' => $serverRequestLogTableName . '.id',
            'cost_time' => $serverRequestLogTableName . '.cost_time',
        ];

        if (Schema::connection('Master')->hasTable($serverRequestLogTableName)) {
            $model = new ServerRequestLog();
            $model = $model->setTable($serverRequestLogTableName);
            if (!empty($transactionId)) {
                $type = 3;
            }

            !empty($sort) && $model = $model->orderBy($sorts[$sort], $order);
            $type != -1 && $model = $model->where($serverRequestLogTableName . '.type', $type);
            // $clientId && $model = $model->where($serverRequestLogTableName.'.client_id', $clientId);
            $uid && $model = $model->where($serverRequestLogTableName . '.uid', $uid);
            $isSuccess != -1 && $model = $model->where($serverRequestLogTableName . '.is_success', $isSuccess);
            $costTime == 0 && $model = $model->where($serverRequestLogTableName . '.cost_time', '<', 500);
            $costTime == 1 && $model = $model->where($serverRequestLogTableName . '.cost_time', '>=', 500);
            $costTime == 2 && $model = $model->where($serverRequestLogTableName . '.cost_time', '<=', 200);
            // $isSuccess == 0 && $model = $model->where('error_code', '!=', 0);

            if ($id == 0) {
                $model = $model->where($serverRequestLogTableName . '.pid', 0);
            } else {
                $id = is_numeric($id) ? $id : $model->getTraceId($id);
                $model = $model->where($serverRequestLogTableName . '.id', $id)->orWhere($serverRequestLogTableName . '.pid', $id);
            }

            $model = $model->where($serverRequestLogTableName . '.created', '>=', $s);
            $model = $model->where($serverRequestLogTableName . '.created', '<=', $e);

            $f1 = $model->raw('null as queue_name');
            $f2 = $model->raw('null as transaction_id');
            if ($type == 3) {
                $model = $model->leftjoin($transferInoutServerRequestLogTableName, $transferInoutServerRequestLogTableName . '.server_request_log_id', '=', $serverRequestLogTableName . '.id');
                $model = $model->where($transferInoutServerRequestLogTableName . '.created', '>=', $s);
                $model = $model->where($transferInoutServerRequestLogTableName . '.created', '<=', $e);
                $transactionId && $model = $model->where($transferInoutServerRequestLogTableName . '.transaction_id', $transactionId);
                $f1 = $transferInoutServerRequestLogTableName . '.queue_name';
                $f2 = $transferInoutServerRequestLogTableName . '.transaction_id';
            }

            $model = $model->select(
                $serverRequestLogTableName . '.id',
                $serverRequestLogTableName . '.pid',
                $serverRequestLogTableName . '.client_id',
                $serverRequestLogTableName . '.type',
                $serverRequestLogTableName . '.url',
                $serverRequestLogTableName . '.cost_time',
                $serverRequestLogTableName . '.response',
                $serverRequestLogTableName . '.error_code',
                $serverRequestLogTableName . '.error_text',
                $serverRequestLogTableName . '.params',
                $serverRequestLogTableName . '.method',
                $serverRequestLogTableName . '.code',
                $serverRequestLogTableName . '.created',
                $serverRequestLogTableName . '.uid',
                $serverRequestLogTableName . '.is_success',
                $serverRequestLogTableName . '.admin_id',
                $f1,
                $f2,
            );
            $total = $model->count();
            $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        } else {
            $rows = [];
            $total = 0;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
        ];
    }

    public function serverRequestLogAdd(Request $request, $clientId, $id)
    {
        $serverRequestLogTableName = 'server_request_log_' . $clientId;
        $model = new ServerRequestLog();
        $model = $model->setTable($serverRequestLogTableName);
        $data = $model->where('id', $id)->first();
        $pid = $data->pid == 0 ? $data->id : $data->pid;

        $args = json_decode($data->args, true);
        if ($data->type == 1) {
            $mcb = new MerchantCB();
            $res = $mcb->getVerifySession($args[0], $pid);
        } else if ($data->type == 2) {
            $mcb = new MerchantCB();
            $res = $mcb->getCashGet($args[0], $args[1], $pid);
        } else if ($data->type == 3) {
            $mcb = new MerchantCB();
            $res = $mcb->getCashTransferInOut($args[0], $pid);
        } else if ($data->type == 4) {
            $mcf = new MerchantCF();
            $res = $mcf->verifySession($args[0], $pid);
        } else {
            return ['success' => 0, 'result' => __('ts.method not found')];
        }

        $this->actionLog->create([
            'admin_id' => $this->admin->getLoginID($request),
            'admin_username' => $this->admin->getLoginUsername($request),
            'browser' => $request->header('User-Agent'),
            'key' => 'SERVER_REQUEST_RETRY',
            'is_success' => $res['error'] == null ? 1 : 0,
            'url' => $request->url(),
            'ip' => $this->ip($request),
            'desc' => 'retry',
            'target_id' => $pid,
            'method' => $request->method()
        ]);

        return ['success' => 1, 'result' => 'success', 'data' => $res, 'pid' => $pid];
    }

    public function serverPostLogView(Request $request)
    {
        return view('Analysis/Customer/serverPostLogView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function serverPostLogList(Request $request)
    {
        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $transferReference = $request->query->get('transfer_reference');
        $clientId = $request->query->get('client_id');
        $traceId = $request->query->get('trace_id');
        $uid = $request->query->get('uid');
        $type = $request->query->get('type', -1);
        $isSuccess = $request->query->get('is_success', -1);
        $costTime = $request->query->get('cost_time', -1);
        $created = $request->query->get('created');
        $created = urldecode($created);
        $created = explode(' - ', $created);
        $s  = \DateTime::createFromFormat('m/d/Y', $created[0])->format('Y-m-d 00:00:00');
        $e  = \DateTime::createFromFormat('m/d/Y', $created[1])->format('Y-m-d 23:59:59');

        $tableName = 'server_post_log_' . $clientId;
        $tableSubName = 'server_post_sub_log_' . $clientId;

        if (Schema::connection('Master')->hasTable($tableName)) {
            $model = new ServerPostLog();
            $model = $model->setTable($tableName);
            !empty($sort) && $model = $model->orderBy($sort, $order);
            $type != -1 && $model = $model->where($tableName . '.type', $type);
            // $clientId && $model = $model->where($tableName . '.client_id', $clientId);
            $uid && $model = $model->where($tableName . '.uid', $uid);
            $traceId && $model = $model->where($tableName . '.trace_id', $traceId);
            if ($isSuccess == 1) {
                $model = $model->whereNull($tableName . '.error_code');
            } else if ($isSuccess == 0) {
                $model = $model->whereNotNull($tableName . '.error_code');
            }

            $costTime == 0 && $model = $model->where('cost_time', '<', 200);
            $costTime == 1 && $model = $model->where('cost_time', '>=', 200);
            $costTime == 2 && $model = $model->where('cost_time', '<=', 100);

            $transferReference && $model = $model->where($tableSubName . '.transfer_reference', $transferReference);
            $model = $model->where($tableName . '.created', '>=', $s);
            $model = $model->where($tableName . '.created', '<=', $e);

            $model = $model->select(
                $tableName . '.id',
                $tableName . '.trace_id',
                $tableName . '.uid',
                $tableName . '.client_id',
                $tableName . '.type',
                $tableName . '.arg',
                $tableName . '.return',
                $tableName . '.ip',
                $tableName . '.error_code',
                $tableName . '.error_text',
                $tableName . '.cost_time',
                $tableSubName . '.transfer_reference',
                $tableName . '.created',
            );
            $model = $model->leftjoin($tableSubName, $tableName . '.id', '=', $tableSubName . '.pid');
            $total = $model->count();
            $rows = $model->offset($offset)->limit($limit)->get()->toArray();
        } else {
            $rows = [];
            $total = 0;
        }
        return [
            'result' => [],
            'rows' => $rows,
            'success' => 1,
            'total' => $total,
            't' => $tableName,
        ];
    }
}
