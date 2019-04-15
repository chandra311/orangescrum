<div class="listdv">
    <div class="fl icn_udet_db"><img class="portlet-header" src="<?php echo HTTP_IMAGES; ?>images/u_det_sto.png" /></div>
    <div class="fl cnt_udet_db"><b><?php if(isset($usage_details['0']['total_filesize']['filesize']) && !empty($usage_details['0']['total_filesize']['filesize'])){ echo $usage_details['0']['total_filesize']['filesize'];} else { echo 0;}?></b> MB <?php echo __("of File Storage"); ?></div>
    <div class="cb"></div>
</div>
<?php if(isset($usage_details['0']['total_projects'])){ ?>
<div class="listdv">
    <div class="fl icn_udet_db"><img class="portlet-header" src="<?php echo HTTP_IMAGES; ?>images/u_det_proj.png" /></div>
    <div class="fl cnt_udet_db"><b><?php if(isset($usage_details['0']['total_projects']['cnt_projects']) && !empty($usage_details['0']['total_projects']['cnt_projects'])){ echo $usage_details['0']['total_projects']['cnt_projects'];} else { echo 0;}?></b> <?php echo __("Projects"); ?></div>
    <div class="cb"></div>
</div>
<?php } ?>
<div class="listdv">
    <div class="fl icn_udet_db"><img class="portlet-header" src="<?php echo HTTP_IMAGES; ?>images/u_det_usr.png" /></div>
    <div class="fl cnt_udet_db"><b><?php if(isset($usage_details['0']['total_users']['cnt_users']) && !empty($usage_details['0']['total_users']['cnt_users'])){ echo $usage_details['0']['total_users']['cnt_users'];} else { echo 0;}?></b> <?php echo __("Active Users"); ?></div>
    <div class="cb"></div>
</div>

