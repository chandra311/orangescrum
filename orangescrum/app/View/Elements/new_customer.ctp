<style>
.popup_form .new_customer table tr td{padding:5px 20px 5px 0px;}
</style>
<center><div id="cust_err_msg" style="color:#FF0000;display:none;"></div></center>
<?php  echo $this->Form->create('Customer',array('url'=>array('controller' =>'invoices', 'action' => 'add_customer','plugin'=>'Invoice'),'name'=>'frm_add_customer','id'=>'frm_add_customer')); ?>
<?php  //echo $this->Form->create('Customer',array('url'=>'/invoices/add_customer','name'=>'frm_add_customer','id'=>'frm_add_customer')); ?>
<?php print $this->Form->input('customer_id', array('label'=>false,'type'=>'hidden', 'id' => 'cust_id','value'=>'')); ?>
    <div class="data-scroll new_customer">
        <table cellpadding="0" cellspacing="0" class="col-lg-12">
            <tr>
                <td class="popup_label" valign="top"><?php echo __('Name');?>:*</td>
                <td>
                    <?php echo $this->Form->text('cust_title', array('value' => '', 'class' => 'form-control fl', 'id' => 'cust_title', 'placeholder' => __("Title",true), 'style' => 'width:14%','maxlength'=>'10')); ?>
                    <?php echo $this->Form->text('cust_fname', array('value' => '', 'class' => 'form-control fl', 'id' => 'cust_fname', 'placeholder' => __("First Name",true), 'style' => 'width:43%','maxlength'=>'100')); ?>
                    <?php echo $this->Form->text('cust_lname', array('value' => '', 'class' => 'form-control fr', 'id' => 'cust_lname', 'placeholder' => __("Last Name",true), 'style' => 'width:42%','maxlength'=>'100')); ?>
                    <div class="cb"></div>
                    <div class="err" style="text-align:left;" id="cust_name_err"></div>
                </td>
            </tr>
            <tr>
                <td><?php echo __('Email');?>:*</td>
                <td><?php echo $this->Form->text('cust_email', array('value' => '', 'class' => 'form-control', 'id' => 'cust_email', 'placeholder' => __("Email",true))); ?></td>
            </tr>
            <tr>
                <td><?php echo __('Currency');?>:*</td>
                <td>
                    <?php echo $this->Form->input('cust_currency',array('options'=>$this->Format->currency_opts(),'empty'=>__('Select Currency', true), 'class' => 'form-control fl', 'id' => 'cust_currency', 'placeholder' => __("Currency",true), 'style' => 'width:100%','label'=>false)); ?>
                </td>
            </tr>
            <tr class="customer_options" style="display:none;">
                <td><?php echo __('Organization');?>:</td>
                <td><?php echo $this->Form->text('cust_organization', array('value' => '', 'class' => 'form-control', 'id' => 'cust_organization', 'placeholder' => __("Organization",true))); ?></td>
            </tr>
            <tr class="customer_options" style="display:none;">
                <td><?php echo __('Address');?>:</td>
                <td>
                    <?php echo $this->Form->text('cust_street', array('value' => '', 'class' => 'form-control fl', 'id' => 'cust_street', 'placeholder' => __("Street",true), 'style' => 'width:100%')); ?>                    
                </td>
            </tr>
            <tr class="customer_options" style="display:none;">
                <td>&nbsp;</td>
                <td>
                    <?php echo $this->Form->text('cust_city', array('value' => '', 'class' => 'form-control fl', 'id' => 'cust_city', 'placeholder' => __("City",true), 'style' => 'width:49%')); ?>
                    <?php echo $this->Form->text('cust_state', array('value' => '', 'class' => 'form-control fr', 'id' => 'cust_state', 'placeholder' => __("State",true), 'style' => 'width:50%')); ?>
                    
                </td>
            </tr>
            
            <tr class="customer_options" style="display:none;">
                <td>&nbsp;</td>
                <td>
                    <?php echo $this->Form->text('cust_country', array('class' => 'form-control fl', 'id' => 'cust_country', 'placeholder' => __("Country",true), 'style' => 'width:49%')); ?>
                    <?php echo $this->Form->text('cust_zipcode', array('class' => 'form-control fr', 'id' => 'cust_zipcode', 'placeholder' => __("Postal Code",true), 'style' => 'width:50%','maxlength'=>'10')); ?>
                </td>
            </tr>
            <tr class="customer_options" style="display:none;">
                <td>&nbsp;</td>
                <td>
                    <?php echo $this->Form->text('cust_phone', array('value' => '', 'class' => 'form-control fl', 'id' => 'cust_phone', 'placeholder' => __("Phone Number",true), 'style' => 'width:49%','maxlength'=>'20')); ?>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <div class="checkbox fl"  style="">
                        <label>
                        <?php echo $this->Form->checkbox('cust_status', array('hiddenField' => false, 'value' => 'Inactive', 'id' => 'cust_status')); ?>
                        <?php echo __('Make Inactive',true);?>
                        </label>
                    </div>
                </td>
            </tr>
            <tr><td>&nbsp;</td><td><a class="fl anchor" style="color:#006699" id="more_customer_options">+ <?php echo __('Details');?></a></td></tr>
        </table>    
    </div>
    <div style="padding-left:145px;">
        <span id="cust_loader" style="display:none;">
            <img src="<?php echo HTTP_IMAGES; ?>images/case_loader2.gif" alt="Loader"/>
        </span>
        <span id="btn">
            <button type="button" value="Add" class="btn btn_blue" id="btn_add_customer"><i class="icon-big-tick"></i><?php echo __('Create');?></button>
            <span class="or_cancel cancel_on_direct_pj"><?php echo __('or');?> <a onclick="closePopup();"><?php echo __('Cancel');?></a></span>
        </span>

    </div>
<?php echo $this->Form->end(); ?>