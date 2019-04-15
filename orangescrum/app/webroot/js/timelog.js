$(document).ready(function(){
        $(document).on('keyup','.check_minute_range', function(e){
        var inpt = $(this).val().trim();
        var char_restirict = /^[0-9\.\:]+$/.test(inpt);
        if(!char_restirict){
           $(this).val(inpt.substr(0,inpt.length-1)); 
        }
        var t_inpt = inpt.split(":");
        var d_inpt = inpt.split(".");
        var len = t_inpt.length-1;
        var d_len = d_inpt.length-1;
        if(len >= 2 || d_len >=2 || (len & d_len)){
            $(this).val(inpt.substr(0,inpt.length-1));
            showTopErrSucc('error', _("Invalid time"));
        }else{
            if(len > 0 || d_len > 0){
                var c_ln = 0;
                var d_ln = 0;
                if(inpt.indexOf(":") != -1){
                    var sec_part = inpt.substr(inpt.indexOf(":")+1);
                    c_ln = sec_part.length;
                }
                if(inpt.indexOf(".") != -1){
                    var dsec_part = inpt.substr(inpt.indexOf(".")+1);
                    d_ln = dsec_part.length;
                }
                if(c_ln > 2 || d_ln > 2){
                    $(this).val(inpt.substr(0,inpt.length-1));
                    showTopErrSucc('error', _("Minute can not exceed 2 digit"));
                }
            }
        }        
    });
    $(document).on('focus','.tl_hours',function(){
        $(this).attr('readonly','readonly');
        $(this).closest('.timelog_block').find('.timelog_toggle_block').show();
    });
    $(document).on('click', 'body', function(e){
        $(e.target).closest('.estb').size()>0 || $(e.target).hasClass('fl') ?'':$('.estb').show();$('.est_hr').hide();
    });
    $('[rel=tooltip]').tipsy({
        gravity:'s',
        fade:true
    });
    $(document).on('click','.estb',function(event) {
        event.stopPropagation();
		$('.estb').hide();
		$('.est_hr').show();
		$('.est_hr').focus();
    }); 
	$(document).on('click','.est_hr',function(event) {
        event.stopPropagation();
		$('.estb').hide();
		$('.est_hr').show();
    }); 
	var clone = '';  
	clone = $("#ul_timelog1").clone();
    /* Add the project name as "all" in the project menu bar when all project is selected */   
    if(getCookie('All_Project') == "all"){
           $('#pname_dashboard').html("All");
            $('#projFil').val("all");
        }

});
/* Submit Time log Form*/
function submitLogTimeform(){
	$('#frmaddlogtim').submit();
}
/* Edit Time Log*/
function editTimelog(obj){
	$obj = $(obj);
	if(typeof $obj.closest('td').attr('data-logid') == 'undefined' && typeof $obj.closest('td').attr('data-prjctid') == 'undefined')return false;
	createlog('','',$obj);
}
/*delete time log*/
function deletetimelog(obj){
	$obj = $(obj);
	if(typeof $(obj).closest('td').attr('data-logid') == 'undefined')return false;
	if(confirm(_("Are you sure to delete the time log?"))){
		var prjunid = $('#projFil').val();
		$('#pagingtable').remove();
		var logid = $obj.closest('td').attr('data-logid');
		 $.ajax({
			url:HTTP_ROOT+"delete_timelog",
			data:{'projuniqid':prjunid,logid:logid},
			method:'post',
			dataType:'json',
			success:function(response){
				if(response.success == '1'){
					showTopErrSucc('success',_('Timelog deleted successfully.'));
					var params = parseUrlHash(urlHash);
                                    if (params[0] == 'details') {
                                        easycase.ajaxCaseDetails(params[1]);
                                    } else {
                                        window.location = HTTP_ROOT + "timelog";
				}
			}
			}
		});
	}
}
function changeEstHour(caseId, caseUniqId, cno, value){
        //console.log(LogTime.convertToMin($("#est_hr"+caseId).val())==LogTime.convertToMin($("#est_hr"+caseId).attr('data-default-val')));
        if(LogTime.convertToMin($("#est_hr"+caseId).val())==LogTime.convertToMin($("#est_hr"+caseId).attr('data-default-val'))){
            $("#est_hr"+caseId).val($("#est_hr"+caseId).attr('data-default-val'));
            return false;
        }

	$(".estb").hide();
	var estHour = $("#est_hr"+caseId).val();
	var estlod = "estlod"+caseId;
	$('#'+estlod+'').show();
	$(".est_hr").hide();

	if(parseInt(estHour.replace(/[^0-9]+/g, '')) == 0){
		showTopErrSucc('error',_('Estimated hour(s) can not be 0.'));
		$('#'+estlod+'').hide();
		$("#est_hr"+caseId).val(value).show();
		return;
	}
	$.post(HTTP_ROOT+"easycases/ajax_change_estHour", {"caseId":caseId, "estHour":estHour},
		function(data){ 
			//$('#'+estlod+'').hide();
			//$("#est_hr").show();
                        
            if (data.isAssignedUserFree != 1 && GTLG == 1) {
                var estimated_hr = Math.floor(data.task_details.Easycase.estimated_hours / 3600);
                openResourceNotAvailablePopup(data.task_details.Easycase.assign_to, data.task_details.Easycase.gantt_start_date, data.task_details.Easycase.due_date, estimated_hr, data.task_details.Easycase.project_id, data.task_details.Easycase.id, data.task_details.Easycase.uniq_id, data.isAssignedUserFree);
            }
	}, 'json').always(function(){
		actiononTask(caseId,caseUniqId,cno,'esthour');
	});
}
function edittimelog(obj){
	if(typeof $(obj).closest('td').attr('data-logid') == 'undefined')return false;
	createlog('','',$(obj).closest('td').attr('data-logid'));
}
function edittimelogCalendar(log_id,prjct_id){
	createlog('','',log_id,prjct_id);
}

function hidereset(){
	createCookie('datelog', '' , -365, '');
	createCookie('rsrclog', '' , -365, '');
	createCookie('timelogsort', '' , -365, '');
	createCookie('flt_typ', '' , -365, '');
	createCookie('flt_val', '' , -365, '');
	$('#btn-reset-timelog').hide();
	$('#logstrtdt,#logenddt').val('');
	$('#rsrclog').val('');
	$('#pagingtable').remove();
    var prjunid = $('#projFil').val();
    $.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'page':'log'},function(data) {
    if(data) {
            $('.timelog-detail-tbl').html('');
            $('.main-container-div').html(data);
            $('#timelogloader').hide();
            footer_update();
        }
    });
}

function createlog(casesid){
	createCookie('timelogsort', '' , -365, '');
	createCookie('logstrtdt', '' , -365, '');
	createCookie('logenddt', '' , -365, '');
	createCookie('datelog', '' , -365, '');
	createCookie('rsrclog', '' , -365, '');
    var prjunid = $('#projFil').val();
    $('#tskttl').html("");
    if(prjunid == 'all'){
        if(typeof arguments[2] != 'undefined' && typeof arguments[2] == 'object'){
			prjunid = $(arguments[2]).closest('td').attr('data-prjctUniqid');
        } else if(typeof arguments[2] != 'undefined' && typeof arguments[2] == 'string' && arguments[2] != ''){
            prjunid = arguments[3];
        } else if(typeof arguments[1] != 'undefined' && typeof arguments[1] == 'string' && arguments[1] != ''){            
            prjunid = $(arguments[4]).attr('data-puid');
        } 
        else{
            showTopErrSucc('error', _('Oops! You are in All project. Please choose a project.'));return false;
        }
        if(typeof arguments[2] != 'undefined' && typeof arguments[2] == 'number' && arguments[2] == 0){
            prjunid = arguments[3];
        }
    }
    
	openPopup('log');
    $("#whosassign1").attr('disabled',false);
    $("#tsksid").attr('disabled',false);
    $(".logtime-content").find('.plus-btn').show();
    //var dt = new Date(); 
	var dt = new Date(PROFILE_DTTM);
    $('#endtime1').timepicker('setTime', dt);
    dt.setMinutes(dt.getMinutes() - 30);
    $('#strttime1').timepicker('setTime', dt);
    $('#tsmn1').val('0:30');
    $('#tskdesc').val('').keyup();
    $('input[name=log_id]').remove();
    $('#lgtimebtn').attr('disabled',false);
    $('#lgtimebtn').removeClass('loginactive');
    if(arguments[3] != ''){
    	$('#workeddt1').datepicker("setDate", new Date(arguments[3]));	
    }else{
    $('#workeddt1').datepicker("setDate", new Date());
    }
    $('.totalbreak').val('');
    $('.crsid').hide();
    $('#is_billable1').attr('checked','checked');
    
    $(".new_log").show();
    
    if(casesid != 0){
    	var cstitle = unescape(arguments[1]);
    }
    var logid = '';
    if(typeof arguments[2] != 'undefined' && arguments[2] != '' && typeof arguments[2] == "object"){
            logid = $(arguments[2]).closest('td').attr('data-logid');
        }
    if(typeof arguments[2] != 'undefined' && arguments[2] != '' && typeof arguments[2] == "string") {
        logid = arguments[2] ;
    }   
    if(logid !=''){
        $('#lgtimebtn').find('span').html(_('Update'));
    }else{
        $('#lgtimebtn').find('span').html(_('Save'));
    }
	//alert(prjunid);return false; 
    $.post(HTTP_ROOT+"existing_task",{'projuniqid':prjunid},function(data) {
        if(data) {
                
		$('#tsksid').html(data);
		if(casesid != 0){ 
			$('#tsksid').val(casesid);
			if(cstitle.length > 60){
				$('#tskttl').html(cstitle.substr(0,60) + "...");
				$('#tskttl').attr('title',cstitle);
			}else{
				$('#tskttl').html(cstitle);
			}
		}
			var usrhtml = "<option value=''>Select User</option>";
			$.each(PUSERS, function(key, val){
				$.each(val, function(k1, v1){
					var usrid = v1['User']['id'];
					usrhtml += "<option value='"+usrid+"'>"+v1['User']['name']+"</option>";
				});
			});
			$('#whosassign1').html(usrhtml);
			$('#whosassign1').val(SES_ID);
		//}
		$(".loader_dv").hide();
		$(".new_log .popup_form").css({
			"margin-top":"0px"
		});
		$('#inner_log').show(); 
        $('#prjsid').val(prjunid);
		if(casesid == 0){
			var csprj = $('#pname_dashboard').html();
		     //   $('#tskttl').html(csprj);
                    //    $('#tskttl').html($("#tsksid option:selected").html());
			$('#tsksid').focus();
			$('#lgtimebtn').attr('disabled','disabled');
			$('#lgtimebtn').attr('title','Select a task.');
			$('#lgtimebtn').addClass('loginactive');
		}

                /*sets edit mode data*/
                if(logid!=''){
                    $.ajax({
                        url:HTTP_ROOT+"timelog_details",
                        data:{'projuniqid':prjunid,logid:logid},
                        method:'post',
                        dataType:'json',
                        success:function(response){
                            setEditTimeLog(response);
                            project_timelog_details(response.task_id,prjunid);
                            $('#tskttl').html($("#tsksid option:selected").html());
                        }
                    });
                }else{
                    project_timelog_details(casesid);
                }
        }
    });
    
   /*  $('#tsksid').find('option:first').attr('selected', 'selected');
     if(rsrch == ""){
	     var usrhtml = "<option value=''>Select User</option>";
	     $.each(PUSERS, function(key, val){
	    		$.each(val, function(k1, v1){
	    			var usrid = v1['User']['id'];
	    			usrhtml += "<option value='"+usrid+"'>"+v1['User']['name']+"</option>";
	    		});
	    	});
	    $('#whosassign1').html(usrhtml);
	    $('#whosassign1').val(SES_ID);
    }
    if(casesid != '0'){
        var cstitle = arguments[1];
        $('#tskttl').html(cstitle.substr(0,60) + "...");
    	$('#tsksid').val(casesid);
    	
    }else{
        var csprj = $('#pname_dashboard a').html();
        $('#tskttl').html(csprj);
        
       // $('#existtskid').show();
    	//showtaskpopup();
    } */
}

function modifyheader(){
	if($('#tsksid').val() != ""){
		var tskdata = $("#tsksid option:selected").text();
		if(tskdata.length > 60){
			$('#tskttl').html(tskdata.substr(0,60) + "...");
			$('#tskttl').attr('title',tskdata);
		}else{
			$('#tskttl').html(tskdata);
		}
		$('#lgtimebtn').removeClass('loginactive');
		$('#lgtimebtn').removeAttr('title');
		$('#lgtimebtn').attr('disabled',false);
	}else{
		var csprj = $('#pname_dashboard a').html();
		$('#tskttl').html(csprj);
		$('#lgtimebtn').addClass('loginactive');
		$('#lgtimebtn').attr('disabled','disabled');
	}
	
}


function showtaskpopup(){
    openPopup('log');
    $('.abc').show();
    $(".new_log").show();
    $( ".new_log" ).addClass( "ovrlaynewlog" );
    var prjunid = $('#projFil').val();
    $.post(HTTP_ROOT+"easycases/existing_task",{'projuniqid':prjunid},function(data) {
        if(data) {
            $(".loader_dv").hide();
            $('#task_log').html(data);
            $('#task_log').show();
        }
    });
    
    
}

function savetsk(){
	$('#tsksid').val($('#prjtsklist').val());
	if($('#prjtsklist').val() == ""){
		showTopErrSucc('error',_('Please select a task'));
		return false;
	}else{
		var tskdata = $("#prjtsklist option:selected").text();
		$('#slttsk').html(tskdata);
		$('#tskttl').html(tskdata);
		$('#existtskid a').show();
		$('#slttsk').show();
		closetskPopup();
	}
	
}

function closetskPopup(){
	closePopup();
	$( "ul[id^='ul_timelog']" ).each(function( index ) {
		if(index != 0){
			$(this).remove();
		}else{
			var x = $(this).attr('id');
			var ids = x.substr(2,4);
			$("#crsid"+ids).hide();
		}
	});
}

function showtimelog(srchtyp, filter){
    $('#timelogloader').show();
    createCookie('flt_typ','' , 365, '');
    createCookie('flt_val','' , 365, '');
	var user = $('#rsrclog').html();
	var prjunid = $('#projFil').val();
    if(srchtyp == 'resourcesrch'){
        var usrfltr = getCookie('rsrclog');
        if(typeof usrfltr != 'undefined' && usrfltr != ''){
            usrfltr = usrfltr+filter;
            createCookie('rsrclog', usrfltr, 365, '');
        }else{
            createCookie('rsrclog', filter, 365, '');
        }
    }
    if(srchtyp == 'datesrch'){
        if(filter == 'custom'){
            filter = $('#logstrtdt').val() + ':' + $('#logenddt').val();
            var start_date = $('#logstrtdt').val();
            var end_date = $('#logenddt').val();
        }
        createCookie('datelog', filter, 365, '');
    }
	createCookie('logusersort', user, 365, '');
	$('#pagingtable').remove();
    $('#filter_text').html('');
	$.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'page':'log'},function(data) {
		if(data) {
				//$('.timelog-detail-tbl').html('');
				$('.main-container-div').html(data);
                                $('#slct_rsrc').val(getCookie('rsrclog'));
				$('#timelogloader').hide();
                                $('#logstrtdt').val(start_date); 
                                $('#logenddt').val(end_date);
                                
				$("#btn-reset-timelog").css({'display':'inline-block'});
                $('[rel=tooltip]').tipsy({
                    gravity:'s',
                    fade:true
                });
				//$('#logstrtdt').val('');
				//$('#logenddt').val('');
			}
	});
}
function showlogfordate(date){
    $('#timelogloader').show();
    $('#pagingtable').remove();
    $('#filter_text').html('');
    var prjunid = $('#projFil').val();
    if((TPAY && TPAY == 1) || (GTLG && GTLG == 1)){
        createCookie('flt_typ','date', 365, '');
        createCookie('flt_val',date, 365, '');
    }
    $.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'date':date, 'page':'log'},function(data) {
		if(data) {
				//$('.timelog-detail-tbl').html('');
				$('.main-container-div').html(data);
				$('#timelogloader').hide();
				$("#btn-reset-timelog").show();
                                $('[rel=tooltip]').tipsy({
                                    gravity:'s',
                                    fade:true
                                });
				//$('#logstrtdt').val('');
				//$('#logenddt').val('');
			}
	});
}
function showlogforuser(usrid, user_name){
    $('#timelogloader').show();
    $('#pagingtable').remove();
    $('#filter_text').html('');
    var prjunid = $('#projFil').val();
    if((TPAY && TPAY == 1) || (GTLG && GTLG == 1)){
        createCookie('flt_typ','user', 365, '');
    createCookie('flt_val',usrid, 365, '');
    }
   
    $.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'usrid':usrid, 'user_name':user_name, 'page':'log'},function(data) {
		if(data) {
				//$('.timelog-detail-tbl').html('');
				$('.main-container-div').html(data);
				$('#timelogloader').hide();
				$("#btn-reset-timelog").show();
                                $('[rel=tooltip]').tipsy({
                                    gravity:'s',
                                    fade:true
                                });
				//$('#logstrtdt').val('');
				//$('#logenddt').val('');
			}
	});
}
function showlogfortask(csid, cstitle){
    $('#timelogloader').show();
    $('#pagingtable').remove();
    $('#filter_text').html('');
    var prjunid = $('#projFil').val();
    if((TPAY && TPAY == 1) || (GTLG && GTLG == 1)){
        if(typeof arguments[2] != 'undefined'){
            var csids = $(arguments[2]).attr('data-csid');
            var cstitles = $(arguments[2]).text(); 
        createCookie('flt_typ','task', 365, '');
        createCookie('flt_val',csids, 365, '');
        createCookie('task_name',cstitles, 365, '');
        }
    }
    
    $.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'csid':csid, 'cstitle':cstitle, 'page':'log'},function(data) {
		if(data) {
				$('.main-container-div').html(data);
				$('#timelogloader').hide();
				$("#btn-reset-timelog").show();
				$('[rel=tooltip]').tipsy({
					gravity:'s',
					fade:true
				});
				$('#btn-reset-timelog').show();
			}
	});
}
function sorting(type){
	createCookie('rsrclog', '', -365, '');
	createCookie('logstrtdt', '', -365, '');
	createCookie('logenddt', '', -365, '');
	createCookie('logusersort', '', -365, '');
	$('#timelogloader').show();
	createCookie('timelogsort', type, 365, '');
	if(type == 'Date'){
		var tsk_sort = '#tsk_sort1';
	}else if(type == 'Name'){
		var tsk_sort = '#tsk_sort2';
	}else if(type == 'Task'){
		var tsk_sort = '#tsk_sort3';
	}else{
		var tsk_sort = '#tsk_sort4';
	}
	var cls = $(tsk_sort).attr('class');
	cls = cls.split(" ");
	if(cls[3] == 'tsk_asc'){
		var sort = 'DESC';
	}else{
		var sort = 'ASC';
	}
	var prjunid = $('#projFil').val();
	var usrid = $('#rsrclog').val();
	var strdt = $('#logstrtdt').val();
	var eddt = $('#logenddt').val();
	$('#pagingtable').remove();
	$.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'page':'log', 'type':type, 'sort':sort},function(data) {
		if(data) {
				$('.main-container-div').html(data);
				$('#timelogloader').hide();
				if(sort == 'ASC'){
					$(tsk_sort).addClass('tsk_asc');
				}else{
					$(tsk_sort).addClass('tsk_desc');
				}
				$("#btn-reset-timelog").css({'display':'inline-block'});
                                $('[rel=tooltip]').tipsy({
                                    gravity:'s',
                                    fade:true
                                });
				//$('#logstrtdt').val('');
				//$('#logenddt').val('');
			}
	});
}
function open_more_opt_pgbar(cid,obj){
    obj.stopPropagation();
    open_more_opt('more_opt19',cid);
}
/*
 * author: GKM
 */
function setEditTimeLog(response){
    $("#whosassign1").val(response.user_id).attr('disabled',true);
    $("#tsksid").val(response.task_id).attr('disabled',true);
    $("#hidden_task_id").val(response.task_id);
    $(".logtime-content").find('.plus-btn').hide();
    $('#workeddt1').datepicker("setDate", new Date(response.start_datetime_v1));
    var srt_time = response.start_time.split(':');
    var smode = srt_time[0]>=12?'pm':'am';
    var shr = srt_time[0]>12?parseInt(srt_time[0])-12:srt_time[0];
    var smin = srt_time[1];
	$('#tskdesc').val(response.description);
    
    var end_time = response.end_time.split(':');
    var emode = end_time[0]>=12?'pm':'am';
    var ehr = end_time[0]>12?parseInt(end_time[0])-12:end_time[0];
    var emin = end_time[1];
    //console.log(shr+':'+smin+''+smode+' >> '+ehr+':'+emin+''+emode)
    $('#strttime1').timepicker('setTime', shr+':'+smin+''+smode);
    $('#endtime1').timepicker('setTime', ehr+':'+emin+''+emode);
    
    var break_time =  response.break_time/60;
    var bh = Math.floor(break_time/60)
    var bm = Math.floor(break_time%60)
    $('#tshr1').val((bh<10?0:'')+bh+':'+(bm<10?0:'')+bm);
    
    var total_hours = response.total_hours/60;
    var th = Math.floor(total_hours/60);
    var tm = Math.floor(total_hours%60);
    $('#tsmn1').val((th<10?0:'')+th+':'+(tm<10?0:'')+tm);
    
    //response.is_billable
    $('#is_billable1').attr('checked',(response.is_billable=='1'?true:false));
    $('#lgtimebtn').attr('disabled',false).removeClass('loginactive');
    if($('input[name=log_id]').size() >0){
        $('input[name=log_id]').val(response.log_id);
    }else{
        var logid = $('<input>').attr({type:'hidden',name:'log_id',value:response.log_id});
        $('#lgtimebtn').after(logid);
    }
    
}
var LogTime = {
    initiateLogTime:function(id){
        var id = typeof id != 'undefined' ? id : '';
        //if(id == '')return false;
        $('#start_time'+id).timepicker({
                'minTime': '12:00am',
                'step': '5',
                'forceRoundTime': true,
                'useSelect':true,
                'maxTime':'11:59pm',
                scrollbar:true,
                noneOption:{'label': 'Select','value': ''}
		});
        $('#end_time'+id).timepicker({
                'minTime': '12:00am',
                'step': '5',
                'forceRoundTime': true,
                'useSelect':true,
                'maxTime':'11:59pm',
                scrollbar:true,
                noneOption:{'label': 'Select','value': ''}
        });
        
        var d= new Date();
        $('#end_time'+id).timepicker('setTime', null);
        //d.setMinutes(d.getMinutes() - 30);
        $('#start_time'+id).timepicker('setTime', null);
        
        
        $(document).on('blur','.tl_break_time',function(){
            LogTime.updatehrs($(this).closest('.timelog_block'),id);
        }).on('change','.tl_start_time,.tl_end_time',function(){
            LogTime.updatehrs($(this).closest('.timelog_block'),id);
        });
        
    },
    updatehrs:function($obj){
		var st_time = $obj.find('.tl_start_time').val();
                var en_time = $obj.find('.tl_end_time').val();
                if(st_time == '' || en_time == ''){
                    $obj.find('.tl_hours').val('');
                    $obj.find('.tl_break_time').val('');
                    //$obj.find('.tl_start_time').val('');
                    //$obj.find('.tl_end_time').val('')
                    return false;
				}
                
                var st_timespl= '0';		
                var st_mode = (st_time.indexOf('pm') > -1) ? 'pm' : 'am';
                st_time = (st_time.indexOf('pm') > -1) ? st_time.replace('pm','') : st_time.replace('am','');
                var st_tmsp = st_time.split(":");
                if(st_mode == 'pm'){
                        st_timespl = (st_tmsp[0] < 12 ) ? parseInt(st_tmsp[0]) + 12 : 12;
		}else{
                        st_timespl = (st_tmsp[0] == 12 ) ? "00" : st_tmsp[0];
		}
                st_timesplit = st_timespl+":"+st_tmsp[1];
                st_time_minute = (parseInt(st_timespl)*60)+parseInt(st_tmsp[1]);
		
                var en_timespl = '';
                var en_mode = (en_time.indexOf('pm') > -1) ? 'pm' : 'am';
                en_time = (en_time.indexOf('pm') > -1) ? en_time.replace('pm','') : en_time.replace('am','');
                var en_tmsp = en_time.split(":");
                
                if(en_mode == 'pm'){
                    en_timespl = (en_tmsp[0] < 12 ) ? parseInt(en_tmsp[0]) + 12 : 12;
		}else{
                    en_timespl = (en_tmsp[0] == 12 ) ? "00" : en_tmsp[0];
		}
                en_timesplit = en_timespl+":"+en_tmsp[1];
                var en_time_minute = (parseInt(en_timespl)*60)+parseInt(en_tmsp[1]);
                
                if( st_time_minute <= en_time_minute){
                    
                }else{
                    //adding 24 hr to end time
                    en_time_minute = en_time_minute+1440;
                }
                var spend_duration = en_time_minute-st_time_minute;
                
                
		diffinmins = spend_duration ;
		diffhours = Math.floor(diffinmins/60);
		diffmins = (diffinmins%60);
		
                var final_spend = (diffhours)+':'+(diffmins>9?diffmins:'0'+diffmins);
                //console.log("final_spend:"+final_spend);
		$obj.find('.tl_hours').val(final_spend);
                LogTime.calculate_break($obj,id);
	},
    calculate_break:function($obj,id){
            $obj.find('.tl_break_time').val($obj.find('.tl_break_time').val().replace('-',''));
            var break_time = $.trim($obj.find('.tl_break_time').val())!=''?$.trim($obj.find('.tl_break_time').val().replace('-','')):'0';
            var spend_time = $.trim($obj.find('.tl_hours').val());
            //console.log(break_time+" >> "+spend_time);
            var br_hr = '00';
            var br_min = '00';
            var br_time = '';
            var extra_hr = '';
            if(break_time.indexOf('.')>'-1'){
                br_time = isNaN(break_time) ? 0 : break_time*60;
                br_hr = Math.floor(br_time/60);
                br_min = Math.floor(br_time%60);
            }else if(break_time.indexOf(':')>'-1'){
                br_time = break_time.split(':');
                extra_hr = (br_time[1] == '') ? 0 : Math.floor(parseInt(br_time[1])/60);
                //br_hr = parseInt(br_time[0])+parseInt(extra_hr); 
                br_hr = (br_time[0] == '') ? 0 : (parseInt(br_time[0])+parseInt(extra_hr));
                br_min = (br_time[1] == '') ? 0 : Math.floor(br_time[1]%60);
            }else{
                br_hr = break_time;                
                br_min = '0';
            }
            
            var sp_time = spend_time.split(':');
            var total_sp_min = (parseInt(sp_time[0])*60)+parseInt(sp_time[1]);
            var total_br_min = (parseInt(br_hr)*60)+parseInt(br_min);
            //console.log(total_sp_min+' < '+total_br_min);
            var spend_duration = total_sp_min-total_br_min;
            var sp_hr = Math.floor(spend_duration/60);
            var sp_min = Math.floor(spend_duration%60);
            var final_sp = parseInt(sp_hr)>0 || parseInt(sp_min) > 0 ? parseInt(sp_hr)+':'+(sp_min<10?"0":"")+sp_min : '0:00';
            
            if(total_sp_min<total_br_min){
                $('#break_time'+id).focus();
                showTopErrSucc('error',_('Break time can not exceed the total spent hours.'));
                return false;
            }
            var final_br = parseInt(br_hr)>0 || parseInt(br_min) > 0 ? parseInt(br_hr)+':'+(br_min<10?"0":"")+br_min : '';
            //console.log(final_sp+' === '+final_br);
            $obj.find('.tl_hours').val(final_sp);
            $obj.find('.tl_break_time').val(final_br);
        },
    calulate_break_minute:function(id){
        var id = typeof id != 'undefined' ? id : '';
        var break_time = $('#break_time'+id).val()!=''?$('#break_time'+id).val():'0:00';
        var br_hr = '00';
        var br_min = '00';
        var br_time = '';
        var extra_hr = '';
        if(break_time.indexOf('.')>'-1'){
            br_time = break_time*60;
            br_hr = Math.floor(br_time/60);
            br_min = Math.floor(br_time%60);
        }else if(break_time.indexOf(':')>'-1'){
            br_time = break_time.split(':');
            extra_hr = Math.floor(parseInt(br_time[1])/60);
            br_hr = parseInt((br_time[0]=='')?0:br_time[0])+parseInt(extra_hr);                
            br_min = Math.floor(br_time[1]%60);
        }else{
            br_hr = break_time;                
            br_min = '0';
        }

        var total_br_min = (parseInt(br_hr)*60)+parseInt(br_min);
        return total_br_min;
    },
    calulate_spend_minute:function(id){
        var id = typeof id != 'undefined' ? id : '';
        var st_time = $('#start_time'+id).val()!='' ? $('#start_time'+id).val() : '0:00';
        var en_time = $('#end_time'+id).val()!='' ? $('#end_time'+id).val() : '0:00';
       
       /*start time*/
        var st_timespl = '';
        var st_mode = (st_time.indexOf('pm') > -1) ? 'pm' : 'am';
        st_time = (st_time.indexOf('pm') > -1) ? st_time.replace('pm','') : st_time.replace('am','');
        var st_tmsp = st_time.split(":");
        if(st_mode == 'pm'){
                st_timespl = (st_tmsp[0] < 12 ) ? parseInt(st_tmsp[0]) + 12 : 12;
        }else{
                st_timespl = (st_tmsp[0] == 12 ) ? "00" : st_tmsp[0];
        }
        var st_time_minute = (parseInt(st_timespl)*60)+parseInt(st_tmsp[1]);
        
        /*end time*/
        var en_timespl = '';
        var en_mode = (en_time.indexOf('pm') > -1) ? 'pm' : 'am';
        en_time = (en_time.indexOf('pm') > -1) ? en_time.replace('pm','') : en_time.replace('am','');
        var en_tmsp = en_time.split(":");

        if(en_mode == 'pm'){
            en_timespl = (en_tmsp[0] < 12 ) ? parseInt(en_tmsp[0]) + 12 : 12;
        }else{
            en_timespl = (en_tmsp[0] == 12 ) ? "00" : en_tmsp[0];
        }
        var en_time_minute = (parseInt(en_timespl)*60)+parseInt(en_tmsp[1]);

        if( st_time_minute <= en_time_minute){

        }else{
            //adding 24 hr to end time
            en_time_minute = en_time_minute+1440;
        }
        var spend_duration = en_time_minute-st_time_minute;


        /*diffinmins = spend_duration ;
        diffhours = Math.floor(diffinmins/60);
        diffmins = (diffinmins%60);
        var final_spend = (diffhours)+':'+(diffmins>9?diffmins:'0'+diffmins);
        $('.tl_spend_hour').val(final_spend);*/
        return spend_duration;
    },
    convertToMin:function(s_time){
        var r_time = 0;
        if(s_time.indexOf('.')>'-1'){
                r_time = s_time*60;
        }else if(s_time.indexOf(':')>'-1'){
            var sp_time = s_time.split(':');
            r_time = parseInt(sp_time[0]*60)+parseInt(sp_time[1]!=''?sp_time[1]:0);
        }else{
            r_time=s_time;
        }
        return r_time;
    }
};

/*author: GKM
 * used to update task log time details for time log popup
 */
function project_timelog_details(task_id){
    var prjunid = '' ;
    if(typeof arguments[1] != 'undefined' && typeof arguments[1] != ''){
        prjunid = arguments[1];
    }else{
        prjunid =  $('#projFil').val();
    }
    var params = {prjunid:prjunid,tskid:(typeof task_id != 'undefined' ? task_id : '')};
    $.ajax({
        url:HTTP_ROOT+"project_time_details",
        data:params,
        method:'post',
        dataType:'json',
        success:function(response){
            //console.log(response)
            $('#logtime_total').html(format_time_hr_min(response.total_spent));
            $('#logtime_billable').html(format_time_hr_min(response.billable_hours));
            $('#logtime_nonbillable').html(format_time_hr_min(response.nonBillableHrs));
            $('#logtime_estimated').html(format_time_hr_min(response.total_estimated));
        }
	});
}
function logpagging(page){
	var start = $('#logstrtdt').val();
	var end = $('#logenddt').val();
	var userid = $('#rsrclog').val();
	var prjunid = $('#projFil').val();
	$('#pagingtable').remove();
	$.post(HTTP_ROOT+"timelog",{'projFil':prjunid,'page':'log', 'casePage':page},function(data) {
		if(data) {
			$('.timelog-detail-tbl').html('');
			$('.main-container-div').html(data);
			$('#timelogloader').hide();
			$('#logstrtdt').val(start);
			$('#logenddt').val(end);
			$('#rsrclog').val(userid);
                        $('[rel=tooltip]').tipsy({
                            gravity:'s',
                            fade:true
                        });
			}
	});
}
/* function openPopup(role) { 
    $('#popup_bg_main').removeClass('popup_bg_main');
    $(".popup_overlay").css({
        display:"block"
    });
    if(role == 'log'){ 
    	 $(".popup_bg").css({
		display:"block",
		"width":'790px'
    	});
    }else{
	    $(".popup_bg").css({
		display:"block",
		"width":'546px'
	    });
	}
    $(".popup_form").css({
        "margin-top": "20px"
    });
    $(".loader_dv").show();
} */
	
	function updatetime(countr){
		var mn = parseInt($('#tsmn'+countr).val());
		if($.trim(mn) == "NaN"){
			mn = parseInt(0);
			$('#tsmn'+countr).val('0:00');
		}
		var hr = parseInt($('#tshr'+countr).val());
		if($.trim(hr) == "NaN"){
			hr = parseInt(0);
			//$('#tshr'+countr).val('00:00');
		}
		var logdt = $('#workeddt'+countr).val();
		var time2 = $('#endtime'+countr).val();
		if(time2.indexOf('pm') > -1){
			//var time2 = $('#endtime'+countr).val();
			time2 = time2.replace('pm','');
			var tmsp = time2.split(":");
			var timespl = parseInt(tmsp[0]) + 12;
			var timesplit2 = timespl+":"+tmsp[1];
		}else{
		 var timesplit2 = time2.replace('am','');
		}
		var enddt = logdt + " "+timesplit2;	
		var d= new Date(enddt);
		if(d.getHours() < hr){
			var d1 = new Date(logdt+" 00:00");
			$('#strttime'+countr).timepicker('setTime', d1);
			updatehrs(countr);
			
		}else{
			d.setMinutes(d.getMinutes() - mn);
			d.setHours(d.getHours() - hr);
			$('#strttime'+countr).timepicker('setTime', d);
		}
                
	}
	function stvariables(){
	        if($('#tsksid').val() == ""){
	        	showTopErrSucc('error',_('Please select a task'));
	       		$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	        	return false;
	        }
	       var x=0;
	       var y = 0;
	       var z=0;
	       var zro1 = 0;
	       var zro2 = 0;
	        $( "select[id^='whosassign']" ).each(function( index ) {
  			if($(this).val() == ""){
	        		x=1;
	        	}
		});
		 if(x==1){
	        	showTopErrSucc('error',_('Please select user'));
	        	$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	        	return false;
	        }
	        
	        var x = '';
	        
		 if(y==1){
	        	showTopErrSucc('error',_("End Time can't be earlier than Start Time"));
	        	$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	        	return false;closetskPopup
	        }
	        
	        $( "input[id^='tsmn']" ).each(function( index ) {
  			if(parseInt($(this).val()) < 0){
	        		z=1;
	        	}
	        	if(!(/^[0-9]([0-9])?$/.test($(this).val()))){
	        		//z=1;
	        	}
	        	//if($.trim($(this).val()) == '0:00'){
	        	if($.trim($(this).val()) == ''){
	        		zro2=1;
	        	}
		});
                
		 if(z==1){
	        	showTopErrSucc('error',_("End Time can't be earlier than Start Time"));
	        	$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	        	return false;
	        }
	        if(zro2 == 1){
	        	showTopErrSucc('error',_("Please select start and end time"));
	        	$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	        	return false;
	        }
                //return false;
                var chkstr = 0;
                var invalidduration = false;
	        $( "ul[id^='ul_timelog']" ).each(function( index ) {
			var x = $(this).attr('id');
			var ids = x.substr(2,4);
			var str1 = $('#whosassign'+ids).val()+" "+$('#workeddt'+ids).val()+" "+$('#strttime'+ids).val()+" "+$('#endtime'+ids).val();
			$( "ul[id^='ul_timelog']" ).each(function( index ) {
				var y = $(this).attr('id');
				var idy = y.substr(2,4);
				if(idy != ids){
					var str2 = $('#whosassign'+idy).val()+" "+$('#workeddt'+idy).val()+" "+$('#strttime'+idy).val()+" "+$('#endtime'+idy).val();
					if(str1 == str2){
		       				chkstr = 1;
						return false;
					}
				}
			});
			if(chkstr ==1){
				return false;
			}
                        
                        /*check for break time < spend time*/
                        $ul = $(this);
                        var total_br_min = calulate_break_minute($ul);
                        var total_sp_min = calulate_spend_minute($ul);
                        //console.log(total_sp_min+' < '+total_br_min+' >> '+(total_sp_min<total_br_min));
                        if(total_sp_min<total_br_min){
                            $ul.find('.totalbreak').focus();
                            invalidduration = true;
                            return false;
                        }
		});
		if(invalidduration){
                    showTopErrSucc('error',_('Break time can not exceed the total spent hours.'));
                    return false;
                }
                
		if(chkstr ==1){
			showTopErrSucc('error',_('Duplicate data found'));
	        	$('#lgquickloading').hide();
	       		$('#lgtimebtn').show();
	       		return false;
		}  
		$('#prjsid').val() == '' ? $('#prjsid').val($('#projFil').val()): "";
		var billindx = "";
		$( ".billablecls" ).each(function( index ) {
			if($(this).prop("checked") == true){
                		billindx += index+",";
            		}
		});
		$('#chked_ids').val(billindx);
                $stay = false;
                if(getHash().indexOf('details/')!='-1' && PAGE_NAME == 'dashboard'){
                    $stay = true;
                }
                //if($stay){
                    ajax_log_form_submit();
                    return false; 			
                //}
                //$('#lgtimebtn').hide();
                //$('#lgquickloading').show();
	}
	function  ajax_log_form_submit(){
            $('#lgtimebtn').hide();
			$('#pagingtable').remove();
            $('#lgquickloading').show();
            $('#page_type').val('details');
            $.ajax({
                url:$('#lgtimebtn').closest('form').attr('action'),
                data:$('#lgtimebtn').closest('form').serialize(),
                method:'post',
                dataType:'json',
                success:function(response){
                    if(response.success == 'No'){
                        $('#lgtimebtn').show();
                        $('#lgquickloading').hide();
                        var html = '';
                        var users_arr = new Array();
                        $('#whosassign1').find('option').each(function(){
                            users_arr[$(this).val()]=$(this).html();
                        });
						if(typeof response.dependerr != 'undefined'){
							showTopErrSucc('error',response.dependerr);
						}else{
                            $.each(response.data,function(index,value){
                                $.each(value,function(index1,value2){
                                    html += users_arr[value2.user_id]+" on "+value2.task_date+" from "+value2.start_time+" to "+value2.end_time+" ";
                                    html +="<br/>";
                                });

                            });
                            showTopErrSucc('error',_('Time Log value overlapping for following users')+':<br/>'+html);
						}                        
                        return false;
                    }
                    if(response.success){
                        closetskPopup();
                        $('#lgtimebtn').show();
                        $('#lgquickloading').hide();
                        var params = parseUrlHash(urlHash);
                        if(params[0] == 'tasks'){
                            easycase.refreshTaskList();
                        }else if(params[0] == 'details'){
                           easycase.ajaxCaseDetails(params[1]);
                        }else if(params[0] == 'calendar' && PAGE_NAME == 'time_log'){
                           $("#calendar_btn").trigger("click");
                        }else{
                        window.location = HTTP_ROOT+"timelog";
                    }
                }
                }
            });
        }
	function updatehrs(countr){
		var logdt = $('#workeddt'+countr).val();
		var st_time = $('#strttime'+countr).val();
                var st_tmsp = '0';
                var st_timespl= '0';
                var st_timesplit='0';
		
                var st_mode = (st_time.indexOf('pm') > -1) ? 'pm' : 'am';
                st_time = (st_time.indexOf('pm') > -1) ? st_time.replace('pm','') : st_time.replace('am','');
                st_tmsp = st_time.split(":");
                if(st_mode == 'pm'){
                        st_timespl = (st_tmsp[0] < 12 ) ? parseInt(st_tmsp[0]) + 12 : 12;
		}else{
                        st_timespl = (st_tmsp[0] == 12 ) ? "00" : st_tmsp[0];
		}
                st_timesplit = st_timespl+":"+st_tmsp[1];
                st_time_minute = (parseInt(st_timespl)*60)+parseInt(st_tmsp[1]);
                        
                
		var en_time = $('#endtime'+countr).val();
                var en_tmsp = '';
                var en_timesplit = '';
                var en_timespl = '';
                var en_mode = (en_time.indexOf('pm') > -1) ? 'pm' : 'am';
                en_time = (en_time.indexOf('pm') > -1) ? en_time.replace('pm','') : en_time.replace('am','');
                en_tmsp = en_time.split(":");
                
                if(en_mode == 'pm'){
                    en_timespl = (en_tmsp[0] < 12 ) ? parseInt(en_tmsp[0]) + 12 : 12;
		}else{
                    en_timespl = (en_tmsp[0] == 12 ) ? "00" : en_tmsp[0];
		}
                en_timesplit = en_timespl+":"+en_tmsp[1];
                var en_time_minute = (parseInt(en_timespl)*60)+parseInt(en_tmsp[1]);
                
                if( st_time_minute <= en_time_minute){
                    
                }else{
                    //adding 24 hr to end time
                    en_time_minute = en_time_minute+1440;
                }
                var spend_duration = en_time_minute-st_time_minute;
                //console.log(en_time_minute+' >> '+st_time_minute+' >> '+spend_duration);
		/*var stdt = logdt + " "+st_timesplit;
		var enddt = logdt + " "+en_timesplit;	
		var d2 = new Date(enddt);
		var d1 = new Date(stdt);
		diffinmins = ((d2-d1)/60000) ;*/
                
		diffinmins = spend_duration ;
		diffhours = Math.floor(diffinmins/60);
		diffmins = (diffinmins%60);
		//$('#tshr'+countr).val();
                var final_spend = (diffhours)+':'+(diffmins>9?diffmins:'0'+diffmins);
		$('#tsmn'+countr).val(final_spend);
                calculate_break($('#ul_timelog'+countr));
	}
	
	var cntr = 1;
	var clone = '';
        var trigger_blur = true;
	$(document).ready(function(){
                clone = $("#ul_timelog1").clone();
                $(document).on('change',"#tsksid",function () {
                    project_timelog_details($(this).val())
                });
                $(document).on('mousedown',function (e) {
                    //console.log($(e.target));
                    //console.log($(e.target).closest('.plus-btn').size());
                    trigger_blur = ($(e.target).closest('.append-new-row,.crsid,.log-btn,.popup_title').size()>0)?false:true;
                });
                $(document).on('blur',".totalbreak",function (e) {
                    
                    //calculate_break($(this).closest('ul'));
                    //console.log($(this).closest('ul').attr('id').replace(/\D+/g,''));
                    //console.log(trigger_blur);
                    if(trigger_blur){
                    updatehrs($(this).closest('ul').attr('id').replace(/\D+/g,''));
                    }
                });
                $("#tskdesc").keyup(function (e) {
                    autoheight(this);
                });              


                if(getHash().indexOf('details/')!='-1' && PAGE_NAME == 'dashboard'){
                    //$("#tsksid").attr('disabled',true);
                }


                $("#workeddt"+cntr).datepicker({
                        dateFormat: 'M d, yy',
                        //maxDate:'+0d',
                        onSelect: function(dateText, inst) {
                            var dt = new Date();
                            $id = ($(this).attr('id').replace(/\D+/g,''));
                            if(new Date(dateText).toString() === new Date(dt.getFullYear(),dt.getMonth(),dt.getDate(),0,0,0,0).toString()){
                                //$('#endtime'+$id).timepicker('setTime', dt);
                                //dt.setMinutes(dt.getMinutes() - 30);
                                //$('#strttime'+$id).timepicker('setTime', dt);
                                //$('#endtime'+$id).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                                //$('#strttime'+$id).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                            }else{
                                //$('#endtime'+$id).timepicker('option',{'minTime':$('#strttime'+cntr).val(), 'maxTime':'11:55pm' });
                                //$('#strttime'+$id).timepicker('option',{'minTime':'12:00am', 'maxTime':$('#endtime'+cntr).val() });
                            }
                        }
                });
                var d= new Date();

                $('#strttime'+cntr).timepicker({
                        'minTime': '12:00am',
                        'step': '5',
                        'forceRoundTime': true,
                        'useSelect':true,
                        'maxTime':'11:59pm',
                });
                $('#endtime'+cntr).timepicker({
                        'minTime': '12:00am',
                        'step': '5',
                        'forceRoundTime': true,
                        'useSelect':true,
                        'maxTime':'11:59pm',
                });
                $('#endtime'+cntr).timepicker('setTime', d);
                d.setMinutes(d.getMinutes() - 30);
                $('#strttime'+cntr).timepicker('setTime', d);

                //$('#endtime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                //$('#strttime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());


                $('#endtime'+cntr).on('changeTime', function() {
                        //$('#strttime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                });
                $('#strttime'+cntr).on('changeTime', function() {
                        //$('#endtime'+cntr).timepicker('option', 'minTime', $('#strttime'+cntr).val());
                });
                //$('#endtime'+cntr).timepicker('option', 'minTime', $('#strttime'+cntr).val());
                updatetime(1);
				var proj_id = $('#projFil').val();
				//footer_update(proj_id);
	});
	function appendnewrow(){
                //clone.find('label').html('').remove();
                var nclone = clone.clone();  ;
                nclone.find('.margin52').addClass('margin15').removeClass('margin52');
                nclone.find('.ui-timepicker-select').remove();
                nclone.find('.ui-timepicker-input').removeClass('ui-timepicker-input');
                nclone.find('.hasDatepicker').removeClass('hasDatepicker');
                
	        cntr++;
                
                nclone.attr('id','ul_timelog'+cntr)
                nclone.find('input,select,li').each(function(){
                    var type = this.type || this.tagName.toLowerCase();
                    
                    var oldid = $(this).attr('id');
                    if(typeof oldid !='undefined'){
                        var newid = oldid.replace(/\d+/,cntr); 
                        $(this).attr('id',newid);
                    }
                    if($(this).hasClass('updatehrs')){
                        $(this).attr({onchange:"updatehrs("+cntr+")"});
                    }
                    if(type == 'select-one'){
                        //console.log($(this).val());
                    }
                    if(type == 'text'){
                        $(this).val('');
                    }
                });
	        var cdate = "<?php echo date('M d, Y',strtotime('now')); ?>";
                
		$('.log-time').append(nclone);
                $("#crsid"+cntr).find('a').attr('onclick','removeUI('+cntr+');')
		$("#workeddt"+cntr).datepicker({
			dateFormat: 'M d, yy',
                        //maxDate:'+0d',
                        setDate: new Date(),
			onSelect: function(dateText, inst) { 
                            $id = ($(this).attr('id').replace(/\D+/g,''));
                            var dt = new Date();
                            if(new Date(dateText).toString() === new Date(dt.getFullYear(),dt.getMonth(),dt.getDate(),0,0,0,0).toString()){
                                //$('#endtime'+$id).timepicker('setTime', dt);
                                //dt.setMinutes(dt.getMinutes() - 30);
                                //$('#strttime'+$id).timepicker('setTime', dt);
                                //$('#endtime'+$id).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                                //$('#strttime'+$id).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                            }else{
                                //$('#endtime'+$id).timepicker('option',{'minTime':$('#strttime'+cntr).val(), 'maxTime':'11:55pm' });
                                //$('#strttime'+$id).timepicker('option',{'minTime':'12:00am', 'maxTime':$('#endtime'+cntr).val() });
                            }
                        }
		});
                $("#workeddt"+cntr).datepicker("setDate", new Date() );
                $("#tsmn"+cntr).val("0:30");
                $("#is_billable"+cntr).val(cntr);
		var d= new Date();
                
		$('#strttime'+cntr).timepicker({
			'minTime': '12:00am',
			'step': '5',
			'forceRoundTime': true,
			'useSelect':true,
                        'maxTime':'11:59pm',			
		});
		$('#endtime'+cntr).timepicker({
			'minTime': '12:00am',
			'step': '5',
			'forceRoundTime': true,
			'useSelect':true,
			'maxTime':'11:59pm',
		});
	        $('#endtime'+cntr).timepicker('setTime', d);
		d.setMinutes(d.getMinutes() - 30);
		$('#strttime'+cntr).timepicker('setTime', d);
		
		//$('#endtime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
		//$('#strttime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
                
		var d= new Date();
		
		$('#endtime'+cntr).on('changeTime', function() {
			//$('#strttime'+cntr).timepicker('option', 'maxTime', $('#endtime'+cntr).val());
		});
		$('#strttime'+cntr).on('changeTime', function() {
			//$('#endtime'+cntr).timepicker('option', 'minTime', $('#strttime'+cntr).val());
		});
		var usrhtml = "<option value=''>Select User</option>";
		//if(rsrch == ""){
			$.each(PUSERS, function(key, val){
			$.each(val, function(k1, v1){
				var usrid = v1['User']['id'];
				usrhtml += "<option value='"+usrid+"'>"+v1['User']['name']+"</option>";
			});
			});
		//}else{
			//usrhtml = rsrch;
		//}
		$('#whosassign'+cntr).html(usrhtml);
		$('#whosassign'+cntr).val(SES_ID);
                
                if($("ul[id^='ul_timelog']").length < 2){
                   $(".crsid").hide();
		}else{
                    $(".crsid").show();
                }
		
	}
	
	function removeUI(countr){
		$( "#ul_timelog"+countr ).remove();
                if($("ul[id^='ul_timelog']").length < 2){
                    $(".crsid").hide();
		}else{
                    $(".crsid").show();                    
                }
	}
	
	function displayblock(showid,hiddenid){
		$('#'+showid).show();
		$('#'+hiddenid).hide();
	}
        function autoheight(a) {
            if($(a).val() !=''){
                if (!$(a).prop('scrollTop')) {
                    do {
                        var b = $(a).prop('scrollHeight');
                        var h = $(a).height();
                        $(a).height(h - 30);
                    }
                    while (b && (b != $(a).prop('scrollHeight')));
                }
                $(a).height($(a).prop('scrollHeight'));
            }else{
                $(a).css('height',32);
            }
        }
        function calculate_break($ul){
            $ul.find('.totalbreak').val($ul.find('.totalbreak').val().replace(/[^\d\:\.]+/g,''));
            var break_time = $.trim($ul.find('.totalbreak').val())!=''?$.trim($ul.find('.totalbreak').val().replace('-','')):'0';
            var spend_time = $.trim($ul.find('.totalduration').val());
            //console.log(break_time+" >> "+spend_time);
            var br_hr = '00';
            var br_min = '00';
            var br_time = '';
            var extra_hr = '';
            if(break_time.indexOf('.')>'-1'){
                /*converting to minute*/
                br_time = isNaN(break_time) ? 0 : break_time*60;
                br_hr = Math.floor(br_time/60);
                br_min = Math.floor(br_time%60);
            }else if(break_time.indexOf(':')>'-1'){
                br_time = break_time.split(':');
                extra_hr = (br_time[1] == '') ? 0 : Math.floor(parseInt(br_time[1])/60);
                //br_hr = parseInt(br_time[0])+parseInt(extra_hr);                
                br_hr = (br_time[0] == '') ? 0 : (parseInt(br_time[0])+parseInt(extra_hr));
                br_min = (br_time[1] == '') ? 0 : Math.floor(br_time[1]%60);
            }else{
                br_hr = break_time;                
                br_min = '0';
            }
            
            var sp_time = spend_time.split(':');
            var total_sp_min = (parseInt(sp_time[0])*60)+parseInt(sp_time[1]);
            var total_br_min = (parseInt(br_hr)*60)+parseInt(br_min);
            //console.log(total_sp_min+' < '+total_br_min);
            var spend_duration = total_sp_min-total_br_min;
            var sp_hr = Math.floor(spend_duration/60);
            var sp_min = Math.floor(spend_duration%60);
            /*if final spent is greater than zero then it will place the value else will make it empty*/
            var final_sp = parseInt(sp_hr)>0 || parseInt(sp_min) > 0 ? parseInt(sp_hr)+':'+(sp_min<10?"0":"")+sp_min : '0:00';
            if(total_sp_min<total_br_min){
                showTopErrSucc('error',_('Break time can not exceed the total spent hours.'));
                return false;
            }
            /*if final break is greater than zero then it will place the value else will make it empty*/
            var final_br = parseInt(br_hr)>0 || parseInt(br_min) > 0 ? parseInt(br_hr) + ':' + (br_min<10?"0":"") +br_min:'';
            /*assigning values to time spent and break time fields */
            $ul.find('.totalduration').val($ul.find('.totalduration').val()!='' && final_sp==''?$ul.find('.totalduration').val():final_sp);
            $ul.find('.totalbreak').val(final_br);
        }
        
        function calulate_break_minute($ul){
            var break_time = $ul.find('.totalbreak').val();
            var br_hr = '00';
            var br_min = '00';
            var br_time = '';
            var extra_hr = '';
            if(break_time.indexOf('.')>'-1'){
                br_time = break_time*60;
                br_hr = Math.floor(br_time/60);
                br_min = Math.floor(br_time%60);
            }else if(break_time.indexOf(':')>'-1'){
                br_time = break_time.split(':');
                extra_hr = Math.floor(parseInt(br_time[1])/60);
                br_hr = parseInt((br_time[0]=='')?0:br_time[0])+parseInt(extra_hr);                
                br_min = Math.floor(br_time[1]%60);
            }else{
                br_hr = break_time;                
                br_min = '0';
            }

            var total_br_min = (parseInt(br_hr)*60)+parseInt(br_min);
            return total_br_min;
        }
        function calulate_spend_minute($ul){
            /*var spend_time = $ul.find('.totalduration').val();                        
            var sp_time = spend_time.split(':');
            var total_sp_min = (parseInt(sp_time[0])*60)+parseInt(sp_time[1]);
            return total_sp_min;*/
            $id = ($ul.attr('id').replace(/\D+/g,''));
            var st_time = $('#strttime'+$id).val();
            var st_tmsp = '0';
            var st_timespl= '0';
            var st_timesplit='0';

            var st_mode = (st_time.indexOf('pm') > -1) ? 'pm' : 'am';
            st_time = (st_time.indexOf('pm') > -1) ? st_time.replace('pm','') : st_time.replace('am','');
            st_tmsp = st_time.split(":");
            if(st_mode == 'pm'){
                    st_timespl = (st_tmsp[0] < 12 ) ? parseInt(st_tmsp[0]) + 12 : 12;
            }else{
                    st_timespl = (st_tmsp[0] == 12 ) ? "00" : st_tmsp[0];
            }
            st_timesplit = st_timespl+":"+st_tmsp[1];
            st_time_minute = (parseInt(st_timespl)*60)+parseInt(st_tmsp[1]);


            var en_time = $('#endtime'+$id).val();
            var en_tmsp = '';
            var en_timespl = '';
            var en_mode = (en_time.indexOf('pm') > -1) ? 'pm' : 'am';
            en_time = (en_time.indexOf('pm') > -1) ? en_time.replace('pm','') : en_time.replace('am','');
            en_tmsp = en_time.split(":");

            if(en_mode == 'pm'){
                en_timespl = (en_tmsp[0] < 12 ) ? parseInt(en_tmsp[0]) + 12 : 12;
            }else{
                en_timespl = (en_tmsp[0] == 12 ) ? "00" : en_tmsp[0];
            }
            var en_time_minute = (parseInt(en_timespl)*60)+parseInt(en_tmsp[1]);

            if( st_time_minute <= en_time_minute){

            }else{
                //adding 24 hr to end time
                en_time_minute = en_time_minute+1440;
            }
            var spend_duration = en_time_minute-st_time_minute;


            /*diffinmins = spend_duration ;
            diffhours = Math.floor(diffinmins/60);
            diffmins = (diffinmins%60);
            var final_spend = (diffhours)+':'+(diffmins>9?diffmins:'0'+diffmins);*/
            return spend_duration;
        }
		/*Author: GKM
 * it is used to show time formats
 * */
function format_time_hr_min(secs){
    var hrs = Math.floor(secs/3600)>0 ? Math.floor(secs/3600)+' hr'+(Math.floor(secs/3600)>1?'s':'')+' ' : '';
    var mins = Math.round((secs%3600)/60) > 0 ? Math.round((secs%3600)/60)+' min'+(Math.round((secs%3600)/60)>1?'s':'')+'' : '';
    return hrs!='' || mins!='' ? hrs+mins : '---';
}

function ajax_timelog_export_csv(){
    
    var projuniqid= ($('#projFil').val() != 'undefined')? $('#projFil').val() : '';
    var usrid = ($('#flt_resource').val()!= 'undefined')? $('#flt_resource').val() : '' ;
    var strddt = ($('#flt_start_date').val()!= 'undefined')? $('#flt_start_date').val() : '' ;
    var enddt = ($('#flt_end_date').val()!= 'undefined')? $('#flt_end_date').val() : '' ;
    var url_params = 'projuniqid='+projuniqid+'&usrid='+usrid+'&strddt='+strddt+'&enddt='+enddt;
    if(TPAY ==1 || GTLG == 1)
	if($('#unpaid_list').length){
		if($('#unpaid_list').is(':checked')){
			url_params += '&ispaid=unpaid';
		}else if($('#paid_list').is(':checked')){
			url_params += '&ispaid=paid';
		}else{
			url_params += '&ispaid=both';
		}
	}
    var url = HTTP_ROOT+"export_csv_timelog?"+url_params;
    window.open(url,'_blank');
    return false;
    var params = {'projuniqid':$('#projFil').val(),'usrid':$('#rsrclog').val(),'strddt':$('#logstrtdt').val(),'enddt':$('#logenddt').val()};
    $.post(HTTP_ROOT+"log_times/export_csv_timelog",params,function(data) {
    if(data){
    $('#caseLoader').hide();
      }
    });
}
/* Timer Coding Starts */
function expandTimer(){
    if(!$('.timer-detail').is(':visible')) {
        $('.timer-header').animate({
            'bottom':'200px',
            'left':'175px'
        },function(){
            $('.timer-header .open-activity').removeClass('up').addClass('down');
            $('.timer-detail').show();
        });
    } else {
        $('.timer-detail').hide();
        $('.timer-div').css({'background':'none'});
        $('.timer-header').animate({
            'bottom':'0px',
            'left':'175px'
        },function(){
            $('.timer-header .open-activity').removeClass('down').addClass('up');
        });
    }
}
var prj_slct, $prj_slct;
var tsk_slct, $tsk_slct;
function openTimer(){
    if(timer_interval){
        showTopErrSucc('error', _('Timer is already running.'));return false;
    }
    $('#save-tm-span').hide();
    $('#timerquickloading').hide(); 
    $('#start-tm-span').show();
    $('#cancel-timer-btn').show();
    var strURL = HTTP_ROOT + "users/project_menu";
    $('.timer-pause-btn, .timer-play-btn').hide();
    $('.timer-div').show();
    $('.timer-time').text('00 : 00 : 00');
    $('#timerdesc').val('');
    $.post(strURL, {
        "page": 'timer',
        "limit": 'all',
        "filter": ''
    }, function (data) {
        if (data) {
            typeof prj_slct != 'undefined' ? prj_slct.destroy(): '';
            $('#select-timer-proj').html(data);
            $('#select-timer-proj').val($('#projFil').val());
            $('#timer_hidden_proj_id').val($('#select-timer-proj').val());
            $('#timer_hidden_proj_nm').val($('#select-timer-proj option:selected').html());
            $prj_slct = $('#select-timer-proj').selectize({
                allowEmptyOption: true,
                onChange: function(value) {
                    if (!value.length) return;
                    tsk_slct.clear();
                    $('#timer_hidden_proj_id').val(value);
                    $('#timer_hidden_proj_nm').val($('#select-timer-proj option:selected').html());
                    getTasksofProject(value);
                }
            });
            prj_slct = $prj_slct[0].selectize;
            prj_slct.enable();
            getTasksofProject($('#projFil').val());
            $('.timer-header').animate({
                'bottom':'200px',
                'left':'175px'
            },function(){
                $('.timer-header .open-activity').removeClass('up').addClass('down');
                $('.timer-detail').show();
            });
        }
    });
}
function getTasksofProject(puid){
    $.post(HTTP_ROOT + "existing_task", {
        'projuniqid': puid,
        'page':'timer'
    }, function (data) {
        if (data) {
            typeof tsk_slct != 'undefined' ? tsk_slct.destroy() : '';
            $('#select-timer-task').html(data);
            $tsk_slct = $('#select-timer-task').selectize({
                onChange: function(value) {
                    if (!value.length) return;
                    if(value != 0){
                        $('#save-tm-span').hide();
                        $('#start-tm-span').show();
                    }
                },
                create: function(input){
                    $.post(HTTP_ROOT + "easycases/quickTask", {
                        'title': input,
                        'project_id': $('#select-timer-proj').val(),
                        'type': 'inline',
                        'mid': ''
                    }, function (res) {
                        if (res.error) {
                            showTopErrSucc('error', res.msg);
                            return false;
                        } else {
                            showTopErrSucc('success', _('Task posted successfully.'));
                            $('#select-timer-task').append('<option value="'+res.curCaseId+'">'+input+'</option>');
                            $('#select-timer-task').val(res.curCaseId);
                            tsk_slct.addOption({value:res.curCaseId,text:input});
                            tsk_slct.addItem(res.curCaseId);
                        }
                    }, 'json');
                }
            });
            tsk_slct = $tsk_slct[0].selectize;
            tsk_slct.enable();
        }
    });
}
function startTaskTimer(){
    var caseId = $('#select-timer-task').val();
    if(caseId == 0){
        showTopErrSucc('error', _('Please select a task to start timer.'));return false;
    }
    $('#start-tm-span').hide();
    $('#save-tm-span').show();
    $('.timer-pause-btn').show();
    var caseTitle = $('#select-timer-task').find('option:selected').html();
    var projId = $('#timer_hidden_proj_id').val();
    var projNm = $('#timer_hidden_proj_nm').val();
    startTimer(caseId,caseTitle, '', projId, projNm);
}
var timer_interval;
var offset;
var clock = 0;
var now = Date.now();
var x = 0;
$(function(){
    var tmdet = getCookie('timerDtls');
    var tm = getCookie('timer');
    if(typeof tmdet != 'undefined' && tmdet != '' && typeof tm != 'undefined' && tm != ''){
        var tmCsDet = tmdet.split('|');
        var caseid = tmCsDet[0];
        var casetitle = tmCsDet[1];
        var caseuniqid = tmCsDet[2] != '' ? tmCsDet[2] : '' ;
        var prjuid = tmCsDet[3];
        var prjnm = tmCsDet[4];
        var description = getCookie('timerDescription');
        if(typeof description != 'undefined' && description != ''){
            $('#timerdesc').val(description);
        }else{
            $('#timerdesc').val('');
        }
		if(typeof prjuid == 'undefined' || prjuid == ''){
            saveTimer('old_data');
        }else{
        startTimer(caseid, casetitle, caseuniqid, prjuid, prjnm, tm);
    }
    }
});
function startTimer(caseId, caseTitle, caseUniqId, prjUid, prjnm){
    var paused = getCookie('timerPaused');
    paused = typeof paused !== 'undefined' ? paused : 0;
    if((timer_interval || paused) && !arguments[5]){
        showTopErrSucc('error', _('Timer is already running.'));return false;
    }
    $('#start-tm-span').hide();
    expandTimer();
    $('#timer_hidden_tsk_id').val(caseId);
    $('#timer_hidden_tsk_uniq_id').val(caseUniqId);
    var orgprjnm = prjnm;
    var orgcaseTitle = caseTitle;
    if(typeof prjUid != 'undefined' && prjUid != '' && typeof prjnm != 'undefined' && prjnm != ''){
        if(typeof prj_slct != 'undefined'){prj_slct.destroy();}
        if(typeof tsk_slct != 'undefined'){tsk_slct.destroy();}
        if(prjnm && prjnm.length > 30){
             prjnm = prjnm.substr(0,25);
        }
        $('#select-timer-proj').html('<option value="'+prjUid+'">'+prjnm+'</option>');
         caseTitle = unescape(caseTitle);
         if(caseTitle && caseTitle.length > 30){
            caseTitle = caseTitle.substr(0,25);
         }
        $('#select-timer-task').html('<option value="'+caseId+'">'+caseTitle+'</option>');
    }
    $('#timer_hidden_proj_id').val(prjUid);
    $prj_slct = $('#select-timer-proj').selectize({allowEmptyOption: true});
    $tsk_slct = $('#select-timer-task').selectize({allowEmptyOption: true});
    prj_slct = $prj_slct[0].selectize;
    tsk_slct = $tsk_slct[0].selectize;
    prj_slct.disable();
    tsk_slct.disable();
    $('.timer-div').find('.tsk-title').html(caseTitle);
    $('.timer-div').find('.tsk-ttl').html(caseTitle);
    $('#cancel-timer-btn').show();
    $('#save-tm-span').show();
    $('#timerquickloading').hide();
    if(caseTitle && caseTitle.length > 30){
        $('.timer-div').find('.tsk-ttl').attr({'title':$('.timer-div').find('.tsk-ttl').html(), 'rel':'tooltip'});
    }
    $('[rel=tooltip]').tipsy({gravity: 's',fade: true});
    var paused = getCookie('timerPaused');
    paused = typeof paused != 'undefined' ? paused : 0;
    if(paused == 1){
        $('.timer-play-btn').css({'display':'inline-block'});
        $('.timer-pause-btn').hide();
    }else{
        $('.timer-play-btn').hide();
        $('.timer-pause-btn').css({'display':'inline-block'});
    }
    if(typeof arguments[5] != 'undefined' && arguments[5] != ''/* && !paused*/){
        offset   = Date.now();
        update(arguments[5]);
    }/*else if(paused){
        resume();
    }*/else{
        remember_filters('timerDtls', caseId+'|'+orgcaseTitle+'|'+caseUniqId+'|'+prjUid+'|'+orgprjnm);
        remember_filters('timer', Date.now());
        $('.timer-time').text('00 : 00 : 00');
        $('#timer_is_billable').prop('checked', true);
        var description = getCookie('timerDescription');
        if(typeof description != 'undefined' && description != ''){
            $('#timerdesc').val(description);
        }else{
        $('#timerdesc').val('');
    }
    }
    if(!timer_interval && !paused){
        offset   = now = Date.now();
        timer_interval = setInterval(update, 1);
    }
    $('.timer-div').show();
    var match = (window || this).location.href.match(/#(.*)$/);
    if(match != null){
        var params = parseUrlHash(match[1]);
        if(params[0] != 'details' && caseUniqId != '' && $('#t_'+caseUniqId).length){
            $('#t_'+caseUniqId).remove();
        }
    }
}
function update(){
    now = typeof arguments[0] != 'undefined' ? arguments[0] : Date.now();
    if(typeof arguments[0] != 'undefined' && typeof arguments[1] == 'undefined'){
		var paused = getCookie('timerPaused');
		paused = typeof paused != 'undefined' ? paused : 0;
		if(paused == 1){
        clock = getCookie('timerDuration');
			clock = parseInt(clock);
    }else{
			var d = offset-now;
			var pausedTime = 0;
			var resumeTime = getCookie('timerResume');
			if(typeof resumeTime != 'undefined' && resumeTime != ''){
				var timeEnd = getCookie('timerEnd');
				pausedTime = parseInt(resumeTime) - parseInt(timeEnd);
			}
			d = d - pausedTime;
			clock+=d;
		}
    }else{
        var d = now - offset;
        offset = now;
        clock += d;
    }
    $('#timer_hidden_duration').val(clock);
    remember_filters('timerDuration', clock);
    var milliseconds = parseInt((clock%1000)/100),
        seconds = parseInt((clock/1000)%60),
        minutes = parseInt((clock/(1000*60))%60),
        hours = parseInt((clock/(1000*60*60))%24);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;
    $('.timer-time').text(hours + " : " + minutes + " : " + seconds);
}
function pauseTimer(e){
    if(e){
        e.stopPropagation();
    }
    if (timer_interval) {
        clearInterval(timer_interval);
        timer_interval = null;
        remember_filters('timerPaused', 1);
        remember_filters('timerEnd', Date.now());
    }
    $('.timer-pause-btn').hide();
    $('.timer-play-btn').css({'display':'inline-block'});
}
function resumeTimer(e){
    if(e){
        e.stopPropagation();
    }
    if(!timer_interval){
        timer_interval = setInterval(resume, 1000);
        remember_filters('timerPaused', 0);
        remember_filters('timerResume', Date.now());
    }
    $('.timer-play-btn').hide();
    $('.timer-pause-btn').css({'display':'inline-block'});
}
function resume(){
    clock = getCookie('timerDuration');
    var d = parseInt(clock);
    d = d+1000;
    $('#timer_hidden_duration').val(d);
    remember_filters('timerDuration', d);
    var milliseconds = parseInt((d%1000)/100),
        seconds = parseInt((d/1000)%60),
        minutes = parseInt((d/(1000*60))%60),
        hours = parseInt((d/(1000*60*60))%24);

    hours = (hours < 10) ? "0" + hours : hours;
    minutes = (minutes < 10) ? "0" + minutes : minutes;
    seconds = (seconds < 10) ? "0" + seconds : seconds;
    $('.timer-time').text(hours + " : " + minutes + " : " + seconds);
}
function setDescription(){
    var description = $('#timerdesc').val();
    remember_filters('timerDescription', description);
}
function saveTimer(){
    if(typeof getCookie('timerDtls') == 'undefined' || typeof getCookie('timer') == 'undefined' || typeof getCookie('timerDuration') == 'undefined'){
        stopTimer();
        showTopErrSucc('error',_('The timer is expired.'));
        return false;
     }
    if($('#timer_hidden_duration').val() < 60000){
        showTopErrSucc('error',_('You can not log less than one minute.'));
        return false;
    }
    if(timer_interval) {
        clearInterval(timer_interval);
        timer_interval = null;
    }
	var old_data = '';
	if(typeof arguments[0] !='undefined'){
		old_data = arguments[0];
	}
    $('#cancel-timer-btn').hide();
    $('#save-tm-span').hide();
    $('#timerquickloading').show();
    var url = HTTP_ROOT+'saveTimer';
    var caseUniqId = $('#timer_hidden_tsk_uniq_id').val();
    var project_id = $('#timer_hidden_proj_id').val() != '' ? $('#timer_hidden_proj_id').val():$('#select-timer-proj').val();
    var task_id = $('#timer_hidden_tsk_id').val() != '' ? $('#timer_hidden_tsk_id').val():$('#select-timer-task').val();
    var params = {
        project_id : project_id,
        task_id : task_id,
        start_time : getCookie('timer'),
        totalduration : $('#timer_hidden_duration').val(),
        end_time : Date.now(),
        description: trim($('#timerdesc').val()),
        chked_ids : $('#timer_is_billable').is(':checked') ? 1:0
    };
    $.post(url,{params: params},function(data){
        if(data.status == 'success'){
			if(old_data == 'old_data'){
				stopTimer('old_data_success');
			}else{
            stopTimer();
				refreshTasks = 1;
				var match = (window || this).location.href.match(/#(.*)$/);
				if(match != null){
					var params = parseUrlHash(match[1]);
					if(params[0] != 'details' && caseUniqId != '' && $('#t_'+caseUniqId).length){
						$('#t_'+caseUniqId).remove();
					}
				}
            $('#cancel-timer-btn').show();
            $('#save-tm-span').show();
            $('#start-tm-span').hide();
            $('#timerquickloading').hide(); 
            showTopErrSucc('success',_('Timelog updated successfully.'));
            if(PAGE_NAME == 'time_log'){
                $('#projFil').val($('#select-timer-proj').val());
                window.location.reload();
            }
                                var params = parseUrlHash(urlHash);
                                if (params[0] == 'details') {
                                    easycase.ajaxCaseDetails(params[1]);
			}
			}
        }else{
            if(data.success == 'No'){
				if(old_data == 'old_Data'){
					stopTimer('old_data_overlap');
				}else{
            stopTimer();
                var html = '';
                var users_arr = new Array();
                $.each(PUSERS, function (key, val) {
                    $.each(val, function (k1, v1) {
                        users_arr[v1['User']['id']]=v1['User']['name'];
                    });
                });
				if(typeof data.dependerr != 'undefined'){
					showTopErrSucc('error',data.dependerr);
				}else{
                $.each(data.data,function(index,value){
                    $.each(value,function(index1,value2){
                        html += users_arr[value2.user_id]+" on "+value2.task_date+" from "+value2.start_time+" to "+value2.end_time+" ";
                        html +="<br/>";
                    });

                });
                showTopErrSucc('error',_('Time Log value overlapping for following users')+':<br/>'+html);
            }
        }
        }
        }
    }, 'json');
}
function stopTimer(){
    if(timer_interval) {
        clearInterval(timer_interval);
        timer_interval = null;
    }
    clock = 0;
    remember_filters('timerDtls', '');
    remember_filters('timer', '');
    remember_filters('timerEnd', '');
    remember_filters('timerDescription', '');
    remember_filters('timerPaused', 0);
    remember_filters('timerResume', '');
    $('#timer_hidden_tsk_id').val('');
    $('#timer_hidden_proj_id').val('');
    $('.timer-time').text('00:00:00');
    $('.timer-div').hide();
	if(typeof arguments[0] != 'undefined'){
		if(arguments[0] == 'old_data_success'){
			showTopErrSucc('success', _('Timer functionality is updated. So the old timer data is saved. Please start a new timer.'));
		}else if(arguments[0] == 'old_data_overlap'){
			showTopErrSucc('error', _('Timer functionality is updated. So the old timer data can not be saved due to overlap condition. Please start a new timer.'));
}
	}
}
/* Timer Ends Here */
/* Timer Filter coding Starts */
var general = {
    filterDate: function (page, filter, title, type) {
        if (page == 'timelog') {
            $("#filter_date_lbl").html(title);
            if (filter != 'custom') {
                $('#logstrtdt,#logenddt').val('');
                $(".custome_timelog").hide();
                $('#dropdown_menu_createddate').find('input[type="checkbox"]').removeAttr('checked');
                if (type == "check") {
                    if ($('#timelog_' + filter + '').is(":checked")) {
                        $('#timelog_' + filter + '').removeAttr('checked');
                    } else {
                        $('#timelog_' + filter + '').attr('checked', 'checked');
                    }
                } else {
                    if ($('#timelog_' + filter + '').is(":checked")) {
                        $('#timelog_' + filter + '').removeAttr('checked');
                    } else {
                        $('#timelog_' + filter + '').attr('checked', 'checked');
                    }
                }
            } else if (filter == 'custom') {
                $('#tlog_date').val($('#logstrtdt').val() + ':' + $('#logenddt').val());
                //ajaxTimeLogView();
            }
            $('#tlog_date').val(filter);
            showtimelog('datesrch', filter);
        }
    },
    filterResource: function (uid, type) {
        var checked = '';
        if (type == "check") {
            if ($('#res_' + uid).is(":checked")) {
                $('#res_' + uid).attr('checked', 'checked');
            } else {
                $('#res_' + uid).removeAttr('checked');
            }
        } else {
            if ($('#res_' + uid).is(":checked")) {
                $('#res_' + uid).removeAttr('checked');
            } else {
                $('#res_' + uid).attr('checked', 'checked');
            }
        }
        var filter = '';
        $('.resource_check').each(function () {
            if ($(this).is(":checked")) {
                var uid = $(this).attr('data-id');
                filter += uid + "-";
            }
        });
        $('#tlog_resource').val(filter);
        createCookie('rsrclog', filter, 365, '');
        
        showtimelog('resourcesrch', filter);
    }
};
function customdatetlog(obj) {
    $('#dropdown_menu_createddate').find('input[type="checkbox"]').prop('checked', false);
    $('.custome_timelog').is(':visible') ? $(obj).find('#timelog_custom').prop("checked", false): $(obj).find('#timelog_custom').prop("checked", true);
    $('.custome_timelog').toggle();
    $('.custome_timelog').closest('ul').scrollTop(200);
}
function hidereplytimelog() {
    createCookie("SHOWTIMELOG", 'No', 365, DOMAIN_COOKIE);
    $(".tl-msg-header").slideUp();
    $('.hidetablelog').slideUp("slow", function () {
        $('.showreplylog').show();
        $(".tl-msg-btn").find('.logmore-btn').hide();
        $(".tl-msg-box").addClass('slideup');
        $('.detail_timelog_header').show();
    });
}

function showreplytimelog() {
    createCookie("SHOWTIMELOG", 'Yes', 365, DOMAIN_COOKIE);
    $('.showreplylog').hide();
    $('.detail_timelog_header').hide();
    $(".tl-msg-header").slideDown();
    $('.hidetablelog').slideDown("slow", function () {
        $(".tl-msg-btn").find('.logmore-btn').show();
    });
}

/* BY CHP
 * */

 function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '')
          .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
          prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
          sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
          dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
          s = '',
          toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
              .toFixed(prec);
          };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
          .split('.');
        if (s[0].length > 3) {
          s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
          .length < prec) {
          s[1] = s[1] || '';
          s[1] += new Array(prec - s[1].length + 1)
            .join('0');
        }
        return s.join(dec);
      }
      
  /* By:CHP
   * time log research filter */
  function filterByResource(obj){
      var user_id = $(obj).val();
             createCookie('rsrclog', '', 365, '');
      $('#tlog_resource').val(user_id);
      showtimelog('resourcesrch', user_id);
  }