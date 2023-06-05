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

        ["name" => "tiyan", "field" => "tiyan", "val" => "1", "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "jinhua", "field" => "jinhua", "val" => "100", "valType" => "integer", "re" => trim('机器人出现金花牌型概率%')],
        ["name" => "shunqing", "field" => "shunqing", "val" => "100", "valType" => "integer", "re" => trim('机器人出现顺金牌型概率%')],
        ["name" => "shunzi", "field" => "shunzi", "val" => "100", "valType" => "integer", "re" => trim('机器人出现顺子牌型概率%')],
        ["name" => "duizi", "field" => "shunzi", "val" => "100", "valType" => "integer", "re" => trim('机器人出现对子牌型概率%')],
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
    ], 're' => trim('一些其他的机器人设置')],
];