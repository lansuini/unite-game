<?php
return [
    ["name" => "kill_rate", "field" => "kill_rate", "val" => null, "valType" => "integer", "re" => "kill count", 'layout' => 0, 'md' => 2],
    ["name" => "stock_defend", "field" => "stock_defend", "val" => null, "valType" => "integer", "re" => "Anti breakdown", 'layout' => 1, 'md' => 2],
    ["name" => "adjust_min", "field" => "adjust_min", "val" => null, "valType" => "integer", "re" => "Change table floating minimum", 'layout' => 2, 'md' => 2],
    ["name" => "adjust_max", "field" => "adjust_max", "val" => null, "valType" => "integer", "re" => "Change table floating maximum value", 'layout' => 3, 'md' => 2],
    ["name" => "max_bet", "field" => "max_bet", "val" => null, "valType" => "integer", "re" => "Maximum bet amount", 'layout' => 4, 'md' => 2],
    ["name" => "max_pay_double", "field" => "max_pay_double", "val" => null, "valType" => "integer", "re" => "Maximum Payout Multiple", 'layout' => 5, 'md' => 2],
    ["name" => "player_ctr", "field" => "player_ctr", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "bet", "field" => "bet", "val" => null, "valType" => "integer", "re" => "bet amount"],
            ["name" => "touch", "field" => "touch", "val" => null, "valType" => "integer", "re" => "trigger value"],
        ],
    ], 're' => "player_ctr", 'layout' => 6, 'md' => 3],

    ["name" => "stock_form", "field" => "stock_form", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => "Minimum multiple"],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => "Maximum multiple"],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => "Probability weight"],
        ],
    ], 're' => "Inventory Winning Table", 'layout' => 7, 'md' => 3],

    ["name" => "player_form", "field" => "player_form", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => "Minimum multiple"],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => "Maximum multiple"],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => "Probability weight"],
        ],
    ], 're' => "player winning table", 'layout' => 8, 'md' => 3],

    ["name" => "normal_form", "field" => "normal_form", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => "Minimum multiple"],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => "Maximum multiple"],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => "Probability weight"],
        ],
    ], 're' => "Ordinary winning table", 'layout' => 9, 'md' => 3],

];