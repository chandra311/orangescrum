<tr id="tr_members">
    <td><?php echo __("Members"); ?>: </td>
    <td>
	<div id="div_members">
	    <select name="data[Easycase][members]" class="text_field form-control">
		<option value="all"><?php echo __("All"); ?></option>
		<?php if(isset($memArr)){
		    foreach($memArr as $mem){
			$members = explode("-",$_COOKIE['MEMBERS']); ?>
		<option value="<?php echo $mem['User']['id'];?>" <?php if(in_array($mem['User']['id'], $members)){?>selected="selected"<?php } ?>><?php echo ucfirst($mem['User']['name']);?></option>
		    <?php }
		}
		?>
	    </select>
	</div>
    </td>
</tr>
<tr id="tr_assign_to">
    <td><?php echo __("Assign to"); ?>: </td>
    <td>
	<div id="div_assign_to">
	    <select name="data[Easycase][assign_to]" class="text_field form-control">
		<option value="all"><?php echo __("All"); ?></option>
		<?php if(isset($asnArr)){
		    foreach($asnArr as $Asn){
			$Asnbers = explode("-",$_COOKIE['ASSIGNTO']); ?>
		<option value="<?php echo $Asn['User']['id'];?>" <?php if(in_array($Asn['User']['id'], $Asnbers)){?>selected="selected"<?php } ?>><?php echo ucfirst($Asn['User']['name']);?></option>
		    <?php }
		}
		?>
	    </select>
	</div>
    </td>
</tr>