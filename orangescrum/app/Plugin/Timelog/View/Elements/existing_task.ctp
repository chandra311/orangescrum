<option value="0"> <?php echo $page == 'timer' ? __('Select Task') : '-- '.__('Select').' --'; ?></option>
<?php if(isset($tsklist)) { ?>
<?php foreach($tsklist as $k=>$v) { ?>
	<option value= "<?php echo $k; ?>" ><?php echo strlen($v) > 30 ? $this->Format->shortLength(strip_tags($v), 30):strip_tags($v); ?></option>
<?php  } 
}
?>
