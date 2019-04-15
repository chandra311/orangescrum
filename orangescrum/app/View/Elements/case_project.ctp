<style type="text/css">
.pr_low{background:none !important;}
.pr_medium{background:none !important;}
.pr_high{background:none !important;}
.label{font-weight:normal;}
.tsk_tbl td {border-right: 0px solid #FFF !important;border-bottom: 0px solid #FFF !important;}
.anchor{cursor:pointer;}
.act_log_task{background: url("<?php echo HTTP_ROOT; ?>img/Time_log_icon.png") no-repeat;width:20px;height:19px;}
.act_timer{background: url("<?php echo HTTP_ROOT; ?>img/timer.png") no-repeat;width:20px;height:19px;}
.task_title_icons_reoccurrence{background:url(<?php echo HTTP_ROOT; ?>/img/task_act_icons.png)no-repeat 0px 0px;position:relative;width:13px;height:11px;margin-top: 5px;display: block;float: left;margin-right:5px;}
#custom_date .cdate_div_cls {padding-left:10px;}
</style>
<?php ?>
<table width="100%" class="tsk_tbl compactview_tbl">
    <tr style="" class="tab_tr">
        <td width="18%" class="all_td">
            <div class="dropdown fl">
                <input type="checkbox" class="fl chkAllTsk" id="chkAllTsk" />
                <div class="all_chk"></div>
                <ul class="dropdown-menu" id="dropdown_menu_chk">
                     <% if(typeof wrkflw_id != 'undefined' && typeof status_lists != 'undefined' && wrkflw_id == "0" && status_lists.length == 0) { %>
                    <li><a href="javascript:void(0);" onclick="multipleCaseAction(<%= '\'caseStart\'' %>)"><div class="act_icon act_start_task fl" title="<?php echo __("Start"); ?>"></div><?php echo __("Start"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="multipleCaseAction(<%= '\'caseResolve\'' %>)"><div class="act_icon act_resolve_task fl" title="<?php echo __("Resolve"); ?>"></div><?php echo __("Resolve"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="multipleCaseAction(<%= '\'caseId\'' %>)"><div class="act_icon act_close_task fl" title="><?php echo __("Close"); ?>"></div><?php echo __("Close"); ?></a></li>
                    <% } %>
                    <?php if(SES_TYPE == 1 || SES_TYPE == 2) {?>
                    <li id="mvTaskToProj"><a href="javascript:void(0);" onclick="mvtoProject(<%= '\' \'' %>,<%= '\' \'' %>,<%= '\'movetop\'' %>)"><div class="act_icon pro_mov fl" title="<?php echo __("Move to project"); ?>"></div><?php echo __("Move to project"); ?></a></li>
                    <?php if(defined('PT') && PT == 1){ ?>
                    <li id="crtProjTmpl"><a href="javascript:void(0);" onclick="createPojectTemplate(<%= '\' \'' %>,<%= '\' \'' %>,<%= '\'movetop\'' %>)"><div class="act_icon pro_mov fl" title="<?php echo __("Create Project Template"); ?>"></div><?php echo __("Create Project Template"); ?></a></li>
                    <?php } ?>
                        <?php } ?>
                </ul>
            </div>
        </td>
        <td class="task_cn">
            <% if(GrpBy == "") { %>
            <a href="javascript:void(0);" title="<?php echo __('Task');?>#" onclick="ajaxSorting(<%= '\'caseno\', ' + caseCount + ', this' %>);" class="sortcaseno"> <% } %>
                <div class="fl"><?php echo __('Task');?>#</div><div class="<% if(GrpBy == "") { %> tsk_sort <% } %> fl <% if(typeof csNum != 'undefined' && csNum != "") { %>tsk_<%= csNum %><% } %>"></div>
           <% if(GrpBy == "") { %> </a> <% } %>
        </td>
        <td class="task_wd">
            <% if(GrpBy == "") { %>
            <a class="sorttitle" href="javascript:void(0);" title="<?php echo __('Title');?>" onclick="ajaxSorting(<%= '\'title\', ' + caseCount + ', this' %>);"> <% } %>
                <div class="fl"><?php echo __('Title');?></div><div class="<% if(GrpBy == "") { %> tsk_sort <% } %> fl <% if(typeof csTtl != 'undefined' && csTtl != "") { %>tsk_<%= csTtl %><% } %>"></div>
       <% if(GrpBy != "") { %> </a> <% } %>
        </td>
        <td class="assign_wd_td">
             <% if(GrpBy == "") { %>
            <a class="sortcaseAt" href="javascript:void(0);" title="<?php echo __('Assigned to');?>" onclick="ajaxSorting(<%= '\'caseAt\', ' + caseCount + ', this' %>);" > <% } %>
                <div class="fl"><?php echo __('Assigned to');?></div><div class="<% if(GrpBy == "") { %> tsk_sort <% } %> fl <% if(typeof csAtSrt != 'undefined' && csAtSrt != "") { %>tsk_<%= csAtSrt %><% } %>"></div>
         <% if(GrpBy != "") { %> </a> <% } %>
        </td>
        <td class="tsk_due_dt">
             <% if(GrpBy == "") { %>
            <a class="sortduedate" href="javascript:void(0);" title="<?php echo __('Due Date');?>" onclick="ajaxSorting(<%= '\'duedate\', ' + caseCount + ', this' %>);"> <% } %>
                <div class="fl"><?php echo __('Due Date');?></div><div class="<% if(GrpBy == "") { %> tsk_sort <% } %> fl <% if(typeof csDuDt != 'undefined' && csDuDt != "") { %>tsk_<%= csDuDt %><% } %>"></div>
         <% if(GrpBy == "") { %> </a> <% } %>
        </td>
    </tr>
    <%
    var count = 0;
    var totids = "";
    var openId = "";
    var groupby = GrpBy;
    var prvGrpvalue='';
    var pgCaseCnt = caseAll?countJS(caseAll):0;
    if(caseCount && caseCount != 0){
	var count=0;
	var caseNo = "";
	var chkMstone = "";
	var caseLegend = "";
	var totids = "";
	var projectName ='';var projectUniqid='';
	for(var caseKey in caseAll){
		var getdata = caseAll[caseKey];
		count++;
		var caseAutoId = getdata.Easycase.id;
		var caseUniqId = getdata.Easycase.uniq_id;
		var caseNo = getdata.Easycase.case_no;
		var caseUserId = getdata.Easycase.user_id;
		var caseTypeId = getdata.Easycase.type_id;
		var projId = getdata.Easycase.project_id;
		var caseLegend = getdata.Easycase.legend;
		var casePriority = getdata.Easycase.priority == null ? 1:getdata.Easycase.priority;
		var caseFormat = getdata.Easycase.format;
		var caseTitle = getdata.Easycase.title;
		var isactive = getdata.Easycase.isactive;
		var caseAssgnUid = getdata.Easycase.assign_to;
		var getTotRep = 0;
        var max_lgndall = 0;
        if(getdata.Easycase.priority == null || getdata.Easycase.priority == ''){
            getdata.Easycase.priority = 1 ;
        }
        if(typeof status_lists != 'undefined' && status_lists.length !=0){
            for(var kys in status_lists){
                if(getdata.Easycase.project_id == kys)
                {
                    max_lgndall = status_lists[kys];
                }
            }
        }
		if(getdata.Easycase.case_count && getdata.Easycase.case_count!=0) {		
			getTotRep = getdata.Easycase.case_count;
		}

		if(caseUrl == caseUniqId) {
			openId = count;
		}
		if(caseLegend==2 || caseLegend==4){
			var headerlegend = 2;
		}else{
			var headerlegend = caseLegend;
		}
		var chkDat = 0;

		if(projUniq=='all' && (typeof getdata.Easycase.pjname !='undefined')){
			projectName = getdata.Easycase.pjname;
			projectUniqid = getdata.Easycase.pjUniqid;
		}else if(projUniq!='all'){
			projectName = getdata.Easycase.pjname;
			projectUniqid = getdata.Easycase.pjUniqid;
		}
		if(projUniq=='all') { %>
    <tr>
        <td colspan="5" align="left" class="tkt_pjname"><div class="<% if(count!=1) {%>y_day<% } %>"><%= getdata.Easycase.pjname %></div></td>
    </tr>
    <% 		}
		if(groupby && groupby!='date'){
			if(groupby=='status' && (headerlegend != prvGrpvalue)){%>
    <tr><td colspan="5" align="left" class="curr_day">
            <% if(getdata.Easycase.csSts){ %>
                <%= getdata.Easycase.stsgrp %>
               <% }else{ %>
            <%= easycase.getStatus(getdata.Easycase.type_id, getdata.Easycase.legend) %>
               <% } %>
        </td></tr>
    <% prvGrpvalue= headerlegend; 
			}else if(groupby=='priority' && (getdata.Easycase.priority != prvGrpvalue)){%>
    <tr><td colspan="5" align="left" class="curr_day"><%= easycase.getColorPriority(getdata.Easycase.priority) %></td></tr>
    <%	prvGrpvalue = getdata.Easycase.priority;
			}else if(groupby=='due_date' && (getdata.Easycase.csDueDate !=prvGrpvalue)){%>
    <tr><td colspan="5" align="left" class="curr_day"><%= getdata.Easycase.csDueDate %></td></tr>
    <%	prvGrpvalue= getdata.Easycase.csDueDate;
			}else if(groupby=='assignto' && (getdata.Easycase.assign_to !=prvGrpvalue)){%>
    <tr><td colspan="5" align="left" class="curr_day"><%= getdata.Easycase.asgnShortName %></td></tr>
    <%	prvGrpvalue= getdata.Easycase.assign_to;
			}
		}else{
			if(getdata.Easycase.newActuldt && getdata.Easycase.newActuldt!=0) {%>
    <tr>
        <td colspan="5" align="left" class="curr_day"><div class="<% if(count!=1 && !getdata.Easycase.pjname) {%>y_day<% } %>"><%= getdata.Easycase.newActuldt %></div></td>
    </tr>
    <%	}}
		var bgcol = "#F2F2F2";
		if(chkDat == 1) { bgcol = "#FFF"; }
		var borderBottom = "";
		if(pgCaseCnt == count) { borderBottom = "border-bottom:1px solid #F2F2F2;"; } %>
    <% if(isactive==0) {%>
    <tr class="tr_all" id="curRow<%= caseAutoId %>" style="background: #F3F3F3;">
        <% }else {%>
    <tr class="tr_all" id="curRow<%= caseAutoId %>">
        <% }%>
        <td <% if(groupby =='' || groupby !='priority'){%>class="pr_<%= easycase.getPriority(casePriority) %>"<% } %> valign="top">
            <% if(typeof lgnd_max != 'undefined' && (caseLegend != 3 && lgnd_max ==0 && getdata.Easycase.legend != lgnd_max && projUniq!='all') || (max_lgndall ==0 && getdata.Easycase.legend != max_lgndall && (projUniq=='all' && caseLegend != 3))) { %>
            <input type="checkbox" style="cursor:pointer" id="actionChk<%= count %>" value="<%= caseAutoId + '|' + caseNo + '|' + caseUniqId %>" class="fl mglt chkOneTsk">
            <% } else if(typeof lgnd_max != 'undefined' && (caseLegend == 3 && lgnd_max ==0 && getdata.Easycase.legend != lgnd_max )) { %>
            <input type="checkbox" id="actionChk<%= count %>" checked="checked" value="<%= caseAutoId + '|' + caseNo + '|closed' %>" disabled="disabled" class="fl mglt chkOneTsk">
            <%  } else if(typeof lgnd_max != 'undefined' && (lgnd_max !=0 && getdata.Easycase.legend == lgnd_max) || (max_lgndall !=0 && getdata.Easycase.legend == max_lgndall && projUniq=='all'))  { %>
            <input type="checkbox" id="actionChk<%= count %>" checked="checked" value="<%= caseAutoId + '|' + caseNo + '|closed' %>" disabled="disabled" class="fl mglt chkOneTsk">
            <% } else { %>
            <input type="checkbox" style="cursor:pointer" id="actionChk<%= count %>" value="<%= caseAutoId + '|' + caseNo + '|' + caseUniqId %>" class="fl mglt chkOneTsk">
            <% } %>
            <input type="hidden" id="actionCls<%= count %>" value="<%= caseLegend %>" disabled="disabled" size="2"/>
            <div class="dropdown fl">
                <div class="sett" data-toggle="dropdown"></div>
                <ul class="dropdown-menu sett_dropdown-caret">
                    <li class="pop_arrow_new"></li>
                    <% var caseFlag="";
					if(caseLegend == 1) { caseFlag=1; }
					if(getdata.Easycase.isactive == 1) { %>
                    <li onclick="startCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" id="start<%= caseAutoId %>" style=" <% if(caseFlag == "1"){ %>display:block<% } else { %>display:none<% } %>">
                        <a href="javascript:void(0);"><div class="act_icon act_start_task fl" title="<?php echo __("Start"); ?>"></div><?php echo __("Start"); ?></a>
                    </li>
                    <% }
					if(caseLegend == 1 || caseLegend == 2 || caseLegend == 4) { caseFlag=2; }
					if(getdata.Easycase.isactive == 1){ %>
                    <li onclick="caseResolve(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" id="resolve<%= caseAutoId %>" style=" <% if(caseFlag == 2){ %> display:block <% } else { %> display:none <% } %>">
                        <a href="javascript:void(0);"><div class="act_icon act_resolve_task fl" title="<?php echo __("Resolve"); ?>"></div><?php echo __("Resolve"); ?></a>
                    </li>
                    <% }
					if(caseLegend == 1 || caseLegend == 2 || caseLegend == 4 || caseLegend == 5) { caseFlag=5; }
					if(getdata.Easycase.isactive == 1 && (SES_TYPE<=2 || (getdata.Easycase.user_id==SES_ID))){ %>
                    <li onclick="setCloseCase(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'' + caseUniqId + '\'' %>);" id="close<%= caseAutoId %>" style=" <% if(caseFlag == 5) { %>display:block <% } else { %>display:none<% } %>">
                        <a href="javascript:void(0);"><div class="act_icon act_close_task fl" title="<?php echo __("Close"); ?>"></div><?php echo __("Close"); ?></a>
                    </li>
                    <% } %>
                    <?php if(defined('TLG') && TLG == 1){ ?>
					<% if(getdata.Easycase.isactive == 1) { %>
                    <li onclick="createlog(<%= '\'' + caseAutoId + '\'' %>,<%= '\'' + escape(caseTitle) + '\'' %>,<%= '\'\'' %>,<%= '\'\'' %>,this);" class="anchor" data-puid = "<%= projectUniqid %>">
                            <a><div class="act_icon act_log_task fl" title="<?php echo __('Log Time');?>"></div><?php echo __('Log Time');?></a>
                    </li>
                    <% if(caseLegend !=3 && caseTypeId != 10){ %>
                    <li onclick="startTimer(<%= '\'' + caseAutoId + '\'' %>,<%= '\'' + escape(caseTitle) + '\'' %>, <%= '\'' + caseUniqId + '\'' %>, <%= '\'' + projectUniqid + '\'' %>, <%= '\'' + projectName + '\'' %>)">
                         <a href="javascript:void(0);"><div class="act_icon act_timer fl" title="<?php echo __("Start timer"); ?>"></div><?php echo __("Start timer"); ?></a>
                    </li>
                    <% } } %>
                    <?php } ?>
                    <% if((caseFlag == 5 || caseFlag==2) && getdata.Easycase.isactive == 1) { %>
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
					if( SES_ID == caseUserId) { caseFlag=3; }
					if(getdata.Easycase.isactive == 1 && getdata.Easycase.reply_cnt == 0 && caseLegend == 1){ %>
                    <li onclick="editask(<%= '\''+ caseUniqId+'\',\''+projectUniqid+'\',\''+projectName+'\'' %>);" id="edit<%= caseAutoId %>" style=" <% if(caseFlag == 3 || SES_TYPE == 1 || SES_TYPE == 2){ %>display:block <% } else { %>display:none<% } %>">
                        <a href="javascript:void(0);"><div class="act_icon act_edit_task fl" title="<?php echo __("Edit"); ?>"></div><?php echo __("Edit"); ?></a>
                    </li>
                    <% }
					if((caseLegend == 1 || caseLegend == 2 || caseLegend == 4)) { caseFlag=2; }
					if((SES_TYPE == 1 || SES_TYPE == 2) || ((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) &&  (SES_ID == caseUserId))){
					%>
					<% if(getdata.Easycase.isactive == 1){ %>
		    <li data-prjid="<%= projId %>" data-caseid="<%= caseAutoId %>" data-caseno="<%= caseNo %>"  id="mv_prj<%= caseAutoId %>" style=" " onclick="mvtoProject(<%= '\'' + count + '\'' %>,this);">
		    <a href="javascript:void(0);"><div class="act_icon pro_mov fl" title="<?php echo __("Move to Project"); ?>"></div><?php echo __("Move to Project"); ?></a>
		    </li>
		    <% } %>
		    <% if(getdata.Easycase.isactive == 0){ %>
			<li data-prjid="<%= projId %>" data-caseid="<%= caseAutoId %>" data-caseno="<%= caseNo %>"  id="mv_prj<%= caseAutoId %>" style=" ">
				<a onclick="restoreFromTask(<%= caseAutoId %>,<%= projId %>,<%= caseNo %>)" href="javascript:void(0);"><div class="act_icon act_restore_task fl" title="<?php echo __("Restore"); ?>"></div><?php echo __("Restore"); ?></a>
			</li>
			<li data-prjid="<%= projId %>" data-caseid="<%= caseAutoId %>" data-caseno="<%= caseNo %>"  id="mv_prj<%= caseAutoId %>" style=" ">
			    <a onclick="removeFromTask(<%= caseAutoId %>,<%= projId %>,<%= caseNo %>)" href="javascript:void(0);"><div class="act_icon act_del_task fl" title="<?php echo __("Remove"); ?>"></div><?php echo __("Remove"); ?></a>
			</li>
		    <% } %>
                    <% }
					if(getdata.Easycase.isactive == 1){ %>
                    <li onclick="moveTask(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'\'' %>,<%= '\'' + projId + '\'' %>);" id="moveTask<%= caseAutoId %>" style=" <% if(caseFlag == 2){ %> display:block <% } else { %> display:none <% } %>">
                        <a href="javascript:void(0);"><div class="act_icon task_move_mlst fl" title="<?php echo __("Move Task To Milestone"); ?>"></div><?php echo __("Move to Milestone"); ?></a>
                    </li>
                    <% } %>
                    <% if(getdata.Easycase.milestone_id){ %>
                    <li onclick="removeTask(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>, <%= '\'\'' %>,<%= '\'' + projId + '\'' %>);" id="moveTask<%= caseAutoId %>" style=" <% if(caseFlag == 2){ %> display:block <% } else { %> display:none <% } %>">
                        <a href="javascript:void(0);"><div class="act_icon task_remove_mlst fl" title="<?php echo __("Move Task To Milestone"); ?>"></div><?php echo __("Remove from"); ?> <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo __("Milestone"); ?></a>
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
            <% if(((caseLegend == 1 || caseLegend == 2 || caseLegend == 4) || (custom_workflow !=0 && highest_status != caseLegend)) && getdata.Easycase.isactive==1) {
                var showQuickAct = 1;
            } 
            var custom_maxlgnd =projUniq != 'all' ? lgnd_max : max_lgndall;
            if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd ){
                var chckmaxlegend = 1 ;
            }
            %>
            <div class="dropdown fl" style="width:32px;">
                <div id="showUpdStatus<%= caseAutoId %>" class="type_<%= getdata.Easycase.csTdTyp[0] %> <% if(getdata.Easycase.isactive == 1){ %>clsptr<% } %> <% if($.inArray(getdata.Easycase.csTdTyp[0], ['dev', 'bug', 'upd']) == -1) { %>opcty4<% } %>" title="<%= _(getdata.Easycase.csTdTyp[1]) %>" data-toggle="dropdown"><% if(getdata.Easycase.csTdTyp[2]==0){ %><span style="margin-left: 9px;display: inline-block;overflow: hidden;text-overflow: ellipsis; white-space: nowrap;width: 40px;"><%= _(getdata.Easycase.csTdTyp[0])%></span><% } %></div>
                <span id="typlod<%= caseAutoId %>" class="type_loader">
                    <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __("Loading..."); ?>" title="<?php echo __("Loading..."); ?>"/>
                </span>
                <% if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd){ %>
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
                        <a href="javascript:void(0);" <% if(t > 12){ %> style="margin-left:27px;" <% } %>><div class="task_types_<%= t1 %> fl"></div><%= _(t2) %></a>
                    </li>
                    <% } %>
                </ul>
                <% } %>
            </div>
            <% if(isactive==0){ %>
            <div id="csStsRep<%= count %>" class="fl tsk_sts"><div class="label new" style="background-color: olive"><?php echo __('Archived');?></div></div>
            <%}else if(groupby =='' || groupby !='status'){%>
            <div id="csStsRep<%= count %>" class="fl tsk_sts">
            <% if(getdata.Easycase.csSts){ %>
                <%= getdata.Easycase.csSts %>
               <% }else{ %>
            <%= easycase.getStatus(getdata.Easycase.type_id, getdata.Easycase.legend) %>
            <% } %>
            </div>
            <% } %>
        </td>
        <td valign="top" style="padding-right:20px;text-align:right"><%= caseNo %></td>
        <td class="title_det_wd">
            <div class="fl title_wd">
                <div id="titlehtml<%= count %>" data-task="<%= caseUniqId %>" class="fl case-title <% if((getdata.Easycase.legend==3) || (typeof lgnd_max != 'undefined' && lgnd_max !=0 && getdata.Easycase.legend == lgnd_max) || (max_lgndall !=0 && getdata.Easycase.legend == max_lgndall)) { %>closed_tsk<% } %>"> <div class="case_title wrapword task_title_ipad <% if(caseTitle.length>40){%>overme<% }%> " title="<%= formatText(ucfirst(caseTitle)) %>  "><%= formatText(ucfirst(caseTitle)) %></div></div>
                <% if(RCT == 1 && getdata.Easycase.is_recurring == 1){ %>
                <a id="recurringTaskId_<%= caseAutoId %>" href="javascript:void(0);" onclick="showRecurringInfo(<%= caseAutoId %>);" >
                <span class="task_title_icons_reoccurrence" rel="tooltip" title="Repeated Task" style="margin-left:20px;"></span>
                </a>
                <% } else if(RCT == 1 && getdata.Easycase.is_recurring == 2){%>
				 <a id="recurringTaskId_<%= caseAutoId %>" href="javascript:void(0);" >
                    <span class="task_title_icons_reoccurrence" rel="tooltip" title="Task Created Using Recurring Task" style="margin-left:20px;"></span>
                </a>
                <% } %>
            <?php if(defined('GNC') && GNC == 1){ ?>
                <div style="width:160px; float:left; height:14px;">
                    <% if(getdata.Easycase.children && getdata.Easycase.children != ""){ %>
                        <span class="fl case_act_icons task_parent_block" id="task_parent_block_<%= caseUniqId %>">
                            <div rel="" title="<?php echo __("Parents");?>" onclick="showParents(<%= '\'' + caseAutoId + '\'' %>,<%= '\'' + caseUniqId + '\'' %>,<%= '\'' + getdata.Easycase.children + '\'' %>);" class=" task_title_icons_parents fl"></div>
                            <div class="dropdown dropup fl1 open1 showParents">
                                <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                    <li class="pop_arrow_new"></li>
                                    <li class="task_parent_msg" style=""><?php echo __("These tasks are waiting on this task.");?></li>
                                    <li>
                                        <ul class="task_parent_items" id="task_parent_<%= caseUniqId %>" style="">
                                            <li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </span>
                    <% } %>
                    <% if(getdata.Easycase.depends && getdata.Easycase.depends != ""){ %>
                        <span class="fl case_act_icons task_dependent_block" id="task_dependent_block_<%= caseUniqId %>">
                            <div rel="" title="<?php echo __("Dependents");?>" onclick="showDependents(<%= '\'' + caseAutoId + '\'' %>,<%= '\'' + caseUniqId + '\'' %>,<%= '\'' + getdata.Easycase.depends + '\'' %>);" class=" task_title_icons_depends fl"></div>
                            <div class="dropdown dropup fl1 open1 showDependents">
                                <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                    <li class="pop_arrow_new"></li>
                                    <li class="task_dependent_msg" style=""><?php echo __("Task can't start. Waiting on these task to be completed.");?></li>
                                    <li>
                                        <ul class="task_dependent_items" id="task_dependent_<%= caseUniqId %>" style="">
                                            <li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </span>
                    <% } %>
                </div>
            <?php } ?>
            </div>
            <div class="att_fl fr" <% if(getdata.Easycase.format != 1 && getdata.Easycase.format != 3) { %> style="display:none;" id="fileattch<%= count %>"<% } %>></div>
            <div class="cb rcb"></div>
            <div class="fnt999 fl">
                <div class="fl">
                    <span id="stsdisp<%= caseAutoId %>" class="cview_hide"><% if(getTotRep && getTotRep!=0) { %><?php echo __("updated"); ?><% } else { %><?php echo __("created"); ?><% } %></span> <?php echo __("by"); ?> <span <% if(getdata.Easycase.usrName) { %> original-title="<%= getdata.Easycase.usrName %>" <% } %>><%= _(getdata.Easycase.usrShortName) %></span>
                    <span class="cview_hide">
                        <% if(getdata.Easycase.updtedCapDt.indexOf('Today')==-1 && getdata.Easycase.updtedCapDt.indexOf('Y\'day')==-1) { %><?php echo __("on"); ?><% } %>
                    </span>
                    <span id="timedis<%= count %>" class="cview_hide">
                        <%= getdata.Easycase.updtedCapDt %>.
                    </span>
                    <span id="timedis<%= count %>" class="cview_show" title="<%= getdata.Easycase.updtedCapDt %>">
                        <%= getdata.Easycase.fbstyle %>.
                    </span>
                </div>
                <div class="fl" style="<% if(!getTotRep || getTotRep==0) { %>display:none<% } %>">
                    <div id="repno<%= count %>" class="fl bblecnt"></div>
					(<% if(getTotRep && getTotRep!=0) { %><%= getTotRep %><% } %>)
                </div>
            </div>
            <div class="cb"></div>
        </td>
        <% if(isactive==0){ %>
        <td></td>
        <% } else {%>
        <td valign="top">
            <div class="dropdown fl">
                <% if((projUniq != 'all')){ %>
                <span id="showUpdAssign<%= caseAutoId %>" title="edit Assign to" class="clsptr" <% if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd){ %> data-toggle="dropdown" onclick="displayAssignToMem(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + projUniq + '\'' %>,<%= '\'' + caseAssgnUid + '\'' %>,<%= '\'' + caseUniqId + '\'' %>)" <% } %>><%= getdata.Easycase.asgnShortName %><schckmaxlegendpan class="due_dt_icn"></span></span>
                <% } else { %>
                <span id="showUpdAssign<%= caseAutoId %>" style="cursor:text;text-decoration:none;color:#666666;"><%= getdata.Easycase.asgnShortName %></span>
                <% } %>
                <% if((projUniq != 'all')){ %>
                <span id="asgnlod<%= caseAutoId %>" class="asgn_loader">
                    <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __("Loading..."); ?>" title="<?php echo __("Loading..."); ?>"/>
                </span>
                <% } %>
                <% if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd){ %>
                <ul class="dropdown-menu asgn_dropdown-caret" id="showAsgnToMem<%= caseAutoId %>">
                    <li class="pop_arrow_new"></li>
                    <li class="text-centre"><img src="<?php echo HTTP_IMAGES; ?>images/del.gif" id="assgnload<%= caseAutoId %>" /></li>
                </ul>
                <% } %>
            </div>
        </td>
        <% } %>
        <td class="fnt12" valign="top">
            <div class="dropdown fl">
                <% 
                if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd){ %>
                <div class="fl" <% if(getdata.Easycase.isactive == 1 && (getdata.Easycase.legend!=3 && getdata.Easycase.legend!=5) && getdata.Easycase.legend != custom_maxlgnd){ %> data-toggle="dropdown" original-title="edit Due Date" style="cursor:pointer"<% } %>>
                     <span id="showUpdDueDate<%= caseAutoId %>" title="<%= getdata.Easycase.csDuDtFmtT %>">
                        <%= getdata.Easycase.csDuDtFmt %>
                        <% if(chckmaxlegend == 1){ %>
                        <span class="due_dt_icn"></span>
                        <% } %>
                    </span>
                    <span id="datelod<%= caseAutoId %>" class="asgn_loader">
                        <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __("Loading..."); ?>" title="<?php echo __("Loading..."); ?>"/>
                    </span>
                </div>
                <% } %>
                <% if(getdata.Easycase.isactive == 1 && showQuickAct == 1){ %>
                <ul class="dropdown-menu dudt_dropdown-caret">
                    <li class="pop_arrow_new"></li>
                    <li><a href="javascript:void(0);" onclick="changeCaseDuedate(<%= '\'' + caseAutoId + '\'' %>, <%= '\'' + caseNo + '\'' %>);changeDueDate(<%= '\'' + caseAutoId + '\', \'00/00/0000\', \'No Due Date\', \'' + caseUniqId + '\'' %>)"><?php echo __("No Due Date"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="changeCaseDuedate(<%= '\'' + caseAutoId + '\', \'' + caseNo + '\'' %>); changeDueDate(<%= '\'' + caseAutoId + '\', \'' + mdyCurCrtd + '\', \'Today\', \'' + caseUniqId + '\'' %>)"><?php echo __("Today"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="changeCaseDuedate(<%= '\'' + caseAutoId + '\', \'' + caseNo + '\'' %>); changeDueDate(<%= '\'' + caseAutoId + '\', \'' + mdyTomorrow + '\', \'Tomorrow\', \'' + caseUniqId + '\'' %>)"><?php echo __("Tomorrow"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="changeCaseDuedate(<%= '\'' + caseAutoId + '\', \'' + caseNo + '\'' %>); changeDueDate(<%= '\'' + caseAutoId + '\', \'' + mdyMonday + '\', \'Next Monday\', \'' + caseUniqId + '\'' %>)"><?php echo __("Next Monday"); ?></a></li>
                    <li><a href="javascript:void(0);" onclick="changeCaseDuedate(<%= '\'' + caseAutoId + '\', \'' + caseNo + '\'' %>); changeDueDate(<%= '\'' + caseAutoId + '\', \'' + mdyFriday + '\', \'This Friday\', \'' + caseUniqId + '\'' %>)"><?php echo __("This Friday"); ?></a></li>
                    <li>
                        <a href="javascript:void(0);" class="cstm-dt-option" data-csatid="<%= caseAutoId %>">
                            <input value="" type="hidden" id="set_due_date_<%= caseAutoId %>" class="set_due_date" title="<?php echo __("Custom Date"); ?>" style=""/>
                            <span style="position:relative;top:2px;cursor:text;"><?php echo __("Custom Date"); ?></span>
                        </a>
                    </li>
                </ul>
                <% } %>
            </div>
        </td>
    </tr>
    <%
		totids += caseAutoId + "|";
	}
    }
    if(!caseCount || caseCount==0){
    var case_type = $("#caseMenuFilters").val(); %>
    <tr>
        <td colspan="5" align="center" style="padding:10px 0;color:#FF0000">

            <% if(case_type == 'cases' || case_type == ''){
				if(filterenabled){%>
					<?php echo __("No Tasks"); ?>
            <% }else{ %>
            <?php echo $this->element('no_data', array('nodata_name' => 'tasklist','case_type'=>'')); ?>
            <% } %>
            <% }else if(case_type == 'assigntome'){
				if(filterenabled){ %>
					<?php echo __("No tasks for me"); ?>
            <% }else{ %>
            <?php echo $this->element('no_data', array('nodata_name' => 'tasklist','case_type'=>'assigntome')); ?>
            <% } %>
            <% }else if(case_type == 'overdue'){
				if(filterenabled){ %>
					<?php echo __("No tasks as overdue"); ?>
            <% }else{ %>
            <?php echo $this->element('no_data', array('nodata_name' => 'tasklist','case_type'=>'overdue')); ?>
            <% } %>
            <% }else if(case_type == 'delegateto'){
				if(filterenabled){ %>
					<?php echo __("No tasks delegated"); ?>
            <% }else{ %>
            <?php echo $this->element('no_data', array('nodata_name' => 'tasklist','case_type'=>'delegateto')); ?>
            <% } %>
            <% }else if(case_type == 'highpriority'){
				if(filterenabled){ %>
					<?php echo __("No high priority tasks"); ?>
            <% }else{ %>
            <?php echo $this->element('no_data', array('nodata_name' => 'tasklist','case_type'=>'highpriority')); ?>
            <% } %>
            <% } %>
        </td>
    </tr>
    <% } %>
</table>
<% $("#task_paginate").html('');
if(caseCount && caseCount!=0) {
	var pageVars = {pgShLbl:pgShLbl,csPage:csPage,page_limit:page_limit,caseCount:caseCount};
	$("#task_paginate").html(tmpl("paginate_tmpl", pageVars));
} %>
<?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('task')==1){ 
    /**Not Show create project */
}else{ ?> 
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
<input type="hidden" name="hid_cs" id="hid_cs" value="<%= count %>"/>
<input type="hidden" name="totid" id="totid" value="<%= totids %>"/>
<input type="hidden" name="chkID" id="chkID" value=""/>
<input type="hidden" name="slctcaseid" id="slctcaseid" value=""/>
<input type="hidden" id="getcasecount" value="<%= caseCount %>" readonly="true"/>
<input type="hidden" id="openId" value="<%= openId %>" />
<input type="hidden" id="email_arr" value=<%= '\'' + ((typeof email_arr != 'undefined' && email_arr)?email_arr:'') + '\''  %>  />
       <input type="hidden" id="curr_sel_project_id" value="<% if(projUniq!='all'){%><%= projId %> <% } %>"  />