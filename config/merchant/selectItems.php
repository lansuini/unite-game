<?php

use App\Models\Manager\Merchant\Role;
use App\Models\Manager\Merchant\Admin;
use App\Models\NodeEntrance;
use App\Models\Customer;
use App\Models\CustomerSub;
use App\Models\Node;

use Illuminate\Http\Request;
return [
    'testItems' => [
        'Y' => 'ts.Yes',
        'N' => 'ts.No',
    ],

    'customerType' => function () {
        return Customer::pluck('company_name', 'id')->toArray();
    },
    'bannedType' => [
        ['key' => '0', 'value' => 'ts.-', 'txt-class' => ''],
        ['key' => '1', 'value' => 'ts.Normal', 'txt-class' => 'text-info'],
        ['key' => '3', 'value' => 'ts.Control', 'txt-class' => 'text-success'],
    ],

    'OSType' => [
        ['key' => '2', 'value' => 'ts.Android', 'txt-class' => 'text-info'],
        ['key' => '3', 'value' => 'ts.IOS', 'txt-class' => 'text-success'],
    ],
    'lockType' => [
        '0' => 'ts.Normal',
        '1' => 'ts.Locked',
    ],
    'bindGoogleCodeType' => [
        '0' => 'ts.Unbind',
        '1' => 'ts.Bound',
    ],
    'roleType' => function () {
        return Role::pluck('name', 'id')->toArray();
    },
    'nodeType' => function () {
        return Node::pluck('name', 'id')->toArray();
    },
    'accountSearchType' => [
        'player_name' => 'ts.PlayerName',
        'nickname' => 'ts.Nickname',
        'uid' => 'ts.UID',
    ],
    'accountSearchTimeType' => [
        'created' => 'ts.Created',
        'last_logon_time' => 'ts.LastLogonTime',
    ],
    'successType' => [
        ['key' => '0', 'value' => 'ts.Fail', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Succ', 'txt-class' => 'text-success'],
    ],
    'success2Type' => [
        ['key' => '0', 'value' => 'ts.Undefined', 'txt-class' => 'text-warning'],
        ['key' => '1', 'value' => 'ts.Succ', 'txt-class' => 'text-success'],
        ['key' => '2', 'value' => 'ts.Fail', 'txt-class' => 'text-danger'],
    ],
    'gameAliasType' => function () {
        $data = config('gm.game_alias');
        $new = [];
        foreach ($data as $k => $v) {
            $new[] = ['key' => $k, 'value' => $v['name']];
        }
        return $new;
    },
    'customerSubType' => function (Request $request) {
        $admin = new Admin();
        $id = $admin->getLoginID($request);
        $admin = $admin->where('id', $id)->first();
        return CustomerSub::where('customer_id', $admin->client_id)->pluck('symbol', 'id')->toArray();
    },
    'customerSubType2' => function () {
        $customerSub = new CustomerSub;
        return $customerSub
            ->pluck('symbol', 'id')
            ->toArray();
    },
    'actionType' => [
        'MERCHANT_MANAGER_ADMIN_EDIT_PASSWORD' => 'ts.update admin password',
        'MERCHANT_MANAGER_ADMIN_EDIT_GOOGLECODE' => 'ts.update admin googleCode',

        'MERCHANT_MANAGER_ADMIN_CREATE' => 'ts.create admin',

        'MERCHANT_MANAGER_ADMIN_CREATE' => 'ts.create admin',
        'MERCHANT_MANAGER_ADMIN_EDIT' => 'ts.update admin',
        'MERCHANT_MANAGER_ADMIN_DELETE' => 'ts.remove admin',

        'MERCHANT_MANAGER_ROLE_CREATE' => 'ts.create role',
        'MERCHANT_MANAGER_ROLE_EDIT' => 'ts.update role',
        'MERCHANT_MANAGER_ROLE_DELETE' => 'ts.remove role',

    ],
];
