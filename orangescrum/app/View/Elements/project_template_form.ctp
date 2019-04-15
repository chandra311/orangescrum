<%
var template_name = (typeof(ProjectTemplate) == "undefined") ? "" : ProjectTemplate.module_name;
var template_id = (typeof(ProjectTemplate) == "undefined") ? "" : ProjectTemplate.id;
%>
<table cellpadding="0" cellspacing="0" class="col-lg-12 new_proj_temp_new_tab">
<tbody id="parent-mlsts-tbody">
		<tr>
		<td><?php echo __("Title"); ?>:</td>
		<td>
			<input type="text" name="data[ProjectTemplate][module_name]" id="module_name" class="form-control" value="<%= template_name %>" />
			<input type="hidden" name="data[ProjectTemplate][id]" id="templatre_id" class="form-control" value= "<%= template_id %>" />
		</td>
		</tr>
		<tr class="fixedTR">
		<td></td>
		<td style="text-align:left;">
			<span style="position:relative;top:0px;">
				<a href="javascript:jsVoid();" style="color:#06C;text-decoration:underline;font-size:12px;padding-left:5px;" class="add-more-ptmilestone">+ Add Milestone</a>
			</span>
			<span class="or-span">&nbsp;&nbsp; OR &nbsp;</span>
			<span style="position:relative;top:0px;">
				<a href="javascript:jsVoid();" style="color:#06C;text-decoration:underline;font-size:12px;padding-left:5px;" class="add-more-pttask">+ Add Tasks To Default Milestone</a>
			</span>
		</td>
		</tr>
		<% if(typeof(ProjectTemplate) != "undefined" && !po.empty(ProjectTemplateMilestone)){
			var mlstn_cntr = 0;
			var mlstn_case_cntr = 0;
			for(var mlstKey in ProjectTemplateMilestone){
				var mlstn_cases = ProjectTemplateMilestone[mlstKey]['ProjectTemplateCase'];
				var mlst_id = ProjectTemplateMilestone[mlstKey]['id'];
				var mlst_startdate = moment(ProjectTemplateMilestone[mlstKey]['start_date'], 'YYYY-MM-DD').isValid() ? moment(ProjectTemplateMilestone[mlstKey]['start_date'],'YYYY-MM-DD').format('MMM DD,YYYY') : "";
				var mlst_endate = moment(ProjectTemplateMilestone[mlstKey]['end_date'], 'YYYY-MM-DD').isValid() ? moment(ProjectTemplateMilestone[mlstKey]['end_date'],'YYYY-MM-DD').format('MMM DD,YYYY') : "";
				var mlst_desc = ProjectTemplateMilestone[mlstKey]['description'];
				var is_default = parseInt(ProjectTemplateMilestone[mlstKey]['is_default']);
				var mlst_title = (is_default) ? "Default Milestone" : ProjectTemplateMilestone[mlstKey]['title'];
				%>


				<tr id="pr_tm_ml_form_<%= mlstn_cntr %>" class="mlstn-rows" style="display: none;">
			    <td colspan="2">
			        <center>
			            <div id="proj_temp_new_mlstn_err_<%= mlstn_cntr %>" class="fnt_clr_rd" style="display:block;font-size:15px;"></div>
			        </center>
			        <table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_tsksss">
					<tbody>
								<tr>
						    <td><span class="fnt_clr_rd">* </span>Title:</td>
						    <td>
					 			<input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][title]" type="text" class="form-control" value="<%= mlst_title %>">
					            <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][id]" type="hidden" class="form-control" value="<%= mlst_id %>">
					            <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][is_default]" type="hidden" class="form-control" value="<%= is_default %>">
						    </td>
					</tr>
					<tr>
					    <td style="vertical-align:top">Description:</td>
					    <td>
					    	<textarea name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][description]" class="form-control"><%= mlst_desc %></textarea>
					    </td>
					</tr>
					<tr>
					    <td colspan="2">
					    	<div style="width:28%;float:left">
					    		<?php echo __("Start Date"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
				    		</div>
					    	<div style="width:29%;float:left">
					    		<input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][start_date]" id="pr_tmpl_mls_start_date_<%= mlstn_cntr %>" type="text" data-fldname="start_date" class="datepicker form-control" value="<%= mlst_startdate %>" readonly>
					    	</div>
					    	<div style="width:13%;float:left">
					    		<?php echo __("End Date"); ?>:&nbsp;&nbsp;
					    	</div>
					    	<div style="width:30%;float:left">
					    		<input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][end_date]" id="pr_tmpl_mls_end_date_<%= mlstn_cntr %>" data-fldname="end_date" type="text " class="datepicker form-control" value="<%= mlst_endate %>" readonly>
					    	</div>
					    	<div style="clear:left"></div>
				    	</td>
					</tr>
					<tr>
						<td></td>
						<td class="btn_align">
							<span class="ldr-ad-btn">
								<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="loading... " title="loading... "> 
							</span>
							<span>
								<button class="btn btn_blue" type="button" data-state="exist" data-mlstnno="<%= mlstn_cntr %>" onclick="po.validateMstn(this,<%= mlstn_cntr %>,<%= mlst_id %>)"><i class="icon-big-tick"></i>Update Milestone</button>
					            <span class="or_cancel">or<a onclick="po._cancelmlstn(this);" data-mlstnno="<%= mlstn_cntr %>">Cancel</a></span>
							</span>
						</td>
					</tr>
				</tbody>
				</table>
				</td>
				</tr>


				<tr id="pr_tm_ml_det_<%= mlstn_cntr %>" class="mlstn-rows">
				    <td></td>
				    <td style="text-align:left;">
				        <div style="position:relative;" class="mlstn-div">
				        	<span class="clpse-icon opened"><span class="glyphicon glyphicon-minus-sign"></span></span>
				        		<%= mlst_title %>
			        		<% if(!is_default){ %>
				        	<span class="ct_icon act_edit_task pr-temp-mlstn-edit-icon edit-span dn" onclick="po._editmlstn(<%= mlstn_cntr %>);"></span>
				        	<span class="act_icon act_del_task pr-temp-mlstn-delete-icon dlt-span dn" data-mlstnId="<%= mlst_id %>" onclick="po._dltmlstnexis(this,<%= mlstn_cntr %>);"></span>
				        	<a href="#" class="mlstn-task-add pr-temp-mlstn-add-link" data-mlstnno="<%= mlstn_cntr %>">+ Add Task</a>
				        	<% } %>
				        </div>
				        <div>
				           	
				           	<table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_mlstn_tsks" style="" id="tmpl_mlstns_task_form_<%= mlstn_cntr %>">
						    <tbody id="tmpl_mlstns_task_form_tbody_<%= mlstn_cntr %>">
						    <% if(!po.empty(mlstn_cases)){
							for(var mlstcasesKey in mlstn_cases){
								var mlst_tsk_id = mlstn_cases[mlstcasesKey]['id'];
								var mlst_tsk_title = mlstn_cases[mlstcasesKey]['title'];
								var mlst_tsk_startdate = moment(mlstn_cases[mlstcasesKey]['start_date'], 'YYYY-MM-DD').isValid() ? moment(mlstn_cases[mlstcasesKey]['start_date'],'YYYY-MM-DD').format('MMM DD,YYYY') : "";
								var mlst_tsk_endate = moment(mlstn_cases[mlstcasesKey]['end_date'], 'YYYY-MM-DD').isValid() ? moment(mlstn_cases[mlstcasesKey]['end_date'],'YYYY-MM-DD').format('MMM DD,YYYY') : "";
								var mlst_tsk_desc = mlstn_cases[mlstcasesKey]['description'];
								var mlst_tsk_estimated_hours = mlstn_cases[mlstcasesKey]['estimated_hours'];
								var mlst_tsk_assign_to = mlstn_cases[mlstcasesKey]['assign_to'];
								var mlst_tsk_depends = mlstn_cases[mlstcasesKey]['depends'];
								var is_dummy = (typeof(mlstn_cases[mlstcasesKey]['is_dummy']) == "undefined") ? false : mlstn_cases[mlstcasesKey]['is_dummy'];
								%>
						        <tr id="pr_tm_ml_tsk_det_<%= mlstn_case_cntr %>" >
						            <td></td>
						            <td style="text-align:left;border-bottom:1px solid #DCDADB;">
						                <div style="position:relative;" class="edt-task-div"><%= mlst_tsk_title %>
						                <span class="ct_icon act_edit_task pr-temp-mlstn-edit-icon edit-span dn" onclick="po._editmlstntsk(<%= mlstn_case_cntr %>,<%= mlstn_cntr %>);"></span>
						                <% if(is_dummy){ %>
						                	<span class="act_icon act_del_task pr-temp-mlstn-delete-icon dlt-span dn" onclick="po._dltmlstntsk(<%= mlstn_case_cntr %>,<%= mlstn_cntr %>);"></span>
						                <% }else{ %>
						                <span class="act_icon act_del_task pr-temp-mlstn-delete-icon dlt-span dn" data-tskId="<%= mlst_tsk_id %>" onclick="po._dltmlstntskexis(this,<%= mlstn_case_cntr %>,<%= mlstn_cntr %>);"></span>
						                <% } %>
						                </div>
						            </td>
						        </tr>
						        <tr id="pr_tm_ml_tsk_form_<%= mlstn_case_cntr %>" class="mlstn-task-rows puid<%= mlst_tsk_id %> parentTR" data-tskId="<%= mlst_tsk_id %>" style="display: none;">
						            <td colspan="2">
						                <center>
						                    <div id="proj_temp_new_mlstn_task_err_<%= mlstn_case_cntr %>" class="fnt_clr_rd" style="display:block;font-size:15px;"></div>
						                </center>
						                <table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_tsks_forms">
						                    <tbody>
						                        <tr>
						                            <td><span class="fnt_clr_rd">* </span>Title:</td>
						                            <td>
						                                <input type="text" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][title]" data-fldname="title" class="form-control" value="<%= mlst_tsk_title %>">
						                                <input type="hidden" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][id]" data-fldname="id" class="form-control mlst_uniq_Id" value="<%= mlst_tsk_id %>">
						                            </td>
						                        </tr>
						                        <tr>
						                            <td style="vertical-align:top">Description:</td>
						                            <td>
						                                <textarea name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][description]" data-fldname="description" class="form-control"><%= mlst_tsk_desc %></textarea>
						                            </td>
						                        </tr>
						                        <tr>
												    <td colspan="2">
												    	<div style="width:22%;float:left">
												    		<?php echo __("Start Date"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
											    		</div>
												    	<div style="width:28%;float:left">
												    		<input id="pr_tmpl_mls_task_start_date_<%= mlstn_case_cntr %>" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][start_date]" data-fldname="start_date" type="text" class="datepicker form-control" value="<%= mlst_tsk_startdate %>" readonly>
												    	</div>
												    	<div style="width:20%;float:left">
												    		<?php echo __("End Date"); ?>:
												    	</div>
												    	<div style="width:30%;float:left">
												    		<input id="pr_tmpl_mls_task_end_date_<%= mlstn_case_cntr %>" data-fldname="end_date" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][end_date]" type="text" class="datepicker form-control" value="<%= mlst_tsk_endate %>" readonly>
												    	</div>
												    	<div style="clear:left"></div>
											    	</td>
												</tr>
						                        <tr>
												    <td colspan="2">
												    	<div style="width:22%;float:left;">
												    		<?php echo __("Estd. Hour(s)"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
											    		</div>
												    	<div style="width:28%;float:left">
												    		<input id="pr_tmpl_mls_task_estimated_hours_<%= mlstn_case_cntr %>" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][estimated_hours]" data-fldname="estimated_hours" type="text" class="form-control" value="<%= mlst_tsk_estimated_hours %>" >
												    	</div>
												    	<div style="width:20%;float:left">
												    		<?php echo __("Assign To"); ?>:
												    	</div>
												    	<div style="width:30%;float:left">
                                                                    	<div class="dropdown option-toggle p-6" >
                                                                        <div class="opt51">
                                                                            <a href="javascript:jsVoid()" onclick="po.open_more_opt(this,<%= '\'more_opt\'' %> );" >                               
                                                                                <span class="tsk_asgn_to_p">me</span>
                                                                                <i class="caret mtop-10 fr"></i>
                                                                            </a>
                                                                        </div>
                                                                        <div class="more_opt">
                                                                            <ul>                                
                                                                            </ul>
                                                                        </div>
												    		<input id="pr_tmpl_mls_task_assign_to_<%= mlstn_case_cntr %>" data-fldname="assign_to" name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][assign_to]" type="hidden" class="form-control assignto" value="<%= mlst_tsk_assign_to %>" >
                                                                    </div>
												    	</div>
												    	<div style="clear:left"></div>
											    	</td>
												</tr>
                                                <?php if(defined('GNC') && GNC == 1){ ?>
                                                  <tr class="depends_row">
                                                    <td colspan="2">
                                                        <div style="width:22%;float:left;">
                                                            <?php echo __("Depends On"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
                                                        </div>
                                                         <div style="width:30%;float:left">
                                                                <div class="dropdown option-toggle p-6" >
                                                                    <div class="opt52">
                                                                        <a href="javascript:jsVoid()" onclick="po.open_more_opt(this,<%= '\'more_opt_depends\'' %>);" >                               
                                                                            <span class="tsk_depends_p"></span>
                                                                            <i class="caret mtop-10 fr"></i>
                                                                        </a>
                                                                    </div>
                                                                    <div class="more_opt_depends">
                                                                        <ul>   
                                                                            <li><a href="javascript:jsVoid()" onclick="po._depends(-1,this,<%= '\'Select\'' %>);" value="-1"><span class="value">-1</span>Select</a></li>
                                                                        </ul>
                                                                    </div>
                                                            <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][depends]" id="pr_tmpl_mls_depends_<%= mlstn_case_cntr %>" data-fldname="depends" type="hidden" data_id="<%= mlst_tsk_depends %>" class="form-control depends dependsEdit" value="<%= mlst_tsk_depends %>" >
                                                            <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][seq_order]" id="pr_tmpl_mls_seq_order_<%= mlstn_case_cntr %>" data-fldname="seq_order" type="hidden" class="form-control seq_order" value="<%= mlstn_case_cntr %>" >
                                                           </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php }else{ ?>
                                                     <tr class="depends_row class graw_out">
                                                        <td colspan="2">
                                                            <div style="width:22%;float:left;">
                                                                <?php echo __("Depends On"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                             <div style="width:25%;float:left">
                                                                    <div class="dropdown option-toggle p-6" >
                                                                        <div class="opt52">
                                                                            <a href="javascript:jsVoid()">                               
                                                                                <span class="tsk_depends_p">Select</span>
                                                                                <i class="caret mtop-10 fr"></i>
                                                                            </a>
                                                                        </div>                            
                                                                <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][depends]" id="pr_tmpl_mls_depends_<%= mlstn_case_cntr %>" data-fldname="depends" type="hidden" data_id="<%= mlst_tsk_depends %>" class="form-control depends dependsEdit" value="" >
                                                                <input name="data[ProjectTemplate][ProjectTemplateMilestone][<%= mlstn_cntr %>][ProjectTemplateCase][<%= mlstn_case_cntr %>][seq_order]" id="pr_tmpl_mls_seq_order_<%= mlstn_case_cntr %>" data-fldname="seq_order" type="hidden" class="form-control seq_order" value="" >
                                                               </div>
                                                            </div>
                                                            <div style="color:red; font-style: italic; float:left;width: 40%;font-size:12px; line-height: 20px;">This feature will be enable after install Gantt Chart Addon</div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
						                        <tr>
						                            <td></td>
						                            <td class="btn_align">
						                                <span class="ldr-ad-btn">
															<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="loading..." title="loading..."> 
														</span>
						                                <span>
						                                 <% if(is_dummy){ %>
										                	<button class="btn btn_blue" type="button" data-mlstntskno="<%= mlstn_case_cntr %>" data-mlstnno="<%= mlstn_cntr %>" onclick="po.validateMstntsk(this)"><i class="icon-big-tick"></i>Update Task</button>
										                <% }else{ %>
															<button class="btn btn_blue" type="button" data-mlstntskno="<%= mlstn_case_cntr %>" data-state="exist" data-mlstnno="<%= mlstn_cntr %>" onclick="po.validateMstntsk(this,<%= mlstn_case_cntr %>,<%= mlstn_cntr %>,<%= mlst_tsk_id %>)"><i class="icon-big-tick"></i>Update Task</button>
										                <% } %>
												            <span class="or_cancel">or<a onclick="po._cancelmlstntsk(this);" data-mlstnno="<%= mlstn_cntr %>" data-mlstntskno="<%= mlstn_case_cntr %>">Cancel</a></span>
						                                </span>
						                            </td>
						                        </tr>
						                    </tbody>
						                </table>
						            </td>
						        </tr>
						        <% mlstn_case_cntr++;}
								} %>
						    </tbody>
						</table>

				        </div>
				    </td>
				</tr>
		<% mlstn_cntr++;}
		} %>
		</tbody>
	</table>