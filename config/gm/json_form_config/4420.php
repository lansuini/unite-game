<?php

/**
 * 德州扑克花表单配置
 * 
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "level", "field" => "level", "val" => null, "valType" => "integer", "re" => trim('房间级别(初中高)')],
        ["name" => "tiyan", "field" => "tiyan", "val" => null, "valType" => "integer", "re" => trim('是否体验场')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => null, "valType" => "integer", "re" => trim('体验金(分)')],
        ["name" => "control_time", "field" => "control_time", "val" => null, "valType" => "integer", "re" => trim('延长的用户操作时间')],
        ["name" => "small_bet", "field" => "small_bet", "val" => null, "valType" => "integer", "re" => trim('小盲注金额')],
        ["name" => "gratuity", "field" => "gratuity", "val" => null, "valType" => "integer", "re" => trim('打赏金额')],
        ["name" => "chargerate", "field" => "chargerate", "val" => null, "valType" => "integer", "re" => trim('税收比例')],
        ["name" => "lose_rate", "field" => "lose_rate", "val" => null, "valType" => "integer", "re" => trim('输钱返佣比')],
        ["name" => "max_offline_trustee", "field" => "max_offline_trustee", "val" => null, "valType" => "integer", "re" => trim('最大掉线托管次数')],
        ["name" => "good_cards", "field" => "good_cards", "val" => null, "valType" => "integer", "re" => trim('机器人好牌率')],
        ["name" => "max_stay", "field" => "max_stay", "val" => null, "valType" => "integer", "re" => trim('提示观战超时的用户范围')],
        ["name" => "limit_play", "field" => "limit_play", "val" => "0", "valType" => "integer", "re" => trim('体验场限制玩牌局数')],

        ["name" => "king_tong_hua_shun", "field" => "king_tong_hua_shun", "val" => "0", "valType" => "integer", "re" => trim('至尊同花顺')],
        ["name" => "tong_hua_shun", "field" => "tong_hua_shun", "val" => "0", "valType" => "integer", "re" => trim('同花顺')],
        ["name" => "tie_zhi", "field" => "tie_zhi", "val" => "0", "valType" => "integer", "re" => trim('铁支')],
        ["name" => "hu_lu", "field" => "hu_lu", "val" => "0", "valType" => "integer", "re" => trim('葫芦')],
        ["name" => "tong_hua", "field" => "tong_hua", "val" => "0", "valType" => "integer", "re" => trim('同花')],
        ["name" => "shun_zi", "field" => "shun_zi", "val" => "0", "valType" => "integer", "re" => trim('顺子')],
        ["name" => "san_tiao", "field" => "san_tiao", "val" => "0", "valType" => "integer", "re" => trim('三条')],
        ["name" => "liang_dui", "field" => "liang_dui", "val" => "0", "valType" => "integer", "re" => trim('两对')],
        ["name" => "badcardtimes", "field" => "badcardtimes", "val" => null, "valType" => "integer", "re" => trim('坏牌次数')],
        ["name" => "maxcondiscardtimes", "field" => "maxcondiscardtimes", "val" => null, "valType" => "integer", "re" => trim('最大连续弃牌次数')],
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