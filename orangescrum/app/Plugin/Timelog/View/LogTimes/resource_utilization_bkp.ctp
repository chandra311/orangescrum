<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.bootgrid.fa.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>jquery.bootgrid.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_ROOT; ?>css/jquery.bootgrid.min.css" />
<div class="proj_grids glide_div" id="main_con_task">
<div class="proj_grids glide_div setting_wrapper task_listing resource_sec cmn_tbl_widspace" id="resource_utilization_div">	
	<?php  echo $this->element('analytics_header'); ?>
    <div class="resourceUtilization">
    <div class="fl case-filter-menu " data-toggle="dropdown" rel="tooltip" title="Filter" onclick="openfilter_popup(0,'dropdown_menu_all_filters');" style="display:none">
        <button type="button" class="btn tsk-menu-filter-btn flt-txt">
                <i class="icon_flt_img"></i>
                <?php echo __("Filters"); ?>
                <i class="icon-filter-right"></i>
        </button>
        <ul class="dropdown-menu" id="dropdown_menu_all_filters" style="position: absolute;">
            <li class="pop_arrow_new"></li>
            <li>
                <a href="javascript:jsVoid();" title="Date" data-toggle="dropdown" onclick="allfiltervalue('utilization', event);"> <?php echo __("Date"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_utilization_div">
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_utilization">
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_today" data-id="today" <?php if($_COOKIE['utilization_date_filter'] == 'today'){ echo "checked"; } ?> onclick="utilization('today', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('today','text');" style="cursor:pointer;">
                             <?php echo __("Today"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_yesterday" data-id="yesterday" <?php if($_COOKIE['utilization_date_filter'] == 'yesterday'){ echo "checked"; } ?> onclick="utilization('yesterday', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('yesterday','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Yesterday"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_thisweek" data-id="thisweek" <?php if($_COOKIE['utilization_date_filter'] == 'thisweek'){ echo "checked"; } ?> onclick="utilization('thisweek', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('thisweek','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("This Week"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_thismonth" data-id="thismonth" <?php if($_COOKIE['utilization_date_filter'] == 'thismonth'){ echo "checked"; } ?> onclick="utilization('thismonth', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('thismonth','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("This Month"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_thisquarter" data-id="thisquarter" <?php if($_COOKIE['utilization_date_filter'] == 'thisquarter'){ echo "checked"; } ?> onclick="utilization('thisquarter', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('thisquarter','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("This Quarter"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_thisyear" data-id="thisyear" <?php if($_COOKIE['utilization_date_filter'] == 'thisyear'){ echo "checked"; } ?> onclick="utilization('thisyear', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('thisyear','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("This Year"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_lastweek" data-id="lastweek" <?php if($_COOKIE['utilization_date_filter'] == 'lastweek'){ echo "checked"; } ?> onclick="utilization('lastweek', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('lastweek','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Last Week"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_lastmonth" data-id="lastmonth" <?php if($_COOKIE['utilization_date_filter'] == 'lastmonth'){ echo "checked"; } ?> onclick="utilization('lastmonth', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('lastmonth','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Last Month"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_lastquarter" data-id="lastquarter" <?php if($_COOKIE['utilization_date_filter'] == 'lastquarter'){ echo "checked"; } ?> onclick="utilization('lastquarter', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('lastquarter','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Last Quarter"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_lastyear" data-id="lastyear" <?php if($_COOKIE['utilization_date_filter'] == 'lastyear'){ echo "checked"; } ?> onclick="utilization('lastyear', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('lastyear','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Last year"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_last365days" data-id="last365days" <?php if($_COOKIE['utilization_date_filter'] == 'last365days'){ echo "checked"; } ?> onclick="utilization('last365days', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization('last365days','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Last 365 days"); ?>
                            </font></a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="utilization_custom" style="cursor:pointer" onclick="customdatetutilization();" <?php if(strpos($_COOKIE['utilization_date_filter'], ':')){ echo "checked"; } ?> />
                                <font onClick="customdatetutilization()" >&nbsp;<?php echo __("Custom Date"); ?></font>
                            </a>
                        </li>
                        <div id="custom_utilization" style="display: none;">
                            <?php $dt = explode(':', $_COOKIE['utilization_date_filter']); ?>
                            <div  class="cdate_div_cls">
                                <input type="text" id="utilizationstrtdt"  value="<?php print $dt[0]; ?>" placeholder="<?php echo __("From"); ?>" class="form-control"/><br/>
                                <input type="text" id="utilizationenddt" value="<?php print $dt[1]; ?>" placeholder="<?php echo __("To"); ?>" class="form-control" />
                            </div>
                            <div  class="cduedate_btn_div" style="text-align:center;margin-top: 5px;cursor:pointer">
                                <button class="btn btn-primary cdate_btn" style="cursor: pointer;"  onclick="utilization('custom', 'Custom');"><?php echo __("Search"); ?></button>
                            </div>
                        </div>
                    </ul>
                </div>
            </li>
            <li>
                <a href="javascript:jsVoid();" title="Due Date" data-toggle="dropdown" onclick="allfiltervalue('utilization_status', event);"> <?php echo __("Status"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_utilization_status_div">
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_utilization_status">
                        <?php $stsFil = explode('-', $_COOKIE['utilization_status_filter']); ?>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_new" data-id="1" <?php if(in_array('1', $stsFil)){ echo "checked"; } ?> onclick="utilization_status('new', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization_status('new','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("New"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_wip" data-id="2" <?php if(in_array('2', $stsFil)){ echo "checked"; } ?> onclick="utilization_status('wip', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization_status('wip','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("In Progress"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_closed" data-id="3" <?php if(in_array('3', $stsFil)){ echo "checked"; } ?> onclick="utilization_status('closed', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization_status('closed','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Closed"); ?>
                            </font></a>
                        </li>
                        <li><a href="javascript:void(0);">
                            <input type="checkbox" id="utilization_resolved" data-id="5" <?php if(in_array('5', $stsFil)){ echo "checked"; } ?> onclick="utilization_status('resolved', 'check');" style="cursor:pointer;" />
                            <font onClick="utilization_status('resolved','text');" style="cursor:pointer;">
                            &nbsp;<?php echo __("Resolved"); ?>
                            </font></a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="javascript:jsVoid();" title="Status" data-toggle="dropdown" onclick="allfiltervalue('utilization_project', event);"><?php echo __("Project"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_utilization_project_div">
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_utilization_project"></ul>
                </div>
            </li>
            <li>
                <a href="javascript:jsVoid();" title="Types" data-toggle="dropdown" onclick="allfiltervalue('utilization_resource', event);"><?php echo __("Resource"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_utilization_resource_div" >
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_utilization_resource"></ul>
                </div>
            </li>
        </ul>
    </div>
    <div class="utilization_filter_msg fl" data-column-id="filter_msg"></div>
    <table id="grid-keep-selection" class="table table-striped resource-utilize-table">
        <thead>
            <tr>
                <th data-column-id="date" data-order="asc"><?php echo __("Date"); ?></th>
                <th data-column-id="resource" data-identifier="true"><?php echo __("Resource"); ?></th>
                <th data-column-id="project" data-visible="false"><?php echo __("Project"); ?></th>
                <th data-column-id="task_title" data-visible="false"><?php echo __("Task Title"); ?></th>
                <th data-column-id="task_status" data-visible="false" data-sortable="false"><?php echo __("Status"); ?></th>
                <th data-column-id="task_type" data-visible="false" data-sortable="false"><?php echo __("Type"); ?></th>
                <th data-column-id="hours"><?php echo __("Hour(s) Spent"); ?></th>
                <th data-column-id="is_billable"><?php echo __("Billable"); ?></th>
            </tr>
        </thead>
    </table>
    </div>
</div>
</div>
<style>
    #grid-keep-selection th{background-color: #F3F3F3;font-size: 13px;color: #222;border: 1px solid #ccc;padding: 10px 0px 8px 10px;border-top: 1px solid #CCC;font-weight: normal;text-align: left;}
    #grid-keep-selection td{border: 1px solid #ccc;padding: 8px 0px 8px 10px;}
    .resourceUtilization{width: 100%;margin-top: 15px;border-bottom: 0px;}
    .logmore-btn a {float:right;z-index: 9999999;cursor:pointer;width: 150px;height: 30px;color: #fff;background-color: #00bcd5/*#367FA9#2fb45b*/;font-size: 14px;border-radius: 5px;display: block;padding: 2px;line-height: 29px;text-align: center;text-decoration: none;}
    .actionBar{width:auto; float:right;}
</style>
<script>
$(document).ready(function(){
    
    var url = HTTP_ROOT+'ajax_resource_utilization';
    $("#grid-keep-selection").bootgrid({
    ajax: true,
    url: url,
    post: function ()
    {
        var chk = new Array;
        $('.dropdown-item-checkbox').each(function(){
            if($(this).is(':checked')){
              chk.push($(this).attr('name'));  
        }
        });
        return{ check: chk};
    },
    //selection: true,
    //multiSelect: true,
    rowCount: [50, 75, 100, 200],
    sorting: true,
    multiSort: false,
    rowSelect: true,
    keepSelection: true,
    responseHandler: function(response){
        $('.utilization_filter_msg').html('');
        $('.resourceUtilization .case-filter-menu').show();
        $('.utilization_filter_msg').html(response.filter_msg.date);
        $('#recource_utilization_export_btn').show();
        if(response.filter_msg.status != ''){
            for(var k in response.filter_msg.status){
                var sts = response.filter_msg.status[k];
                if(typeof sts != 'function'){
                    $('.utilization_filter_msg').append(sts);
                }
            }
        }
        if(response.filter_msg.project != ''){
            for(var k in response.filter_msg.project){
                var prj = response.filter_msg.project[k];
                if(typeof prj != 'function'){
                $('.utilization_filter_msg').append(prj);
            }
        }
        }
        if(response.filter_msg.resource != ''){
            for(var k in response.filter_msg.resource){
                var usr = response.filter_msg.resource[k];
                if(typeof usr != 'function'){
                $('.utilization_filter_msg').append(usr);
            }
        }
        }
        if($('.utilization_filter_msg').find('div').size() > 1){
			$('.utilization_filter_msg').append('<div class="fl db-filter-reset-icon ico-close utilization_filter_msg_close" rel="tooltip" title="Reset Filter" style="margin-left:10px;margin-top:8px;color:red;font-weight:bold;cursor:pointer;"></div>');
        }
        return response;
    }
    });
    $(document).on('click', ".utilization_filter_msg_close", function(){
        $('.utilization_filter_msg').html('');
        $('#dropdown_menu_utilization').find('input[type="checkbox"]').removeAttr('checked');
        $('#dropdown_menu_utilization_status').find('input[type="checkbox"]').removeAttr('checked');
        $('#dropdown_menu_utilization_project').find('input[type="checkbox"]').removeAttr('checked');
        $('#dropdown_menu_utilization_resource').find('input[type="checkbox"]').removeAttr('checked');
        $('#dropdown_menu_utilization').find('input[type="text"]').val("");
        $('.custome_timelog').hide();
        remember_filters('utilization_date_filter', 'all');
        remember_filters('utilization_status_filter', 'all');
        remember_filters('utilization_project_filter', 'all');
        remember_filters('utilization_resource_filter', 'all');
        $('#grid-keep-selection').bootgrid('reload');
    });
    $("span[rel='tooltip']").tipsy();
    $('.actions').find('div:nth-child(2)').attr('rel', 'tooltip');
    $('.actions').find('div:nth-child(2)').attr('title', 'Select Limit');
    $('.actions').find('div:nth-child(3)').attr('rel', 'tooltip');
    $('.actions').find('div:nth-child(3)').attr('title', 'Add/Remove Column');
});

function utilization(filter, type){
    if(filter !== 'custom'){
        $('#utilizationstrtdt,#utilizationenddt').val('');
        $("#custom_utilization").hide();
        $('#dropdown_menu_utilization').find('input[type="checkbox"]').removeAttr('checked');
        if(type === "check"){ 
            if($('#utilization_'+filter+'').is(":checked")){
                $('#utilization_'+filter+'').prop('checked', false);
            }else{
                $('#utilization_'+filter+'').prop('checked', true);
            }
        }else{
            if($('#utilization_'+filter+'').is(":checked")){
                $('#utilization_'+filter+'').prop('checked', false);
            }else{
                $('#utilization_'+filter+'').prop('checked', true);
            }
        }
    }else if(filter === 'custom'){
        filter = $('#utilizationstrtdt').val()+':'+$('#utilizationenddt').val();
        $('#dropdown_menu_utilization').find('input[type="checkbox"]').prop('checked', false);
        $('#utilization_custom').prop('checked', true);
    }
    remember_filters('utilization_date_filter', filter);
    $('#grid-keep-selection').bootgrid('reload');
}

function utilization_status(filter, type){
    if(type === "check"){ 
        if($('#utilization_'+filter+'').is(":checked")){
            $('#utilization_'+filter+'').prop('checked', true);
        }else{
            $('#utilization_'+filter+'').prop('checked', false);
        }
    }else{
        if($('#utilization_'+filter+'').is(":checked")){
            $('#utilization_'+filter+'').prop('checked', false);
        }else{
            $('#utilization_'+filter+'').prop('checked', true);
        }
    }
    var sts_filter = '';
    $('#dropdown_menu_utilization_status').find('input[type="checkbox"]').each(function(){
        if($(this).is(':checked')){
            var sts = $(this).attr('data-id');
            sts_filter += sts+'-';
        }
    });
    remember_filters('utilization_status_filter', sts_filter);
    $('#grid-keep-selection').bootgrid('reload');
}

function utilization_resource(id, type){
    var totid = $('#dropdown_menu_utilization_resource').find('input[type="checkbox"]').size();
    var y = 'userid_'+id;
	if (type == "check")
	{
	    if ($('#'+y).is(':checked')) {
		$('#'+y).prop('checked', true);
	    }
	    else
	    {
		$('#'+y).prop('checked', false);
	    }
	}
	else
	{
	    if (!$('#'+y).is(":checked")) {
		$('#'+y).prop('checked', true);
	    }
	    else
	    {
		$('#'+y).prop('checked', false);
	    }
	}
    var x = '';
    $('.utilization-resource').each(function() {
	var dt_id = $(this).attr('data-id');
	if($("#"+this.id).is(':checked')){
	    var userid = "userids_" + dt_id;
	    var uservalue = $("#"+userid).val();
	    x += uservalue + "-";
	}
    });
    
    if (x === "") {
		var types = "all";
    } else {
		var types = x.substring(0, x.length - 1);
    }
    remember_filters('utilization_resource_filter',types);
    $('#grid-keep-selection').bootgrid('reload');
}

function utilization_project(id, type){
    var x = "";
    id = 'prjid_'+id;
	if (type == "check")
	{
	    if ($('#'+id).is(':checked')) {
		$('#'+id).prop('checked', true);
	    }
	    else
	    {
		$('#'+id).prop('checked', false);
	    }
	}
	else
	{
	    if (!$('#'+id).is(':checked')) {
		$('#'+id).prop('checked', true);
	    }
	    else
	    {
		$('#'+id).prop('checked', false);
	    }
	}
    var x = '';
    $('.utilization-project').each(function() {
	var dt_id = $(this).attr('data-id');
	if($(this).is(':checked')){
	    var prjid = "prjids_" + dt_id;
	    var prjvalue = $("#"+prjid).val();
	    x += prjvalue + "-";
	}
    });
    if (x === "") {
		var types = "all";
    } else {
		var types = x.substring(0, x.length - 1);
    }
    remember_filters('utilization_project_filter',types);
    $('#grid-keep-selection').bootgrid('reload');
}

function ajax_resource_utilization_export_csv(){
    var project = '';
    var task = '';
    var chk = new Array;
    $('.dropdown-item-checkbox').each(function(){
        if($(this).is(':checked')){
          chk.push($(this).attr('name'));  
        }
    });
    var array1 =  ["task_status", "task_type"];
    var is_same = (array1.length === chk.length) && array1.every(function(element, index) {
        return element === chk[index]; 
    });
    is_same = ($.inArray('task_status', chk) > -1 || $.inArray('task_type', chk) > -1) && ($.inArray('task_title', chk) === -1);
    if(is_same){
        showTopErrSucc('error', 'Please select task title to export.');
        return false;
    }else{
    var search = $('.search').find('.search-field').val().trim() !== '' ? $('.search').find('.search-field').val() : '';
    var url_params = 'projuniqid='+$('#projFil').val()+'&task='+task+'&project='+project+'&check='+chk+'&search='+search;
    var url = HTTP_ROOT+"ajax_resource_utilization_export_csv?"+url_params;
    window.open(url,'_blank');
    return false;
            }
}
function customdatetutilization(){
    $('#dropdown_menu_utilization').find('input:checked').removeAttr('checked');
    $('#utilization_custom').attr('checked', 'checked');
    $('#custom_utilization').show();
}
$(document).on('click', function(e){
    $(e.target).hasClass('case-filter-menu') ? $("#dropdown_menu_all_filters").show() : $("#dropdown_menu_all_filters").hide();
});

function removeStatus(id){
    var sct_id = '';
    if(id == 1){
        act_id = 'new';
    }else if(id == 2){
        act_id = 'wip';
    }else if(id == 3){
        act_id = 'closed';
    }else if(id == 5){
        act_id = 'resolved';
    }
    $('#dropdown_menu_utilization_status').find('input[id="utilization_'+act_id+'"]').prop('checked', false);
    var sts_filter = '';
    $('.utilization-status').each(function(){
        if($(this).is(':checked')){
            var sts = $(this).attr('data-id');
            sts_filter += sts+'-';
        }
    });
    remember_filters('utilization_status_filter', sts_filter);
    $('#grid-keep-selection').bootgrid('reload');
}

function removeProject(id){
    $('#prjid_'+id).removeAttr('checked');
    var x = '';
    $('.utilization-project').each(function() {
	var dt_id = $(this).attr('data-id');
	if($(this).is(':checked')){
	    var prjid = "prjids_" + dt_id;
	    var prjvalue = $("#"+prjid).val();
	    x += prjvalue + "-";
	}
    });
    
    if (x === "") {
		var types = "all";
    } else {
		var types = x.substring(0, x.length - 1);
    }
    remember_filters('utilization_project_filter',types);
    $('#grid-keep-selection').bootgrid('reload');
}

function removeResource(id){
    $('#userid_'+id).prop('checked', false);
    var x = '';
    $('.utilization-resource').each(function() {
	var dt_id = $(this).attr('data-id');
	if($("#"+this.id).is(':checked')){
	    var userid = "userids_" + dt_id;
	    var uservalue = $("#"+userid).val();
	    x += uservalue + "-";
	}
    });
    
    if (x === "") {
		var types = "all";
    } else {
		var types = x.substring(0, x.length - 1);
    }
    remember_filters('utilization_resource_filter',types);
    $('#grid-keep-selection').bootgrid('reload');
}

function removeDate(){
    $('#dropdown_menu_utilization').find('input[type="checkbox"]').prop('checked', false);
    $('#dropdown_menu_utilization').find('input[type="text"]').val("");
    $('.custome_timelog').hide();
    remember_filters('utilization_date_filter', '');
    $('#grid-keep-selection').bootgrid('reload');
}
$(function(){
    $( "#utilizationstrtdt" ).datepicker({
		dateFormat: 'M d, yy',
		changeMonth: false,
		changeYear: false,
		//minDate: 0,
		hideIfNoPrevNext: true,
		maxDate: "0D",
		onClose: function( selectedDate ) {
			 $( "#utilizationenddt" ).datepicker( "option", "minDate", selectedDate );
		 }
	});
	$( "#utilizationenddt" ).datepicker({
		dateFormat: 'M d, yy',
		changeMonth: false,
		changeYear: false,
		//minDate: 0,
		hideIfNoPrevNext: true,
		maxDate: "0D",
		onClose: function( selectedDate ) {
				$( "#utilizationstrtdt" ).datepicker( "option", "maxDate", selectedDate );
			}
	});
});
</script>