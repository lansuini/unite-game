<?php

/**
 * 	德州牛牛表单配置
 */
return [

    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "chang_ci", "field" => "chang_ci", "val" => "0", "valType" => "integer", "re" => trim('体验场开关1 = 打开 0 = 关闭')],
        ["name" => "round", "field" => "round", "val" => null, "valType" => "integer", "re" => trim('最多局数(超过则不会自动开始下一局)')],
        ["name" => "public_joker", "field" => "public_joker", "val" => "0", "valType" => "integer", "re" => trim('公共牌大小王开关')],
        ["name" => "pay_coin", "field" => "pay_coin", "val" => "100000", "valType" => "integer", "re" => trim('房卡数量')],

        ["name" => "pay_type", "field" => "pay_type", "val" => null, "valType" => "integer", "re" => trim('房费支付类型：房主支付或AA支付,仅限房卡场')],
        ["name" => "rule_type", "field" => "rule_type", "val" => null, "valType" => "integer", "re" => trim('游戏规则类型 ：设置牛牛,抢庄牛牛玩法')],
        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "max_player_count", "field" => "max_player_count", "val" => null, "valType" => "integer", "re" => trim('单局最多xx人')],
        ["name" => "min_player_count", "field" => "min_player_count", "val" => null, "valType" => "integer", "re" => trim('最小xx人开局')],
        ["name" => "area_type", "field" => "area_type", "val" => null, "valType" => "integer", "re" => trim('游戏ID')],
        ["name" => "grab_banker_multi", "field" => "grab_banker_multi", "val" => null, "valType" => "integer", "re" => trim('最大抢庄倍数')],
        ["name" => "up_banker_score", "field" => "up_banker_score", "val" => null, "valType" => "integer", "re" => trim('上庄分数')],
        ["name" => "injection", "field" => "injection", "val" => null, "valType" => "integer", "re" => trim('推注')],
        ["name" => "tax", "field" => "tax", "val" => null, "valType" => "integer", "re" => trim('税收,千分比')],
        ["name" => "forbid_enter", "field" => "forbid_enter", "val" => null, "valType" => "integer", "re" => trim('游戏开始后禁止加入')],
        ["name" => "forbid_shuffle", "field" => "forbid_shuffle", "val" => null, "valType" => "integer", "re" => trim('禁止搓牌')],
        ["name" => "min_carry_coin", "field" => "min_carry_coin", "val" => null, "valType" => "integer", "re" => trim('最小携带货币数  未使用')],

        ["name" => "banker_times", "field" => "banker_times", "val" => null, "valNum" => 4, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('抢庄倍数1234倍')],
        ["name" => "bet_times", "field" => "bet_times", "val" => null, "valNum" => 5, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('下注倍数1,2,4,8,16')],
        ["name" => "limit_play", "field" => "limit_play", "val" => "0", "valType" => "integer", "re" => trim('体验场限制玩牌局数')],
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
    ], 're' => trim('一些其他的机器人设置')],
];