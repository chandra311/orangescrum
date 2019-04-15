<?php echo $this->Form->create('Vacation', array('id' => 'addleave' )); ?>
    <div class="data-scroll user_pdt">
        <table cellpadding="0" cellspacing="0" class="col-lg-12 new_auto_tab">
            <tr>
                <td><?php echo __("Start Date"); ?>:</td>
                <td>
                    <?php echo $this->Form->hidden('user_id', array('id' => 'applicant', 'value' => @$leavearr['UserLeave']['user_id'])); ?>
                    <?php echo $this->Form->hidden('id', array('id' => 'leave_id', 'value' => @$leavearr['UserLeave']['id'])); ?>
                    <?php $startdate = (isset($leavearr['UserLeave']['start_date']) && !empty($leavearr['UserLeave']['start_date'])) ? date('M d, Y', strtotime($leavearr['UserLeave']['start_date'])) : "" ;?>
                    <?php $enddate = (isset($leavearr['UserLeave']['end_date']) && !empty($leavearr['UserLeave']['end_date'])) ? date('M d, Y', strtotime($leavearr['UserLeave']['end_date'])) : "" ;?>
                    <?php echo $this->Form->text('start_date', array('class' => 'datepicker form-control', 'id' => 'leave_start_date', 'readonly' => 'readonly', 'style' => 'cursor:text', 'value' => $startdate)); ?>
                    <?php echo $this->Form->hidden('start_date', array('class' => 'datepicker form-control', 'id' => 'leave_act_start_date', 'readonly' => 'readonly', 'style' => 'cursor:text', 'value' => @$leavearr['UserLeave']['start_date'])); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo __("End Date"); ?>:</td>
                <td>
                    <?php echo $this->Form->text('end_date', array('class' => 'datepicker form-control', 'id' => 'leave_end_date', 'readonly' => 'readonly', 'style' => 'cursor:text', 'value' => $enddate)); ?>
                    <?php echo $this->Form->hidden('end_date', array('id' => 'leave_act_end_date', 'value' => @$leavearr['UserLeave']['end_date'])); ?>
                </td>
            </tr>
            <tr>
                <td><?php echo __("Reason of Leave"); ?>:</td>
                <td>
                    <?php echo $this->Form->textarea('reason', array('id' => 'leave_description', 'class' => 'form-control', 'value' => @$leavearr['UserLeave']['reason'])); ?>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <div class="fl">
                        <span id="ldr" style="display:none;">
                            <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loading..." title="Loading..." />
                        </span>
                        <span id="btn_leave">
                            <button type="button" value="Update" name="leaveBtn" id="leaveBtn" class="btn btn_blue" onclick="return validateLeaveForm();"><?php if (!empty($edit)) { echo __("Save"); } else { echo __("Add"); } ?></button>
                            
                            <?php if(isset($leavearr) && !empty($leavearr)){ ?>
                            <span class="or_cancel"><?php echo __('or'); ?>
                                      <button type="button" value="Update" name="CnclleaveBtn" id="CnclleaveBtn" class="btn btn_blue" onclick="cancelLeave();"><?php echo __("Cancel Leave");  ?></button>
                                      </span>
                            <?php   }?>
                             <span class="or_cancel"><?php echo __('or'); ?>
                                <a onclick="closePopup();"><?php echo __("Cancel"); ?></a>
                            </span>
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php echo $this->Form->end(); ?>
<script type="text/javascript">
    $(function() {
        $("#leave_start_date").datepicker({
            dateFormat: 'M d, yy',
            altField: '#leave_act_start_date',
            altFormat: 'yy-mm-dd',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function( selectedDate ) {
                $("#leave_end_date").datepicker( "option", "minDate", selectedDate );
            }
        });
        $("#leave_end_date").datepicker({
            dateFormat: 'M d, yy',
            altField: '#leave_act_end_date',
            altFormat: 'yy-mm-dd',
            changeMonth: false,
            changeYear: false,
            hideIfNoPrevNext: true,
            onClose: function( selectedDate ) {
                $( "#leave_start_date" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    });
</script>