<?php $userArr = $GLOBALS['projOwnAdmin']; ?> 
<center><div id="err_msg" style="color:#FF0000;display:none;"></div></center>
<?php echo $this->Form->create('Project', array('url' => '/projects/add_project', 'name' => 'projectadd', 'onsubmit' => 'return projectAdd(\'txt_Proj\',\'txt_shortProj\',\'txt_Proj\',\'loader\',\'btn\')')); ?>
<div class="data-scroll">
    <table cellpadding="0" cellspacing="0" class="col-lg-12">
        <tr>
            <td class="popup_label"><?php echo __("Project Name"); ?>:</td>
            <td>
                <?php echo $this->Form->text('name', array('value' => '', 'class' => 'form-control', 'id' => 'txt_Proj', 'placeholder' => __("My Project", true), 'maxlength' => '100')); ?>

            </td>
        </tr>
        <tr>
            <td><?php echo __("Customer Name"); ?>:</td>
            <td>
                <?php echo $this->Form->text('short_name', array('value' => '', 'class' => 'form-control ttu', 'id' => 'txt_shortProj', 'placeholder' => "MP", 'maxlength' => '100')); ?>
                <span id="ajxShort" style="display:none">
                    <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" width="16" height="16"/>
                </span>
                <span id="ajxShortPage"></span>
            </td>
        </tr>
		<tr>
            <td class="popup_label"><?php echo __("Product Type"); ?>:</td>
            <td>
                <?php echo $this->Form->text('product_type', array('value' => '', 'class' => 'form-control', 'id' => 'txt_Prod', 'placeholder' => __("Product", true), 'maxlength' => '100')); ?>

            </td>
        </tr>
        <?php if (defined('TSG') && TSG == 1) { ?>
            <tr>
            <td><?php echo __('Task Status Group'); ?>:</td>
                <td>
                    <select id="add_workflow" class="form-control" name="data[Project][workflow_id]">
                    <option value="default" selected="selected"><?php echo __('Default Task Status Group'); ?></option>
                    <?php if (is_array($GLOBALS['workflowList'])) { ?>
                        <?php foreach ($GLOBALS['workflowList'] as $k => $val) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $val; ?></option>
                        <?php } ?>
                    <?php } ?>
                    </select>
                </td>
            </tr>
        <?php } ?>
        <?php if (defined('PT') && PT == 1) { ?>
            <tr id="default_projtemp_tr"  >
                <td>Template:<div class="opt_field">(optional)</div></td>
                <td class="v-top">
                    <select name="data[Project][module_id]" id="sel_Typ" class="form-control" onchange="checkNewTmplVal(this.value);
                            view_btn_case(this.value);">
                        <option value="0" selected>[<?php echo __("Select"); ?>]</option>
                        <?php if($templates_modules){ foreach ($templates_modules as $templates_modules => $val) { ?>
                            <option value="<?php echo $val['ProjectTemplate']['id'] ?>"><?php echo $val['ProjectTemplate']['module_name'] ?></option>
                        <?php }}?>
                    </select>
                    <!--span id="btn_cse" style="display:none;margin-top:10px;">
                            <a href="javascript:jsVoid();" style="margin-left:3px;width:100px;font-size:12px;" class="blue small" onclick="viewTemplateCases();">View Task</a>
                        </span>
                        <span id="btn_load" style="display:none;margin-top:10px;">
                            <a href="javascript:jsVoid()" style="text-decoration:none;cursor:wait;margin-left:3px;width:100px;">
                            Loading...<img src="<?php echo HTTP_IMAGES; ?>images/del.gif" width="16" height="16" alt="loading..." title="loading..."/>
                            </a>
                        </span-->
                </td>
            </tr>
        <?php } ?>
        <?php
        if (defined('CR') && CR == 1 && SES_CLIENT == 1 && $this->Format->get_client_permission('user') == 1) {
            /*             * Not Show create project */
        } else {
            ?> 
            <?php if (!isset($is_active_proj) || $is_active_proj) { ?>
                <tr>
                    <td class="v-top">
                        <div style="text-align:right">
                            <span id="add_new_member_txt">
                                <?php if (count($userArr) < 2) { ?>
                                <?php echo __("Add new Users"); ?>:
                                <?php } else { ?>	
                                    <?php echo __("Add Users"); ?>:
                                <?php } ?>	
                            </span>
                        <div class="opt_field">(<?php echo __("optional"); ?>)</div>
                        </div>
                    </td>
                    <td style="text-align:left">
                        <div class="fl check_user">
                            <?php foreach ($userArr AS $k => $usr) { ?>
                                <label class="checkbox-inline" style="margin:0 10px 5px 0;">
                                    <input type="checkbox" checked="checked" name="data[Project][members][]" class="proj_mem_chk" onclick="addremoveadmin(this)"  value="<?php echo $usr['User']['id']; ?>"/>
                                    &nbsp;<span id="puser<?php echo $usr['User']['id']; ?>"><?php echo $usr['User']['name']; ?></span>
                                    <?php if ($usr['CompanyUser']['user_type'] == 1) { ?>
                                    <span class="user_green">(<?php echo __("owner"); ?>)</span>
                                    <?php } else { ?>
                                    <span class="user_red">(<?php echo __("admin"); ?>)</span>
                                    <?php } ?>
                                </label>
                            <?php } ?>								
                        </div>
                        <textarea id="members_list"  class="wickEnabled form-control expand" rows="2" wrap="virtual" name="data[Project][members_list]"></textarea>
                    <div class="user_inst">(<?php echo __("Use comma to separate multiple email ids"); ?>)</div>
                        <div id="err_mem_email" style="display: none;color: #FF0000;"></div>
                        <div id="autopopup"></div>
                    </td>
                </tr>
                <?php /* ?><tr id="default_assignto_tr" <?php if(count($userArr)<2){?>style="display: none;" <?php }?>>
                  <td>Default Assign To:</td>
                  <td>
                  <select id="select_default_assign" class="form-control" name="data[Project][default_assign]">
                  <option value="">-Select-</option>
                  <?php foreach ($userArr AS $k => $usr) { ?>
                  <option value="<?php echo $usr['User']['id']; ?>" <?php if (!$k) { ?>selected<?php } ?>><?php echo $usr['User']['name']; ?></option>
                  <?php } ?>
                  </select>
                  </td>
                  </tr><?php */ ?>
            <?php } ?>
        <?php } ?>
    </table>    
</div>
<div style="padding-left:145px;">
    <?php
    $totProj = "";
    if ((!$user_subscription['is_free']) && ($user_subscription['project_limit'] != "Unlimited")) {
        $totProj = $this->Format->checkProjLimit($user_subscription['project_limit']);
    }
    if ($totProj && $totProj >= $user_subscription['project_limit']) {
        if (SES_TYPE == 3) {
            ?>
            <font color="#FF0000"><?php echo __("Sorry, Project Limit Exceeded!"); ?></font>
            <br/>
            <font color="#F05A14"><?php echo __("Please contact your account owner to create more projects"); ?></font>
            <?php
        } else {
            ?>
            <font color="#FF0000"><?php echo __("Sorry, Project Limit Exceeded!"); ?></font>
            <br/>
            <font color="#F05A14"><a href="<?php echo HTTP_ROOT; ?>pricing"><?php echo __("Upgrade"); ?></a> <?php echo __("you account to create more projects"); ?></font>
            <?php
        }
    } else {
        ?>
        <input type="hidden" name="data[Project][validate]" id="validate" readonly="true" value="0"/>
        <span id="loader" style="display:none;">
            <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loader"/>
        </span>
        <span id="btn">
            <button type="button" value="Create" name="crtProject" class="btn btn_blue" onclick="return projectAdd('txt_Proj', 'txt_shortProj', 'loader', 'btn');"><i class="icon-big-tick"></i><?php echo __("Create"); ?></button>
        <!--<button class="btn btn_grey" type="button" onclick="closePopup();"><i class="icon-big-cross"></i>Cancel</button>-->
            <span class="or_cancel"><?php echo __('or'); ?>
                <a onclick="closePopup();"><?php echo __("Cancel"); ?></a>
            </span>
        </span>
        <?php
    }
    ?>
</div>
<?php echo $this->Form->end(); ?>
<script>
    function checkNewTmplVal(tmplVal) {
        if (tmplVal == 'new') {
            $('.crt_project_tmpl').hide();
            $('.new_project').hide();
            createNewTemplate();
        } else {
            $("#projtemptitle").val('');
            $("#projtemptitle").focus();
            $("#project_temp_err").html('');
            $(".project_temp_popup").hide();
        }
    }
    var new_tmpl = '';
    function createNewTemplate() {
        $('.crt_project_tmpl').hide();
        $('.new_project').hide();
        openPopup();
        new_tmpl = 1;
        $("#projtemptitle").val('').keyup();
        $("#projtemptitle").focus();
        $("#project_temp_err").html('');
        $(".project_temp_popup").show();
    }
    function CreateTemplate() {
        var temp_id = $("#hid_tmpl").val();
        if (temp_id != 0) {
            if ($('#projFil').val() !== 'all') {
                var cbval = '';
                var case_id = new Array();
                var spval = '';
                var case_no = new Array();
                $('input[id^="actionChk"]').each(function (i) {
                    if ($(this).is(":checked") && !($(this).is(":disabled"))) {
                        cbval = $(this).val();
                        spval = cbval.split('|');
                        case_id.push(spval[0]);
                        case_no.push(spval[1]);
                    }
                });
            } else {
                return false;
            }
            if (1) {
                $("#crtprjtmpl_btn").hide();
                $("#crtprjtmplloader").show();
                $.post(HTTP_ROOT + "templates/createProjectTemplateFromTasks", {"temp_id": temp_id, "case_id": case_id}, function (res) {
                    closePopup();
                    if (res.msg == 'success') {
                        showTopErrSucc('success', '<?php echo __("Template updated successfully"); ?>.');
                        document.location.href = HTTP_ROOT + "templates/projects";
                    } else {
                        showTopErrSucc('error', "<?php echo __("Unable to update project template"); ?>.");
                        return false;
                    }
                }, 'json');
            } else {
                return false;
            }
        } else {
            showTopErrSucc('error', "<?php echo __("Please select one template"); ?>.");
            return false;
        }
    }
</script>