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
                            <select class="form-control" data-field="client_id" id="client_id"></select>
                                <select class="form-control" data-field="client_id_sub" id="client_id_sub"></select>
                                <select class="form-control" data-field="game_id" id="game_id"></select>
                                <input type="text" class="form-control" data-field="uid" placeholder="{{ __('ts.UID') }}" />
                                <input type="text" class="form-control" data-field="player_name" placeholder="{{ __('ts.player_name') }}" />
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
                field: "client_id_sub",
                title: "{{ __('ts.Sub-Client') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['customerSubType'], b)
                },
                typeValue: 'C00005',
            },
            {
                field: "uid",
                title: "{{ __('ts.UID') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "player_name",
                title: "{{ __('ts.player_name') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "bet_id",
                title: "{{ __('ts.bet_id') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            {
                field: "detail",
                title: "{{ __('ts.detail') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },
            
            {
                field: "parent_bet_id",
                title: "{{ __('ts.parent_bet_id') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return b
                },
                typeValue: 'C00000',
            },

            {
                field: "create_time",
                title: "{{ __('ts.create_time') }}",
                align: "center",
                sortable: true,
                typeValue: 'C00000',
            },
        ]
    }

    $(function() {

        $('#reservationdate').datetimepicker({
            format: 'L',
            defaultDate: moment()
        })

        // $('#reservation').daterangepicker({
        //     "startDate": moment().subtract(3, 'days'),
        //     "endDate": moment()
        // })

        $("#client_id").change(function() {
            var customerId = $(this).val()
            common.getAjax(apiPath + "getbasedata?requireItems=customerSubType&customer_id=" + customerId, function(a) {
                $("#client_id_sub").html('')
                $("#client_id_sub").initSelect(a.result.customerSubType, "key", "value", "{{ __('ts.sub-client') }}")
            })
        })

        var requireItems = 'gameAliasType,customerType,customerSubType'
        var requireItems2 = 'gameAliasType,customerType,customerSubType2'
        common.getAjax(apiPath + "getbasedata?requireItems=" + requireItems, function(a) {
            typeData = a.result
            $("#game_id").initSelect(a.result.gameAliasType, "key", "value", "{{ __('ts.Games') }}")
            $("#client_id").initSelect(a.result.customerType, "key", "value", "{{ __('ts.client') }}")
            $("#client_id_sub").initSelect(a.result.customerSubType, "key", "value", "{{ __('ts.sub-client') }}")
            $("#client_id_sub").select2()
            $("#client_id").select2()
            $("#game_id").select2()
            $("#btnSearch").initSearch(apiPath + "apidata/gamedetails", getColumns(), {
                sortName: "id",
                sortOrder: 'desc',
                showRefresh: true,
                showColumns: true,
                showCustomExport: true,
                showCustomExportKey: "sub_data_report_export",
                showCustomExportFilename: "{{ __($pageTitle[1] ?? '')}}",
                showCustomExportRequireItems: requireItems2,
                toolbar: '#toolbar',
                toolbarAlign: 'right',
            })
            $("#btnSubmit").click()
        })
    })
</script>
@stop