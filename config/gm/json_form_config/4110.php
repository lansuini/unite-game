<?php

/**
 * 炸金花表单配置
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "areatype", "field" => "areatype", "val" => null, "valType" => "integer", "re" => trim('游戏ID')],
        ["name" => "playcount", "field" => "playcount", "val" => null, "valType" => "integer", "re" => trim('游戏局数(-1代表不限制)')],
        ["name" => "playercount", "field" => "playercount", "val" => null, "valType" => "integer", "re" => trim('单局最多xx人')],
        ["name" => "minplayercount", "field" => "minplayercount", "val" => null, "valType" => "integer", "re" => trim('最小xx人开局')],
        ["name" => "dizhu", "field" => "dizhu", "val" => null, "valType" => "integer", "re" => trim('底注,10表示10.00金币')],
        ["name" => "ruchang", "field" => "ruchang", "val" => null, "valType" => "integer", "re" => trim('入场最低条件')],
        ["name" => "lichang", "field" => "lichang", "val" => null, "valType" => "integer", "re" => trim('最大离场金币： 分')],
        ["name" => "ruchangtimes", "field" => "ruchangtimes", "val" => null, "valType" => "integer", "re" => trim('入场下限是底注的几倍')],
        ["name" => "lichangtimes", "field" => "lichangtimes", "val" => null, "valType" => "integer", "re" => trim('离场下限是底注的几倍')],
        ["name" => "ruletype", "field" => "ruletype", "val" => null, "valType" => "integer", "re" => trim('规则类型：普通模式 0,激情模式 1')],
        ["name" => "menthreeround", "field" => "menthreeround", "val" => "0", "valType" => "select", "re" => trim('必闷三圈'), "options" => [
            1 => trim('是'),
            0 => trim('否'),
        ]],
        ["name" => "menpai_round", "field" => "menpai_round", "val" => null, "valType" => "integer", "re" => trim('焖牌次数')],

        ["name" => "roundsnumber", "field" => "roundsnumber", "val" => null, "valType" => "integer", "re" => trim('最大回合数')],
        ["name" => "paycreater", "field" => "paycreater", "val" => null, "valType" => "integer", "re" => trim('支付模式：房主支付')],
        ["name" => "payaa", "field" => "payaa", "val" => null, "valType" => "integer", "re" => trim('支付模式：AA支付')],
        ["name" => "tax", "field" => "tax", "val" => null, "valType" => "integer", "re" => trim('税收占比,千分比')],

        ["name" => "tiyan", "field" => "tiyan", "val" => 0, "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "limit_play", "field" => "limit_play", "val" => "0", "valType" => "integer", "re" => trim('体验场限制玩牌局数')],

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
        ["name" => "precheat", "field" => "PreCheat", "val" => null, "valType" => "integer", "re" => trim("防作弊模式(1:打乱分桌,2:优先真人分桌,同地区的和最近一起游戏的真人不同桌,没有符合的则安排机器人一桌,3:放入规定数目的机器人同桌(must_robot字段)；不配置则优先真人组桌,没有条件限制")],
        ["name" => "patch_pool_time", "field" => "patch_pool_time", "val" => 0, "valType" => "integer", "re" => trim("匹配池冷却时间,单位秒,0秒表示不启用")],
        ["name" => "limit_same_place", "field" => "limit_same_place", "val" => 0, "valType" => "integer", "re" => trim("限制同区域0关闭1打开")],
        ["name" => "limit_last_round", "field" => "limit_last_round", "val" => 0, "valType" => "integer", "re" => trim("上一局同桌限制0关闭1打开")],
        ["name" => "limit_same_plat", "field" => "limit_same_plat", "val" => 0, "valType" => "integer", "re" => trim("同平台限制0关闭1打开")],
    ], 're' => trim('一些其他的机器人设置')],
];