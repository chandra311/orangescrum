<!--[if lt IE 10]>
<style type="text/css">
.lbl_ie{font-family: myriadpro-regular;font-size:18px;margin:0px 20px 5px;color:#636363;display:block}
.login_box{border:1px solid #ccc}
input#txt_Password{font-family: Arial}
</style>
<![endif]-->
<style type="text/css">
@media screen and (max-width:1030px){
.box {left:140px !important;}
}
@media screen and (max-width:850px){
.box {left:25px !important;width:680px !important;padding:16px 10px !important}
.wrapper_new {padding-top:0;}
}
@media screen and (max-width:650px){
.box{display:none !important}
.overlay {z-index:1 !important;background: none !important}
}
@media screen and (max-width:490px){
.wrapper_new,.login-page-wrapper{width:450px;margin:0 auto}
.login_box {padding:0;width:450px;margin:0 auto}
.textbox {width:380px}
.login-btm-img > img{width:100% !important}
/*.box,.get-demo-popup-wrapper{width:400px !important}*/
.get-demo-popup-wrapper > div + div{font-size:15px !important;line-height:22px !important}
}
@media screen and (max-width:370px){
/*.box,.get-demo-popup-wrapper{width:286px !important}*/
.wrapper_new,.login-page-wrapper{width:350px;margin:0 auto}
.login_box {padding:0;width:340px;margin:0 auto}
.textbox {width:270px}
/*.get-demo-popup-wrapper ul li strong{word-wrap:break-word}*/
}
@media screen and (max-width:330px){
/*.box,.get-demo-popup-wrapper{width:235px !important}*/
.wrapper_new,.login-page-wrapper{width:310px;margin:0 auto}
.login_box {width:300px;}
.textbox {width:228px;font-size:20px}
.btn.btn_blue {padding:6px 8px}
}
/* Style for overlay and box */
.btn .btn_blue{
	background-image: -moz-linear-gradient(center top , #43c86f, #2fb45b);
    color: #fff;
    font-family: Verdana,Geneva,sans-serif;
    font-size: 14px;
    margin-right: 10px;
    padding: 6px 27px;
}
.overlay{
	background:transparent url(<?php echo HTTP_ROOT; ?>img/overlay_images/overlay.png) repeat top left;
	position:fixed;
	top:0px;
	bottom:0px;
	left:0px;
	right:0px;
	z-index:100;
}
.box{
	position:fixed;
	top:-200px;
	left:30%;
	right:30%;
	background-color:#fff;
	color:#7F7F7F;
	padding:20px;
	border:2px solid #ccc;
	-moz-border-radius: 20px;
	-webkit-border-radius:20px;
	-khtml-border-radius:20px;
	-moz-box-shadow: 0 1px 5px #333;
	-webkit-box-shadow: 0 1px 5px #333;
	z-index:101;
}
.box h1{
	border-bottom: 1px dashed #7F7F7F;
	margin:-20px -20px 0px -20px;
	padding:10px;
	background-color:#FFEFEF;
	color:#EF7777;
	-moz-border-radius:20px 20px 0px 0px;
	-webkit-border-top-left-radius: 20px;
	-webkit-border-top-right-radius: 20px;
	-khtml-border-top-left-radius: 20px;
	-khtml-border-top-right-radius: 20px;
}
a.boxclose{
	float:right;
	width:26px;
	height:26px;
	background:transparent url(<?php echo HTTP_ROOT; ?>img/overlay_images/cancel.png) repeat top left;
	margin-top:-30px;
	margin-right:-30px;
	cursor:pointer;
}
</style>
<script>
    $(document).ready(function() {
        $('#txt_UserId').focus();
        var hashurl = getHash()
        parseUrlHash(hashurl)
	
	
	var visitortime = new Date();
	var visitortimezone = -visitortime.getTimezoneOffset()/60;
	$('#timezone_id').val(visitortimezone);
	
    });

    function loginDemo(email, pass) {
        $("#txt_UserId").val(email);
        $("#txt_Password").val(pass);
        $("#UserLoginForm").submit();
    }
    function getHash(window) {
        var match = (window || this).location.href.match(/#(.*)$/);
        return match ? match[1] : '';
    }

    function parseUrlHash(hash) {
        var urlVars = {};
        var params = (hash.substr(0)).split("/");
        if (params[1]) {
            $('#case_details').val(params[1]);
        }
    }
</script>
<div class="top_m_cont_land">
    <div class="wrapper_new">
        <div style="display:table-cell; height:100%; min-height:100%; vertical-align:middle">
            <div class="login-page-wrapper" style="position:relative; z-index:9;">
                <div class="bg_logo_inner" style="top:-100px;left:-180px"></div>
                <div class="logo_landing">
                    <a href="<?php echo HTTPS_HOME; ?>"><img src="<?php echo HTTP_ROOT; ?>img/images/presales.png?v=<?php echo RELEASE; ?>"  border="0" alt="Orangescrum.com" title="Orangescrum.com"/></a>
                    <?php
					if(!$findCompany['Company']['id']) {
						?>
                        <h4><?php echo __("Welcome to Orangescrum Community Edition v1.6.1"); ?></h4>
                        <div style="color:#666;background:#F0F0F0;font-size:13px;padding:5px 10px;text-align:left;font-family:'Courier New', Courier, monospace;border:1px dashed #FF7E00;">
                        <?php echo __("Make sure that,"); ?> <br/>
                        <ul>
                       	<li><?php echo __("You have write permission (777) to"); ?> <b>`app/tmp`</b> <?php echo __("and"); ?> <b>`app/webroot`</b> <?php echo __("folders"); ?></li>
                        
                        <?php
						if(!defined('SMTP_PWORD') || SMTP_PWORD == "******") { ?>
                        <li><?php echo __("You have provided the details of"); ?> <b>SMTP</b> <?php echo __("email sending options in"); ?> <b>`app/Config/constants.php`</b></li>
<?php
						}
						?><li><?php echo __("You have updated FROM_EMAIL_NOTIFY and SUPPORT_EMAIL in "); ?><b>`app/Config/constants.php`</b></li>
						
                        </ul>
                        </div>
                        <?php
					}
					else {
						$pos = strpos(SUB_FOLDER, '/');
						if ($pos === false) {
							//echo '<ul><li style="color:red;">'.__("Replace the SUB_FOLDER name as").' "'.SUB_FOLDER.'/" '.__("instead of").' "'.SUB_FOLDER.'" '.__("in the constants.php").'</li></ul>';
						}
					?>
					<div style="padding:10px 5px;">
						<div style="clear:both"></div>
						<div style="float:left"><a href="https://www.orangescrum.com/how-it-works" target="_blank"><?php echo __("How it Works?"); ?></a></div>
						<div style="float:right;padding-right:10px;"><a href="https://www.orangescrum.com/help" target="_blank"><?php echo __("Help!"); ?></a></div>
					</div>
					<?php
					}
					?>
                </div>
                <div class="login_table">
                    <div style="height:100%;display:table; width:100%;" class="m-480">

                        <div id="container" style="display:table-cell; vertical-align:middle">
							
                            <div class="">
                                <div class="fl m-fl-none" style="right:0px; left:-8px;">
									<?php if(!$rightpath) { ?>
										<style>
										.cake-error {
											display:none;
										}
										</style>
										<div style="color:#FF0000;font-size:14px;text-align:center;">
											<?php echo __("Update"); ?> <b>SUB_FOLDER</b> in <b>app/Config/constants.php</b> <?php echo __("to "); ?><b>define('SUB_FOLDER', '<?php echo $sub_folder; ?>/');</b>
											<br/>
											<?php
											if(SUB_FOLDER) {
												echo __("Make sure that, the")." '<b>.htaccess</b>' ".__("file is there in the root directory");
											}
											?>
										</div>
									<?php 
									}
									else {
									?>
                                    <div class="login_box">
                                        <h2 style="font-size:22px;">
                                            <?php
                                            if($findCompany['Company']['id']) {
                                                echo __("Login to your Account");
                                                $action = "/login";
                                            }
                                            else {
                                                echo __("Quick Signup");
                                                $action = "/login";
                                            }
                                            ?>
                                       </h2>
                                       <div class="login-btm-img"><img src="<?php echo HTTP_ROOT; ?>img/images/login_header_shadow.png?v=<?php echo RELEASE; ?>" width="350" height="8"/></div>
                                        
                                        <?php echo $this->Form->create('User', array('id'=>'userLoginForm','action' => $action)); ?>
										<input type="hidden" name="data[User][timezone_id]" id="timezone_id" value="">
                                       
                                        <div class="login_dialog top_inc_app_land_from" id="login_dialog" style="margin-top:0px;">
                                           <div id="divide"></div>
					   <div style="text-align:center;">
                                                <?php 
						    if(isset($update_email_message)){
							echo $update_email_message;
						     }else{
							echo $this->Session->flash(); 
						     }
						?>
                                            </div>

											 <?php
                                            if(!$findCompany['Company']['id']) {
                                             ?>
                                                 <label class="lbl_ie"><?php echo __("Company Name"); ?></label>
												<?php echo $this->Form->text('company', array('size' => '30', 'class' => 'textbox', 'placeholder' => __("Company Name"), 'title' => __("Company Name"), 'id' => 'company', 'style' => 'background:#fff')); ?>

                                            </div>
                                            <?php
                                            }
                                            ?>
                                            
                                            <label class="lbl_ie"><?php echo __("Email ID"); ?></label>
                                            <?php echo $this->Form->text('email', array('size' => '30', 'class' => 'textbox', 'placeholder' => __("Email ID"), 'title' => __("Email ID"), 'id' => 'email', 'style' => 'background:#fff')); ?>

                                            <label class="lbl_ie"><?php echo __("Password"); ?></label>
                                            <?php echo $this->Form->password('password', array('size' => '30', 'class' => 'textbox', 'placeholder' => __("Password"), 'title' => __("Password"), 'id' => 'password')); ?>
                                            <div class="gap10"></div>
                                            
                                            <?php
                                            if($findCompany['Company']['id']) {
                                             ?>
                                            <div>
                                                <div style="margin-top:0px; margin-left:20px;width:100%">
                                                    <input type="hidden" value="" name="case_details" id="case_details" />
                                                    <button type="submit" value="Save" name="submit_Pass" id="submit_Pass" class="btn btn_blue" style="width:91%"><?php echo __("Log in"); ?></button> <!--Or &nbsp;-->

                                                <?php
                                                if (isset($_GET['project'])) {
                                                    ?>
                                                        <input type="hidden" name="data[User][project]" value="<?php echo $_GET['project']; ?>" readonly="true">
                                                        <?php
                                                    }
                                                    if (isset($_GET['case'])) {
                                                        ?>
                                                        <input type="hidden" name="data[User][case]" value="<?php echo $_GET['case']; ?>" readonly="true">
                                                        <?php
                                                    }
                                                    if (isset($_GET['file'])) {
                                                        ?>
                                                        <input type="hidden" name="data[User][file]" value="<?php echo $_GET['file']; ?>" readonly="true">
                                                        <?php
                                                    }
                                                    ?>

                                                </div>
                                                <div class="gap10"></div>
                                                <div class="fl" style="margin-left:20px;">
                                                    <input type="checkbox" name="data[User][remember_me]" id="chk_Rem" class="auto" value="1" style="cursor:pointer; border:none"/>
                                                    <span class="rem_posn" style="color:#666;"><?php echo __("Remember me"); ?></span>
                                                </div>
                                                <div class="fr or_cancel"><a href="<?php echo HTTP_ROOT; ?>users/forgotpassword" class="forgot_pwd"><?php echo __("Forgot Password"); ?>?</a></div>
                                                <?php
                                                }
                                                else {
                                                ?>
                                                <div class="gap10"></div>
                                                <div style="margin-top:0px; margin-left:20px;" class="fl">
                                                <?php echo __("By signing up you you agree that you have read, understand, and accept the"); ?> <b><a href="<?php echo HTTP_ROOT; ?>license" style="color:#FF0000;margin:0;"><?php echo __("License"); ?></a></b>
                                                <br/><br/>
                                                <button type="button" value="Signup" name="submit_button" id="submit_button" class="btn btn_blue" onclick="return validateForm()"><?php echo __("Signup"); ?></button>
                                                <img src="<?php echo HTTP_ROOT."img/images/case_loader2.gif"; ?>" id="submit_loader" style="display:none;"/>
                                                <div class="gap10"></div>
                                                </div>
                                                <?php
                                                }
                                                ?>
                                                <div class="cb"></div>
                                            </div>
                                            <div class="gap10"></div>
                                            <?php /*
                                            if($findCompany['Company']['id'] && USE_GOOGLE == 1) {
                                             ?>
                                                <div class="" style="text-align: center;">
													<div class="goog_log_or"><img src="<?php echo HTTP_IMAGES; ?>images/g_login_or.png?v=<?php echo RELEASE; ?>"/></div>
													<div class="gogle_log_sup" onclick="loginWithGoogle();"><img src="<?php echo HTTP_ROOT; ?>img/glogin.png" alt="Loading..."/></div>
													 <div class="gap10"></div>
												</div>
                                            <?php
                                            } */
                                            ?>
                                                <?php echo $this->Form->end(); ?>
                                        </div>
                                    </div>
                                    <div class="cb"></div>
                                </div>
                            </div>
                            <div class="cb"></div>
							<?php
							}
							?>
                        </div>

                    </div>
                    <div class="cb"></div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<input type="hidden" name="pageurl" id="pageurl" value="<?php echo HTTP_ROOT; ?>" size="1" readonly="true"/>

<?php
if(!$findCompany['Company']['id']) {
 ?>
<script>
    function validateForm() {
	var error_flag =1;
	var name = '';
	var email =$.trim($("#email").val());
	var password =$.trim($("#password").val());
	var company =$.trim($("#company").val());
	var timezone_id = $("#timezone_id").val();

	var emailRegEx = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	var letterNumber = /^[0-9a-zA-Z]+$/;

	if(email == "") {
		$("#email").css({"border":"1px solid #FF0000"});
		$("#email").focus();
		error_flag=0;
	}else {
		if(!email.match(emailRegEx)){
                    $("#email").css({"border":"1px solid #FF0000"});
                    $("#email").focus();
                    error_flag=0;
		}
	}

	if(password == "") {
		$("#password").css({"border":"1px solid #FF0000"});
		$("#password").focus();
		error_flag=0;
	}
	if(company == "") {
		$("#company").css({"border":"1px solid #FF0000"});
		$("#company").focus();
		error_flag=0;
	}
	if(!error_flag){
            return false;
	}
        else {
	    $("#submit_button").hide();
	    $("#submit_loader").show();
	    var strURL = "<?php echo HTTP_ROOT;?>";
	    $.post(strURL+"users/register_user",{'email':email,'password':password,'company':company,'timezone_id':timezone_id},function(data) {
		//console.log(data);
		if(data.message == 'success'){
		    $('#userLoginForm').submit();
		}else{
		    alert(data.message);
		}
	    },'json');
	   
           return false;
        }
}
</script>
<?php } ?>
