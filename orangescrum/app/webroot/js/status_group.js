var chck_change = 0;
$(function(){
    $('input[id^="color"]').each(function(){
        $(this).css({color:$(this).css('background')});
    });
});
function submitWorkflow(id) {
    var msg = "";
    var wfl_name = $('#' + id).val().trim();
    var projid = $("#hid_wflproj").val().trim();
    if (wfl_name == "") {
        msg = _("'Task Status Group Name' cannot be left blank!");
        $('#' + id).focus();
        showTopErrSucc('error', msg);
        return false;
    }
    $("#btn").css({
        "visibility": "hidden"
    });
    $("#settingldr").css({
        "display": "block"
    });
    var wflid = $("#hid_wfl").val();
    $(".workflow_edit_button").hide();
    $.post(HTTP_ROOT + "taskstatusgroup/Workflows/ajax_check_workflow_exists", {
        "wflid": wflid,
        "name": escape(wfl_name),
        "project_id": projid
    }, function (data) {
        if (data) {
            if (data == "Workflow") {
                $("#btn").css({
                    "visibility": "visible"
                });
                $("#btn").show();
                $("#settingldr").hide();
                msg = _("'Task Status Group Name' is already exists!");
                showTopErrSucc('error', msg);
                $('#' + id).focus();
                return false;
            } else {
                //$("#pg").val($(".button_page").html());
                //$("#validateprj").val('1');
                document.wfledit.submit();
                return true;
            }
        }
    });
}
function workflow_details(id, name) {
    var url = HTTP_ROOT + 'taskstatusgroup/Workflows/workflow_details';
    if (id == 0 && (name == "" || name == "Default Task Status Group" || name == "Default Workflow")) {
        $('.wfl_div').hide();
        $('.workflow_details').html(tmpl('default_workflow', default_stages));
        $(".workflow_detail_back").show();
        $('.workflow_details').show();
        for (var key in default_stages) {
            var getdata = default_stages[key];
            $('#percentage_' + getdata.Status.id).slider({
                "value": getdata.Status.percentage,
                "animate": true,
                range: "min",
                start: function (event, ui) {
                    event.stopPropagation();
                },
                slide: function (event, ui) {
                    event.stopPropagation();
                    return false;
                }
            });
        }
        return false;
    }
    $.post(url, {'id': id, 'name': name}, function (data) {
        //console.log(data.res);
        $('.wfl_div').hide();
        $('.workflow_details').html(tmpl('workflow_det', data));
        $('#wfl_txt').val(name);
        $(".workflow_detail_back").show();
        $('.workflow_details').show();
        $('[rel=tooltip]').tipsy({gravity: 's', fade: true});
        setTimeout(function () {
            //$('.stages').find('li').not($('.stages').find('li').first()).not($('.stages').find('li').last()).removeClass('ui-state-disabled1');
            $('.stages').find('.delete:first,.delete:last').css('visibility', 'hidden');
            $('.stages').find('.percentage:first,.percentage:last').css('visibility', 'hidden');
            $('.stages').find('.percent_txt:first,.percent_txt:last').css('visibility', 'visible');
            $(".color").ColorPicker({
                //flat: true,
                //color: '#0000ff',
                onSubmit: function (hsb, hex, rgb, el) {
                    $(el).val('#' + hex);
                    $(el).ColorPickerHide();
                    var sid = $(el).attr('id').split("_");
                    changeStatusColor(sid[1]);
                },
                onBeforeShow: function () {
                    $(this).ColorPickerSetColor(this.value);
                },
                onHide: function (colpkr) {
                    $(colpkr).fadeOut(500);
                    return false;
                }
            })
                    .on("keyup", function () {
                        $(this).ColorPickerSetColor(this.value);
                    });
            for (var key in data) {
                var res = data[key];
                for (var key in res) {
                    var getdata = res[key];
                    for (var k in getdata.Status) {
                        var sts = getdata.Status[k];
                        var stval;
                        $('#percentage_' + sts.id).slider({
                            "value": sts.percentage,
                            "animate": true,
                            range: "min",
                            slide: function (event, ui) {
                                $(event.target).closest('.percentage').find('span.percent_txt').text(ui.value + ' %');
                            },
                            start: function (event, ui) {
                                stval = ui.value;
                            },
                            stop: function (event, ui) {
                                var sid = $(event.target).closest('.percentage').attr('id').split("_");
                                var prev_li = $(event.target).attr('id').split("_");
                                var prev_liId = $("#"+prev_li[1]).prev('li').attr('id'); 
                                
                                var prev_prct_val = parseInt($('#'+prev_liId).find('input[id^="percentagehid_"]').val()) ;
                                if (ui.value > 0 && ui.value < 100 && ui.value > prev_prct_val) {
                                    $("#percentagehid_" + sid[1]).val(ui.value);
                                    changeStatusPercent(sid[1], ui.value);
                                } /*else if(ui.value < prev_prct_val){
                                    $("#percentagehid_" + sid[1]).val(stval);
                                    $("#txt_" + sid[1]).html(stval + " %");
                                    $('#percentage_' + sid[1]).slider("value", stval);
                                    showTopErrSucc('error', _('Please select percentage range between 1 to 99.'));
                                } */else {
                                    $("#percentagehid_" + sid[1]).val(stval);
                                    $("#txt_" + sid[1]).html(stval + " %");
                                    $('#percentage_' + sid[1]).slider("value", stval);
                                    if(ui.value < prev_prct_val){
                                        showTopErrSucc('error', _('Please select percentage value higher than the previous one.'));
                                    } else {
                                        showTopErrSucc('error', _('Please select percentage range between 1 to 99.'));
                                }
                                    
                            }
                            }
                        });
                    }
                }
            }
            $('.stages').sortable({
                cursor: "move",
                handle: ".sort_number",
                items: "li:not(.ui-state-disabled1)",
                update: function (event, ui) {
                    update_sequence();
                }
            });
            /*$('.stages').find('li').draggable({
                cursor: "move",
                handle: '.delete',
                stack: "li",
                cancel: ".sort_number",
                revert: "invalid",
                start: function (event, ui) {
                    var top = $(this).position().top;
                    var left = $(this).position().left;

                    $(this).data('orgTop', top);
                    $(this).data('orgLeft', left);
                }
            });
            $(".delete-wfl").droppable({
                accept: "li",
                drop: function (event, ui) {
                    if ($(event.target).hasClass('delete-wfl')) {
                        var sts_id = $(ui.helper).attr('id');
                        $(ui.helper).hide();
                        delete_status(sts_id, ui);
                    }
                }
            });*/
        }, 500);
    }, 'json');
}

function delete_status(sid, ui) {
    var wid = $('#workflow_hid').val();
    var url = HTTP_ROOT + "taskstatusgroup/Workflows/deleteTaskStatus";
    var arg = arguments[2];
    var sts = "";
    if (typeof ui != 'object') {
        sts = confirm(_('Are you sure want to delete the status ?'));
    }

    if (sts == true || typeof ui == 'object') {
        if ($('.stages').find('li').length > 1) {
            $.post(url, {'sid': sid, 'wid': wid}, function (data) {
                if (data.msg == 'success') {
                    showTopErrSucc('success', _('The status deleted successfully.'));
                    if (typeof ui != 'object') {
                        var li_id = $(arg).closest('li').attr('id');
                        $('#' + li_id).remove();
                    } else {
                        $(ui.helper).remove();
                    }
                    update_sequence();
                } else if (data.msg == 'fail') {
                    if (typeof ui == 'object') {
                        $(ui.helper).show();
                    }
                    showTopErrSucc('error', _('Status cannot be deleted.'));
                } else if (data.msg == 'not authorised') {
                    if (typeof ui == 'object') {
                        var top = $(ui.helper).attr('orgTop');
                        var left = $(ui.helper).attr('orgLeft');
                        $(ui.helper).css({'top': 0, 'left': 0});
                        $(ui.helper).show();
                    }
                    showTopErrSucc('error', _('Status cannot be deleted as it has assigned to some of the tasks.'));
                }
            }, 'json');
        } else {
            showTopErrSucc('error', _('Minimun one status need to be present.'));
            return false;
        }
    } else {
        return false;
    }
}

function update_sequence(pass) {
    $('#caseLoader').show();
    var wid = $('#workflow_hid').val();
    var url = HTTP_ROOT + "taskstatusgroup/Workflows/update_status_sequence";
    var order = new Array();
    $(".stages li").each(function (i) {
        var str = {"status_id": $(this).attr('id').replace(/[^\d+]/g, ''), "seq_odr": (i + 1)};
        order.push(str);
        $(this).find('h1.sort_number').text(i + 1);
    });
    $.post(url, {"workflow_id": wid, "order": order}, function (data) {
        if (data) {
            $('#caseLoader').hide();
            if (pass == 'reload') {
                workflow_details(wid, $('#wfl_txt').val());
            } else {
                showTopErrSucc('success', _('Status sequence changed successfully.'));
            }
        }
    });
}

function changeStatusName(sid, obj) {
    chck_change = 1;
    var url = HTTP_ROOT + "taskstatusgroup/Workflows/changeStatusName";
    var wid = $('#workflow_hid').val();
    if (sid != 0) {
        if ($("#new_wfl_txt").val() == "" || $("#new_wfl_txt").val() == "Untitled Task Status Group") {
            $("#new_wfl_txt").focus();
            showTopErrSucc('error', _('Task Status Group name cannot be left blank.'));
            return false;
        }
        var tmp_id = $(obj).attr("id").split('_');
        var params = {'name': ''};
        var name = '';
        var error = 0;
        var label = '';
        var prev_name = $("#prev_name_"+tmp_id[1]).val();
        if (tmp_id[1] != sid) {
            label = $('#name_' + tmp_id[1]).val().trim();
            if (label != '') {
                params.name = label;
                params.seq_odr = $('.stages').find('li#n' + tmp_id[1]).index() + 1;
                params.color = $('#color_' + tmp_id[1]).val();
                params.percentage = $("#percentagehid_" + tmp_id[1]).val();
                params.prjuid = $("#projFil").val();
                sid = 0;
            } else {
                error = 1;
            }
        } else {
            label = $('#name_' + sid).val().trim();
            if (label != '') {
                var name = label;
            } else {
                error = 1;
            }
        }
        if (error != 1) {
            $("#caseLoader").show();
            $.post(url, {'wid': wid, "sid": sid, "params": params, "name": name}, function (data) {
                    $('#caseLoader').hide();
                if (data == '0') {
                    
                    showTopErrSucc('success', _('Status updated successfully.'));
                    chck_change = 0;
                    if (params.name != '') {
                        update_sequence('reload');
                    }
                } else if(data == '1'){
                    showTopErrSucc('error', _('Status name cannot be saved.'));
                } else {
                    showTopErrSucc('error', _('Status name already exists .'));
                    $("#name_"+tmp_id[1]).val(prev_name);
                    return false;
                }
            });
        } else {
            showTopErrSucc('error', _('Status name cannot be left blank.'));
            return false;
        }
    }
}

function changeStatusColor(sid) {
    chck_change = 1;
	var most_latestk = $('#most_latestk').val();
	var nm = $('#name_'+most_latestk).val().trim();
    if (sid) {
        if ($("#new_wfl_txt").val() == "" || $("#new_wfl_txt").val() == "Untitled Task Status Group") {
            $("#new_wfl_txt").focus();
            showTopErrSucc('error', _('Task Status Group name cannot be left blank.'));
            return false;
        }
        var label = $('#name_' + sid).val();
        if (label == '' || typeof label == 'undefined') {
            showTopErrSucc('error', _('Status name cannot be left blank.'));
            $('#color_' + sid).val("#8dc2f8");
            return false;
        } else {
            $("#caseLoader").show();
            var color = $('#color_' + sid).val();
            var url = HTTP_ROOT + "taskstatusgroup/Workflows/changeStatusColor";
            $.post(url, {'status_id': sid, 'color': color}, function (data) {
                if (data) {
                    chck_change = 0;
                    $("#caseLoader").hide();
                    $('#color_' + sid).css({background:color,color:color});
                    showTopErrSucc('success', _('Status color changed successfully.'));
                }
            });
        }
    }
}

function changeStatusPercent(sid, percent) {
    chck_change = 1;
    if (sid) {
        if ($("#new_wfl_txt").val() == "" || $("#new_wfl_txt").val() == "Untitled Task Status Group") {
            $("#new_wfl_txt").focus();
            showTopErrSucc('error', _('Task Status Group name cannot be left blank.'));
            return false;
        }
        var label = $('#name_' + sid).val();
        if (label == '' || typeof label == 'undefined') {
            showTopErrSucc('error', _('Status name cannot be left blank.'));
            //$('#percentage_' + sid).slider({"value": 10});//prb commented
            $('#percentage_' + sid).slider({"value": percent});
            $('#percentage_' + sid).find('div.ui-slider-range-min').css({"width": '10%'});
            return false;
        } else {
            $("#caseLoader").show();
            var url = HTTP_ROOT + "taskstatusgroup/Workflows/changeStatusPercent";
            $.post(url, {'status_id': sid, 'percent': percent}, function (data) {
                if (data) {
                    $("#caseLoader").hide();
                    chck_change = 0;
                    $("#txt_" + sid).html(percent + " %");
                    showTopErrSucc('success', _('Status percentage changed successfully.'));
                }
            });
        }
    }
}
function createStatus() {
    if($('.tmpStatusField').length>0){
        $('.tmpStatusField').find('input[id^=name_]').focus();
        return false;
    }
	var most_latestk = $('#most_latestk').val();	
    if (!checkName()) {
        var clone = $('ul.stages').find('li:first').clone();
        var wid = $("#workflow_hid").val();
        var size = $('ul.stages').find('li').size();
		size = parseInt(most_latestk)+1;
		$('#most_latestk').val(size);
        $('ul.stages').find('li:last').find('h1.sort_number').text(parseInt($('ul.stages').find('li:last-child').find('h1.sort_number').text())+1);
        var prev_percent = parseInt($('ul.stages').find('li:last-child').prev('li').find('input[id^="percentagehid_"]').val())+1;
		if(prev_percent == '100'){
			prev_percent = 99;
		}
        clone.attr("id", 'n' + size).addClass('tmpStatusField');
        clone.find('h1.sort_number').text(parseInt($('ul.stages').find('li:last-child').find('h1.sort_number').text())-1);
		clone.find('input[id^="prev_name_"]').val('').attr({'id': 'prev_name_' + size});//added by prb
        clone.find('input.name').val('').attr({'id': 'name_' + size});
        clone.find('input.color').val('#8dc2f8').css({background:'#8dc2f8',color:'#8dc2f8'}).attr({'id': 'color_' + size});
        clone.find('div.ui-slider-range-min').css({"width": '10%'});
        clone.find('div[id^="percentage_"]').attr("id", 'percentage_' + size);
        clone.find('input.percent').val(prev_percent).attr({'id': 'percentagehid_' + size});
        clone.find('span[id^="txt_"]').text(prev_percent +"%").attr({"id": 'txt_' + size});
        clone.find('div[id^="percentage_"]').slider({
            "value": prev_percent,
            "animate": true,
            range: "min",
            slide: function (event, ui) {
                $(event.target).closest('.percentage').find('span.percent_txt').text(ui.value + ' %');
            },
            stop: function (event, ui) {
                var sid = $(event.target).closest('.percentage').attr('id').split("_");
                changeStatusPercent(sid[1], ui.value);
            }
        });
        clone.find('input.color').ColorPicker({
            //flat: true,
            //color: '#0000ff',
            onSubmit: function (hsb, hex, rgb, el) {
                $(el).val('#' + hex);
                $(el).ColorPickerHide();
                var sid = $(el).attr('id').split("_");
                changeStatusColor(sid[1]);
            },
            onBeforeShow: function () {
                $(this).ColorPickerSetColor(this.value);
            },
            onHide: function (colpkr) {
                //$(el).val(hex);
                $(colpkr).fadeOut(500);
                return false;
            }
        })
                .on("keyup", function () {
                    $(this).ColorPickerSetColor(this.value);
                });
        //clone.css({"display":"block"});
        //$('ul.stages').append(clone);
            clone.find('.percentage').css({'visibility': 'visible'});
            //clone.removeClass('ui-state-disabled1');
            $('ul.stages').find("li:last").before(clone);
    }
}

/* Function to check whether any label name is blank or not */
function checkName() {
    var count = 0;
    $(document).find('ul.stages').find('li').each(function () {
        if ($(this).find('input.name').val() == "") {
            count++;
        }
    });
    if (count > 0) {
        chck_change = 1;
        return true;
    } else {
        chck_change = 0;
        return false;
    }
}

function newWorkflow() {
    $('.wfl_div').hide();
    $('.workflow_details').html(tmpl('new_workflow', default_stages));
    $(".workflow_detail_back").show();
    $('.workflow_details').show();
    //$('.stages').find('li').not($('.stages').find('li').first()).not($('.stages').find('li').last()).removeClass('ui-state-disabled1');
    $('.stages').find('.delete:first,.delete:last').css('visibility', 'hidden');
    $('.stages').find('.percentage:first,.percentage:last').css('visibility', 'hidden');
    $('.stages').find('.percent_txt:first,.percent_txt:last').css('visibility', 'visible');
    $(".color").ColorPicker({
        //flat: true,
        //color: '#0000ff',
        onSubmit: function (hsb, hex, rgb, el) {
            $(el).val('#' + hex);
            $(el).ColorPickerHide();
            var sid = $(el).attr('id').split("_");
            changeStatusColor(sid[1]);
        },
        onBeforeShow: function () {
            $(this).ColorPickerSetColor(this.value);
        },
        onHide: function (colpkr) {
            //$(el).val(hex);
            $(colpkr).fadeOut(500);
            return false;
        }
    })
            .on("keyup", function () {
                $(this).ColorPickerSetColor(this.value);
            });
    var count = 0;
    for (var key in default_stages) {
        var getdata = default_stages[key];
        var stval;
        count++;
        $('#percentage_' + count).slider({
            "value": getdata.Status.percentage,
            "animate": true,
            range: "min",
            slide: function (event, ui) {
                $(event.target).closest('.percentage').find('span.percent_txt').text(ui.value + ' %');
            },
            start: function (event, ui) {
                stval = ui.value;
            },
            stop: function (event, ui) {
                var sid = $(event.target).closest('.percentage').attr('id').split("_");
                if ($("#new_wfl_txt").val().trim() != "") {
                    $("#percentagehid_" + sid[1]).val(ui.value);
                    changeStatusPercent(sid[1], ui.value);
                } else {
                    $("#percentagehid_" + sid[1]).val(stval);
                    $("#txt_" + sid[1]).html(stval + " %");
                    $('#percentage_' + sid[1]).slider("value", stval);
                    showTopErrSucc('error', _('Please add Task Status Group name to change percentage.'));
                }
            }
        });
    }
    $('.stages').sortable({
        cursor: "move",
        handle: ".sort_number",
        items: "li:not(.ui-state-disabled1)",
        update: function (event, ui) {
            $('#caseLoader').show();
            var wid = $('#workflow_hid').val();
            var url = HTTP_ROOT + "taskstatusgroup/Workflows/update_status_sequence";
            var order = new Array();
            $(".stages li").each(function (i) {
                var str = {"status_id": $(this).attr('id'), "seq_odr": (i + 1)};
                order.push(str);
                $(this).find('h1').text(i + 1);
            });
            $.post(url, {"workflow_id": wid, "order": order}, function (data) {
                if (data) {
                    $('#caseLoader').hide();
                    chck_change = 0;
                    showTopErrSucc('success', _('Status sequence changed successfully.'));
                }
            });
        }
    });
    //$(".stages li").disableSelection();
}
function addNewTaskStatus() {
    openPopup();
    $('#newstatus_btn').text(_('Add'));
    $(".new_taskstatus").show();
    $(".loader_dv").hide();
    //setting default form field value

    $('#inner_taskstatus').show();
    $("#task_status_nm").val('');
    $("#task_status_col").val('');
    $("#task_status_nm").focus();
}

function validateTaskStatus() {
    var msg = "";
    var nm = $.trim($("#task_status_nm").val());
    var id = $.trim($("#new-statusid").val());
    var col = $.trim($("#task_status_col").val());
    $("#tterr_msg").html("");
    if (nm === "") {
        msg = _("'Name' cannot be left blank!");
        $("#tserr_msg").show();
        $("#tserr_msg").html(msg);
        $("#task_status_nm").focus();
        return false;
    } else {
        if (!nm.match(/^[A-Za-z0-9]/g)) {
            msg = _("'Name' must starts with an Alphabet or Number!");
            $("#tserr_msg").show();
            $("#tserr_msg").html(msg);
            $("#task_status_nm").focus();
            return false;
        }
    }
    if (col === "") {
        msg = _("'Color' cannot be left blank!");
        $("#tserr_msg").show();
        $("#tserr_msg").html(msg);
        $("#task_status_col").focus();
        return false;
    }
    col = '#' + col;
    $.post(HTTP_ROOT + "taskstatusgroup/Workflows/validateTaskStatus", {'id': id, 'name': nm, 'col': col}, function (data) {
        if (data.status == 'success') {
            $("#tserr_msg").hide();
            $("#tsbtn").hide();
            $("#tsloader").show();
            $('#customTaskStatusForm').submit();
        } else {
            $("#tsbtn").show();
            $("#tsloader").hide();
            if (data.msg == 'name') {
                $("#tserr_msg").show().html(_('Name already esists!. Please enter another name.'));
            } else if (data.msg == 'col') {
                $("#tserr_msg").show().html(_('Color already esists!. Please enter another color.'));
            } else {
                $("#tserr_msg").show().html(_('Oops! Missing input parameters.'));
            }
            return false;
        }
    }, 'json');
}

function saveTaskStatus() {
    var isStatusIds = 0;
    $(".all_ts").each(function () {
        if ($(this).is(":checked")) {
            isStatusIds = 1;
        }
    });

    if (parseInt(isStatusIds)) {
        $('.all_ts').attr('disabled', false);
        $("#ts_save_btn").hide();
        $("#loader_img_ts").show();
        $('#task_status').attr("action", HTTP_ROOT + "taskstatusgroup/Workflows/saveTaskStatus");
        $("#task_status").submit();
        return true;
    } else {
        showTopErrSucc('error', _('Check atleast one task status.'));
        return false;
    }
}

function deleteTaskStatus(obj) {
    var nm = $(obj).attr("data-name");
    var id = $(obj).attr("data-id");

    if (confirm(_("Are you sure you want to delete")+" '" + nm + "' "+_("task status")+" ?")) {
        $("#del_tsk_" + id).hide();
        $("#lding_tsk_" + id).show();
        $.post(HTTP_ROOT + "taskstatusgroup/Workflows/deleteTaskStatus", {"id": id}, function (res) {
            if (parseInt(res)) {
                $("#dv_tsk_" + id).fadeOut(300, function () {
                    $(this).remove();
                    showTopErrSucc('success', _("Task status")+" '" + nm + "' "+_("has deleted successfully."));
                });
            } else {
                $("#lding_tsk_" + id).hide();
                $("#del_tsk_" + id).show();
                showTopErrSucc('error', _('Error in deletion of task status.'));
            }
        });
    }
}

function editTaskStatus(obj) {
    var nm = $(obj).attr("data-name");
    var id = $(obj).attr("data-id");
    var col = $(obj).attr("data-col");
    $('#newstatus_btn').text(_('Update'));
    openPopup();
    $(".new_taskstatus").show();
    $(".loader_dv").hide();
    $('#inner_taskstatus').show();
    $("#task_status_nm").val(nm);
    $("#new-statusid").val(id);
    $("#task_status_col").val(col);
    $("#task_status_nm").focus();
}
function addWorkflow() {
    var url = HTTP_ROOT + "taskstatusgroup/Workflows/add_workflow";
    var wfl_name = $("#new_wfl_txt").val().trim();
    if (wfl_name != '') {
        $("#caseLoader").show();
        $.post(url, {'wfl_name': wfl_name}, function (data) {
            if (data) {
                $("#caseLoader").hide();
                $("#add_workflow").append("<option value='"+data.id+"'>"+data.name+"</option>");
                workflow_details(data.id, data.name)
            } else{
                $("#caseLoader").hide();
                 showTopErrSucc('error', _('Task Status Group name already exists.'));
                 $("#new_wfl_txt").val('');
                return false;
            }
        }, 'json');
    } else {
        showTopErrSucc('error', _('Task Status Group name cannot be left blank.'));
        return false;
    }
}
/* Edit the name of Task Status Group */
function changeWorkflowName(id) {
    chck_change = 1;
    var url = HTTP_ROOT + "taskstatusgroup/Workflows/edit_workflow";
    $("#caseLoader").show();
    var wrkflw_name = $("#wfl_txt").val().trim();
    var wrkflw_prevname = $("#wfl_prev_txt").val().trim();
    if (wrkflw_name != '') {
        $.post(url, {'wrkflw_id': id, 'wrkflw_name': wrkflw_name}, function (data) {
            if (data == '0') {
                $("#caseLoader").hide();
                $("#wfl_txt").val(wrkflw_name);
                showTopErrSucc('success', _('Task Status Group name changed successfully.'));
                chck_change = 0;
            } else if(data == '1') {
                showTopErrSucc('error', _('Failed to update Task Status Group name.'));
            } else{
                $("#wfl_txt").val(wrkflw_prevname);
                 $("#caseLoader").hide();
                  showTopErrSucc('error', _('Task Status Group name already exists.'));
            }
        });
    } else {
        showTopErrSucc('error', _('Task Status Group name cannot be left blank.'));
        return false;
    }
}
$(function () {
    $('.workflow_detail_back').click(function () {
        checkName();
        if (chck_change == 1) {
            showTopErrSucc("error", _("Some fileds are not saved yet."));
            return false;
        }
        $(".workflow_details").hide();
        $(".workflow_detail_back").hide();
        $(".wfl_div").show();
        window.location.reload();
    });
    window.onbeforeunload = function () {
        if (chck_change == 1) {
            return false;
        }
    };
    $('.icon-del-wfl').click(function () {
        if (confirm(_("Are you sure, you want to delete this Task Status Group?"))) {
            var wid = $(this).attr("data-workflow-id");
            var url = HTTP_ROOT + "taskstatusgroup/Workflows/delete_workflow";
            $.post(url, {'wid': wid}, function (data) {
                if (data) {
                    $(this).parent().parent().parent().parent().remove();
                    showTopErrSucc('success', _('Task Status Group is successfully deleted'));
                    window.location.reload();
                } else {
                    showTopErrSucc('error', _('Task Status Group is not deleted.'));
                    return false;
                }
            });
        } else {
            return false;
        }
    });
});