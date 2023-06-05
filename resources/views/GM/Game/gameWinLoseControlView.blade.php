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
                                <input type="text" class="form-control" data-field="gameid" placeholder="{{ __('ts.GAMEID') }}" value="" />
                                <input type="text" class="form-control" data-field="name" placeholder="{{ __('ts.name') }}" value="" />
                                <button type="button" class="btn btn-default" id="btnSearch">
                                    <i class="fas fa-search"></i>{{ __('ts.Search') }}
                                </button>
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

<div class="modal fade" id="JSONModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('ts.JSON Edit') }} : <span id="keyVal"></span></h4>
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
                <!-- <input type="button" class="btn btn-info" value="{{ __('ts.AutoFixed') }}" id="DefaultJSONBtnSubmit" /> -->
                <input type="button" class="btn btn-primary" value="{{ __('ts.Submit') }}" id="JSONBtnSubmit" />
            </div>
        </div>
    </div>
</div>

<div id="enabledToggle" class="hide" style="display: none">
    <form>
        <input type="text" class="form-control" data-field="enabled">
    </form>
</div>

</div>
@append

@section('content')
<!-- edit form -->

@stop

@section('script')

<script id="processInput" type="text/html">
    <div class="form-group row">
        <div class="col-sm-8">

            <input type="text" class="form-control" data-field="roomIDs" val />
        </div>
        <div class="col-md-4">
            <button class="btn btn-xs btn-danger" onclick="removeProcessItem(this)">
                remove
            </button>
        </div>
    </div>
</script>

<script>
    var typeData = []

    function showJSONModal(data) {
        var clientId = $('#divSearch').find("[data-field='client_id']").val()
        $('#JSONModal form').find("[data-field='id']").val(data['id'])
        $('#keyVal').html("stock_ctr_" + clientId + "_" + data['gameid'] + "_" + data['id']);
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
                // cform.patch("enabledToggle", apiPath + 'server/game/room/enabled/' + data['id'])
                $.ajax({
                    url: apiPath + 'game/winlosecontrol/json/' + clientId + '/' + data['id'],
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

    function getColumns() {
        return [{
                field: "id",
                title: "{{ __('ts.ID') }}",
                align: "center",
                sortable: true,
            }, {
                field: "gameid",
                title: "{{ __('ts.GAME') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['playType'], b) + '(' + b + ')'
                }
            }, {
                field: "name",
                title: "{{ __('ts.Name') }}",
                align: "center",
                sortable: true,
            },

            {
                field: "enabled",
                title: "{{ __('ts.Enabled') }}",
                align: "center",
                sortable: true,
                formatter: function(b, c, a) {
                    return cform.getValue(typeData['enabledType'], b)
                }
            },
            {
                field: "updated",
                title: "{{ __('ts.Updated') }}",
                align: "center",
            },
            {
                field: "stock_value",
                title: "{{ __('ts.StockValue') }}",
                align: "center",
            },
            {
                field: "stock_imp_value",
                title: "{{ __('ts.StockImpValue') }}",
                align: "center",
            },
            {
                field: "-",
                title: "{{ __('ts.Action') }}",
                align: "center",
                formatter: function(b, c, a) {
                    return "<a class=\"btn btn-xs btn-warning\" onclick='showJSONModal(" + JSON.stringify(c) + ")'>{{ __('ts.JSON') }}</a>"
                }
            }
        ]
    }

    $(function() {

        common.getAjax(apiPath + "getbasedata?requireItems=enabledType,experienceType,playType,customerAPIType2", function(a) {
            typeData = a.result
            $("#client_id").initSelect(a.result.customerAPIType2, "key", "value", "{{ __('ts.Client') }}", 0 in a.result.customerAPIType2 ? a.result.customerAPIType2[0]['key'] : null)
            $("#btnSearch").initSearch(apiPath + "game/winlosecontrol", getColumns())
            $("#btnSubmit").click()

            $(".enabled").initSelect(a.result.enabledType, "key", "value", "{{ __('ts.enabled') }}")
            $(".tiyan").initSelect(a.result.experienceType, "key", "value", "{{ __('ts.experience') }}")

            $(".gameid").initSelect(a.result.playType, "key", "value", "{{ __('ts.play') }}")

            // common.initSection(true)
        });

        $('#JSONBtnSubmit').click(function() {
            var clientId = $('#divSearch').find("[data-field='client_id']").val()
            var id = $('#JSONModal form').find("[data-field='id']").val()
            // console.log(common.getFields('JSONModal'))
            var data = {}
            var value = $('#JSONModal form').serializeArray()
            var importFile = $('#file1').prop('files')

            // console.log(importFile)
            // console.log(importFile)
            // console.log(importFile.val())
            if (importFile && importFile[0]) {
                var file = importFile[0]
                var fr = new FileReader()
                // fr.onload = receivedText
                fr.readAsText(file)
                //fr.readAsBinaryString(file) //as bit work with base64 for example upload to server
                // fr.readAsDataURL(file)

                fr.onload = function(e) {
                    cform.patch2('JSONModal', apiPath + 'game/winlosecontrol/json/' + clientId + '/' + id + '?importJson=1', function(d) {
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
                    }, e.target.result)
                }
                return;
            }

            $.each(value, function(index, item) {
                var keys = item.name.replace(/]/g, '').split('[')

                for (var i = 0; i < keys.length; i++) {
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


            cform.patch2('JSONModal', apiPath + 'game/winlosecontrol/json/' + clientId + '/' + id, function(d) {
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

        // $('#DefaultJSONBtnSubmit').click(function() {
        //     var clientId = $('#divSearch').find("[data-field='client_id']").val()
        //     var id = $('#JSONModal form').find("[data-field='id']").val()
        //     cform.patch2('JSONModal', apiPath + 'game/winlosecontrol/json/' + clientId + '/' + id + '?autoFixed=1', function(d) {
        //         if (d == 'success') {
        //             myAlert.success(d)
        //             $('#JSONModal').modal('hide')
        //         } else {
        //             $('#JSONItems').html(d)
        //             $('.actionButton').click(function() {
        //                 var act = $(this).attr('act');
        //                 var field = $(this).attr('field');
        //                 $('input[data-field="actionButton"]').val(act)
        //                 $('input[data-field="actionField"]').val(field)
        //                 $('#JSONBtnSubmit').click()
        //             })
        //         }
        //     }, '{}')
        // })
    })
</script>
@stop