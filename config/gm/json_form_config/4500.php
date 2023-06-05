<?php

/**
 * 百人牛牛
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "NewJettonSwitch", "field" => "NewJettonSwitch", "val" => null, "valType" => "integer", "re" => trim("个人筹码位置切换开关,1筹码设置json生效,0客户端设置筹码生效")],
        // ["name" => "play_mode", "field" => "play_mode", "val" => null, "valType" => "integer", "re" => trim('玩法 0 - 五倍场 1 - 十倍场')],
        ["name" => "play_mode", "field" => "play_mode", "val" => "0", "valType" => "select", "re" => trim('玩法 0 - 五倍场 1 - 十倍场'), "options" => [
            0 => trim('五倍场'),
            1 => trim('十倍场'),
        ]],
        ["name" => "RobotBankerRate", "field" => "RobotBankerRate", "val" => "0", "valType" => "integer", "re" => trim('机器人上庄概率%')],
        ["name" => "RobotMinGold", "field" => "RobotMinGold", "val" => "100000", "valType" => "integer", "re" => trim('机器人携带最小金币(元)')],
        ["name" => "RobotMaxGold", "field" => "RobotMaxGold", "val" => null, "valType" => "integer", "re" => trim('机器人携带最大金币(元)')],
        ["name" => "BankerUp", "field" => "BankerUp", "val" => null, "valType" => "integer", "re" => trim('房间上庄金币要求(元)')],
        ["name" => "BankerRobotMaxGold", "field" => "BankerRobotMaxGold", "val" => null, "valType" => "integer", "re" => trim('机器人上庄携带最大金币(元)')],
        ["name" => "HonorGuestMin", "field" => "HonorGuestMin", "val" => null, "valType" => "integer", "re" => trim('vip座位机器人最低携带金币(元)')],
        ["name" => "RobotLeave", "field" => "RobotLeave", "val" => null, "valType" => "integer", "re" => trim('机器人金币小于xxx离开(分)')],
        ["name" => "RobotMax", "field" => "RobotMax", "val" => null, "valType" => "integer", "re" => trim('机器人携带金币达到xxx离开(分)')],
        ["name" => "UserWinRate", "field" => "UserWinRate", "val" => null, "valType" => "integer", "re" => trim('真人玩家库存随机状态下的赢率%')],
        ["name" => "UserWinReChoice", "field" => "UserWinReChoice", "val" => null, "valType" => "integer", "re" => trim('玩家赢钱重洗牌')],

        ["name" => "bet_info", "field" => "bet_info", "val" => null, "valNum" => 5, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('机器人下注筹码(分)')],
        ["name" => "guest_bet_info", "field" => "guest_bet_info", "val" => null, "valNum" => 5, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('vip座位机器人下注筹码(分)')],

        // ["name" => "control_state", "field" => "control_state", "val" => null, "valType" => "integer", "re" => trim('控制 1.控制偏向庄家发牌, 2.偏向闲家发牌. 0.正常发牌')],
        ["name" => "control_state", "field" => "control_state", "val" => "0", "valType" => "select", "re" => trim('控制挡位  0=随机,1=杀,2=放'), "options" => [
            0 => trim('随机'),
            1 => trim('杀'),
            2 => trim('放'),
        ]],

        // ["name" => "banker_times", "field" => "banker_times", "val" => null, "valNum" => 4, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('抢庄倍数1234倍')],
        // ["name" => "bet_times", "field" => "bet_times", "val" => null, "valNum" => 5, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('下注倍数1,2,4,8,16')],

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
        ["name" => "chips_rate", "field" => "chips_rate", "val" => null, "valNum" => 10, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('各个下注筹码概率%')],
        ["name" => "IsCankBanker", "field" => "IsCankBanker", "val" => "0", "valType" => "select", "re" => trim('是否开启上注'), "options" => [
            0 => trim('否'),
            1 => trim('是'),
        ]],
        // ["name" => "nomal_control_tian", "field" => "nomal_control_tian", "val" => "0", "valType" => "select", "re" => trim('天区域第三方挡位'), "options" => [
        //     0 => trim('正常'),
        //     1 => trim('杀'),
        //     2 => trim('放'),
        // ]],
        // ["name" => "nomal_control_di", "field" => "nomal_control_di", "val" => "0", "valType" => "select", "re" => trim('地区域第三方挡位'), "options" => [
        //     0 => trim('正常'),
        //     1 => trim('杀'),
        //     2 => trim('放'),
        // ]],
        // ["name" => "nomal_control_xuan", "field" => "nomal_control_xuan", "val" => "0", "valType" => "select", "re" => trim('玄区域第三方挡位'), "options" => [
        //     0 => trim('正常'),
        //     1 => trim('杀'),
        //     2 => trim('放'),
        // ]],
        // ["name" => "nomal_control_huang", "field" => "nomal_control_huang", "val" => "0", "valType" => "select", "re" => trim('黄区域第三方挡位'), "options" => [
        //     0 => trim('正常'),
        //     1 => trim('杀'),
        //     2 => trim('放'),
        // ]],

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


        ["name" => "BankerListMin", "field" => "BankerListMin", "val" => null, "valType" => "integer", "re" => trim('上庄列表最小当庄数,包含机器人,0关闭填充机器人')],
        ["name" => "BankerListMax", "field" => "BankerListMax", "val" => null, "valType" => "integer", "re" => trim('上庄列表最大当庄数,包含机器人,0关闭填充机器人')],
        
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
