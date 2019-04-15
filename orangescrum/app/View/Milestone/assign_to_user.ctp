<?php echo $this->Form->create('Milestone', array('id' => 'addmilestone' )); ?>
<div class="data-scroll">
    <table cellpadding="0" cellspacing="0" class="col-lg-12">
	<tr>
	    <td class="v-top"><?php echo __("Project"); ?>:</td>
	    <td style="text-align: left;">
    			<input type="hidden" name="data[Milestone][project_id]" id="project_id" value="<?php echo $milearr['Milestone']['project_id']; ?>" data-pname="<?php echo $projArr[0]['Project']['name']; ?>" data-puniq="<?php echo $projArr[0]['Project']['uniq_id']; ?>">
			    <b><?php echo $this->Format->formatText($projArr[0]['Project']['name']); ?></b>
	    </td>
	</tr>
        <tr style="display: none">
	    <td><span class="fnt_clr_rd">* </span><?php echo __("Title"); ?>:</td>
	    <td>
		<?php echo $this->Form->text('title', array('class' => 'form-control', 'id' => 'title', 'maxlength' => '100', 'value' => $milearr['Milestone']['title'])); ?>
		<?php echo $this->Form->hidden('user_id', array('id' => 'user_id','value' => SES_ID)); ?>
		<?php echo $this->Form->hidden('id', array('id' => 'id', 'value' => $milearr['Milestone']['id'])); ?>
		<?php echo $this->Form->hidden('urlname', array('id' => 'urlname_assin_user')); ?>
	    </td>
	</tr>
	<tr>
	    <td><?php echo __("User"); ?>:</td>
	    <td>
		<?php echo $this->Form->input('assign_to', array('class' => 'form-control', 'id' => 'title', 'maxlength' => '100','type'=>'select','label'=>false,'empty'=>'Choose One','options'=>$user_list,'value'=>!empty($milearr['Milestone']['assign_id'])?$milearr['Milestone']['assign_id']:'')); ?>
	    </td>
	</tr>
	
	
	
	<tr>
		<td></td>
		<td>
			<div class="fl">
				<span id="ldr1" style="display:none;">
				<img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..." />
				</span>
				<span id="btn_mlstn">
					<button onclick="showButton()" type="submit" value="Update" name="milestone" id="milestone" class="btn btn_blue" ><i class="icon-big-tick"></i><?php if (!empty($edit)) { echo __("Save");}else {echo __("Add");} ?></button>
				<!--<button class="btn btn_grey reset_btn" type="button" name="cancel" onclick="closePopup();" ><i class="icon-big-cross"></i>Cancel</button>-->
                 <span class="or_cancel"><?php echo __('or'); ?>
                    <a onclick="closePopup();"><?php echo __("Cancel"); ?></a>
                </span>
				</span>
			</div>
		</td>
	</tr>
    </table>
</div>

<input type="hidden" value="<?php echo $mlstfrom;?>" id="milestone_crted_from"/>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
    function showButton(){
        $("#btn_mlstn").hide();
        $("#ldr1").show();
    }
    </script>
