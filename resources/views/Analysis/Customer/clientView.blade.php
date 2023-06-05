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
                                <input type="text" class="form-control" data-field="company_name" placeholder="{{ __('ts.Company') }}" />
                                <input type="text" class="form-control" data-field="operator_token" placeholder="{{ __('ts.OperatorToken') }}" />
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('ts.Edit') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <form class="row g-3">
                        <div class="col-md-6">
                            <div class="row">

                                <div class="col-md-12 sr-only">
                                    <label class="col-form-label">{{ __('ts.id') }}</label>
                                    <input type="text" class="form-control" data-field="id" />
                                </div>

                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.Company') }}</label>
                                    <input type="text" class="form-control" data-field="company_name" />
                                </div>

                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.OperatorToken') }}</label>
                                    <input type="text" class="form-control" data-field="operator_token" readonly />
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">SecretKey<a class="btn btn-xs btn-dark autors">{{ __('ts.AutoFixed') }}</a></label>
                                    <input type="text" class="form-control" data-field="secret_key" />
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.API IP Limit') }}</label>
                                    <textarea class="form-control" data-field="api_ip_white" style="min-height: 300px;"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.MerchantAddress') }}</label>
                                    <input type="text" class="form-control" data-field="merchant_addr" />
                                </div>

                                <div class="col-md-12 form-edit">
                                    <label class="col-form-label" for="selStatus">{{ __('ts.Lock') }}</label>
                                    <select class="form-control is_lock" data-field="is_lock"></select>
                                </div>

                                <div class="col-md-12 form-edit">
                                    <label class="col-form-label">{{ __('ts.API Mode') }}</label>
                                    <select class="form-control api_mode" data-field="api_mode"></select>
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.GameDomain') }}</label>
                                    <input type="text" class="form-control" data-field="game_domain" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.Game Switch Control') }}</label>
                                    <div class="game_oc_checkbox game_oc_edit"></div>
                                    <input type="hidden" class="form-control game_oc_edit_value" data-field="game_oc" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.Open Server Maintenance') }}</label>
                                    <div class="game_oc_checkbox game_mc_edit"></div>
                                    <input type="hidden" class="form-control game_mc_edit_value" data-field="game_mc" />
                                </div>
                            </div>
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
                    <form class="row g-3">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.Company') }}</label>
                                    <input type="text" class="form-control" data-field="company_name" />
                                </div>

                                <div class="col-md-12">
                                    <label class="col-form-label">OperatorToken<a class="btn btn-xs btn-dark autors">{{ __('ts.AutoFixed') }}</a></label>
                                    <input type="text" class="form-control" data-field="operator_token" />
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">SecretKey<a class="btn btn-xs btn-dark autors">{{ __('ts.AutoFixed') }}</a></label>
                                    <input type="text" class="form-control" data-field="secret_key" />
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.API IP Limit') }}</label>
                                    <textarea class="form-control" data-field="api_ip_white"></textarea>
                                </div>

                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.MerchantAddress') }}</label>
                                    <input type="text" class="form-control" data-field="merchant_addr" />
                                </div>

                                <div class="col-md-12 form-edit">
                                    <label class="col-form-label" for="selStatus">{{ __('ts.Lock') }}</label>
                                    <select class="form-control is_lock" data-field="is_lock"></select>
                                </div>

                                <div class="col-md-12 form-edit">
                                    <label class="col-form-label">{{ __('ts.API Mode') }}</label>
                                    <select class="form-control api_mode" data-field="api_mode"></select>
                                </div>


                                <div class="col-md-12">
                                    <label class="" for="txtRole">{{ __('ts.GameDomain') }}</label>
                                    <input type="text" class="form-control" data-field="game_domain" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="col-form-label">{{ __('ts.Game Switch Control') }}</label>
                                    <div class="game_oc_checkbox game_oc_create"></div>
                                    <input type="hidden" class="form-control game_oc_create_value" data-field="game_oc" />
                                </div>

                            </div>
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

    <div class="modal fade" id="JSONModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('ts.Settings Edit') }} <span id="keyVal"></span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                    <form class="">
                        <div class="form-group sr-only">
                            <label class="col-form-label">{{ __('ts.id') }}</label>
                            <input type="text" class="form-control" data-field="id" />
                        </div>
                        <div id="JSONItems" class="row g-3"></div>
                    </form>
                </div>

                <div class="modal-footer">
                    <input type="button" class="btn btn-default" value="{{ __('ts.Close') }}" data-dismiss="modal" />
                    <input type="button" class="btn btn-info" value="{{ __('ts.AutoFixed') }}" id="DefaultJSONBtnSubmit" />
                    <input type="button" class="btn btn-primary" value="{{ __('ts.Submit') }}" id="JSONBtnSubmit" />
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
        e = e || 32;
        var t = "ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678",
            a = t.length,
            n = "";
        for (i = 0; i < e; i++) n += t.charAt(Math.floor(Math.random() * a));
        return n
    }

    $('.autors').click(function() {
        $(this).parent().parent().find('input').val(randomString(32))
    })

    function showEditModal(data) {
        cform.get('editModal', apiPath + 'customer/client/' + data['id'], function(res) {
            var gameOCVal = res.data.game_oc
            var gameMCVal = res.data.game_mc
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

                $('.game_mc_edit').find('input:checkbox').each(function() {
                    var val = $(this).val()
                    if (gameMCVal.indexOf(val) != -1) {
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
                cform.del(apiPath + "customer/client/" + id, function(d) {
                    location.href = location.href
                })
            }
        })
    }

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
            field: "company_name",
            title: "{{ __('ts.Company') }}",
            align: "center"
        }, {
            field: "operator_token",
            title: "{{ __('ts.OperatorToken') }}",
            align: "center"
        }, {
            field: "api_ip_white",
            title: "{{ __('ts.API_IP_Limit') }}",
            align: "center",
            width: "180",
            widthUnit: "px",
        }, {
            field: "is_lock",
            title: "{{ __('ts.Locked') }}",
            align: "center",
            formatter: function(b, c, a) {
                return cform.getValue(typeData['lockType'], b)
            },
            sortable: true,
        }, {
            field: "merchant_addr",
            title: "{{ __('ts.MerchantAddr') }}",
            align: "center"
        }, {
            field: "game_domain",
            title: "{{ __('ts.GameDomain') }}",
            align: "center"
        }, {
            field: "api_mode",
            title: "{{ __('ts.API Mode') }}",
            align: "center",
            formatter: function(b, c, a) {
                return cform.getValue(typeData['apiModeType'], b)
            },
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
                    "<a class=\"btn btn-xs btn-danger\" onclick='delAccount(\"" + c.id + "\")'>{{ __('ts.Del') }}</a>" +
                    "<a class=\"btn btn-xs btn-info\" onclick='showJSONModal(" + JSON.stringify(c) + ")'>{{ __('ts.Settings') }}</a>"
            }
        }]
    }


    function showJSONModal(data) {
        $('#JSONModal form').find("[data-field='id']").val(data['id'])
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
                // cform.patch("enabledToggle", apiPath + 'server/game/room/enabled/' + data['id'])
                $.ajax({
                    url: apiPath + 'customer/client/json/' + data['id'],
                    type: "get",
                    success: function(d) {
                        $('#JSONItems').html(d)

                        $('.actionButton').click(function() {
                            var act = $(this).attr('act');
                            var field = $(this).attr('field');
                            $('input[data-field="actionButton"]').val(act)
                            $('input[data-field="actionField"]').val(field)

                            console.log(act, field);
                            $('#JSONBtnSubmit').click()
                        })

                        Swal.close()
                        $('#JSONModal').modal()
                    }
                })
            }
        })
    }

    function createGameOC() {
        var content = '<input type="checkbox" class="chk"> ALL' + '<br>'
        for (var i in typeData['gameAliasType']) {
            content += '&nbsp;<input type="checkbox" value="' + typeData['gameAliasType'][i]['key'] + '"> ' + typeData['gameAliasType'][i]['value'] + '<br>'
        }
        $('.game_oc_checkbox').html(content)
        $('.chk').change(function() {
            $(this).parent().find('input[type=checkbox]').prop('checked', $(this).is(':checked'))
        })
    }

    $(function() {

        common.getAjax(apiPath + "getbasedata?requireItems=lockType,apiModeType,gameAliasType", function(a) {
            typeData = a.result
            $("#selLockType").initSelect(a.result.lockType, "key", "value", "{{ __('ts.lock status') }}")
            $(".api_mode").initSelect(a.result.apiModeType, "key", "value", "{{ __('ts.api mode') }}")
            $(".is_lock").initSelect(a.result.lockType, "key", "value", "{{ __('ts.lock status') }}")

            $("#btnSearch").initSearch(apiPath + "customer/client", getColumns(), {
                sortName: "id",
                sortOrder: 'desc'
            })

            $("#btnSubmit").click()
            createGameOC()
        })


        $('#updateBtnSubmit').click(function() {
            var id = $('#editModal form').find("[data-field='id']").val()
            var gameOCVal = []
            var gameMCVal = []
            $('.game_oc_edit').find('input:checkbox').each(function() {
                var val = $(this).val()
                if ($(this).prop('checked') && parseInt(val) > 0) {
                    gameOCVal.push($(this).val())
                }
            })
            $('.game_oc_edit_value').val(gameOCVal.join(','))

            $('.game_mc_edit').find('input:checkbox').each(function() {
                var val = $(this).val()
                if ($(this).prop('checked') && parseInt(val) > 0) {
                    gameMCVal.push($(this).val())
                }
            })
            $('.game_mc_edit_value').val(gameMCVal.join(','))

            cform.patch('editModal', apiPath + 'customer/client/' + id, function(d) {
                myAlert.success(d.result)
                $('#editModal').modal('hide');
                $('#btnSearch').click()
            })
        })

        $('#createBtnSubmit').click(function() {
            var gameOCVal = []
            $('.game_oc_create').find('input:checkbox').each(function() {
                var val = $(this).val()
                if ($(this).prop('checked') && parseInt(val) > 0) {
                    gameOCVal.push($(this).val())
                }
            })
            $('.game_oc_create_value').val(gameOCVal.join(','))
            cform.post('createModal', apiPath + 'customer/client')
        })

        $('#btnCreate').click(function() {
            showCreateModal()
        })

        $('#JSONBtnSubmit').click(function() {
            var id = $('#JSONModal form').find("[data-field='id']").val()
            // console.log(common.getFields('JSONModal'))
            var data = {}
            var value = $('#JSONModal form').serializeArray()
            $.each(value, function (index, item) {
                var keys = item.name.replace(/]/g, '').split('[')

                for (var i = 0; i < keys.length;i++) {
                    var t = Number(keys[i])
                    if (!isNaN(t)) {
                        // console.log('', t)
                        keys[i] = parseInt(keys[i])
                    }
                }

                if (keys.length == 2) {
                    if (data[keys[0]] == undefined) {
                        data[keys[0]] = typeof(keys[1]) == 'number' ? [] : {}
                    }

                    data[keys[0]][keys[1]] = item.value
         
                } else if (keys.length == 3) {
                    if (data[keys[0]] == undefined) {
                        data[keys[0]] = typeof(keys[1]) == 'number' ? [] : {}
                    }

                    if (data[keys[0]][keys[1]] == undefined) {
                        data[keys[0]][keys[1]] = typeof(keys[2]) == 'number' ? [] : {}
                    }

                    data[keys[0]][keys[1]][keys[2]] = item.value

                } else if (keys.length == 4) {
                    if (data[keys[0]] == undefined) {
                        data[keys[0]] = typeof(keys[1]) == 'number' ? [] : {}
                    }

                    if (data[keys[0]][keys[1]] == undefined) {
                        data[keys[0]][keys[1]] = typeof(keys[2]) == 'number' ? [] : {}
                    }

                    if (data[keys[0]][keys[1]][keys[2]] == undefined) {
                        data[keys[0]][keys[1]][keys[2]] = typeof(keys[3]) == 'number' ? [] : {}
                    }

                    data[keys[0]][keys[1]][keys[2]][keys[3]] = item.value
                } else {
                    data[keys[0]] = item.value
                }
            })

            cform.patch2('JSONModal', apiPath + 'customer/client/json/' + id, function(d) {
                if (d == 'success') {
                    myAlert.success(d)
                    $('#JSONModal').modal('hide')
                } else {
                    $('#JSONItems').html(d)

                    $('.actionButton').click(function() {
                        var act = $(this).attr('act');
                        var field = $(this).attr('field');
                        $('input[data-field="actionButton"]').val(act)
                        $('input[data-field="actionField"]').val(field)

                        console.log(act, field);
                        $('#JSONBtnSubmit').click()
                    })
                }
            }, JSON.stringify(data))
        })

        

        $('#DefaultJSONBtnSubmit').click(function() {
            var id = $('#JSONModal form').find("[data-field='id']").val()
            cform.patch2('JSONModal', apiPath + 'customer/client/json/' + id + '?autoFixed=1', function(d) {
                if (d == 'success') {
                    myAlert.success(d)
                    $('#JSONModal').modal('hide')
                } else {
                    $('#JSONItems').html(d)
                    $('.actionButton').click(function() {
                        var act = $(this).attr('act');
                        var field = $(this).attr('field');
                        $('input[data-field="actionButton"]').val(act)
                        $('input[data-field="actionField"]').val(field)
                        $('#JSONBtnSubmit').click()
                    })
                }
            }, '{}')
        })
    })
</script>
@stop