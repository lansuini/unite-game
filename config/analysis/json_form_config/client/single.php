<?php
return [
    ["name" => "Monitor", "field" => "Monitor", "valType" => "array", "val" => [
        
        ["name" => "Report", "field" => "report", "val" => "0", "valType" => "select", "re" => "", "options" => [
            0 => "No",
            1 => "Yes",
        ]],
    ], 're' => "", 'layout' => 0, 'md' => 3],

    ["name" => "ServerRequestType", "field" => "ServerRequestType", "valType" => "array", "val" => [
        ["name" => "Total", "field" => "total", "val" => "1/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "VerifySession", "field" => "VerifySession", "val" => "1/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "CashGet", "field" => "CashGet", "val" => "1/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
        ["name" => "CashTransferInOut", "field" => "CashTransferInOut", "val" => "1/500/10/1/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
    ], 're' => "ValueFormat:[isChecker/SlowTime/SlowTimeCount/ErrorCount/Flow]", 'layout' => 1, 'md' => 3],

    ["name" => "ServerPostType", "field" => "ServerPostType", "valType" => "array", "val" => [
        ["name" => "Total", "field" => "total", "val" => "0/200/10/0/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],   
        ["name" => "redirect", "field" => "redirect(single)", "val" => "1/200/10/0/350", "valType" => "string", "valCheckType" => "MonitorFormat", "re" => ''],
    ], 're' => "ValueFormat:[isChecker/SlowTime/SlowTimeCount/ErrorCount/Flow]", 'layout' => 2, 'md' => 3],
    
    ["name" => "Queue", "field" => "Queue", "valType" => "array", "val" => [
        ["name" => "normal", "field" => "normal", "val" => "0", "valType" => "string", "valCheckType" => "NumbersFormat"],
        ["name" => "fail", "field" => "fail", "val" => "0", "valType" => "string", "valCheckType" => "NumbersFormat"],
    ], 're' => "", 'layout' => 3, 'md' => 3],
];