<?php if(defined('INV') && INV == 1){ 
    #echo $this->Html->script('scripts/jquery.imgareaselect.pack.js',array('block' => 'scriptBottom')); ?>
	<script type="text/javascript" src="<?php echo HTTP_ROOT; ?>js/scripts/jquery.imgareaselect.pack.js"></script>
<?php } ?>
<div class="user_profile_con thwidth">
<!--Tabs section starts -->
    <?php echo $this->element("company_settings");?>
    
    <?php 
     echo $this->Form->create('Company',array('url'=>'/users/mycompany','onsubmit'=>'return submitCompany()','enctype'=>'multipart/form-data')); ?>
<table cellspacing="0" cellpadding="0" class="col-lg-5" style="text-align:left;">
    <tbody>
        <tr>
            <th><?php echo __('Name');?>:</th>
            <td>
		<?php echo $this->Form->text('name',array('value'=>htmlspecialchars_decode($getCompany['Company']['name']),'class'=>'form-control','id'=>'cmpname','autocomplete'=>'off')); ?>
	    </td>
        </tr>
        <?php if(defined('GTLG') && GTLG == 1){ ?>
        <tr>
            <th><?php echo __("Work Hour per Day"); ?>:</th>
            <td>
				<?php echo $this->Form->input('work_hour',array('type' => 'number', 'div' => false, 'label' => false, 'min' => 0, 'max' => 24, 'step' => '0.01', 'value'=>$getCompany['Company']['work_hour'],'class'=>'fl form-control','id'=>'cmpwrkhr', 'maxlength' => 5, 'autocomplete'=>'off')); ?>
			</td>
        </tr>
        <?php } ?>
		<?php if(defined('MAPI') && MAPI == 1){ ?>
		<tr>
            <th><?php echo __("API Access Token"); ?>:</th>
            <td>
				<?php echo $this->Form->text('api_access_code',array('value'=>$getCompany['Company']['api_access_code'],'class'=>'fl form-control','id'=>'cmpapicode', 'maxlength' => 8, 'readonly' => true, 'style' =>'width:48%;margin-right:20px', 'autocomplete'=>'off')); ?>
				<?php #if(!isset($getCompany['Company']['api_access_code']) || $getCompany['Company']['api_access_code'] == ''){ ?>
					<a href="javascript:void(0);" class="btn btn_blue fl" onclick="generateapicode();" style="margin-right:0"><?php echo $getCompany['Company']['api_access_code'] == '' ? __("Generate"): __("Re-generate"); ?></a>
				<?php #} ?>
				<div class="cb"></div>
			</td>
        </tr>
		<?php } ?>
        <?php if(defined('INV') && INV == 1){ ?>
            <tr>
        <th style="vertical-align:top;"><?php echo __('Logo');?>:</th>
            <td>
        <div id="profDiv"></div>
        <?php
        if(defined('USE_S3') && USE_S3) {
            if($this->Format->pub_file_exists(DIR_USER_PHOTOS_S3_FOLDER, $userdata['Company']['logo'])) {
                $user_img_exists = 1;
            }
        } elseif($this->Format->imageExists(DIR_USER_PHOTOS,$getCompany['Company']['logo'])){
            $user_img_exists = 1;
        }
        if($user_img_exists) { ?>
            <div id="existProfImg" onmouseover="showEditDeleteImg()" onmouseout="hideEditDeleteImg()">
            <?php if(defined('USE_S3') && USE_S3) {
                $fileurl = $this->Format->generateTemporaryURL(DIR_USER_PHOTOS_S3 . $userdata['Company']['logo']);
            } else {
                $fileurl = HTTP_ROOT.'users/files/photos/'.$getCompany['Company']['logo'];
            } ?>
                <div>
                <a href="<?php echo $fileurl; ?>" target="_blank" id="loc_img">
                    <img src="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<?php echo $getCompany['Company']['logo']; ?>&sizex=100&sizey=100&quality=100" border="0" id="profphoto"/>
                </a>
                <?php echo $this->Form->hidden('photo', array('class' => 'text_field', 'id' => 'imgName1', 'name' => 'data[Company][logo]')); ?>
                <?php echo $this->Form->hidden('exst_photo', array('value' => $userdata['Company']['logo'], 'class' => 'text_field', 'name' => 'data[Company][exst_logo]')); ?>
                </div>
            <div style="display:none" id="editDeleteImg">
                <div id="uploadImgLnk">
                <a title="Edit Profile Image" href="javascript:void(0);" onClick="openProfilePopup('company_logo')">
                <div><img src="<?php echo HTTP_IMAGES; ?>images/edit_reply.png" border="0" class="ed_del"></div>
                </a>
            </div>
                <a title="Delete Profile Image" href="<?php echo HTTP_ROOT; ?>users/mycompany/<?php echo urlencode($getCompany['Company']['logo']); ?>">
                <div onclick="return confirm('<?php echo __("Are you sure you want to delete");?>?')" ><img src="<?php echo HTTP_IMAGES; ?>images/delete.png" border="0" class="ed_del"></div>
            </a>
                </div>
                <div class="cb"></div>
            </div>
        <?php } else { ?>
            <div id="defaultUserImg" style="float:left;">
            <a href="javascript:void(0);" onClick="openProfilePopup('company_logo')" >
            <img width="100" height="100" src="<?php echo HTTP_ROOT; ?>img/default-invoice-logo.png">
            </a>
            </div>
        <div id="uploadImgLnk" class="fl" style="margin-top:20px;margin-left:5px;">                                 
                <a href="javascript:void(0);" onClick="openProfilePopup('company_logo')" ><?php echo __('Choose Company Logo');?></a>
            </div>
            <input type="hidden" id="imgName1" name="data[Company][logo]" />
        <?php } ?>
	    </td>
        </tr>
        <?php } ?>
	    <th></th>
            <td class="btn_align">
            	<span id="subprof1">
		<input type="hidden" name="data[User][changepass]" id="changepass" readonly="true" value="0"/>
		<button type="submit" value="Update" name="submit_Pass"  id="submit_Pass" class="btn btn_blue"><i class="icon-big-tick"></i><?php echo __("Update"); ?></button>
		<!--<button type="button" class="btn btn_grey" onclick="cancelProfile('<?php echo $referer;?>');"><i class="icon-big-cross"></i>Cancel</button>-->
         <span class="or_cancel">or
            <a onclick="cancelProfile('<?php echo $referer;?>');"><?php echo __("Cancel");?></a>
        </span>
		</span>
		<span id="subprof2" style="display:none">
		    <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="<?php echo __("Loading"); ?>..." />
		</span>
            </td>
        </tr>						
    </tbody>
</table>
<?php echo $this->Form->end(); ?>

<div class="cbt"></div>
</div>
<style>
.thwidth table th {
    width: 152px;
}
</style>
<?php if(defined('INV') && INV == 1){ ?>
<?php echo $this->Html->script(array('ajaxfileupload'));?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var img_path = $('#loc_img').attr('href');
    if(img_path){
        var n = img_path.lastIndexOf('/');
        var result = img_path.substring(n + 1);
        if(result){
            $('#imgName1').val(result);
        }
    }
    $('#cmpwrkhrs').on('keyup', function(){
        if($(this).val() > 24){
            $(this).css('border','1px solid red');
        }else{
            //$(this).css('border','1px solid red');
        }
    });
});
    function showPreviewImage(imgId) {
        $('#loader').show();
        var imgName = $('#'+imgId).val();
        prevUrl = "<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'companyLogo')) ?>";
        $.ajaxFileUpload({
            url: prevUrl,
            secureuri: false,
            fileElementId: imgId,
            dataType: 'json',
            complete: function(data, status) {
                var data = $.parseJSON(data.responseText);
                if(data.msg == "exceeds"){
                        showTopErrSucc('error',"File size exceeds 2MB.");
                        return false;
                }
                $('#loader').hide();
                var url = (data.url).replace(/&amp;/g, '&');
                if (data.success == 'yes') {
                    $('#exst_logo').val(data.msg);
                    $("#company_logo").attr('src', url);
                } else {
                    showTopErrSucc('error',data.msg);
                }
            }
        });
    }
    function deleteCompanyLogo(){
        if(confirm("<?php echo __('Are you sure to delete logo');?>?")){
            $.ajax({
                url:"<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'deleteCompanyLogo')) ?>",
                method:"post",
                dataType:"json",
                success:function(response){
                    if(response.success == 'Yes'){
                        showTopErrSucc('success',response.msg);
                        $("#company_logo").attr('src', $('#exst_logo').attr('data-dflt'));
                    }else{
                        showTopErrSucc('error',response.msg);
                    }
                }
            });
        }
    }
</script>
<?php } ?>
<?php if(defined('MAPI') && MAPI == 1){ ?>
<script type="text/javascript">
	function randomString(length, chars) {
		var mask = '';
		if (chars.indexOf('a') > -1) mask += 'abcdefghijklmnopqrstuvwxyz';
		if (chars.indexOf('A') > -1) mask += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if (chars.indexOf('#') > -1) mask += '0123456789';
		var result = '';
		for (var i = length; i > 0; --i) result += mask[Math.round(Math.random() * (mask.length - 1))];
		return result;
	}
	function generateapicode(){
		$('#cmpapicode').val(randomString(8, 'aA#'));
	}
</script>
<?php } ?>