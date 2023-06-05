<?php

/**
 * 	李逵表单配置
 */
return [

    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [

        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "tiyan", "field" => "tiyan", "val" => "0", "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "tiyan_rate", "field" => "tiyan_rate", "val" => "0", "valType" => "integer", "re" => trim('体验场概率加成')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => "100000", "valType" => "integer", "re" => trim('体验金(分)')],

        ["name" => "link_reduce", "field" => "link_reduce", "val" => null, "valType" => "integer", "re" => trim('闪电鱼减少概率%')],
        ["name" => "connon_price", "field" => "connon_price", "val" => null, "valType" => "string", "re" => trim('当前场次的炮数。10/20/30表示0.1/0.2/0.3金币')],
        ["name" => "bird_rate", "field" => "bird_rate", "val" => null, "valType" => "string", "re" => trim('捕获概率,万分比,每条鱼对应概率%')],
        ["name" => "fish_limit", "field" => "fish_limit", "val" => null, "valType" => "string", "re" => trim('打死某条鱼需要最小炮数, 25表示打死xx鱼最少需要25炮')],
        ["name" => "fire_limit", "field" => "fire_limit", "val" => null, "valType" => "string", "re" => trim('打死某条鱼需要最小分值, 25表示打死xx鱼最少需要0.25金币')],
        ["name" => "fish_ratio", "field" => "fish_ratio", "val" => null, "valType" => "string", "re" => trim('根据房间人数生成鱼的数量')],
        ["name" => "limit_time", "field" => "limit_time", "val" => "0", "valType" => "integer", "re" => trim('体验场限制时间')],

        ["name" => "room_preinstall", "field" => "room_preinstall", "val" => null, "valType" => "integer", "re" => trim('房间配置项')],
        ["name" => "room_adjust", "field" => "room_adjust", "val" => null, "valType" => "integer", "re" => trim('微调配置项')],

        ["name" => "vibra_curve_switch", "field" => "vibra_curve_switch", "val" => "0", "valType" => "select", "re" => trim('曲线控制开关'), "options" => [
            1 => trim('开'),
            0 => trim('关'),
        ]],
        ["name" => "total_recharge_threshold", "field" => "total_recharge_threshold", "val" => null, "valType" => "integer", "re" => trim('充值门槛')],
        ["name" => "multi_recharge_bill", "field" => "multi_recharge_bill", "val" => null, "valType" => "integer", "re" => trim('流水门槛,总流水达到多少倍失效')],

        ["name" => "curve_cycle_min_time", "field" => "curve_cycle_min_time", "val" => null, "valType" => "integer", "re" => trim('最小循环次数')],
        ["name" => "curve_cycle_max_time", "field" => "curve_cycle_max_time", "val" => null, "valType" => "integer", "re" => trim('最大循环次数')],
    ]],
    
    ["name" => "target", "field" => "target", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "target_top_winscore", "field" => "target_top_winscore", "val" => null, "valType" => "integer", "re" => trim('赢钱峰值,总充值的百分之N')],
            ["name" => "target_win_rate", "field" => "target_win_rate", "val" => null, "valType" => "integer", "re" => trim('赢钱生效概率%')],
            ["name" => "target_bottom_losescore", "field" => "target_bottom_losescore", "val" => null, "valType" => "integer", "re" => trim('输钱波谷,总充值的百分之N')],
            ["name" => "target_lose_rate", "field" => "target_lose_rate", "val" => null, "valType" => "integer", "re" => trim('输钱生效概率%')],
        ],
    ], 're' => trim('用户每次循环波峰波谷配置')],

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

    ["name" => "control", "field" => "control", "valType" => "array", "val" => [
        ["name" => "new_control", "field" => "new_control", "val" => '1', "valType" => "integer", "re" => trim("新旧控制：1新控制,非1旧控制")],
        ["name" => "new_user_control", "field" => "new_user_control", "val" => '3', "valType" => "integer", "re" => trim("新用户档次控制( 0正常 1=轻微杀 2=强杀 3=轻微放 4=强放)")],
        ["name" => "nomal_control", "field" => "nomal_control", "val" => '1', "valType" => "integer", "re" => trim("普通用户档次控制(0正常 1=轻微杀 2=强杀 3=轻微放 4=强放)")],
        ["name" => "new_user_day", "field" => "new_user_day", "val" => '3', "valType" => "integer", "re" => trim("新用户定义天数")],
    ], 're' => trim('控制设置')],

    ["name" => "special_channel", "field" => "special_channel", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "channel", "field" => "channel", "val" => null, "valType" => "integer", "re" => trim("渠道号")],
            ["name" => "control", "field" => "control", "val" => null, "valType" => "integer", "re" => trim("控制(0正常 1=轻微杀 2=强杀 3=轻微放 4=强放)")],
        ]
    ], 're' => trim('特殊渠道档次控制')],




];