@extends('/GM/Layout')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
@include('/GM/navigator')

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">

                    <div classs="card">

                        <div class="card-header">
                            <div class="btn-group v-search-bar" id="divSearch">
                                <select class="form-control" data-field="game_id" id="game_id"></select>
                                <input type="text" class="form-control" data-field="created" style="width:200px;" placeholder="{{ __('ts.created') }}" id="reservation" />
                                <button type="button" class="btn btn-default" id="btnSearch">
                                    <i class="fas fa-search"></i>{{ __('ts.Search') }}
                                </button>
                            </div>

                            <div id="toolbar" class="select">
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tabMain"></table>
                            <div class="font-weight-light">{{ __('ts.TIPs: The latest data is updated hourly') }}</div>
                        </div>

                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

</div>
@append

@section('content')
<!-- edit form -->

@stop

@section('script')
<script>
    function getColumns() {
        return [{
                field: "game_id",
                title: "{{ __('ts.Game') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['gameAliasType'], b)
                },
                sortable: true,
                typeValue: 'C00003',
            },
            {
                field: "bet_amount",
                title: "{{ __('ts.BetAmount') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                typeValue: 'C00001',
            },
            {
                field: "bet_count",
                title: "{{ __('ts.BetCount') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "transfer_amount",
                title: "{{ __('ts.TransferAmount') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                typeValue: 'C00001',
            },
            {
                field: "login_user_cnt",
                title: "{{ __('ts.LoginUser') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "valid_user_cnt",
                title: "{{ __('ts.VaildUser') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "tax",
                title: "{{ __('ts.Tax') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                typeValue: 'C00001',
            },
            {
                field: "RTP",
                title: "{{ __('ts.RTP') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return common.ya(b) + '%'
                },
                typeValue: 'C00002',
            },
            {
                field: "RTPET",
                title: "{{ __('ts.RTP(Excluding Tax)') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return common.ya(b) + '%'
                },
                typeValue: 'C00002',
            },
            {
                field: "count_date",
                title: "{{ __('ts.CountDate') }}",
                align: "center",
                sortable: true,
            },
        ]
    }

    $(function() {

        $('#reservation').daterangepicker({
            "startDate": moment().subtract(30, 'days'),
            "endDate": moment()
        })
        var requireItems = 'gameAliasType'
        common.getAjax(apiPath + "getbasedata?requireItems=gameAliasType", function(a) {
            typeData = a.result
            $("#game_id").initSelect(a.result.gameAliasType, "key", "value", "{{ __('ts.Games') }}")
            $("#btnSearch").initSearch(apiPath + "report/datareport", getColumns(), {
                sortName: "count_date",
                sortOrder: 'desc',
                showRefresh: true,
                showColumns: true,
                showCustomExport: true,
                showCustomExportKey: "data_report_export",
                showCustomExportFilename: "{{ __($pageTitle[1] ?? '')}}",
                showCustomExportRequireItems: requireItems,
                toolbar: '#toolbar',
                toolbarAlign: 'right',

            })
            $("#btnSubmit").click()
        })

        common.initSection(true)

    })
</script>
@stop