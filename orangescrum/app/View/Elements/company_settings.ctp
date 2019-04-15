<div class="tab tab_comon mycompany_ipad">
    <ul class="nav-tabs">
        <li <?php if (PAGE_NAME == 'mycompany') { ?>class="active" <?php } ?>>
            <a href="<?php echo HTTP_ROOT . 'my-company'; ?>" id="sett_mail_noti_prof" rel="tooltip" title="<?php echo __("My Company"); ?>">
                <div class="fl grp_comp"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("My Company"); ?></div>
                <div class="cbt"></div> 
            </a>
        </li>
        <li <?php if (PAGE_NAME == 'groupupdatealerts') { ?>class="active" <?php } ?> style="width:177px">
            <a href="<?php echo HTTP_ROOT . 'reminder-settings'; ?>" id="sett_mail_repo_prof" rel="tooltip" title="<?php echo __("Daily Catch-Up"); ?>">
                <div class="fl grp_alt"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Daily Catch-Up"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <li <?php if (PAGE_NAME == 'importexport' || PAGE_NAME == 'csv_dataimport' || PAGE_NAME == 'confirm_import') { ?>class="active" <?php } ?>>
            <a href="<?php echo HTTP_ROOT . 'import-export'; ?>" id="sett_imp_exp_prof" rel="tooltip" title="<?php echo __("Import & Export"); ?>">
                <div class="fl grp_impx"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Import & Export"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <li <?php if (PAGE_NAME == 'task_type') { ?>class="active" <?php } ?>>
            <a href="<?php echo HTTP_ROOT . 'task-type'; ?>" id="sett_task_type" rel="tooltip" title="<?php echo __("Task Type"); ?>">
                <div class="fl" style="height: 18px;width: 18px;margin-right: 6px;">
                    <img src="<?php echo HTTP_ROOT . "img/tasktype.png"; ?>"  width="16px" height="16px"/>
                </div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Task Type"); ?></div>
                <div class="cbt"></div>
            </a>
        </li>
        <?php if(defined('API') && API == 1){ ?>
        <li <?php if (CONTROLLER == 'Apis' && PAGE_NAME == 'settings') { ?>class="active" <?php } ?> style="width:110px;">
            <a href="<?php echo HTTP_ROOT . 'api-settings'; ?>" id="sett_spi_setting" rel="tooltip" title="<?php echo __("API"); ?>">
                <div class="fl" style="height: 18px;width: 18px;margin-right: 6px;">
                    <img src="<?php echo HTTP_ROOT . "img/tasktype.png"; ?>"  width="16px" height="16px"/>
                </div>
                <div class="fl ellipsis-view maxWidth120 "><?php echo __("API"); ?></div>
                <div class="cbt"></div>
            </a>
        </li> 
        <?php } ?>
        <?php if (defined('CR') && CR == 1) { ?>
            <li <?php if (CONTROLLER == 'ClientRestriction' && PAGE_NAME == 'settings') { ?>class="active" <?php } ?> style="width:225px;">
                <a href="<?php echo HTTP_ROOT . 'clientrestriction/ClientRestriction/settings'; ?>" id="sett_spi_setting">
                    <div class="fl" style="height: 18px;width: 18px;margin-right: 6px;">
                        <img src="<?php echo HTTP_ROOT . "img/tasktype.png"; ?>"  width="16px" height="16px"/>
                    </div>
                    <div class="fl"><?php echo __("Client Restrictions"); ?></div>
                    <div class="cbt"></div>
                </a>
            </li>
        <?php } ?>
        <div class="cbt"></div>
    </ul>
</div>
