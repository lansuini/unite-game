<?php

return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "NewJettonSwitch", "field" => "NewJettonSwitch", "val" => null, "valType" => "integer", "re" => trim("个人筹码位置切换开关,1筹码设置json生效,0客户端设置筹码生效")],
        ["name" => "NormalBetArea", "field" => "NormalBetArea", "val" => "0", "valType" => "select", "re" => trim('普通下注区域(分)'), "options" => [
            100 => 100,
            500 => 500,
            1000 => 1000,
            2000 => 2000,
            5000 => 5000,
            10000 => 10000,
            50000 => 50000,
            100000 => 100000,
            500000 => 500000,
            1000000 => 1000000,
        ]],

        ["name" => "robot_big_rate", "field" => "robot_big_rate", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注概率%')],
        ["name" => "robot_min_big_bet_gold_rate", "field" => "robot_min_big_bet_gold_rate", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注最小倍数')],
        ["name" => "robot_max_big_bet_gold_rate", "field" => "robot_max_big_bet_gold_rate", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注最大倍数')],
        ["name" => "robot_min_big_bet_gold", "field" => "robot_min_big_bet_gold", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注最小基础金币 元')],
        ["name" => "robot_max_big_bet_gold", "field" => "robot_max_big_bet_gold", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注最大基础金币 元')],
        ["name" => "robot_big_bet_max_area", "field" => "robot_big_bet_max_area", "val" => "0", "valType" => "integer", "re" => trim('机器人下大注区域上限')],
        ["name" => "chips_rate", "field" => "chips_rate", "val" => null, "valNum" => 10, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('各个下注筹码概率(分)')],
        ["name" => "IsCankBanker", "field" => "IsCankBanker", "val" => "0", "valType" => "select", "re" => trim('是否开启上注'), "options" => [
            0 => trim('否'),
            1 => trim('是'),
        ]],
        ["name" => "RobotBankerRate", "field" => "RobotBankerRate", "val" => "0", "valType" => "integer", "re" => trim('机器人上庄概率%')],
        ["name" => "control_state", "field" => "control_state", "val" => "0", "valType" => "select", "re" => trim('控制挡位  0=随机,1=杀,2=放'), "options" => [
            0 => trim('随机'),
            1 => trim('杀'),
            2 => trim('放'),
        ]],
        ["name" => "jetton", "field" => "jetton", "val" => null, "valNum" => 5, 'require' => 5, "options" => [
            100 => 100,
            500 => 500,
            1000 => 1000,
            2000 => 2000,
            5000 => 5000,
            10000 => 10000,
            50000 => 50000,
            100000 => 100000,
            500000 => 500000,
            1000000 => 1000000,
        ], "valType" => "arrayVal", "valType2" => "select", "valSubType" => "integer", "re" => trim('用户个人筹码设置(分)')],
    ]],

    ["name" => "VipBetInfo", "field" => "VipBetInfo", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => trim("概率%")],
            ["name" => "index", "field" => "index", "val" => null, "valType" => "integer", "re" => trim("座位号")],
            ["name" => "minnum", "field" => "minnum", "val" => null, "valType" => "integer", "re" => trim("最小筹码(分)")],
            ["name" => "maxnum", "field" => "maxnum", "val" => null, "valType" => "integer", "re" => trim("最大筹码(分)")],
        ]
    ], 're' => trim('贵宾席机器人下注信息')],
];
