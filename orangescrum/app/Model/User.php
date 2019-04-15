<?php
class User extends AppModel{
	public $name = 'User';
	//var $actsAs = array('Global');
        public $virtualFields = array( 'full_name' => 'CONCAT(User.name, " ", User.last_name)');
	public $hasAndBelongsToMany = array(
        'Project' =>
            array(
                'className'              => 'Project',
                'joinTable'              => 'project_users',
                'foreignKey'             => 'user_id',
                'associationForeignKey'  => 'project_id',
				'order' => 'Project.company_id ASC'
            )
    );
/**
 * @method public Get_billing_info(int account_id)
 * @author Andola Dev <support@andolacrm.com>
 */
 	public function beforeSave($options = array()) {
		if(trim($this->data['User']['name'])) {
			$this->data['User']['name'] = htmlentities(strip_tags($this->data['User']['name']));
		}
		if(trim($this->data['User']['last_name'])) {
			$this->data['User']['last_name'] = htmlentities(strip_tags($this->data['User']['last_name']));
		}
		if(trim($this->data['User']['short_name'])) {
			$this->data['User']['short_name'] = htmlentities(strip_tags($this->data['User']['short_name']));
		}
	}
	function  get_billing_info($account_id=SES_COMP){
		App::import('Model','UserSubscription');
		$usersub = new UserSubscription(); 
		$user_sub = $usersub->find('first',array('conditions'=>array('company_id'=>$account_id),'order'=>'id DESC'));
		$pmonth =date('m',  strtotime('-1 month',strtotime($user_sub['UserSubscription']['next_billing_date'])));
		$pyear =date('Y',  strtotime('-1 month',strtotime($user_sub['UserSubscription']['next_billing_date'])));
		$mdays = cal_days_in_month(CAL_GREGORIAN,$pmonth,$pyear);
		if((strtotime($user_sub['UserSubscription']['sub_start_date'])+($mdays*24*60*60))<  strtotime($user_sub['UserSubscription']['next_billing_date'])){
			$dt_chk = date('Y-m-d H:i:s',(strtotime($user_sub['UserSubscription']['next_billing_date'])-($mdays*24*60*60)));
		}else{
			$dt_chk = $user_sub['UserSubscription']['sub_start_date'];
		}
		App::import('Model','CompanyUser');
		$compuser = new CompanyUser(); 
		//$counter = $compuser->find('count',array('conditions'=>array('company_id'=>$account_id,'created <'=>$dt_chk,'(is_active=1 OR is_active=2)')));
		//$user_info =  $compuser->find('all',array('conditions'=>array('company_id'=>$account_id,'created >'=>$dt_chk,'(is_active=1 OR is_active=2)'),'group'=>array('DATE(created)'),'fields'=>array('DATE(created) AS dt','DATE(modified) AS mfd_dt','COUNT(id) as cnt','SUM(est_billing_amt) AS amnt')));
		//$delted_users =  $compuser->find('all',array('conditions'=>array('company_id'=>$account_id,'is_active'=>3,'OR'=>array('created >'=>$dt_chk,'modified >'=>$dt_chk)),'group'=>array('dt','DATE(modified)'),'fields'=>array("IF((created > '".$dt_chk."'),DATE(created),'".date('Y-m-d',strtotime($dt_chk))."') AS dt",'DATE(modified) AS mfd_dt','COUNT(id) as cnt','SUM(est_billing_amt) AS amnt')));
		$counter = $compuser->find('count',array('conditions'=>array('company_id'=>$account_id,'is_active'=>1)));
		$invited_users = $compuser->find('count',array('conditions'=>array('company_id'=>$account_id,'is_active'=>2)));
		$disabled_users = $compuser->find('count',array('conditions'=>array('company_id'=>$account_id,'is_active'=>0,'billing_end_date >= '=>GMT_DATE)));
		$deleted_users = $compuser->find('count',array('conditions'=>array('company_id'=>$account_id,'is_active'=>3,'billing_end_date >= '=>GMT_DATE)));
		//$user_info =  $compuser->find('all',array('conditions'=>array('company_id'=>$account_id,'created >'=>$dt_chk,'(is_active=1 OR is_active=2)'),'group'=>array('DATE(created)'),'fields'=>array('DATE(created) AS dt','DATE(modified) AS mfd_dt','COUNT(id) as cnt','SUM(est_billing_amt) AS amnt')));
		//$delted_users =  $compuser->find('all',array('conditions'=>array('company_id'=>$account_id,'is_active'=>3,'OR'=>array('created >'=>$dt_chk,'modified >'=>$dt_chk)),'group'=>array('dt','DATE(modified)'),'fields'=>array("IF((created > '".$dt_chk."'),DATE(created),'".date('Y-m-d',strtotime($dt_chk))."') AS dt",'DATE(modified) AS mfd_dt','COUNT(id) as cnt','SUM(est_billing_amt) AS amnt')));
		//$user_info['previous_users'] =$counter;
		//$user_info['delted_users'] =$delted_users;
		$user_info['active_users'] = $counter;
		$user_info['invited_users'] = $invited_users;
		$user_info['disabled_users'] = $disabled_users;
		$user_info['deleted_users'] = $deleted_users;
		return $user_info;
		//echo "<pre>";print_r($user_info);exit;
	}

	function getUserFields($condition = array(), $fields = array()) {
	    $this->recursive = -1;
	    return $this->find('first',array('conditions'=>$condition,'fields'=>$fields));
	} 
	function checkActvUser($comp_id=null, $uid=null, $field=null){
		//$this->recursive=-1;
		$ret = null;
		if(empty($comp_id) || empty($uid)){
			$ret['code'] = 2006;
			$ret['status'] = "failure";
			$ret['msg'] = "Auth token is invalid!";
			return $ret;
		}
		$usr = $this->getUserFields(array('User.uniq_id' => $uid), array('id', 'timezone_id'));
		if(empty($usr)){
			$ret['code'] = 2006;
			$ret['status'] = "failure";
			$ret['msg'] = "Auth token is invalid!";
			return $ret;
		}
		if(!$field){
			$field = array('User.id','User.timezone_id','CompanyUser.company_uniq_id','CompanyUser.company_id');
		}		
		array_push($field,"CompanyUser.user_type");
		if(defined('CR') && CR==1){
			array_push($field,"CompanyUser.is_client");
		}
		$uservalid = $this->find('all',array('joins'=>array(
				array(
					'table' => 'company_users',
					'alias' => 'CompanyUser',
					'type' => 'inner',
					'conditions'=> array('CompanyUser.user_id=User.id','CompanyUser.company_uniq_id'=>$comp_id,'CompanyUser.user_id'=>$usr['User']['id'],'CompanyUser.is_active'=>1)
				)),
				'fields'=>$field));
		if(empty($uservalid)){
			$ret['code'] = 2013;
			$ret['status'] = "failure";
			$ret['msg'] = "Your account has been deactivated. Please contact your owner.";
			return $ret;
		}
		$ret = $uservalid[0];
		$ret['code'] = 2000;
		return $ret;
	}
	function get_email_list(){
		$this->recursive=-1;
		$userlist = $this->find('all',array('joins'=>array(
				array(
					'table' => 'company_users',
					'alias' => 'CompanyUser',
					'type' => 'inner',
					'conditions'=> array('CompanyUser.user_id=User.id','User.email IS NOT NULL','CompanyUser.company_id'=>SES_COMP,'CompanyUser.user_type'=>3,'(CompanyUser.is_active = 1 OR CompanyUser.is_active=2)')
				)),
				'fields'=>array('User.id ','User.email','User.name','User.last_name')));
			return $userlist;
		//echo "<pre>";print_r($userlist);exit;
	}
	function get_email_listall($comp_id){
		$this->recursive=-1;
		$userlist = $this->find('all',array('joins'=>array(
				array(
					'table' => 'company_users',
					'alias' => 'CompanyUser',
					'type' => 'inner',
					'conditions'=> array('CompanyUser.user_id=User.id','User.email IS NOT NULL','CompanyUser.company_id'=>$comp_id,'(CompanyUser.is_active = 1)')
				)),
				'fields'=>array('User.id ','User.email','User.name','User.last_name','CompanyUser.user_type','CompanyUser.is_active')));
			return $userlist;
		//echo "<pre>";print_r($userlist);exit;
	}
	function formatActivities($activity, $total, $fmt, $dt, $tz, $csq){
		if($total){
		//Assign value in variables.
			$cnoPidArr = $getTitles = $reqTitles = array();
			foreach ($activity as $k => $v) {
				if($v['Easycase']['istype']!=1) {
					if(!isset($cnoPidArr[$v['Easycase']['case_no'].'_'.$v['Easycase']['project_id']])) {
						$cnoPidArr[$v['Easycase']['case_no'].'_'.$v['Easycase']['project_id']] = array('Easycase.case_no'=>$v['Easycase']['case_no'],'Easycase.project_id'=>$v['Easycase']['project_id']);
					}
				} else {
					$cnoPidArr[$v['Easycase']['case_no'].'_'.$v['Easycase']['project_id']] = array('Easycase.id'=>$v['Easycase']['id']);
				}
			}
			$cnoPidArr = array_values($cnoPidArr);
			
			if($cnoPidArr) {
				$Easycase = ClassRegistry::init('Easycase');
				$Easycase->recursive = -1;
				$getTitles = $Easycase->find('all', array('conditions' => array('OR' => $cnoPidArr,'Easycase.isactive' => 1,'Easycase.istype' => 1),'fields' => array('Easycase.title', 'Easycase.case_no', 'Easycase.project_id')));
			}
			foreach($getTitles as $getTitles){
				$reqTitles[$getTitles['Easycase']['case_no'].'_'.$getTitles['Easycase']['project_id']] = $getTitles['Easycase']['title'];
			}
			
			$dateCurnt = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date"); 
			foreach ($activity as $k => $v) {	   
		    	$updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $v['Easycase']['actual_dt_created'], "datetime");
			$lastDate = $dt->dateFormatOutputdateTime_day($updated, $dateCurnt,'',1);
			$lastDateArr = explode(',',$lastDate);
			if(isset($lastDateArr[2]) && PAGE_NAME=='recent_activities'){
				$lastDate = $lastDateArr[0].','.$lastDateArr[1];
			}
			
			$activity[$k]['Easycase']['id'] = $v['Easycase']['id'];
			
			$activity[$k]['User']['funll_name'] = ucfirst($fmt->formatText($v['User']['name']));
			
			if(PAGE_NAME=='recent_activities') {
				if(stristr(trim($v['User']['name'])," ")) {
					$expname = explode(" ",trim($v['User']['name']));
					$v['User']['name'] = $expname[0];
				}
				$v['User']['name'] = $fmt->shortLength($v['User']['name'],8);
			}
			$activity[$k]['User']['name'] = ucfirst($fmt->formatText($v['User']['name']));
			
			$activity[$k]['Easycase']['lastDate'] = $lastDate;
			$activity[$k]['Easycase']['updated'] = date("g:i a", strtotime($updated));
                        if($v['Easycase']['legend'] == 2 || $v['Easycase']['legend'] == 4){
                            $v['Easycase']['legend'] =2;
                        } 
            $status = ClassRegistry::init('Status');
            $legendnm = $status->find('first', array('conditions'=>array('Status.id'=>$v['Easycase']['legend']), 'fields'=>array('Status.name', 'Status.color')));
			$activity[$k]['Easycase']['legendnm'] = $legendnm['Status']['name'];
			$activity[$k]['Easycase']['legendcol'] = $legendnm['Status']['color'];
			//$activity[$k]['Easycase']['uniqId'] = $csq->getCaseUniqId($v['Easycase']['case_no'], $v['Easycase']['project_id']);
			$msg ='';
			//$casetitle = $csq->getTaskTitle($v['Easycase']['id'], $v['Easycase']['istype'], $v['Easycase']['case_no'], $v['Project']['id']);
			$casetitle = $reqTitles[$v['Easycase']['case_no'].'_'.$v['Easycase']['project_id']];
			
			if(!$casetitle) {
				unset($activity[$k]);
				continue;
			}
			
			$frmt_title_data = $fmt->formatText($casetitle);
			$frmt_title_data = htmlentities($fmt->convert_ascii($fmt->longstringwrap($frmt_title_data)),ENT_QUOTES);
			
			/*if(PAGE_NAME=='recent_activities'){
				$eTitle = '<a href="'.HTTP_ROOT.'dashboard#details/'.$activity[$k]['Easycase']['uniq_id'].'">#'.$activity[$k]['Easycase']['case_no'].'</a>';
			} else {
				$eTitle = '<a href="'.HTTP_ROOT.'dashboard#details/'.$activity[$k]['Easycase']['uniq_id'].'">#'.$activity[$k]['Easycase']['case_no'].": ".$frmt_title_data.'</a>';
			}*/
			$eTitle = '<a href="'.HTTP_ROOT.'dashboard#details/'.$activity[$k]['Easycase']['uniq_id'].'">#'.$activity[$k]['Easycase']['case_no'].": ".$frmt_title_data.'</a>';
			
			$activity[$k]['Easycase']['title_data'] = $eTitle;
			if ($v['Easycase']['istype'] == 2) {		
			    	$caseReplyType = $v['Easycase']['reply_type'];
				$caseDtMsg = $v['Easycase']['message'];
				$caseDtLegend = $v['Easycase']['legend'];
				$caseAssignTo = $v['Easycase']['assign_to'];
				$taskhourspent = $v['Easycase']['hours'];
				$taskcompleted = $v['Easycase']['completed_task'];
				$casePriority = $v['Easycase']['priority'];
				$asgnTo = ''; $sts = ''; $hourspent = ''; $completed = '';$prio = '';	
			if($caseDtMsg == ''){
				if($caseReplyType == 0){
					if($caseDtLegend == 1) {
						$msg = ' <span class="col-crt"><b>created</b></span> '.$eTitle;
					} elseif($caseDtLegend == 2 || $caseDtLegend == 4){
						$msg = ' <span class="col-wip"><b>'.__('responded', true).'</b> </span><span class="fnt_clr_gry">'.__('on', true).'</span> '.$eTitle;
					} elseif($caseDtLegend == 3) {
						$msg = ' <span class="col-clsd"><b>'.__('closed', true).'</b></span> '.$eTitle;
					} elseif($caseDtLegend == 5){
						$msg = ' <span class="col-rslvd"><b>'.__('resolved', true).'</b></span> '.$eTitle;
					} elseif($caseDtLegend == 6){
						$msg = ' <span class="col-rslvd"><b>'.__('Modified', true).'</b></span> '.$eTitle;
                    }else{
                        $status = ClassRegistry::init('Status');
                        $legend = $status->find('first', array('conditions'=>array('Status.id'=>$caseDtLegend), 'fields'=>array('Status.name', 'Status.color')));
                        $msg = ' '.__('Changed the status to', true).' <span class="" style="color:'.$legend['Status']['color'].'"><b>'.__($legend['Status']['name'], true).'</b></span> '.__('of the task', true).' '.$eTitle;
					}			
				}elseif($caseReplyType == 1){
					$caseDtTyp = $v['Easycase']['type_id'];
					$prjtype_name = $csq->getTypeArr($caseDtTyp, $GLOBALS['TYPE']);
					$name = $prjtype_name['Type']['name'];
					$sname = $prjtype_name['Type']['short_name'];
					$image = $fmt->todo_typ($sname,$name);
					$msg = ' <span class="col-wip"><b>'.__('updated', true).'</b> </span><span class="fnt_clr_gry">'.__('task type to', true).' <b>'.$name."</b> ".__('on', true)."</span> ".$eTitle;	
					
				}elseif($caseReplyType == 2){							
					$userArr1 = $csq->getUserDtls($v['Easycase']['assign_to']);				
					$by_name_assign = $userArr1['User']['name'];
					$short_name_assign = $userArr1['User']['short_name'];
					$msg = ' <span class="col-wip"><b>'.__('assigned', true).'</b></span> '.$eTitle.' <span class="fnt_clr_gry">'.__('to', true).' <b>'.$by_name_assign.'</b>('.$short_name_assign.')</span>';
                                       if($v['Easycase']['assign_to'] == 0 ){
                                           $msg = ' <span class="col-wip"><b>'.__('assigned', true).'</b></span> '.$eTitle.' <span class="fnt_clr_gry">'.__('to', true).' <b>Nobody</b></span>';
                                       }
                                       
				}elseif($caseReplyType == 4){
					if($casePriority == 0){                                                                      
						$prio = 'High';
					}elseif($casePriority == 1){
						$prio = 'Medium';
					}elseif($casePriority == 2){
						$prio = 'Low';
					}
					$msg = ' <span class="col-wip"><b>'.__('updated', true).'</b> </span><span class="fnt_clr_gry">'.__('proirity to', true).' <b>'.$prio.'</b> '.__('on', true).'</span> '.$eTitle;
				}elseif($caseReplyType == 3) {
					$caseDtDue = $v['Easycase']['due_date'];
					$curCreated = $tz->GetDateTime(SES_TIMEZONE,TZ_GMT,TZ_DST,TZ_CODE,GMT_DATETIME,"datetime");
					if($caseDtDue != "NULL" && $caseDtDue != "0000-00-00" && $caseDtDue != "" && $caseDtDue != "1970-01-01") {
						$due_date = $dt->dateFormatOutputdateTime_day($caseDtDue,$curCreated,'week');
						$msg = ' <span class="col-wip"><b>'.__('updated', true).'</b> </span><span class="fnt_clr_gry">'.__('due date on', true).'</span> '.$eTitle.' <span class="fnt_clr_gry">'.__('to', true).' <b>'.$due_date.'</b></span>';
					}
				}elseif ($caseReplyType == 5) {
                    $caselegend = $v['Easycase']['legend'];
                    $legend = ClassRegistry::init('Status');
                    $legendDetails = $legend->find('first', array('conditions'=>array('Status.id'=>$caseDtLegend), 'fields'=>array('Status.name', 'Status.color')));
                    $msg = '<span class="col-wip"><b>'.__('updated', true).'</b> </span><span class="fnt_clr_gry">'.__('Status changed to', true).' <b style="color:' . $legendDetails['Status']['color'] . '">' . $legendDetails['Status']['name'] . '</b></span>';
                }
			}else{			
				$msg = ' <span class="col-wip"><b>'.__('responded', true).'</b> </span><span class="fnt_clr_gry">'.__('on', true).'</span> '.$eTitle;
			}
			} else {
				$msg = ' <span class="col-crt"><b>'.__('created', true).'</b></span> '.$eTitle;
			}
			$activity[$k]['Easycase']['msg'] = $msg;
			if($project_id != 'all'){
			    	if($project_id == $v['Project']['id']){
			    		$activity[$k]['Project']['name'] = '';
			    	}else{
			    		$activity[$k]['Project']['name'] = $v['Project']['name'];
			    	}
			    }
		    }
			$activity = array_values($activity);
		}	
		return array('activity' => $activity, 'total' => $total);
	}
	
	function getOverdue($projid,$today,$type = NULL){
		$ProjectUser = ClassRegistry::init('ProjectUser');
		$Easycase = ClassRegistry::init('Easycase');
		$qry = '';
		if($projid == 'all'){
			$getAllProj = $ProjectUser->find('all',array('conditions'=>array('ProjectUser.user_id'=>SES_ID,'ProjectUser.company_id'=>SES_COMP),'fields'=>'ProjectUser.project_id'));
			$projIds = array();
			foreach($getAllProj as $pj) {
				$projIds[] = $pj['ProjectUser']['project_id'];
			}
			$getUsers = array();
			if(count($projIds)) {
				$pjids = "(".implode(",",$projIds).")";
				$qry = "AND ProjectUser.project_id IN ".$pjids."";
			}
		}else{
			$pjids = $projid;
			$qry = "AND Project.uniq_id = '".$pjids."'";
		}
		$cond = '';
		if($type=='my') {
		    $cond = " AND Easycase.assign_to ='".SES_ID."'";
		} elseif($type=='delegated') {
		    $cond = " AND Easycase.user_id ='".SES_ID."' AND Easycase.assign_to !='".SES_ID."'";
		}
    $clt_sql = 1;
        if (defined('CR') && CR==1 && CakeSession::read("Auth.User.is_client") == 1) {
            $clt_sql = "((Easycase.client_status = " . CakeSession::read("Auth.User.is_client") . " AND Easycase.user_id = " . CakeSession::read("Auth.User.id") . ") OR Easycase.client_status != " . CakeSession::read("Auth.User.is_client") . ")";
        }
		$over_milestone="SELECT  `Easycase`.case_no,`Easycase`.dt_created,`Easycase`.uniq_id,`Easycase`.project_id,`Easycase`.due_date,
		    `Easycase`.title, `User`.name FROM `easycases` AS `Easycase` inner JOIN  project_users AS `ProjectUser` 
		    ON (`Easycase`.`project_id` = `ProjectUser`.`project_id`) inner JOIN `users` AS `User` 
		    ON(`Easycase`.`user_id` = `User`.`id` AND `Easycase`.`due_date` < '".$today."' AND  `Easycase`.`due_date`!= '0000-00-00' 
		    AND `Easycase`.`due_date`!= 'NULL' AND Easycase.isactive='1' AND `Easycase`.istype ='1' AND Easycase.title !='' ".$cond."
		    AND `Easycase`.legend !='3' AND `Easycase`.legend !='5') inner JOIN `projects` AS `Project` 
		    ON(`ProjectUser`.`project_id`=`Project`.`id` AND `Project`.`isactive`='1') WHERE `ProjectUser`.`user_id` = '".SES_ID."' 
		    AND " . $clt_sql . " AND `ProjectUser`.`company_id` = '".SES_COMP."' ".$qry." order by `Easycase`.due_date DESC LIMIT 0,5";
		$overdue = $Easycase->query($over_milestone);
		
		return $overdue;		
	}
	function getUpcoming($projid,$today,$type = NULL, $limit = 5){
		$ProjectUser = ClassRegistry::init('ProjectUser');
		$Easycase = ClassRegistry::init('Easycase');
		$qry = '';
		if($projid == 'all'){
			$getAllProj = $ProjectUser->find('all',array('conditions'=>array('ProjectUser.user_id'=>SES_ID,'ProjectUser.company_id'=>SES_COMP),'fields'=>'ProjectUser.project_id'));
			$projIds = array();
			foreach($getAllProj as $pj) {
				$projIds[] = $pj['ProjectUser']['project_id'];
			}
			$getUsers = array();
			if(count($projIds)) {
				$pjids = "(".implode(",",$projIds).")";
				$qry = "AND ProjectUser.project_id IN ".$pjids."";
			}
		}else{
			$pjids = $projid;
			$qry = "AND Project.uniq_id = '".$pjids."'";
		}
		$cond = '';
		if($type=='my') {
		    $cond = " AND Easycase.assign_to ='".SES_ID."'";
		} elseif($type=='delegated') {
		    $cond = " AND Easycase.user_id ='".SES_ID."' AND Easycase.assign_to !='".SES_ID."'";
		}

		$next_milestone="SELECT  `Easycase`.case_no,`Easycase`.dt_created,`Easycase`.uniq_id,`Easycase`.project_id,`Easycase`.due_date, 
		    `Easycase`.title, `User`.name, `Project`.name, `Project`.uniq_id FROM `easycases` AS `Easycase` inner JOIN  project_users AS `ProjectUser` 
		    ON (`Easycase`.`project_id` = `ProjectUser`.`project_id`) inner JOIN `users` AS `User`
		    ON(`Easycase`.`user_id` = `User`.`id` AND `Easycase`.`due_date` >= '".$today."' AND Easycase.isactive='1'
		    AND `Easycase`.istype ='1' AND Easycase.title !='' ".$cond.") inner JOIN `projects` AS `Project` ON(`ProjectUser`.`project_id`=`Project`.`id` 
		    AND `Project`.`isactive`='1') WHERE `ProjectUser`.`user_id` = '".SES_ID."' AND `ProjectUser`.`company_id` = '".SES_COMP."'
		    ".$qry." order by `Easycase`.due_date ASC LIMIT 0,$limit";
		$upcoming = $Easycase->query($next_milestone);
		
		return $upcoming;
	}
/**
 *@method public downgrade_limitation(array $subscriptin) Checking limitation of the user while upgrading the plan
 
 * @return array
 */	
	function downgrade_limitation($subscription=array()){
		if($subscription){
			// Checking for Project Limitation
			$Project = ClassRegistry::init('Project');
			$Project->recursive = -1;
			$totProj = $Project->find('count', array('conditions' => array('Project.company_id'=>SES_COMP),'fields' => 'DISTINCT Project.id'));
			$retarr['totproj']= $totProj;
			// Checking for User Limitation
			$companyusers_cls = ClassRegistry::init('CompanyUser');
			$totalUsers = $companyusers_cls->find('count',array('conditions'=>array('company_id'=>SES_COMP , 'is_active !='=>3)));
			$retarr['totalusers']= $totalUsers;
			// Checking for Storage Limitation
			App::import('Component', 'Format');
			$format = new FormatComponent();
			$used_space = $format->usedSpace();
			$retarr['used_space']= $used_space;
			// Validating data with downgraded subscription Limit
			$retarr['proj_limit_exceed']=0;$retarr['user_limit_exceed']=0;$retarr['storage_limit_exceed']=0;
			if(strtolower($subscription['project_limit']) !='unlimited'){
				if($totProj > $subscription['project_limit']){
					$retarr['proj_limit_exceed']=1;
				}
			}
			if(strtolower($subscription['user_limit']) !='unlimited'){
				if($totalUsers > $subscription['user_limit']){
					$retarr['user_limit_exceed']=1;
				}
			}
			if(strtolower($subscription['storage']) !='unlimited'){
				if($used_space > $subscription['storage']){
					$retarr['storage_limit_exceed']=1;
				}
			}
			return $retarr;
		}else{
			return false;
		}
	}
	function getProjectOwnAdmin(){
		 return $this->query("SELECT User.name,User.last_name,User.id,User.short_name,CompanyUser.user_type FROM users AS User,company_users AS CompanyUser WHERE User.id=CompanyUser.user_id AND CompanyUser.company_id='".SES_COMP."' AND CompanyUser.is_active ='1' AND CompanyUser.user_type!='3' AND User.isactive='1' ORDER BY CompanyUser.user_type ASC");
	}
/**
 * @Method Public validate_emailurl($data=array()) Check email and URL existance with our db
 
 * @return array 
 */	
	function validate_emailurl($data=  array()){
		$this->recursive = -1;
		$arr['email']='success';$arr['seourl']='success';
		if($data['email']) {
			if(filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
				$checkUsr = $this->find('first',array('conditions'=>array('User.email'=>urldecode($data['email'])),'fields'=>array('User.id')));
				 if($checkUsr['User']['id']) {
					 $arr['email']='error';
					 $arr['email_msg']=__("Email already exists! Please try another", TRUE);
				 }
			}else{
				$arr['email']='error';
				$arr['email_msg']=__("Please enter a valid email.", true);
			}	
		}
		$Company = ClassRegistry::init('Company');
		$Company->recursive = -1;
		$seo_url = urldecode($data['seo_url']);
                $all_mail=array('yahoo','hotmail','live','reddif','outlook','rediff','aim','zoho','icloud','mail','gmax','shortmail','inbox','gmail');
		$check=0;
                if(in_array($seo_url, $all_mail)){
                    $check=1;
                }
                if($seo_url == 'app' || $seo_url == 'www' || $check){
			$arr['seourl']='error'; 
			$arr['seourl_msg']="<b>'".$seo_url."'</b> ".__("is not allowed", true)."."; 
		}else{
			$checkUsr = $Company->find('first',array('conditions'=>array('Company.seo_url'=>$seo_url),'fields'=>array('Company.id')));
			if($checkUsr['Company']['id']) {
				$arr['seourl']='error'; 
				$arr['seourl_msg']=__("Oops, site address already in use!", true);
			}
		}
		if(isset($data['coupon_code']) && $data['coupon_code']) {
			$Coupon = ClassRegistry::init('Coupon');
			$Coupon->recursive=-1;
			$coupon = $Coupon->find('first',array('conditions'=>array('Coupon.code'=>trim($data['coupon_code']),'Coupon.isactive'=>'1',array('OR'=>array('Coupon.expires >= CURDATE()','Coupon.expires'=>'0000-00-00')))));
			if (!$coupon) {
				$arr['coupon']='error'; 
				$arr['coupon_msg']=__("Invalid coupon code!", true); 
			} elseif ($coupon['Coupon']['company_id']) {
				$arr['coupon']='error'; 
				$arr['coupon_msg']=__("Oops! Coupon code has already used.", true); 
			}
		}
		return $arr;
	}
         function getProfileBgColr($uid = null) {
            if ($uid) {
                $t_clr = Configure::read('PROFILE_BG_CLR');
                $random_bgclr = $t_clr[array_rand($t_clr, 1)];
                $ret_colr = $random_bgclr;
                if (!isset($_SESSION['user_profile_colr'])) {
                    $_SESSION['user_profile_colr'] = array();
                    $_SESSION['user_profile_colr'][$uid] = $random_bgclr;
                } else {
                    if (!array_key_exists($uid, $_SESSION['user_profile_colr'])) {
                        $_SESSION['user_profile_colr'][$uid] = $random_bgclr;
                    } else {
                        $ret_colr = $_SESSION['user_profile_colr'][$uid];
                    }
                }
                return $ret_colr;
            }
        }
		
	function api_check_user_exists($email,$companyId) {
			if ($email) {
			$ret = 0;
			$checkUsr = $this->find('first', array('conditions' => array('User.email' => urldecode($email)), 'fields' => array('User.id')));
			if ($checkUsr) {					
				$user_id = $checkUsr['User']['id'];
				$UserInvitation = ClassRegistry::init('UserInvitation');
				$ui = $UserInvitation->find('first', array('conditions' => array('UserInvitation.company_id' => $companyId, 'UserInvitation.user_id' => $user_id), 'fields' => array('UserInvitation.id')));
				if ($ui['UserInvitation']['id']) {
					$ret = 2; //invited
				} else {
					$CompanyUser = ClassRegistry::init('CompanyUser');
					$cu = $CompanyUser->find('first', array('conditions' => array('CompanyUser.company_id' => $companyId, 'CompanyUser.user_id' => $user_id, 'CompanyUser.user_type' => 1), 'fields' => array('CompanyUser.id')));
					if ($cu['CompanyUser']['id']) {
						$ret = 1; //owner
					} else {
						$chku = $CompanyUser->find('first', array('conditions' => array('CompanyUser.company_id' => $companyId, 'CompanyUser.user_id' => $user_id, 'CompanyUser.is_active !=3'), 'fields' => array('CompanyUser.id')));
						if ($chku['CompanyUser']['id']) {
							$ret = 3; //exist
}
					}
				}
			}
			return $ret;
		}
    }
	
	function getUserLoginInfo($uid){
		$user_inf = $this->find('first', array('conditions' => array('User.id' => $uid, 'User.isactive' => 1)));
		$data_inf = null;
		if (isset($user_inf) && !empty($user_inf)) {
			//Set user details.
			$data_inf['results']['auth_token'] = $user_inf['User']['uniq_id'];
			$data_inf['results']['user']['info']['email'] = $user_inf['User']['email'];
			$data_inf['results']['user']['info']['first_name'] = $user_inf['User']['name'];
			$data_inf['results']['user']['info']['last_name'] = $user_inf['User']['last_name'];
			$data_inf['results']['user']['info']['short_name'] = $user_inf['User']['short_name'];
			$data_inf['results']['user']['info']['time_zone'] = $user_inf['User']['timezone_id'];
			$data_inf['results']['user']['info']['photo'] = $user_inf['User']['photo'];		
			
			$Company_User = ClassRegistry::init('CompanyUser');
			$sql = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name,Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $uid . " AND CompanyUser.is_active=1 GROUP BY CompanyUser.company_id";
			$CompanyUser = $Company_User->query($sql);

			if (isset($CompanyUser) && !empty($CompanyUser)) {
				//Getting latest company or project
				$Project_User = ClassRegistry::init('ProjectUser');
				$sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='" . $user['User']['id'] . "' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
				$ProjectUser = $Project_User->query($sql);

				$companyId = $companyUniqId = $projectId = '';
				if (isset($ProjectUser) && !empty($ProjectUser)) {
					$companyId = $ProjectUser['0']['Company']['id'];
					$companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
					$projectId = $ProjectUser['0']['Project']['uniq_id'];
				} else {
					$companyId = $CompanyUser['0']['CompanyUser']['company_id'];
					$companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
				}
				//Set company details
				foreach ($CompanyUser as $key => $value) {
					$short_name = trim($value['Companies']['seo_url']);
					$companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $short_name, "user_type" => $value['CompanyUser']['user_type']);
					if ($value['Companies']['uniq_id'] == $companyUniqId) {
						$companyData = array_merge($companyData, array("selected" => "1"));
					}
					$data_inf['results']['companies']['company'][$key] = $companyData;
				}
			}
		}
		return $data_inf;
	}	
	function getUserCurrentStatus($auth_token=null, $hFormat){
		$ret = array('code'=>2000,'status'=>'OK');
		if($auth_token){
			$user = $this->getUserFields(array('User.uniq_id' => trim($auth_token)), array('User.id','User.uniq_id','User.name','User.email','User.photo','User.password','User.short_name'));
			if(!empty($user)){
				$Company = ClassRegistry::init('Company');
				$is_client = '';
				if (defined('CR') && CR==1){
					$is_client = ',CompanyUser.is_client';
				}
				$work_hour = '';
				if (defined('GTLG') && GTLG==1){
					$work_hour = ',Company.work_hour';
				}
				$getComps = $Company->query("SELECT CompanyUser.user_type,CompanyUser.is_active,CompanyUser.is_access_change,CompanyUser.change_timestamp,Company.uniq_id,Company.seo_url,Company.id".$is_client.$work_hour." FROM company_users AS CompanyUser,companies AS Company WHERE CompanyUser.company_id=Company.id AND CompanyUser.user_id='" . $user["User"]["id"]. "'");
				if(!empty($getComps)){
					if($getComps[0]['CompanyUser']['is_active'] == 1){
						$hFormatres = $hFormat->get_client_permission('all',$getComps[0]['Company']['id']);
						if($hFormatres){
							unset($hFormatres['company_id']);
							unset($hFormatres['id']);
						}
						$ret['clientRestriction'] = $hFormatres;
						$ret['uniq_id'] = $getComps[0]['Company']['uniq_id'];
						$ret['user_type'] = $getComps[0]['CompanyUser']['user_type'];
						$ret['change_timestamp'] = $getComps[0]['CompanyUser']['change_timestamp'];
						$ret['is_access_change'] = $getComps[0]['CompanyUser']['is_access_change'];
						$ret['name'] = $user["User"]["name"];
						$ret['email'] = $user["User"]["email"];
						$ret['password'] = $user["User"]["password"];
						$ret['short_name'] = $user["User"]["short_name"];
						$img_url = '';
						if(isset($user["User"]["photo"]) && !empty($user["User"]["photo"])) {
							if (defined('USE_S3') &&  USE_S3) {
								$img_url = HTTP_ROOT.'users/image_thumb/?type=photos&file='.$user['User']['photo'].'&sizex=100&sizey=100&quality=100';
							}else{						
								$img_url = HTTP_ROOT.'users/image_thumb/?type=photos&file='.$user['User']['photo'].'&sizex=100&sizey=100&quality=100';
							}
						}
						$ret['photo'] = $img_url;

						$ret['is_client'] = 0;
						if (defined('CR') && CR==1){
							if($getComps[0]['CompanyUser']['is_client'] == 1){
								$ret['is_client'] = 1;
							}
						}
						if (defined('GTLG') && GTLG==1){
							$ret['work_hour'] = $getComps[0]['Company']['work_hour'];
						}
					}else{
						$ret['code'] = 2005;
						$ret['status'] = "failure";
						$ret['msg'] = "Your account has been deactivated. Please contact your account owner.";
					}
				}else{
					$ret['code'] = 2006;
					$ret['status'] = "failure";
					$ret['msg'] = "Auth token is invalid!";
				}
			}else{
				$ret['code'] = 2006;
				$ret['status'] = "failure";
				$ret['msg'] = "Auth token is invalid!";
			}
		}else{
			$ret['code'] = 2006;
			$ret['status'] = "failure";
			$ret['msg'] = "Auth token is invalid!";
		}	
		return $ret;
	}
}