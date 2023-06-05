<?php

/**
 * 	全民捕鱼配置
 */
return [

    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [

        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "open_tiyan", "field" => "open_tiyan", "val" => "0", "valType" => "integer", "re" => trim('是否体验场')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => "0", "valType" => "integer", "re" => trim('体验金(分)')],
        ["name" => "tiyan_rate", "field" => "tiyan_rate", "val" => "0", "valType" => "integer", "re" => trim('体验场概率加成')],

        ["name" => "tax", "field" => "tax", "val" => null, "valType" => "integer", "re" => trim('抽水')],
        ["name" => "boss_name", "field" => "boss_name", "val" => null, "valType" => "string", "re" => trim('BOSS名称')],
        ["name" => "boss_icon", "field" => "boss_icon", "val" => null, "valType" => "int", "re" => trim('BOSS图标序号')],
        ["name" => "room_weight", "field" => "room_weight", "val" => null, "valType" => "int", "re" => trim('房间系数')],
        ["name" => "cannon_ratio", "field" => "cannon_ratio", "val" => null, "valType" => "string", "re" => trim('子弹系数')],

        ["name" => "dynamic_interval", "field" => "dynamic_interval", "val" => null, "valType" => "integer", "re" => trim('动态权重区间')],
        ["name" => "dynamic_weight", "field" => "dynamic_weight", "val" => null, "valType" => "string", "re" => trim('动态权重')],

        ["name" => "room_preinstall", "field" => "room_preinstall", "val" => null, "valType" => "integer", "re" => trim('房间配置项')],
        ["name" => "room_adjust", "field" => "room_adjust", "val" => null, "valType" => "integer", "re" => trim('微调配置项')],
        ["name" => "new_control", "field" => "new_control", "val" => '1', "valType" => "integer", "re" => trim("新旧控制：1新控制,非1旧控制")],

    ]],

    ["name" => "fish_ratio", "field" => "fish_ratio", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "id", "field" => "id", "val" => null, "valType" => "string", "re" => trim('鱼ID')],
            ["name" => "weight", "field" => "weight", "val" => null, "valType" => "string", "re" => trim('修正权重值')],
            ["name" => "prov_min", "field" => "prov_min", "val" => null, "valType" => "integer", "re" => trim('最小概率%')],
            ["name" => "prov_max", "field" => "prov_max", "val" => null, "valType" => "integer", "re" => trim('最大概率%')],
            ["name" => "shield", "field" => "shield", "val" => null, "valType" => "integer", "re" => trim('初始护盾')],
        ],
    ], 're' => trim('fish_ratio配置')],






];