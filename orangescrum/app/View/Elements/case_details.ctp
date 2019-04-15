<% var showQuickAct = showQuickActDD = 0;
if(((csLgndRep == 1 || csLgndRep == 2 || csLgndRep == 4) || (custom_workflow !=0 && highest_status != csLgndRep)) && is_active==1) {
	var showQuickAct = 1;
}
if(showQuickAct){
	var showQuickActDD = 1;
}
%>
<% if(csStartDtFmtT != 'NoSt'){ %>
<style>
hr {margin-top: 9px;
    margin-bottom: 9px;}
.due-txt, .stdudtfont {font-size:16px;}
</style>
<% } %>
<div id="t_<%= csUniqId %>" class="task_detail" style="margin-top:-15px;">
	<div class="page-wrapper">
		<div class="col-lg-9 fl task_details_row">
        <?php if(defined('TSG') && TSG == 1){ ?>
        <section class="col-lg-12 detail-status-bar">
            <% var width = completedtask ;
               var sts_color = csLgndcolor ;
               var sts_name = csLgndName ;
               %>
            <ul class="status-bar-ul"><li style="width:<%= completedtask %>%;background:<%= sts_color%>;display: block;margin: 0;padding: 0; vertical-align: middle;"><span class="arrow-frwd" style="border-left: 10px solid <%= sts_color%>"></span> </li></ul>
            <div class="status-title" style="color:<%= sts_color %>;text-align:left;top:0px;font-weight:600"> <%= sts_name %>&nbsp;( <%= completedtask %>% )</div>
        </section>
        <div class="cb"></div>
        <?php } ?>
		<div class="row">
		  <div class="col-lg-12 task_details_title"> 
				<h1 class="wrapword">
					#<%= csNoRep %>: <%= formatText(ucfirst(caseTitle)) %>
				</h1>
				<div class="last_update">
					<% if(cntdta && (cntdta>0)) { %>Last updated<% } else { %>Created<% } %> by <b><%= lstUpdBy %></b>
					<% if(lupdtm.indexOf('Today')==-1 && lupdtm.indexOf('Y\'day')==-1) { %>on<% } %>
					<span title="<%= lupdtTtl %>"><%= lupdtm %>.</span>
					<% if(cntdta) { %>
					<span>&nbsp;<i class="icon-twit-count"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<%= total %>)</span>
					<% } %>
                    <?php if(defined('GNC') && GNC == 1){ ?>
                    <span style="display:inline-block;">
                        <% if(children && children != ""){ %>
                        <span class="fl  task_parent_block" id="task_parent_block_<%= csUniqId %>">
                            <div rel="" title="<?php echo __("Parents");?>" onclick="showParents(<%= '\'' + csid + '\'' %>,<%= '\'' + csUniqId + '\'' %>,<%= '\'' + children + '\'' %>);" class=" task_title_icons_parents fl"></div>
                            <div class="dropdown dropup fl1 open1 showParents">
                                <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                    <li class="pop_arrow_new"></li>
                                    <li class="task_parent_msg" style=""><?php echo __("These tasks are waiting on this task.");?></li>
                                    <li><ul class="task_parent_items" id="task_parent_<%= csUniqId %>"><li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li></ul></li>
                                </ul>
                            </div>
                        </span>
                        <% } %>
                        <% if(depends && depends != ""){ %>
                        <span class="fl  task_dependent_block" id="task_dependent_block_<%= csUniqId %>">
                            <div rel="" title="<?php echo __("Dependents");?>" onclick="showDependents(<%= '\'' + csid + '\'' %>,<%= '\'' + csUniqId + '\'' %>,<%= '\'' + depends + '\'' %>);" class=" task_title_icons_depends fl"></div>
                            <div class="dropdown dropup fl1 open1 showDependents">
                                <ul class="dropdown-menu  bottom_dropdown-caret" style="left: -11px; padding:5px; cursor:default; min-width:250px; max-width:500px;">
                                    <li class="pop_arrow_new"></li>
                                    <li class="task_dependent_msg" style=""><?php echo __("Task can't start. Waiting on these task to be completed.");?></li>
                                    <li><ul class="task_dependent_items" id="task_dependent_<%= csUniqId %>"><li style="text-align:center;" class="loader"><img src="<?php echo HTTP_ROOT;?>img/images/loader1.gif"></li></ul></li>
                                </ul>
                            </div>
                        </span>
                        <% } %>
                    </span>
                    <?php } ?>
				</div>
		  </div>
		  <div class="col-lg-12 task_details_title"> 
				 <div class="col-lg-4 task_elements">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><?php echo __("Type"); ?>:</td>
							<td>
								<div id="typdiv<%= csAtId %>" class="fl typ_actions <% if(showQuickAct==1){ %> dropdown<% } %>">
									<div class="fl task_types_<%= taskTyp.short_name %>"></div>
									<b <% if(showQuickAct==1){ %> class="quick_action" data-toggle="dropdown" <% } %>><%= _(taskTyp.name) %></b>
									<% if(showQuickAct==1){ %>
									<ul class="dropdown-menu quick_menu">
										<li class="pop_arrow_new"></li>
										<% for(var k in GLOBALS_TYPE) {
											var v = GLOBALS_TYPE[k];
											var t = v.Type.id;
											var t1 = v.Type.short_name;
											var t2 = _(v.Type.name);
										%>
										<li>
											<a href="javascript:void(0);" <% if(t > 12){ %> style="margin-left:27px;" <% } %> onclick="changetype(<%= '\'' + csAtId + '\'' %>, <%= '\'' + t + '\'' %>, <%= '\'' + t1 + '\'' %>, <%= '\'' + t2 + '\'' %>, <%= '\'' + csUniqId + '\'' %>, <%= '\'' + csNoRep + '\'' %>)"><div class="task_types_<%= t1 %> fl"></div><%= t2 %></a>
										</li>
										<% } %>
									</ul>
									<% } %>
								</div>
								<span id="dettyplod<%= csAtId %>" style="display:none">
									<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
								</span>
							</td>
						</tr>
						<tr>
							<td><?php echo __("Priority"); ?>:</td>
							<td>
								<div id="pridiv<%= csAtId %>" data-priority ="<%= csPriRep %>" class="pri_actions <%= protyCls %><% if(showQuickAct==1){ %> dropdown<% } %>">
									<b <% if(showQuickAct==1){%> class="quick_action" data-toggle="dropdown" <% } %>><%= protyTtl %></b>
									<% if(showQuickAct==1){ %>
									<ul class="dropdown-menu quick_menu">
										<li class="pop_arrow_new"></li>
										<li><a href="javascript:void(0);" class="low_priority" onclick="detChangepriority(<%= '\'' + csAtId + '\', \'2\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Low');?></a></li>
										<li><a href="javascript:void(0);" class="medium_priority" onclick="detChangepriority(<%= '\'' + csAtId + '\', \'1\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Medium'); ?></a></li>
										<li><a href="javascript:void(0);" class="high_priority" onclick="detChangepriority(<%= '\'' + csAtId + '\', \'0\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('High'); ?></a></li>
									</ul>
									<% } %>
								</div>
								<span id="prilod<%= csAtId %>" style="display:none">
									<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
								</span>
							</td>
						</tr>
						<tr>
							<td><?php echo __("Status"); ?>:</td>
							<% if(TSG != 1){ %>
                                <td><% if(is_active){ %><%= easycase.getColorStatus(csTypRep, csLgndRep) %> <% } else { %><span class="fnt_clr_rd">Archived</span><% } %></td>
                            <% } else { %>
                                <td>
                                    <% if(is_active){ %>
                                    <div id="stsdiv_<%= csAtId %>" data-priority ="<%= csLgndRep %>" class=" dropdown">
									<span style="font-weight:bold;color:<%= csLgndcolor %>"  class="quick_action" data-toggle="dropdown"><%= csLgndName %></span>
									
								</div>
								<span id="prilod<%= csAtId %>" style="display:none">
									<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
								</span>
                                    <% } else { %><span class="fnt_clr_rd">Archived</span><% } %>
                                    </td>
                            <% } %>    
                            </tr>
					</table>
				 </div>
				 <div class="col-lg-4 task_elements">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td><?php echo __("Est. Hour(s)"); ?>:</td>
                            <?php if(defined('TLG') && TLG == 1){ ?>
                            <td class="esttd" style="padding-right:0px;line-height:18px;">
							<% if(csLgndRep != 3 && csLgndRep != 5 && taskTyp.id !== "10"){ %>
					            <b class="estb fl border ttc" style="cursor:pointer;margin:0px;">
					            <% if(estimated_hours != 0.0) { %>
					                    <%= format_time_hr_min(estimated_hours) %>
					            <% } else { %>
					                    <?php echo __("None"); ?>
					            <% } %></b>
					            <% var est_time = Math.floor(estimated_hours/3600)+':'+(Math.round(Math.floor(estimated_hours%3600)/60)<10?"0":"")+Math.round(Math.floor(estimated_hours%3600)/60); %>
					            <input type="text" id="est_hr<%=csAtId%>" class="est_hr form-control check_minute_range" style="width:80px;height:23px;display:none;" maxlength="5" rel="tooltip" title="You can enter time as 1.5(that mean 1 hour and 30 minutes)" onkeypress="return numeric_decimal_colon(event)" onblur="changeEstHour(<%= '\''+csAtId+'\' , \''+csUniqId+'\', \''+csNoRep+ '\', \''+est_time+ '\'' %>)" value="<%= est_time %>" placeholder="hh:mm" data-default-val="<%=est_time%>"/>
					            <% if(csLgndRep !=3 && csLgndRep !=5){ %>
					                <!--<span class="estdrp due_dt_icn fl" style="display:none;margin-left:5px;margin-top:5px;"></span>-->
					            <% } %>
							<% }else { %>
							<b class="ttc">
					            <% if(estimated_hours != 0.0) { %>
								<%= format_time_hr_min(estimated_hours) %>
							<% } else { %>
								<?php echo __("None"); ?>
								<% } %></b>
							<% } %>
							<span id="estlod<%=csAtId%>" style="display:none;margin-left:0px;">
								<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
							</span>
							</td>
                            <?php } else{ ?>
                            <td><b>
					            <% if(estimated_hours != 0.0) { %>
								<%= estimated_hours %>
					            <% } else { %>
								<i class="no_due_dt"><?php echo __("None"); ?></i>
					            <% } %>
							</b></td>
                            <?php } ?>
						</tr>
						<tr>
							<td><?php echo __("Hour(s) Spent"); ?>:</td>
							<td><b>
							<% if(hours != 0.0) { %>
                            <?php if(defined('TLG') && TLG == 1){ ?>
								<%= format_time_hr_min(hours) %>
                            <?php }else{ ?>
                                <%= hours %>
                            <?php } ?>
							<% } else { %>
								<i class="no_due_dt"><?php echo __("None"); ?></i>
							<% } %>
							</b></td>
						</tr>
						<tr>
							<td><?php echo __("Milestone"); ?>:</td>
							<td><b>
							<% if(mistn != '') { %>
								<%= mistn %>
							<% } else { %>
								<i class="no_due_dt"><?php echo __("None"); ?></i>
							<% } %>
							</b></td>
						</tr>
					</table>
				 </div>
				 <div class="col-lg-4 task_elements">
					<table cellpadding="0" cellspacing="0">
						<?php /*?><tr>
							<td>Milestone:</td>
							<td><b>
							<%= mistn?shortLength(mistn,20):'<i class="no_due_dt">None</i>' %>
							</b></td>
						</tr><?php */?>
						<tr>
							<td><?php echo __("Project"); ?>:</td>
							<td>
								<b class="ttc"><%= shortLength(projName,16) %></b>
							</td>
						</tr>
						<tr>
							<td><?php echo __("Task Progress"); ?>:</td>
							<td>
							<% 
								if((csLgndRep == 5 || csLgndRep == 3) && TSG == 0) {
								completedtask = 100;
							    } 
								if(csLgndRep ==4 ){
								   completedtask = 50 ;
								}
							    var progress = 0;
							    if(completedtask){
								progress = completedtask;
							    }
							%>
							<div class="tsk_det_progrs">
							<div class="imprv_bar_fade col-lg-12">
							    <div class="cmpl_fade" style="width:<%= progress %>%"></div>
							</div>
							<center><div class="tsk_prgrss"><%= progress %>%</div></center>
							</div>
							</td>
						</tr>
					</table>
				 </div>
		  </div> 
		  <div class="cb"></div>
		  <div class="col-lg-12">
			<div class="details_task_block">
				<div class="details_task_head">
					<div class="fl">
						<% if(pstFileExst) { %>
                            <img data-original="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<%= pstPic %>&sizex=35&sizey=35&quality=100" class="lazy round_profile_img rep_bdr" title="<%= pstNm %>" width="35" height="35" />
						<% } else { %>
                            <span class="round_profile_img <%= post_colr %> act_prof_styl"><%= post_shtnm %></span>
					<?php /*	<img data-original="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=user.png&sizex=35&sizey=35&quality=100" class="lazy round_profile_img rep_bdr" title="<%= pstNm %>" width="35" height="35" /> */ ?>
						<% } %>
					</div>
					<div class="fl">
						<span><?php echo __("Created by"); ?> <b class="ttc"><%= shortLength(crtdBy,20) %></b></span>
						<div class="fnt999"> <%= frmtCrtdDt %></div>
					</div>                                    
				</div>
				<% if(dispSec) { %>
				<div class="details_task_desc wrapword">
					<%= csMsgRep %>
				<% var fc = 0;
				if(csFiles) { %>
					<br/><br/>
					<div class="case_clip"><div></div></div>
					<% var images = ""; var caseFileName = "";
					for(var fileKey in filesArr) {
						var getFiles = filesArr[fileKey];
						caseFileName = getFiles.CaseFile.file;
						downloadurl = getFiles.CaseFile.downloadurl;
                        var d_name = getFiles.CaseFile.display_name;
						if(!d_name){
							d_name = caseFileName;
						}
						if(getFiles.CaseFile.is_exist) {
							fc++; %>
							<div class="attch_file_bg fl">
								<div class="fl tsk_ficn"><div class="tsk_fl <%= easycase.imageTypeIcon(getFiles.CaseFile.format_file) %>_file"></div></div>
								<div class="fl">
									<% if(downloadurl) { %>
									<span><a href="<%= downloadurl %>" target="_blank" alt="<%= caseFileName %>" title="<%= d_name %>"><%= shortLength(d_name,37) %></a></span>
									<% } else { %>
									<span><%= shortLength(d_name,37) %></span></span>
									<div class="fnt999">
									<% if(getFiles.CaseFile.is_ImgFileExt){ %>
										(<%= getFiles.CaseFile.file_size %>)&nbsp;&nbsp;
										<span class="gallery">
										<a href="<%= getFiles.CaseFile.fileurl %>" target="_blank" alt="<%= d_name %>" title="<%= d_name %>" rel="prettyPhoto[]"><?php echo __("View");?> </a>
										</span>
										&nbsp;&nbsp;
										<a href="<?php echo HTTP_ROOT; ?>easycases/download/<%= caseFileName %>" alt="<%= d_name %>" title="<%= d_name %>"><?php echo __("Download"); ?></a>
									<%  } else{ %>
										(<%= getFiles.CaseFile.file_size %>)&nbsp;
										<a href="<?php echo HTTP_ROOT; ?>easycases/download/<%= caseFileName %>" alt="<%= d_name %>" title="<%= d_name %>"><?php echo __("Download"); ?></a>
										<% } %>
									</div>
								<% } %>
								</div>
							</div>
							<% if(fc%2==0) { %>
							<div class="cb"></div>
							<% } %>
					<% 	}
					} %>
				<% } %>
				<div class="cb"></div>
				</div>
				<% } %>
			</div>
		  </div>
		  <div class="cb"></div>
		  <% if(cntdta){
		  if(total > 5){ %>
		  <div class="col-lg-12">
			<div class="fr view_rem">
				<span id="morereply<%= csAtId %>" style="<% if(cntdta > 5) { %>display:none<% } %>">
					<a href="javascript:void(0);" onclick="showHideMoreReply(<%= '\''+csAtId+'\',\'more\'' %>)">
						<% remaining = total-5;
						if(remaining == 1) { %>
							<?php echo __("View remaining"); ?> <%= remaining %> <?php echo __("thread"); ?> <%
						} else { %>
							<?php echo __("View remaining"); ?> <%= remaining %> <?php echo __("threads"); ?> <%
						} %>
					</a>
				</span>
				<span id="hidereply<%= csAtId %>" <% if(cntdta <= 5) { %> style="display:none" <% } %>>
					<a href="javascript:void(0);" onclick="showHideMoreReply(<%= '\''+csAtId+'\',\'less\'' %>)">
						<?php echo __("View latest 5"); ?>
					</a>
				</span>
				<span class="rep_st_icn"></span>
				<span id="loadreply<%= csAtId %>" style="visibility: hidden;"><img src="<?php echo HTTP_IMAGES; ?>images/del.gif" width="16" height="16" alt="loading..." title="loading..."/></span>
			</div>
			<div class="fr view_rem">
				<span id="repsort_desc_<%= csAtId %>" <%= ascStyle %>> 
					<a href="javascript:void(0);" onclick="sortreply(<%= '\''+csAtId+'\'' %>,<%= '\''+csUniqId+'\'' %>)" rel="tooltip" title="<?php echo __("View oldest thread on top"); ?>"><?php echo __("Newer"); ?></a>
				</span>
				<span id="repsort_asc_<%= csAtId %>" <%= descStyle %> > 
					<a href="javascript:void(0);" onclick="sortreply(<%= '\''+csAtId+'\'' %>,<%= '\''+csUniqId+'\'' %>)" rel="tooltip" title="<?php echo __("View newest thread on top"); ?>"><?php echo __("Older"); ?></a>
				</span>
				<span class="rep_st_icn"></span>
				<span id="loadreply_sort_<%= csAtId %>" style="visibility: hidden;"><img src="<?php echo HTTP_IMAGES; ?>images/del.gif" width="16" height="16" alt="loading..." title="loading..."/></span>
			</div>
		  </div>
		  <div class="cb"></div>
		  <input type="hidden" value="less" id="threadview_type<%= csAtId %>" />
		  <input type="hidden" value="<%= thrdStOrd %>" id="thread_sortorder<%= csAtId %>" />
		  <input type="hidden" value="<%= remaining %>" id="remain_case<%= csAtId %>" />
		  <% } %>
		  <div id="reply_content<%= csAtId %>">
		  	<div id="showhidemorereply<%= csAtId %>">
		  		<?php echo $this->element('case_reply'); ?>
		  	</div>
		  </div>
		  <% } %>
          <?php if(defined('TLG') && TLG == 1){ ?>
            <div class="time_log_reply task-details-tlog" id="reply_time_log<%= csAtId %>">
                <?php echo $this->element('Timelog.case_timelog'); ?>
            </div>
          <?php } ?>
		</div><!-- /.row --><!-- Case Detail -->
	
		<input type="hidden" name="data[Easycase][sel_myproj]" id="CS_project_id<%= csAtId %>" value="<%= projUniqId %>" readonly="true">
		<input type="hidden" name="data[Easycase][case_no]" id="CS_case_no<%= csAtId %>" value="<%= csNoRep %>" readonly="true"/>
		<input type="hidden" name="data[Easycase][type_id]" id="CS_type_id<%= csAtId %>" value="<%= csTypRep %>" readonly="true"/>
		<input type="hidden" name="data[Easycase][title]" id="CS_title<%= csAtId %>" value="" readonly="true"/>
		<input type="hidden" name="data[Easycase][priority]" id="CS_priority<%= csAtId %>" value="<%= csPriRep %>" readonly="true"/>
		<input type="hidden" name="data[Easycase][org_case_id]" id="CS_case_id<%= csAtId %>" value="<%= csAtId %>" readonly="true"/>
		<input type="hidden" name="data[Easycase][istype]" id="CS_istype<%= csAtId %>" value="2" readonly="true"/>
		<% if(is_active){ %>
        <?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('disable_replay_to_client')==0){ }else{ ?>
		<div class="reply_task_block" id="reply_box<%= csAtId %>">
			<div class="fl">
				<% if(usrFileExst){  %>
				<img data-original="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<%= usrPhoto %>&sizex=60&sizey=60&quality=100" class="lazy round_profile_img asignto" width="60" height="60" title="<%= usrName %>"/>
                                <% } 
                                else{ %>
                                <span class="round_profile_img <%= user_colr %> det_prof_styl"><%= user_shtnm %></span>
                                <% }%>
                                
			</div>
			<div class="fl col-lg-10">
				<i class="icon-reply-yelow"></i>
				<div class="fr">
					<div class="fl">
					<a href="javascript:void(0);" id="custom<%= csAtId %>"  onclick="changeToRte(<%= '\''+csAtId+'\'' %>,<%= '\''+csUniqId+'\'' %>)" style="display:none"><?php echo __("HTML Editor"); ?></a>
					<a href="javascript:void(0);" id="txt<%= csAtId %>" onclick="changeToRte(<%= '\''+csAtId+'\'' %>,<%= '\''+csUniqId+'\'' %>)" style="display:block"><?php echo __("Text Editor"); ?></a>
					</div>
					<div class="rep_st_icn"></div>
				</div>
				<div class="cb"></div>
				<div class="col-lg-12 fl">
					<div class="fl lbl-font16 wyp" id="hidstatus<%= csAtId %>" style="position:absolute;top:-30px;"><?php echo __("Write your Reply"); ?>:&nbsp;</div>
					<div class="col-lg-12 w80p">
				<span id="html<%= csAtId %>" style="display:block;">
					<span id="hidhtml<%= csAtId %>" style="display:none;">
						<textarea name="data[Easycase][message]" id="<%= 'txa_comments'+csAtId %>" rows="2" class="col-lg-12"></textarea>
						<span id="htmlloader<%= csAtId %>" style="color:#999999; display: none; float:left;">
							<?php echo __("Loading..."); ?>
						</span>
					</span>
					<span id="showhtml<%= csAtId %>" data-task="<%= csAtId %>">
						<textarea name="data[Easycase][message]" id="<%= 'txa_comments'+csAtId %>" rows="2" class="reply_txt_ipad col-lg-12" style="color:#C8C8C8"></textarea>
					</span>
				</span>
				<span id="plane<%= csAtId %>" style="display:none;">
					<textarea name="data[Easycase][message]" id="txa_plane<%= csAtId %>" rows="1" class="col-lg-12"></textarea>
				</span>
				<input type="hidden" value="1" id="editortype<%= csAtId %>"/>
					</div>	
				</div>				
				<div class="cb"></div>
				<div class="col-lg-12 m-top-20">
					<?php /*User loop */ ?>
				</div>
				<div class="cb"></div>
				

				<div class="col-lg-12 m-marg-20">
					<% var val = ""; %>
					<div class="col-lg-6 fl">
						<div class="fl lbl-font16" id="hidstatus<%= csAtId %>"><?php echo __("Status"); ?>:&nbsp;</div>
						<span id="hiddrpdwnstatus<%= csAtId %>">
						<select class="select form-control fl" style="width:170px;" onchange="valforlegend(this.value,'legend<%= csAtId %>')" >
                        <% if(typeof statuslist == 'undefined'){ %>
						<% if(csLgndRep == 1) { val = 2; %>
                                <option value="1"><?php echo __("New"); ?></option>
                                <option value="2" selected><?php echo __("In Progress"); ?></option>
                                <option value="3"><?php echo __("Close"); ?></option>
                                <option value="5"><?php echo __("Resolve"); ?></option>
						<% } else if(csLgndRep == 2 || csLgndRep == 4){ val = 2; %>
                                <option value="2" selected=selected ><?php echo __("In Progress"); ?></option>
                                <option value="3"><?php echo __("Close"); ?></option>
                                <option value="5"><?php echo __("Resolve"); ?></option>
						<% } else if(csLgndRep == 5){ val = 2; %>
                                <option value="2" selected=selected ><?php echo __("In Progress"); ?></option>
                                <option value="3"><?php echo __("Close"); ?></option>
						<% } else if(csLgndRep=="3"){ val = 2; %>
                                <option value="2" selected=selected ><?php echo __("In Progress"); ?></option>
						<% } %>
                        <% }else{
                            for(var key in statuslist){
                                var getsts = statuslist[key];
                                for(var k in getsts.Status){
                                var sts = getsts.Status[k];
                                val = sts.id; %>
                            <option value="<%= val %>" <% if(csLgndRep == val){ %>selected=selected<% } %>><%= sts.name %></option>
                        <% }
                                    } val = csLgndRep ;
                        } %>
						</select>
						<input type="hidden" name="legend" id="legend<%= csAtId %>" value="<%= val %>">
						</span>
					</div>
					<div class="col-lg-6 fl">
						<div class="fl lbl-font16"><?php echo __("Assign to"); ?>:</div>
						<select name="data[Easycase][assign_to]" id="CS_assign_to<%= csAtId %>" class="form-control fl" style="width:170px" onchange="select_reply_user(<%= '\''+csAtId+'\'' %>,this);">
						<% if(countJS(allMems)) {
							for(var casekey in allMems) {
								var asgnMem = allMems[casekey];
								if(SES_ID == asgnMem.User.id) {
									if(asgnMem.User.id == Assign_to_user) { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="<%= SES_ID %>" selected><?php echo __("me"); ?></option>
									<% } else if(checkAsgn == "self") { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="self" selected>self</option>
									<% } else if(checkAsgn == "NA") { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="NA" selected>NA</option>
									<% } else { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="<%= SES_ID %>"><?php echo __("me"); ?></option>
									<% }		
								}else if(asgnMem.User.id==Assign_to_user) { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="<%= asgnMem.User.id %>" selected><%= asgnMem.User.name %></option>
								<% } else { %>
									<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="<%= asgnMem.User.id %>" <% if(checkAsgn == "other" && csUsrAsgn == asgnMem.User.id) { %><% } %>><%= asgnMem.User.name %></option>
								<% 	}
							}
						} else { %>
						<option data-tsk-id="<%= csAtId %>" class="assign-to-fld-repl" value="<%= SES_ID %>" selected><?php echo __("me"); ?></option> 				
						<% } %>
						</select>
					</div>
				<div class="cb"></div>
				</div>
				<div class="h20"></div>
				<div class="col-lg-12 fl tskmore" id="tskmore_<%= csAtId %>" style="display:block">
					<div class="col-lg-12">
					<div class="col-lg-6 fl">
						<div class="fl lbl-font16"><?php echo __("Priority"); ?>:</div>
                            <div style="margin-top:5px">
                                <div class="fl prio_radio y_low" onclick="edited_priority(<%= '\''+csAtId+'\'' %>,this);"><input type="radio" name="task_priority" value="2" id="priority_low" class="" <% if(csPriRep==2){ %>checked="checked" <% } %> />
                                <label tabindex=4 class="pri-label"></label></div>
                        <div class="fl pri_type"><?php echo __("Low"); ?></div>
                                <div class="fl prio_radio g_mid" onclick="edited_priority(<%= '\''+csAtId+'\'' %>,this);"><input type="radio" name="task_priority" value="1" id="priority_mid" class=""  <% if(csPriRep==1){ %>checked="checked" <% } %>  />
                                <label tabindex=4 class="pri-label"></label></div>
                        <div class="fl pri_type"><?php echo __("Medium"); ?></div>
                                <div class="fl prio_radio h_red" onclick="edited_priority(<%= '\''+csAtId+'\'' %>,this);"><input type="radio" name="task_priority" value="0" id="priority_high" class="" <% if(csPriRep==0){ %>checked="checked" <% } %> />
                                <label tabindex=4 class="pri-label"></label></div>
                        <div class="fl pri_type"><?php echo __("High"); ?></div>
                            </div>
					</div>
                    <?php if(defined('TLG') && TLG != 1){ ?>
					<div class="col-lg-6 fl">
						<div class="fl lbl-font16"><?php echo __("Hour(s) Spent"); ?>:</div>
						<input type="text" class="form-control hrs_box" style="font-size: 13px;width:80px" rel="tooltip"  title="You can enter time as 1.5 (that  mean 1 hour and 30 minutes)." maxlength="6" name="data[Easycase][hours]" id="hours<%= csAtId %>" onkeypress="return numericDecimal(event)"/>
					</div>
					<div class="cb h20"></div>
                    <?php } ?>
					<% if(csLgndRep != 0 && TSG == 0){ %>
					<div class="col-lg-6 fl">
						<div class="fl lbl-font16" style="margin:0 16px 0 0"><?php echo __("Completed"); ?>:</div>
						<select class="form-control fl" style="width:80px;"  id="completed<%= csAtId %>" >
							<% if(csLgndRep == 1 || csLgndRep == 2 || csLgndRep == 4 || csLgndRep == 0){ %>
							<option value="0" <% if(completedtask == 0){ %> selected <% }else{ %>""<%} %>><?php echo __("0"); ?></option>
							<option value="10" <% if(completedtask == 10){ %> selected <% }else{ %>""<%} %>><?php echo __("10"); ?></option>
							<option value="20" <% if(completedtask == 20){ %> selected <% }else{ %>""<%} %>><?php echo __("20"); ?></option>
							<option value="30" <% if(completedtask == 30){ %> selected <% }else{ %>""<%} %>><?php echo __("30"); ?></option>
							<option value="40" <% if(completedtask == 40){ %> selected <% }else{ %>""<%} %>><?php echo __("40"); ?></option>
							<option value="50" <% if(completedtask == 50){ %> selected <% }else{ %>""<%} %>><?php echo __("50"); ?></option>
							<option value="60" <% if(completedtask == 60){ %> selected <% }else{ %>""<%} %>><?php echo __("60"); ?></option>
							<option value="70" <% if(completedtask == 70){ %> selected <% }else{ %>""<%} %>><?php echo __("70"); ?></option>
							<option value="80" <% if(completedtask == 80){ %> selected <% }else{ %>""<%} %>><?php echo __("80"); ?></option>
							<option value="90" <% if(completedtask == 90){ %> selected <% }else{ %>""<%} %>><?php echo __("90"); ?></option>
							<option value="100" <% if(completedtask ==100){ %> selected <% }else{ %>""<%} %>><?php echo __("100"); ?></option>  
							<% }else if(csLgndRep == 5 || csLgndRep == 3){ %>
							<option value="0" ><?php echo __("0"); ?></option>
							<option value="10"><?php echo __("10"); ?></option>
							<option value="20"><?php echo __("20"); ?></option>
							<option value="30"><?php echo __("30"); ?></option>
							<option value="40"><?php echo __("40"); ?></option>
							<option value="50"><?php echo __("50"); ?></option>
							<option value="60"><?php echo __("60"); ?></option>
							<option value="70"><?php echo __("70"); ?></option>
							<option value="80"><?php echo __("80"); ?></option>
							<option value="90"><?php echo __("90"); ?></option>
							<option value="100" selected><?php echo __("100"); ?></option>
							<% } %>
						</select><div class="fl pad-6">%</div>
					</div>
					<% } %>
					
					<div class="cb"></div>
				</div>
					<input type="hidden" name="totfiles" id="totfiles<%= csAtId %>" value="0" readonly="true">
					<form class="upload<%= csAtId %> attch_form" id="file_upload<%= csAtId %>" action="<?php echo HTTP_ROOT; ?>easycases/fileupload/" method="POST" enctype="multipart/form-data">
						<div class="fl" style="margin-top:10px;">
							<!--<span class="customfile-button" aria-hidden="true">Browse</span>-->
							<div class="fl lbl-font16 attch_ipad" style="margin:0 16px 10px 0"><?php echo __("Attachment(s)"); ?>:</div>
							<div id="holder_detl" style="" class="fl">
							    <div class="customfile-button fl" style="right:0">
								    <input class="customfile-input fl" name="data[Easycase][case_files]" id="tsk_attach<%= csAtId %>" type="file" multiple=""  style="width:230px;height:66px;"/>
								    <div class="att_fl fl" style="margin-right:5px"></div><div class="fr"><?php echo __("Select multiple files to upload..."); ?></div>
							    </div>
							    <div style="margin-left:4px;color:#F48B02;font-size:13px;" class="fnt999"><?php echo __("Drag and Drop files to Upload"); ?></div>
							    <div class="fnt999"><?php echo __("Max size"); ?> <%= MAX_FILE_SIZE %> Mb</div>
							</div>
						</div>
                        <?php 
						if($user_subscription['btprofile_id'] || $user_subscription['is_free'] || $GLOBALS['FREE_SUBSCRIPTION'] == 0) {
							$is_basic_or_free = 0;
						} else {
							$is_basic_or_free = 1;
						}
						if($user_subscription['is_cancel']) {
							$is_basic_or_free = 0;
						}
						?>
                        <?php if(USE_DROPBOX == 1 || USE_GOOGLE == 1){?>
						<div class="fr" style="width:248px;margin-top:50px">
                        	<?php if(USE_DROPBOX == 1) { ?>
							<div class="fr btn-al-mr">
								<button type="button" class="customfile-button" onclick="connectDropbox(<%= csAtId %>,<?php echo $is_basic_or_free;?>);">
									<span class="icon-drop-box"></span>
									Dropbox
								</button>
							</div>
                            <?php } ?>
                            <?php if(USE_GOOGLE == 1) { ?>
							<div class="btn-al-mr">
								<button type="button" class="customfile-button" onclick="googleConnect(<%= csAtId %>,<?php echo $is_basic_or_free;?>);">
									<span class="icon-google-drive"></span>
									Google Drive
								</button>
								<span id="gloader" style="display: none;">
									<img src="<?php echo HTTP_IMAGES;?>images/del.gif" style="position: absolute;bottom: 95px;margin-left: 125px;"/>
								</span>
							</div>
                            <?php } ?>
						</div>
                        <?php } ?>
						<div class="cb"></div>
					</form>
					<div id="table1">
					<table class="up_files<%= csAtId %>" id="up_files<%= csAtId %>" style="font-weight:normal;margin-left:146px">
                                        </table>
                                        </div>
					<div id="drive_tr_<%= csAtId %>" style="margin-left: 146px;margin-bottom:15px;">
						<form id="cloud_storage_form_<%= csAtId %>" name="cloud_storage_form_<%= csAtId %>"  action="javascript:void(0)" method="POST">
							<div style="float: left;margin-top: 7px;" id="cloud_storage_files_<%= csAtId %>"></div>
						</form>
						<div style="clear: both;margin-bottom: 3px;"></div>
					</div>
				</div>
					<div>
						<span class="lbl-font16"><?php echo __("Notify via Email"); ?>:</span> <input type="checkbox" name="chkAllRep" style="margin-left:10px;" id="<%= csAtId %>chkAllRep" value="all" class="clsptr" onclick="checkedAllResReply('<%= csAtId %>')" <% if(allMems.length == usrArr.length) { %> checked="checked" <% } %> /> <?php echo __("All"); ?>
					</div>
					<% 	var i = 0;
					if(countJS(allMems)){ %>
						<div id="mem<%= csAtId %>">
							<div  id="viewmemdtls<%= csAtId %>" class="tbl_check_name fl">
							<table cellpadding="1" cellspacing="1" border="0" width="100%">
							<% for(var memkey in allMems){
								var getAllMems = allMems[memkey];
                                if((getAllMems.User.is_client != 1 || (client_status != 1))){ 
								var j = i%3;
								if(j == 0)	{ %>
								<tr>
								<% } %>
									<td align="left" valign="top"  style="font-weight:normal;color:#4B4B4B;"> 
									<input data-tsk-id="<%= csAtId %>" type="checkbox" name="data[Easycase][user_emails][]" id="<%= csAtId %>chk_<%= getAllMems.User.id %>" value="<%= getAllMems.User.id %>" style="cursor:pointer;" class="chk_fl <% if(getAllMems.User.is_client==1){%> chk_client_reply <% }%>" onClick="removeAllReply('<%= csAtId %>')" <% if($.inArray(getAllMems.User.id,usrArr)!=-1){ %> checked <% } %> />
									<span class="det_nm_wd" title="<%= shortLength(getAllMems.User.name,18) %>"><%= shortLength(getAllMems.User.name,18) %></span>
									<input type="hidden" name="data[Easycase][proj_users][]" id="proj_users"  value="<%= getAllMems.User.id %>" readonly="true" />
									</td>
								<% i = i+1; var k = i%3;
								if(k == 0){ %>
								</tr>
							<% 	}
                                                        }
							} %>
								<tr>
									<input type="hidden" name="hidtotresreply" id="hidtotresreply<%= csAtId %>" value="<%= i %>" readonly="true" />
									<td colspan="3"></td>
								</tr>
							</table>
							</div>
						</div>
					<% } %>
				<div class="cb"></div>
                <?php if(defined('CR') && CR == 1){ ?>
				 <div class="padlft-non padrht-non">
                    <div class="fl no-cl" id="clientdiv">
                        <span><input type="checkbox" name="chk_all" id="make_client_dtl" value="0" onclick="chk_client_reply();" /></span> 
                        <span class="tfont ml"><?php echo __("Do not show this reply to the client"); ?></span>
                    </div>
                 </div>
                <?php } ?>
				
				<div class="cb"></div>
				<div class="col-lg-12 m-top-20">
					<!--<div class="fl lbl-font16 lbl_cs_det"></div>-->
					<span id="postcomments<%= csAtId %>">
						<button class="btn btn_blue" type="button" name="data[Easycase][postdata]" onclick="return validateComments(<%= '\''+csAtId+'\',\''+csUniqId+'\',\''+csLgndRep+'\',\''+SES_TYPE+'\',\''+csProjIdRep+'\'' %>);"><i class="icon-big-tick"></i><?php echo __("Post"); ?></button>
                        <span class="or_cancel"><?php echo __("or"); ?></span>
						<button class="task_detail_back or_cancel" type="reset" id="rset"><i class="icon-big-cross"></i><?php echo __("Cancel"); ?></button>
					</span>
					<span id="loadcomments<%= csAtId %>" style="display:none;">
						<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..." style="padding:5px;"/>
					</span>
					<input type="hidden" value="<%= total %>" id="hidtotrp<%= csAtId %>" />
					


				</div>			
			</div>
			<div class="cb"></div>
		</div>
        <?php } ?>
		<% } %>
	</div><!-- /.page-wrapper -->
	</div>
	<div class="col-lg-3 fl col_task case_det_rt">
		<a href="javascript:void(0);" onclick="reloadTaskDetail(<%= '\''+ csUniqId+'\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Reload">
				<i class="icon-reload"></i>
			</div>
		</a>
        <?php if(defined('TLG') && TLG == 1){ ?>
            <% if(is_active){ %>
            <a href="javascript:void(0);"  onclick="createlog(<%= '\'' + csAtId + '\'' %>,<%= '\'' + escape(caseTitle) + '\'' %>,<%= '\'\'' %>,<%= '\'\'' %>,this);" data-puid = "<%= projUniqId %>">
                <div class="btn gry_btn smal30 mb3" rel="tooltip" title="<?php echo __("Log Time"); ?>" style="padding-right:20px;">
                    <i class="act_icon act_log_task fl"></i>
                </div>
            </a>
            <% } %>
        <?php } ?>
		<% if(!is_active){ %>
		<a href="javascript:void(0);" onclick="restoreTaskDetail(<%= '\''+ csUniqId+'\',\''+csNoRep+'\'' %>);">
		    <div class="btn gry_btn smal30 mb3" rel="tooltip" title="<?php echo __("Restore"); ?>" style="padding-right:20px;">
				<i class="icon-restore"></i>
			</div>
		</a>
		<% } %>
        <%  if((showQuickActDD && (SES_TYPE == 1 || SES_TYPE == 2 || (csUsrDtls== SES_ID)))){ %>
		<a href="javascript:void(0);" onclick="editask(<%= '\''+ csUniqId+'\',\''+projUniqId+'\',\''+projName+'\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Edit">
				<i class="icon-edit"></i>
			</div>
		</a>
		<% } %>
		<?php /*?>
		<% if(csLgndRep == 1 && csTypRep!= 10) { %>
		<a href="javascript:void(0);" onclick="startCase(<%= '\'' + csAtId + '\'' %>, <%= '\'' + csNoRep + '\'' %>, <%= '\'' + csUniqId + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Start">
				<i class="act_icon act_start_task fl"></i>
			</div>
		</a>
		<% } %><?php */?>
		<% if(is_active && (SES_TYPE == 1 || SES_TYPE == 2 || ((csLgndRep == 1 || csLgndRep == 2 || csLgndRep == 4) && ( SES_ID == csUsrDtls)))) { %>
		<a href="javascript:void(0);" onclick="archiveCase(<%= '\'' + csAtId + '\'' %>, <%= '\'' + csNoRep + '\'' %>, <%= '\'' + csProjIdRep + '\'' %>, <%= '\'t_' + csUniqId + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Archive">
				<i class="icon-arch"></i>
			</div>
		</a>
		<% } if(SES_TYPE == 1 || SES_TYPE == 2 || (csLgndRep == 1  && SES_ID == csUsrDtls)) { %>
		<a href="javascript:void(0);" onclick="deleteCase(<%= '\'' + csAtId + '\'' %>, <%= '\'' + csNoRep + '\'' %>, <%= '\'' + csProjIdRep + '\'' %>, <%= '\'t_' + csUniqId + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Delete">
				<i class="icon-delet"></i>
			</div>
		</a>
		<% } if(is_active && ((is_active && csLgndRep == 1 || csLgndRep == 2 || csLgndRep == 4) && csTypRep!= 10)) { %>
		<a href="javascript:void(0);" onclick="caseResolve(<%= '\'' + csAtId + '\'' %>, <%= '\'' + csNoRep + '\'' %>, <%= '\'' + csUniqId + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Resolve">
				<i class="icon-closs"></i>
			</div>
		</a>
		<% } if(is_active && ((csLgndRep == 1 || csLgndRep == 2 || csLgndRep == 4 || csLgndRep == 5) && csTypRep != 10)) { %>
		<a href="javascript:void(0);" onclick="setCloseCase(<%= '\'' + csAtId + '\'' %>, <%= '\'' + csNoRep + '\'' %>, <%= '\'' + csUniqId + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Close">
				<i class="icon-resol"></i>
			</div>
		</a>
		<% } %>
		<a href="javascript:void(0);" onclick="downloadTask(<%= '\''+ csUniqId+'\'' %>,<%= '\'' + csNoRep + '\'' %>);">
			<div class="btn gry_btn smal30 mb3" rel="tooltip" title="Download">
				<i class="icon-taskdownl"></i>
			</div>
		</a>
		<div class="cb"></div>
		<hr/>
		<div>
			<div class="asign_block">
				<div class="fl icon-asign-to"></div>
				<div id="case_dtls_asgn<%= csAtId %>" class="fl asgn_actions <% if(showQuickAct==1){ %> dropdown<% } %>">
					<span <% if(showQuickAct==1){ %> class="quick_action" data-toggle="dropdown"<% } %> onclick="displayAssignToMem(<%= '\'' + csAtId + '\'' %>, <%= '\'' + projUniqId + '\'' %>,<%= '\'' + asgnUid + '\'' %>,<%= '\'' + csUniqId + '\'' %>,<%= '\'details\'' %>,<%= '\'' + csNoRep + '\'' %>)"><?php echo __("Assigned To"); ?></span>
					<% if(showQuickAct==1){ %>
					<ul class="dropdown-menu quick_menu" id="detShowAsgnToMem<%= csAtId %>">
						<li class="text-centre"><img src="<?php echo HTTP_IMAGES; ?>images/del.gif" id="detAssgnload<%= csAtId %>" /></li>
					</ul>
					<% } %>
				</div>
				<div class="fl" id="detasgnlod<%= csAtId %>" style="display:none">
					<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." title="Loading..."/>
				</div>
			</div>
			<div class="cb"></div>
			<div class="fl">
				<% if(asgnPic && asgnPic!=0) { %>
				<img src="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<%= asgnPic %>&sizex=60&sizey=60&quality=100" class="round_profile_img asignto" title="<%= asgnTo %>" width="60" height="60" />
				<% } else { %>
                                <span class="round_profile_img <%= asgncolr %> act_prof_styl"><%= asgn_shtnm %></span>
			<?php /*	<img src="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=user.png&sizex=60&sizey=60&quality=100" class="round_profile_img asignto" title="<%= asgnTo %>" width="60" height="60" /> */ ?>
				<% } %>
			</div>
			<div class="fl">
				<span><b id="case_dtls_new<%= csAtId %>" class="ttc"><%= shortLength(asgnTo,15) %></b></span>
				<div class="fnt999"><%= asgnEmail %></div>
			</div>
		</div>
		<div class="cb"></div>
		<hr/>
		<% if(csStartDtFmtT != 'NoSt'){ %>
		<div class="task_due_dt">
			<div class="cb"></div>
			<div class="fl icon-due-date"></div>
			<div id="case_dtls_start<%= csAtId %>" class="fl due_actions <% if(showQuickActDD==1){ %> dropdown<% } %>">
					<% if(csStartDtFmt) { %>
					<div title="<%= csStartDtFmtT %>" rel="tooltip" <% if(showQuickActDD==1){ %> class="fl duequick_action stdudtfont" data-toggle="dropdown"<% } else { %> class="fl stdudtfont" <% } %>><%= csStartDtFmt %></div>
					<input type="hidden" value="<%= csStartDtFmtT %>" id="start_date_chng_<%= csAtId %>" />
					<% } else { %>
					<div <% if(showQuickActDD==1){ %> class="no_due_dt duequick_action" data-toggle="dropdown"<% } else { %> class="fl no_due_dt" <% } %>><span class="due-txt ellipsis-view maxWidth190"><?php echo __('No Start Date');?></span></div>
					<input type="hidden" value="NoStartDate" id="start_date_chng_<%= csAtId %>" />
					<% } %>
					<% if(showQuickActDD==1){ %>
					<ul class="dropdown-menu quick_menu">
							<li class="pop_arrow_new"></li>
							<li><a href="javascript:void(0);" onclick="detChangeStartDate(<%= '\'' + csAtId + '\', \'00/00/0000\', \'No Start Date\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('No Start Date');?></a></li>
							<li><a href="javascript:void(0);" onclick="detChangeStartDate(<%= '\'' + csAtId + '\', \'' + mdyCurCrtd + '\', \'Today\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Today');?></a></li>
							<li><a href="javascript:void(0);" onclick="detChangeStartDate(<%= '\'' + csAtId + '\', \'' + mdyTomorrow + '\', \'Tomorrow\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Tomorrow');?></a></li>
							<li><a href="javascript:void(0);" onclick="detChangeStartDate(<%= '\'' + csAtId + '\', \'' + mdyMonday + '\', \'Next Monday\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Next Monday');?></a></li>
							<li><a href="javascript:void(0);" onclick="detChangeStartDate(<%= '\'' + csAtId + '\', \'' + mdyFriday + '\', \'This Friday\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('This Friday');?></a></li>
							<li>
								<a href="javascript:void(0);" class="cstm-dt-option" data-csatid="<%= csAtId %>">
									<input value="" type="hidden" id="det_set_start_date_<%= csAtId %>" class="set_due_date" title="<?php echo __('Custom Date');?>" style=""/>
									<span style="position:relative;top:2px;cursor:text;"><?php echo __('Custom Date');?></span>
								</a>
							</li>
					</ul>
					<% } %>
			</div>
			<span id="detSdlod<%= csAtId %>" style="display:none">
					<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __('Loading');?>..." title="<?php echo __('Loading');?>..."/>
			</span>
		</div>
		<div class="cb"></div>
		<hr/>
		<% } %>
		<div class="task_due_dt">
			<div class="cb"></div>
			<div class="fl icon-due-date"></div>
			<div id="case_dtls_due<%= csAtId %>" class="fl due_actions <% if(showQuickActDD==1){ %> dropdown<% } %>">
				<% if(csDuDtFmt) { %>
				<div title="<%= csDuDtFmtT %>" rel="tooltip" <% if(showQuickActDD==1){ %> class="fl duequick_action stdudtfont" data-toggle="dropdown"<% } else { %> class="fl stdudtfont" <% } %>><%= csDuDtFmt %></div>
				<input type="hidden" value="<%= csDuDtFmtT %>" id="due_date_chng_<%= csAtId %>" />
				<% } else { %>
				<div <% if(showQuickActDD==1){ %> class="no_due_dt duequick_action" data-toggle="dropdown"<% } else { %> class="fl no_due_dt" <% } %>><span class="due-txt ellipsis-view maxWidth190"><?php echo __('No Due Date');?></span></div>
				<input type="hidden" value="NoDueDate" id="due_date_chng_<%= csAtId %>" />
				<% } %>
				<% if(showQuickActDD==1){ %>
				<ul class="dropdown-menu quick_menu">
					<li class="pop_arrow_new"></li>
					<li><a href="javascript:void(0);" onclick="detChangeDueDate(<%= '\'' + csAtId + '\', \'00/00/0000\', \'No Due Date\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('No Due Date');?></a></li>
					<li><a href="javascript:void(0);" onclick="detChangeDueDate(<%= '\'' + csAtId + '\', \'' + mdyCurCrtd + '\', \'Today\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Today');?></a></li>
					<li><a href="javascript:void(0);" onclick="detChangeDueDate(<%= '\'' + csAtId + '\', \'' + mdyTomorrow + '\', \'Tomorrow\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Tomorrow');?></a></li>
					<li><a href="javascript:void(0);" onclick="detChangeDueDate(<%= '\'' + csAtId + '\', \'' + mdyMonday + '\', \'Next Monday\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('Next Monday');?></a></li>
					<li><a href="javascript:void(0);" onclick="detChangeDueDate(<%= '\'' + csAtId + '\', \'' + mdyFriday + '\', \'This Friday\', \'' + csUniqId + '\', \'' + csNoRep + '\'' %>)"><?php echo __('This Friday');?></a></li>
					<li>
						<a href="javascript:void(0);" class="cstm-dt-option" data-csatid="<%= csAtId %>">
							<input value="" type="hidden" id="det_set_due_date_<%= csAtId %>" class="set_due_date" title="<?php echo __('Custom Date');?>" style=""/>
							<span style="position:relative;top:2px;cursor:text;"><?php echo __('Custom Date');?></span>
						</a>
					</li>
				</ul>
				<% } %>
			</div>
			<span id="detddlod<%= csAtId %>" style="display:none">
				<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="<?php echo __('Loading');?>..." title="<?php echo __('Loading');?>..."/>
			</span>
		</div>
		<div class="cb"></div>
		<hr/>
		<div class="task_due_dt">
                <div class="cb"></div>
                <div class="fl icon-set-attach"></div>
		<% var fc = 0;
		if(all_files.length) { %>
			<div class="fl">
				<span><?php echo __('Files in this Task');?></span>
			</div>
		<% var imgaes = ""; var caseFileName = "";
		for(var fkey in all_files){
			var getFiles = all_files[fkey];
			caseFileName = getFiles.CaseFile.file;
			downloadurl = getFiles.CaseFile.downloadurl;
            var d__fil_name = getFiles.CaseFile.display_name;
			if(!d__fil_name){
				d__fil_name = caseFileName;
			}
			if(getFiles.CaseFile.is_exist) {
				fc++;
				if(fc==6) {
			%>
			<div class="tsk_files_more">
			<% } %>
			<div class="cb"></div>
			<div class="fl upload_icn"><div class="tsk_fl <%= easycase.imageTypeIcon(getFiles.CaseFile.format_file) %>_file"></div></div>
			<div class="fl">
				<% if(getFiles.CaseFile.is_ImgFileExt){ %>
				<% if(downloadurl){ %>
				    <span class="gallery"><a href="<%= downloadurl %>" target="_blank" alt="<%= d__fil_name %>" title="<%= d__fil_name %>" rel="prettyImg[]"><%= shortLength(d__fil_name,25) %></a></span>
				<% } else { %>
				    <span class="gallery"><a href="<?php echo HTTP_ROOT; ?>easycases/download/<%= caseFileName %>" alt="<%= d__fil_name %>" title="<%= d__fil_name %>" rel="prettyImg[]"><%= shortLength(d__fil_name,25) %></a></span>
				<% 	} 
				} else{
					if(downloadurl) { %>
						<a href="<%= downloadurl %>" target="_blank" alt="<%= d__fil_name %>" title="<%= d__fil_name %>"><%= shortLength(d__fil_name,25) %></a>
					<% } else { %>
						<a href="<?php echo HTTP_ROOT; ?>easycases/download/<%= caseFileName %>" alt="<%= d__fil_name %>" title="<%= d__fil_name %>"><%= shortLength(d__fil_name,25) %></a>
					<% }
				} %>
				<div class="fnt999" style="line-height: 3px;"><%= getFiles.CaseFile.file_date %></div>
			</div>
			<% } %>
			<% }
			if(fc>5) { %>
			</div>
			<div class="cb"></div>
			<div class="fr ftsk_more">
				<a class="more_in_menu" href="javascript:;"><?php echo __('More');?></a><b class="menu_more_arr file_more"></b>
			</div>
		<% }
		} if(fc==0) { %>
		<div class="fl">
			<span class="no_due_dt ellipsis-view maxWidth190"><?php echo __('No Files in this Task');?></span>
		</div>
		<% } %>
		</div>
		<div class="cb"></div>	
		<hr/>
		<div class="task_due_dt">
			<div class="cb"></div>
			<div class="fl icon-activity"></div>
			<div class="fl">
				<span><?php echo __("Activities"); ?></span><br/>
			</div>
			<div class="cb"></div>
			<div class="activ_listing">
				<div><span><?php echo __("Created"); ?>:</span> <%= frmtCrtdDt %></div>
				<div><span><?php echo __("Last Updated"); ?>:</span> <%= lupdtm %></div>
				<% if(lstRes) { %>
				<div class="col_r"><span><?php echo __("Resolved"); ?>:</span> <%= lstRes %></div>
				<% }
				if(lstCls) { %>
				<div class="col_g"><span><?php echo __("Closed"); ?>:</span> <%= lstCls %></div>
				<% } %>
			</div>
		</div>
		<div class="cb"></div>	
		<hr/>
		<div class="task_due_dt">
			<div class="cb"></div>
			<div class="fl icon-p-invol"></div>
			<div class="fl">
				<span><?php echo __("People Involved"); ?></span><br/>
			</div>
		</div>
		<div class="cb"></div>
		<% for(i in taskUsrs) { %>
		<div class="fl">
			<% var upic = 'user.png';
			if(taskUsrs[i].User.photo && taskUsrs[i].User.photo!=0) {
				var upic = taskUsrs[i].User.photo; %>
			<img src="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<%= upic %>&sizex=40&sizey=40&quality=100" class="round_profile_img ppl_invol" title="<%= ucwords(formatText(taskUsrs[i].User.name+' '+taskUsrs[i].User.last_name)) %>" width="40" height="40" rel="tooltip" />
                    <%	} 
                        else { %>
                        <span class="round_profile_img <%= taskUsrs[i].User.color %> act_prof_styl" title="<%= ucwords(formatText(taskUsrs[i].User.name+' '+taskUsrs[i].User.last_name)) %>"><%= taskUsrs[i].User.sht_name %></span>
                        <% } %>
			
                        
		</div>
		<% } %>
	</div>
	<div class="cb"></div>	
</div>
<style>
.act_log_task{background: url("<?php echo HTTP_ROOT; ?>/img/Time_log_icon.png") no-repeat;width:20px;height:19px;}
.mb3{margin-bottom:3px;}
.task_details_row .detail-status-bar ul.status-bar-ul li::after {
    right: -11px;
    top: -128%;
    content: "â–º";
    font-size: 13px;
    color: <%= csLgndcolor %>;
    position: absolute;
    border: 0px;
    height: inherit;
    width: inherit;
    margin-top: inherit;
    display:none
}
.arrow-frwd{border-bottom: 6px solid transparent;
    border-left: 10px solid #8dc2f8;
    border-top: 5px solid transparent;
    display: inline-block;
    height: 0;
    position: absolute;
    right: -10px;
    top: 0px;
    vertical-align: middle;
    width: 0;}
.detail-status-bar{height:auto;margin:10px auto}
.detail-status-bar .status-title{top:10px}
.detail-status-bar ul.status-bar-ul{padding:5px;height:auto;}
.quick_menu li.pop_arrow_new{margin-left:10px}
.detail-status-bar ul.status-bar-ul li {list-style-type: none;cursor: pointer;display: inline-block;padding: 0px;height: 11px;margin-top: 0;position: relative;vertical-align: middle;}
.open > .dropdown-menu.lgnd_menu {display: block;max-height: 300px;overflow-y: auto;}

</style>
