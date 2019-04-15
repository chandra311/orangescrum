<div class="crt_slide task_action_bar">
	<button type="button" class="btn gry_btn task_create_back" onclick="crt_popup_close()"><i class="icon-backto"></i><?php echo __("Go Back"); ?></button>
</div>	

<div class="breadcrumb_div">

<ol class="breadcrumb breadcrumb_fixed">
	<li>
		<a href="<?php echo HTTP_ROOT.Configure::read('default_action');?>">	<i class="icon-home"></i></a>
	</li>
<?php if(CONTROLLER == "easycases" && (PAGE_NAME == "mydashboard")) { ?>
	<li><?php echo __("Dashboard"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "easycases" && (PAGE_NAME == "files")) { ?>
	<li><?php echo __("Files"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "milestones" && (PAGE_NAME == "milestone" || PAGE_NAME=='milestonelist')) { ?>
	<li><?php echo __("Milestone"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "archives" && (PAGE_NAME == "listall")) { ?>
	<li><?php echo __("Archive"); ?></li>
	<li><?php echo __("Tasks"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "projects" && (PAGE_NAME == "manage")) { ?>
	<li><?php echo __("Projects"); ?></li>
	<li><?php echo __("Manage"); ?></li>
    <li class="kanbn dashborad-view-type" id="select_view">
        <a href="<?php echo HTTP_ROOT.'projects/manage';?>"><div id="cview_btn" class="btn gry_btn kan30" title="<?php echo __('Card View'); ?>"><img src="<?php echo HTTP_ROOT; ?>img/prj_icon.png" style="width:20px;margin-top:-8px;margin-left:-2px" /></div></a>
        <a href="<?php echo HTTP_ROOT.'projects/manage/active-grid';?>"><div id="lview_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("List View"); ?>"><i class="icon-list-view"></i></div></a>
    </li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "manage")) { ?>
	<li><?php echo __("Users"); ?></li>
	<li><?php echo __("Manage"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "profile")) { ?>
	<li><?php echo __("Personal Settings"); ?></li>
	<li><?php echo __("My Profile"); ?></li>
<?php }
	if(CONTROLLER == "users" && (PAGE_NAME == "changepassword")) { ?>
	<li><?php echo __("Personal Settings"); ?></li>
	<li><?php echo __("Change Password"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "email_notifications")) { ?>
	<li><?php echo __("Personal Settings"); ?></li>
	<li><?php echo __("Notifications"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "email_reports")) { ?>
	<li><?php echo __("Personal Settings"); ?></li>
	<li><?php echo __("Email Reports"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "mycompany")) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("My Company"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "dailyupdatealerts")) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Daily Catch-Up"); ?></li>
<?php } 
    if(CONTROLLER == "ClientRestriction" && PAGE_NAME == "settings"){ ?>
    <li><?php echo __("Company Settings"); ?></li>
    <li><?php echo __("Client Restrictions"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "importexport")) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Import & Export"); ?></li>
<?php } 
    if($this->params['plugin'] == "MultiLanguage" && (PAGE_NAME == "settings")) {?>
	<li><?php echo __("Personal Settings"); ?></li>
	<li><?php echo __("Language"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "cancelact")) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Cancel Account"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "subscription")) { ?>
	<li><?php echo __("Account Settings"); ?></li>
	<li><?php echo __("Subscription"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "creditcard")) { ?>
	<li><?php echo __("Account Settings"); ?></li>
	<li><?php echo __("Credit Card"); ?></li>
<?php }
	if(CONTROLLER == "users" && (PAGE_NAME == "transaction")) { ?>
	<li><?php echo __("Account Settings"); ?></li>
	<li><?php echo __("Transactions"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "account_activity")) { ?>
	<li><?php echo __("Account Settings"); ?></li>
	<li><?php echo __("Account Activity"); ?></li>
<?php } 
if(CONTROLLER == "users" && (PAGE_NAME == "upgrade_member")) { ?>
	<li><?php echo __("Subscription"); ?></li>
	<li><?php echo __("Upgrade Subscription"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "downgrade")) { ?>
	<li><?php echo __("Subscription"); ?></li>
	<li><?php echo __("Downgrade Subscription"); ?></li>
<?php } 
	if(CONTROLLER == "users" && (PAGE_NAME == "edit_creditcard")) { ?>
	<li><?php echo __("Credit Card"); ?></li>
	<li><?php echo __("Edit Credit Card"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "confirmationPage")) { ?>
	<li><?php echo __("Subscription"); ?></li>
	<li><?php echo __("Account Limitation"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "pricing")) { ?>
	<li><?php echo __("Subscription"); ?></li>
	<li><?php echo __("Pricing"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "activity")) { ?>
	<li><?php echo __("Activities"); ?></li>
<?php } ?>	
<?php if(CONTROLLER == "projects" && (PAGE_NAME == "importexport" || PAGE_NAME=='csv_dataimport' || PAGE_NAME=='confirm_import') ) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Import & Export"); ?></li>
<?php }
    if(CONTROLLER == "projects" && (PAGE_NAME == "task_type")) { ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Task Type"); ?></li>
<?php } ?>	
<?php if(CONTROLLER == "projects" && PAGE_NAME == "groupupdatealerts"){ ?>
	<li><?php echo __("Company Settings"); ?></li>
	<li><?php echo __("Daily Progress Reminder"); ?></li>
<?php } ?>	
<?php if(CONTROLLER == "easycases" && (PAGE_NAME == "dashboard")) {?>
	<li><span id="brdcrmb-cse-hdr"><?php echo __("Tasks"); ?></span></li>
<?php } ?>
<?php if(CONTROLLER == "LogTimes" && (PAGE_NAME == "time_log")) {?>
	<li><span id="brdcrmb-cse-hdr"><?php echo __("Time Log"); ?></span></li>
<?php } ?>
<?php if(CONTROLLER == "templates" && (PAGE_NAME == "view_templates")) {?>
	<li><?php echo __("Template"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "ProjectTemplates" && (PAGE_NAME == "projects")) {?>
	<li><?php echo __("Templates"); ?></li>
	<li><?php echo __("Project"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "templates" && (PAGE_NAME == "tasks")) {?>
	<li><?php echo __("Templates"); ?></li>
	<li><?php echo __("Task"); ?></li>
<?php } ?>

<?php if(CONTROLLER == "reports" && (PAGE_NAME == "glide_chart")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Bug Reports"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "reports" && (PAGE_NAME == "hours_report")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Hours Spent Reports"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "reports" && (PAGE_NAME == "chart")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Task Reports"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "users" && (PAGE_NAME == "cancel_account")) {?>
	<li><?php echo __("Account"); ?></li>
	<?php if(($user_subscription['subscription_id']>1) && !$user_subscription['is_free']){?>
	<li><?php echo __("Cancel Account"); ?></li>
	<?php }else{?>
	<li><?php echo __("Delete Account"); ?></li>
	<?php } ?>
<?php } ?>
<?php if(CONTROLLER == 'Workflows' && PAGE_NAME == 'workflow'){ ?>
    <li><?php echo __("Task Status Group"); ?></li>
    <li><button class="btn gry_btn workflow_detail_back" type="button" style="margin-left:18px;display:none;">
		<i class="icon-backto"></i><?php echo __("Go Back"); ?>
		</button>
    </li>
<?php } ?>
<?php if(CONTROLLER == "reports" && (PAGE_NAME == "weeklyusage_report")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Weekly Usage Report"); ?></li>
	<li><?php echo __("Project"); ?>: <span class="weekly_all"><?php echo __("All"); ?></span></li>
<?php } ?>
<?php if(CONTROLLER == "LogTimes" && (PAGE_NAME == "resource_utilization")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Resource Utilizations"); ?></li>
<?php } ?>
<?php if(CONTROLLER == "LogTimes" && (PAGE_NAME == "resource_availability")) {?>
	<li><?php echo __("Analytics"); ?></li>
	<li><?php echo __("Resource Availability"); ?></li>
<?php } ?>
<?php if(CONTROLLER == 'Install' && PAGE_NAME == 'index'){ ?>
    <li><?php echo __("Installation Wizard"); ?></li>
<?php } ?>
<?php if(CONTROLLER == 'Install' && PAGE_NAME == 'installed_addons'){ ?>
    <li><?php echo __("Installation Wizard"); ?></li>
    <li><?php echo __("Installed Add-ons"); ?></li>
<?php } ?>
<?php if (CONTROLLER == "Ganttchart" && (PAGE_NAME == "manage" || PAGE_NAME == "ganttv2")) { ?>
    <li><?php echo __('Miscellaneous'); ?></li>
    <li class="under-icon"><?php echo __('Gantt Chart'); ?></li>
<?php } ?>
<?php if((CONTROLLER == "easycases" && (PAGE_NAME == "dashboard" || PAGE_NAME == "mydashboard")) || (CONTROLLER == "milestones" && (PAGE_NAME == "milestone" || PAGE_NAME=='milestonelist')) || (CONTROLLER == "users" && (PAGE_NAME == "activity")) || (CONTROLLER == "LogTimes" && PAGE_NAME == "time_log") || (CONTROLLER == "invoices" && PAGE_NAME == "invoice") || (CONTROLLER == "projects" && PAGE_NAME == "task-status") || (CONTROLLER == "Ganttchart")) {?>
	<li class="dropdown" id="prj_drpdwn"><?php echo __("Project"); ?>:
	<?php if((count($getallproj) == 0) && (SES_TYPE == 1 || SES_TYPE == 2) ) { ?>
		<a onclick="newProject()" href="javascript:void(0);"> <i style="color: 2D678D; font-weight: bold;"> <?php echo __("Create Project"); ?></i></a>
	<?php }else{
		 if(count($getallproj)=='0'){ ?>
		    <i style="color:#FF0000"><?php echo __("None"); ?></i>
	<?php } else {
			if(count($getallproj)=='1'){
				echo "<span style='color:#000;' title='".ucfirst($getallproj['0']['Project']['name'])."'>".$this->Format->shortLength(ucfirst($getallproj['0']['Project']['name']),20)."</span>";
			    $swPrjVal = $getallproj['0']['Project']['name'];
			}else{
                if($projUniq == 'all'){ ?>
                    <a href="javascript:void(0);" onclick="view_project_menu('<?php echo PAGE_NAME;?>');" data-toggle="dropdown" class="option-toggle" id="prj_ahref">
                        <div class="prjnm_ttc"><span id="pname_dashboard" class="ttc "><?php echo __("All"); ?></span></div>
                        <i class="caret"></i>
                    </a>
                <?php }else{ 
			    $swPrjVal = $this->Format->shortLength($projName,20); ?>
			<a href="javascript:void(0);" onclick="view_project_menu('<?php echo PAGE_NAME;?>');" data-toggle="dropdown" class="option-toggle" id="prj_ahref">
			    <div class="prjnm_ttc"><span id="pname_dashboard" class="ttc "><?php echo $this->Format->mb_ucfirst($getallproj['0']['Project']['name']); ?></span></div>
			    <i class="caret"></i>
			</a>
            <?php } ?>
			<div class="dropdown-menu lft popup" id="projpopup">
			    <center>
				<div id="loader_prmenu" style="display:none;">
				    <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __("loading..."); ?>" title="<?php echo __("loading..."); ?>"/>
				</div>
			    </center>
			    <?php if(count($getallproj) >= 6) { ?>
			    <div id="find_prj_dv" style="display: none;">
				<input type="text" placeholder="<?php echo __("Find a Project"); ?>" class="form-control pro_srch" onkeyup="search_project_menu('<?php echo PAGE_NAME;?>',this.value,event)" id="search_project_menu_txt">
				<i class="icon-srch-img"></i>
				<div id="load_find_dashboard" style="display:none;" class="loading-pro">
				    <img src="<?php echo HTTP_IMAGES;?>images/del.gif"/>
				</div>
			    </div>
			    <?php } ?>
			    <div id="ajaxViewProject" style='display:none;'></div>
				<div id="ajaxViewProjects"></div>
			</div>
	<?php } ?>
	<?php } ?>
	<?php }?>
	</li>
	<?php } ?>

        <?php if (CONTROLLER == "Ganttchart" && (PAGE_NAME == "manage" || PAGE_NAME == "ganttv2")) { ?>
           <?php if(SES_TYPE < 3){ ?>
            <span style="margin-left:50px;"><a style="color:#ff9600" href="javascript:void(0)" onclick="gantt_setting()"><?php echo __('Gantt Setting'); ?></a></span>
            <?php } ?>
        <?php } ?>

<?php if(CONTROLLER == "reports" && (PAGE_NAME == "glide_chart" || PAGE_NAME == "chart" || PAGE_NAME == "hours_report")) { ?>
	<li class="dropdown" id="prj_drpdwn"><?php echo __("Project"); ?>:
	<?php if((count($getallproj) == 0) && (SES_TYPE == 1 || SES_TYPE == 2) ) { ?>
		<a onclick="newProject()" href="javascript:void(0);"><i style="color: 2D678D; font-weight: bold;"> <?php echo __("Create Project"); ?></i></a>
		<!--<button onclick="newProject('menupj','loaderpj');">Create Project</button>-->
	<?php }else{
		 if(count($getallproj)=='0'){ ?>
		    --<?php echo __("None"); ?>--
	<?php } else {
	 if(count($getallproj)=='1'){
				echo $getallproj['0']['Project']['name'];
			    $swPrjVal = $getallproj['0']['Project']['name'];
			}else{
			    $swPrjVal = $this->Format->shortLength($projName,30); ?>
			<a href="javascript:void(0);" onclick="view_project_menu('<?php echo PAGE_NAME;?>');" data-toggle="dropdown" class="option-toggle" id="prj_ahref">
			    <span id="pname_dashboard" class="ttc"><?php echo isset($getallproj['0']['Project']['name'])?$getallproj['0']['Project']['name']:ucfirst($swPrjVal); ?></span>
			    <i class="caret"></i>
			</a>
			<div class="dropdown-menu lft popup" id="projpopup">
			    <center>
				<div id="loader_prmenu" style="display:none;">
				    <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __("loading..."); ?>" title="<?php echo __("loading..."); ?>"/>
				</div>
			    </center>
			    <?php if(count($getallproj) >= 6) { ?>
			    <div id="find_prj_dv" style="display: none;">
				<input type="text" placeholder="<?php echo __("Find a Project"); ?>" class="form-control pro_srch" onkeyup="search_project_menu('<?php echo PAGE_NAME;?>',this.value,event)" id="search_project_menu_txt">
				<i class="icon-srch-img"></i>
				<div id="load_find_dashboard" style="display:none;" class="loading-pro">
				    <img src="<?php echo HTTP_IMAGES;?>images/del.gif"/>
				</div>
			    </div>
			    <?php } ?>
			    <div id="ajaxViewProject" style='display:none;'></div>
				<div id="ajaxViewProjects"></div>
			</div>
	<?php } ?>
	<?php } ?>
	<?php }?>
	</li>
	<?php } ?>
	<?php if(PAGE_NAME=='dashboard'){?>
	<li  class="kanbn dashborad-view-type" id="select_view">
	<a href="<?php echo HTTP_ROOT.'dashboard#tasks';?>" onclick="checkHashLoad('tasks');"><div id="lview_btn" class="btn gry_btn kan30" title="<?php echo __("List View"); ?>"><i class="icon-list-view"></i></div></a>
	<a href="<?php echo HTTP_ROOT.'dashboard#tasks';?>" onclick="checkHashLoad('compactTask');"><div id="cview_btn" class="btn gry_btn kan30" title="<?php echo __("Compact View"); ?>"><i class="icon-compact-view"></i></div></a>
	<a href="<?php echo HTTP_ROOT.'dashboard#kanban';?>" onclick="checkHashLoad('kanban');"><div id="kbview_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Kanban View"); ?>"><i class="icon-kanv-view"></i></div></a>
	<a href="<?php echo HTTP_ROOT.'dashboard#activities';?>" onclick="checkHashLoad('activities');"><div id="actvt_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Activities"); ?>"><i class="icon-actvt-view"></i></div></a>
	<a href="<?php echo HTTP_ROOT.'dashboard#calendar';?>" onclick="calendarView('calendar');"><div id="calendar_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Calendar"); ?>"><img src="<?php echo HTTP_ROOT; ?>img/calendar.png" style="margin-top:-8px;margin-left:-2px"></img></div></a>
	</li>
	<?php } ?>
	<?php if($this->params['plugin'] == 'Timelog' && $this->params['controller'] == 'LogTimes' && $this->params['action']  == 'time_log'){?>
		<li class="kanbn dashborad-view-type" id="select_view">
			<a href="<?php echo HTTP_ROOT.'timelog';?>"><div id="lview_btn" class="btn gry_btn kan30" title="<?php echo __('List View'); ?>"><i class="icon-list-view"></i></div></a>
            <a href="<?php echo HTTP_ROOT.'timelog#calendar';?>" onclick="getCalenderForTimeLog('calendar');"><div id="calendar_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Calendar"); ?>"><img src="<?php echo HTTP_ROOT; ?>img/calendar.png" style="margin-top:-8px;margin-left:-2px"></img></div></a>
            <?php if(defined('GTLG') && GTLG == 1){ ?>
			<a href="<?php echo HTTP_ROOT.'timelog#chart';?>" onclick="getChartForTimeLog('chart');"><div id="chart_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Chart"); ?>"><img src="<?php echo HTTP_ROOT; ?>img/pie-icon.png" style="margin-top:-8px;margin-left:-2px"></img></div></a>
			<?php } ?>
		</li>

        <?php /* <li class="bcrumbtimelog" id="tasklogbreadcum" style="float: right;margin-right:133px;">
		<div class="fr filter_dt1 filter_analytics timelog-cal1">
            <div class="task_due_dt brcmbcal">
		<div class="fl icon-date-filter"></div>
		<div class="fl">
			<input type="text" class="smal_txt" placeholder="<?php echo __("From Date"); ?>" readonly  style="width:115px;height:30px;font-size:13px !important;background:#FFFF99;" id="logstrtdt" value="<?php echo $frm; ?>"/> <span>-</span>
			<input type="text" class="smal_txt" placeholder="<?php echo __("To Date"); ?>" readonly style="width:115px;height:30px;font-size:13px !important;background:#FFFF99;" id="logenddt" value="<?php echo $to; ?>"/>
		</div>
		<div class="fl" style="margin-left:10px;margin-top:2px;">
		<select class="form-control" id="rsrclog" style="height:30px;">
		<option value="">Select Resource</option>
		<?php foreach($rsrclist as $uid=>$uname) { ?>
                    <option value="<?php echo $uid; ?>"><?php echo $uname; ?></option>
            <?php } ?>
		</select>
		</div>
		<div class="fl apply_button">
			<div id="apply_btn" class="fl">
				<button class="btn btn_blue aply_btn" type="button" style="height:30px;" onclick= "showtimelog('datesrch');" value="Update" name="submit_Profile" id="submit_Profile"><?php echo __("Search"); ?></button>
			</div>
		</div>
	</div>
                </div>
            <div class="cb"></div>
	</li>  */ ?>
	<?php } ?>
	<li  class="kanbn dashborad-view-type" id="select_view_mlst" style="display: none;">
		<a href="<?php echo HTTP_ROOT.'dashboard#milestone';?>" onclick="checkHashLoad('milestone');" ><div id="mlview_btn" class="btn gry_btn kan30" title="<?php echo __("Manage Milestone"); ?>"><i class="icon-list-view"></i></div></a>
		<a href="<?php echo HTTP_ROOT.'dashboard#milestonelist';?>" onclick="checkHashLoad('milestonelist');"><div id="mkbview_btn" class="btn gry_btn kan30" style="border-radius:0 3px 3px 0"  title="<?php echo __("Milestone Kanban View"); ?>"><i class="icon-kanv-view"></i></div></a>
		<!--<a href="javascript:void(0);" onclick="addEditMilestone(this);" id="mlist_crt_mlstbtn" class="mlstlink_new" data-name="" data-uid="" data-id="">Create Milestone</a>-->
	<?php /*	<button style="margin-left:25px;" onclick="addEditMilestone(this);" id="mlist_crt_mlstbtn" type="button" value="Create Milestone" class="btn btn_blue">    <?php echo __("Create Milestone"); ?>   </button> */ ?>
	</li>
	
</ol>
</div>	
<div class="task_action_bar_div task_detail_head">
	<div class="task_action_bar">
		<button class="btn gry_btn task_detail_back" type="button" style="margin-left:18px;">
		<i class="icon-backto"></i><?php echo __("Go Back"); ?>
		</button>
		<div class="fr">
			<button class="btn gry_btn next" type="button" title="<?php echo __("Next"); ?>">

			<i class="icon-next"></i>
			</button>
		</div>
		<div class="fr">
			<button class="btn gry_btn prev" type="button" title="<?php echo __("Previous"); ?>">
			<i class="icon-prev"></i>
			</button>
		</div>
	</div><!-- Case Detail buttons -->
</div>
<div class="task_action_bar_div milestonekb_detail_head">
	<div class="task_action_bar">
		<button class="btn gry_btn task_detail_back" type="button" style="margin-left:18px;">
		<i class="icon-backto"></i><?php echo __("Go Back"); ?>
		</button>
	</div><!-- Case Detail buttons -->
</div>
<style>
/*.breadcrumb > li + li::before {background:none !important;}*/
.brcmbcal{line-height:30px;}
</style>