<script>
    function poker_4630(data) {
        data['poker_detail'] = data['poker_detail'].replaceAll("'", '"')
        var poker = JSON.parse(data['poker_detail'])
        // var pokers = poker['area_bet'].split(' ')
        // // console.log(pokers)
        var html = poker['crashr_rate']
        // for (var i in pokers) {
        //     // %02X
        //     if (pokers[i]) {
        //         html += '<img width="20" src="/images/games/cards/0x' + toHex(pokers[i]) + '.png"/>'
        //     }
        // }
        // console.log(html)
        return html
    }

    function getColumns_4630() {
        var res = getColumns_4201()
        // console.log(res[2])
        res[2]['title'] = "{{ __('ts.Rate') }}"
        res[2]['formatter'] = function(b, c, a) {
            return poker_4630(c)
        }
        return res
    }
</script>