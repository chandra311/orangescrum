<style>
    #holder { border: 4px dashed #F8F81E;padding: 8px;height:85px;background: #F0F0F0;}
    #holder.hover { border: 4px dashed #0c0; }
    .isRecurring{width:65px;position:absolute;top:500px;right:30%}
    .isRecurring .repeat{margin-top:3px}
    .recurring-block{position:absolute;top:470px;right:3%;background: #f6f7f9 none repeat scroll 0 0;border: 1px solid #ccc;padding: 10px 10px 0px 10px;color:#5A5A5A;}
    .starts-repeat{margin-left:10px;}
    .group-ele.f-select{width:230px;margin-left:8px;}
	.group-ele.n-text{width:740px;margin-right:0px;height:auto;}
	.group-ele{width:210px;margin-bottom:7px;}
	.group-ele .form-control{height:24px;padding:3px 5px}
	.group-ele .form-control.w105{width:93px;}
	.group-ele.w200{width:auto;}
	.noEnd{width:260px;}
    .starts-repeat{margin-left:10px;}
    .form-control[disabled] + .calendar-img {cursor: not-allowed;}
    .calendar-img {position: absolute;right: 5px;top: 1px;}
    input.hasDatepicker[readonly][disabled] {background: #eeeeee none repeat scroll 0 0;}
</style>
<input type="hidden" name="totfiles" id="totfiles" value="0" readonly="true">
<input type="hidden" id="is_default_task_type" value="<?php echo $GLOBALS['TYPE_DEFAULT'];?>" readonly="true">
<input type="hidden" id="CS_type_id" value="<?php if(isset($taskdetails) && $taskdetails['type_id']){echo $taskdetails['type_id'];}else{if (isset($GLOBALS['TYPE_DEFAULT']) && $GLOBALS['TYPE_DEFAULT']==1) {echo (isset($GLOBALS['TYPE'][0]['Type']['id']) && $GLOBALS['TYPE'][0]['Type']['id']) ? $GLOBALS['TYPE'][0]['Type']['id'] : $GLOBALS['TYPE'][1]['Type']['id'];}}?>">
<input type="hidden" id="CS_priority" value="<?php if(isset($taskdetails) && $taskdetails['priority']){echo $taskdetails['priority'];}else{?>1 <?php }?>">
<input type="hidden" id="CS_due_date" value="<?php if(isset($taskdetails) && $taskdetails['due_date']){echo date('m/d/Y',strtotime($taskdetails['due_date']));}else{?>No Due Date<?php }?>">
<input type="hidden" id="CS_start_date" value="<?php if(isset($taskdetails) && $taskdetails['gantt_start_date']){echo date('m/d/Y',strtotime($taskdetails['gantt_start_date']));} ?>">
<input type="hidden" id="CS_milestone" value="<?php if(isset($taskdetails) && $taskdetails['milestone_id']){echo $taskdetails['milestone_id'];} ?>">
<input type="hidden" id="CSrepeat_due_date" value='' />
<input type="hidden" id="CSrepeat_start_date" value='' />
<input type="hidden" id="CSrepeat_type" value='' />
<input type="hidden" id="CSrepeat_occurrence" value='' />
<input type="hidden" id="userIds" />
<input type="hidden" id="userNames" />

<div class="head_back"></div>
<div id="cover" class="outer"></div>
<div id="pagefade" class="pagefade" style="z-index:0"></div>
<div>

	<?php 
	if($user_subscription['btprofile_id'] || $user_subscription['is_free'] || $GLOBALS['FREE_SUBSCRIPTION'] == 0) {
		$is_basic_or_free = 0;
	} else {
		$is_basic_or_free = 1;
	}
	if($user_subscription['is_cancel']) {
		$is_basic_or_free = 0;
	}
	?>
	<div class="case_field">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="width:56%;">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="case_fieldprof" >
								<div class="fl lbl-m-wid"><?php echo __("Task Type"); ?>:</div>
							</td>
							<td align="left">
								<div id="sample" class="dropdown option-toggle p-6 fl">
									<div class="opt1" id="opt1">
										<a href="javascript:jsVoid()" onclick="open_more_opt('more_opt');">
											<span id="ctsk_type">
											<?php if(isset($taskdetails) && $taskdetails['type_id']){
												foreach($select as $k=>$v){
													if($v['Type']['id'] == $taskdetails['type_id']){
													    if (trim($v['Type']['short_name']) && file_exists(WWW_ROOT."img/images/types/".$v['Type']['short_name'].".png")) {
														$imgicn = HTTP_IMAGES.'images/types/'.$v['Type']['short_name'].'.png';
													    } else {
														//$imgicn = HTTP_IMAGES.'images/types/default.png';
													    }
													    if (trim($imgicn)){ ?>
														<img class="flag" src="<?php echo $imgicn;?>" alt="type" style="padding-top:3px;"/>&nbsp;<?php echo $v['Type']['name'];?>
													    <?php } else { ?>
														<?php echo __($v['Type']['name'], true);?>
													    <?php } ?>
												<?php break; }
												}
											}else{
											    if (isset($GLOBALS['TYPE_DEFAULT']) && $GLOBALS['TYPE_DEFAULT']==1) {?>
												<?php /* <img class="flag" src="<?php echo HTTP_IMAGES.'images/types/dev.png';?>" alt="type" style="padding-top:3px;"/>&nbsp;Development */ ?>
											    <span style="padding-left:5px;"></span><?php echo __($GLOBALS['TYPE'][0]['Type']['name'], true);?>
											    <?php } else {?>
												<?php }?>
											<?php }?>
											</span> 
											<i class="caret mtop-10 fr"></i>
										</a>
									</div>
									<div class="more_opt" id="more_opt">
										<ul>
											<?php
											foreach($GLOBALS['TYPE'] as $k=>$v){
												foreach($v as $key=>$value){
													foreach($value as $key1=>$result){
														if($key1=='name'&& $key1='short_name'){
                                                            if (trim($value['short_name']) && file_exists(WWW_ROOT."img/images/types/".$value['short_name'].".png")) {
                                                                $im1= $this->Format->todo_typ_src($value['short_name'],$value['name']);
                                                            } else {
                                                                $im1 = '';
                                                                //$im1 = HTTP_IMAGES.'images/types/default.png';
                                                            }
                                                            if (trim($im1)) {
                                                                echo "<li>
                                                                    <a href='javascript:jsVoid()'>
                                                                        <img class='flag' src='".$im1."' alt='' />
                                                                        <span class='value'>".$value['id']."
																		</span>".__($value['name'],true)."
                                                                    </a>
                                                                </li>";
                                                            } else {
                                                                echo "<li>
                                                                    <a href='javascript:jsVoid()'>
                                                                        <span style='padding-left: 27px;'></span>
                                                                        <span class='value'>".$value['id']."
																		</span>".__($value['name'],true)."
                                                                    </a>
                                                                </li>";
                                                            }
                                                        }
                                                    }
												}
											} ?>
										 </ul>
									</div>
								</div>
                                <?php
								if(SES_TYPE == 1 || SES_TYPE == 2 || IS_MODERATOR == 1)
								{
								?>
                                <span style="position:relative;top:4px;"><a href="<?php echo HTTP_ROOT."task-type"; ?>" style="color:#06C;text-decoration:underline;font-size:12px;padding-left:5px;" target="_blank"><?php echo __("Add New"); ?></a></span>
                                <?php
								}
								?>
							</td>
						</tr>
					</table>
				</td>
				<td align="left" id="milestone_td" style="width:45%;">
				<div class="col-lg-12" style="padding-left:0">
					<div class="col-lg-3 lbl-m-wid" style="width:98px;padding-left:0"><?php echo __("Milestone"); ?>:</div>
					<div class="col-lg-6 createtask rht-con">	
						<div class="fl dropdown option-toggle p-6" style="margin-left: 12px;text-align:left">
							<div class="opt1" id="opt8">
								<a href="javascript:jsVoid()" onclick="open_more_opt('more_opt8');">
									<span id="selected_milestone">
										<?php if(isset($taskdetails['milestone']) && $taskdetails['milestone']){
											echo $taskdetails['milestone'];
										 }else{?>
											<?php echo __("No milestone"); ?>
										<?php }?>
									</span>
									<i class="caret mtop-10 fr"></i>
								</a>
							</div>
							<div class="more_opt" id="more_opt8">
								<ul></ul>
							</div>
						</div>
					</div>
					</div>
				</td>
				<?php /*?><td id="milestone_td" align="right">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="case_fieldprof" >
								<span id="hd1">
									<div class="fl lbl-m-wid"><?php echo __("Milestone"); ?>:</div>
								</span>
							</td>
							<td align="right">
								<div id="sample10" class="dropdown option-toggle p-6" style="margin-left:15px;text-align:left">
									<div class="opt1" id="opt8">
										<a href="javascript:jsVoid()" onclick="open_more_opt('more_opt8');">
											<span id="selected_milestone">
												<?php if(isset($taskdetails['milestone']) && $taskdetails['milestone']){
													echo $taskdetails['milestone'];
												 }else{?>
													No milestone
												<?php }?>
											</span>
											<i class="caret mtop-10 fr"></i>
										</a>
									</div>
									<div class="more_opt" id="more_opt8">
										<ul></ul>
									</div>
								</div>
							</td>
						</tr>
					</table>


















				</td><?php */?>
			</tr>
		</table>
	</div>
	<div class="case_field">
		<span>
			<div class="fl lbl-m-wid"><?php echo __("Description"); ?>:</div>
			<div id="divNewCase" class="col-lg-9 createtask fl rht-con">
				<textarea name="data[Easycase][message]" id="CS_message" onfocus="openEditor()" rows="2" style="resize:none" class="form-control" placeholder="<?php echo __('Enter Description...'); ?>"><?php if(isset($taskdetails['message']) && $taskdetails['message']){echo $taskdetails['message']; }?></textarea>
			</div>
			<div id="divNewCaseLoader" style="display:none;padding:20px ;text-align: center;color:#999999;">
				<?php echo __('Loading...'); ?>
			</div>
		</span>
	</div>
	<div class="cb"></div>
	<!--<div class="case_field w-736">
        <?php if(defined('TLG') && TLG != 1){ ?>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td align="left">
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td class="case_fieldprof" >
								<div class="fl lbl-m-wid"><?php echo __('Estimated Hour(s)'); ?>:</div>
							</td>
							<td>
								<div id="sample" class="col-lg-9 createtask fl rht-con">
									<a rel="tooltip" href="javascript:;" original-title="You can enter time as 1.5  (that  mean 1 hour and 30 minutes).">
									<input type="text" onkeypress="return numericDecimal(event)" id="hours" name="data[Easycase][hours]" maxlength="6" class="form-control" style="width:80px;" value="<?php //if(isset($taskdetails['hours']) && $taskdetails['hours']){echo $taskdetails['hours'];}?>"/>
									</a>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
        <?php } ?>
	</div>-->
	<div class="clear"></div>
	<div class="case_field">
		<table border="0" cellpadding="0" cellspacing="0" style="padding-left:2px;" id="table1">
			<tr>
				<td class="case_fieldprof" valign="top">
					<div class="fl lbl-m-wid"><?php echo __("Attachment(s)"); ?>:</div>
				</td>
				<td align="left">
                                    <table cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td>
								<form id="file_upload" action="<?php echo HTTP_ROOT."easycases/fileupload/"; ?>" method="POST" enctype="multipart/form-data">
									<div class="fl" style="margin:10px 0;">
										<div id="holder" style="">
										<div class="customfile-button" style="right:0">
											<input class="customfile-input fl" name="data[Easycase][case_files]" type="file" multiple=""  style="width:233px;height:74px;" title="<?php echo __('No files setelcted'); ?>"/>
											<input name="data[Easycase][usedstorage]" type="hidden" id="usedstorage" value=""/>
											<input name="data[Easycase][allowusage]" type="hidden" id="allowusage" value="<?php echo $user_subscription['storage']; ?>"/>
											<div class="att_fl fl" style="margin-right:5px"></div><div class="fr"><?php echo __("Select multiple files to upload..."); ?></div>
										</div>
                                        <div style="margin-left:4px;color:#F48B02;font-size:13px;" class="fnt999"><?php echo __("Drag and Drop files to Upload"); ?></div>
										<div style="margin-left:6px" class="fnt999"><?php echo __("Max size"); ?> <?php echo MAX_FILE_SIZE; ?> Mb</div>
										</div>									
									</div>
									<?php if(USE_DROPBOX == 1 || USE_GOOGLE == 1){?>
									<div class="fr drive_con drive_con_ipad" style="width:360px;">
                                    	<?php if(USE_DROPBOX == 1) { ?>
										<div class="fr btn-al-mr drive_drop">
											<button type="button" class="customfile-button" onclick="connectDropbox(0,<?php echo USE_DROPBOX;?>);">
												<span class="icon-drop-box"></span>
												<?php echo __("Dropbox"); ?>
											</button>
										</div>
                                        <?php } ?>
                                        <?php if(USE_GOOGLE == 1) { ?>
										<div class="btn-al-mr drive_mgl">
											<button type="button" class="customfile-button" onclick="googleConnect(0,<?php echo USE_GOOGLE;?>);">
												<span class="icon-google-drive"></span>
												<?php echo __("Google Drive"); ?>
											</button>
											<span id="gloader" style="display: none;">
												<img src="<?php echo HTTP_IMAGES;?>images/del.gif" style="position: absolute;bottom: 95px;margin-left: 125px;"/>
											</span>
										</div>
                                        <?php } ?>
									</div>
									<?php }?>
									<div class="cb"></div>
								</form>     
							</td>
						</tr>
						<tr>
							<td>
								<table id="up_files" style="font-weight:normal;width: 100%;"></table>
							</td>	
						</tr>
					</table>
				</td>
			</tr>
			<tr id="drive_tr_0" style="display: none;">
				<td>&nbsp;</td>
				<td>
				<form id="cloud_storage_form_0" name="cloud_storage_form_0"  action="javascript:void(0)" method="POST">
					<div style="float: left;margin-top: 7px;" id="cloud_storage_files_0"></div>
				</form>
				<div style="clear: both;margin-bottom: 3px;"></div>
				</td>
			</tr>
		</table>
	</div>
	<div class="cb"></div>
</div>
<script>
var holder = document.getElementById('holder'),
    tests = {
      dnd: 'draggable' in document.createElement('span')
    };

if (tests.dnd) {
  holder.ondragover = function () { this.className = 'hover'; return false; };
  holder.ondrop = function (e) {
	$('#holder').removeClass('hover');
	if($.trim(e.dataTransfer.files[0].type) === "" || e.dataTransfer.files[0].size === 0) {
	    alert('File "'+e.dataTransfer.files[0].name+'" has no extension!\nPlease upload files with extension.');
	    e.stopPropagation();
	    e.preventDefault();
	}
	return false;
  };
}
$(function(){
    $('#holder').mouseout(function(){
	$('#holder').removeClass('hover');
    });
});
</script>
