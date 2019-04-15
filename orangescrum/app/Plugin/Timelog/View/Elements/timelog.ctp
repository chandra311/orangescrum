<?php if(isset($resCaseProj)){ 
        $rsrchtml = "<option value=''>Select Resource</option>";
	foreach($resCaseProj as $k=>$v){
		$rsrchtml .= "<option value='".$v['User']['id']."'>".$v['User']['name']." ".$v['User']['last_name']."</option>";
	} ?>
	<script type="text/javascript">
		var rsrch = "<?php echo $rsrchtml; ?>";
	</script>
	
<?php } ?>
<div class="timelog-table-head">
<div>
<div class="fl">
<span class="time-log-head">Time Log</span>
<div class="spent-time">
<div class="fl">
<span class="total use-time">Total: </span>
<span class="use-time">Logged:</span>
<span><?php echo $thrs; ?></span>
</div>
<div class="fl" style="margin:0px 20px 0px 20px;">
<span class="use-time">Billable:</span>
<span><?php echo $thoursbillable; ?></span>
</div>
<div class="fl">
<span class="use-time">Estimated:</span>
<span><?php echo $cntestmhrs; ?></span>
</div>
<div class="cb"></div>
</div>
</div>
<div class="fr">
<div class="logmore-btn">
<a href="javascript:void(0);" onclick="createlog('0');">Log more time
<span class="sprite btn-clock"></span>
</a>
</div>
</div>
<div class="cb"></div>
</div>

</div>
<div class="timelog-detail-tbl">
<table cellpadding="3" cellspacing="4">
<tr>
<th>Date</th>
<th>Name</th>
<th>Task</th>
<th>Description</th>
<th>Type</th>
<th>Start</th>
<th>End</th>
<th>Billable</th>
<th>Invoiced</th>
<th>Duration</th>
</tr>
<?php if (!empty($logtimes)) { ?>
<?php foreach ($logtimes as $log) { ?>
<tr>
<td><?php echo date('M d, Y',strtotime($log['LogTime']['task_date'])); ?></td>
<td><?php $name = $this->Format->getUserDtls($log['LogTime']['user_id']);echo $name['User']['name']; ?></td>
<?php $tsks = $this->Format->getTaskdetails($log['LogTime']['project_id'],$log['LogTime']['task_id']); ?>
<td title="<?php echo $tsks['Easycase']['title']; ?>" rel="tooltip"><?php echo $this->Format->frmtdata($tsks['Easycase']['title'],0,20); ?></td>
<td <?php if(!empty($log['LogTime']['description'])) { ?>rel="tooltip" <?php } ?> title="<?php echo $log['LogTime']['description']; ?>" ><?php echo $this->Format->frmtdata($log['LogTime']['description'],0,20); ?></td>
<!--<td><?php //$tsktyp = $this->Format->getTaskType($tsks['Easycase']['type_id']);echo $tsktyp['Type']['name']; ?></td>-->
<td><?php echo $this->Format->chngdttime($log['LogTime']['task_date'],$log['LogTime']['start_time']); ?> </td>
<td><?php echo $this->Format->chngdttime($log['LogTime']['task_date'],$log['LogTime']['end_time']); ?></td>
<td align="center"><span <?php if($log['LogTime']['is_billable']){ ?> class="sprite yes" <?php } else { ?> class="sprite no" <?php } ?> ></span></td>
<td>
<span class="fl"><?php $hrs = floor($log['LogTime']['total_hours']/3600)." hrs ".(($log['LogTime']['total_hours']%3600)/60)." min"; echo $hrs; ?></span>
</td>
</tr>
<?php } ?>
<?php }else{ ?>
	<tr>
<td colspan="10">No records......</td>
</tr>
<?php } ?>
</table>
</div>
<div>
<div class="fl crt-task">
<span><!-- Log time for this task --></span>
</div>
<div class="fr ht_log">
<!--
<span class="fl">Hide time log</span>
<a href=""><span class="fl sprite up-btn"></span></a>
-->
</div>
<div class="cb"></div>
</div>
