<?php
if ($nodata_name == 'caselist') {
    $imageClass = 'icon-no-archive';
    $msgHead = __('No tasks have been archived yet on this project', true);
    $msgDesc = __('All archived tasks of this project will appear here', true);
} else if ($nodata_name == 'filelist') {
    $imageClass = 'icon-no-archive';
    $msgHead = __('No files have been archived yet on this project', true);
    $msgDesc = __('All archived files of this project will appear here', true);
} else if ($nodata_name == 'activity') {
    $imageClass = 'icon-no-activity';
    $msgHead = __('No task activities on this project', true);
    $msgDesc = __('All activities of this project will appear here', true);
} else if ($nodata_name == 'files') {
    $imageClass = 'icon-no-files';
    $msgHead = __('No files have been shared or uploaded on this project', true);
    $msgDesc = __('All files shared on this project will appear here', true);
} else if ($nodata_name == 'files-search') {
    $imageClass = 'icon-no-files';
    $msgHead = __('No files found', true);
    $msgDesc = '';
} else if ($nodata_name == 'milestonelist') {
    $imageClass = 'icon-no-milestone';
    //$msgHead = 'No milestone have been created on this project';
    //$msgDesc = 'All milestone created on this project will appear here';
	$msgHead = __('No milestone', true);
    $msgDesc = '';
}else if ($nodata_name == 'tasklist') {
	$imageClass = 'icon-no-task';
	if($case_type=='overdue'){
		$msgHead = __('No Overdue Task on this project', true);
	}elseif($case_type=='highpriority'){
		$msgHead = __('No High Priority Task have been created on this project', true);
	}elseif($case_type=='assigntome'){
		$msgHead = __('No Task for me on this project', true);
	}elseif($case_type=='delegateto'){
		$msgHead = __('No Task delegated on this project', true);
	}else{
		$msgHead = __('No Task have been created on this project', true);
	}
    $msgDesc = __('All Task created on this project will appear here', true);
}
?>
<div class="fl col-lg-12 not-fonud ml_not_found">
	<div class="icon_con <?php echo $imageClass;?>"></div>
	<h2><?php echo $msgHead; ?></h2>
	<div><?php echo $msgDesc; ?></div>
<?php if ($nodata_name == 'milestonelist') {?>
	<div style="padding-top:10px;">
		<button class="btn btn_blue" value="Add" type="button" onclick="addEditMilestone(this);" style="margin:0;">
			<?php echo __("Create Milestone"); ?>
		</button>
	</div>
<?php }?>
</div>