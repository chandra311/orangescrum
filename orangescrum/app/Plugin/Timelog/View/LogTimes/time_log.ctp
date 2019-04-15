<?php

#echo "<pre>";print_r($logtimesArr);exit;
if(isset($resCaseProj)){ #pr($resCaseProj);
        $rsrchtml = "<option value=''>".__('Select Resource')."</option>";
	foreach($resCaseProj as $k=>$v){
		$rsrchtml .= "<option value='".$v['User']['id']."'>".$v['User']['name']." ".$v['User']['last_name']."</option>";
	} ?>
	<?php if($logtimesArr['calltype'] != 'log' || (isset($logtimesArr['check_sort']) && !empty($logtimesArr['check_sort']))){?>
<script type="text/javascript">
    var rsrch = "<?php echo $rsrchtml; ?>";
    $(function () {
        $('#rsrclog').html(rsrch);
    });
</script>
	<?php } 
		if(isset($_COOKIE['rsrclog']) && !empty($_COOKIE['rsrclog'])){ ?>
<script type="text/javascript">
    var userid = "<?php echo $_COOKIE['rsrclog']; ?>";
    $(function () {
        $('#rsrclog').val(userid);
    });
</script>
	<?php } ?>
	<?php if(isset($_COOKIE['logstrtdt']) && !empty($_COOKIE['logstrtdt'])){ ?>
<script type="text/javascript">
    var startdate = "<?php echo $_COOKIE['logstrtdt']; ?>";
    $(function () {
        $('#logstrtdt').val(startdate);
    });
</script>
	<?php } ?>
	<?php if(isset($_COOKIE['logenddt']) && !empty($_COOKIE['logenddt'])){ ?>
<script type="text/javascript">
    var enddate = "<?php echo $_COOKIE['logenddt']; ?>";
    $(function () {
        $('#logenddt').val(enddate);
    });
</script>
	<?php } ?>
<?php } ?>
<div class="row">
    <div id="resetdiv" class="filter-info col-lg-10"><div class="fl"><span><?php echo __('Showing time log');?>:</span><span id="filter_text" class="filter-text">&nbsp;<?php if(!empty($filter_text)){echo $filter_text;}else{echo __("for all users and all dates");} ?></span></div>
        <div id="btn-reset-timelog" class="fl db-filter-reset-icon" onclick="hidereset();" rel="tooltip" <?php if($logtimesArr['reset'] == 1){?>style="display:block;"<?php }else{ ?>style="display:none;"<?php } ?> original-title="<?php echo __("Reset Filters"); ?>"></div>
    </div>
    <div class="col-lg-2 task_section case-filter-menu" onclick="openfilter_popup(0, 'dropdown_menu_all_timelog_filters');" rel="tooltip" title="Time Log Filter" data-toggle="dropdown" style="display: block;">
        <button class="btn tsk-menu-filter-btn flt-txt tlog-fltr-vtn fr" type="button">
            <i class="icon_flt_img"></i><?php echo __("Filters"); ?><i class="icon-filter-right"></i>
        </button>
        <input type="hidden" id="tlog_date" value=""/>
        <input type="hidden" id="tlog_resource" value=""/>
        <ul id="dropdown_menu_all_timelog_filters" class="dropdown-menu" style="position: absolute; display: none;">
            <li class="pop_arrow_new"></li>
            <li>
                <a href="javascript:jsVoid();" title="Date" data-toggle="dropdown" onclick="allfiltervalue('createdDate', event);"> <?php echo __("Date"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_createddate_div">
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_createddate">
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_alldates" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'alldates' || $_COOKIE['datelog'] == '') ?"checked":"";?> data-id="alldates" onclick="general.filterDate('timelog', 'alldates', 'All', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'alldates', 'All', 'text');" > <?php echo __("All Dates"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_today" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'today') ?"checked":"";?> data-id="today" onclick="general.filterDate('timelog', 'today', 'Today', 'check');"/>
                                <font data-id="today" onclick="general.filterDate('timelog', 'today', 'Today', 'text');" > <?php echo __("Today"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_yesterday" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'yesterday') ?"checked":"";?> data-id="yesterday" onclick="general.filterDate('timelog', 'yesterday', 'Yesterday', 'check');"/>
                                <font data-id="yesterday" onclick="general.filterDate('timelog', 'yesterday', 'Yesterday', 'text');" > <?php echo __("Yesterday"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_thisweek" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'thisweek') ?"checked":"";?> data-id="thisweek" onclick="general.filterDate('timelog', 'thisweek', 'This Week', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'thisweek', 'This Week', 'text');" > <?php echo __("This Week"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_thismonth" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'thismonth') ?"checked":"";?> data-id="thismonth" onclick="general.filterDate('timelog', 'thismonth', 'This Month', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'thismonth', 'This Month', 'text');" > <?php echo __("This Month"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_thisquarter" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'thisquarter') ?"checked":"";?> data-id="thisquarter" onclick="general.filterDate('timelog', 'thisquarter', 'This Quarter', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'thisquarter', 'This Quarter', 'text');" > <?php echo __("This Quarter"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_thisyear" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'thisyear') ?"checked":"";?> data-id="thisyear" onclick="general.filterDate('timelog', 'thisyear', 'This Year', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'thisyear', 'This Year', 'text');" > <?php echo __("This Year"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_lastweek" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'lastweek') ?"checked":"";?> data-id="lastweek" onclick="general.filterDate('timelog', 'lastweek', 'Last Week', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'lastweek', 'Last Week', 'text');" > <?php echo __("Last Week"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_lastmonth" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'lastmonth') ?"checked":"";?> data-id="lastmonth" onclick="general.filterDate('timelog', 'lastmonth', 'Last Month', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'lastmonth', 'Last Month', 'text');" > <?php echo __("Last Month"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_lastquarter" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'lastquarter') ?"checked":"";?> data-id="lastquarter" onclick="general.filterDate('timelog', 'lastquarter', 'Last Quarter', 'check');"/>
                                <font data-id="alldates" onclick="general.filterDate('timelog', 'lastquarter', 'Last Quarter', 'text');" > <?php echo __("Last Quarter"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_lastyear" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'lastyear') ?"checked":"";?> data-id="lastyear" onclick="general.filterDate('timelog', 'lastyear', 'Last Year', 'check');"/>
                                <font data-id="lastyear" onclick="general.filterDate('timelog', 'lastyear', 'Last Year', 'text');" > <?php echo __("Last Year"); ?></font>
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);">
                                <input type="checkbox" id="timelog_last365days" class="cbox_date" <?php echo ($_COOKIE['datelog'] == 'last365days') ?"checked":"";?> data-id="last365days" onclick="general.filterDate('timelog', 'last365days', 'Last 365Days', 'check');"/>
                                <font data-id="last365days" onclick="general.filterDate('timelog', 'last365days', 'Last 365Days', 'text');" > <?php echo __("Last 365Days"); ?></font>
                            </a>
                        </li>
                        <li class="custom-date-btn">
                            <a class="anchor cstm-dt-option" onclick="customdatetlog(this);">
                                <input type="checkbox" id="timelog_custom" class="cbox_date" <?php echo (strpos($_COOKIE['datelog'], ':') > 0) ?"checked":"";?> data-id="custom"/>
                                <font data-id="custom"><?php echo __("Custom Date"); ?></font>
                            </a>
                        </li>
                        <li class="custome_timelog custom_date_li" style="display: none;">
                            <div  class="cdate_div_cls" style="padding-left:10px">
                                <?php 
                                    if(strpos($_COOKIE['datelog'], ':')){
                                        $dt = explode($_COOKIE['datelog'], ':');
                                        $frm = $dt[0];
                                        $to = $dt[1];
                                    } else {
                                        $frm = $startdate;
                                        $to = $enddate;
                                    }
                                ?>
                                <input type="text" class="smal_txt form-control " placeholder="<?php echo __("From Date"); ?>" readonly  id="logstrtdt" value="<?php echo $frm; ?>"/>
                                <input type="text" class="smal_txt form-control " placeholder="<?php echo __("To Date"); ?>" readonly id="logenddt" value="<?php echo $to; ?>"/>
                            </div>
                            <div  class="cdate_btn_div" style="text-align:center;margin-top: 5px;cursor:pointer">
                                <button class="btn btn-primary cdate_btn" type="button" onclick="general.filterDate('timelog', 'custom', 'Custom');" id="btn_timelog_search"><?php echo __("Search"); ?></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
			<?php if(SES_TYPE <=2){ ?>
            <li>
                <a href="javascript:jsVoid();" title="Resource" data-toggle="dropdown" onclick="allfiltervalue('resource', event);"> <?php echo __("Resource"); ?></a>
                <div class="dropdown_status" id="dropdown_menu_resource_div">
                    <i class="status_arrow_new"></i>
                    <ul class="dropdown-menu" id="dropdown_menu_resource"></ul>
                </div>
            </li>
			<?php } ?>
        </ul>
    </div>
   
    <input type="hidden" id="flt_start_date" value ="<?php echo $startdate; ?>">
    <input type="hidden" id="flt_end_date" value ="<?php echo $enddate; ?> ">
    <input type="hidden" id="flt_resource" value ="<?php echo implode(',',$usrid); ?> ">
</div>
<div class="cb"></div>
<div id="timelogloader" style="display:none;">
    <div class="loadingdata" style="background:#F0C36D;position:fixed;left:50%"><img src="<?php echo HTTP_IMAGES; ?>ajax-loader.gif" title="<?php echo __("loading"); ?>..."/> <?php echo __("loading"); ?>...</div>
</div>
<div class="timelog-table" id="timelogtbl">
<?php #print_r($logtimesArr);exit; ?>
    <div class="timelog-table-head">
        <div>
            <div class="spent-time col-lg-7 fl">
                <div class="fl">
<?php /* <span class="time-log-head"><?php echo __('Time Log');?>:</span> */ ?>
                </div>
                <div class="fl">
                    <span class="total use-time" style="display:none;"><?php echo __("Total"); ?>: </span>
                    <span class="use-time"><?php echo __('Logged');?>:</span>
                    <span id ="ttl_hr"><?php echo $logtimesArr['details']['totalHrs']; ?></span>
                </div>
                <div class="fl" style="margin:0px 5px">
                    <span class="use-time"><?php echo __('Billable');?>:</span>
                    <span id = "blbl_hr"><?php echo $logtimesArr['details']['billableHrs']; ?></span>
                </div>
                <div class="fl" style="margin:0px 5px">
                    <span class="use-time"><?php echo __('Non-billable');?>:</span>
                    <span id="non_blbl"><?php echo $logtimesArr['details']['nonbillableHrs']; ?></span>
                </div>
                <div class="fl" id ="est_hrs">
                    <span class="use-time"><?php echo __('Estimated');?>:</span>
                    <span><?php echo $logtimesArr['details']['estimatedHrs'];#$cntestmhrs; ?></span>
                </div>
                <div class="cb"></div>
            </div>
            <div class="col-lg-5 fr" style="margin:7px auto;padding-right: 0">
                <div class="logmore-btn fr"  title="">
                    <a class="anchor" style="padding-left: 0px;margin-right:8px; width:150px; padding-right: 0px;" onclick="ajax_timelog_export_csv();"><span class="icon-exp"></span><?php echo __('Export(.csv)');?></a>
                </div>
    <?php /* <div class="logmore-btn fr">
        <a href="javascript:void(0);" onclick="createlog('0', '');"><?php echo __('Log time');?><span class="sprite btn-clock"></span></a>
    </div>
    <div class="logmore-btn fr">
        <a href="javascript:void(0);" style="padding-left:20px" onclick="openTimer();"><?php echo __('Start Timer');?><span class="sprite btn-timer"></span></a>
    </div> */ ?>

            </div>
            <div class="cb"></div>
        </div>

    </div>
    <div class="timelog-detail-tbl">
        <table cellpadding="3" cellspacing="4">
            <tr>
<?php if($prjctId == "all"){ ?>
                <th><a class="sorttitle"  onclick="sorting('Project');" title="<?php echo __('Project');?>" href="javascript:void(0);">
                        <div class="fl"><?php echo __('Project');?></div>
                        <div id="tsk_sort4" class="tsk_sort fl"></div>
                    </a></th>      
<?php }?>
                <th><a class="sorttitle" onclick="sorting('Date');" title="<?php echo __('Date');?>" href="javascript:void(0);">
                        <div class="fl"><?php echo __('Date');?></div>
                        <div id="tsk_sort1" class="tsk_sort fl "></div>
                    </a></th>
                <th><a class="sorttitle" onclick="sorting('Name');" title="<?php echo __('Name');?>" href="javascript:void(0);">
                        <div class="fl"><?php echo __('Name');?></div>
                        <div id="tsk_sort2" class="tsk_sort fl "></div>
                    </a></th>
                <th><a class="sorttitle" onclick="sorting('Task');" title="<?php echo __('Task');?>" href="javascript:void(0);">
                        <div class="fl"><?php echo __('Task');?></div>
                        <div id="tsk_sort3" class="tsk_sort fl "></div>
                    </a></th>
                <th><?php echo __('Note');?></th>
                <th><?php echo __('Start');?></th>
                <th><?php echo __('End');?></th>
                <th><?php echo __('Break');?></th>
                <th><?php echo __('Billable');?></th>
                <th>Hours</th>
                <th style="text-align: center;padding: 0px;width: 5%;"><?php echo __('Action');?></th>
            </tr>
<?php #pr($logtimesArr);exit; ?>
<?php if (!empty($logtimesArr['logs'])) { ?>
<?php foreach ($logtimesArr['logs'] as $key=>$log) { ?>
            <tr>
<?php if($prjctId == "all"){ ?>
                <td id="name_<?php echo $log['LogTime']['project_id'] ;?>">
<?php echo $log['Project']['project_name']; ?></td>  
<?php } ?>
                <td class="anchor" onclick="showlogfordate('<?php echo date('M d, Y',strtotime($log['LogTime']['task_date'])); ?>')"><?php echo date('M d, Y',strtotime($log['LogTime']['task_date'])); ?></td>
<?php $name = $this->Format->getUserDtls($log['LogTime']['user_id']);?>
                <td class="anchor" onclick="showlogforuser(<?php echo $log['LogTime']['user_id']; ?>, '<?php echo $name['User']['name']; ?>')">
<?php echo $name['User']['name']; ?></td>
<?php $tsks = $this->Format->getTaskdetails($log['LogTime']['project_id'],$log['LogTime']['task_id']); ?>
                <td class="anchor" onclick="showlogfortask(<?php echo $log['LogTime']['task_id']; ?>, '<?php echo $tsks['Easycase']['title']; ?>')" title="<?php echo $tsks['Easycase']['title']; ?>" rel="tooltip"><?php echo $this->Format->frmtdata($tsks['Easycase']['title'],0,20); ?></td>
                <td <?php if(!empty($log['LogTime']['description'])) { ?>rel="tooltip" <?php } ?> title="<?php echo $log['LogTime']['description']; ?>" ><?php echo $this->Format->frmtdata($log['LogTime']['description'],0,20); ?></td>
                <td><?php echo $this->Format->chngdttime($log['LogTime']['task_date'],$log['LogTime']['start_time']); ?> </td>
                <td><?php echo $this->Format->chngdttime($log['LogTime']['task_date'],$log['LogTime']['end_time']); ?></td>
                <td><?php echo $this->Format->format_time_hr_min($log['LogTime']['break_time']); ?></td>
                <td align="center"><span <?php if($log['LogTime']['is_billable']){ ?> class="sprite yes" <?php } else { ?> class="sprite no" <?php } ?> ></span></td>
                <td>
                    <span class="fl"><?php $hrs = floor($log['LogTime']['total_hours']/3600)." hrs ".(($log['LogTime']['total_hours']%3600)/60)." min"; echo $hrs; ?></span>
                </td>
<?php  if($log['LogTime']['user_id'] == SES_ID || SES_TYPE == 1 || SES_TYPE == 2){ $project_uniq = $this->Format->getprjctUnqid($log['LogTime']['project_id']);?>
                <td class="edtdltlog" data-logid="<?php echo $log['LogTime']['log_id'];?>"  data-prjctUniqid="<?php echo $project_uniq['Project']['uniq_id']; ?>" data-prjctId="<?php echo $log['LogTime']['project_id']; ?>">
<?php } else { ?>
                <td style="position:relative;">
                    <div class="timelog-overlap" style="" rel="tooltip" title="You are not authorised to modify."></div>
<?php } ?>
                    <a class="anchor edit_time_log" onclick="editTimelog(this)"><span class="fl sprite note"></span></a>
                    <a class="anchor delete_time_log" onclick="deletetimelog(this);"><span class="fl sprite delete"></span></a>
                </td>
            </tr>
<?php } ?>
<?php }else{ ?>
            <tr>
                <td colspan="10"><?php echo __('No records');?>......</td>
            </tr>
<?php } ?>
        </table>
    </div>
</div>
<div id="calendar_view" class="calendar_section calendar_resp" style="display:block;margin-top: 12px;"></div>
<div id="caseLoader">
    <div class="loadingdata"><?php echo __("Loading"); ?>...</div>
</div>
<?php if($logtimesArr['caseCount']){
	$caseCount = $logtimesArr['caseCount'];
	$page_limit = $logtimesArr['page_limit'];
	$casePage = $logtimesArr['csPage'];
?>
<table id="pagingtable" cellpadding="0" cellspacing="0" border="0" align="right" <?php if($logtimesArr['calltype'] != 'log'){?> style="margin-top:0px;"<?php } ?>>
    <tr>
        <td align="center" style="padding-top:5px;padding-right:35px;">
            <div class="show_total_case" style="font-weight:normal;color:#000;font-size:12px;">
				<?php echo  $this->Format->pagingShowRecords($caseCount,$page_limit,$casePage); ?>
            </div>
        </td>
    </tr>
    <tr>
        <td align="center">
            <ul class="pagination" style="padding-right:35px;">
		<?php $caseCount = $logtimesArr['caseCount'];
			 $page = $logtimesArr['csPage'];
			$page_limit = $logtimesArr['page_limit'];
			if($page_limit < $caseCount){
				$numofpages = $caseCount / $page_limit;
				if(($caseCount % $page_limit) != 0){
					$numofpages = $numofpages+1;
				}
				$lastPage = $numofpages;
				$k = 1;
				$data1 = "";
				$data2 = "";
				if($numofpages > 5){
					$newmaxpage = $page+2;
					if($page >= 3){
						$k = $page-2;
						$data1 = "...";
					}
					if(($numofpages - $newmaxpage) >= 2){
						if($data1){
							$data2 = "...";
							$numofpages = $page+2;
						}else{
							if($numofpages >= 5){
								$data2 = "...";
								$numofpages = 5;
							}
						}
					}
				}
				if($data1){
					echo "<li><a href='javascript:void(0)' onclick='logpagging(1)' class=\"button_act\">&laquo; ".__("First")."</a></li>";
					echo "<li class='hellip'>&hellip;</li>";
				}
				if($page != 1){
					$pageprev = $page-1;
                     echo "<li><a href='javascript:void(0)' onclick='logpagging(".$pageprev.")' class=\"button_act\">&lt;&nbsp;".__("Prev")." </a></li>";
				}else{
					echo "<li><a href='javascript:jsVoid();' class=\"button_prev\" style=\"cursor:text\">&lt;&nbsp;". __("Prev")."</a></li>";
				}
				for($i = $k; $i <= $numofpages; $i++){
					if($i == $page) {
						echo "<li><a href='javascript:jsVoid();' class=\"button_page\" style=\"cursor:text\">".$i."</a></li>";
					}else {
                     if($projtype == 'inactive'){
                          echo "<li><a href='javascript:void(0)' onclick='logpagging(".$i.")' class=\"button_act\" >".$i."</a></li>";
                     }else{
                          echo "<li><a href='javascript:void(0)' onclick='logpagging(".$i.")' class=\"button_act\" >".$i."</a></li>";
                     }
					}
				}
				if(($caseCount - ($page_limit * $page)) > 0){
					$pagenext = $page+1;
                    echo "<li><a href='javascript:void(0)' onclick='logpagging(".$pagenext.")' class=\"button_act\" >".__("Next"). "&nbsp;&gt; </a></li>";       
				}else{
                     echo "<li><a href='javascript:void(0)' class=\"button_prev\">".__("Next")."&nbsp;&gt;</a></li>";
				}
				if($data2){
					echo "<li class='hellip'>&hellip;</li>";
                    echo "<li><a href='javascript:void(0)' onclick='logpagging(".floor($lastPage).")' class=\"button_act\" >".__("Last")." &raquo;</a></li>";
				}
			} ?>
            </ul>
        </td>
    </tr>
</table>
<?php }	?>
<?php if(SES_TYPE < 3){?>
<div class="timelog-table" style="border: none;display:inline-block; margin-top:0px;">
    <div class="fr">
        <a href="<?php echo HTTP_ROOT."resource-utilization/" ?>" title="<?php echo __("Resource Utilization");?>" class="pull-right" style="color:#2d6dc4"><?php echo __("Resource Utilization Report");?></a>
    </div>
</div>
<?php } ?>
<div class="crt_task_btn_btm">
    <div class="pr">
        <div class="os_plus ctg_btn">
            <div class="ctask_ttip">
                <span class="label label-default"><?php echo __("Start Timer"); ?></span>
            </div>
            <a onclick="openTimer();" href="javascript:void(0)">
                <img src="<?php echo HTTP_ROOT; ?>img/images/strt_timer.png" class="tme_icn" />
            </a>
        </div>
    </div>
    <div class="os_plus">
        <div class="ctask_ttip">
            <span class="label label-default">
                <?php echo __("Log Time"); ?>
            </span>
        </div>
        <a href="javascript:void(0)" onClick="createlog('0', '');">
            <img class="prjct_icn ctask_icn" src="<?php echo HTTP_ROOT; ?>img/images/crt_timelog.png"> 
            <img src="<?php echo HTTP_ROOT; ?>img/images/plusct.png" class="add_icn" />
        </a>
    </div>
</div>
</div>
</div>
<style>
    .timelog-table{width:100%;border:1px solid #ccc;margin-top:15px;border-bottom:0px;}
    .timelog-table-head{margin:10px 5px 0px 5px;}
    .timelog-table-head .time-log-head{font-size: 23px;font-weight: bold;color:#444;}
    .logmore-btn a{width:100px;height:30px;color:#fff;background-color:#2fb45b;font-size:14px;border-radius:5px;display: block;padding:2px;line-height: 29px;text-align: center;text-decoration:none;margin-right:5px} 
    .timelog-table-head .spent-time{margin:5px 0 10px;}
    .spent-time .total{font-size:16px;color:#444;}
    .timelog-table .timelog-detail-tbl table {width:100%;}
    .spent-time .use-time{color:#8E8E8E;}
    .spent-time span{font-size:13px;}
    .timelog-table .timelog-detail-tbl th{background-color:#F3F3F3;font-size:13px;color:#222;padding:10px 0px 8px 10px; border-top:1px solid #CCC; font-weight:normal;  text-align: left;}	
    .timelog-table .timelog-detail-tbl td{border:1px solid #ccc;padding:8px 0px 8px 9px; }
    .timelog-table .timelog-detail-tbl table  tr:hover { background-color: #ffff99;}
    .crt-task{font-size:14px;color:#558DD8;margin-left:10px; padding: 5px 0;}
    .ht_log{padding: 5px 0;}
    .sprite{background:url("<?php echo HTTP_ROOT; ?>img/sprite.png")no-repeat;position:relative;display:block;width:22px;height:20px;cursor:pointer;}
    .sprite.btn-clock{ background-position: 0px -63px;left:3px;top:-24px;}
    .sprite.btn-timer{ background:url("<?php echo HTTP_ROOT; ?>img/ico-timer.png");background-position: -21px 0px;left:-20px;top:-26px;}
    .sprite.yes{background-position:2px -40px;left:-6px;top:0;cursor: default;}
    .sprite.no{background-position:2px -20px;left:-6px;top:0;cursor: default;}
    .sprite.up-btn{background-position: 2px -80px;left:-2px;top:0;}
    .sprite.note{ background-position:2px 0;left:-8px;top:0;}
    .sprite.delete{ background: rgba(0, 0, 0, 0) url("<?php echo HTTP_ROOT; ?>img/sprite_osv2.png") no-repeat scroll -185px -170px;height: 17px;width: 19px;left:-8px;}
    .icon-date-filter{background: url("<?php echo HTTP_ROOT; ?>img/sprite_osv2.png") no-repeat scroll -341px -166px transparent;height: 27px;margin-right: 10px;width: 28px;}
    .loginactive {background: #f4f4f4 none repeat scroll 0 0 !important;border: 1px solid #d8d8d8;border-radius: 5px;color: #a2a2a2 !important;cursor: not-allowed !important;font-family: "Raleway !important";font-size: 14px;padding: 5px 20px !important;text-decoration: none;}
    .filter-info{}
    .filter-text{font-weight:bold;font-style:italic}
    .m-50{margin-left:50px;}
    .anchor{cursor:pointer;}
    .btn.btn_blue.reset_btn{font-size: 13px;height: 27px;line-height: 14px;margin: 0 0 0 5px;padding: 5px 10px;background:#ffff99;color:black}
    .btn.btn_blue.aply_btn{font-size: 13px;height: 28px;line-height: 14px;margin: 0 0 0 5px;padding: 0px 5px;}
    .temp-class{border-style: none;}
    .disable{background:#d8d8d8}
    .case-filter-menu #dropdown_menu_all_timelog_filters{top:28px;left:inherit;right:0px}
    .case-filter-menu .dropdown-menu#dropdown_menu_all_timelog_filters .pop_arrow_new{margin-left:78px}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_createddate_div{left:inherit;right:130px;}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_createddate_div ul{width:125px}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_resource_div{left:inherit;right:147px}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_resource_div .status_arrow_new{background: rgba(0, 0, 0, 0) url("<?php echo HTTP_ROOT; ?>img/images/arrow_rht.png") no-repeat scroll 0 0;right:inherit;left:138px}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_createddate_div .status_arrow_new{background: rgba(0, 0, 0, 0) url("<?php echo HTTP_ROOT; ?>img/images/arrow_rht.png") no-repeat scroll 0 0;right:inherit;left:120px}
    .case-filter-menu #dropdown_menu_all_timelog_filters #dropdown_menu_resource_div .checkbox{margin:0px;}
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $('#custom_duedate').hide();
        $('#select_view div').tipsy({
            gravity: 'n',
            fade: true
        });

        $("#logstrtdt").datepicker({
            dateFormat: 'M d, yy',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function (selectedDate) {
                $("#logenddt").datepicker("option", "minDate", selectedDate);
            },
        });
        $("#logenddt").datepicker({
            dateFormat: 'M d, yy',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function (selectedDate) {
                $("#logstrtdt").datepicker("option", "maxDate", selectedDate);
            },
        });
        if (CONTROLLER == 'LogTimes' && PAGE_NAME == 'time_log') {
            $('#lview_btn').addClass('disable');
        }
		
		$('.custom_date_li,.custom-date-btn,#ui-datepicker-div').on('click',function(e){
			e.stopPropagation();
		});
		
        $(document).off('click').on('click', function (e) {
			console.log(e.target);
            $(e.target).hasClass('tlog-fltr-vtn') ? '' : $('#dropdown_menu_all_timelog_filters').hide();
            $(e.target).hasClass('top-links') || $(e.target).closest('.top-links').length ? $(e.target).closest('li').addClass('open') : $('.top-links').closest('li').removeClass('open');
        });
    });
</script>
