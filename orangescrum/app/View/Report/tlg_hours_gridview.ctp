<div class="hr_spent_div">
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
		<td align="left" colspan="2">
			<table border="0" cellpadding="0" width=100% class="hr_spent_tbl">
				<tr class="hr_spent_row">
					<td align="left" class="tophead_first">
						<?php echo __("Name"); ?>
					</td>
					<td align="right" class="tophead">
						<?php echo __("Replies"); ?>
					</td>
					<td align="right" class="tophead">
                                          <?php if(isset($status_name) && !empty($status_name)){
                                                    echo __($status_name, true) ;
                                                } else{
                                                    echo __("Resolved") ;
                                                }?>
					</td>
					<td align="right" class="tophead">
						<?php echo __("Hours Spent"); ?>
					</td>
				</tr>
				<?php
				if(!empty($users)) {
					$count=0; $clas = "";
					$thrs = array();
					$mnhrs = array();
					foreach($users as $k=>$v){
						$user_id = $v['User']['id'];
						$count++;
						$thrs[] = $loglist[$user_id];
						if(isset($mainhrarr[$v['e']['user_id']])){
							$mnhrs[] = $mainhrarr[$v['e']['user_id']];
						}
						if($count %2==0) { $clas = "row_col"; }
						else { $clas = "row_col_alt"; }
						?>
						 <tr class="<?php echo $clas?>" id="userlist<?php echo $count;?>" <?php if($prjAllArr['Project']['isactive'] == 2) { ?> style="background-color:#FEE2E2;" <?php } ?>>	
                                        <td class="hr_spent_row"><?php echo $v['User']['name'];?></td>
                                        <td align="right" class="hr_spent_row_lower">
                                            <?php echo (isset($replylist[$user_id])) ? $replylist[$user_id] : 0 ;?>
                                        </td>
                                        <td align="right" class="hr_spent_row">                                                            
                                            <?php echo (isset($resarr[$user_id])) ? $resarr[$user_id] : 0 ;?>
                                        </td>
                                        <td align="right" class="hr_spent_row" style="font-weight:bold;">
                                            <?php  echo (isset($loglist[$user_id])) ? $this->format->format_time_hr_min($loglist[$user_id]) : '0' ; ?>
                                        </td>
                                </tr>	
					<?php }  ?>
						<input type="hidden" id="thrs" value="<?php echo $this->format->format_time_hr_min(array_sum($thrs) + array_sum($mnhrs)); ?>" />	
			<?php	}else{  ?>
					<tr>
						<td class="no_match_td" colspan="4" align="center"><?php echo __("No Results Found"); ?></td>
					</tr>
				<?php }?>
	</table>
	</td></tr>
</table>
</div>
