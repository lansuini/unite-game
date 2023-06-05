<?php

/**
4800-21点JSON配置
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "tiyan", "field" => "tiyan", "defaultValue" => null, "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "defaultValue" => null, "valType" => "integer", "re" => trim('体验金(分)')],
        ["name" => "min_score", "field" => "min_score", "defaultValue" => null, "valType" => "integer", "re" => trim('最小下注(分)')],
        ["name" => "max_score", "field" => "max_score", "defaultValue" => null, "valType" => "integer", "re" => trim('最大下注(分)')],
        ["name" => "jettons", "field" => "jettons", "defaultValue" => null, "valType" => "string", "re" => trim('用户个人筹码设置 100,500,1000,2000,10000(分,必须设置5个筹码)')],
        ["name" => "control_type", "field" => "control_type", "val" => "0", "valType" => "select", "re" => trim('第三方控制类型'), "options" => [
            0 => trim("随机"),
            1 => trim("杀"),
            2 => trim("放"),
        ]],
        ["name" => "RobotMinBet", "field" => "RobotMinBet", "val" => null, "valType" => "integer", "re" => trim("机器人最小下注(元)")],
        ["name" => "RobotMaxBet", "field" => "RobotMaxBet", "val" => null, "valType" => "integer", "re" => trim("机器人最大下注(元)")],
    ]],

    ["name" => "robot", "field" => "robot", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer"],
        ],
    ], 're' => trim('每桌有多少个机器人及概率%')],

    ["name" => "robot_leave", "field" => "robot_leave", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "minute", "field" => "minute", "val" => null, "valType" => "integer", "re" => trim("机器人离开分钟数")],
            ["name" => "chance", "field" => "chance", "val" => null, "valType" => "integer", "re" => trim("机器人离开概率%")],
        ],
    ], 're' => trim('机器人玩xx时间离开房间的概率%')],

    ["name" => "robot_number", "field" => "robot_number", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "number", "field" => "number", "val" => null, "valType" => "integer", "re" => trim("单桌机器人数量")],
            ["name" => "chance", "field" => "chance", "val" => null, "valType" => "integer", "re" => trim("单桌机器人概率%")],
        ]
    ], 're' => trim('每桌有多少个机器人及概率%')],

    ["name" => "robot_config", "field" => "robot_config", "valType" => "array", "val" => [  
        ["name" => "robot_max_table", "field" => "robot_max_table", "val" => null, "valType" => "integer", "re" => trim("房间最大机器人数量")],
        ["name" => "robot_max_gold", "field" => "robot_max_gold", "val" => null, "valType" => "integer", "re" => trim("机器人最大携带金币(分)")],
        ["name" => "robot_max_pair", "field" => "robot_max_pair", "val" => null, "valType" => "integer", "re" => trim("适配机器人个数最大值,低于则往房间加机器人")],
    ], 're' => trim('一些其他的机器人设置')],
];