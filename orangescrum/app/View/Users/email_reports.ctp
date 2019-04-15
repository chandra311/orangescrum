<div class="user_profile_con email_rpt">
    <!--Tabs section starts -->
    <?php echo $this->element("personal_settings");?>

    <div class="email_hd">
        <h2 style=""><?php echo __("Send me Email Reports"); ?></h2>
    </div>
    <?php echo $this->Form->create('UserNotification',array('url'=>'/users/email_reports','onsubmit'=>"return validateemailrpt();")); ?>
    <table cellspacing="0" cellpadding="0" class="email_mgt">
        <input type="hidden" name="data[UserNotification][id]" value="<?php echo @$getAllNot['UserNotification']['id']; ?>"/>
        <input type="hidden" name="data[UserNotification][type]" value="1"/>
        <tbody>
            <?php if(SES_TYPE<3) {?>
            <tr>
                <th><?php echo __("Weekly Usage"); ?>:</th>
                <td>
                    <input type="radio" name="data[UserNotification][weekly_usage_alert]" id="wkugalyes" value="1" <?php if(@$getAllNot['UserNotification']['weekly_usage_alert'] == 1) {
                            echo 'checked="checked"';
                               } ?> /><?php echo __("Yes"); ?>
                    <input type="radio" name="data[UserNotification][weekly_usage_alert]" id="wkugalno" value="0" <?php if(@$getAllNot['UserNotification']['weekly_usage_alert'] == 0) {
                            echo 'checked="checked"';
                               } ?> /><?php echo __("No"); ?>
                </td>
            </tr>
                <?php } ?>
            <tr>
                <th><?php echo __("Task Status"); ?>:</th>
                <td>
                    <input type="radio" name="data[UserNotification][value]" id="valdaily" value="1" <?php if(@$getAllNot['UserNotification']['value'] == 1) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("Daily"); ?>
                    <input type="radio" name="data[UserNotification][value]" id="valweekly" value="2" <?php if(@$getAllNot['UserNotification']['value'] == 2) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("Weekly"); ?>
                    <input type="radio" name="data[UserNotification][value]" id="valmonthly" value="3" <?php if(@$getAllNot['UserNotification']['value'] == 3) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("Monthly"); ?>
                    <input type="radio" name="data[UserNotification][value]" id="valnone" value="0" <?php if(@$getAllNot['UserNotification']['value'] == 0) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("None"); ?>
                </td>
            </tr>
            <tr>
                <th class="last"><?php echo __("Task Due (daily)"); ?>:</th>
                <td class="last">
                    <input type="radio" name="data[UserNotification][due_val]" id="dueyes" value="1" <?php if(@$getAllNot['UserNotification']['due_val'] == 1) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("Yes"); ?>
                    <input type="radio" name="data[UserNotification][due_val]" id="dueno" value="0" <?php if(@$getAllNot['UserNotification']['due_val'] == 0) {
                        echo 'checked="checked"';
                           } ?> /><?php echo __("No"); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="cbt"></div>
    <div class="email_hd">
        <h2><?php echo __("Daily Update Report"); ?></h2>
    </div>
    <table cellspacing="0" cellpadding="0" class="email_mgt">
        <tbody>
            <tr>
                <th><?php echo __("Send me Email"); ?>:</th>
                <td>
                    <input type="radio" name="data[DailyupdateNotification][dly_update]"  id="dlyupdateyes" value="1" <?php if(@$getAllDailyupdateNot['DailyupdateNotification']['dly_update'] == 1) {
                        echo 'checked="checked"';
                           } ?> onClick="showbox('show')" /><?php echo __("Yes"); ?>
                    <input type="radio" name="data[DailyupdateNotification][dly_update]"  id="dlyupdateno" value="0" <?php if(@$getAllDailyupdateNot['DailyupdateNotification']['dly_update'] == 0) {
                        echo 'checked="checked"';
                           } ?> onClick="showbox('hide')"/><?php echo __("No"); ?>
                </td>
            </tr>
            <?php
            if(@$getAllDailyupdateNot['DailyupdateNotification']['dly_update'] == 1) {
                $style = '';
                $hr_min = split(':',$getAllDailyupdateNot['DailyupdateNotification']['notification_time']);
            }
            else
                $style = 'style="display:none"';
            ?>
            <tr <?php echo $style; ?> id="dlyupdt">
                <td colspan="2">
                    <table class="col-lg-5 email_mgt rpt_padding">
                        <tbody>
                            <tr>
                                <th><?php echo __("Time"); ?>:</th>
                                <td>
                                    <select id="not_hr" class="form-control mod-wid-153 fl" name="data[DailyupdateNotification][not_hr]">
                                        <option selected="" value=""><?php echo __("Hour"); ?></option>
                                        <?php
                                        for($i = 1;$i<=24;$i++) {
                                            if($i<=9) {
                                                $i = '0'.$i;
                                            }
                                            ?>
                                        <option value="<?php echo $i; ?>" <?php if($i == $hr_min[0]) echo 'selected'; ?>><?php echo $i; ?></option>
                                            <?php }	?>
                                    </select>
                                    <select id="not_mn" class="form-control mod-wid-153 fl min_mgt" name="data[DailyupdateNotification][not_mn]">
                                        <option selected="" value=""><?php echo __("Min"); ?></option>
                                        <?php
                                        for($i =0;$i<=45;$i=$i+15) {
                                            if($i<10)
                                                $i = '0'.$i;
                                            ?>
                                        <option value="<?php echo $i; ?>"<?php if($i == $hr_min[1]) echo 'selected'; ?>><?php echo $i; ?></option>
                                            <?php } ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><?php echo __("Select Projects"); ?>:</th>
                                <td class="last">
                                    <div class="span4">
                                        <select name="data[DailyupdateNotification][proj_name]" id="rpt_selprj" class="form-control mod-wid-153 fl min_mgt">
                                            <?php
                                            if($getAllDailyupdateNot['DailyupdateNotification']['proj_name'] != '') {
                                                $pjarr = explode(",",$getAllDailyupdateNot['DailyupdateNotification']['proj_name']);
                                                if(isset($pjarr[0])) {
                                                    foreach($pjarr as $pjtnm) {
                                                        ?>
                                            <option value="<?php echo $pjtnm;?>" class="selected">
                                                            <?php
                                                            $prjtnm = $this->Casequery->getProjectName($pjtnm);
                                                            echo $prjtnm['Project']['name'];
                                                            ?>
                                            </option>
                                                        <?php  	}

                                                }else {  ?>
                                            <option value="<?php echo $pjarr;?>" class="selected">
                                                        <?php
                                                        $prjtnm = $this->Casequery->getProjectName($pjarr);
                                                        echo $prjtnm['Project']['name'];
                                                        ?>
                                            </option>
                                                    <?php	}
                                                ?>
                                                <?php }
                                            ?>
                                        </select>

                                    </div>
                                    <span id="ajax_loader" style="display:none;position:absolute; right: -25px;top: 59px;">
                                        <img src="<?php echo HTTP_IMAGES; ?>images/del.gif" alt="Loading..." />
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="cbt"></div>
    <table cellspacing="0" cellpadding="0" class="col-lg-5 rpt_tbl">
        <tbody>
            <tr>
                <th></th>
                <td class="btn_align btn_eml_lt">
                    <span id="subprof1">
                        <input type="hidden" name="data[User][changepass]" id="changepass" readonly="true" value="0"/>
                        <button type="submit" value="Save" name="submit_Pass"  id="submit_Pass" class="btn btn_blue"><i class="icon-big-tick"></i><?php echo __("Update"); ?></button>
                        <!--<button type="button" class="btn btn_grey" onclick="cancelProfile('<?php echo $referer;?>');"><i class="icon-big-cross"></i>Cancel</button>-->
                        <span class="or_cancel"><?php echo __("or"); ?>
                            <a onclick="cancelProfile('<?php echo $referer;?>');"><?php echo __("Cancel"); ?></a>
                        </span>
                    </span>
                    <span id="subprof2" style="display: none">
                        <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="<?php echo __("Loading"); ?>..." />
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <?php echo $this->Form->end(); ?>
    <div class="cbt"></div>
</div>
<script>
    $(document).ready(function(){
        getAutocompleteTag("rpt_selprj", "users/getProjects", "380px", "Type to select projects");
    });
    function showbox(act){
        if(act == 'show'){
            $('#dlyupdt').slideDown("fast");
        }else{
            $('#dlyupdt').slideUp("fast");
        }
    }
</script>
