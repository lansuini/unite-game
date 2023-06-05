<?php
$menu = [
    [
        'is_menu' => 2,
        'sort' => 0,
        'name' => 'ts.Dashboard',
        'key' => 'Dashboard',
        'url' => 'dashboard/view',
        'icon' => 'mif-meter',
        'routes' => [
            [['GET'], 'dashboard/view'],
            [['GET'], 'dashboard'],
        ],
        'sub_menu_list' => [
            ['is_menu' => 0, 'sort' => 1, 'name' => 'ts.GetBaseData', 'key' => 'basedata', 'routes' => [
                [['GET'], 'getbasedata'],
                [['GET'], 'merchant/getbasedata'],
                
            ]],
            ['is_menu' => 0, 'sort' => 2, 'name' => 'ts.Lang', 'key' => 'lang', 'routes' => [
                [['POST'], 'admin/lang'],
            ]],
            ['is_menu' => 0, 'sort' => 3, 'name' => 'ts.UpdatePassword', 'key' => 'account_password_edit', 'routes' => [
                [['GET'], 'server/game/maintenance/view'],
                [['POST'], 'server/game/maintenance'],
            ]],
            ['is_menu' => 0, 'sort' => 4, 'name' => 'ts.UpdateGoogleCode', 'key' => 'account_google_code_edit', 'routes' => [
                [['GET'], 'manager/account/googlecode/view'],
                [['POST'], 'manager/account/googlecode'],
            ]],
        ]
    ],
    [
        'is_menu' => 1,
        'sort' => 2,
        'name' => 'ts.Player',
        'key' => 'Player',
        'url' => '',
        'icon' => '',
        'sub_menu_list' => [
            ['is_menu' => 1, 'sort' => 1, 'name' => 'ts.Account', 'key' => 'player_account', 'url' => 'player/account/view', 'routes' => [
                [['GET'], 'player/account/view'],
                [['GET'], 'player/account'],
            ]],
            ['is_menu' => 1, 'sort' => 2, 'name' => 'ts.LoginLog', 'key' => 'player_login_log', 'url' => 'player/loginlog/view', 'routes' => [
                [['GET'], 'player/loginlog/view'],
                [['GET'], 'player/loginlog'],
            ]],
            ['is_menu' => 1, 'sort' => 3, 'name' => 'ts.PlayLog', 'key' => 'player_play_log', 'url' => 'player/playlog/view', 'routes' => [
                [['GET'], 'player/playlog/view'],
                [['GET'], 'player/playlog'],
                [['GET'], 'player/playlogdetail'],
            ]],
            ['is_menu' => 1, 'sort' => 4, 'name' => 'ts.GoldLog', 'key' => 'player_gold_log', 'url' => 'player/goldlog/view', 'routes' => [
                [['GET'], 'player/goldlog/view'],
                [['GET'], 'player/goldlog'],
            ]],
            ['is_menu' => 1, 'sort' => 5, 'name' => 'ts.Real-OnlinePlay', 'key' => 'player_real_online_play', 'url' => 'player/realonlineplay/view', 'routes' => [
                [['GET'], 'player/realonlineplay/view'],
                [['GET'], 'player/realonlineplay'],
            ]],
            ['is_menu' => 1, 'sort' => 6, 'name' => 'ts.Live Match', 'key' => 'player_live_match', 'url' => 'player/livematch/view', 'routes' => [
                [['GET'], 'player/livematch/view'],
                [['GET'], 'player/livematch'],
            ]],
            ['is_menu' => 1, 'sort' => 7, 'name' => 'ts.Online', 'key' => 'player_online', 'url' => 'player/online/view', 'routes' => [
                [['GET'], 'player/online/view'],
                [['GET'], 'player/online'],
            ]],
            ['is_menu' => 1, 'sort' => 8, 'name' => 'ts.RoomWinLose', 'key' => 'player_room_win_lose', 'url' => 'player/roomwinlose/view', 'routes' => [
                [['GET'], 'player/roomwinlose/view'],
                [['GET'], 'player/roomwinlose'],
            ]],
            // ['is_menu' => 1, 'sort' => 9, 'name' => 'ts.DataReport', 'key' => 'data_report', 'url' => 'player/datareport/view', 'routes' => [
            //     [['GET'], 'player/datareport/view'],
            //     [['GET'], 'player/datareport'],
            // ]],
            // ['is_menu' => 0, 'sort' => 10, 'name' => 'ts.DataReportExport', 'key' => 'data_report_export', 'url' => '', 'routes' => []],
            // ['is_menu' => 1, 'sort' => 11, 'name' => 'ts.Sub-DataReport', 'key' => 'sub_data_report', 'url' => 'player/subdatareport/view', 'routes' => [
            //     [['GET'], 'player/subdatareport/view'],
            //     [['GET'], 'player/subdatareport'],
            // ]],
            // ['is_menu' => 0, 'sort' => 12, 'name' => 'ts.Sub-DataReportExport', 'key' => 'sub_data_report_export', 'url' => '', 'routes' => []],
        ]
    ],
    [
        'is_menu' => 1,
        'sort' => 3,
        'name' => 'ts.API',
        'key' => 'APIData',
        'url' => '',
        'icon' => '',
        'sub_menu_list' => [
            ['is_menu' => 1, 'sort' => 1, 'name' => 'ts.TransferInOutLog', 'key' => 'apidata_transferinout', 'url' => 'apidata/transferinout/view', 'routes' => [
                [['GET'], 'apidata/transferinout/view'],
                [['GET'], 'apidata/transferinout'],
            ]],
            ['is_menu' => 1, 'sort' => 2, 'name' => 'ts.GameDetailsLog', 'key' => 'apidata_gamedetails', 'url' => 'apidata/gamedetails/view', 'routes' => [
                [['GET'], 'apidata/gamedetails/view'],
                [['GET'], 'apidata/gamedetails'],
            ]],
            ['is_menu' => 1, 'sort' => 3, 'name' => 'ts.DataReport', 'key' => 'data_report', 'url' => 'apidata/datareport/view', 'routes' => [
                [['GET'], 'apidata/datareport/view'],
                [['GET'], 'apidata/datareport'],
            ]],
            ['is_menu' => 0, 'sort' => 4, 'name' => 'ts.DataReportExport', 'key' => 'data_report_export', 'url' => '', 'routes' => []],
            ['is_menu' => 1, 'sort' => 5, 'name' => 'ts.Sub-DataReport', 'key' => 'data_report', 'url' => 'apidata/subdatareport/view', 'routes' => [
                [['GET'], 'apidata/subdatareport/view'],
                [['GET'], 'apidata/subdatareport'],
            ]],
            ['is_menu' => 0, 'sort' => 6, 'name' => 'ts.Sub-DataReportExport', 'key' => 'sub_data_report_export', 'url' => '', 'routes' => []],
            ['is_menu' => 1, 'sort' => 7, 'name' => 'ts.API Document', 'key' => 'api_document', 'url' => 'support/apidocument/view', 'routes' => [
                [['GET'], 'support/apidocument/view'],
                [['GET'], 'support/apidocument'],
            ]],
        ]
    ],
    [
        'is_menu' => 1,
        'sort' => 4,
        'name' => 'ts.Customer',
        'key' => 'customer',
        'url' => '',
        'icon' => '',
        'sub_menu_list' => [
            ['is_menu' => 1, 'sort' => 1, 'name' => 'ts.Client', 'key' => 'customer_client', 'url' => 'customer/client/view', 'routes' => [
                [['GET'], 'customer/client/view'],
                [['GET', 'POST'], 'customer/client'],
                [['GET', 'PATCH', 'DELETE'], 'customer/client/{id}'],
                [['GET', 'PATCH'], 'customer/client/json/{id}'],
            ]],
            ['is_menu' => 1, 'sort' => 1, 'name' => 'ts.Sub-Client', 'key' => 'customer_sub_client', 'url' => 'customer/subclient/view', 'routes' => [
                [['GET'], 'customer/subclient/view'],
                [['GET', 'POST'], 'customer/subclient'],
                [['GET', 'PATCH', 'DELETE'], 'customer/subclient/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 2, 'name' => 'ts.Merchant', 'key' => 'merchant_manage_admin', 'url' => 'merchant/manager/account/view', 'routes' => [
                [['GET'], 'merchant/manager/account/view'],
                [['GET', 'POST'], 'merchant/manager/account'],
                [['GET', 'PATCH', 'DELETE'], 'merchant/manager/account/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 3, 'name' => 'ts.MerchantRole', 'key' => 'merchant_manage_role', 'url' => 'merchant/manager/role/view', 'routes' => [
                [['GET'], 'merchant/manager/role/view'],
                [['GET', 'POST'], 'merchant/manager/role'],
                [['GET', 'PATCH', 'DELETE'], 'merchant/manager/role/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 4, 'name' => 'ts.MerchantLoginLog', 'key' => 'merchant_manage_login_log', 'url' => 'merchant/manager/loginlog/view', 'routes' => [
                [['GET'], 'merchant/manager/loginlog/view'],
                [['GET'], 'merchant/manager/loginlog'],
            ]],
            ['is_menu' => 1, 'sort' => 5, 'name' => 'ts.MerchantActionLog', 'key' => 'merchant_manage_action_log', 'url' => 'merchant/manager/actionlog/view', 'routes' => [
                [['GET'], 'merchant/manager/actionlog/view'],
                [['GET'], 'merchant/manager/actionlog'],
                [['GET'], 'merchant/manager/actionlog/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 6, 'name' => 'ts.ServerRequestLog', 'key' => 'customer_server_request_log', 'url' => 'customer/serverrequestlog/view', 'routes' => [
                [['GET'], 'customer/serverrequestlog/view'],
                [['GET'], 'customer/serverrequestlog'],
                [['POST', 'GET'], 'customer/serverrequestlog/{clientId}/{id}'],
            ]],
            ['is_menu' => 1, 'sort' => 7, 'name' => 'ts.ServerPostLog', 'key' => 'customer_server_post_log', 'url' => 'customer/serverpostlog/view', 'routes' => [
                [['GET'], 'customer/serverpostlog/view'],
                [['GET'], 'customer/serverpostlog'],
                // [['POST'], 'customer/serverpostlog/{id}'],
            ]],
        ]
    ],
    [
        'is_menu' => 1,
        'sort' => 999,
        'name' => 'ts.AdminManage',
        'key' => 'account_manage',
        'url' => '',
        'icon' => '',
        'sub_menu_list' => [
            ['is_menu' => 1, 'sort' => 1, 'name' => 'ts.Account', 'key' => 'manage_admin', 'url' => 'manager/account/view', 'routes' => [
                [['GET'], 'manager/account/view'],
                [['GET', 'POST'], 'manager/account'],
                [['GET', 'PATCH', 'DELETE'], 'manager/account/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 2, 'name' => 'ts.Role', 'key' => 'manage_role', 'url' => 'manager/role/view', 'routes' => [
                [['GET'], 'manager/role/view'],
                [['GET', 'POST'], 'manager/role'],
                [['GET', 'PATCH', 'DELETE'], 'manager/role/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 3, 'name' => 'ts.LoginLog', 'key' => 'manage_login_log', 'url' => 'manager/loginlog/view', 'routes' => [
                [['GET'], 'manager/loginlog/view'],
                [['GET'], 'manager/loginlog'],
            ]],
            ['is_menu' => 1, 'sort' => 4, 'name' => 'ts.ActionLog', 'key' => 'manage_action_log', 'url' => 'manager/actionlog/view', 'routes' => [
                [['GET'], 'manager/actionlog/view'],
                [['GET'], 'manager/actionlog'],
                [['GET'], 'manager/actionlog/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 5, 'name' => 'ts.Currency', 'key' => 'manage_currency', 'url' => 'manager/currency/view', 'routes' => [
                [['GET'], 'manager/currency/view'],
                [['GET', 'POST'], 'manager/currency'],
                [['GET', 'PATCH', 'DELETE'], 'manager/currency/{id}']
            ]],
            ['is_menu' => 1, 'sort' => 6, 'name' => 'ts.Export', 'key' => 'manage_export_file', 'url' => 'export/view', 'routes' => [
                [['GET'], 'export/view'],
                [['GET'], 'export/download/{id}'],
                [['GET', 'POST'], 'export'],
            ]],
        ],
    ],
];

return $menu;
