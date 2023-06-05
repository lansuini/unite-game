<?php

return [
    ["name" => "rule", "field" => "rule", "valType" => "array", "val" => [
        ["name" => "NewJettonSwitch", "field" => "NewJettonSwitch", "val" => null, "valType" => "integer", "re" => trim("个人筹码位置切换开关,1筹码设置json生效,0客户端设置筹码生效")],
        ["name" => "NormalBetArea", "field" => "NormalBetArea", "val" => "0", "valType" => "select", "re" => trim('普通下注区域(分)'), "options" => [
            100 => 100,
            500 => 500,
            1000 => 1000,
            2000 => 2000,
            5000 => 5000,
            10000 => 10000,
            50000 => 50000,
            100000 => 100000,
            500000 => 500000,
            1000000 => 1000000,
        ]],
        ["name" => "jetton", "field" => "jetton", "val" => null, "valNum" => 5, 'require' => 5, "options" => [
            100 => 100,
            500 => 500,
            1000 => 1000,
            2000 => 2000,
            5000 => 5000,
            10000 => 10000,
            50000 => 50000,
            100000 => 100000,
            500000 => 500000,
            1000000 => 1000000,
        ], "valType" => "arrayVal", "valType2" => "select", "valSubType" => "integer", "re" => trim('用户个人筹码设置(分)')],
    ]],
];
