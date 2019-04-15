<?php if($cnt != 0) { ?>
<div class="fl"> <?php echo __("Total");?> <strong><?php echo $cnt; ?></strong> <?php echo __("Tasks created."); ?></div>
<div class="cb"></div>
<div>
	<ul>
        <?php if(isset($avg_resolved) && !empty($avg_resolved)) { ?>
            <li><?php echo __("Avg. days to Resolve a Task"); ?>: <strong><?php echo round($avg_resolved); ?></strong></li>
        <?php } ?>
		<li><?php echo __("Avg. days to Close a Task"); ?>: <strong><?php echo round($avg_closed); ?></strong></li>
		<li><?php echo __("Hours spent on these Tasks"); ?>: <strong><?php echo $tot_hrs; ?></strong></li>
	</ul>
</div>
<?php }else{ ?>
<div class="fl"><font color='red' size='2px'><?php echo __("No data for this date range & project."); ?></font></div>
<?php } ?> 