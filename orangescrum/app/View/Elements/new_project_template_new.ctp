<style type="text/css">
.form-control[readonly] {cursor: text;background-color: #ffffff;}
.footer-subm-btn{padding: 30px 30px 10px; text-align: right;}
.scrollable-form-area{max-height:350px;overflow-y: scroll;}
.clpse-icon{width:30px;height:30px;vertical-align: inherit;display:inline-block;margin:0 5px;text-align:center;cursor: pointer;}
.pr-temp-mlstn-edit-icon{width:21px;height:20px;vertical-align:middle;display:inline-block;margin:0 5px 0 10px;text-align:center}
.pr-temp-mlstn-delete-icon{width:21px;height:20px;vertical-align:middle;display:inline-block;margin:0 5px;text-align:center}
.pr-temp-mlstn-add-link{display:block;color:#06C;text-decoration:underline;font-size:12px;padding-left: 80px;}
table.pr_templ_tsksss body tr td:first-child{width:141px;}
table.pr_templ_tsks_forms tbody tr td:first-child{width:124px;}
.dn{visibility: hidden;}
.glyphicon{color: #00bcd5;}
.opt51 a,.more_opt a ,.opt52 a,.more_opt_depends a{display:block; text-align: left;}
.depends_row{display:none;}
.dropdown .more_opt_depends{position:relative;}
.dropdown .more_opt_depends ul { background:#fff;border:1px solid #D3D3D3;color:#C5C0B0;display:none;list-style:none;left: -7px;margin-top: 3px;padding: 4px;position: absolute;width: 190px;}
.more_opt_depends ul li a { padding:3px 3px;display:block;z-index:3;}
.opt1 ul li a:hover, .more_opt ul li a:hover,.more_opt_depends ul li a:hover{ background:#F5F5F5 ;display:block;}
.graw_out {background:#ddd;}
.infod{font-size: 12px; text-align: left; padding:5px 10px;display:inline-block;float:left;}
.infod img {width:15px;}


</style>
<div class="cb"></div>
<div id="proj_temp_new_temp">
	<?php echo $this->Form->create('ProjectTemplate', array('id' => 'add-project-template')); ?>
	<center>
		<div id="proj_temp_new_err" class="fnt_clr_rd" style="display:block;font-size:15px;"></div>
	</center>
	<div class="loader_dv" style="display: none;">
		<center><img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..."></center>
	</div>
	<div id="pr_templat_form" class="scrollable-form-area"></div>
	<div class="footer-subm-btn">
		<span id="proj_temp_new_loader" class="ldr-ad-btn">
		<img src="<?php echo HTTP_ROOT; ?>img/images/case_loader2.gif" alt="loading..." title="loading..."></span>
		<span id="proj_temp_task_btn"><button class="btn btn_blue" type="button" onclick="po.validateTemplate()"><i class="icon-big-tick"></i><span id="templa-btn-status">Save</span></button>
		<span class="or_cancel">or<a onclick="closePopup();">Cancel</a></span></span>
	</div>

	<?php echo $this->Form->end(); ?>
</div>

<table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_mls" style="display:none;">
	<tr>
	    <td><span class="fnt_clr_rd">* </span><?php echo __("Title"); ?>:</td>
	    <td>
 			<input name="data[ProjectTemplate][ProjectTemplateMilestone][0][title]"  type="text" class="form-control" value="">
            <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][id]" type="hidden" class="form-control" value="">
            <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][is_default]" type="hidden" class="form-control" value="0">
	    </td>
	</tr>
	<tr>
	    <td style="vertical-align:top"><?php echo __("Description"); ?>:</td>
	    <td>
	    	<textarea name="data[ProjectTemplate][ProjectTemplateMilestone][0][description]" class="form-control"></textarea>
	    </td>
	</tr>
	<tr>
	    <td colspan="2">
	    	<div style="width:28%;float:left">
	    		<?php echo __("Start Date"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
    		</div>
	    	<div style="width:29%;float:left">
	    		<input name="data[ProjectTemplate][ProjectTemplateMilestone][0][start_date]" id="pr_tmpl_mls_start_date" type="text" class="datepicker form-control" data-fldname="start_date" value="" readonly>
	    	</div>
	    	<div style="width:13%;float:left">
	    		<?php echo __("End Date"); ?>:&nbsp;&nbsp;
	    	</div>
	    	<div style="width:30%;float:left">
	    		<input name="data[ProjectTemplate][ProjectTemplateMilestone][0][end_date]" data-fldname="end_date" id="pr_tmpl_mls_end_date" type="text" class="datepicker form-control" value="" readonly>
	    	</div>
	    	<div style="clear:left"></div>
    	</td>
	</tr>
<!--    <tr>
	      <td colspan="2">
            <div style="width:28%;float:left">
                <?php echo __("Estimated Hours"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
            </div>
            <div style="width:29%;float:left">
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][estimated_hours]" id="pr_tmpl_mls_estimated_hours" type="text" data-fldname="estimated_hours" class="form-control" value="">
            </div>
            <div style="width:13%;float:left">
                <?php echo __("Assign To"); ?>:&nbsp;&nbsp;
            </div>
            <div style="width:30%;float:left">
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][assign_to]" id="pr_tmpl_mls_assign_to" data-fldname="assign_to" type="text " class="form-control" value="" >
            </div>
            <div style="clear:left"></div>
        </td>
	</tr>-->
	<tr>
		<td></td>
		<td class="btn_align">
			<span class="ldr-ad-btn">
				<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="loading..." title="loading..."/> 
			</span>
			<span>
				<button class="btn btn_blue" type="button" data-mlstnno="" onclick="po.validateMstn(this)"><i class="icon-big-tick"></i><?php echo __("Create Milestone"); ?></button>
	            <span class="or_cancel"><?php echo __("or"); ?><a onclick="po._dltmlstn(this,'','');" data-mlstnno=""><?php echo __("Cancel"); ?></a></span>
			</span>
		</td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" class="col-lg-12 pr_templ_tsks" style="display:none;">
	<tr>
	    <td><span class="fnt_clr_rd">* </span><?php echo __("Title"); ?>:</td>
	    <td>
 			<input id="" name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" data-fldname="title" readonly type="text" class="form-control" value="">
            <input id="" name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" data-fldname="id" type="hidden" class="form-control" value="">
	    </td>
	</tr>
	<tr>
	    <td style="vertical-align:top"><?php echo __("Description"); ?>:</td>
	    <td>
	    	<textarea name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" data-fldname="description" class="form-control"></textarea>
	    </td>
	</tr>
	<tr>
	    <td colspan="2">
	    	<div style="width:22%;float:left">
	    		<?php echo __("Start Date"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
			</div>
	    	<div style="width:28%;float:left">
	    		<input id="pr_tmpl_mls_task_start_date" name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" data-fldname="start_date" type="text" class="datepicker form-control" value="" readonly>
	    	</div>
	    	<div style="width:20%;float:left">
	    		<?php echo __("End Date"); ?>:
	    	</div>
	    	<div style="width:30%;float:left">
	    		<input id="pr_tmpl_mls_task_end_date" data-fldname="end_date" name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" type="text" class="datepicker form-control" value="" readonly>
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
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_estimated_hours" onkeypress="return numericDecimal(event)" onkeyup="changeDate()" maxlength="6" type="text" data-fldname="estimated_hours" class="form-control" value="">
            </div>
            <div style="width:20%;float:left">
                <?php echo __("Assign To"); ?>:&nbsp;&nbsp;
            </div>
            <div style="width:30%;float:left">
                	<div class="dropdown option-toggle p-6" >
                        <div class="opt51">
                            <a href="javascript:jsVoid()" onclick="po.open_more_opt(this,'more_opt');" >                               
                                <span class="tsk_asgn_to_p">No Body</span>
                                <i class="caret mtop-10 fr"></i>
                            </a>
                        </div>
                        <div class="more_opt">
                            <ul>                                
                            </ul>
                        </div>
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_assign_to" data-fldname="assign_to" type="hidden" class="form-control assignto" value="0" >
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
                            <a href="javascript:jsVoid()" onclick="po.open_more_opt(this,'more_opt_depends');" >                               
                                <span class="tsk_depends_p"></span>
                                <i class="caret mtop-10 fr"></i>
                            </a>
                        </div>
                        <div class="more_opt_depends">
                            <ul>    
                                <li><a href="javascript:jsVoid()" onclick="po._depends(-1,this,'Select');" value="-1"><span class="value">-1</span>Select</a></li>
                            </ul>
                        </div>
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_depends" data-fldname="depends" type="hidden" class="form-control depends" value="" >
                <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_seq_order" data-fldname="seq_order" type="hidden" class="form-control seq_order" value="0" >
               </div>
            </div>
			<div class="infod"><img src="<?php echo HTTP_ROOT;?>img/help_icon.png" title="Circular dependency is not allowed. Circular dependency means the parent task depends to child task and the child task is depends to the parent task!"/> </div>
        </td>
    </tr>
    <?php }else{ ?>
        <tr class="depends_row class graw_out">
            <td colspan="2">
                <div style="width:22%;float:left;">
                    <?php echo __("Depends On"); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
                </div>
                 <div style="width:30%;float:left">
                        <div class="dropdown option-toggle p-6" >
                            <div class="opt52">
                                <a href="javascript:jsVoid()">                               
                                    <span class="tsk_depends_p">Select</span>
                                    <i class="caret mtop-10 fr"></i>
                                </a>
                            </div>                            
                    <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_depends" data-fldname="depends" type="hidden" class="form-control depends" value="" >
                    <input name="data[ProjectTemplate][ProjectTemplateMilestone][0][ProjectTemplateCase]" id="pr_tmpl_mls_seq_order" data-fldname="seq_order" type="hidden" class="form-control seq_order" value="" >
                   </div>
                </div>
                <div style="color:red; font-style: italic; float:left:width: 40%; font-size:12px; line-height: 20px;">This feature will be enabled after installing Gantt Chart Addon</div>
                
            </td>
        </tr>
    <?php } ?>
	<tr>
	<td></td>
	<td class="btn_align">
		<span class="ldr-ad-btn">
			<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="loading..." title="loading..."/> 
		</span>
		<span>
			<button class="btn btn_blue" type="button" data-mlstntskno="" data-mlstnno="" onclick="po.validateMstntsk(this,'','')"><i class="icon-big-tick"></i><?php echo __("Create Task"); ?></button>
            <span class="or_cancel"><?php echo __("or"); ?><a onclick="po._dltmlstntsk(this);" data-mlstnno="" data-mlstntskno=""><?php echo __("Cancel"); ?></a></span>
		</span>
	</td>
	</tr>
</table>
