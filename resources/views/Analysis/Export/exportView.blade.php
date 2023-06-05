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

                        <div class="card-header" style="display: none;">
                            <div class="btn-group v-search-bar" id="divSearch">
                                <input type="text" class="form-control" data-field="key" placeholder="{{ __('ts.Key') }}" />
                                <button type="button" class="btn btn-default" id="btnSearch">
                                    <i class="fas fa-search"></i>{{ __('ts.Search') }}
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="tabMain" style="word-wrap: break-work;word-break: break-all;"></table>

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
    var typeData = []
    function getColumns() {
        return [{
            field: "id",
            title: "{{ __('ts.ID') }}",
            align: "center",
            formatter: function(b, c, a) {
                return b
            },
            sortable: true,
        }, {
            field: "filename",
            title: "{{ __('ts.Filename') }}",
            align: "center"
        }, {
            field: "cost_time",
            title: "{{ __('ts.CostTime(ms)') }}",
            align: "center"
        }, {
            field: "is_success",
            title: "{{ __('ts.isSuccess') }}",
            align: "center",
            formatter: function(b, c, a) {
                return cform.getValue(typeData['success2Type'], b)
            },
            // width: "180",
            // widthUnit: "px",
        }, {
            field: "size",
            title: "{{ __('ts.Size') }}",
            align: "center",
            formatter: function(b, c, a) {
                return b
            },
            sortable: true,
        }, {
            field: "created",
            title: "{{ __('ts.Created') }}",
            align: "center",
        }, {
            field: "-",
            title: "{{ __('ts.Action') }}",
            align: "center",
            formatter: function(b, c, a) {
                if (c.is_success == 1) {
                    return "<a class=\"btn btn-xs btn-primary\" onclick='goToDownload(" + c.id + ")'>{{ __('ts.Download') }}</a>"
                }
            }
        }]
    }

    function goToDownload(id) {
        window.location = apiPath + 'export/download/' + id
    } 

    $(function() {

        common.getAjax(apiPath + "getbasedata?requireItems=success2Type", function(a) {
            typeData = a.result


            $("#btnSearch").initSearch(apiPath + "export", getColumns(), {
                sortName: "id",
                sortOrder: 'desc',
                showRefresh: true,
            })

            $("#btnSubmit").click()
        })

    })
</script>
@stop