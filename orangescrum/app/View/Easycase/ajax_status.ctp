<?php
if(isset($query_All)){
	$CookieStatus = (string)$CookieStatus;
	if(isset($pageload) && $pageload == 0){
	    $default = 1;
	    if(strstr($CookieStatus,"1") || strstr($CookieStatus,"2") || strstr($CookieStatus,"3") || strstr($CookieStatus,"4") || strstr($CookieStatus,"5") || strstr($CookieStatus,"attch") || strstr($CookieStatus,"upd")){
		$default = 1;
	    }
	    if(!$CookieStatus || $CookieStatus == "all"){
		$default = 0;
	    }
	} else {
	    $CookieStatus = "all";
	    $default = 0;
	}
	$disabled = "";
	if($_COOKIE['CURRENT_FILTER'] == 'closecase') {
	    $disabled = "disabled='disabled'";
	}
	?>
	
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_all" <?php if($default == 0) { echo "checked"; } ?> style="cursor:pointer" onClick="checkboxStatus('status_all','check');filterRequest('status');" <?php echo $disabled; ?>/>
		<font <?php if(!$disabled) { ?> onClick="checkboxStatus('status_all','check');filterRequest('status');" <?php } ?> style="font-weight:bold;cursor:pointer">&nbsp;<?php echo __("All"); ?> (<?php echo $query_All; ?>)</font>
	    </a>
	</li>
    <?php if(isset($status) && !empty($status)){ 
        foreach ($status['Status'] as $k=>$sts){ ?>
    
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_<?php echo $sts['name']?>" <?php if(strstr($CookieStatus,$sts['id'])) { echo "checked"; } ?> style="cursor:pointer" data-val="<?php echo $sts['id']; ?>" onClick="checkboxStatus('status_<?php echo $sts['name']?>','check', <?php echo $sts['id']; ?>);filterRequest('status');" <?php echo $disabled; ?>/>
		<font <?php if(!$disabled) { ?> onClick="checkboxStatus('status_<?php echo $sts['name']?>','text',<?php echo $sts['id']; ?>);filterRequest('status');" <?php } ?> style="color:<?php echo $sts['color']?>;cursor:pointer">&nbsp;<?php echo $sts['name']?> (<?php echo $resCaseWidget[$sts['name']]['count']; ?>)</font>
	    </a>
	</li>
    <?php }
        } else{  ?>
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_new" <?php if(strstr($CookieStatus,"1")) { echo "checked"; } ?> style="cursor:pointer" data-val="1" onClick="checkboxStatus('status_new','check', 1);filterRequest('status');" <?php echo $disabled; ?>/>
		<span <?php if(!$disabled) { ?> onClick="checkboxStatus('status_new','text',1);filterRequest('status');" <?php } ?> style="color:#7f4240;cursor:pointer">&nbsp;New (<?php echo isset($resCaseWidget['New']['count']) ?  $resCaseWidget['New']['count'] :  0 ; ?>)</span>
	    </a>
	</li>
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_open" <?php if(strstr($CookieStatus,"2")) { echo "checked"; } ?> style="cursor:pointer" data-val="2" onClick="checkboxStatus('status_open','check', 2);filterRequest('status');" <?php echo $disabled; ?>/>
		<span <?php if(!$disabled) { ?> onClick="checkboxStatus('status_open','text',2);filterRequest('status');" <?php } ?> style="color:#04407C;cursor:pointer">&nbsp;In Progress (<?php echo isset($resCaseWidget['In Progress']['count']) ?  $resCaseWidget['In Progress']['count'] :  0 ; ?>)</span>
	    </a>
	</li>
	
	<?php /*?><div class="slide_menu_div1">
		<input type="checkbox" id="status_start" <?php if(strstr($CookieStatus,"4")) { echo "checked"; } ?> style="cursor:pointer" onClick="checkboxStatus('status_start','check');filterRequest('status');" <?php echo $disabled; ?>/>
		<span <?php if(!$disabled) { ?> onClick="checkboxStatus('status_start','text');filterRequest('status');" <?php } ?> style="color:#55A0C7;cursor:pointer">&nbsp;Started (<?php echo $query_Start; ?>)</span>
	</div><?php */?>
	
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_resolve" <?php if(strstr($CookieStatus,"5")) { echo "checked"; } ?> style="cursor:pointer" data-val="5" onClick="checkboxStatus('status_resolve','check', 5);filterRequest('status');" <?php echo $disabled; ?>/>
		<span <?php if(!$disabled) { ?> onClick="checkboxStatus('status_resolve','text',5);filterRequest('status');" <?php } ?> style="color:#EF6807;cursor:pointer">&nbsp;Resolved (<?php echo isset($resCaseWidget['Resolved']['count']) ?  $resCaseWidget['Resolved']['count'] :  0 ; ?>)</span>
	    </a>
	</li>
    <li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_close" <?php if(strstr($CookieStatus,"3")) { echo "checked"; } ?> style="cursor:pointer" data-val="3" onClick="checkboxStatus('status_close','check', 3);filterRequest('status');" <?php echo $disabled; ?>/>
		<font <?php if(!$disabled) { ?> onClick="checkboxStatus('status_close','text',3);filterRequest('status');" <?php } ?> style="color:#048404;cursor:pointer">&nbsp;Closed (<?php  echo isset($resCaseWidget['Closed']['count']) ?  $resCaseWidget['Closed']['count'] :  0 ; ?>)</font>
	    </a>
	</li>
    <?php } ?>
	<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_file" <?php if(strstr($CookieStatus,"attch")) { echo "checked"; } ?> style="cursor:pointer" data-val ="attch" onClick="checkboxStatus('status_file','check', 'attch');filterRequest('status');"/>
		<span onClick="checkboxStatus('status_file','text');filterRequest('status');" style="color:#3284C4;cursor:pointer">&nbsp;Files (<?php echo $query_Attch; ?>)</span>
	    </a>
	</li>
	
	<?php /*<li>
	    <a href="javascript:void(0);">
		<input type="checkbox" id="status_upd" <?php if(strstr($CookieStatus,"upd")) { echo "checked"; } ?> data-val ="upd" style="cursor:pointer" onClick="checkboxStatus('status_upd','check', 'upd');filterRequest('status');" <?php echo $disabled; ?>/>
		<span <?php if(!$disabled) { ?> onClick="checkboxStatus('status_upd','text');filterRequest('status');" <?php } ?> style="color:#000000;cursor:pointer">&nbsp;Updates (<?php echo $query_Upd; ?>)</span>
	    </a>
	</li> */ ?>
<?php
}
?>
