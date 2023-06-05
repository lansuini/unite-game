<?php

/**
 * 斗地主表单配置
 */
return [

    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [

        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "shuffle_count", "field" => "shuffle_count", "val" => 3, "valType" => "integer", "re" => trim('切牌次数')],
        ["name" => "shuffle_countex", "field" => "shuffle_countex", "val" => 3, "valType" => "integer", "re" => trim('洗牌数')],
        ["name" => "deal_count", "field" => "deal_count", "val" => null, "valType" => "integer", "re" => trim('发牌方式,每次从牌堆抽出几张牌')],
        ["name" => "bome_from", "field" => "bome_from", "val" => null, "valType" => "integer", "re" => trim('炸弹玩法中炸弹最小数量')],
        ["name" => "bome_to", "field" => "bome_to", "val" => null, "valType" => "integer", "re" => trim('炸弹玩法中炸弹最大数量')],
        ["name" => "bome_rate", "field" => "bome_rate", "val" => null, "valType" => "integer", "re" => trim('炸弹玩法出现概率%')],
        ["name" => "straight_rate", "field" => "straight_rate", "val" => null, "valType" => "integer", "re" => trim('顺子牌玩牌出现概率%')],
        ["name" => "norand_rate", "field" => "norand_rate", "val" => null, "valType" => "integer", "re" => trim('不洗牌玩法出现概率%')],

        ["name" => "super_switch", "field" => "super_switch", "val" => "0", "valType" => "integer", "re" => trim('超级加倍开关。0是关,1是开')],
        ["name" => "super_double", "field" => "super_double", "val" => "0", "valType" => "integer", "re" => trim('超级加倍的倍数。')],

        ["name" => "tiyan", "field" => "tiyan", "val" => "0", "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => "100000", "valType" => "integer", "re" => trim('体验金(分)')],
        ["name" => "limit_play", "field" => "limit_play", "val" => "0", "valType" => "integer", "re" => trim('体验场限制玩牌局数')],
//        ["name" => "robot_per_add", "field" => "robot_per_add", "val" => "0", "valType" => "integer", "re" => trim('每秒加入队列机器人个数')],

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
        ["name" => "precheat", "field" => "PreCheat", "val" => null, "valType" => "integer", "re" => trim("防作弊模式(1:打乱分桌,2:优先真人分桌,同地区的和最近一起游戏的真人不同桌,没有符合的则安排机器人一桌,3:放入规定数目的机器人同桌(must_robot字段)；不配置则优先真人组桌,没有条件限制")],
        ["name" => "patch_pool_time", "field" => "patch_pool_time", "val" => 0, "valType" => "integer", "re" => trim("匹配池冷却时间,单位秒,0秒表示不启用")],
        ["name" => "limit_same_place", "field" => "limit_same_place", "val" => 0, "valType" => "integer", "re" => trim("限制同区域0关闭1打开")],
        ["name" => "limit_last_round", "field" => "limit_last_round", "val" => 0, "valType" => "integer", "re" => trim("上一局同桌限制0关闭1打开")],
        ["name" => "limit_same_plat", "field" => "limit_same_plat", "val" => 0, "valType" => "integer", "re" => trim("同平台限制0关闭1打开")],
        ["name" => "must_robot", "field" => "must_robot", "val" => null, "valType" => "integer", "re" => trim("每桌必须xx机器人才开始")],
        ["name" => "robot_per_add", "field" => "robot_per_add", "val" => 0, "valType" => "integer", "re" => trim('每秒加入队列机器人个数')],
    ], 're' => trim('一些其他的机器人设置')],
];