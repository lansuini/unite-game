<script>
    function poker_4690(data) {
        data['poker_detail'] = data['poker_detail'].replaceAll("'", '"')
        var poker = JSON.parse(data['poker_detail'])
        var html = poker['crashr_rate']
        return html
    }

    function getColumns_4690() {
        var res = getColumns_4201()
        // console.log(res[2])
        res[2]['title'] = "{{ __('ts.Rate') }}"
        res[2]['formatter'] = function(b, c, a) {
            return poker_4690(c)
        }
        return res
    }
</script>