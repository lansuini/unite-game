<?php
 
/**
 * 水浒传
 */
return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "tiyan", "field" => "tiyan", "val" => "0", "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => "100000", "valType" => "integer", "re" => trim('体验金(分)')],

        ["name" => "bet_bases", "field" => "bet_bases", "val" => null, "valNum" => 4, "valType" => "arrayVal", "valSubType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "level", "field" => "level", "val" => null, "valType" => "integer", "re" => trim('房间级别')],
        ["name" => "tax", "field" => "tax", "val" => null, "valType" => "integer", "re" => trim('税收')],
        ["name" => "lose_rate", "field" => "lose_rate", "val" => null, "valType" => "integer", "re" => trim('输钱返佣比')],
        ["name" => "max_offline_trustee", "field" => "max_offline_trustee", "val" => null, "valType" => "integer", "re" => trim('最大掉线托管次数')],

        ["name" => "bill_types", "field" => "bill_types", "val" => null, "valType" => "integer", "re" => trim('统计流水方式 0按输赢 1按押注 2按输赢不扣税')],

    ]],
];