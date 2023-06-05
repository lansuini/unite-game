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
                                <input type="text" class="form-control" data-field="uid" placeholder="{{ __('ts.UID') }}" />
                                <input type="text" class="form-control" data-field="pid" placeholder="{{ __('ts.NO.') }}" />
                                <input type="text" class="form-control" style="width:190px;" data-field="created" placeholder="{{ __('ts.created') }}" id="reservation" />
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

    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Game: <span id="spid3"></span> No: <span id="spid2"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="btn-group v-search-bar" id="divSearch2" style="display: none;">
                        <input type="text" class="form-control" data-field="pid" placeholder="{{ __('ts.No.') }}" id="spid" />
                        <input type="text" class="form-control" data-field="uid" placeholder="{{ __('ts.UID') }}" id="suid" />
                        <button type="button" class="btn btn-default" id="btnSearch2">
                            <i class="fas fa-search"></i>{{ __('ts.Search') }}
                        </button>
                    </div>
                    <table id="tabMain2"></table>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-default" value="{{ __('ts.Close') }}" data-dismiss="modal" />
                </div>
            </div>
        </div>
    </div>
</div>
@append

@section('content')
<!-- edit form -->

@stop

@section('script')
<script>
    var cuid = 0

    function toHex(value) {
        let hex = parseInt(value).toString(16)
        if ((hex.length % 2) > 0) {
            hex = "0" + hex
        }
        return hex.toUpperCase()
    }

    function showDetail(pid, uid, gameId) {
        cuid = uid
        $('#detailModal').modal()
        $('#spid').val(pid)
        $('#spid2').html(pid)
        $('#suid').val(uid)
        $('#spid3').html(gameId)
        // console.log('gameId', gameId)
        $("#btnSearch2").initSearch(apiPath + "player/playlogdetail", eval('getColumns_' + gameId + '()'), {
            sortName: "id",
            sortOrder: 'desc',
            // showColumns: true,
            tabId: "tabMain2",
            searchContainerId: "divSearch2",
        })
        $("#btnSearch2").click()
    }

    function getColumns() {
        return [{
                field: "pid",
                title: "{{ __('ts.NO.') }}",
                align: "center",
                formatter: function(b, c, a) {
                    if (typeof window['getColumns_' + c['game_id']] != 'undefined') {
                        return '<a href="#" onclick="showDetail(' + c['pid'] + ',' + c['uid'] + ',' + c['game_id'] + ')">' + b + '</a>'
                    } else {
                        return b
                    }
                },
                sortable: true,
            }, {
                field: "uid",
                title: "{{ __('ts.UID') }}",
                align: "center"
            }, {
                field: "reboot",
                title: "{{ __('ts.Reboot') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return c['user_type'] == 100 ? 'Y' : 'N'
                },
            },
            //  {
            //     field: "types",
            //     title: "{{ __('ts.PlayerType') }}",
            //     align: "center"
            // },
            {
                field: "game_detail",
                title: "{{ __('ts.GameDetail') }}",
                align: "center",
                visible: false
            }, {
                field: "name",
                title: "{{ __('ts.GameName') }}",
                align: "center",
                // formatter: function(b, c, a) {
                //     return cform.getValue(typeData['gameAliasType'], b)
                // }
            },
            // {
            //     field: "category_name",
            //     title: "{{ __('ts.Category') }}",
            //     align: "center"
            // }, 
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
            // {
            //     field: "bill",
            //     title: "{{ __('ts.Bill') }}",
            //     align: "center"
            // }, {
            //     field: "bill2",
            //     title: "{{ __('ts.WinLose(NoTax)') }}",
            //     align: "center"
            // }, {
            //     field: "bill3",
            //     title: "{{ __('ts.WinLose(Tax)') }}",
            //     align: "center"
            // }, {
            //     field: "bill4",
            //     title: "{{ __('ts.WaterLogType') }}",
            //     align: "center"
            // }, 
            // {
            //     field: "times",
            //     title: "{{ __('ts.Times') }}",
            //     align: "center"
            // }, 
            {
                field: "post_time",
                title: "{{ __('ts.Created') }}",
                align: "center",
                formatter: function(b, c, a) {
                    // return moment.unix(b).format("MM/DD/YYYY HH:mm:ss")
                    // return moment(b, 'YYYY-MM-DD HH:mm:ss').format("MM/DD/YYYY HH:mm:ss")
                    return b
                }
            }
        ]
    }

    $(function() {

        $('#reservation').daterangepicker({
            "startDate": moment().subtract(1, 'days'),
            "endDate": moment()
        })

        common.getAjax(apiPath + "getbasedata?requireItems=gameAliasType,resultType", function(a) {
            typeData = a.result
            $("#game_id").initSelect(a.result.gameAliasType, "key", "value", "Games", 4201)
            $("#btnSearch").initSearch(apiPath + "player/playlog", getColumns(), {
                sortName: "id",
                sortOrder: 'desc',
                showColumns: true,
                toolbar: '#toolbar',
                // showExport: true,
                // exportTypes: ['csv'],
                // exportDataType: "all"

            })
            $("#btnSubmit").click()
        })

        common.initSection(true)

    })
</script>
@include('/Analysis/Player/PlayLog/4201')
@include('/Analysis/Player/PlayLog/4510')
@include('/Analysis/Player/PlayLog/4620')
@include('/Analysis/Player/PlayLog/4630')
@stop