<?php
return [
    ["name" => "Monitor", "field" => "Monitor", "valType" => "array", "val" => [
        ["name" => "Report", "field" => "report", "val" => "0", "valType" => "select", "re" => "", "options" => [
            0 => "No",
            1 => "Yes",
        ]],
    ], 're' => "", 'layout' => 0, 'md' => 3],

    ["name" => "ServerRequestType", "field" => "ServerRequestType", "valType" => "array", "val" => [
        ["name" => "Total", "field" => "total", "val" => "0/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "VerifySession", "field" => "VerifySession(Transfer)", "val" => "1/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
    ], 're' => "ValueFormat:[isChecker/SlowTime/SlowTimeCount/ErrorCount/Flow]", 'layout' => 1, 'md' => 3],

    ["name" => "ServerPostType", "field" => "ServerPostType", "valType" => "array", "val" => [
        ["name" => "Total", "field" => "total", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],   
        ["name" => "LoginGame", "field" => "loginGame", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "GetPlayerWallet", "field" => "getPlayerWallet", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "TransferIn", "field" => "transferIn", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "TransferOut", "field" => "transferOut", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "Redirect", "field" => "redirect", "val" => "1/200/10/100/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
    ], 're' => "ValueFormat:[isChecker/SlowTime/SlowTimeCount/ErrorCount/Flow]", 'layout' => 2, 'md' => 3],
    
    // ["name" => "stock_form", "field" => "stock_form", "valType" => "textareaArray", "columns" => [
    //     [
    //         ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => "Minimum multiple"],
    //         ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => "Maximum multiple"],
    //         ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => "Probability weight"],
    //     ],
    // ], 're' => "Inventory Winning Table(format: min,max,rate)", 'layout' => 3, 'md' => 3],
];