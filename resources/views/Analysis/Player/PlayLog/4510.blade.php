<script>
    function poker_4510(data) {
        data['game_detail'] = data['game_detail'].replaceAll("'", '"')
        var poker = JSON.parse(data['game_detail'])
        var pokers = poker['cards'].split('|')
        // console.log('cards', poker['cards'])
        // console.log(pokers)
        var html = ''
        for (var i in pokers) {
            // %02X
            if (pokers[i]) {
                html += '<img width="20" src="/images/games/cards/0x' + toHex(pokers[i]) + '.png"/>'
            }
        }
        // console.log(html)
        return html
    }

    function getColumns_4510() {
        var res = getColumns_4201()
        // console.log(res[2])
        res[2]['formatter'] = function(b, c, a) {
            return poker_4510(c)
        }
        return res
    }
</script>