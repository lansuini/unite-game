<?php

/**
 * 开运夺宝
 * 
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],

        ["name" => "tiyan", "field" => "tiyan", "val" => null, "valType" => "integer", "re" => trim('是否体验场')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => null, "valType" => "integer", "re" => trim('体验金(分)')],
        ["name" => "bet_bases", "field" => "bet_bases", "val" => null, "valNum" => 4, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "level", "field" => "level", "val" => null, "valType" => "integer", "re" => trim('房间级别(初中高)')],


        ["name" => "chargerate", "field" => "chargerate", "val" => null, "valType" => "integer", "re" => trim('税收比例')],
        ["name" => "lose_rate", "field" => "lose_rate", "val" => null, "valType" => "integer", "re" => trim('输钱返佣比')],
        ["name" => "max_offline_trustee", "field" => "max_offline_trustee", "val" => null, "valType" => "integer", "re" => trim('最大掉线托管次数')],

        ["name" => "bill_types", "field" => "bill_types", "val" => null, "valType" => "integer", "re" => trim('统计流水方式 0按输赢 1按押注 2按输赢不扣税')],

    ]],


];