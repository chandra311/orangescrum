<?php

//echo "<pre>";
//print_r($prjlist);exit;
if($_COOKIE['utilization_status_filter'] != '' && $_COOKIE['utilization_status_filter'] != 'all'){
$Asnbers = explode("-", $_COOKIE['utilization_project_filter']);
}
$m=0;
if(isset($status_lists))
{
	$m=0;
	$totAsnCase = 0;
	$h = 0;
	foreach($status_lists as $ks => $vs)
	{
		$m++;
		$stsId = $ks;
		//$AsnUniqId = $Asn['User']['uniq_id'];
		$stsName = $vs;
		//if($m > 5){$h++;
		?>
        <li class="li_check_radio" <?php if($m > 5){$h++;?> id="hidsts_<?php echo $h; ?>" style="display:none;" <?php }?>>
            <div class="checkbox">
                <label>
                    <input type="checkbox" class="utilization-status" id="stsid_<?php echo $stsId; ?>" onClick="utilization_status('<?php echo $stsId; ?>','check');"  data-id="<?php echo $stsId; ?>" <?php if (in_array($stsId, $Asnbers)) { echo "checked"; } ?> />
                    &nbsp;<span title="<?php echo $stsName; ?>"><?php echo $this->Format->shortLength($stsName,15); ?></span>
                    <input type="hidden" name="stsids_<?php echo $stsId; ?>" id="stsids_<?php echo $stsId; ?>" value="<?php echo $stsId; ?>" readonly="true">
                </label>
            </div>
        </li>
<?php }
if($h != 0)
	{
	?>
	<div class="slide_menu_div1 more-hide-div">
		<div class="more" align="right" id="sts_more" >
			<a href="javascript:jsVoid();" onClick="moreLeftNav('sts_more','sts_hide','<?php echo $h; ?>','hidsts_',event)">more...</a>
		</div>
		<div class="more" align="right" id="sts_hide" style="display:none;">
			<a href="javascript:jsVoid();" onClick="hideLeftNav('sts_more','sts_hide','<?php echo $h; ?>','hidsts_',event)">hide...</a>
		</div>
	</div>
	<?php
	} ?>
<?php } ?>