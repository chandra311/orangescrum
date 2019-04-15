<style type="text/css">
    .margin52{margin-top:52px;}
    .margin15{margin-top:15px;margin-left:-5px;}
        .fl{float:left;}
        .fr{float:right;}
        .cb{clear:both;}
        .log-time ul{margin:0px; padding:20px 30px 0;}
        .log-time select.ui-timepicker-select{width:100%;height:34px;padding:6px 8px;line-height:1.428571429;color:#555555;vertical-align:middle;background-color:#ffffff;background-image:none;border:1px solid #cccccc;-webkit-box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);box-shadow:inset 0 1px 1px rgba(0, 0, 0, 0.075);-webkit-transition:border-color ease-in-out .15s, box-shadow ease-in-out .15s;transition:border-color ease-in-out .15s, box-shadow ease-in-out .15s;}
        .popup-container{border:1px solid #eee; margin:0 auto; width:700px; margin-top:40px; background-color:#F6F7F9; 
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.65);}
        .popup-header{height:50px;border-bottom:1px solid #ccc;}
        .popup-header-menu{padding:12px;color: #333;}
        .logtime-content{background-color:#fff; margin-top:-15px;}
        .log-time{width:100%; border:1pxm solid red;}
        .log-time ul li{display:inline-block;style-type:none; margin-right:5px; float:left;}
        .log-time ul.timelog-header{padding: 5px 0 5px 10px; margin: 20px 30px 0; background: #CED8E1;}
        .log-time label{display:block;font-weight:700; font-size:13px; }
        .plus-btn .addbtn{height:22px; border:1px solid #ccc; text-decoration:none; cursor:pointer; background-color:#EEEEEE; color:#ADADAD; 
                          font-weight:bold; border-radius: 5px; font-size: 31px; margin-left:30px; display: inline-block; padding: 10px;
                          text-align:center; line-height:0px; 
            font-family: "Raleway", "HelveticaNeue", "Helvetica Neue", "Helvetica", "Arial", sans-serif;
        }
        .plus-btn{margin-top:15px}
        .plus-btn span{font-size:13px;color:#666;padding-left:5px; }
        .description-task{padding:37px; border-top:1px solid #ccc; position:relative;margin-top:40px;border-bottom:1px dashed #ccc;}
        .description-task .description a{position:absolute;top:-30px;width:100px;height:30px; border:1px solid #ccc; text-decoration:none; 
                      color:#ADADAD;background-color:#EEEEEE;text-align:center;line-height: 29px;}
        .description-task .task a{ background-color: #fff;
                                   border-color: #ccc #ccc -moz-use-text-color; border-image: none; border-style: solid solid none; border-width: 1px 1px medium;
                                   color: #adadad;  float: left; height: 30px; left:148px; position: absolute; text-decoration: none;
                                   top: -30px; width: 60px; text-align:center;line-height: 29px;
        }
        .log-btn{margin:40px 0px 0px 0px;height:50px; text-align: center;}
        .log-btn a input{background-color: #3dbc89; border: 1px solid #3dbc89; border-radius: 5px; color: #fff; cursor: pointer;
                         font-size: 14px; height: 30px; width: 100px;}
        .log-btn a{text-decoration:none;}
        .complete-checkbox{margin-top:20px;}
        .sprite{background:url(("<?php echo HTTP_ROOT; ?>img/sprite.png)no-repeat; position:relative; display:block; width:20px; height:20px;}
        .sprite.no.p-change{background:url("<?php echo HTTP_ROOT; ?>img/sprite.png");background-position:2px -20px;top:0px;cursor:pointer;}
        .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
                background-color: #EEEEEE; cursor: pointer;
        }
        .errselectbox{border:1px solid red;}
        input[readonly].hasDatepicker{background: #fff;}
        .timelog_block .gr-lbl{margin-left:10px;}
        a:focus{text-decoration:none;}
        a:hover{text-decoration:underline;}
</style>
<form action="<?php echo HTTP_ROOT."add_tasklog"; ?>" method="POST" onSubmit="return stvariables();" autocomplete="off" name='frmaddlogtim' id="frmaddlogtim">
    <input type="hidden" name="project_id" value="" id="prjsid" />
    <input type="hidden" name="chked_ids" value="" id="chked_ids" /> 
    <input type="hidden" name="page_type" value="" id="page_type" /> 
    <div class="logtime-content">
        <div style="padding: 20px 30px 0; width: 100%;">
            <label><?php echo __('Task Title');?></label>
            <div class="spent-time fr" id="tl-msg-box">
                <div class="fl"><span class="use-time"><?php echo __('Logged');?>: </span><span id='logtime_total'>---</span></div>
                <div class="fl" style="margin:0px 10px;"><span class="use-time"><?php echo __('Billable');?>: </span><span id='logtime_billable'>---</span></div>
				<div class="fl" style="margin:0 10px 0 0;"><span class="use-time">Non-Billable: </span><span id='logtime_nonbillable'>---</span></div>
                <div class="fl"><span class="use-time"><?php echo __('Estimated');?>: </span><span id='logtime_estimated'>---</span></div>
                <div class="cb"></div>
            </div>
            <input type="hidden" name="hidden_task_id" id="hidden_task_id" value=""/>
            <select id="tsksid" name="task_id" class="form-control" onchange="modifyheader();">
                <option value=""><?php echo __('Select Task');?></option>
            </select>
        </div>
        <div class="log-time">
                <ul class="timelog-header">
                    <li style="width:20%;"><label><?php echo __('Resource');?></label></li>
                    <li style="width:14%;"><label><?php echo __('Date');?></label></li>
                    <li style="width:13%;"><label><?php echo __('Start Time');?></label></li>
                    <li style="width:13%;"><label><?php echo __('End Time');?></label></li>
                    <li><label><?php echo __('Break Time');?></label></li>
                    <li style="margin-left:10px;"><label><?php echo __('Spent Hours');?></label></li>
                    <li></li>
                    <li></li>
                    <div class="cb"></div>
                </ul>
                <ul id="ul_timelog1">
                    <li style="width:15%;">
                            <select id="whosassign1" name="user_id[]" class="form-control"><option value=""><?php echo __('Select User');?></option></select>
                    </li>
                    <li style="width:14%;">
                            <input type="text" id="workeddt1" name="task_date[]" class="form-control" value="<?php //echo date('M d, Y',strtotime('now')); ?>" style="font-size:13px;" readonly/>
                    </li>
                    <li style="width:14%;">
                            <input type="text" id="strttime1" name="start_time[]" onchange="updatehrs(1);" class="form-control updatehrs"  />
                    </li>
                    <li style="width:14%;">
                            <input type="text" id="endtime1" name="end_time[]" onchange="updatehrs(1);" class="form-control updatehrs"/>
                    </li>
                    <li>
                        <div style="width:80px;" class="fl">
                            <input type="text" maxlength="5"  value="" id="tshr1" class="time-spent form-control totalbreak check_minute_range" name="totalbreak[]" placeholder="hh:mm" />
                        </div>
                        <div class="cb"></div>
                    </li>
                    <li>
                            <div style="width:70px;" class="fr">
                                <input type="text" maxlength="5" value="" id="tsmn1" class="time-spent form-control totalduration"  name="totalduration[]" placeholder="hh:mm" readonly/>
                            </div>
                            <div class="cb"></div>
                    </li>
                    <li>
                        <div class="margin15" style="margin-left:5px;">
                            <input type="checkbox" class="billablecls" id="is_billable1" name="is_billable[]" value="1" checked="checked" />
                            <span style=""><?php echo __("Billable"); ?>?</span>
                        </div>
                    </li>
                    <li id="crsid1" class="crsid" style="display:none;">
                        <a href="javascript:void(0);" onClick="removeUI(1);"><span class="sprite no p-change margin15" ></span></a>
                    </li>
                    <div class="cb"></div>
                </ul>
        </div>
        <div class="plus-btn">
            <a href="javascript:void(0);" onclick="appendnewrow();" style="margin-left:25px;" class="append-new-row">
                    <span style=" font-family: helvetica;color:#2D678D;">+ <?php echo __('Add Item');?></span>
                </a>
        </div>
        <div style="margin:15px 30px;" id="tasklog">
                <label><?php echo __('Note:');?>:</label>
                <div><textarea class="form-control" rows="1" cols="" style="width:100%; resize: none;overflow: hidden;" name="description" id="tskdesc"></textarea></div>
        </div>			
		<div class="log-btn">
                    <button type="button" value="Create&Save" name="crtLogTimeSave" class="btn btn_blue loginactive" id="lgtimebtn" onclick="submitLogTimeform()"><i class="icon-big-tick"></i><span><?php echo __("Save");?></span></button>
			<span style="display:none;margin-right:20px;" id="lgquickloading">
			<img alt="Loading..." title="Loading..." src="<?php echo HTTP_ROOT;?>img/images/case_loader2.gif">
			</span>
                    <span class="or_cancel cancel_on_direct_pj"><?php echo __('or'); ?> <a onclick="closetskPopup();"><?php echo __('Cancel');?></a></span>
		</div>
    </div>
</form>

