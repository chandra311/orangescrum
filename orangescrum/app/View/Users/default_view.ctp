<div class="user_profile_con profileth">
<!--Tabs section starts -->
    <?php echo $this->element("personal_settings");?>
<!--Tabs section ends -->
<!--<div style="margin-top:20px;margin-bottom:0px;margin-left:22px;">
	<h2 style="margin-bottom:7px;">Personal Info</h2>
	<hr style="margin-top:0px;background:grey;"/>
</div> -->
<?php echo $this->Form->create('DefaultView', array('url' => '/users/saveDefaultView', 'onsubmit' => 'return validateDefaultViewForm()', 'name' => 'defaultviewform')); ?>
<input name="default_view_id" type="hidden" value="<?php echo $id; ?>" />
<table cellspacing="0" cellpadding="0" class="col-lg-5" style="text-align:left;">
    <tbody>
        <tr>
            <th><?php echo __("Task"); ?>:</th>
            <td>
		 <?php echo $this->Form->select('taskviews', $task_views, array('value' => $taskview, 'class' => 'select form-control floating-label', 'data-dynamic-opts' => 'true', 'empty' => 'Choose One')); ?>
	    </td>
        </tr>
        <tr>
            <th><?php echo __("Milesone"); ?>:</th>
            <td>
		<?php echo $this->Form->select('milestoneviews', $milestone_views, array('value' => $milestoneview, 'class' => 'select form-control floating-label ',  'data-dynamic-opts' => 'true', 'empty' => 'Choose One')); ?>
	    </td>
        </tr>
		<tr>
            <th><?php echo __("Project"); ?>:</th>
        <td>
                <?php echo $this->Form->select('projectview', $project_views, array('value' => $projectview, 'class' => 'select form-control floating-label ',  'data-dynamic-opts' => 'true', 'empty' => 'Choose One')); ?>
	    </td>
                </tr>
        <tr>
	    <th></th>
            <td class="btn_align">
		<span id="subprof1">
		    <button type="submit" value="Update" name="submit_Profile"  id="submit_Profile" class="btn btn_blue"><i class="icon-big-tick"></i><?php echo __("Update"); ?></button>
		    <!--<button type="button" class="btn btn_grey" onclick="cancelProfile('<?php echo $referer;?>');"><i class="icon-big-cross"></i>Cancel</button>-->
			<span class="or_cancel"><?php echo __("or"); ?>
				<a onclick="cancelProfile('<?php echo $referer;?>');"><?php echo __("Cancel"); ?></a>
			</span>
		    <!--<a href="<?php //echo $referer; ?>">Cancel</a>-->
		</span>
		<span id="subprof2" style="display:none">
		    <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="<?php echo __("Loading"); ?>..." />
		</span>
            </td>
        </tr>						
    </tbody>
</table>
<?php echo $this->Form->end(); ?>

<div class="cbt"></div>
</div>
