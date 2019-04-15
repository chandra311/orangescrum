<?php $user=ClassRegistry::init('User')->findById(SES_ID);
if(!empty($user['User']['password'])) {
    define('NO_PASSWORD',0);
}else {
    define('NO_PASSWORD',1);
}
?>

<div class="tab tab_comon">
    <ul class="nav-tabs">
        <li <?php if(PAGE_NAME == 'profile') {?>class="active" <?php }?>>
            <a href="<?php echo HTTP_ROOT.'users/profile';?>" id="sett_my_profile" rel="tooltip" title="<?php echo __("My Profile"); ?>">
                <div class="fl sett_my_prof"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("My Profile"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <li <?php if(PAGE_NAME == 'changepassword') {?>class="active" <?php }?>>
            <?php if(NO_PASSWORD) {?>
            <a href="<?php echo HTTP_ROOT.'users/changepassword';?>" id="sett_cpw_prof" rel="tooltip" title="<?php echo __("Set Password"); ?>">
                <div class="fl sett_cpw"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Set Password"); ?></div>
                <div class="cbt"></div>
            </a>
                <?php }else {?>
            <a href="<?php echo HTTP_ROOT.'users/changepassword';?>" id="sett_cpw_prof" rel="tooltip" title="<?php echo __("Change Password"); ?>">
                <div class="fl sett_cpw"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Change Password"); ?></div>
                <div class="cbt"></div>
            </a>
                <?php }?>
        </li>
        <li <?php if(PAGE_NAME == 'email_notifications') {?>class="active" <?php }?>>
            <a href="<?php echo HTTP_ROOT.'users/email_notifications';?>" id="sett_mail_noti_prof" rel="tooltip" title="<?php echo __("Notifications"); ?>">
                <div class="fl sett_mail_noti"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Notifications"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <li <?php if(PAGE_NAME == 'email_reports') {?>class="active" <?php }?>>
            <a href="<?php echo HTTP_ROOT.'users/email_reports';?>" id="sett_mail_repo_prof" rel="tooltip" title="<?php echo __("Email Reports"); ?>" >
                <div class="fl sett_mail_repo"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Email Reports"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <li <?php if(PAGE_NAME == 'default_view') {?>class="active" <?php }?>>
            <a href="<?php echo HTTP_ROOT.'users/default_view';?>" id="sett_mail_repo_prof" rel="tooltip" title="<?php echo __("Default View"); ?>" >
                <div class="fl sett_mail_repo"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Default View"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <?php if(defined('LANG') && LANG == 1){ ?>
        <li <?php if(CONTROLLER == 'MultiLanguage' && PAGE_NAME == 'settings') {?>class="active" <?php }?>>
            <a href="<?php echo HTTP_ROOT.'language-settings';?>" id="sett_mail_repo_prof" rel="tooltip" title="<?php echo __("Language Settings"); ?>" >
                <div class="fl sett_mail_repo"></div>
                <div class="fl"><?php echo __("Language Settings"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <?php } ?>
        <div class="cbt"></div>
    </ul>
</div>
