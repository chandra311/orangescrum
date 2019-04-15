<body style="width:100%; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#ffffff;">
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="height:auto !important; margin:0; padding:0; width:100% !important; background-color:#F0F0F0;color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:19px; margin-top:0; padding:0; font-weight:normal;">
	<tr>
		<td>
        <div id="tablewrap" style="width:100% !important; max-width:600px !important; text-align:center; margin:0 auto;">
		      <table id="contenttable" width="600" align="center" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF; margin:0 auto; text-align:center; border:none; width: 100% !important; max-width:600px !important;border-top:8px solid #5191BD">
            <tr>
                <td width="100%">
                   <table bgcolor="#FFF" border="0" cellspacing="0" cellpadding="20" width="100%">
                        <tr>
                            <td width="100%" bgcolor="#FFF" style="text-align:left;">
                            	<p>
                                    Hi <?php echo $expName;?>,                    
                                </p>
								
								<?php
								if($existing_user)
								{
								?>
                                                                    <p>You have got an invitation from <a href="mailto:<?php echo $fromEmail; ?>"><?php echo $fromName; ?></a> to join <?php echo $company_name; ?>  on Orangescrum.</p>
                                                                    <p>Please click the button below to accept the invitation in web.</p>                                                                        
                                                                    <a style="font-weight:bold; text-decoration:none;" href="<?php echo HTTP_ROOT.'users/invitation/'.$qstr;?>" target='_blank'><div style="display:block; max-width:100% !important; width:auto !important;margin:auto; height:auto !important;background-color:#0EA426;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:10px;color:#ffffff;font-size:16px;text-align:center">Join <?php echo $company_name; ?></div></a>

                                                                    <p>For Mobile App, please follow the below instruction:</p>
                                                                    <p>
                                                                        Access Url: <?php echo $access_url; ?><br />
                                                                        Access Code: <?php echo $qstr; ?>
                                                                    </p>                                                                        
                                                                    <p>
                                                                    <b>Note:</b><br />
                                                                     If you have not downloaded the Orangescrum App please follow the below link:<br />
                                                                     For iPhone: <a href="https://itunes.apple.com/us/app/orangescrum/id1132539893?mt=8">Download Now</a> <br />
                                                                     For Android: <a href="https://play.google.com/store/apps/details?id=com.andolasoft.orangescrum&hl=en">Download Now</a> <br />
                                                                     Ignore the above message if you have already downloaded.
                                                                    </p>                                                                        
								<?php
								}
								else {
								?>
                                                                    <p><a href="mailto:<?php echo $fromEmail; ?>"><?php echo $fromName; ?></a> has just setup an account for you on Orangescrum.</p>
                                                                    <p>Please click the button below to accept the invitation in web.</p>                                                                        
                                                                    <a style="font-weight:bold; text-decoration:none;" href="<?php echo HTTP_ROOT.'users/invitation/'.$qstr;?>" target='_blank'><div style="display:block; max-width:100% !important; width:auto !important;margin:auto; height:auto !important;background-color:#0EA426;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:10px;color:#ffffff;font-size:16px;text-align:center">Get started with Orangescrum</div></a>

                                                                    <p>For Mobile App, please follow the below instruction:</p>
                                                                    <p>
                                                                        Use the below credential to access mobile App.<br />
                                                                        Access Url: <?php echo $access_url; ?><br />
                                                                        Access Code: <?php echo $qstr; ?>
                                                                    </p>                                                                        
                                                                    <p>
                                                                    <b>Note:</b><br />
                                                                     If you have not downloaded the Orangescrum App please follow the below link:<br />
                                                                     For iPhone: <a href="https://itunes.apple.com/us/app/orangescrum/id1132539893?mt=8">Download Now</a> <br />
                                                                     For Android: <a href="https://play.google.com/store/apps/details?id=com.andolasoft.orangescrum&hl=en">Download Now</a> <br />
                                                                     Ignore the above message if you have already downloaded.
                                                                    </p> 
								<?php
								}
								?>
									
                                <br/>
								
								<p>If you have any questions, please write us at <a href='mailto:<?php echo SUPPORT_EMAIL; ?>'><?php echo SUPPORT_EMAIL; ?></a>, we will be happy to help you.</p>
								
								<br/>
								
								<p>Regards,<br/>
								The Orangescrum Team</p>
                            </td>
                        </tr>
                   </table>
                  
                   <table bgcolor="#F0F0F0" border="0" cellspacing="0" cellpadding="10" width="100%" style="border-top:2px solid #F0F0F0;margin-top:10px;border-bottom:3px solid #2489B3">
                        <tr>
                            <td width="100%" bgcolor="#ffffff" style="text-align:center;">
                            	<p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:14px; margin-top:0; padding:0; font-weight:normal;padding-top:5px;">
									<?php
									if($existing_user)
									{
									?>
										You are receiving this email notification because you have subscribed to Orangescrum, to unsubscribe, please email with subject 'Unsubscribe' to <a href='mailto:support@orangescrum.com'>support@orangescrum.com</a>
									<?php
									}
									else {
									?>
										Your email address is used to invite you on Orangescrum. If you didn't intend to do this, just ignore this email; no account has been created yet.
									<?php
									}
									?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>
		</td>
	</tr>
</table> 
</body>


<?php /*?><table style="padding-top:20px;margin:0 auto;text-align:left;width:100%">
  <tbody>
  	<?php echo EMAIL_HEADER;?>
    <tr>
      <td>
	<div style="color:#000;font-family:Arial;font-size:14px;line-height:1.8em;text-align:left;padding-top: 10px;">
		<p style="display:block;margin:0 0 17px">Hi <?php echo $expName;?>,</p>
		<p>
			<?php echo $invitationMsg;?>
		</p>
		<p>
			<div>Please click on the link below to confirm.</div>
			<div><a href="<?php echo HTTP_ROOT.'users/invitation/'.$qstr;?>" target='_blank'><?php echo HTTP_ROOT."users/invitation/".$qstr; ?></a></div>
		</p>
		<p style="display:block;margin:0">
			Regards,<br/>
			The Orangescrum Team
		</p>				
	</div>
      </td>
    </tr>
    	<?php echo Configure::read('invite_user_footer');?>
    	<?php if(!empty($existing_user)){ ?>
    		<?php echo $existing_user;?>
		<?php echo Configure::read('common_footer');?>
	<?php } ?>
  </tbody>
</table><?php */?>


