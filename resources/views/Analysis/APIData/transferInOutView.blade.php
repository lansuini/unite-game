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
                            <div id="divSearch">
                                <div class="btn-group v-search-bar">
                                    <select class="form-control" data-field="client_id" id="client_id"></select>
                                    <select class="form-control" data-field="client_id_sub" id="client_id_sub"></select>
                                    <select class="form-control" data-field="game_id" id="game_id"></select>
                                    <select class="form-control" data-field="status" id="status"></select>
                                    <select class="form-control" data-field="bill_type" id="bill_type"></select>
                                </div>

                                <div class="btn-group v-search-bar">
                                    <input type="text" class="form-control" data-field="uid" placeholder="{{ __('ts.UID') }}" />
                                    <input type="text" class="form-control" data-field="player_name" placeholder="{{ __('ts.player_name') }}" />
                                    <input type="text" class="form-control" data-field="transaction_id" placeholder="{{ __('ts.transaction_id') }}" />
                                    <input type="text" class="form-control" data-field="parent_bet_id" placeholder="{{ __('ts.parent_bet_id') }}" />
                                    <!-- <input type="text" class="form-control" data-field="created" style="width:200px;" placeholder="{{ __('ts.created') }}" id="reservation" /> -->
                                    <div class=" date" id="reservationdate" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#reservationdate" data-field="created" placeholder="{{ __('ts.created') }}" />
                                        <!-- <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        </div> -->
                                    </div>
                                    <button type="button" class="btn btn-default" id="btnSearch">
                                        <i class="fas fa-search"></i>{{ __('ts.Search') }}
                                    </button>
                                </div>

                                <div id="toolbar" class="select">
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tabMain"></table>
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
    var typeData = [];

    function getColumns() {
        return [{
                field: "id",
                title: "{{ __('ts.ID') }}",
                align: "center",
                sortable: true,
                typeValue: 'C00000',
            },
            {
                field: "game_id",
                title: "{{ __('ts.Game') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['gameAliasType'], b)
                },
                sortable: false,
                typeValue: 'C00003',
            },
            {
                field: "client_id_sub",
                title: "{{ __('ts.Sub-Client') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['customerSubType'], b)
                },
                typeValue: 'C00005',
            },
            {
                field: "player_uid",
                title: "{{ __('ts.UID') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "player_name",
                title: "{{ __('ts.player_name') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "parent_bet_id",
                title: "{{ __('ts.parent_bet_id') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "bet_id",
                title: "{{ __('ts.bet_id') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "bet_amount",
                title: "{{ __('ts.bet_amount') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                typeValue: 'C00001',
            },
            {
                field: "transfer_amount",
                title: "{{ __('ts.transfer_amount') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                typeValue: 'C00001',
            },
            {
                field: "transaction_id",
                title: "{{ __('ts.transaction_id') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "bill_type",
                title: "{{ __('ts.bill_type') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['billType'], b)
                },
                typeValue: 'C00000',
            },
            {
                field: "is_end",
                title: "{{ __('ts.is_end') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "status",
                title: "{{ __('ts.status') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['transferInOutStatusType'], b)
                },
                typeValue: 'C00000',
            },
            {
                field: "balanceBefore",
                title: "{{ __('ts.balanceBefore') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                visible: false,
                typeValue: 'C00001',
            },
            {
                field: "balanceAfter",
                title: "{{ __('ts.balanceAfter') }}",
                align: "center",
                sortable: false,
                formatter: function(b, c, a) {
                    return common.ya(b)
                },
                visible: false,
                typeValue: 'C00001',
            },
            {
                field: "create_time",
                title: "{{ __('ts.create_time') }}",
                align: "center",
                sortable: false,
                typeValue: 'C00000',
            },
        ]
    }

    $(function() {

        // $('#reservation').daterangepicker({
        //     "startDate": moment().subtract(3, 'days'),
        //     "endDate": moment()
        // })


        $('#reservationdate').datetimepicker({
            format: 'L',
            defaultDate: moment()
        })

        $("#client_id").change(function() {
            var customerId = $(this).val()
            common.getAjax(apiPath + "getbasedata?requireItems=customerSubType&customer_id=" + customerId, function(a) {
                $("#client_id_sub").html('')
                $("#client_id_sub").initSelect(a.result.customerSubType, "key", "value", "{{ __('ts.sub-client') }}")
            })
        })

        var requireItems = 'billType,gameAliasType,customerAPIType1,customerSubType,transferInOutStatusType'
        var requireItems2 = 'billType,gameAliasType,customerAPIType1,customerSubType2,transferInOutStatusType'
        common.getAjax(apiPath + "getbasedata?requireItems=" + requireItems, function(a) {
            typeData = a.result
            $("#game_id").initSelect(a.result.gameAliasType, "key", "value", "{{ __('ts.Games') }}")
            $("#client_id").initSelect(a.result.customerAPIType1, "key", "value", "{{ __('ts.client') }}")
            $("#status").initSelect(a.result.transferInOutStatusType, "key", "value", "{{ __('ts.status') }}")
            $("#bill_type").initSelect(a.result.billType, "key", "value", "{{ __('ts.bill_type') }}")
            $("#client_id_sub").initSelect(a.result.customerSubType, "key", "value", "{{ __('ts.sub-client') }}")
            $("#client_id_sub").select2()
            $("#client_id").select2()
            $("#game_id").select2()
            $("#btnSearch").initSearch(apiPath + "apidata/transferinout", getColumns(), {
                sortName: "id",
                sortOrder: 'desc',
                showRefresh: true,
                showColumns: true,
                // showCustomExport: true,
                // showCustomExportKey: "sub_data_report_export",
                // showCustomExportFilename: "{{ __($pageTitle[1] ?? '')}}",
                // showCustomExportRequireItems: requireItems2,
                // toolbar: '#toolbar',
                // toolbarAlign: 'right',
            })
            $("#btnSubmit").click()
        })
    })
</script>
@stop