<?php ?>
<style type="text/css">
    #show_milestonelist .kanban-main .kanban-child{width:318px}
    #show_milestonelist .kbtask_div{width:95%}
</style>
<div id="detail_section"></div>
<div class="page-wrapper task_section" style="text-align: center;" id="filter_section">
    <div class="row"   id="filter_div_menu">
        <div class="filters">
<!--		<i class="db-filter-icon fl"></i>
                <div class="fl ftext">Filters:&nbsp;</div>-->
            <div class="fl task_section case-filter-menu " data-toggle="dropdown" title="<?php echo __('Task Filter');?>" onclick="openfilter_popup(0, 'dropdown_menu_all_filters');">
                <button type="button" class="btn tsk-menu-filter-btn flt-txt">
                    <i class="icon_flt_img"></i>
					<?php echo __("Filters"); ?>
                    <i class="icon-filter-right"></i>
                </button>
                <ul class="dropdown-menu" id="dropdown_menu_all_filters" style="position: absolute;">
                    <li class="pop_arrow_new"></li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Time');?>" data-toggle="dropdown" onclick="allfiltervalue('date');"> <?php echo __("Time"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_date_div">
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_date"></ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Due Date');?>" data-toggle="dropdown" onclick="allfiltervalue('duedate');"> <?php echo __("Due Date"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_duedate_div">
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_duedate"></ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Status');?>" data-toggle="dropdown" onclick="allfiltervalue('status');"><?php echo __("Status"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_status_div">
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_status"></ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Types');?>" data-toggle="dropdown" onclick="allfiltervalue('types');"><?php echo __("Types"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_types_div" >
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_types"></ul>
                        </div>

                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Priority');?>" data-toggle="dropdown" onclick="allfiltervalue('priority');"><?php echo __("Priority"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_priority_div" >
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_priority"></ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Users');?>" data-toggle="dropdown" onclick="allfiltervalue('users');"><?php echo __("Created by"); ?> </a>
                        <div class="dropdown_status" id="dropdown_menu_users_div" >
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_users"></ul>
                        </div>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Assign To');?>" data-toggle="dropdown" onclick="allfiltervalue('assignto');"><?php echo __("Assign To"); ?></a>
                        <div class="dropdown_status" id="dropdown_menu_assignto_div" >
                            <i class="status_arrow_new"></i>
                            <ul class="dropdown-menu" id="dropdown_menu_assignto"></ul>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="fl" id="filtered_items" style="padding-left:10px;" ></div>
            <!-- Filter options ends-->
            <div class="filter_btn_section fl" id="savereset_filter">
                <!--				<div style="display:none;" id="savefilter_btn" class="fl"  >
                                                        <div class="db-filter-save-icon fl" onClick="showSaveFilter();" title="Save Filter" rel="tooltip"></div>
                                                         <div id="inner_save_filter" class="sml_popup_bg">
                                                                <div>
                                                                        <div class="popup_title smal">
                                                                                <span>Save Custom Filter</span>
                                                                        </div>
                                                                        <div class="popup_form smal_form">
                                                                            <table cellpadding="0" cellspacing="0" class="col-lg-12" id="inner_save_filter_td">
                                                                                        <tr>
                                                                                                <td colspan="2">
                                                                                                        <span id="loaderpj" style="display:block;">
                                                                                                                <center>
                                                                                                                <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..." />
                                                                                                                </center>
                                                                                                        </span>
                                                                                                </td>
                                                                                        </tr>
                                                                            </table>
                                                                        </div>
                                                                </div>
                                                         </div>
                                                </div>-->
                <div class="fl db-filter-reset-icon" style="display:none;" id="reset_btn" title="<?php echo __('Reset Filters');?>" rel="tooltip" onClick="resetAllFilters('all');"></div>
            </div>
            <div class="fl task_section case-filter-menu taskgroupby-div" data-toggle="dropdown" title="<?php echo __('Task Filter');?>" onclick="openfilter_popup(0, 'dropdown_menu_groupby_filters');">
                <button type="button" class="btn tsk-menu-sortgroup-btn flt-txt" >
                    <i class="icon_groupby_img"></i><?php echo __("Group by"); ?><i class="icon-filter-right"></i>
                </button>
                <ul class="dropdown-menu" id="dropdown_menu_groupby_filters" style="position: absolute;">
                    <li class="pop_arrow_new"></li>
                    <!--				<li>
                                                            <a href="javascript:jsVoid();" title="Time" data-toggle="dropdown" onclick="groupby('crtdate');"> Created Date</a>
                                                    </li>-->
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Due Date');?>" data-toggle="dropdown" onclick="groupby('duedate');"> <?php echo __("Due Date"); ?></a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Status');?>" data-toggle="dropdown" onclick="groupby('status');"><?php echo __("Status"); ?></a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Priority');?>" data-toggle="dropdown" onclick="groupby('priority');"><?php echo __("Priority"); ?></a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();" title="<?php echo __('Priority');?>" data-toggle="dropdown" onclick="groupby('assignto');"><?php echo __("Assigned to"); ?></a>
                    </li>
                </ul>
            </div>
            <div class="fl" id="groupby_items"></div>
            <div class="fl task_section case-filter-menu tasksortby-div " data-toggle="dropdown" >
                <button type="button" class="btn tsk-menu-sortgroup-btn flt-txt sortby_btn <?php if(isset($_COOKIE['TASKGROUPBY']) && ($_COOKIE['TASKGROUPBY']!='date')){?>disable-btn<?php }?> " onclick="openfilter_popup(0, 'dropdown_menu_sortby_filters');" <?php if(isset($_COOKIE['TASKGROUPBY']) && ($_COOKIE['TASKGROUPBY']!='date')){?>disabled="disabled"<?php }?>>
                    <i class="icon_sortby_img"></i><?php echo __("Sort by"); ?><i class="icon-filter-right"></i>
                </button>
                <ul class="dropdown-menu" id="dropdown_menu_sortby_filters" style="position: absolute;">
                    <li class="pop_arrow_new"></li>
                    <li>
                        <a href="javascript:jsVoid();"  title="<?php echo __("Title"); ?>" data-toggle="dropdown" onclick="ajaxSorting('title');"><?php echo __("Title"); ?></a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();"  title="<?php echo __("Task"); ?>#" data-toggle="dropdown" onclick="ajaxSorting('caseno');"><?php echo __("Task"); ?>#</a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();"  title="<?php echo __("Due Date"); ?>" data-toggle="dropdown" onclick="ajaxSorting('duedate');"> <?php echo __("Due Date"); ?></a>
                    </li>
                    <li>
                        <a href="javascript:jsVoid();"  title="<?php echo __("Assigned to"); ?>" data-toggle="dropdown" onclick="ajaxSorting('caseAt');"><?php echo __("Assigned to"); ?></a>
                    </li>
            <?php if(defined('TSG') && TSG == 1){ ?>
                    <li>
                        <a href="javascript:jsVoid();"  title="<?php echo __("Status"); ?>" data-toggle="dropdown" onclick="ajaxSorting('Status');"><?php echo __("Status"); ?></a>
                    </li>
            <?php } ?>
                </ul>
            </div>
            <div class="fl" id="sortby_items"></div>
            <div class="cb"></div>
        </div>
    </div>
    <div class="cb"></div>
    <!-- /.row --><!-- Task filters -->
</div><!-- /.page-wrapper -->
<div class="media-overflow">
    <table cellpadding="0" cellspacing="0" width="100%" class="task_section dashbod_tbl_m10 fixed_layout m-width-800">
        <tr>
            <td id="topaction" class="">
                <!--Tabs section starts -->
                <div style="display:block;border:0px solid #FF0000;" class="tab" id="topactions">
                    <ul id="myTab4" class="nav-tabs">

		<?php
		if (ACT_TAB_ID && ACT_TAB_ID > 1) {
		    $tablists = Configure::read('DTAB');
		    foreach ($tablists AS $tabkey => $tabvalue) {
			if ($tabkey & ACT_TAB_ID) {
			    $default_actv = "";
			    if($tabvalue["fkeyword"] == "cases") { $tab_spn_id = "tskTabAllCnt"; $default_actv = "active";}
				elseif($tabvalue["fkeyword"] == "assigntome") { $tab_spn_id = "tskTabMyCnt"; }
				elseif($tabvalue["fkeyword"] == "delegateto") { $tab_spn_id = "tskTabDegCnt"; }
				elseif($tabvalue["fkeyword"] == "highpriority") { $tab_spn_id = "tskTabHPriCnt"; }
				elseif($tabvalue["fkeyword"] == "overdue") { $tab_spn_id = "tskTabOverdueCnt"; }
			    ?>
                        <li class="<?php echo $default_actv;?>">
                            <a class="cattab"  id="<?php echo $tabvalue["fkeyword"]; ?>_id" onclick="caseMenuFileter('<?php echo $tabvalue["fkeyword"]; ?>', 'dashboard', 'cases', '');" data-toggle="tab" title="<?php print $tabvalue["ftext"];?>">
                                <div class="fl <?php echo $tabvalue["fkeyword"];?>"></div>
                                <div class="fl"><div class="fl ellipsis-view maxWidth90"><?php echo $tabvalue["ftext"]; ?></div><span class="fl" id="<?php echo $tab_spn_id;?>"></span></div>
                                <div class="cbt"></div>
                            </a>
                        </li>
			<?php } ?>
		    <?php } ?>
                        <li class="pop_li">

                            <a href="javascript:void(0);" class="select_button_ftop" onclick="newcategorytab();" rel="tooltip" title="<?php echo __("Tab Settings"); ?>">
                                <div class="tab_pop">+</div>
                            </a>
                        </li>
                        <div style="clear:both"></div>
		<?php } ?>
                    </ul>
                </div>
                <!--Tabs section ends -->
            </td>
        </tr>
        <tr>
            <td>
                <!--Task listing section starts here-->
                <div id="caseViewSpanUnclick">
                    <div id="caseViewDetails" style="display:none"></div>
                    <div id="caseViewSpan" style="display:block"></div>
                    <div id="task_paginate" style="display:block"></div>
                </div>
                <!--Task listing section ends here-->
            </td>
        </tr>
    </table>
</div><!-- Tab and task list -->

<div id="caseFileDv" style="display:block"></div>
<div id="caseKanbanDv"  style="text-align: center;display:block;position: absolute;margin-left: 20px;margin-top: -20px;">
    <div id="show_search_kanban" class="global-srch-res fl"></div><div style="float:left;text-align:center;margin-top:5px;" id="resetting_kanban"></div>
</div>
<div id="kanban_list" class="kanban_section kanban_resp" style="display:block"></div>
<div id="calendar_view" class="calendar_section calendar_resp" style="display:block;margin-top: 12px;"></div>
<div id="caseLoader">
    <div class="loadingdata"><?php echo __("Loading..."); ?></div>
</div>

<!--Task activities section start here-->
<div class="page-wrapper" id="actvt_section" style="display:none">
    <div class="col-lg-9 fl m-left-20 activity_ipad">
        <div id="activities"></div>
        <div style="display:none;" id="more_loader" class="morebar">
            <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="<?php echo __("loading..."); ?>" title="<?php echo __("loading..."); ?>"/>
        </div>
    </div>
    <div class="col-lg-3 fl act_rt_div">
        <div class="tab tab_comon tab_task">
            <ul class="nav-tabs activ_line mod_wide">
                <li class="active">
                    <a href="javascript:void(0);" id="myTab" onclick="myactivities('myTab', 'delegatedTab');">
                        <div class="fl" ><?php echo __("My"); ?></div>
                        <div class="cbt"></div>
                    </a>
                </li>
                <li id="file_li">
                    <a href="javascript:void(0);"  id="delegatedTab" onclick="delegateactivities('myTab', 'delegatedTab');">
                        <div class="fl"><?php echo __("Delegated"); ?></div>
                        <div class="cbt"></div>
                    </a>
                </li>
                <div class="cbt"></div>
            </ul>
        </div>
        <div class="cb"></div>

        <div id="Upcoming"></div>
        <div id="moreOverdueloader" class="moreOverdueloader"><?php echo __("Loading Tasks..."); ?></div>
        <hr/>
        <div id="Overdue"></div>
        <hr/>
        <div id="PieChart" style="display: none;"></div>
    </div>
    <div class="cb"></div>
</div>
<div class="cb"></div>
<input type="hidden" id="displayed" value="30">
<!--Task activities section ends here-->
<!-- Milestone Listing Start -->
<div id="caseMilestoneDv"  style="text-align: center;display:block;position: absolute;margin-left: 20px;margin-top: 0px;">
    <div id="show_search" class="global-srch-res fl"></div><div style="float:left;text-align:center;margin-top:5px;" id="resetting"></div>
</div>
<div class="cb"></div>

<div id="milestone_content" >
    <div id="manage_milestone" style="display: none;">
        <div class="tab tab_comon" id="mlsttab" >
            <ul class="nav-tabs mod_wide">
                <li class="active" id="mlstab_act">
                    <a href="javascript:void(0);" onclick="ManageMilestoneList(1)" >
                        <div class="fl act_milestone"></div>
                        <div class="fl"><?php echo __("Active"); ?></div>
                        <div class="cbt"></div>
                    </a>
                </li>
                <li id="mlstab_cmpl">
                    <a href="javascript:void(0);" onclick="ManageMilestoneList(0)" >
                        <div class="fl mt_completed"></div>
                        <div class="fl"><?php echo __("Completed"); ?></div>
                        <div class="cbt"></div>
                    </a>
                </li>
                <div class="cbt"></div>
            </ul>
        </div><br />
        <div id="manage_milestone_list"></div>
        <div id="milestone_paginate" style="margin-right: 3%;"></div>
    </div>


    <div id="milestonelisting">
        <div id="manage_milestonelist" style="display: none;">
            <div class="tab tab_comon" id="mlsttab">
                <ul class="nav-tabs mod_wide">
                    <li class="active" id="mlstab_act_kanban">
                        <a href="javascript:void(0);" onclick="showMilestoneList('', 1)" >
                            <div class="fl act_milestone"></div>
                            <div class="fl"><?php echo __("Active"); ?></div>
                            <div class="cbt"></div>
                        </a>
                    </li>
                    <li id="mlstab_cmpl_kanban">
                        <a href="javascript:void(0);" onclick="showMilestoneList('', 0)" id="completed_tab" >
                            <div class="fl mt_completed"></div>
                            <div class="fl"><?php echo __("Completed"); ?></div>
                            <div class="cbt"></div>
                        </a>
                    </li>
                    <div class="cbt"></div>
                </ul>
            </div>
            <br />
        </div>
        <div id="show_milestonelist"></div>
        <div class="milestonenextprev" style="display: none;" >
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
        </div>
    </div>
</div>

<!-- Milestone Listing End -->

<script type="text/template" id="case_project_tmpl">
<?php echo $this->element('case_project'); ?>
</script>
<!--<script type="text/template" id="case_project_tmpl">
<?php //echo $this->element('compact_view'); ?>
</script>-->
<script type="text/template" id="kanban_task_tmpl">
<?php echo $this->element('kanban_task'); ?>
</script>
<script type="text/template" id="paginate_tmpl">
<?php echo $this->element('paginate'); ?>
</script>
<script type="text/template" id="case_details_tmpl">
<?php echo $this->element('case_details'); ?>
</script>
<script type="text/template" id="case_replies_tmpl">
<?php echo $this->element('case_reply'); ?>
</script>
<script type="text/template" id="case_widget_tmpl">
<?php echo $this->element('ajax_case_status'); ?>
</script>

<script type="text/template" id="case_files_tmpl">
<?php echo $this->element('case_files'); ?>
</script>

<script type="text/template" id="date_filter_tmpl">
<?php echo $this->element('date_filter'); ?>
</script>

<script type="text/template" id="duedate_filter_tmpl">
<?php echo $this->element('duedate_filter'); ?>
</script>
<script type="text/template" id="ajax_activity_tmpl">
    <?php echo $this->element("../Users/json_activity");?>
</script>
<script type="text/template" id="manage_milestone_tmpl">
<?php echo $this->element('manage_milestone'); ?>
</script>
<script type="text/template" id="milestonelist_tmpl">
<?php echo $this->element('ajax_milestonelist'); ?>
</script>
<script type="text/template" id="project_template_tmpl">
<?php echo $this->element('project_template_form'); ?>
</script>
<input type="hidden" name="checktype" id="checktype" value="" size="10" readonly="true">
<input type="hidden" name="caseStatus" id="caseStatus" value="<?php echo $caseStatus; ?>" size="10" readonly="true">
<input type="hidden" name="caseStatusprev" id="caseStatusprev" value="" size="10" readonly="true">
<input type="hidden" name="priFil" id="priFil" value="<?php echo $priorityFil; ?>" size="14" readonly="true"/>
<input type="hidden" name="caseTypes" id="caseTypes" value="<?php echo $caseTypes; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseMember" id="caseMember" value="<?php echo $caseUserId; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseAssignTo" id="caseAssignTo" value="<?php echo $caseAssignTo; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseSearch" id="caseSearch" value="<?php echo $caseSearch; ?>" size="4" readonly="true"/>
<input type="hidden" name="mlstPage" id="mlstPage" value="1" size="4" readonly="true"/>
<input type="hidden" name="caseId" id="caseId" value="<?php //echo $caseUniqId; ?>" size="14" readonly="true"/>
<input type="hidden" name="caseDate" id="caseDate" value="<?php echo $caseDate; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseTitle" id="caseTitle" value="<?php echo $caseTitle; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseDueDate" id="caseDueDate" value="<?php echo $caseDueDate; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseCreatedDate" id="caseCreatedDate" value="<?php echo $caseCreatedDate; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseNum" id="caseNum" value="<?php echo $caseNum; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseLegendsort" id="caseLegendsort" value="<?php echo $caseLegendsort; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseAtsort" id="caseAtsort" value="<?php echo $caseAtsort; ?>" size="4" readonly="true"/>
<input type="hidden" name="isSort" id="isSort" value="<?php echo $isSort; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseStart" id="caseStart" value="" size="4" readonly="true"/>
<input type="hidden" name="caseChangeType" id="caseChangeType" value="" size="4" readonly="true"/>
<input type="hidden" name="caseChangePriority" id="caseChangePriority" value="" size="4" readonly="true"/>
<input type="hidden" name="caseChangeAssignto" id="caseChangeAssignto" value="" size="4" readonly="true"/>
<input type="hidden" name="caseChangeDuedate" id="caseChangeDuedate" value="" size="4" readonly="true"/>
<input type="hidden" name="caseResolve" id="caseResolve" value="" size="4" readonly="true"/>
<input type="hidden" name="clearCaseSearch" id="clearCaseSearch" value="" size="4" readonly="true"/>
<input type="hidden" name="caseMenuFilters" id="caseMenuFilters" value="<?php echo $caseMenuFilters?$caseMenuFilters:'cases'; ?>" size="4" readonly="true"/>
<input type="hidden" name="customFIlterId" id="customFIlterId" value="" size="4" readonly="true"/>

<input type="hidden" name="milestoneIds" id="milestoneIds" value="<?php echo $milestoneIds; ?>" size="4" readonly="true"/>

<input type="hidden" name="caseDetailsSorting" id="caseDetailsSorting" value="<?php echo $caseDtlsSort; ?>" size="4" readonly="true"/>
<input type="hidden" name="urllvalueCase" id="urllvalueCase" value="<?php echo $urllvalueCase; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseUrl" id="caseUrl" value="<?php echo $caseUrl; ?>" size="4" readonly="true"/>
<input type="hidden" name="caseDateFil" id="caseDateFil" value="<?php echo $caseDateFil; ?>" size="4" readonly="true"/>
<input type="hidden" name="casedueDateFil" id="casedueDateFil" value="<?php echo $casedueDateFil; ?>" size="4" readonly="true"/>

<input type="hidden" name="prvhash" id="prvhash" value="" readonly="true"/>
<input type="hidden" name="milestoneUid" id="milestoneUid"   readonly="true"  value=''/>
<!-- Used for switching from milestone list to kanban task list and Accordingly counter changed -->
<input type="hidden" name="milestoneUid" id="milestoneId"   readonly="true"  value=''/>
<!-- differentiate between list view and Compact View -->
<input type="hidden" name="lviewtype" id="lviewtype"   readonly="true"  value='<?php echo $_COOKIE['LISTVIEW_TYPE'];?>'/>

<input type="hidden" id="last_project_id" value="">
<input type="hidden" id="last_project_uniqid" value="">
<input type="hidden" value="0" id="totalMlstCnt" readonly="true"/>
<input type="hidden" value="0" id="milestoneLimit" readonly="true"/>
<input type="hidden" value="1" id="mlsttabvalue" readonly="true"/>
<input type="hidden" value="milestone" id="refMilestone" readonly="true"/>
<input type="hidden" id="storeIsActive">
<input type="hidden" id="storeIsActivegrid">
<input type="hidden" id="view_type" value="kanban">
<input type="hidden" id="search_text">
<script type="text/javascript">
    $(document).ready(function (event) {
        if (PT && PT == 1) {
            po.init();
        }
        $(document).click(function (e) {
            if ($(e.target).is(".filter_opn")) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                $('#dropdown_menu_all_filters').hide();
                $('#dropdown_menu_sortby_filters').hide();
                $('#dropdown_menu_groupby_filters').hide();
                $('.dropdown_status').hide();
            }
        });
    });
    $(".proj_mng_div .contain").hover(function () {
        $(this).find(".proj_mng").stop(true, true).animate({
            bottom: "0px",
            opacity: 1
        }, 400);
    }, function () {
        $(this).find(".proj_mng").stop(true, true).animate({
            bottom: "-42px",
            opacity: 0
        }, 400);
    });
    $(document).on('click', '.milestonenextprev .prev', function () {
        //$('#milestoneLimit').val(parseInt($('#milestoneLimit').val())-6);
        var isActive = ($('#storeIsActive').val() != '') ? $('#storeIsActive').val() : 1;
        var search_key = $('#search_text').val();
        showMilestoneList('prev', isActive, 1, search_key);
    });
    $(document).on('click', '.milestonenextprev .next', function () {
        var isActive = ($('#storeIsActive').val() != '') ? $('#storeIsActive').val() : 1;
        var search_key = $('#search_text').val();
        showMilestoneList('next', isActive, 1, search_key);
    });
    po = {
        clone_item: null,
        clone_item_mlstask: null,
        cnt: 0,
        mlstask_cnt: 0,
        payload: '',
        parent_table: $('.new_proj_temp_new_tab'),
        mlstn_data: {},
        all_users : {},
        all_tasks : [],
        show_depends : false,
        init: function () {
            $.post(HTTP_ROOT + "projecttemplate/ProjectTemplates/getCompanyUsers",{},function(res){
                po.all_users=res;            
            },'json');
            this.clone_item = $('.pr_templ_mls').filter(':hidden').clone();
            this.clone_item_mlstask = $('.pr_templ_tsks').filter(':hidden').clone();
            $(document).on('click', '.add-more-ptmilestone', function (event) {
                var unsaved_mlstn = !1;
                $.each(po.mlstn_data, function (index, val) {
                    if (po.empty(val)) {
                        unsaved_mlstn = !0;
                    }
                });
                (!unsaved_mlstn) && po.addMoreMls();
            });
            $(document).on('click', '.add-more-pttask', function (event) {
                var num = po.get_row_count(),
                        unsaved_mlstn_tsk = !1;
                if (!num) {
                    po.addMoreTsks();
                } else {
                    $.each(po.mlstn_data[num]['CaseDetail'], function (index, val) {
                        po.empty(val) && (unsaved_mlstn_tsk = !0)
                    });
                    (!unsaved_mlstn_tsk) && po.addMoreTsks();
                }
            });
            $(document).on('click', '.mlstn-task-add', function (event) {
                var mlst = $(this).attr('data-mlstnno');
                var unsaved_mlstn_tsk = !1;
                var clps_el = $(this).closest('div').find('.clpse-icon');
                !$(clps_el).hasClass("opened") && po.hideshowmlstns($(clps_el));
                $.each(po.mlstn_data[mlst]['CaseDetail'], function (index, val) {
                    po.empty(val) && (unsaved_mlstn_tsk = !0)
                });
                (!unsaved_mlstn_tsk) && po.addMoremlsTsks(mlst);
            });
            $(document).on('click', '.clpse-icon', function (event) {
                po.hideshowmlstns($(this));
            });
            $(".create_proj_temp_new-btn").click(function () {
                po.openpopup('new');
            });
            po.cnt = $('[id^=pr_tm_ml_det_]').length;
            po.mlstask_cnt = $('[id^=pr_tm_ml_tsk_det_]').length;
            $(document).on("mouseenter", ".mlstn-div", function () {
                $(this).find('.edit-span').removeClass('dn')
                $(this).find('.dlt-span').removeClass('dn')
            });
            $(document).on("mouseleave", ".mlstn-div", function () {
                $(this).find('.edit-span').addClass('dn')
                $(this).find('.dlt-span').addClass('dn')
            });
            $(document).on("mouseenter", ".edt-task-div", function () {
                $(this).find('.edit-span').removeClass('dn')
                $(this).find('.dlt-span').removeClass('dn')
            });
            $(document).on("mouseleave", ".edt-task-div", function () {
                $(this).find('.edit-span').addClass('dn')
                $(this).find('.dlt-span').addClass('dn')
            });
        },
        hideshowmlstns: function (obj) {
            if ($(obj).hasClass('opened')) {
                $(obj).removeClass('opened');
                $(obj).parent('div').next('div').hide();
                $(obj).find('span').removeClass('glyphicon-minus-sign').addClass('glyphicon-plus-sign');
            } else {
                $(obj).addClass('opened');
                $(obj).parent('div').next('div').show();
                $(obj).find('span').removeClass('glyphicon-plus-sign').addClass('glyphicon-minus-sign');
            }
        },
        openpopup: function (type, casepage) {
            closePopup();
            po.mlstn_data = {};
            var cbval = '';
            var case_id = new Array();
            var spval = '';
            var case_no = new Array();
            var mlstn_onj = {};
            if (type == "new") {
                if ($('#projFil').val() !== 'all') {
                    $('.nw-pr-tempmlstn').hide();
                    openPopup();
                    $('#pr_templat_form').html('');
                    $("#proj_temp_new_err").html('');
                    $(".proj_temp_new_popup_ch").show();
                    $('.loader_dv').show();
                    $('input[id^="actionChk"]').each(function (i) {
                        if ($(this).is(":checked") && !($(this).is(":disabled"))) {
                            cbval = $(this).val();
                            spval = cbval.split('|');
                            case_id.push(spval[0]);
                            case_no.push(spval[1]);
                        }
                    });
                    $(".nw-pr-tempmlstn").hide();
                    $('#inner_mlstn-nw-pr-mlstn').html('');
                    $("#addeditMlst-nw-pr-mlstn").show();
                    $.post(HTTP_ROOT + "projecttemplate/ProjectTemplates/getCases", {
                        "case_id": case_id
                    }, function (res) {
                        $('.loader_dv').hide();
                        $('#templa-btn-status').html('Save');
                        mlstn_onj['ProjectTemplate'] = {
                            'id': "",
                            'module_name': "",
                            'is_default': 1
                        };
                        mlstn_onj['ProjectTemplateMilestone'] = [{
                                'id': "",
                                'title': "Default milestone",
                                'is_default': 1,
                                'title': "",
                                        'description': "",
                                'start_date': "",
                                'end_date': '',
                                'ProjectTemplateCase': []
                            }];
                        $.each(res, function (ok7index, val) {
                            var obj = {};
                            obj.title = val.title;
                            obj.description = val.description;
                            obj.start_date = '';
                            obj.end_date = '';
                            obj.id = '';
                            obj.is_dummy = true;
                            mlstn_onj['ProjectTemplateMilestone'][0]['ProjectTemplateCase'].push(obj);
                        });
                        $('#pr_templat_form').html(tmpl("project_template_tmpl", mlstn_onj));
                        $("#module_name").val('');
                        $("#module_name").focus();
                        var data_obj = {};
                        $('[id^=pr_tm_ml_form_]').each(function (index, el) {
                            data_obj[index] = {
                                'title': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][title]']").val(),
                                'start_date': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][start_date]']").val(),
                                'end_date': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][end_date]']").val(),
                                'mlstn_desc': $("textarea[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][description]']").val(),
                                'CaseDetail': {},
                                'mlstn_status': parseInt($("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][is_default]']").val())
                            };
                            $('#pr_tm_ml_det_' + index).find('.mlstn-task-rows').each(function (i, e) {
                                data_obj[index]['CaseDetail'][i] = {};
                                data_obj[index]['CaseDetail'][i] = {
                                    'title': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][title]']").val(),
                                    'start_date': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][start_date]']").val(),
                                    'end_date': $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][end_date]']").val(),
                                    'mlstn_desc': $("textarea[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][description]']").val()
                                };
                            });
                            $('.datepicker').each(function (index, el) {
                                var id = $(this).attr('id');
                                var fld = $(this).attr('data-fldname');
                                $('#' + id).datepicker({
                                    dateFormat: 'M d, yy',
                                    changeMonth: false,
                                    changeYear: false,
                                    hideIfNoPrevNext: true
                                });
                            });
                        });
                        po.mlstn_data = data_obj;
                        po.cnt = $('[id^=pr_tm_ml_det_]').length;
                        po.mlstask_cnt = $('[id^=pr_tm_ml_tsk_det_]').length;
                    }, 'json');
                } else {
                    return false;
                }
            }
            $(".popup_bg").css({
                "width": '750px'
            });
        },
         _setDependsDropdown:function (num,edit){
            po.show_depends=false;  
            po.all_tasks = [];      

                num = num -1;
                $("input[data-fldname='title']" ).each(function( index ) {
                    if($(this).val() !=''){                 
                        po.show_depends = true;
                        if(typeof $(this).closest('td').find("input[data-fldname='id']") !='undefined'
                        &&
                        $(this).closest('td').find("input[data-fldname='id']").val() != ''){
                            po.all_tasks[$(this).closest('td').find("input[data-fldname='id']").val()]=$(this).val();
                        }else{
                            po.all_tasks.push($(this).val());
                        }
                    }
                  });
             if(po.show_depends){
                var notSetDepends =true;
                $( "#pr_tm_ml_tsk_form_"+num).find(".depends_row").show();
                 $.each( po.all_tasks , function( i, value ) {
                   if(typeof value != 'undefined'){   
                   if(typeof edit != 'undefined'){
                       dvalue= $( "#pr_tm_ml_tsk_form_"+num).find(".depends").val();
                       if(i==dvalue){
                           notSetDepends= false;
                           $( "#pr_tm_ml_tsk_form_"+num).find(".tsk_depends_p").html(ucfirst(formatText(value)));  
                       }
                   }              
                    $( "#pr_tm_ml_tsk_form_"+num).find('.more_opt_depends ul').append('<li><a href="javascript:jsVoid()" onclick="po._depends(' + i + ',this,\''+ucfirst(formatText(value))+'\');" value="' + i + '"><span class="value">' + i + '</span>' + ucfirst(formatText(value)) + '</a></li>');
                    }
                 });
                 if(notSetDepends){             
                    $( "#pr_tm_ml_tsk_form_"+num).find(".tsk_depends_p").html(ucfirst(formatText("Select"))); 
                 }

             }

        },
        addMoreMls: function () {
            var itm = po.clone_item.clone();
            itm.find('input').val('');
            itm.find("[data-mlstnno='']").attr('data-mlstnno', po.cnt);
            itm.find('input').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/\d/g, po.cnt));
                if ($(this).hasClass('datepicker')) {
                    ($(this).attr('id') == "pr_tmpl_mls_start_date") ? $(this).attr('id', 'pr_tmpl_mls_start_date_' + po.cnt) : $(this).attr('id', 'pr_tmpl_mls_end_date_' + po.cnt);
                }
            });
            itm.find("[data-mlstnno='']").attr('data-mlstnno', po.cnt);
            itm.find('textarea').each(function () {
                $(this).attr('name', $(this).attr('name').replace(/\d/g, po.cnt));
            });
            var new_el = '<tr id="pr_tm_ml_form_' + po.cnt + '" class="mlstn-rows"><td colspan="2"><center><div id="proj_temp_new_mlstn_err_' + po.cnt + '" class="fnt_clr_rd" style="display:block;font-size:15px;"></div></center><table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_tsksss">' + $(itm).html() + '</table></td></tr>';
            $('.new_proj_temp_new_tab').find('#parent-mlsts-tbody').append($(new_el));
            $("#pr_templat_form").animate({
                scrollTop: $('#pr_tm_ml_form_' + po.cnt).offset().top
            }, 2000);
            po.mlstn_data[po.cnt] = {};
            $('#pr_tmpl_mls_start_date_' + po.cnt).removeClass('hasDatepicker');
            $('#pr_tmpl_mls_end_date_' + po.cnt).removeClass('hasDatepicker');
            $('#pr_tmpl_mls_start_date_' + po.cnt).datepicker({
                dateFormat: 'M d, yy',
                changeMonth: false,
                changeYear: false,
                minDate: 0,
                hideIfNoPrevNext: true,
                onSelect: function (dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    var m = new Date(date);
                    var n = m.setDate(m.getDate() + 1);
                    $("#pr_tmpl_mls_end_date_" + po.cnt).datepicker("option", "minDate", new Date(n));
                }
            });
            $("#pr_tmpl_mls_end_date_" + po.cnt).datepicker({
                dateFormat: 'M d, yy',
                changeMonth: false,
                changeYear: false,
                minDate: 0,
                hideIfNoPrevNext: true,
                onSelect: function (dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    var m = new Date(date);
                    var n = m.setDate(m.getDate() + 1);
                    $("#pr_tmpl_mls_start_date_" + po.cnt).datepicker("option", "maxDate", new Date(n))
                }
            });
            po.cnt++;
        },
        validateMstn: function (obj, mlstn_cntr, mlstn_id) {
            var frm_no = $(obj).attr('data-mlstnno');
            var exist_attr = $(obj).attr('data-state');
            var form_tr = $('#pr_tm_ml_form_' + frm_no);
            var title = $(form_tr).find("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + frm_no + "][title]']").val();
            var start_date = $(form_tr).find("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + frm_no + "][start_date]']").val();
            var end_date = $(form_tr).find("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + frm_no + "][end_date]']").val();
            var mlstn_desc = $(form_tr).find("textarea[name='data[ProjectTemplate][ProjectTemplateMilestone][" + frm_no + "][description]']").val();
            var errMsg;
            var done = 1;
            if (title.trim() == "") {
                errMsg = '<?php echo __("Title cannot be left blank!"); ?>';
                $('#title').focus();
                done = 0;
            } else if (start_date.trim() != "" && end_date.trim() == "") {
                errMsg = '<?php echo __("End date cannot be left blank!"); ?>';
                $('#pr_tmpl_mls_start_date_' + frm_no).focus();
                done = 0;
            } else if (end_date.trim() != "" && start_date.trim() == "") {
                errMsg = '<?php echo __("Start date cannot be left blank!"); ?>';
                $('#pr_tmpl_mls_end_date_' + frm_no).focus();
                done = 0;
            } else if (start_date.trim() != "" && end_date.trim() != "" && Date.parse(start_date) > Date.parse(end_date)) {
                errMsg = '<?php echo __("Start Date cannot exceed End Date!"); ?>';
                $('#pr_tmpl_mls_end_date_' + frm_no).focus();
                done = 0;
            }
            if (done == 0) {
                $('#proj_temp_new_mlstn_err_' + frm_no).html(errMsg).show();
            } else {
                $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + frm_no + "][is_default]']").val(0)
                if (!po.empty(po.mlstn_data[frm_no]['CaseDetail'])) {
                    po.mlstn_data[frm_no]['title'] = title;
                    po.mlstn_data[frm_no]['start_date'] = start_date;
                    po.mlstn_data[frm_no]['end_date'] = end_date;
                    po.mlstn_data[frm_no]['mlstn_desc'] = mlstn_desc;
                } else {
                    po.mlstn_data[frm_no] = {
                        'title': title,
                        'start_date': start_date,
                        'end_date': end_date,
                        'mlstn_desc': mlstn_desc,
                        'CaseDetail': {}
                    };
                }
                var new_el = '<tr id="pr_tm_ml_det_' + frm_no + '" class="mlstn-rows"><td></td><td style="text-align:left;"><div style="position:relative;" class="mlstn-div"><span class="clpse-icon opened"><span class="glyphicon glyphicon-minus-sign"></span></span>' + po.mlstn_data[frm_no]['title'] + '<span class="ct_icon act_edit_task pr-temp-mlstn-edit-icon edit-span dn" onclick="po._editmlstn(' + frm_no + ');"></span>';
                new_el += '<span class="act_icon act_del_task pr-temp-mlstn-delete-icon dlt-span dn" onclick="po._dltmlstn(' + frm_no + ');"></span>';
                new_el += '<a href="#" class="mlstn-task-add pr-temp-mlstn-add-link" data-mlstnno="' + frm_no + '">+ Add Task</a></div><div><table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_mlstn_tsks" style="display:none;" id="tmpl_mlstns_task_form_' + frm_no + '"><tbody id="tmpl_mlstns_task_form_tbody_' + frm_no + '">';
                if (!po.empty(po.mlstn_data[frm_no]['CaseDetail'])) {
                    var existing_tasks = $('#tmpl_mlstns_task_form_tbody_' + frm_no).html();
                    new_el += existing_tasks;
                }
                new_el += '</tbody></table></div>';
                if ($('#pr_tm_ml_det_' + frm_no).length) {
                    var div_el = $(new_el).find('td').eq(1).find('div').eq(0).html();
                    var div2_el = $(new_el).find('td').eq(1).find('div').eq(1).html();
                    $('#pr_tm_ml_det_' + frm_no).find('td').eq(1).find('div').eq(0).html('');
                    $('#pr_tm_ml_det_' + frm_no).find('td').eq(1).find('div').eq(0).html(div_el);
                    $('#pr_tm_ml_det_' + frm_no).find('td').eq(1).find('div').eq(1).html('');
                    $('#pr_tm_ml_det_' + frm_no).find('td').eq(1).find('div').eq(1).html(div2_el);
                    $('#tmpl_mlstns_task_form_' + frm_no).show();
                    $('#pr_tm_ml_det_' + frm_no).show();
                    po.updateValues();
                } else {
                    $(obj).closest('table').closest('tr').before(new_el);
                }
                $('#pr_tm_ml_form_' + frm_no).hide();
            }
            return false;
        },
        updateValues: function () {
            $.each(po.mlstn_data, function (index, val) {
                $.each(val.CaseDetail, function (i, v) {
                    $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][title]']").val(v.title);
                    $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][start_date]']").val(v.start_date);
                    $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][end_date]']").val(v.end_date);
                    $("textarea[name='data[ProjectTemplate][ProjectTemplateMilestone][" + index + "][ProjectTemplateCase][" + i + "][description]']").val(v.mlstn_desc);
                });
            });
        },
        _cancelmlstntsk: function (obj) {
            var mlstsk = $(obj).attr('data-mlstntskno');
            $('#pr_tm_ml_tsk_form_' + mlstsk).hide();
            $('#pr_tm_ml_tsk_det_' + mlstsk).show();
        },
        _cancelmlstn: function (obj) {
            var mlstsk = $(obj).attr('data-mlstnno');
            $('#pr_tm_ml_form_' + mlstsk).hide();
            $('#pr_tm_ml_det_' + mlstsk).show();
        },
        validateMstntsk: function (obj, mlstn_case_cntr, mlstn_cntr, mlst_tsk_id) {
            var frm_no = $(obj).attr('data-mlstntskno');
            var mlst_no = $(obj).attr('data-mlstnno');
            var title = $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][title]']").val();
            var start_date = $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][start_date]']").val();
            var end_date = $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][end_date]']").val();
            var mlstn_desc = $("textarea[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][description]']").val();
            var estimated_hours = $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][estimated_hours]']").val();
            var assign_to = $("input[name='data[ProjectTemplate][ProjectTemplateMilestone][" + mlst_no + "][ProjectTemplateCase][" + frm_no + "][assign_to]']").val();
		
            var errMsg;
            var done = 1;
            if (title.trim() == "") {
                errMsg = '<?php echo __("Task Title cannot be left blank!"); ?>';
                $('#title').focus();
                done = 0;
            } else if (start_date.trim() != "" && end_date.trim() == "") {
                errMsg = '<?php echo __("Task end date cannot be left blank!"); ?>';
                $('#pr_tmpl_mls_task_start_date_' + frm_no).focus();
                done = 0;
            } else if (end_date.trim() != "" && start_date.trim() == "") {
                errMsg = '<?php echo __("Task start date cannot be left blank!"); ?>';
                $('#pr_tmpl_mls_task_end_date_' + frm_no).focus();
                done = 0;
            } else if (start_date.trim() != "" && end_date.trim() != "" && Date.parse(start_date) > Date.parse(end_date)) {
                errMsg = '<?php echo __("Task Start Date cannot exceed End Date!"); ?>';
                $('#pr_tmpl_mls_task_end_date_' + frm_no).focus();
                done = 0;
            }
            if (done == 0) {
                $('#proj_temp_new_mlstn_task_err_' + frm_no).html(errMsg).show();
                return false;
            } else {
                if (!po.empty(po.mlstn_data[mlst_no]['CaseDetail'][frm_no])) {
                    po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['title'] = title;
                    po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['start_date'] = start_date;
                    po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['end_date'] = end_date;
                    po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['mlstn_desc'] = mlstn_desc;
                    po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['estimated_hours'] = estimated_hours;
					po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['assign_to'] = assign_to;
                } else {
                po.mlstn_data[mlst_no]['CaseDetail'][frm_no] = {
                    'title': title,
                    'start_date': start_date,
                    'end_date': end_date,
                    'mlstn_desc': mlstn_desc,
                    'estimated_hours': estimated_hours,
					'assign_to': assign_to
                };
                }
                var new_el = '<tr id="pr_tm_ml_tsk_det_' + frm_no + '"><td></td><td style="text-align:left;border-bottom:1px solid #DCDADB;"><div style="position:relative;" class="edt-task-div">' + po.mlstn_data[mlst_no]['CaseDetail'][frm_no]['title'] + '<span class="ct_icon act_edit_task pr-temp-mlstn-edit-icon edit-span dn" onclick="po._editmlstntsk(' + frm_no + ',' + mlst_no + ');"></span>';
                new_el += '<span class="act_icon act_del_task pr-temp-mlstn-delete-icon dlt-span dn" onclick="po._dltmlstntsk(' + frm_no + ',' + mlst_no + ');"></span></div>';
                if ($('#pr_tm_ml_tsk_det_' + frm_no).length) {
                    var div_el = $(new_el).find('td').eq(1).find('div').html();
                    $('#pr_tm_ml_tsk_det_' + frm_no).find('td').eq(1).html('');
                    $('#pr_tm_ml_tsk_det_' + frm_no).find('td').eq(1).html('<div style="position:relative;" class="edt-task-div">' + div_el + '</div>');
                    $('#pr_tm_ml_tsk_det_' + frm_no).show();
                } else {
                    $(obj).closest('table').closest('tr').before(new_el);
                }
                $('#pr_tm_ml_tsk_form_' + frm_no).hide();
            }
        },
        _dltmlstntsk: function (obj, mlst_no) {
            var frm_no = (typeof ($(obj).attr('data-mlstntskno')) == "undefined") ? obj : $(obj).attr('data-mlstntskno');
            var ml_no = (typeof ($(obj).attr('data-mlstnno')) == "undefined") ? mlst_no : $(obj).attr('data-mlstnno');
            var if_data = !po.empty(po.mlstn_data[ml_no]['CaseDetail'][frm_no]) ? !0 : !1;
            if (typeof ($(obj).attr('data-mlstntskno')) == "undefined") {
                $('#pr_tm_ml_tsk_det_' + frm_no).remove();
                $('#pr_tm_ml_tsk_form_' + frm_no).remove();
            } else {
                if (if_data) {
                    $('#pr_tm_ml_tsk_form_' + frm_no).hide();
                    $('#pr_tm_ml_tsk_det_' + frm_no).show();
                } else {
                $(obj).closest('table').closest('tr').remove();
            }
            }
            if (ml_no) {
                if (!if_data) {
                if (frm_no in po.mlstn_data[ml_no]['CaseDetail']) {
                    delete po.mlstn_data[ml_no]['CaseDetail'][frm_no];
                }
            }
            }
        },
        _editmlstntsk: function (obj) {
            var frm_no = (typeof ($(obj).attr('data-mlstntskno')) == "undefined") ? obj : $(obj).attr('data-mlstntskno');
            $('#pr_tm_ml_tsk_det_' + frm_no).hide();
            $('#pr_tm_ml_tsk_form_' + frm_no).show();
        },
        _dltmlstn: function (obj) {
            var frm_no = (typeof ($(obj).attr('data-mlstnno')) == "undefined") ? obj : $(obj).attr('data-mlstnno');
            var if_data = !po.empty(po.mlstn_data[frm_no]) ? !0 : !1;
            if (typeof ($(obj).attr('data-mlstnno')) == "undefined") {
                $('#pr_tm_ml_det_' + frm_no).remove();
                $('#pr_tm_ml_form_' + frm_no).remove();
            } else {
                if (if_data) {
                    $('#pr_tm_ml_form_' + frm_no).hide();
                    $('#pr_tm_ml_det_' + frm_no).show();
                } else {
                $(obj).closest('table').closest('tr').remove();
            }
            }
            if (frm_no in po.mlstn_data) {
                if_data || delete po.mlstn_data[frm_no];
            }
        },
        _editmlstn: function (obj) {
            var frm_no = (typeof ($(obj).attr('data-mlstnno')) == "undefined") ? obj : $(obj).attr('data-mlstnno');
            $('#pr_tm_ml_det_' + frm_no).hide();
            $('#pr_tm_ml_form_' + frm_no).show();
        },
        _assign_users: function(key,obj,value){
            $(obj).closest(".dropdown").find('.assignto').val(key);
            $(obj).closest(".dropdown").find('.tsk_asgn_to_p').html(value);
            $(".more_opt ul").hide();
        },
         _depends:function(key,obj,value){
            $(obj).closest(".dropdown").find('.depends').val(key);
            $(obj).closest(".dropdown").find('.tsk_depends_p').html(value);
            $(".more_opt_depends ul").hide();
        },
        open_more_opt: function(obj,v){      
          $(obj).closest(".dropdown").find('.'+v+' ul').show();  
        },
        addMoremlsTsks: function (num) {
            var itm = this.clone_item_mlstask.clone();
             for (var key in po.all_users) {
                if (po.all_users.hasOwnProperty(key)) {
                    if (SES_ID == key) {
                        itm.find('.more_opt ul').append('<li><a href="javascript:jsVoid()" onclick="po._assign_users(' + key + ',this,\'me\');" value="' + key + '"><span class="value">' + key + '</span>me</a></li>');
                    } else {
                        itm.find('.more_opt ul').append('<li><a href="javascript:jsVoid()" onclick="po._assign_users(' + key + ',this,\''+ucfirst(formatText(po.all_users[key]))+'\');" value="' + key + '"><span class="value">' + key + '</span>' + ucfirst(formatText(po.all_users[key])) + '</a></li>');
                    }
                }
              }
            itm.find('.more_opt ul').append('<li><a href="javascript:jsVoid()" onclick="po._assign_users(0,this,\'No Body\');" value="0"><span class="value">0</span>No Body</a></li>');
            itm.find('input').val('');
            itm.find("[data-mlstntskno='']").attr('data-mlstntskno', po.mlstask_cnt);
            itm.find("[data-mlstnno='']").attr('data-mlstnno', num);
            itm.find('input').each(function () {
                var fld_name = $(this).attr('data-fldname');
                var orgname = $(this).attr('name');
                var orgname = orgname.replace(/[[]\d]+/g, "[" + num + "]");
                $(this).attr('name', orgname + "[" + po.mlstask_cnt + "][" + fld_name + "]");
                if ($(this).hasClass('datepicker')) {
                    ($(this).attr('id') == "pr_tmpl_mls_task_start_date") ? $(this).attr('id', 'pr_tmpl_mls_task_start_date_' + po.mlstask_cnt) : $(this).attr('id', 'pr_tmpl_mls_task_end_date_' + po.mlstask_cnt);
                }
            });
            itm.find('textarea').each(function () {
                var fld_name = $(this).attr('data-fldname');
                var orgname = $(this).attr('name');
                var orgname = orgname.replace(/[[]\d]+/g, "[" + num + "]");
                $(this).attr('name', orgname + "[" + po.mlstask_cnt + "][" + fld_name + "]");
            });
            var new_el = '<tr id="pr_tm_ml_tsk_form_' + po.mlstask_cnt + '" class="mlstn-task-rows"><td></td><td><center><div id="proj_temp_new_mlstn_task_err_' + po.mlstask_cnt + '" class="fnt_clr_rd" style="display:block;font-size:15px;"></div></center><table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_tsks_forms">' + $(itm).html() + '</table></td></tr>';
            $('#tmpl_mlstns_task_form_' + num).show();
            $('#tmpl_mlstns_task_form_' + num).find('#tmpl_mlstns_task_form_tbody_' + num).append($(new_el));
            $("#pr_templat_form").animate({
                scrollTop: $('#pr_tm_ml_tsk_form_' + po.mlstask_cnt).offset().top
            }, 2000).promise().done(function () {
                po._setDependsDropdown(po.mlstask_cnt);   
                $("[id ^='pr_tm_ml_tsk_form_']").find("input").attr("readonly",false);
              });
            po.mlstn_data[num]['CaseDetail'][po.mlstask_cnt] = {};
            $('#pr_tmpl_mls_task_start_date_' + po.mlstask_cnt).removeClass('hasDatepicker');
            $('#pr_tmpl_mls_task_end_date_' + po.mlstask_cnt).removeClass('hasDatepicker');
            $('#pr_tmpl_mls_task_start_date_' + po.mlstask_cnt).datepicker({
                dateFormat: 'M d, yy',
                changeMonth: false,
                minDate: 0,
                changeYear: false,
                hideIfNoPrevNext: true,
                onClose: function (selectedDate) {
                    $("#pr_tmpl_mls_task_end_date_" + po.mlstask_cnt).datepicker("option", "minDate", selectedDate);
                },
            });
            $("#pr_tmpl_mls_task_end_date_" + po.mlstask_cnt).datepicker({
                dateFormat: 'M d, yy',
                changeMonth: false,
                minDate: 0,
                changeYear: false,
                hideIfNoPrevNext: true,
                onClose: function (selectedDate) {
                    $("#pr_tmpl_mls_task_start_date_" + po.mlstask_cnt).datepicker("option", "maxDate", selectedDate);
                },
            });
            po.mlstask_cnt++;
        },
        addMoreTsks: function () {
            var is_def_exists = po.get_row_count();
            is_def_exists && po.addMoremlsTsks(is_def_exists);
        },
        get_row_count: function () {
            var status = !1;
            var key;
            $.each(po.mlstn_data, function (index, val) {
                if (typeof (val.mlstn_status) !== "undefined" && val.mlstn_status === 1) {
                    status = !0;
                    key = index;
                }
            });
            return (status) ? key : status;
        },
        validateTemplate: function () {
            var title = $("input[name='data[ProjectTemplate][module_name]']").val();
            var errMsg;
            var done = 1;
            if (title.trim() == "") {
                errMsg = '<?php echo __("Template title cannot be left blank!"); ?>';
                $('#title').focus();
                done = 0;
            } else if (po.empty(po.mlstn_data)) {
                done = 0;
                errMsg = 'Please add some milestone(s) before submit!';
            }
            if (po.mlstn_data) {
                var mlstn_wth_errors = [];
                $.each(po.mlstn_data, function (index, val) {
                    if (po.empty(val.CaseDetail)) {
                        mlstn_wth_errors.push(val.title);
                    }
                });
                if (!po.empty(mlstn_wth_errors)) {
                    done = 0;
                    errMsg = 'Please add some task(s) in milestone "' + mlstn_wth_errors.join(', ') + '" before submit!';
                }
            }
            if (done == 0) {
                $('#proj_temp_new_err').html(errMsg).show();
            } else {
                $('#proj_temp_task_btn').hide();
                $('#proj_temp_new_loader').show();
                var str_url = HTTP_ROOT;
                $.ajax({
                    url: HTTP_ROOT + 'projecttemplate/ProjectTemplates/add',
                    type: 'POST',
                    dataType: 'json',
                    data: $("#add-project-template").serialize(),
                }).done(function (res) {
                    if (res.status) {
                        $('#proj_temp_new_loader').hide();
                        $('#proj_temp_task_btn').show();
                        closePopup();
                        showTopErrSucc('success', _("Project template created succssfully."));
                        location.reload();
                    }
                });
            }
            return false;
        },
        empty: function (mixedVar) {
            var undef
            var key
            var i
            var len
            var emptyValues = [undef, null, false, 0, '', '0']
            for (i = 0, len = emptyValues.length; i < len; i++) {
                if (mixedVar === emptyValues[i]) {
                    return true
                }
            }
            if (typeof mixedVar === 'object') {
                for (key in mixedVar) {
                    if (mixedVar.hasOwnProperty(key)) {
                        return false
                    }
                }
                return true
            }
            return false
        }
    };
</script>