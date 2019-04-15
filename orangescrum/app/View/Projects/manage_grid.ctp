<link rel="stylesheet"  type="text/css" href="<?php echo HTTP_ROOT; ?>css/demo_page.css">
<link rel="stylesheet"  type="text/css" href="<?php echo HTTP_ROOT; ?>css/demo_table.css">
<link rel="stylesheet"  type="text/css" href="<?php echo HTTP_ROOT; ?>css/betauser.css">
<script type="text/javascript" src="<?php echo JS_PATH . 'datatable.js'; ?>"></script>
<script type="text/javascript" src="<?php echo JS_PATH . 'betauser.js'; ?>"></script>
<style type="text/css">
    .table.table-condensed.table-hover.table-striped.m-list-tbl.dataTable{width:100%}
    .task_listing table.table td.text-center, .task_listing table.table th.text-center, table.table td.text-center, table.table th.text-center {text-align: center;}
    .task_listing table.table th, .cmn-popup table.table th {font-weight: normal;color: #727272;font-size: 14px;vertical-align: middle;border-bottom: 2px solid #ccc;text-align: left;background: #fff;}
    .task_listing table.table td, .cmn-popup table.table td {border: 0px;vertical-align: middle;color: #888;font-size: 14px;text-align: left;}
    ul.dropdown-menu li a{text-align: left}
    ul.dropdown-menu li a .glyphicon{margin-right:5px;margin-left:5px}
    .pop_arrow_new{margin-top: -12px}
</style>
<?php
$count = 0;
$clas = "";
$space = 0;
$spacepercent = 0;
$totCase = 0;
$totHours = '0.0';
?>
<div class=" task_lis_page">
    <div class="task_listing">
        <div class="proj_grids glide_div" id="project_grid_div">
            <div class="loader_bg" id="projectLoader"><div class="loader-wrapper md-preloader md-preloader-warning"><svg version="1.1" height="48" width="48" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="25" stroke-width="4"></circle></svg></div></div>
            <div class="utilization_filter_msg" data-column-id="filter_msg" style="display:none;"></div>
            <div class="m-cmn-flow">
                <table id="grid-keep-selection" class="table table-condensed table-hover table-striped m-list-tbl">
                    <thead>
                        <tr>
                            <th class="text-center tophead manage-list-th1">&nbsp;</th>
                            <th class="tophead manage-list-th2">Project Name</th>
                            <th class="text-center tophead manage-list-th3">Short Name</th>
                            <th class="text-center tophead manage-list-th4">Product Type</th>
                            <th class="text-center tophead manage-list-th6">#Tasks</th>
                            <th class="text-center tophead manage-list-th7">#Users</th>
                            <th class="text-center tophead manage-list-th8">Storage<br />Used(Mb)</th>
                            <th class="text-center tophead manage-list-th9">Hour(s)<br />Spent</th>
                            <th class=" tophead manage-list-th10">Last Activity</th>
							
                        </tr>
                    </thead>
                    <?php
                    //}
                    if (count($prjAllArr)) {
                        foreach ($prjAllArr as $k => $prjArr) {
                            $totUser = !empty($prjArr[0]['totusers']) ? $prjArr[0]['totusers'] : '0';
                            $totCase = (!empty($prjArr[0]['totalcase'])) ? $prjArr[0]['totalcase'] : '0';
                            $totHours = (!empty($prjArr[0]['totalhours'])) ? $prjArr[0]['totalhours'] : '0';
                            ?>
                            <?php
                            $prj_name = ucwords(trim($prjArr['Project']['name']));
                            $len = 15;
                            $prj_name_shrt = $this->Format->shortLength($prj_name, $len);
                            $value_format = $this->Format->formatText($prj_name);
                            $value_raw = html_entity_decode($value_format, ENT_QUOTES);
                            $tooltip = '';
                            if (strlen($value_raw) > $len) {
                                $tooltip = $prj_name;
                            }
                            if ($prjArr['Project']['isactive'] == 1 && $prjArr['Project']['status'] == 1) {
                                $sts_txt = 'Started';
                            } else if ($prjArr['Project']['isactive'] == 1 && $prjArr['Project']['status'] == 2) {
                                $sts_txt = 'Hold on';
                            } else if ($prjArr['Project']['isactive'] == 1 && $prjArr['Project']['status'] == 3) {
                                $sts_txt = 'Stack';
                            } else if ($prjArr['Project']['isactive'] == 2) {
                                $sts_txt = 'Completed';
                            }
                            ?>
                            <tr class="row_tr prjct_lst_tr">
                                <td class="text-center">
                                    <div class="dropdown">
                                        <div data-toggle="dropdown" class="sett dropdown-toggle" ></div>
                                        <?php if (SES_TYPE == 1 || SES_TYPE == 2 || (SES_TYPE == 3 && $prjArr['Project']['user_id'] == SES_ID)) { ?>
                                            <ul class="dropdown-menu " >
                                                <li class="pop_arrow_new"></li>
                                                <?php if ($projtype == 'active-grid') { ?>
                                                    <li><a href="javascript:void(0);" class="icon-add-usr" data-prj-id="<?php echo $prjArr['Project']['id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-plus-sign "></i><?php echo __("Add User"); ?></a></li>
                                                    <li id="remove<?php echo $prjArr['Project']['id']; ?>">
                                                        <?php if (!empty($prjArr[0]['totusers'])) { ?>
                                                            <a href="javascript:void(0);" class="icon-remove-usr" data-prj-id="<?php echo $prjArr['Project']['id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-minus-sign  "></i><?php echo __("Remove User"); ?></a>
                                                        <?php } ?>
                                                    </li>
                                                    <li id="ajax_remove<?php echo $prjArr['Project']['id']; ?>" style="display:none;">
                                                        <a href="javascript:void(0);" class="icon-remove-usr" data-prj-id="<?php echo $prjArr['Project']['id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-minus-sign "></i><?php echo __("Remove User"); ?></a>
                                                    </li>
                                                    <li><a href="javascript:void(0);" class="icon-edit-usr" data-prj-id="<?php echo $prjArr['Project']['uniq_id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-pencil"></i><?php echo __("Edit"); ?></a></li>
                                                    <li>
                                                        <?php if ($prjArr[0]['totalcase']) { ?>
                                                            <a href="javascript:void(0);" class="disbl_prj" data-prj-id="<?php echo $prjArr['Project']['id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-ban-circle "></i><?php echo __("Disable"); ?></a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" class="del_prj" data-prj-id="<?php echo $prjArr['Project']['uniq_id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-trash "></i><?php echo __("Delete"); ?></a>
                                                        <?php } ?>
                                                    </li>
                                                <?php } else { ?>
                                                    <li><a href="javascript:void(0);" class="enbl_prj" data-prj-id="<?php echo $prjArr['Project']['id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-ok-circle "></i><?php echo __("Enable"); ?></a></li>
                                                    <li><a href="javascript:void(0);" class="del_prj" data-prj-id="<?php echo $prjArr['Project']['uniq_id']; ?>" data-prj-name="<?php echo $prj_name; ?>"><i class="glyphicon glyphicon-trash "></i><?php echo __("Delete"); ?></a></li>
                                                <?php } ?>        
                                            </ul>
                                        <?php } else { ?>
                                            <ul class="dropdown-menu" <?php if (SES_TYPE == 3 && $prjArr['Project']['user_id'] != SES_ID) { ?>onclick="notAuthAlert();"<?php } ?>>
                                                <?php
                                                if ($projtype == 'active-grid') {
                                                    if ($prjArr['Project']['isactive'] == 2 || $prjArr['Project']['status'] == 4) {
                                                        ?>
                                                        <li><a href="javascript:void(0);">Not Complete</a></li>
                                                        <li><a href="javascript:void(0);"> Delete</a></li>
                <?php } else { ?>
                                                        <li><a href="javascript:void(0);"> Add User</a></li>
                                                        <li>
                                                            <?php if (!empty($prjArr[0]['totusers'])) { ?>
                                                                <a href="javascript:void(0);"> Remove User</a>
                    <?php } ?>
                                                        </li>
                                                        <li style="display:none;">
                                                            <a href="javascript:void(0);"> Remove User</a>
                                                        </li>
                                                        <li><a href="javascript:void(0);"> Edit</a></li>
                                                        <li>
                                                            <?php if ($prjArr[0]['totalcase']) { ?>
                                                                <a href="javascript:void(0);"> Complete</a>
                                                            <?php } else { ?>
                                                                <a href="javascript:void(0);"> Delete</a>
                                                        <?php } ?>
                                                        </li>
                                                    <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <li><a href="javascript:void(0);"> Not Complete</a>
                                                    </li>
                                                    <li><a href="javascript:void(0);"> Delete</a></li>
                                            <?php } ?>                            
                                            </ul>

        <?php } ?>
                                    </div>
                                </td>
                                <td align="left"><a class="ttl_listing" id="prj_ttl_<?php echo $prjArr['Project']['uniq_id']; ?>" href="<?php echo HTTP_ROOT."dashboard/?project=".$prjArr['Project']['uniq_id'];?>" title="<?php echo $tooltip; ?>" onclick="return projectBodyClick('<?php echo $prjArr['Project']['uniq_id']; ?>');"><?php echo $prj_name_shrt; ?>&nbsp;</a><br />
                                    <span style="color:#8d8d8e;font-size:12px;">
                                        <?php
                                        $locDT = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $prjArr['Project']['dt_created'], "datetime");
                                        $gmdate = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
                                        $dateTime = $this->Datetime->dateFormatOutputdateTime_day($locDT, $gmdate, 'time');
                                        ?>
        <?php echo 'Created by  ' . $this->Format->formatText($p_u_name[$prjArr['Project']['user_id']]); ?> on <?php echo $dateTime; ?>
                                    </span>
                                </td>
                                <td class="text-center"><?php echo (($prjArr['Project']['short_name'])); ?></td>
                                
                                <td class="text-center"><?php echo $totCase; ?></td>
                                <td class="text-center"><?php echo!empty($prjArr[0]['totusers']) ? $prjArr[0]['totusers'] : '0'; ?></td>
                                <td class="text-center">
                                    <?php
                                    $filesize = 0;
                                    if ($totCase && isset($prjArr[0]['storage_used']) && $prjArr[0]['storage_used']) {
                                        $filesize = number_format(($prjArr[0]['storage_used'] / 1024), 2);
                                        if ($filesize != '0.0' || $filesize != '0') {
                                            $filesize = $filesize;
                                        }
                                        $space = $space + $filesize;
                                    }
                                    echo $filesize;
                                    ?>
                                </td>
                                <td class="text-center"><?php echo $totHours > 0 ? $this->format->formatHour($totHours) : $totHours; ?></td>
                                <td>
                                    <?php
                                    $getactivity = $this->Casequery->getlatestactivitypid($prjArr['Project']['id'], 1);
                                    if ($getactivity == "") {
                                        echo 'No activity';
                                    } else {
                                        $curCreated = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
                                        $updated = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $getactivity, "datetime");
                                        $locDT = $this->Datetime->dateFormatOutputdateTime_day($updated, $curCreated);
                                        echo $locDT;
                                    }
                                    ?></td>

                            </tr>
                            <?php
                        }
                    }
                    ?>
                </table>
            </div>
            <div class="loader_bg" id="projectLoader"><div class="loader-wrapper md-preloader md-preloader-warning"><svg version="1.1" height="48" width="48" viewBox="0 0 75 75"><circle cx="37.5" cy="37.5" r="25" stroke-width="4"></circle></svg></div></div>
        </div>
    </div>
    <?php
    if (defined('CR') && CR == 1 && SES_CLIENT == 1 && $this->Format->get_client_permission('project') == 1) {
        /*         * Not Show create project */
    } else {
        ?> 
        <div class="crt_task_btn_btm">
            <div class="os_plus">
                <div class="ctask_ttip">
                    <span class="label label-default">
    <?php echo __("Create New Project"); ?>
                    </span>
                </div>
                <a href="javascript:void(0)" onClick="newProject()">
                    <img class="prjct_icn ctask_icn" src="<?php echo HTTP_ROOT; ?>img/images/project-icn.png"> 
                    <img src="<?php echo HTTP_ROOT; ?>img/images/plusct.png" class="add_icn" />
                </a>
            </div>
        </div>
<?php } ?>
</div>
<script>
    $(document).ready(function () {
        var url = HTTP_ROOT + 'projects/manage/grid';
        $('#grid-keep-selection').dataTable({
            "bAutoWidth": false,
            "aLengthMenu": [[10, 20, 30, 50, 100], [10, 20, 30, 50, 100]],
            "iDisplayLength": 10,
            "iDisplayStart": 0,
            //"aaSorting": [[ 0, "asc" ]],
            "aaSorting": [],
            "sPaginationType": "full_numbers",
            //"aoColumns": [{sWidth:'3%'},{sWidth:'9%'},{sWidth:'8%'},{sWidth:'9%'},{sWidth:'6%'},{sWidth:'6%'},{sWidth:'10%'},{sWidth:'10%'},{sWidth:'5%'},{sWidth:'10%'},{ "bSortable": true,sWidth:'5%'},{ "bSortable": true,sWidth:'12%'}]
            "aoColumnDefs": [
                {'bSortable': false, 'aTargets': [0]}
            ],
            oinit: function () {
                console.log('loaded')
            }
        });
    });
    function notAuthAlert() {
        showTopErrSucc('error', "Oops! You are not authorized to do this operation. Please contact your Admin/Owner.");
    }
    function assignMeToPrj(obj) {
        var loc = HTTP_ROOT + "projects/assignRemovMeToProject/";
        var proj_id = $(obj).attr('data-prj-id');
        var proj_uid = $(obj).attr('data-prj-uid');
        var user_ids = $(obj).attr('data-prj-usr');
        var pname = $(obj).attr('data-prj-name');
        $.post(loc, {
            'user_ids': user_ids,
            'project_id': proj_id,
            'typ': 'as'
        }, function (res) {
            if (res.status == 'nf') {
                showTopErrSucc('error', 'Failed to assign user to the project.');
            } else {
                if (trim(res.message) != '') {
                    showTopErrSucc('success', res.message);
                    $('.assgnremoveme' + proj_uid).html('<a href="javascript:void(0);" class="icon-add-usr" data-prj-uid ="' + proj_uid + '" data-prj-id="' + proj_id + '" data-prj-name="' + pname + '" data-prj-usr="<?php echo SES_ID; ?>" onclick="removeMeFromPrj(this);"><i class="material-icons">&#xE15C;</i> Remove Me From This Project</a>');
                }
            }
        }, 'json');
    }
    function removeMeFromPrj(obj) {
        var loc = HTTP_ROOT + "projects/assignRemovMeToProject/";
        var proj_id = $(obj).attr('data-prj-id');
        var proj_uid = $(obj).attr('data-prj-uid');
        var user_ids = $(obj).attr('data-prj-usr');
        var pname = $(obj).attr('data-prj-name');
        $.post(loc, {
            'user_ids': user_ids,
            'project_id': proj_id,
            'typ': 'rm'
        }, function (res) {
            if (res.status == 'nf') {
                showTopErrSucc('error', 'Failed to assign user to the project.');
            } else {
                if (trim(res.message) != '') {
                    showTopErrSucc('success', res.message);
                    $('.assgnremoveme' + proj_uid).html('<a href="javascript:void(0);" class="icon-add-usr" data-prj-uid ="' + proj_uid + '" data-prj-id="' + proj_id + '" data-prj-name="' + pname + '" data-prj-usr="<?php echo SES_ID; ?>" onclick="assignMeToPrj(this);"><i class="material-icons">&#xE147;</i> Add Me To This Project</a>');
                }
            }
        }, 'json');
    }
</script>