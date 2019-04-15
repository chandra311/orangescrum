<style>
    .proj_created_by {height: 36px;overflow-x: auto;}
</style>
<div class="proj_grids">
    <?php 
	$srch_res = '';
	if(isset($_GET['project']) && trim($_GET['project']) && isset($prjAllArr[0]['Project']) && !empty($prjAllArr[0]['Project'])){
	    if($prjAllArr[0]['Project']['name']) {
		$srch_res = ucfirst($prjAllArr[0]['Project']['name']);
	    } else {
		$srch_res = $prjAllArr[0]['Project']['short_name'];
	    }
	}
	
	$active_url = HTTP_ROOT.'projects/manage';
	$inactive_url = $active_url.'/inactive';
        if ($projtype == '') {
        $grid_url = $active_url . '/active-grid';
        $cookie_value = 'active-grid';
    } elseif ($projtype == 'inactive') {
        $grid_url = $active_url . '/inactive-grid';
        $cookie_value = 'inactive-grid';
    }
	if(isset($_GET['proj_srch']) && trim($_GET['proj_srch'])) {
	    $srch_res = $proj_srch = $_GET['proj_srch'];
	    $active_url .="?proj_srch=".$proj_srch;
	    $inactive_url .="?proj_srch=".$proj_srch;
            $grid_url .="?proj_srch=" . $proj_srch;
	}
	?>
    <?php if(trim($srch_res)){ ?>
    <div class="global-srch-res fl"><?php echo __("Search Results for"); ?>: <span><?php echo $srch_res;?></span></div>
    <div class="fl global-srch-rst"><a href="<?php echo HTTP_ROOT.'projects/manage';?>"><?php echo __("Reset"); ?></a></div>
    <div class="cb"></div>
    <?php } ?>

    <!--Tabs section starts -->
    <div class="tab tab_comon">
        <ul class="nav-tabs mod_wide">
            <li <?php if($projtype == '') { ?> class="active" <?php }?>>
                <a href="<?php echo $active_url; ?>" title="<?php echo __("Active"); ?>">
                    <div class="pro_actv fl"></div>
                    <div class="fl ellipsis-view maxWidth120"><?php echo __("Active"); ?><span id="active_proj_cnt" class="counter">(<?php echo $active_project_cnt;?>)</span></div>
                    <div class="cbt"></div>
                </a>
            </li>
            <li <?php if($projtype == 'inactive') { ?> class="active" <?php }?>>
                <a href="<?php echo $inactive_url; ?>" title="<?php echo __("Inactive"); ?>">
                    <div class="pro_inactv fl"></div>
                    <div class="fl ellipsis-view maxWidth120"><?php echo __("Inactive"); ?><span id="inactive_proj_cnt" class="counter">(<?php echo $inactive_project_cnt;?>)</span></div>
                    <div class="cbt"></div>
                </a>
            </li>
            <div class="cbt"></div>
        </ul>
    </div>
    <!--Tabs section ends -->
<?php $count=0; $clas = "";
	$space = 0;
	$spacepercent=0;
	$totCase = 0;
	$totHours = '0.0';
?>
    <div class="cb"></div>
    <div class="col-lg-12 prj_div" style="margin-top:30px">
	<?php if(!empty($prjAllArr[0]) && isset($prjAllArr[0])){} else { ?>
        <div class="col-lg-4">
            <div class="col-lg-12 text-centre">
                <div style="display: block !important;margin-top: 123px;width:103%;">
                    <div class="fnt_clr_rd"><?php if (@SES_TYPE ==3){ ?><?php echo __("You have not created any project yet"); ?>.<?php } else { ?><?php echo __("No projects found."); ?><?php } ?></div>
                </div>
            </div>
        </div>
    <?php } ?>

	<?php //}
	if(count($prjAllArr)){
	$ik=1;
	foreach($prjAllArr as $k=>$prjArr){
	$totUser = !empty($prjArr[0]['totusers']) ? $prjArr[0]['totusers']: '0';
	$totCase = (!empty($prjArr[0]['totalcase'])) ? $prjArr[0]['totalcase']: '0';	
	$totHours = (!empty($prjArr[0]['totalhours'])) ? $prjArr[0]['totalhours']: '0.0';
	if($ik+1%5 == 0){ ?>
        <div class="cb" style="margin:5px;"></div>
	<?php } $ik = $ik++; ?>
        <div class="usr_mcnt fl cmn_bdr_shadow pr cmn_overflow <?php if($projtype == 'inactive' || $projtype == 'inactive-grid') { ?>inactv<?php } ?> "
             data-prjuid="<?php echo $prjArr['Project']['uniq_id']; ?>">
            <div class="usr_top_cnt">
                <div class="usr_act_det">
					<?php if($prjArr['Project']['user_id'] == SES_ID || SES_TYPE <=2){ ?>
                    <div class="mng_sett glyphicon glyphicon-option-vertical" data-toggle="dropdown"></div>
					<?php } ?>
                    <ul class="dropdown-menu mng_dropdown-caret">
                    <?php $prj_name = mb_convert_case($prjArr['Project']['name'], MB_CASE_TITLE, "UTF-8");
						$len = 23;
						$prj_name_shrt = $prj_name;//$this->Format->shortLength($prj_name,$len);
			//			$value_format = $this->Format->formatText($prj_name);
			//			$value_raw = html_entity_decode($value_format, ENT_QUOTES);
						$tooltip =$prj_name;
			//			if(strlen($value_raw) > $len){
			//			    $tooltip = $prj_name;
			//			}
						?>
                            <?php if ($projtype == '') { ?>
                             <?php if($prjArr['Project']['user_id'] == SES_ID || SES_TYPE <=2 ){ 
                                 if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('user')==1){ } else {?>  
                        <li><a href="javascript:void(0);" class="icon-add-usr" data-prj-id="<?php echo $prjArr['Project']['id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-plus-sign "></i><?php echo __("Add User"); ?></a></li>
                        <li id="remove<?php echo $prjArr['Project']['id'];?>">
                                    <?php if(!empty($prjArr[0]['totusers'])){ ?>
                            <a href="javascript:void(0);" class="icon-remove-usr" data-prj-id="<?php echo $prjArr['Project']['id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-minus-sign  "></i><?php echo __("Remove User"); ?></a>
                                    <?php } ?>
                        </li>
                        <li id="ajax_remove<?php echo $prjArr['Project']['id'];?>" style="display:none;">
                            <a href="javascript:void(0);" class="icon-remove-usr" data-prj-id="<?php echo $prjArr['Project']['id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-minus-sign "></i><?php echo __("Remove User"); ?></a>
                        </li>
                                 <?php } ?>
                        <li><a href="javascript:void(0);" class="icon-edit-usr" data-prj-id="<?php echo $prjArr['Project']['uniq_id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-pencil"></i><?php echo __("Edit"); ?></a></li>
                        <li>
								 <?php if($prjArr[0]['totalcase']) { ?>
                            <a href="javascript:void(0);" class="disbl_prj" data-prj-id="<?php echo $prjArr['Project']['id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-ban-circle "></i><?php echo __("Disable"); ?></a>
								<?php } else { ?>
                            <a href="javascript:void(0);" class="del_prj" data-prj-id="<?php echo $prjArr['Project']['uniq_id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-trash "></i><?php echo __("Delete"); ?></a>
								<?php } ?>
                        </li>							
							<?php } ?>
                            <?php 
								}else { ?>
								<?php if($prjArr['Project']['user_id'] == SES_ID || SES_TYPE <=2){ ?>
                        <li><a href="javascript:void(0);" class="enbl_prj" data-prj-id="<?php echo $prjArr['Project']['id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-ok-circle "></i><?php echo __("Enable"); ?></a></li>
                        <li><a href="javascript:void(0);" class="del_prj" data-prj-id="<?php echo $prjArr['Project']['uniq_id'];?>" data-prj-name="<?php echo $prj_name;?>"><i class="glyphicon glyphicon-trash "></i><?php echo __("Delete"); ?></a></li>
								<?php } ?>
                            <?php } ?>                            
                    </ul>


                </div>
                <div class="user_img user_img_proj shrt_bck" <?php if(strlen($prjArr['Project']['short_name']) > 7){ ?> style="width:103px" <?php } ?> >
                    <h4><?php echo __(($prjArr['Project']['short_name'])); ?></h4>

                </div>
                <div class="proj_created_by">
					<?php
					$locDT = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $prjArr['Project']['dt_created'], "datetime");
					$gmdate = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
					$dateTime = $this->Datetime->dateFormatOutputdateTime_day($locDT, $gmdate, 'time');
					?>
					<?php 
						$mix_nams = $user_names[$prjArr['Project']['user_id']];
						if($mix_nams != ''){
							$mix_nams = __('Created by '.$mix_nams.' on '.$dateTime);
						}else{
							$mix_nams = __('Created').' '. __('on').' '. $dateTime;
						}
						echo $mix_nams; ?>
                </div>

                <div class="top_projn">
                    <h3 class="prj_name ellipsis-view"><a href="<?php echo HTTP_ROOT."dashboard/?project=".$prjArr['Project']['uniq_id'];?>" rel="tooltip" title="<?php echo $tooltip;?>"><?php echo $prj_name_shrt;?>&nbsp;</a></h3>
                    <span class="cnt_usr ellipsis-view proj_description_wdth"><?php echo $prjArr['Project']['description']; ?></span>
                </div>
            </div>
            <div class="usr_cnts">
                <ul>
                    <li class="actvty">
                        <span class="cnt_ttl_usr"><?php echo __("Last Activity"); ?></span>
                        <span class="cnt_usr"><?php $getactivity=$this->Casequery->getlatestactivitypid($prjArr['Project']['id'],1);
			if($getactivity==""){
				echo 'No activity';
			}else{
				$curCreated = $this->Tmzone->GetDateTime(SES_TIMEZONE,TZ_GMT,TZ_DST,TZ_CODE,GMT_DATETIME,"datetime");
				$updated = $this->Tmzone->GetDateTime(SES_TIMEZONE,TZ_GMT,TZ_DST,TZ_CODE,$getactivity,"datetime");
				$locDT = $this->Datetime->dateFormatOutputdateTime_day($updated, $curCreated);
				echo $locDT;
			}
			?>
                        </span>
                    </li>
                    <li>
                        <span class="cnt_ttl_usr"><?php echo __("User(s)"); ?></span>
                        <span class="cnt_usr" id="tot_prjusers<?php echo $prjArr['Project']['id']; ?>"><?php echo!empty($prjArr[0]['totusers']) ? $prjArr[0]['totusers'] : '0'; ?></span>
                    </li>
                    <li>
                        <span class="cnt_ttl_usr"><?php echo __("Task(s)"); ?></span>
                        <span class="cnt_usr"><?php echo $totCase; ?></span>
                    </li>
                    <?php if(defined('TSG') && TSG == 1){ ?>
                    <li>
                        <span class="cnt_ttl_usr"><?php echo __("Task Status Group"); ?></span>
                       <?php if($prjArr['Project']['workflow_id'] != 0){ ?>
                        <span class="cnt_usr ellipsis-view"><b><a href="<?php echo HTTP_ROOT; ?>Task-Status-Group" title="<?php echo $prjArr['0']['wname']; ?>" ><?php echo $prjArr['0']['wname']; ?></a></b></span>
                       <?php } else{ ?>
                        <span class="cnt_usr ellipsis-view"><b><a href="<?php echo HTTP_ROOT; ?>Task-Status-Group" title="<?php echo __("Default Task Status Group"); ?>"><?php echo __("Default Task Status Group"); ?></a></b></span>
                      <?php }?>
                    </li>
                    <?php } ?>
                    <li>
                        <span class="cnt_ttl_usr"><?php echo __("Storage"); ?></span>
                        <?php
					$filesize = 0;
					if ($totCase && isset($prjArr[0]['storage_used']) && $prjArr[0]['storage_used']) {
					    $filesize = number_format(($prjArr[0]['storage_used'] / 1024), 2);
					    if($filesize != '0.0' || $filesize != '0') {
						$filesize = $filesize;
					    }
					    $space = $space + $filesize;
					}
					
			?>
                        <span class="cnt_usr"><?php echo $filesize;?> mb</span>
                    </li>
                    <li>
                        <span class="cnt_ttl_usr"><?php echo __("Hour(s)"); ?></span>
                        <span class="cnt_usr"><?php echo (!empty($prjArr[0]['totalhours']) && ($prjArr[0]['totalhours'] != '0.0' || $prjArr[0]['totalhours'] != '0')) ? $prjArr[0]['totalhours'] : '0'; ?></span>
                    </li>

                </ul>
            </div>
        </div>
	<?php } ?>
	<?php } ?>
    </div>
    <div class="cb"></div>
<?php if($caseCount){?>
    <table cellpadding="0" cellspacing="0" border="0" align="right">
        <tr>
            <td align="center" style="padding-top:5px;padding-right:35px;">
                <div class="show_total_case" style="font-weight:normal;color:#000;font-size:12px;">
				<?php echo  $this->Format->pagingShowRecords($caseCount,$page_limit,$casePage); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center">
                <ul class="pagination" style="padding-right:35px;">
		<?php $page = $casePage;
			if($page_limit < $caseCount){
				$numofpages = $caseCount / $page_limit;
				if(($caseCount % $page_limit) != 0){
					$numofpages = $numofpages+1;
				}
				$lastPage = $numofpages;
				$k = 1;
				$data1 = "";
				$data2 = "";
				if($numofpages > 5){
					$newmaxpage = $page+2;
					if($page >= 3){
						$k = $page-2;
						$data1 = "...";
					}
					if(($numofpages - $newmaxpage) >= 2){
						if($data1){
							$data2 = "...";
							$numofpages = $page+2;
						}else{
							if($numofpages >= 5){
								$data2 = "...";
								$numofpages = 5;
							}
						}
					}
				}
				if($data1){
                 if($projtype == 'inactive'){
                     echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=1' class=\"button_act\">&laquo; ". __("First"). "</a></li>";
		}else{
                     echo "<li><a href='".HTTP_ROOT."projects/manage?page=1' class=\"button_act\">&laquo; ". __("First"). "</a></li>";
                }
					echo "<li class='hellip'>&hellip;</li>";
		    }
				if($page != 1){
					$pageprev = $page-1;
                if($projtype == 'inactive'){
                     echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=".$pageprev."' class=\"button_act\">&lt;&nbsp;". __("Prev"). "</a></li>";
                }else{
                     echo "<li><a href='".HTTP_ROOT."projects/manage?page=".$pageprev."' class=\"button_act\">&lt;&nbsp;". __("Prev"). "</a></li>";
                }
				}else{
					echo "<li><a href='javascript:jsVoid();' class=\"button_prev\" style=\"cursor:text\">&lt;&nbsp;". __("Prev"). "</a></li>";
				}
				for($i = $k; $i <= $numofpages; $i++){
					if($i == $page) {
						echo "<li><a href='javascript:jsVoid();' class=\"button_page\" style=\"cursor:text\">".__($i, true)."</a></li>";
					}else {
                     if($projtype == 'inactive'){
                          echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=".$i."' class=\"button_act\" >".__($i, true)."</a></li>";
                     }else{
                          echo "<li><a href='".HTTP_ROOT."projects/manage?page=".$i."' class=\"button_act\" >".__($i, true)."</a></li>";
                     }
					}
				}
				if(($caseCount - ($page_limit * $page)) > 0){
					$pagenext = $page+1;
                if($projtype == 'inactive'){
                     echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=".$pagenext."' class=\"button_act\" >". __("Next"). "&nbsp;&gt;</a></li>";
                }else{
                     echo "<li><a href='".HTTP_ROOT."projects/manage?page=".$pagenext."' class=\"button_act\" >". __("Next"). "&nbsp;&gt;</a></li>";
                }                                             
				}else{
                if($projtype == 'inactive'){
                     echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=".$pagenext."' class=\"button_prev\">". __("Next"). "&nbsp;&gt;</a></li>";
                }else{
                     echo "<li><a href='".HTTP_ROOT."projects/manage?page=".$pagenext."' class=\"button_prev\">". __("Next"). "&nbsp;&gt;</a></li>";
                }
				}
				if($data2){
					echo "<li class='hellip'>&hellip;</li>";
                if($projtype == 'inactive'){
                     echo "<li><a href='".HTTP_ROOT."projects/manage/inactive?page=".floor($lastPage)."' class=\"button_act\" >". __("Last"). " &raquo;</a></li>";
                }else{
                     echo "<li><a href='".HTTP_ROOT."projects/manage?page=".floor($lastPage)."' class=\"button_act\" >". __("Last"). " &raquo;</a></li>";
                }
				}
			} ?>
                </ul>
            </td>
        </tr>
    </table>
<?php }	?>
<?php if(defined('CR') && CR == 1 && SES_CLIENT ==1 && $this->Format->get_client_permission('project')==1){ 
    /**Not Show create project */
}else{?> 
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
<div class="cb"></div>
<input type="hidden" id="getcasecount" value="<?php echo $caseCount; ?>" readonly="true"/>
<input type="hidden" id="totalcount" name="totalcount" value="<?php echo $count; ?>"/>

