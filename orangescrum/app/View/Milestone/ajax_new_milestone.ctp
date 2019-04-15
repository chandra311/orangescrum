<?php

echo $this->Form->create('Milestone', array('id' => 'addmilestone' )); ?>
<div class="data-scroll">
    <table cellpadding="0" cellspacing="0" class="col-lg-12">
        <tr>
            <td class="v-top"><?php echo __("Project"); ?>:</td>
            <td <?php if ($milearr['Milestone']['id']) {?> style="text-align: left;" <?php }?>>
		<?php if (!$milearr['Milestone']['id']) { ?>
                <select id="project_id" name="data[Milestone][project_id]" class="form-control" onchange="fetch_select()">
                    <option value="">--<?php echo __("Select"); ?>--</option>
				<?php foreach ($projArr as $prjarr) {  ?>
                    <option <?php  if ($projUid == $prjarr['Project']['uniq_id']) { echo "selected=selected";} ?>
                        value="<?php echo $prjarr['Project']['id']; ?>" data-pname="<?php echo $prjarr['Project']['name']; ?>" data-puniq="<?php echo $prjarr['Project']['uniq_id']; ?>"><?php echo $this->Format->formatText($prjarr['Project']['name']); ?></option>
					<?php } ?>
                </select>
			<?php } else { ?>
                <input type="hidden" name="data[Milestone][project_id]" id="project_id" value="<?php echo $milearr['Milestone']['project_id']; ?>" data-pname="<?php echo $projArr[0]['Project']['name']; ?>" data-puniq="<?php echo $projArr[0]['Project']['uniq_id']; ?>">
                <b><?php echo $this->Format->formatText($projArr[0]['Project']['name']); ?></b>
			<?php } ?>
            </td>
        </tr>
        <tr>
            <td class="v-top"><?php echo __("Assign To User"); ?>:</td>
            <td style="text-align: left;">
                 <?php echo $this->Form->input('assign_id',array('type'=>'select','empty'=>'Choose One','label'=>false,'div'=>false,"class"=>"form-control",'id'=>'ass_id'));?>
            </td>
        </tr>
        <tr>
            <td><span class="fnt_clr_rd">* </span><?php echo __("Title"); ?>:</td>
            <td>
		<?php echo $this->Form->text('title', array('class' => 'form-control', 'id' => 'title', 'maxlength' => '100', 'value' => $milearr['Milestone']['title'])); ?>
		<?php echo $this->Form->hidden('user_id', array('id' => 'user_id','value' => SES_ID)); ?>
		<?php echo $this->Form->hidden('id', array('id' => 'id', 'value' => $milearr['Milestone']['id'])); ?>
            </td>
        </tr>
        <tr>
            <td style="vertical-align:top"><?php echo __("Description"); ?>:</td>
            <td>
		<?php echo $this->Form->hidden('id', array('type' => 'textarea', 'id' => 'id', 'value' => $milearr['Milestone']['id'])); ?>
		<?php echo $this->Form->textarea('description', array('id' => 'description', 'class' => 'form-control', 'value' => $milearr['Milestone']['description'])); ?>
            </td>
        </tr>
        <tr>
            <td><span class="fnt_clr_rd">* </span><?php echo __("Start Date"); ?>:</td>
	    <?php
	    if (!empty($milearr['Milestone']['start_date']) && $milearr['Milestone']['start_date'] != '0000-00-00') {
			$milearr['Milestone']['start_date'] = date('M j, Y',strtotime($milearr['Milestone']['start_date']));
	    }else if($milearr['Milestone']['start_date'] == '0000-00-00'){
			$milearr['Milestone']['start_date'] = '';
	    }
	    if (!empty($milearr['Milestone']['end_date']) && $milearr['Milestone']['end_date'] != '0000-00-00') {
			$milearr['Milestone']['end_date'] = date('M j, Y',strtotime($milearr['Milestone']['end_date']));
	    }else if($milearr['Milestone']['end_date'] == '0000-00-00'){
			$milearr['Milestone']['end_date'] = '';
	    }
	    ?>
            <td>
		<?php echo $this->Form->text('start_date', array('class' => 'datepicker form-control', 'id' => 'start_date', 'readonly' => 'readonly', 'value' => $milearr['Milestone']['start_date'])); ?>
            </td>
        </tr>
        <tr>
            <td><span class="fnt_clr_rd">* </span><?php echo __("End Date"); ?>:</td>
            <td>
		<?php echo $this->Form->text('end_date', array('class' => 'datepicker form-control', 'id' => 'end_date', 'readonly' => 'readonly', 'value' => $milearr['Milestone']['end_date'])); ?>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <div class="fl">
                    <span id="ldr" style="display:none;">
                        <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..." />
                    </span>
                    <span id="btn_mlstn">
                        <button type="button" value="Update" name="milestone" id="milestone" class="btn btn_blue" onclick="return validatemilestone();"><i class="icon-big-tick"></i><?php if (!empty($edit)) { echo __("Save");}else {echo __("Add");} ?></button>
                <!--<button class="btn btn_grey reset_btn" type="button" name="cancel" onclick="closePopup();" ><i class="icon-big-cross"></i>Cancel</button>-->
                        <span class="or_cancel"><?php echo __('or'); ?>
                            <a onclick="closePopup();"><?php echo __("Cancel"); ?></a>
                        </span>
                    </span>
                </div>
            </td>
        </tr>
    </table>
</div>

<input type="hidden" value="<?php echo $mlstfrom;?>" id="milestone_crted_from"/>
<?php echo $this->Form->end(); ?>
<script>
    $(function(){
       var pro_id = $("#project_id").val(); 
       if(pro_id!=""){
           fetch_select();
       }
    });
    $(function () {
        $("#start_date").datepicker({
            dateFormat: 'M d, yy',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function (selectedDate) {
                $("#end_date").datepicker("option", "minDate", selectedDate);
            },
        });
    });
    $(function () {
        $("#end_date").datepicker({
            dateFormat: 'M d, yy',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function (selectedDate) {
                $("#start_date").datepicker("option", "maxDate", selectedDate);
            },
        });
    });
    function validatemilestone() {
        //console.log($('#addmilestone').serialize());return false;
        var title = $('#title').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var project_id = $('#project_id').val();

        if ($('#id').val()) {
            var proj_uniq = $('#project_id').attr('data-puniq');
            var proj_name = $('#project_id').attr('data-pname');
        } else {
            var proj_uniq = $('#project_id option[value="' + project_id + '"]').attr('data-puniq');
            var proj_name = $('#project_id option[value="' + project_id + '"]').attr('data-pname');
        }

        var errMsg;
        var done = 1;

        if (project_id.trim() == "") {
            errMsg = '<?php echo __("Project cannot be left blank!"); ?>';
            $('#project_id').focus();
            done = 0;
        } else if (title.trim() == "") {
            errMsg = '<?php echo __("Title cannot be left blank!"); ?>';
            $('#title').focus();
            done = 0;
        } else if (start_date.trim() == "") {
            errMsg = '<?php echo __("Start Date cannot be left blank!"); ?>';
            $('#start_date').focus();
            done = 0;
        } else if (end_date.trim() == "") {
            errMsg = '<?php echo __("End Date cannot be left blank!"); ?>';
            $('#end_date').focus();
            done = 0;
        } else if (Date.parse(start_date) > Date.parse(end_date)) {
            errMsg = '<?php echo __("Start Date cannot exceed End Date!"); ?>';
            $('#end_date').focus();
            done = 0;
        }
        if (done == 0) {
            showTopErrSucc('error', errMsg);
            return false;
        } else {
            var mdata = $('#addmilestone').serialize();
            //var url = HTTP_ROOT+'milestones/ajax_new_milestone?'+mdata;
            var url = HTTP_ROOT + 'milestones/ajax_new_milestone';
            $('#inner_mlstn #btn_mlstn').hide();
            $('#inner_mlstn #ldr').show();
            //$.post(url, {"mdata":mdata}, function(res){
            $.ajax({
                type: "POST",
                url: url,
                data: mdata,
                dataType: "json",
                success: function (res) {
                    if (res.error) {
                        showTopErrSucc('error', res.msg);
                    } else if (res.success) {
                        showTopErrSucc('success', res.msg);
                    }
                    $('#mlstPage').val(1);
                    if ($('#milestone_crted_from').val() == 'createTask') {
                        milstoneonTask($('#title').val(), res.milestone_id);
                    } else if ($('#caseMenuFilters').val() == 'milestone') {
                        if ($('#id').val()) {
                            //ManageMilestoneList();alert(1);
                            updateAllProj('proj_' + proj_uniq, proj_uniq, 'dashboard', '0', proj_name);
                        } else {
                            //ManageMilestoneList(1);
                            $('#mlsttabvalue').val(1);
                            updateAllProj('proj_' + proj_uniq, proj_uniq, 'dashboard', '0', proj_name);
                        }
                    } else if ($('#caseMenuFilters').val() == 'milestonelist') {
                        //showMilestoneList();
                        updateAllProj('proj_' + proj_uniq, proj_uniq, 'dashboard', '0', proj_name);
                    } else if ($('#caseMenuFilters').val() == 'kanban') {
                        easycase.showKanbanTaskList();
                    } else {
                        if (PAGE_NAME != 'dashboard') {
                            window.location.href = HTTP_ROOT + 'dashboard#milestone';
                        } else {
                            ManageMilestoneList();
                        }
                    }
                    $('#inner_mlstn #btn_mlstn').show();
                    $('#inner_mlstn #ldr').hide();
                    closePopup();
                }
            });
        }
    }
    function fetch_select() {
        var pro_id = $("#project_id").val();
        if (pro_id != "") {
            var url = HTTP_ROOT + 'milestones/user_data';
            $.ajax({
                type: 'post',
                url: url,
                data: {
                    project_id: pro_id
                },
                success: function (response) {
                    $("#ass_id").html(response);
                }
            });
        }
    }
</script>