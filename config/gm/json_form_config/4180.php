<?php

/**
 * 二人麻将表单配置
 */
return [

    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [

        ["name" => "base_score", "field" => "base_score", "val" => null, "valType" => "integer", "re" => trim('游戏底分(分)')],
        ["name" => "tiyan", "field" => "tiyan", "val" => "0", "valType" => "integer", "re" => trim('体验场,1体验,0非体验')],
        ["name" => "tiyan_gold", "field" => "tiyan_gold", "val" => "100000", "valType" => "integer", "re" => trim('体验金(分)')],

        ["name" => "playercount", "field" => "playercount", "val" => null, "valType" => "integer", "re" => trim('对局玩家数量')],

    ]],
];