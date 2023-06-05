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
                                <input type="text" class="form-control" data-field="symbol" placeholder="{{ __('ts.symbol') }}" />
                                <select class="form-control" data-field="is_lock" id="selLockType"></select>
                                <button type="button" class="btn btn-default" id="btnSearch">
                                    <i class="fas fa-search"></i>{{ __('ts.Search') }}
                                </button>

                                <button type="button" class="btn btn-primary" id="btnCreate">
                                    <i class="fas fa-plus"></i>{{ __('ts.Create') }}
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

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('ts.Edit') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <form class="">
                        <div class="form-group sr-only">
                            <label class="col-form-label">{{ __('ts.id') }}</label>
                            <input type="text" class="form-control" data-field="id" />
                        </div>

                        <div class="form-group">
                            <label class="" for="txtRole">{{ __('ts.Symbol') }}</label>
                            <input type="text" class="form-control" data-field="symbol" readonly />
                        </div>

                        <div class="form-group">
                            <label class="" for="txtRole">{{ __('ts.Remark') }}</label>
                            <textarea class="form-control" data-field="remark"></textarea>
                        </div>

                        <div class="form-group form-edit">
                            <label class="col-form-label" for="selStatus">{{ __('ts.Lock') }}</label>
                            <select class="form-control is_lock" data-field="is_lock"></select>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-default" value="{{ __('ts.Close') }}" data-dismiss="modal" />
                    <input type="button" class="btn btn-primary" value="{{ __('ts.Submit') }}" id="updateBtnSubmit" />
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('ts.Create') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <form class="">
                        <div class="form-group">
                            <label class="" for="txtRole">{{ __('ts.Symbol') }}<a class="btn btn-xs btn-dark autors">{{ __('ts.AutoFixed') }}</a></label>
                            <input type="text" class="form-control" data-field="symbol" />
                        </div>

                        <div class="form-group">
                            <label class="" for="txtRole">{{ __('ts.Remark') }}</label>
                            <textarea class="form-control" data-field="remark"></textarea>
                        </div>

                        <div class="form-group form-edit">
                            <label class="col-form-label" for="selStatus">{{ __('ts.Lock') }}</label>
                            <select class="form-control is_lock" data-field="is_lock"></select>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-default" value="{{ __('ts.Close') }}" data-dismiss="modal" />
                    <input type="button" class="btn btn-primary" value="{{ __('ts.Submit') }}" id="createBtnSubmit" />
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
    var typeData = []

    function randomString(e) {
        e = e || 6;
        var t = "ABCDEFGHJKMNPQRSTWXYZ",
            a = t.length,
            n = "";
        for (i = 0; i < e; i++) n += t.charAt(Math.floor(Math.random() * a));
        return n
    }

    $('.autors').click(function() {
        $(this).parent().parent().find('input').val(randomString(6))
    })

    function showEditModal(data) {
        cform.get('editModal', apiPath + 'settings/subclient/' + data['id'], function(res) {
            var gameOCVal = res.data.game_oc
            if (gameOCVal != null) {
                gameOCVal = gameOCVal.split(',')
                $('.game_oc_edit').find('input:checkbox').each(function() {
                    var val = $(this).val()
                    if (gameOCVal.indexOf(val) != -1) {
                        $(this).prop('checked', true)
                    } else {
                        $(this).prop('checked', false)
                    }
                })
            }

        })
    }

    function showCreateModal() {
        $('#createModal').modal()
    }

    function delAccount(id) {
        myConfirm.show({
            title: "{{ __('ts.confirm deletion ?') }}",
            sure_callback: function() {
                cform.del(apiPath + "settings/subclient/" + id, function(d) {
                    location.href = location.href
                })
            }
        })
    }

    function getColumns() {
        return [{
            field: "symbol",
            title: "{{ __('ts.Symbol') }}",
            align: "center"
        }, {
            field: "remark",
            title: "{{ __('ts.Remark') }}",
            align: "center"
        }, {
            field: "is_lock",
            title: "{{ __('ts.Locked') }}",
            align: "center",
            formatter: function(b, c, a) {
                return cform.getValue(typeData['lockType'], b)
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
                return "<a class=\"btn btn-xs btn-primary\" onclick='showEditModal(" + JSON.stringify(c) + ")'>{{ __('ts.Edit') }}</a>" +
                    "<a class=\"btn btn-xs btn-danger\" onclick='delAccount(\"" + c.id + "\")'>{{ __('ts.Del') }}</a>"
            }
        }]
    }

    $(function() {

        common.getAjax(apiPath + "getbasedata?requireItems=lockType", function(a) {
            typeData = a.result
            $("#selLockType").initSelect(a.result.lockType, "key", "value", "{{ __('ts.lock status') }}")
            $(".is_lock").initSelect(a.result.lockType, "key", "value", "{{ __('ts.lock status') }}")
            $("#btnSearch").initSearch(apiPath + "settings/subclient", getColumns(), {
                sortName: "id",
                sortOrder: 'desc'
            })
            $("#btnSubmit").click()
        })


        $('#updateBtnSubmit').click(function() {
            var id = $('#editModal form').find("[data-field='id']").val()
            cform.patch('editModal', apiPath + 'settings/subclient/' + id, function(d) {
                myAlert.success(d.result)
                $('#editModal').modal('hide');
                $('#btnSearch').click()
            })
        })

        $('#createBtnSubmit').click(function() {
            cform.post('createModal', apiPath + 'settings/subclient')
        })

        $('#btnCreate').click(function() {
            showCreateModal()
        })
    })
</script>
@stop