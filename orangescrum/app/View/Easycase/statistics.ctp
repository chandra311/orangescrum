<div class="listdv">
    <div class="fl stas_icn stas_red"><?php if(isset($statistics['0']['task_without_due_date']['task_without_due_date']) && !empty($statistics['0']['task_without_due_date']['task_without_due_date'])){ echo $statistics['0']['task_without_due_date']['task_without_due_date'];} else { echo 0;}?></div>
    <div class="fl stas_cnt_db"><?php echo __("Task without Due Date"); ?></div>
    <div class="cb"></div>
</div>
<?php /*?><div class="lstbtndv"></div>
<div class="listdv">
    <div class="fl stas_icn stas_red"><?php if(isset($statistics['0']['task_have_no_hours']['task_have_no_hours']) && !empty($statistics['0']['task_have_no_hours']['task_have_no_hours'])){ echo $statistics['0']['task_have_no_hours']['task_have_no_hours'];} else { echo 0;}?></div>
    <div class="fl stas_cnt_db">Task have no hours spent</div>
    <div class="cb"></div>
</div><?php */?>
<div class="lstbtndv"></div>
<div class="listdv">
    <div class="fl stas_icn stas_green"><?php if(isset($statistics['0']['hours_spent']['hours_spent']) && !empty($statistics['0']['hours_spent']['hours_spent'])){ echo defined('TLG') && TLG == 1 ? $this->Format->format_time_hr_min($statistics['0']['hours_spent']['hours_spent']): $statistics['0']['hours_spent']['hours_spent'];} else { echo 0;}?></div>
    <div class="fl stas_cnt_db"><?php echo __("Hours spent and still counting"); ?></div>
    <div class="cb"></div>
</div>
<div class="lstbtndv"></div>
<div class="listdv">
    <div class="fl stas_icn stas_green"><?php if(isset($statistics['0']['task_hours']['task_hours']) && !empty($statistics['0']['task_hours']['task_hours'])){ echo defined('TLG') && TLG == 1 ? $this->Format->format_time_hr_min($statistics['0']['task_hours']['task_hours']): $statistics['0']['hours_spent']['hours_spent'];} else { echo 0;}?></div>
    <div class="fl stas_cnt_db"><?php echo __("Hours spent on"); ?> <?php echo $task_type_name;?></div>
    <div class="cb"></div>
</div>
<!--
<div class="lstbtndv"></div>
<div class="listdv">
    <div class="fl stas_icn stas_green">47</div>
    <div class="fl stas_cnt_db"><?php echo __("Task without Due Date"); ?></div>
    <div class="cb"></div>
</div>
<div class="lstbtndv"></div>
<div class="listdv">
    <div class="fl stas_icn stas_orange">47</div>
    <div class="fl stas_cnt_db"><?php echo __("Task without Due Date"); ?></div>
    <div class="cb"></div>
</div>
-->