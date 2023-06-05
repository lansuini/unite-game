<script>
    function poker_4201(data) {
        data['poker_detail'] = data['poker_detail'].replaceAll("'", '"')
        var poker = JSON.parse(data['poker_detail'])
        var pokers = poker['hand_card'].split(' ')
        // console.log(pokers)
        var html = ''
        for (var i in pokers) {
            // %02X
            if (pokers[i]) {
                var s = pokers[i].length == 1 ? "0" + pokers[i] : pokers[i]
                html += '<img width="20" src="/images/games/cards/0x' + s + '.png"/>'
                // html += '<img width="20" src="/images/games/cards/0x' + toHex(pokers[i]) + '.png"/>'
            }
        }
        // console.log(html)
        return html
    }

    function getColumns_4201() {
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
                field: "poker_detail",
                title: "{{ __('ts.Poker') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return poker_4201(c)
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