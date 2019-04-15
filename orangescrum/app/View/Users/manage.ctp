<input type="hidden" id="role" value="<?php echo $role;?>">
<input type="hidden" id="type" value="<?php echo $type;?>">
<input type="hidden" id="user_srch" value="<?php echo $user_srch;?>">
<div class="proj_grids">
	<?php 
	$srch_res = '';
	if(isset($_GET['user']) && trim($_GET['user']) && isset($userArr['0']['User']) && !empty($userArr['0']['User'])){
	    if($userArr['0']['User']['name']) {
		$srch_res = ucfirst($userArr['0']['User']['name'])." ".ucfirst($userArr['0']['User']['last_name']);
	    } else {
		$srch_res = $userArr['0']['User']['email'];
	    }
	}
	
	if(isset($user_srch) && trim($user_srch)) {
	    $srch_res = $user_srch;
	}
	?>
    <?php if(trim($srch_res)){ ?>
    <div class="global-srch-res fl"><?php echo __("Search Results for"); ?>: <span><?php echo $srch_res;?></span></div>
    <div class="fl global-srch-rst"><a href="<?php echo HTTP_ROOT.'users/manage';?>"><?php echo __("Reset"); ?></a></div>
	<div class="cb"></div>
    <?php } ?>
<div class="tab tab_comon tab_task">
        <ul class="nav-tabs mod_wide">
	    <li id="task_li" <?php if($role == '' || $role == 'all'){?>class="active" <?php }?>>
                <a href="javascript:void(0);" onclick="filterUserRole('all','<?php echo $user_srch;?>');" title="<?php echo __("Active"); ?>">
                <div class="usrr_actv fl"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Active"); ?><span class="counter">(<?php echo $active_user_cnt;?>)</span></div>
                <div class="cbt"></div>
                </a>
            </li>
            <li id="file_li" <?php if($role == 'invited'){?>class="active" <?php }?>>
                <a href="javascript:void(0);" onclick="filterUserRole('invited','<?php echo $user_srch;?>');" title="<?php echo __("Invited"); ?>">
                <div class="usrr_invt fl "></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Invited"); ?><span class="counter">(<?php echo $invited_user_cnt;?>)</span></div>
                <div class="cbt"></div>
                </a>
            </li>
            <li id="task_li" <?php if($role == 'disable'){?>class="active" <?php }?>>
                <a href="javascript:void(0);" onclick="filterUserRole('disable','<?php echo $user_srch;?>');" title="<?php echo __("Disabled"); ?>">
                <div class="usrr_disbl fl "></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Disabled"); ?><span class="counter">(<?php echo $disabled_user_cnt;?>)</span></div>
                <div class="cbt"></div>
                </a>
            </li>
            <?php if(defined('INV') && INV == 1){ /*?>
            <li id="task_li" <?php if($role == 'clients'){?>class="active" <?php }?>>
                <a href="javascript:void(0);" onclick="filterUserRole('clients','<?php echo $user_srch;?>');" title="<?php echo __("Clients"); ?>">
                <div class="usrr_disbl fl"></div>
                <div class="fl ellipsis-view maxWidth120"><?php echo __("Clients"); ?><span class="counter">(<?php echo $totalclients;?>)</span></div>
            <div class="cbt"></div>
                </a>
            </li>
            <?php */ } ?>
            <div class="cbt"></div>
        </ul>
    </div>
    
    
<div class="col-lg-12 user_div m-left-20">
    <?php 
	$count = 1;
	$is_invited_user = 0;
	if ($role == 'invited') {
	    $is_invited_user = 1;
	}
	foreach($userArr as $user) { 
		if ($user['CompanyUser']['user_type'] == 1) {
		    $colors = 'user-owner';
		    $usr_typ_name = __('Owner', true);
		} else if ($user['CompanyUser']['user_type'] == 2) {
		    $colors = 'user-admin';
		    $usr_typ_name = __('Admin', true);
		} else if ($user['CompanyUser']['user_type'] == 3 && $role != 3) {
		    $colors = 'user-usr';
		    $usr_typ_name = __('User', true);
		}
		
		if ($role == 'invited') {
		    $colors = 'user-usr';
		    $usr_typ_name = __('User', true);
		}
    if(defined('CR') && CR==1){            
    if($user['CompanyUser']['is_client'] == 1){
			$colors = 'cli_clr';
			$usr_typ_name = __('Client', true);
		}
		if($user['CompanyUser']['is_client'] == 1 && $user['CompanyUser']['user_type'] == 2){
			$colors = 'cli_clr';
			$usr_typ_name = __('Admin/Client', true);
		}
   }             
		?>
    <div class="usr_mcnt fl cmn_bdr_shadow">	
							
		<div class="usr_top_cnt">
			<div class="usr_cat <?php echo $colors;?>"><?php echo $usr_typ_name;?></div>
			<div class="usr_act_det">
				<div class="mng_sett glyphicon glyphicon-option-vertical" data-toggle="dropdown"></div>
                <ul class="dropdown-menu mng_dropdown-caret usr_mng">
					
					<?php if ($user['CompanyUser']['user_type'] == 1 ) { ?>
						  <li><a class="icon-assign-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>"><i class="glyphicon glyphicon-paste "></i> <?php echo __("Assign Project"); ?></a></li>
						  <li><input id="rmv_allprj_<?php echo $user['User']['id'];?>" type="hidden" value="<?php echo $user['User']['all_projects'];?>"/>
						<a id="rmv_prj_<?php echo $user['User']['id'];?>" class="icon-remprj-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>" data-total-project="<?php echo $user['User']['total_project'];?>" <?php if($user['User']['all_project'] == ''){ ?> style="display:none;" <?php } ?>><i class="glyphicon glyphicon-minus-sign "></i> <?php echo __("Remove Project"); ?></a></li>
					 <?php }else{ ?>
						 <?php if($role == 'invited'){ ?>
							<li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<a class="icon-assign-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>"><i class="glyphicon glyphicon-paste "></i> <?php echo __("Assign Project"); ?></a>
							<input id="rmv_allprj_<?php echo $user['User']['id'];?>" type="hidden" value="<?php echo $user['User']['all_projects'];?>"/>
							<span id="rmv_prj_<?php echo $user['User']['id'];?>" <?php if($user['User']['all_project'] == ''){ ?> style="display:none;"<?php } ?>></span>
						  </li>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<a class="icon-remprj-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>" data-total-project="<?php echo $user['User']['total_project'];?>"><i class="glyphicon glyphicon-minus-sign "></i> <?php echo __("Remove Project"); ?></a>
						  </li>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<a class="icon-delete-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?del=<?php echo urlencode($user['User']['uniq_id']); ?>&role=<?php echo $_GET['role']; ?>" Onclick="return confirm('Are you sure you want to delete \'<?php echo $user['User']['email']; ?>\' ?')"><i class="glyphicon glyphicon-remove-circle "></i> <?php echo __("Delete"); ?></a>
						  </li>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">				  
							<a class="icon-resend-usr" href="javascript:void(0);" onclick="return resend_invitation('<?php echo $user['User']['qstr']; ?>','<?php echo $user['User']['email']; ?>');"><i class="glyphicon glyphicon-envelope "></i>  <?php echo __("Resend"); ?></a>
						  </li>
						  <?php }else if($role == 'disable'){ ?>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<a class="icon-enable-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?act=<?php echo urlencode($user['User']['uniq_id']); ?>&role=<?php echo $_GET['role']; ?>" Onclick="return confirm('<?php echo __("Are you sure you want to enable"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-ok-sign"></i> <?php echo __("Enable"); ?></a>
						  </li>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<input id="rmv_allprj_<?php echo $user['User']['id'];?>" type="hidden" value="<?php echo $user['User']['all_projects'];?>"/>
							<a id="rmv_prj_<?php echo $user['User']['id'];?>" class="icon-remprj-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>" data-total-project="<?php echo $user['User']['total_project'];?>" <?php if($user['User']['all_project'] == ''){ ?> style="display:none;" <?php } ?>><i class="glyphicon glyphicon-minus-sign "></i> <?php echo __("Remove Project"); ?></a>
						  </li>
						    <?php }else if($role == 'client' && defined('INV') && INV == 1){ ?>
								
									<li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
									<a class="icon-enable-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?act=<?php echo urlencode($user['User']['uniq_id']); ?>&role=<?php echo $_GET['role']; ?>" Onclick="return confirm('<?php echo __("Are you sure you want to enable"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-ok-sign"></i> <?php echo __("Enable"); ?></a>
									</li>
									<li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
									<input id="rmv_allprj_<?php echo $user['User']['id'];?>" type="hidden" value="<?php echo $user['User']['all_projects'];?>"/>
									<a id="rmv_prj_<?php echo $user['User']['id'];?>" class="icon-remprj-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>" data-total-project="<?php echo $user['User']['total_project'];?>" <?php if($user['User']['all_project'] == ''){ ?> style="display:none;" <?php } ?>><i class="glyphicon glyphicon-minus-sign "></i>  <?php echo __("Remove Project"); ?></a>
									</li>
                                                                        <li><a class="icon-disable-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?deact=<?php echo urlencode($user['User']['uniq_id']); ?>" Onclick="return confirm('<?php echo __("Are you sure you want to disable"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-ban-circle "></i><?php echo __("Disable"); ?></a></li>
                                                                       <!----- Client Restriction---->
                                                                      <?php if(defined('CR') && CR == 1){ ?> 
                                                                       <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
                                                                             <?php if($user['CompanyUser']['is_client'] == '0'){  ?>
                                                                             <a class="icon-client-usr" href="<?php echo HTTP_ROOT;?>users/manage/?grant_client=<?php echo urlencode($user['User']['uniq_id']); ?>" onclick="return confirm('<?php echo __("Are you sure you want to mark"); ?> \'<?php echo ucfirst($user['User']['name']); ?>\' <?php echo __("as client"); ?> ?')"><i class="glyphicon glyphicon-user"></i> <?php echo __("Mark Client"); ?></a>
                                                                             <?php } else {?>
                                                                             <a class="icon-revclient-usr" href="<?php echo HTTP_ROOT;?>users/manage/?revoke_client=<?php echo urlencode($user['User']['uniq_id']); ?>" onclick="return confirm('<?php echo __("Are you sure you want to revoke client access from"); ?> \'<?php echo ucfirst($user['User']['name']); ?>\' ?')"><i class="glyphicon glyphicon-user"></i> <?php echo __("Revoke Client"); ?> </a>
                                                                             <?php } ?>
                                                                       </li>
                                                                      <?php } ?>
                                                                       <!-- End-->
                                                                        
                                                                        <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">				  
                                                                            <a class="icon-resend-usr" href="javascript:void(0);" onclick="return resend_invitation('<?php echo $user['User']['qstr']; ?>','<?php echo $user['User']['email']; ?>');"><i class="glyphicon glyphicon-envelope "></i>  <?php echo __("Resend"); ?></a>
                                                                        </li>
                                                    <?php	}
								else{?>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<a class="icon-assign-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>"><i class="glyphicon glyphicon-paste "></i>  <?php echo __("Assign Project"); ?></a>
						  </li>
						  <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
							<input id="rmv_allprj_<?php echo $user['User']['id'];?>" type="hidden" value="<?php echo $user['User']['all_projects'];?>"/>
							<a class="icon-remprj-usr" href="javascript:void(0);" data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['name'];?>" data-total-project="<?php echo $user['User']['total_project'];?>"><i class="glyphicon glyphicon-minus-sign "></i> <?php echo __("Remove Project"); ?></a>
							<span id="rmv_prj_<?php echo $user['User']['id'];?>" <?php if($user['User']['all_project'] == ''){ ?> style="display:none;"<?php } ?>></span>
						  </li>
                                                  <li><a class="icon-disable-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?deact=<?php echo urlencode($user['User']['uniq_id']); ?>" Onclick="return confirm('<?php echo __("Are you sure you want to disable"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-ban-circle "></i><?php echo __("Disable"); ?></a></li>
						 
                                                  <!----- Client Restriction---->
                                                  <?php if(defined('CR') && CR == 1){ ?> 
                                                   <li data-usr-id="<?php echo $user['User']['id'];?>" data-usr-name="<?php echo $user['User']['email'];?>">
                                                        <?php if($user['CompanyUser']['is_client'] == '0'){  ?>
                                                        <a class="icon-client-usr" href="<?php echo HTTP_ROOT;?>users/manage/?grant_client=<?php echo urlencode($user['User']['uniq_id']); ?>" onclick="return confirm('<?php echo __("Are you sure you want to mark"); ?> \'<?php echo ucfirst($user['User']['name']); ?>\' <?php echo __("as client"); ?> ?')"><i class="glyphicon glyphicon-user"></i> <?php echo __("Mark Client"); ?></a>
                                                        <?php } else {?>
                                                        <a class="icon-revclient-usr" href="<?php echo HTTP_ROOT;?>users/manage/?revoke_client=<?php echo urlencode($user['User']['uniq_id']); ?>" onclick="return confirm('<?php echo __("Are you sure you want to revoke client access from"); ?> \'<?php echo ucfirst($user['User']['name']); ?>\' ?')"><i class="glyphicon glyphicon-user"></i> <?php echo __("Revoke Client"); ?></a>
                                                        <?php } ?>
						  </li>
                                                   <?php } ?>
                                                  <!-- End-->
                                                  <li>
							<?php if($istype == 1){ ?>
								<?php if ($user['CompanyUser']['user_type'] == 2){ ?>
								<a class="icon-revadmin-usr" href="<?php echo HTTP_ROOT; ?>users/manage/?revoke_admin=<?php echo urlencode($user['User']['uniq_id']); ?>" Onclick="return confirm('<?php echo __("Are you sure you want to revoke Admin privilege from"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-minus "></i><?php echo __("Revoke Admin"); ?></a>
                                                                <?php } else { ?>
								<a class="icon-admin-usr " href="<?php echo HTTP_ROOT; ?>users/manage/?grant_admin=<?php echo urlencode($user['User']['uniq_id']); ?>" Onclick="return confirm('<?php echo __("Are you sure you want to grant Admin privilege to"); ?> \'<?php echo $user['User']['name']; ?>\' ?')"><i class="glyphicon glyphicon-plus "></i><?php echo __("Grant Admin"); ?></a>
								<?php } ?><?php } ?>
						  </li>
						  <?php  } ?>
					 <?php } ?>			  
					</ul>
				</span>
			</div>
			<?php $random_bgclr = $this->Format->getProfileBgColr($user['User']['id']); ?>			
			<div class="user_img holder <?php echo $random_bgclr; ?>">
				<?php if(trim($user['User']['photo'])) {?>
					<img class="lazy" data-original="<?php echo HTTP_ROOT; ?>users/image_thumb/?type=photos&file=<?php echo $user['User']['photo']; ?>&sizex=94&sizey=94&quality=100" width="64" height="64" style="border-radius:50%"/>
				<?php } else { ?>
					<?php 
						if(isset($user['User']['name']) && trim($user['User']['name'])) { ?>
						<span class="name_txt"><?php echo mb_substr(trim($user['User']['name']),0,1, "utf-8"); ?></span>
						<?php }else if(isset($user['User']['short_name']) && trim($user['User']['short_name'])){
							echo mb_substr(trim($user['User']['short_name']),0,1, "utf-8");
						}else{ ?>
							<?php /*<img src="<?php echo HTTP_ROOT; ?>img/images/user.png" /> */ ?>
                            <span class="name_txt"><?php echo mb_substr(trim($user['User']['email']),0,1, "utf-8"); ?></span>
						<?php } ?>
				<?php } ?>									
			</div>
			<h3 class="invite_user_cls ellipsis-view"  rel="tooltip" title="<?php echo ucfirst($user['User']['name']); ?>" data-usr-id="<?php echo $user['User']['id']; ?>" data-usr-name="<?php echo trim($user['User']['name']); ?>"><?php if(isset($user['User']['name']) && trim($user['User']['name'])) {echo trim($user['User']['name']); } else { echo "&nbsp;";} ?></h3>
			<h4><?php echo $user['User']['short_name']; ?></h4>
		</div>
		
		
		<div class="usr_cnts">
			<ul>
				<li>
					<span class="cnt_ttl_usr"><?php echo __("Last Activity"); ?></span>
					<span class="cnt_usr">
						<?php
						if ($user['CompanyUser']['is_active'] == 0 && $_GET['role'] == 'invited') {
						$activity = "<span class='fnt_clr_rd'>". __("Invited"). "</span>";
						}else if ($_GET['role'] == 'recent') {
						if($user['User']['is_active'] == 2){
							$activity = "<span class='fnt_clr_rd'>". __("Invited"). "</span>";
						}else if(($istype == 1 || $istype == 2) && !$user['User']['dt_last_login']) {
							$activity = "<span class='fnt_clr_rd'>". __("No activity yet"). "</span>";
						}else if($user['User']['dt_last_login']){
							$activity = $user['User']['latest_activity'];
						}
						}else {
						if ($user['User']['dt_last_login']) {
							$activity = $user['User']['latest_activity'];
						} elseif ($user['CompanyUser']['is_active']) {
						}
						if(($istype == 1 || $istype == 2) && !$user['User']['dt_last_login']) {
							if($user['CompanyUser']['is_active'] == 2){
							$activity = "<span class='fnt_clr_rd'>". __("Invited"). "</span>";
							}else{
							$activity = "<span class='fnt_clr_rd'>". __("No activity yet"). "</span>";
							}
						}
						} 
						echo $activity;
						?>											
					</span>
				</li>
				<li>
					<span class="cnt_ttl_usr"><?php echo __("Created"); ?></span>
					<span class="cnt_usr">
						<?php
							if ($role == "invited") {
							$crdt = $user['UserInvitation']['created'];
							} else if ($role == "recent") {
							$crdt = $user['User']['created'];	 
							}else{
							$crdt = $user['CompanyUser']['created'];
							}
							if ($crdt != "0000-00-00 00:00:00") {
								echo $user['User']['created_on'];
							} ?>
					</span>
				</li>
				<li>
					<span class="usr_email cnt_ttl_usr"><?php echo __("Email"); ?></span>
					<span class="cnt_usr" title="<?php echo $user['User']['email']; ?>">
					<?php 
					$email = $this->Format->shortLength($user['User']['email'],25);
					echo $email; ?></span>
				</li>
				<li>
					<span class="cnt_ttl_usr"><?php echo __("Projects"); ?></span>
					<span id="remain_prj_<?php echo $user['User']['id'];?>" class="cnt_usr nm_usr nm_prj_mx_width ellipsis-view" title="<?php echo $user['User']['all_project_lst']; ?>">
						<?php if(isset($user['User']['all_project']) && trim($user['User']['all_project'])) { 	echo $user['User']['all_project'];
						} else { echo 'N/A'; }
						?>
					</span>
				</li>
			</ul>
		</div>
	</div>
    <?php $count++;
		} ?>
    <input type="hidden" id="is_invited_user" value="<?php echo $is_invited_user;?>" />
    
   <?php //} 
   if(!isset($userArr) || empty($userArr)){ 
   	$style = ($role == 'clients') ? "margin-left:380px;" : "";
   	?>
	<div class="col-lg-4">
		<div class="col-lg-12 text-centre">
		    <div style="display: block !important;margin-top: 123px;width:94%;<?php echo $style;?>">
			<div class="fnt_clr_rd"><?php echo __("No users found."); ?></div>
			</div>
		</div>
	</div>
    <?php } ?>
</div>
    
<div class="cbt"></div>
<input type="hidden" id="getcasecount" value="<?php echo $caseCount; ?>" readonly="true"/>
<?php if ($caseCount) { ?>
<div class="tot-cs fr">
    <div class="sh-tot-cs">
	<?php echo $this->Format->pagingShowRecords($caseCount, $page_limit, $casePage); ?>
    </div>
    <div class="pg-ntn">
	<ul class="pagination">
	    <?php
	    $page = $casePage;
	    if ($page_limit < $caseCount) {
		$numofpages = $caseCount / $page_limit;
		if (($caseCount % $page_limit) != 0) {
		    $numofpages = $numofpages + 1;
		}
		$lastPage = $numofpages;
		$k = 1;
		$data1 = "";
		$data2 = "";
		if ($numofpages > 5) {
		    $newmaxpage = $page + 2;
		    if ($page >= 3) {
			$k = $page - 2;
			$data1 = "...";
		    }
		    if (($numofpages - $newmaxpage) >= 2) {
			if ($data1) {
			    $data2 = "...";
			    $numofpages = $page + 2;
			} else {
			    if ($numofpages >= 5) {
				$data2 = "...";
				$numofpages = 5;
			    }
			}
		    }
		}
		if ($data1) {
		    echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=1' class=\"button_act\" >&laquo; ".__('First')."</a></li>";
		    echo "<li class='hellip'>&hellip;</li>";
		}
		if ($page != 1) {
		    $pageprev = $page - 1;
		    echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=" . $pageprev . "' class=\"button_act\">&lt;&nbsp;".__('Prev')."</a></li>";
		} else {
		    echo "<li><a href='javascript:jsVoid();' class=\"button_prev\" style=\"cursor:text\">&lt;&nbsp;".__('Prev')."</a></li>";
		}
		for ($i = $k; $i <= $numofpages; $i++) {
		    if ($i == $page) {
			echo "<li><a href='javascript:jsVoid();' class=\"button_page\" style=\"cursor:text\">" . __($i, true) . "</a></li>";
		    } else {
			echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=" . $i . "' class=\"button_act\" >" . __($i, true) . "</a></li>";
		    }
		}
		if (($caseCount - ($page_limit * $page)) > 0) {
		    $pagenext = $page + 1;
		    echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=" . $pagenext . "' class=\"button_act\" >".__('Next')."&nbsp;&gt;</a></li>";
		} else {
		    echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=" . $pagenext . "' class=\"button_prev\">".__('Next')."&nbsp;&gt;</a></li>";
		}
		if ($data2) {
		    echo "<li class='hellip'>&hellip;</li>";
		    echo "<li><a href='" . HTTP_ROOT . "users/manage/?role=" . $this->params['url']['role'] . "&type=" . $this->params['url']['type'] . "&user_srch=" . $this->params['url']['user_srch'] . "&page=" . floor($lastPage) . "' class=\"button_act\" >".__('Last')." &raquo;</a></li>";
		}
	    }
	    ?>
	    </ul>
	</div>
    </div>
<?php } ?>
<input type="hidden" id="totalcount" name="totalcount" value="<?php echo $count; ?>"/>
</div>
<div id="projectLoader">
    <div class="loadingdata"><?php echo __("Sending invitation again"); ?>...</div>
</div>
<div class="crt_task_btn_btm">
    <div class="os_plus">
        <div class="ctask_ttip">
            <span class="label label-default">
				<?php echo __("Add New User"); ?>
            </span>
        </div>
        <a href="javascript:void(0)" onClick="newUser()">
            <img src="<?php echo HTTP_ROOT; ?>img/images/add_usr.png" class="prjct_icn ctask_icn" />
            <img src="<?php echo HTTP_ROOT; ?>img/images/plusct.png" class="add_icn" />
        </a>
    </div>
</div>