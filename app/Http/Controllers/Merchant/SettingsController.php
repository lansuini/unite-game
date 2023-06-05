<?php

namespace App\Http\Controllers\Merchant;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\CustomerSub;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class SettingsController extends MerchantController
{
    public function subClientView(Request $request)
    {
        return view('Merchant/Settings/subClientView', ['pageTitle' => $this->role->getCurrentPageTitle($request)]);
    }

    public function subClientList(Request $request)
    {
        $clientId = $this->admin->getCurrent($request)->client_id;

        $limit = $request->query->get('limit', 20);
        $offset = $request->query->get('offset', 0);
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'desc');
        $symbol = $request->query->get('symbol');
        $isLock = $request->query->get('is_lock');

        $model = new CustomerSub();
        !empty($sort) && $model = $model->orderBy($sort, $order);
        $symbol && $model = $model->where('symbol', 'like', '%' . $symbol . '%');
        $model = $model->where('customer_id', $clientId);
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
            'is_lock',
        )
            ->where('id', $id)->first();
        return ['success' => 1, 'data' => $data];
    }

    public function subClientAdd(Request $request)
    {
        $clientId = $this->admin->getCurrent($request)->client_id;
        $customerId = $clientId;
        $symbol = $request->get('symbol');

        $count = CustomerSub::where('customer_id', $clientId)->count();
        if ($count > 8) {
            return ['success' => 0, 'result' => __('ts.sub-client add limit')];
        }

        $validator = Validator::make($request->all(), [
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
            'is_lock',
        );
        $data['customer_id'] = $clientId;
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
        $clientId = $this->admin->getCurrent($request)->client_id;
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
        CustomerSub::where('id', $id)->where('customer_id', $clientId)->update($data);
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
        $clientId = $this->admin->getCurrent($request)->client_id;
        $before = CustomerSub::select(
            'symbol',
            'remark',
            'customer_id',
            'is_lock',
        )->where('id', $id)->first();
        if (empty($before)) {
            return ['success' => 0, 'result' => __('ts.id error')];
        }
        CustomerSub::where('id', $id)->where('customer_id', $clientId)->delete();
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
}
