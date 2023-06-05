<script>
    function getColumns_4620() {
        return [{
                field: "uid",
                title: "{{ __('ts.UID') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return b == cuid ? '<span class="text-danger">' + b + '</span>' : b
                },
            }, {
                field: "reboot",
                title: "{{ __('ts.RT') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return c['user_type'] == 100 ? 'Y' : 'N'
                },
            },
            {
                field: "cards",
                title: "{{ __('ts.Cards') }}",
                align: "center",
                formatter: function(b, c, a) {
                    c['game_detail'] = c['game_detail'].replaceAll("'", '"')
                    var poker = JSON.parse(c['game_detail'])
                    var pokers = poker['cards'].split('|')
                    // var txts = [
                    //     '<span style="color:yellow;background:grey">黄</span>',
                    //     '<span style="color:white;background:grey">白</span>',
                    //     '<span style="color:pink;background:grey">粉</span>',
                    //     '<span style="color:blue;background:grey">蓝</span>',
                    //     '<span style="color:red;background:grey">红</span>',
                    //     '<span style="color:green;background:grey">绿</span>'
                    // ]
                    var txts = [
                        "{{ __('ts.Yellow') }}",
                        "{{ __('ts.White') }}",
                        "{{ __('ts.Pink') }}",
                        "{{ __('ts.Blue') }}",
                        "{{ __('ts.Red') }}",
                        "{{ __('ts.Green') }}"
                    ]
                    // console.log('cards', poker['cards'])
                    // console.log(pokers)
                    var html = []
                    for (var i in pokers) {
                        html.push(txts[parseInt(pokers[i]) - 1])
                    }
                    // console.log(html)
                    return html.join("&nbsp;")
                },
            },
            {
                field: "game_result",
                title: "{{ __('ts.GameResult') }}",
                align: "center",
                formatter: function(b, c, a) {
                    c['game_detail'] = c['game_detail'].replaceAll("'", '"')
                    var poker = JSON.parse(c['game_detail'])
                    var pokers = poker['game_result'].split('|')
                    var txts = [
                        "{{ __('ts.Lose') }}",
                        "{{ __('ts.Win') }}",
                    ]
                    var html = []
                    for (var i in pokers) {
                        if (pokers[i]) {
                            html.push(txts[parseInt(pokers[i])])
                        }
                    }
                    return html.join("&nbsp;")
                },
            },
            {
                field: "area_bet",
                title: "{{ __('ts.AreaBet') }}",
                align: "center",
                formatter: function(b, c, a) {
                    c['poker_detail'] = c['poker_detail'].replaceAll("'", '"')
                    var poker = JSON.parse(c['poker_detail'])
                    var pokers = poker['area_bet'].split('|')
                    var html = []
                    for (var i in pokers) {
                        if (pokers[i]) {
                            html.push(pokers[i])
                        }
                    }
                    return html.join("&nbsp;")
                },
            },
            {
                field: "area_win",
                title: "{{ __('ts.AreaWin') }}",
                align: "center",
                formatter: function(b, c, a) {
                    c['poker_detail'] = c['poker_detail'].replaceAll("'", '"')
                    var poker = JSON.parse(c['poker_detail'])
                    var pokers = poker['area_win'].split('|')
                    var html = []
                    for (var i in pokers) {
                        if (pokers[i]) {
                            html.push(pokers[i])
                        }
                    }
                    return html.join("&nbsp;")
                },
            },
            {
                field: "jack_pot",
                title: "{{ __('ts.JackPot') }}",
                align: "center",
                formatter: function(b, c, a) {
                    c['poker_detail'] = c['poker_detail'].replaceAll("'", '"')
                    var poker = JSON.parse(c['poker_detail'])
                    return poker['jack_pot']
                },
            },
            {
                field: "result",
                title: "{{ __('ts.Result') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['resultType'], b)
                },
            }, {
                field: "before",
                title: "{{ __('ts.Before') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return common.ya(c['score2'] - c['score1'])
                }
            }, {
                field: "score2",
                title: "{{ __('ts.After') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return common.ya(b)
                }
            }, {
                field: "score1",
                title: "{{ __('ts.WinLose') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return common.ya(b)
                }
            },
        ]
    }
</script>