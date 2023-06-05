<?php

use App\Models\Manager\Analysis\Role;
use App\Models\NodeEntrance;
use App\Models\Customer;
use App\Models\CustomerSub;
use Illuminate\Http\Request;

return [
    'testItems' => [
        'Y' => 'ts.Yes',
        'N' => 'ts.No',
    ],
    'accountType' => [
        ['key' => '2', 'value' => 'ts.Normal', 'txt-class' => 'text-info'],
        ['key' => '1', 'value' => 'ts.Guest', 'txt-class' => 'text-warning'],
        ['key' => '100', 'value' => 'ts.Robot', 'txt-class' => 'text-danger'],
    ],
    'riskUserType' => [
        ['key' => '0', 'value' => 'ts.Normal', 'txt-class' => 'text-info'],
        ['key' => '1', 'value' => 'ts.Control', 'txt-class' => 'text-danger'],
    ],
    'bannedType' => [
        ['key' => '0', 'value' => 'ts.-', 'txt-class' => ''],
        ['key' => '1', 'value' => 'ts.Normal', 'txt-class' => 'text-info'],
        ['key' => '3', 'value' => 'ts.Control', 'txt-class' => 'text-success'],
    ],
    'accountSearchType' => [
        'player_name' => 'ts.PlayerName',
        'nickname' => 'ts.Nickname',
        'uid' => 'ts.UID',
    ],
    'accountSearchTimeType' => [
        'created' => 'ts.Created',
        'last_logon_time' => 'ts.LastLogonTime',
    ],
    'OSType' => [
        ['key' => '2', 'value' => 'ts.Android', 'txt-class' => 'text-info'],
        ['key' => '3', 'value' => 'ts.IOS', 'txt-class' => 'text-success'],
    ],
    'lockType' => [
        ['key' => '0', 'value' => 'ts.Normal', 'txt-class' => 'text-info'],
        ['key' => '1', 'value' => 'ts.Locked', 'txt-class' => 'text-danger'],
    ],
    'billType' => [
        ['key' => '54', 'value' => 'ts.Game Cost', 'txt-class' => 'text-primary'],
        ['key' => '56', 'value' => 'ts.On-stage fee', 'txt-class' => 'text-info'],
        ['key' => '63', 'value' => 'ts.Player wins tax money', 'txt-class' => 'text-warning'],
        ['key' => '70', 'value' => 'ts.Prize Pool', 'txt-class' => 'text-danger'],
        ['key' => '62', 'value' => 'ts.Handred Bet', 'txt-class' => 'text-info'],
        ['key' => '64', 'value' => 'ts.Bet Return', 'txt-class' => 'text-info'],
    ],
    'transferInOutStatusType' => [
        ['key' => '0', 'value' => 'ts.Wait', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Finish', 'txt-class' => 'text-success'],
    ],
    'apiModeType' => [
        '0' => 'ts.Single',
        '1' => 'ts.Transfer',
    ],
    'bindGoogleCodeType' => [
        '0' => 'ts.Unbind',
        '1' => 'ts.Bound',
    ],
    'roleType' => function () {
        return Role::pluck('name', 'id')->toArray();
    },
    'playType' => function () {
        return NodeEntrance::pluck('method_name', 'game_id')->toArray();
    },
    'serverRequestType' => [
        1 => 'ts.VerifySession',
        2 => 'ts.CashGet',
        3 => 'ts.CashTransferInOut',
        4 => 'ts.VerifySession(Transfer)',
    ],
    'serverPostType' => [
        0 => 'ts.loginGame',
        1 => 'ts.getPlayerWallet',
        2 => 'ts.transferIn',
        3 => 'ts.transferOut',
        4 => 'ts.redirect',
        5 => 'ts.redirect(single)',
    ],
    'costTimeType1' => [
        0 => 'ts.Normal[<500ms]',
        1 => 'ts.Slow[>=500ms]',
        2 => 'ts.Fast[<=200ms]',
    ],
    'costTimeType2' => [
        0 => 'ts.Normal[<200ms]',
        1 => 'ts.Slow[>=200ms]',
        2 => 'ts.Fast[<=100ms]',
    ],
    'customerType' => function () {
        return Customer::pluck('company_name', 'id')->toArray();
    },
    'customerSubType' => function (Request $request) {
        $customerId = $request->input('customer_id', 0);
        $customerSub = new CustomerSub;
        if ($customerId > 0) {
            return $customerSub->where('customer_id', $customerId)->pluck('symbol', 'id')->toArray();
        }
        return $customerSub
            // ->select($customerSub->raw("concat('[', id, ']', symbol) as symbol"), "id")
            ->pluck('symbol', 'id')
            ->toArray();
    },
    'customerSubType2' => function () {
        $customerSub = new CustomerSub;
        return $customerSub
            // ->select($customerSub->raw("concat('[', id, ']', symbol) as symbol"), "id")
            ->pluck('symbol', 'id')
            ->toArray();
    },
    'customerAPIType1' => function () {
        $c = Customer::orderBy('api_mode', 'asc')->get();
        $r = [];
        foreach ($c as $v) {
            $txt = $v->api_mode == 1 ? 'Transfer' : 'Single';
            $r[] = ['key' => $v->id, 'value' => $v->company_name . '[' . $txt . ']'];
        }
        return $r;
    },
    'customerAPIType2' => function () {
        $c = Customer::orderBy('api_mode', 'desc')->get();
        $r = [];
        foreach ($c as $v) {
            $txt = $v->api_mode == 1 ? 'Transfer' : 'Single';
            $r[] = ['key' => $v->id, 'value' => $v->company_name . '[' . $txt . ']'];
        }
        return $r;
    },
    'successType' => [
        ['key' => '0', 'value' => 'ts.Fail', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Succ', 'txt-class' => 'text-success'],
    ],
    'success2Type' => [
        ['key' => '0', 'value' => 'ts.Undefined', 'txt-class' => 'text-warning'],
        ['key' => '1', 'value' => 'ts.Succ', 'txt-class' => 'text-success'],
        ['key' => '2', 'value' => 'ts.Fail', 'txt-class' => 'text-danger'],
    ],
    'experienceType' => [
        ['key' => '0', 'value' => 'ts.Not Exp Room', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Exp Room', 'txt-class' => 'text-success'],
    ],
    'enabledType' => [
        ['key' => '0', 'value' => 'ts.Stop', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Enable', 'txt-class' => 'text-success'],
    ],

    'processControlGameType' => [ // 0约战1普通2百人3私人房
        ['key' => '0', 'value' => 'ts.Dating', 'txt-class' => 'text-danger'],
        ['key' => '1', 'value' => 'ts.Normal', 'txt-class' => 'text-success'],
        ['key' => '2', 'value' => 'ts.Hundreds of people', 'txt-class' => 'text-info'],
        ['key' => '3', 'value' => 'ts.private room', 'txt-class' => 'text-dark'],
    ],

    'resultType' => [
        ['key' => '1', 'value' => 'ts.LOSE', 'txt-class' => 'text-danger'],
        ['key' => '2', 'value' => 'ts.WIN', 'txt-class' => 'text-success'],
        ['key' => '3', 'value' => 'ts.DRAW', 'txt-class' => 'text-dark'],
    ],

    'gameAliasType' => function () {
        $data = config('gm.game_alias');
        $new = [];
        foreach ($data as $k => $v) {
            $new[] = ['key' => $k, 'value' => $v['name']];
        }
        return $new;
    },

    'gameAliasType2' => function () {
        $data = config('gm.game_alias');
        $new = [];
        $new[] = ['key' => 0, 'value' => 'Web-Lobby'];
        foreach ($data as $k => $v) {
            $new[] = ['key' => $k, 'value' => $v['name']];
        }
        return $new;
    },

    'actionType' => [
        'ANALYSIS_MANAGER_ADMIN_EDIT_PASSWORD' => 'ts.update admin password',
        'ANALYSIS_MANAGER_ADMIN_EDIT_GOOGLECODE' => 'ts.update admin googleCode',
        'ANALYSIS_MANAGER_ADMIN_CREATE' => 'ts.create admin',
        'ANALYSIS_MANAGER_ADMIN_CREATE' => 'ts.create admin',
        'ANALYSIS_MANAGER_ADMIN_EDIT' => 'ts.update admin',
        'ANALYSIS_MANAGER_ADMIN_DELETE' => 'ts.remove admin',
        'ANALYSIS_MANAGER_ROLE_CREATE' => 'ts.create role',
        'ANALYSIS_MANAGER_ROLE_EDIT' => 'ts.update role',
        'ANALYSIS_MANAGER_ROLE_DELETE' => 'ts.remove role',

        'MERCHANT_MANAGER_ADMIN_EDIT_PASSWORD' => 'ts.update merchant admin password',
        'MERCHANT_MANAGER_ADMIN_EDIT_GOOGLECODE' => 'ts.update merchant admin googleCode',
        'MERCHANT_MANAGER_ADMIN_CREATE' => 'ts.create merchant admin',
        'MERCHANT_MANAGER_ADMIN_CREATE' => 'ts.create merchant admin',
        'MERCHANT_MANAGER_ADMIN_EDIT' => 'ts.update merchant admin',
        'MERCHANT_MANAGER_ADMIN_DELETE' => 'ts.remove merchant admin',
        'MERCHANT_MANAGER_ROLE_CREATE' => 'ts.create merchant role',
        'MERCHANT_MANAGER_ROLE_EDIT' => 'ts.update merchant role',
        'MERCHANT_MANAGER_ROLE_DELETE' => 'ts.remove merchant role',

        'SERVER_REQUEST_RETRY' => 'ts.server request retry',

        'MANAGER_CURRENCY_CREATE' => 'ts.create currency role',
        'MANAGER_CURRENCY_EDIT' => 'ts.update currency role',
        'MANAGER_CURRENCY_DELETE' => 'ts.remove currency role',
    ],

    'reasonType' => [

        54 => 'ts.Game Cost',
        56 => 'ts.On-stage fee',
        62 => 'ts.Handred Bet',
        63 => 'ts.Player wins tax money',
        70 => 'ts.Prize Pool',
        64 => 'ts.Bet Return',
        8001 => 'ts.transfer In',
        8002 => 'ts.transfer Out',

    ]
];
