<style type="text/css">
.pr_low{border: 1px solid #EFF4CC;background:#FCFEE6}
.pr_medium{border: 1px solid #D7E0CF;background:#F9FFF5}
.pr_high{border: 1px solid #EECACC; background:#FEF2F2}
.kban_all_task {
    height: 552px;
    padding-top: 7px;
    padding-bottom: 1px;
    overflow-y: auto;
    overflow-x: hidden;
}
.kban_all_task .ui-draggable {cursor: -webkit-grab;}
.spnt_red {
	color: #D23F31;
}
.spnt_green {
	color: #56b40b;
}
.adding-task-newTask {margin-top:3px;}
</style>
<% if(!morecontent){%>
<% if(mlstId){
var days = mlstdays_diff;
%>
<div class="mlst-heading">
	<div class=" fl"><%= shortLength(ucfirst(mlstTitle), 60) %></div>	
	<!--<div class="mlst-heading"><%= shortLength(ucfirst(mlstTitle), 50) %></div>-->
        <?php if(defined('CR') && CR == 1 && SES_CLIENT ==1){ 
                                                /**Not Show create project */
        }else{?> 
	<div class="dropdown fl">
			<div class="sett" data-toggle="dropdown"></div>
			<ul class="dropdown-menu sett_dropdown-caret" style="margin-top:5px;padding:5px 0;">
				<li class="pop_arrow_new"></li>
                                <?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('task')==1){ 
                                    /**Not Show create task */
                                }else{?> 
				<li  onClick="creatask(<%= mlstId %>)" >
					<a href="javascript:void(0);">
						<div class="ct_icon act_create_task fl"></div>
						<div class="fl"><?php echo __("Create New Task"); ?></div>
						<div class="cb"></div>
					</a>
				</li>
                                <?php } ?>
				<li  onClick="addTaskToMilestone(<%= '\'\',\''+ mlstId + '\'' %>,<%= '\'' + mlstProjId+ '\'' %>,<%= '\'' + count + '\'' %>)" rel="tooltip" title="<?php echo __("Add Task"); ?>">
					<a href="javascript:void(0);"><div class="ct_icon icon-add-task-milston fl"></div><div class="fl mntxt"><?php echo __("Add Task"); ?></div></a>
				</li>
				<li onclick="addEditMilestone(<%= '\'\',\'' + mlstUid + '\'' %>,<%= '\'' + mlstId + '\'' %>,<%= '\'' + mlstTitle + '\',1' %>)" class="makeHover" rel="tooltip" title="<?php echo __("Edit"); ?>">
					<a href="javascript:void(0)"><div class="ct_icon act_edit_task fl"></div><?php echo __("Edit"); ?> </a>
				</li>
				<li onClick="delMilestone(<%= '\'\',\'' + mlstTitle + '\'' %>,<%= '\'' + mlstUid + '\'' %>);" class="makeHover" rel="tooltip" title="<?php echo __("Delete"); ?>">
					<a href="javascript:void(0);" ><div class="act_icon act_del_task fl"></div><div class="fl mntxt" style="margin-left:-6px"><?php echo __("Delete"); ?></div></a>
				</li>
				<% if(mlsttype=='0'){ %>
					<li onclick="milestoneRestore(<%= '\'\',\'' + mlstUid + '\'' %>, <%= '\'' + mlstTitle + '\'' %>);"  rel="tooltip" title="<?php echo __("Restore"); ?>" >
						<a href="javascript:jsVoid();"><div class="ct_icon mt_completed fl"></div> <div class="fl mntxt" style="margin-left:-6px"><?php echo __("Restore"); ?></div></a>
					</li>
				<% }else{ %>
					<li onclick="milestoneArchive(<%= '\'\',\'' + mlstUid + '\'' %>, <%= '\'' + mlstTitle + '\'' %>);"  rel="tooltip" title="<?php echo __("Complete"); ?>" >
						<a href="javascript:jsVoid();"><div class="ct_icon mt_completed fl"></div> <div class="fl mntxt" style="margin-left:-6px"><?php echo __("Complete"); ?></div></a>
					</li>
				<% } %>
				
			</ul>
		</div>
        <?php } ?>
<!--		<div class="fr"><%= mlsttotalCs %></div>-->
		<div class="cb"></div>
		<div class="fl">
				<span class="mlst-dt">
			<img src="<?php echo HTTP_ROOT."img/images/clock.png"; ?>">&nbsp;&nbsp;
			<% if(days == 0) { %>
				<b style="color:green"><?php echo __("Today"); ?></b>
			<% } else { %>
				<font color="#565656"><%= mlstDT %></font>&nbsp;
			<% if(intEndDate < intCurCreated) { %>
					<font color="red">
			<% 		if(days > 1) { %>
						(<?php echo __("Late by"); ?> <%= days %> <?php echo __("days"); ?>)
			<% 		} else { %>
						(<?php echo __("Late by"); ?> <%= days %> <?php echo __("day"); ?>)
			<% 		} %>
					</font>
			<% } else { %>
					<font color="green">
			<% 		if(days > 1) { %>
						(<?php echo __("Coming up in"); ?> <%= days %> <?php echo __("days"); ?>)
			<% 		} else { %>
						(<?php echo __("Coming up in"); ?> <%= days %> <?php echo __("day"); ?>)
			<% 		} %>
					</font>
			<% }
			} %>
			</span>
		</div>
		<div class="cb"></div>
		<div class=" fl imprv_bar col-lg-6" title="<%= totalClosedCs + ' out of ' + mlsttotalCs + ' tasks are closed' %>" rel="tooltip">
			<div style="width:<%= mlstFill %>%;" class="cmpl_green"></div>
		</div>
		<div class="fl clsd-txt"><%= mlstFill %>% <?php echo __("closed"); ?></div>
		<div class="cb"></div>
		</div>

<% } %>
<div class="kanban-main">
	<%
	}
	var clscnt=1;
        var caseAll_length=countJS(caseAll);
	for(var taskallkey in caseAll){
		var tasklist = new Array();
		var tasktype='';
                var sts_of_tsk = '';
		if(taskallkey=='newTask'){tasktype='New';sts_of_tsk =1;}else if(taskallkey=='inprogressTask'){tasktype='In Progress';sts_of_tsk =2;}else if(taskallkey=='resolvedTask'){tasktype='Resolved';sts_of_tsk =5;}else if(taskallkey=='closedTask'){tasktype='Closed';sts_of_tsk =5;}else{tasktype =taskallkey;sts_of_tsk =10;}
	 if(!morecontent){
	%>
		<div class="fl kanban-child kanban-<%= clscnt++ %>" id="<%= taskallkey.replace(/\s/g, '') %>" data-temp-id="<%= taskallkey %>">
		<div class="kbhead kbhead_<%= taskallkey.replace(/\s/g, '') %>">
			<div class=" fl ellipsis-view" style="max-width:120px" title="<%= _(tasktype) %>"> <%= _(tasktype) %> </div>
            <% for(var key in caseAll[taskallkey]){
                var stsdata = caseAll[taskallkey][key];
                var lgnd = stsdata.Easycase.legend == 4 ? 2:stsdata.Easycase.legend;break;
            } %>
			<div class="fr" title="Tasks" rel="tooltip" id="cnter_<%= lgnd %>" style="margin-left:7px;color:#aaaaaa"></div>
                       <?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('task')==1){ 
                            /**Not Show create task */
                        }else{?> 
                            <div class="fr adding-task-kanban adding-task-<%= taskallkey %> <% if(clscnt>2 || projUniq == 'all'){ %>hidden<%}%>"><a href="javascript:void(0);" onClick="creatask(<%= mlstId %>)"></a></div>
                        <?php } ?>
			<div class="cb"></div>
			<div class="fl mlst_prj_nm estimated_spent_dv">	
				<%
					var res_progreess = getEstimatedPercentage(arr_kanban_hours[taskallkey].estd_hrs,arr_kanban_hours[taskallkey].sphours,0);
					res_progreess = res_progreess.split('__');
				%>
				<span style="margin-right:10px;" title="Estimated: <%= getHourMinut(arr_kanban_hours[taskallkey].estd_hrs) %>" rel="tooltip"><%= getHourMinut(arr_kanban_hours[taskallkey].estd_hrs) %></span>
				<span class="<%= res_progreess[1] %>" title="Spent: <%= getHourMinut(arr_kanban_hours[taskallkey].sphours) %>" rel="tooltip"><%= getHourMinut(arr_kanban_hours[taskallkey].sphours) %></span>
			</div>
			<div class="cb"></div>
			<% if((sts_of_tsk <= 5) && (tasktype == 'New' || tasktype == 'In Progress' || tasktype == 'Resolved' || tasktype == 'Closed')){ %>
				<?php /*<input type="hidden" id="<%= taskallkey.replace(/\s/g,'') %>_limit" value="<%= taskallkey.replace(/\s/g,'')+'_limit' %>" /> */ ?>
			<% }else{ %>
				<?php /*<input type="hidden" id="<%= taskallkey.replace(/\s/g,'') %>_limit" value="<%= Status_limit[taskallkey.replace(/\s/g,'')+'_limit'] %>" /> */ ?>
				<input type="hidden" id="<%= taskallkey.replace(/\s/g,'') %>_limit" value="<%= Status_limit[taskallkey.replace(/\s/g,'')+'_limit'] %>" />
			<% } %>
		</div>
		<div class="kanban_content kban_all_task" >
	<%	}
			var tasklist = caseAll[taskallkey];
			var count = 0;
			var totids = "";
			var openId = "";
			var pgCaseCnt = countJS(tasklist);
			var caseCount = countJS(tasklist);
				if(caseCount && caseCount != 0){
					var count=0;
					var caseNo = "";
					var chkMstone = "";
					var caseLegend = "";
					var totids = "";
					var projectName ='';var projectUniqid='';
			for(var caseKey in tasklist){
				var getdata = tasklist[caseKey];
				count++;
				var caseAutoId = getdata.Easycase.id;
				var caseUniqId = getdata.Easycase.uniq_id;
				var caseNo = getdata.Easycase.case_no;
				var caseUserId = getdata.Easycase.user_id;
				var caseTypeId = getdata.Easycase.type_id;
				var projId = getdata.Easycase.project_id;
				var caseLegend = getdata.Easycase.legend;
				var casePriority = getdata.Easycase.priority;
				var caseFormat = getdata.Easycase.format;
				var caseTitle = getdata.Easycase.title;
				var caseAssgnUid = getdata.Easycase.assign_to;
				
				var prjworkflow_id = 0;
				prjworkflow_id = getdata.Easycase.workflow_id;
				
				var getTotRep = 0;
				if(getdata.Easycase.case_count && getdata.Easycase.case_count!=0) {		
					getTotRep = getdata.Easycase.case_count;
				}
                                var max_lgndall = 0 ;
                                 if(status_lists.length !=0){
                                    for(var kys in status_lists){
                                        if(getdata.Easycase.project_id == kys)
                                        {
                                            max_lgndall = status_lists[kys];
                                        }
                                    }
                                }
				if(caseUrl == caseUniqId) {
					openId = count;
				}

				var chkDat = 0;

				if(projUniq=='all' && (typeof getdata.Easycase.pjname !='undefined')){
					projectName =	getdata.Easycase.pjname;
					projectUniqid = getdata.Easycase.pjUniqid;
				}else if(projUniq!='all'){
					projectName =	getdata.Easycase.pjname;
					projectUniqid = getdata.Easycase.pjUniqid;
				} %>
		<div class="pr_<%= easycase.getPriority(casePriority) %> kbtask_div" title="<% if(getTotRep && getTotRep!=0) { %><?php echo __('Updated'); ?><% } else { %><?php echo __('Created'); ?><% } %> by <%= getdata.Easycase.usrShortName %> <% if(getdata.Easycase.updtedCapDt.indexOf('Today')==-1 && getdata.Easycase.updtedCapDt.indexOf('Y\'day')==-1) { %><?php echo __('on'); ?><% } %> <%= getdata.Easycase.updtedCapDt %> ">
			<!--<div class="fl" rel="tooltip" title="<% if(getTotRep && getTotRep!=0) { %><?php echo __('Updated'); ?><% } else { %><?php echo __('Created'); ?><% } %> by <%= getdata.Easycase.usrShortName %> <% if(getdata.Easycase.updtedCapDt.indexOf('Today')==-1 && getdata.Easycase.updtedCapDt.indexOf('Y\'day')==-1) { %>on<% } %> <%= getdata.Easycase.updtedCapDt %> "> <%= getdata.Easycase.proImage %></div> -->	
			<div class="fl title_wd">
                        <div id="titlehtml<%= count %>" data-task="<%= caseUniqId %>" class="fl case-title <% if(((caseAll_length==clscnt-1 || getdata.Easycase.legend==3) && projUniq !='all') ||((getdata.Easycase.legend==max_lgndall || getdata.Easycase.legend==3) && projUniq =='all')) { %>closed_tsk<% } %>"> 
                	<span class="case_title wrapword">#<%= caseNo %>: <%= caseTitle %>
                    &nbsp;
                            <% if(getdata.Easycase.csDuDtFmt.search("Set Due Dt") >= 0 || getdata.Easycase.csDuDtFmt=='No Due Date'){%><% } else { %><span class="dt-icon" title="<%= getdata.Easycase.csDuDtFmtT %>"><a href="javascript://"></a></span><% } %>
                            <% if(getTotRep && getTotRep!=0) { %><span class="bblecnt2"></span><span class="count_knbn" style="top:-1px;position: relative;font-size: 11px !important;padding-left: 2px;"><%= getTotRep %><% } %></span>
                            </span>
                </div>
				<!--<div class="fr" <% if(!getTotRep || getTotRep==0) { %> style="display:none" <% } %>>
				<div id="repno<%= count %>" class="fl bblecnt2"></div>
				<span class="count_knbn"> &nbsp;<% if(getTotRep && getTotRep!=0) { %><%= getTotRep %><% } %></span>
			</div>-->
			</div>
			<div class="cb"></div>
            <div class="fl">
            	<% if(projUniq=='all'){ %>
				<div class="pjname-cls" style="font-weight:normal"><%= projectName %></div>
				<% } %>
            </div>
            <% if(getdata.Easycase.asgnShortName && getdata.Easycase.asgnShortName.search("me") == -1) { %>
			<div class="fr" title="<?php echo __('Assigned to'); ?><%= getdata.Easycase.asgnShortName %>" style="float:right">
                <div class="fl img-cls-assn1"></div>
				<div class="fnt999 fl">&nbsp;</div>
				<div class="fl" >
					<% if((projUniq != 'all') && (caseLegend == 1 || caseLegend == 2 || caseLegend == 4)){ %>
						<span id="showUpdAssign<%= caseAutoId %>" class="clsptr fnt13" style="font-size:11px;cursor:text;" onclick="displayAssignToMem(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + projUniq + '\'' %>,<%= '\'' + caseAssgnUid + '\'' %>,<%= '\'' + caseUniqId + '\'' %>)"><%= getdata.Easycase.asgnShortName %></span>
					<% } else { %>
						<span id="showUpdAssign<%= caseAutoId %>" style="cursor:text;text-decoration:none;color:#666666;font-size:11px"><%= getdata.Easycase.asgnShortName %></span>
					<% } %>
					<% if((projUniq != 'all') && (caseLegend == 1 || caseLegend == 2 || caseLegend == 4)){ %>
					<span id="asgnlod<%= caseAutoId %>" class="asgn_loader">
						<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
					</span>
					<% } %>
					<!--<ul class="dropdown-menu asgn_dropdown-caret" id="showAsgnToMem<%= caseAutoId %>">
						<li class="pop_arrow_new"></li>
						<li class="text-centre"><img src="<?php echo HTTP_IMAGES; ?>images/del.gif" id="assgnload<%= caseAutoId %>" /></li>
					</ul>-->
				</div>
				<div class="dropdown fr m_rht3">
					<!--<div id="showUpdStatus<%= caseAutoId %>" class="type_<%= getdata.Easycase.csTdTyp[0] %> <% if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && getdata.Easycase.isactive == 1){ %>clsptr<% } %> <% if($.inArray(getdata.Easycase.csTdTyp[0], ['dev', 'bug', 'upd']) == -1) { %>opcty4<% } %>" title="<%= getdata.Easycase.csTdTyp[1] %>" data-toggle="dropdown"></div>
					<span id="typlod<%= caseAutoId %>" class="type_loader">
						<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
					</span>
					 <% if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && getdata.Easycase.isactive == 1){ %>
					<ul class="dropdown-menu type_dropdown-caret" style="width:175px;">
						<li class="pop_arrow_new"></li>
						<%
						for(var k in GLOBALS_TYPE) {
							var v = GLOBALS_TYPE[k];
							var t = v.Type.id;
							var t1 = v.Type.short_name;
							var t2 = v.Type.name;
						%>
							<li onclick="changeCaseType(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>); changestatus(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + t + '\'' %>, <%= '\'' + t1 + '\'' %>, <%= '\'' + t2 + '\'' %>, <%= '\'' + caseUniqId + '\'' %>)">
								<a href="javascript:void(0);"><div class="task_types_<%= t1 %> fl"></div><%= t2 %></a>
							</li>
						<% } %>
					</ul>
					<% } %> -->
				</div>
				<div class="cb"></div>
			</div>
                         <% } %>
			<div>
            <?php if(defined('GNC') && GNC == 1){ ?>
                <div style="width:auto;float:left;height:14px;">
                    <span class="case_act_icons fl" style="display:inline-block;">
                            <% if(getdata.Easycase.children && getdata.Easycase.children != ""){ %>
                            <span class="fl  task_parent_block" id="task_parent_block_<%= getdata.Easycase.uniq_id %>">
                                <div rel="" title="Parents" onclick="showParents(<%= '\'' + getdata.Easycase.id + '\'' %>,<%= '\'' + getdata.Easycase.uniq_id + '\'' %>,<%= '\'' + getdata.Easycase.children + '\'' %>);" class=" task_title_icons_parents fl"></div>
                                <div class="dropdown dropup fl1 open1 showParents">
                                    <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                        <li class="pop_arrow_new"></li>
                                        <li class="task_parent_msg" style=""><?php echo __("These tasks are waiting on this task.");?></li>
                                        <li>
                                            <ul class="task_parent_items" id="task_parent_<%= getdata.Easycase.uniq_id %>" style="">
                                                <li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </span>
                            <% } %>
                            <% if(getdata.Easycase.depends && getdata.Easycase.depends != ""){ %>
                            <span class="fl  task_dependent_block" id="task_dependent_block_<%= getdata.Easycase.uniq_id %>">
                                <div rel="" title="Dependents" onclick="showDependents(<%= '\'' + getdata.Easycase.id + '\'' %>,<%= '\'' + getdata.Easycase.uniq_id + '\'' %>,<%= '\'' + getdata.Easycase.depends + '\'' %>);" class=" task_title_icons_depends fl"></div>
                                <div class="dropdown dropup fl1 open1 showDependents">
                                    <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                        <li class="pop_arrow_new"></li>
                                        <li class="task_dependent_msg" style=""><?php echo __("Task can't start. Waiting on these task to be completed.");?></li>
                                        <li>
                                            <ul class="task_dependent_items" id="task_dependent_<%= getdata.Easycase.uniq_id %>" style="">
                                                <li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                            </span>
                            <% } %>
                       </span>
                </div>
            <?php } ?>
                            
                        <div class="fr">
                        <div class="dropdown fr">
				<div class="sett" data-toggle="dropdown"></div>
				<ul class="dropdown-menu sett_dropdown-caret">
					<li class="pop_arrow_new"></li>
					<% var caseFlag="";
					if(caseLegend == 1 && caseTypeId!= 10) { caseFlag=1; }
					if(getdata.Easycase.isactive == 1) { %>
					<li onclick="startCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" rel="tooltip" title="<?php echo __("Start"); ?>" id="start<%= caseAutoId %>" style=" <% if(caseFlag == "1"){ %>display:block<% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_start_task fl" title="Start"></div><?php echo __("Start"); ?></a>
					</li>
					<% }
					if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && caseTypeId!= 10) { caseFlag=2; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="caseResolve(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" rel="tooltip" title="<?php echo __("Resolve"); ?>" id="resolve<%= caseAutoId %>" style=" <% if(caseFlag == 2){ %> display:block <% } else { %> display:none <% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_resolve_task fl" title="Resolve"></div><?php echo __("Resolve"); ?></a>
					</li>
					<% }
					if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4 || caseLegend == 5) && caseTypeId != 10) { caseFlag=5; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="setCloseCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" rel="tooltip" title="<?php echo __("Close"); ?>" id="close<%= caseAutoId %>" style=" <% if(caseFlag == 5) { %>display:block <% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_close_task fl" title="Close"></div><?php echo __("Close"); ?></a>
					</li>
					<% } if(caseFlag == 5 || caseFlag==2) { %>
					<% } %>
					<% if(caseLegend == 3) { caseFlag= 7; } else { caseFlag= 8; }
					if(getdata.Easycase.isactive == 1){ %>
                    <?php if((defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('disable_replay_to_client')==1) || (SES_TYPE == 1 || SES_TYPE == 2)){ ?>
					<li id="act_reply<%= count %>" data-task="<%= caseUniqId %>">
						<a href="javascript:void(0);" id="reopen<%= caseAutoId %>" style="<% if(caseFlag == 7){ %>display:block <% } else { %>display:none<% } %>"><div class="act_icon act_reply_task fl" title="<?php echo __("Re-open"); ?>"></div><?php echo __("Re-open"); ?></a>
						<a href="javascript:void(0);" id="reply<%= caseAutoId %>" style="<% if(caseFlag == 8){ %>display:block <% } else { %>display:none<% } %>"><div class="act_icon act_reply_task fl" title="<?php echo __("Reply"); ?>"></div><?php echo __("Reply"); ?></a>
					</li>
                    <?php } ?>
					<% }
					if(caseLegend == 1 && SES_ID == caseUserId) { caseFlag=3; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="editask(<%= '\''+ caseUniqId+'\',\''+projectUniqid+'\',\''+projectName+'\'' %>);" id="edit<%= caseAutoId %>" style=" <% if(caseFlag == 3){ %>display:block <% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_edit_task fl" title="<?php echo __("Edit"); ?>"></div><?php echo __("Edit"); ?></a>
					</li>
					<% } 
					if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && caseTypeId!= 10) { caseFlag=2; }
					if((SES_TYPE == 1 || SES_TYPE == 2) || ((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) &&  (SES_ID == caseUserId))){
					%>
					<li data-prjid="<%= projId %>" data-caseid="<%= caseAutoId %>" data-caseno="<%= caseNo %>" onclick="mvtoProject(<%= '\'' + count + '\'' %>,this)" id="mv_prj<%= caseAutoId %>" style=" ">
					    <a href="javascript:void(0);"><div class="act_icon pro_mov fl" title="<?php echo __("Move to Project"); ?>"></div><?php echo __("Move to Project"); ?></a>
					</li>
					<% } 
					if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && caseTypeId!= 10) { caseFlag=2; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="moveTask(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'\'' %>,<%= '\'' + projId + '\'' %>);" id="moveTask<%= caseAutoId %>" style=" <% if(caseFlag == 2){ %> display:block <% } else { %> display:none <% } %>">
						<a href="javascript:void(0);"><div class="act_icon task_move_mlst fl" title="<?php echo __("Move to Milestone"); ?>"></div><?php echo __("Move to Milestone"); ?></a>
					</li>
					<% } %>
					<% if(getdata.Easycase.isactive == 1){
					if(caseMenuFilters == "milestone" && (SES_TYPE == 1 || SES_TYPE == 2 || SES_ID == getdata.Easycase.Em_user_id)) {
					caseFlag = "remove";
					%>
					<li onclick="removeThisCase(<%= '\'' + count + '\'' %>,<%= '\'' + getdata.Easycase.Emid + '\'' %>, <%= '\'' + caseAutoId + '\'' %>, <%= '\'' + getdata.Easycase.Em_milestone_id + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUserId + '\'' %>);" id="rmv<%= caseAutoId %>" style="<% if(caseFlag == "remove"){ %>display:block<% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_rmv fl" title="<?php echo __("Remove Task"); ?>"></div><?php echo __("Remove Task"); ?></a>
					</li>
					<%
					}
					}
					if(SES_TYPE == 1 || SES_TYPE == 2 || ((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) && ( SES_ID == caseUserId))) { caseFlag = "archive"; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="archiveCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + projId + '\'' %>, <%= '\'t_' + caseUniqId + '\'' %>);" id="arch<%= caseAutoId %>" style="<% if(caseFlag == "archive"){ %>display:block<% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_arcv_task fl" title="<?php echo __("Archive"); ?>"></div><?php echo __("Archive"); ?></a>
					</li>
					<% }
					if(SES_TYPE == 1 || SES_TYPE == 2 || (caseLegend == 1  && SES_ID == caseUserId)) { caseFlag = "delete"; }
					if(getdata.Easycase.isactive == 1){ %>
					<li onclick="deleteCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + projId + '\'' %>, <%= '\'t_' + caseUniqId + '\'' %>);" id="arch<%= caseAutoId %>" style="<% if(caseFlag == "delete"){ %>display:block<% } else { %>display:none<% } %>">
						<a href="javascript:void(0);"><div class="act_icon act_del_task fl" title="<?php echo __("Delete"); ?>"></div><?php echo __("Delete"); ?></a>
					</li>
					<% } %>
				</ul>
			</div>
                             </div>
                        <div class="cb"></div>
                       
                        </div>
		</div>
                            
                                
                                
			<%
				totids += caseAutoId + "|";
			}	}  %>
		</div>
	<% if(!morecontent){%>
	<div id="loader_<%= taskallkey.replace(/\s/g,'') %>" style="text-align:center;font-size:12px;display:none;"><img src="<?php echo HTTP_ROOT;?>img/images/del.gif" alt="loading..." title="loading..."/><br/><?php echo __("Loading"); ?>...</div>
	</div>
<% }} %>
<?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('task')==1){ 
    /**Not Show create task */
}else{?> 
<div class="crt_task_btn_btm crt_tsk_btn">
        <div class="os_plus">
			<div class="ctask_ttip">
				<span class="label label-default"><?php echo __("Create Task"); ?></span>
			</div>
			<a href="javascript:void(0)" onclick="creatask();">
			<img src="<?php echo HTTP_ROOT; ?>img/images/creat-task.png" class="prjct_icn ctask_icn"/> 
				<img src="<?php echo HTTP_ROOT; ?>img/images/plusct.png" class="add_icn" />
			</a>
        </div>
</div>	
<?php } ?>
<% if(!morecontent){  %>
<div class="cb"></div>
</div>
<div class="cb h30"></div>

<input type="hidden" id="newTask_limit" value="<%= newTask_limit %>" />
<input type="hidden" id="inprogressTask_limit" value="<%= inprogressTask_limit %>" />
<input type="hidden" id="resolvedTask_limit" value="<%= resolvedTask_limit %>" />
<input type="hidden" id="closedTask_limit" value="<%= closedTask_limit %>" /> 
 
<% } %>