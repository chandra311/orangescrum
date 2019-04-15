<?php

//echo "<pre>";
//print_r($list);exit;
if($_COOKIE['utilization_resource_filter'] != '' && $_COOKIE['utilization_resource_filter'] != 'all'){
    $archive_usr_fil = explode("-",$_COOKIE['utilization_resource_filter']);
}
$m=0;
if(isset($list))
{
	$m=0;
	$h = 0;
	foreach($list as $li)
	{
		$m++;
		$userId = $li['User']['id'];
		//$AsnUniqId = $Asn['User']['uniq_id'];
		$ArcName = $li['User']['name']." ".$li['User']['last_name'];
		$Arc_date = date('Y-m-d', strtotime($li['Archive']['dt_created']));
		$shortname =  $li['User']['short_name'];
		//if($m > 5){$h++;
		?>
        <li class="li_check_radio" <?php if($m > 5){$h++;?> id="hiduserid_<?php echo $h; ?>" style="display:none;" <?php }?>>
            <div class="checkbox">
                <label>
                    <input class="utilization-resource" type="checkbox" id="userid_<?php echo $userId; ?>" onClick="utilization_resource('<?php echo $userId; ?>','check');"  data-id="<?php echo $userId; ?>" <?php if (in_array($userId, $archive_usr_fil)) { echo "checked"; } ?>/>
                    &nbsp;<?php echo $this->Format->shortLength($ArcName,15); ?>
                    <input type="hidden" name="userids_<?php echo $userId; ?>" id="userids_<?php echo $userId; ?>" value="<?php echo $userId; ?>" readonly="true">
                </label>
            </div>
        </li>
<?php } 
if($h != 0)
	{
	?>
	<div class="slide_menu_div1 more-hide-div">
		<div class="more" align="right" id="User_more" >
			<a href="javascript:jsVoid();" onClick="moreLeftNav('User_more','User_hide','<?php echo $h; ?>','hiduserid_',event)">more...</a>
		</div>
		<div class="more" align="right" id="User_hide" style="display:none;">
			<a href="javascript:jsVoid();" onClick="hideLeftNav('User_more','User_hide','<?php echo $h; ?>','hiduserid_',event)">hide...</a>
		</div>
	</div>
	<?php
	} ?>
<?php } ?>