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

    // ["name" => "stock_form", "field" => "stock_form", "valType" => "arrayMuti", "val" => [
    //     [
    //         ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
    //         ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
    //         ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
    //     ],
    // ], 're' => "Inventory Winning Table", 'layout' => 7, 'md' => 3],

    ["name" => "stock_form_type", "field" => "stock_form_type", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "lose", "field" => "lose", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "loss win", "field" => "loss win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "draw win", "field" => "draw win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "win", "field" => "win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "free_game", "field" => "free_game", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 8, 'md' => 3],

    ["name" => "stock_form_losswin", "field" => "stock_form_losswin", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 9, 'md' => 3],

    ["name" => "stock_form_win", "field" => "stock_form_win", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 10, 'md' => 3],

    ["name" => "stock_form_freegame", "field" => "stock_form_freegame", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 11, 'md' => 3],

    ["name" => "normal_form_type", "field" => "normal_form_type", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "lose", "field" => "lose", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "loss win", "field" => "loss win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "draw win", "field" => "draw win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "win", "field" => "win", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "free_game", "field" => "free_game", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 12, 'md' => 3],

    ["name" => "normal_form_losswin", "field" => "normal_form_losswin", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 13, 'md' => 3],

    ["name" => "normal_form_win", "field" => "normal_form_win", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 14, 'md' => 3],

    ["name" => "normal_form_freegame", "field" => "normal_form_freegame", "valType" => "arrayMuti", "val" => [
        [
            ["name" => "min", "field" => "min", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "max", "field" => "max", "val" => null, "valType" => "integer", "re" => ""],
            ["name" => "rate", "field" => "rate", "val" => null, "valType" => "integer", "re" => ""],
        ],
    ], 're' => "", 'layout' => 15, 'md' => 3],
];