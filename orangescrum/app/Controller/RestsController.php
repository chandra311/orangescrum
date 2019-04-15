<?php
App::uses('AppController', 'Controller'); 

class RestsController extends AppController {

    public $name = 'Rests';
    public $uses = array();
    public $components = array('Auth', 'Session',  'Cookie', 'Tmzone', 'Image','Sendgrid','Email','RequestHandler');

    public function beforeFilter() {
	$this->layout = '';
	$this->Auth->allow('login','getTaskData','fileupload', 'fileremove','casePost','getCompanyAndProjects','getProjectsAndTasks','getAllTasks','getProjectTasks','getTaskDetails','company_statistics','get_user_list','testing_json','taskPost','getProjectsList','signupUser','industryDetails','registerUser'
                            ,'company_user_list','create_project','edit_project','delete_task','forgotPassword','getTaskPermission'); 

	parent::beforeFilter();
    }
		
    /**
     * @method login
     * @param email:string, password:string
     * @return json string objects: Users, Companys , Projects and task with attachments details
     * @author Sunil
     */
    
    function login($googleEmail=null,$googleToken=null) {
	$this->layout = "ajax";
	
	$email = $password = '';
	$isPlugin = 0;   
	$input_request=(array) $this->request->input(json_decode,true);
	if(empty($input_request)){
	    $input_request= $this->request->query;
	}
	//print json_encode($input_request);exit;
	//$input_request=$this->request->query();
	if(isset($input_request['email'])){
	    $email = $input_request['email'];
	} elseif(isset($this->request->query['email'])){
	    //$isPlugin = 1;
	    $email = $this->request->query['email'];
	}elseif(isset($googleEmail) && !empty($googleEmail)){
            $email = $googleEmail;
        } 
	
	if(isset($input_request['password'])){
	    $password = $input_request['password'];
	} elseif(isset($this->request->query['password'])){
	   // $isPlugin = 1;
	    $password = $this->request->query['password'];
	} 
	
	if ( (trim($email) && trim($password)) || (trim($email) && !empty($googleToken)) ) {
	    $this->loadModel('User');
            if(!empty($password)){
                 $user = $this->User->find('first', array('conditions' => array('User.email' => trim($email), 'User.password' => md5($password), 'User.isactive' => 1)));
            }else{
                 $user = $this->User->find('first', array('conditions' => array('User.email' => trim($email), 'User.isactive' => 1)));
            }
	    if (isset($user) && !empty($user)) {
		//Set status
		$data['code'] = 2000;
		$data['status'] = "OK";

		//Set user details.
		$data['results']['auth_token'] = $user['User']['uniq_id'];
		$data['results']['user']['info']['email'] = $user['User']['email'];
		$data['results']['user']['info']['first_name'] = $user['User']['name'];
		$data['results']['user']['info']['last_name'] = $user['User']['last_name'];
		$data['results']['user']['info']['short_name'] = $user['User']['short_name'];
		$data['results']['user']['info']['time_zone'] = $user['User']['timezone_id'];
		if(isset($user['User']['photo']) && !empty($user['User']['photo'])) {
		    $photo = DIR_USER_PHOTOS_S3.$user['User']['photo'];
		    $data['results']['user']['info']['img_url'] = $this->Image->generateTemporaryURL($photo);//$domain."users/image_thumb/?type=photos&file=".$user['User']['photo']."&sizex=45&sizey=50&quality=100";
		} else { 
		    $data['results']['user']['info']['img_url'] = "";
		}
		
		$this->loadModel('CompanyUser');
		$sql = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name,Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $user['User']['id'] . " AND CompanyUser.is_active=1 GROUP BY CompanyUser.company_id";
		$CompanyUser = $this->CompanyUser->query($sql);

		if (isset($CompanyUser) && !empty($CompanyUser)) {
		    //Getting latest company or project
		    $this->loadModel('ProjectUser');
		    $sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='".$user['User']['id']."' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
		    $ProjectUser = $this->ProjectUser->query($sql);

		    $companyId = $companyUniqId = $projectId = '';
		    if (isset($ProjectUser) && !empty($ProjectUser)) {
			$companyId = $ProjectUser['0']['Company']['id'];
			$companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
			$projectId = $ProjectUser['0']['Project']['uniq_id'];
		    }else {
			$companyId = $CompanyUser['0']['CompanyUser']['company_id'];
			$companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
		    }

		    //Set company details
		    foreach ($CompanyUser as $key => $value) {
			$short_name = trim($value['Companies']['seo_url']);
			$companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $short_name, "user_type" => $value['CompanyUser']['user_type']);
			if($value['Companies']['uniq_id'] == $companyUniqId){
			    $companyData = array_merge($companyData,array("selected"=>"1"));
			}
			$data['results']['companies']['company'][$key] = $companyData;
		    }		
		}
	    } else {
		$data['code'] = 2001;
		$data['status'] = "failure";
		$data['msg'] = "Email or Password is invalid!";
	    }
	} else {
	    $data['code'] = 2001;
	    $data['status'] = "failure";
	    $data['msg'] = "Email or Password is invalid!";
	}
	
	if(intval($isPlugin)) {
	    echo $this->jsonpFormat(json_encode($data));
	}else{
	    print json_encode($data);
	}
	exit;
    }
    /**
     * @method forgotPassword
     * @param 
     * @return json string objects: Companys
     * @author Sunil
     */
    
    function forgotPassword(){
        $this->layout = "ajax";        
	$rqst_data=(array) $this->request->input(json_decode,true);
	if(empty($rqst_data)){
	    $rqst_data= $this->request->query;
	}
        if(isset($rqst_data['email']) && !empty($rqst_data['email'])){
            $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.email' => $rqst_data['email']), array('id','uniq_id','name','email'));
	    if(!empty($user)){ 
                $id = $user['User']['id'];
                $name = stripslashes($user['User']['name']);
                $to = trim($user['User']['email']);
                $qstr = md5(uniqid(rand()));
                $urlValue = "?qstr=" . $qstr;
                
//                $this->Email->delivery = 'smtp';
//                $this->Email->to = $to;
//                $this->Email->subject = Configure::read('forgot_password');
//                $this->Email->from = 'Orangescrum<notify@orangescrum.com>';
//                $this->Email->template = 'forgot_password';
//                $this->Email->sendAs = 'html';
//                $this->set('name', $name);
//                $this->set('urlValue', $urlValue);  
               // if ($this->Sendgrid->sendgridsmtp($this->Email)) {
                $from='notify@orangescrum.com';
                $subject='Forgot Password Request';
                $fromname="Orangescrum";
                $furl=HTTP_ROOT.'users/forgotpassword/'.$urlValue;
                $message='<body style="width:100%; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#ffffff;">
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="height:auto !important; margin:0; padding:0; width:100% !important; background-color:#F0F0F0;color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:19px; margin-top:0; padding:0; font-weight:normal;">
	<tr>
		<td>
        <div id="tablewrap" style="width:100% !important; max-width:600px !important; text-align:center; margin:0 auto;">
		      <table id="contenttable" width="600" align="center" cellpadding="0" cellspacing="0" border="0" style="background-color:#FFFFFF; margin:0 auto; text-align:center; border:none; width: 100% !important; max-width:600px !important;border-top:8px solid #5191BD">
            <tr>
                <td width="100%">
                   <table bgcolor="#FFF" border="0" cellspacing="0" cellpadding="20" width="100%">
                        <tr>
                            <td width="100%" bgcolor="#FFF" style="text-align:left;">
                            	<p>
                                    Hi '. $name.',                    
                                </p>
								
								<p>We have received your request to reset password.</p>
								
								<p>To reset, please click the button below.</p>
								
								<a style="font-weight:bold; text-decoration:none;" href="'.$furl.'" target="_blank"><div style="display:block; max-width:100% !important; width:auto !important;margin:auto; height:auto !important;background-color:#0EA426;padding-top:10px;padding-right:10px;padding-bottom:10px;padding-left:10px;border-radius:10px;color:#ffffff;font-size:16px;text-align:center">Go Ahead!</div></a>
									
								
                                <br/>
								
								
								<p>Regards,<br/>
								The Orangescrum Team</p>
                            </td>
                        </tr>
                   </table>
                  
                   <table bgcolor="#F0F0F0" border="0" cellspacing="0" cellpadding="10" width="100%" style="border-top:2px solid #F0F0F0;margin-top:10px;border-bottom:3px solid #2489B3">
                        <tr>
                            <td width="100%" bgcolor="#ffffff" style="text-align:center;">
                            	<p style="color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:14px; margin-top:0; padding:0; font-weight:normal;padding-top:5px;">
									<?php echo NEW_EMAIL_FOOTER; ?>

									You are receiving this email notification because you have subscribed to Orangescrum, to unsubscribe, please email with subject \'Unsubscribe\' to <a href="mailto:support@orangescrum.com">support@orangescrum.com</a>
									
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        </div>
		</td>
	</tr>
</table> 
</body>';
                
                if ($this->Sendgrid->sendGridEmail($from, $to, $subject, $message, '', $fromname)) {
                    $this->User->query("UPDATE users SET query_string='" . $qstr . "' WHERE id=" . $id);
                    $data['code'] = 2000;
                    $data['status'] = "OK";
                    $data['msg'] = "Please check your mail to reset your password.";
                }
            }else{
                $data['code'] = 2006;
                $data['status'] = "failure";
                $data['msg'] = "Please enter a valid email!"; 
            }
        }else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Please enter an email!";
	}
	print json_encode($data);
	exit;
        
    }
    
    /**
     * @method getCompanyAndProjects
     * @param 
     * @return json string objects: Companys , Projects details
     * @author Sunil
     */
    
    function getProjectsList() {
	$this->layout = "ajax";
	$auth_token = '';
	$rqst_data=(array) $this->request->input(json_decode,true);
	if(empty($rqst_data)){
	    $rqst_data= $this->request->query;
	}	
	//pr($this->request->input(json_decode,true));exit;
	if(isset($rqst_data['auth_token'])){
	    $auth_token = $rqst_data['auth_token'];
	}
	
	if(trim($auth_token)){//Check given auth token is valid or not.
	    //Getting user id
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id'));
	    if(!empty($user)){
		//Getting all companies of a user.
		$this->loadModel('CompanyUser');
		$sql = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name, Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $user['User']['id'] . " AND CompanyUser.is_active=1";
		$CompanyUser = $this->CompanyUser->query($sql);
		if (isset($CompanyUser) && !empty($CompanyUser)) {
		    $companyId = $companyUniqId = '';		
			$companyUniqId = $rqst_data['companyId'];
		    
		    //If company id has not given, then finding company id from latest activity of a project or campany.
		    if(trim($companyUniqId)){
			$this->loadModel('Company');
			$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('id'));
			$companyId = $company['Company']['id'];
		    } else {
			//Getting latest company or project
			$this->loadModel('ProjectUser');
			$sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='".$user['User']['id']."' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
			$ProjectUser = $this->ProjectUser->query($sql);
			if (isset($ProjectUser) && !empty($ProjectUser)) {
			    $companyId = $ProjectUser['0']['Company']['id'];
			    $companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
			}else {
			    $companyId = $CompanyUser['0']['CompanyUser']['company_id'];
			    $companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
			}
		    }

		    //Set status
		    $data['code'] = 2000;
		    $data['status'] = "OK";

		    //Set company details
		    foreach ($CompanyUser as $key => $value) {
			$companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $value['Companies']['seo_url'], "user_type" => $value['CompanyUser']['user_type']);
			if($value['Companies']['uniq_id'] == $companyUniqId){
			    $companyData = array_merge($companyData,array("selected"=>"1"));
			}
			$data['results']['companies']['company'][$key] = $companyData;
		    }

		    //Getting project details
		    $sql = "SELECT DISTINCT Project.id, Project.uniq_id, Project.name, Project.default_assign FROM project_users AS ProjectUser LEFT JOIN projects AS Project ON (Project.id= ProjectUser.project_id) WHERE ProjectUser.user_id='" . $user['User']['id'] . "' AND ProjectUser.company_id='" . $companyId . "' AND Project.isactive='1' ORDER BY Project.name ASC";
		    $this->loadModel('Project');
		    $Project = $this->Project->query($sql);
		    
		    if (isset($Project) && !empty($Project)) {
			foreach ($Project as $key => $value) {
			    $data['results']['projects'][] = array("id" => $value['Project']['uniq_id'], "name" => $value['Project']['name']);
			}
			
			$res = $this->getCaseData($Project['0']['Project']['default_assign']);
			$data['results']['default_assign'] = $res['default_assign'];
			$data['results']['email_notification'] = $this->getMemebers($companyId,$Project['0']['Project']['uniq_id']);
		    }
		} else {
		    $data['code'] = 2000;
		    $data['status'] = "OK";
		    $data['msg'] = "No result founds!";
		}
	    } else {
		$data['code'] = 2006;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    function getTaskData() {
	$this->layout = "ajax";
	$auth_token = '';
	$rqst_task=(array) $this->request->input(json_decode,true);	
	if(empty($rqst_task)){
	    $rqst_task= $this->request->query;
	} 
	if(isset($rqst_task['auth_token'])){
		$auth_token = $rqst_task['auth_token'];
	}
	
	if(trim($auth_token)){//Check given auth token is valid or not.
	    //Getting user id
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id'));
	    if(!empty($user)){		
		if(isset($this->request->data['companyId'])){
			 $companyUniqId = $rqst_task['companyId'];
		}
		
		//If company id has not given, then finding company id from latest activity of a project or campany.
		if(trim($companyUniqId)){
		    $this->loadModel('Company');
		    $company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('id'));
		    $companyId = $company['Company']['id'];
		    
		    //Getting project id
		    if(isset($this->request->data['projectId'])){
			$projectUniqId = $this->request->data['projectId'];
		    }elseif(isset($this->request->query['projectId'])){
			$projectUniqId = $this->request->query['projectId'];
		    }
		    
		    if(trim($projectUniqId)){
			$this->loadModel('Project');
			$project = $this->Project->getProjectFields(array('Project.uniq_id' => $projectUniqId), array('id','default_assign'));
			if(!empty($project)){
			    //Set status
			    $data['code'] = 2000;
			    $data['status'] = "OK";
		    
			    $res = $this->getCaseData($project['Project']['default_assign']);
			    $data['results']['default_assign'] = $res['default_assign'];
			    $data['results']['email_notification'] = $this->getMemebers($companyId,$projectUniqId);
			    $data['results']['task_type'] = $res['task_type'];
			} else {
			    $data['code'] = 2009;
			    $data['status'] = "failure";
			    $data['msg'] = "Project id is invalid!";
			}
		    } else {
			$data['code'] = 2009;
			$data['status'] = "failure";
			$data['msg'] = "Project id is invalid!";
		    }
		} else {
		    $data['code'] = 2008;
		    $data['status'] = "failure";
		    $data['msg'] = "Company id is invalid!";
		}
	    } else {
		$data['code'] = 2006;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    function getCaseData($default_assign = NULL) {
	$this->loadModel('User');
	$default_user = $this->User->getUserFields(array('User.id' => $default_assign), array('uniq_id'));
	$data['default_assign'] = $default_user['User']['uniq_id'];
	
	$this->loadModel('Type');
	$type = $this->Type->find('all');

	foreach ($type as $key => $value) {
	    $typeData = array("id" => $value['Type']['id'], "name" => $value['Type']['name'], "short_name" => $value['Type']['short_name']);
	    $data['task_type'][$key] = $typeData;
	}
	
	return $data;
    }
    
    function getMemebers($companyId = NULL, $projUniqId = NULL) {
	$this->loadModel('ProjectUser');
	$quickMem = $this->ProjectUser->query("SELECT DISTINCT User.uniq_id, User.name FROM users as User,project_users as ProjectUser,company_users as CompanyUser,projects as Project WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='".$companyId."' AND Project.uniq_id='".$projUniqId."' AND Project.id=ProjectUser.project_id AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.short_name");
	$data = array();
	foreach($quickMem as $key => $value) {
	    $data[$value['User']['uniq_id']] = ucfirst($value['User']['name']);
	}
	return $data;
    }
    
    private function jsonpFormat($json) {
       return $_GET['callback']."(".$json.")";
    }

    
    /**
     * @method getAllTasks
     * @param auth token:string, company id :string
     * @return json string objects: All tasks with attachments details of a company.
     * @author Sunil
     */
    
    function getAllTasks() {
	$this->layout = "ajax";
	if (isset($this->request->data['auth_token']) && isset($this->request->data['companyId'])) {
	    //Set status
	    $data['code'] = 2000;
	    $data['status'] = "OK";
	    
	    $tasks = $this->getTasks($this->request->data['auth_token'], $this->request->data['companyId']);
	    $data['results']['tasks'] = $tasks;
		$this->loadModel('Type');
	$type = $this->Type->find('all');
	}else{
	    $data['code'] = 2002;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token or company id is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    /**
     * @method getProjectTasks
     * @param auth token:string, project id :string
     * @return json string objects: All tasks with attachments details of a project.
     * @author Sunil
     */
    
    function getProjectTasks() { 
	$this->layout = "ajax";
	$rqst_data=(array) $this->request->input(json_decode,true);
	if(empty($rqst_data)){
	    $rqst_data= $this->request->query;
	} 
	if (isset($rqst_data['auth_token']) && isset($rqst_data['projectId'])) {
            $type= (isset($rqst_data['type']) && !empty($rqst_data['type'])) ? $rqst_data['type'] : "all" ;
	    //Set status
	    $data['code'] = 2000;
	    $data['status'] = "OK";	    
	    $tasks = $this->getTasks($rqst_data['auth_token'], $rqst_data['company_id'], $rqst_data['projectId'],$type);           
	    $data['results']['tasks'] = $tasks;
            $data['results']['total_task'] =  count($tasks);
            
            if(isset($rqst_data['company_id']) &&  !empty($rqst_data['company_id'])){
                $cmpid=$rqst_data['company_id'];
            }else{
                $this->loadModel("Project");
                $cm=$this->Project->query("SELECT Company.uniq_id,Project.workflow_id FROM projects as Project LEFT JOIN companies as Company on Company.id=Project.company_id WHERE Project.uniq_id='".$rqst_data['projectId']."'");
                 $cmpid=$cm['0']['Company']['uniq_id'];
            }
            $data['results']['edit_task']=$this->getTaskPermission($cmpid);
            $this->loadModel("User");
            $u= $this->User->getUserFields(array('User.uniq_id' => $rqst_data['auth_token']), array('id'));
          
            $data['results']['user_type']=$this->getUserType($u['User']['id'],$cmpid);
	
		$companyUniqId=$rqst_data['company_id'];
		$this->loadModel("Company");
		$company_id=$this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('id'));
		$company_id=$company_id['Company']['id'];
                
                /***Task status ****/
                $this->loadModel("Project");
                $cm1=$this->Project->query("SELECT Project.workflow_id FROM projects as Project  WHERE Project.uniq_id='".$rqst_data['projectId']."'");
                 
                $this->loadModel("Status");
                $status_type = $this->Status->find('all',array('conditions'=>array('workflow_id'=>$cm1['0']['Project']['workflow_id']),'order'=>"seq_order ASC"));			
                foreach ($status_type as $key => $value) {
                        $statusTypeData = array("id" => $value['Status']['id'], "name" => $value['Status']['name'], "color" => $value['Status']['color'], "percentage" => $value['Status']['percentage'], "seq_order" => $value['Status']['seq_order']);
                        $data['results']['status_type_details'][$key] = $statusTypeData;
                }
                /*End*/
                
                
		$this->loadModel("Type");
		$companies_id=array("0",$company_id); //use of this comany id to get the task types..
		$type = $this->Type->find('all',array('conditions'=>array('Type.company_id'=>$companies_id)));
		foreach ($type as $key => $value) {
			$typeData = array("id" => $value['Type']['id'], "name" => $value['Type']['name'], "short_name" => $value['Type']['short_name']);
			$data['results']['task_type'][$key] = $typeData;
		}
	}else{
	    $data['code'] = 2003;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token or project id is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    /**
     * @method getTasks
     * @param auth token:string, company id:string, project id :string
     * @return array: All tasks with attachments details of a project or a company.
     * @author Sunil
     */
    
    function getTasks($userId = NULL, $companyId = NULL, $projectId = NULL ,$type ="") {
	$data = array();
	if(isset($userId)){
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $userId), array('id','timezone_id'));
	    
	    $this->loadModel('Timezone');
	    $timezone = $this->Timezone->find('first', array("conditions" => array("Timezone.id" => $user['User']['timezone_id'])));

	    if(trim($projectId)){
		$this->loadModel('Project');
		$project = $this->Project->getProjectFields(array('Project.uniq_id' => $projectId), array('id','company_id'));
                
                $clt_sql = 1;
                $this->loadModel('CompanyUser');
		$companyUser= $this->CompanyUser->find("first",array("conditions"=>array('CompanyUser.company_id' => $project['Project']['company_id'],'CompanyUser.user_id'=>$user['User']['id']), "fields"=>array('is_client')));
                
                if ($companyUser['CompanyUser']['is_client'] == 1) {
                    $clt_sql = "((Easycase.client_status = " . $companyUser['CompanyUser']['is_client'] . " AND Easycase.user_id = " . $user['User']['id'] . ") OR Easycase.client_status != " . $companyUser['CompanyUser']['is_client'] . ")";
                }
                
                if(trim($type) == "me"){ 
                    
                    $sql = "SELECT SQL_CALC_FOUND_ROWS Easycase.*,Project.uniq_id,User.short_name,
		    IF((Easycase.assign_to = 0 OR Easycase.assign_to ='".$user['User']['id']."'),'Me',User.short_name) AS Assigned, User.uniq_id AS assign_id  
		    FROM ( SELECT * FROM easycases as Easycase WHERE istype='1' AND Easycase.isactive=1 AND
		    Easycase.project_id='".$project['Project']['id']."' AND Easycase.project_id!=0 AND ".$clt_sql." AND (Easycase.user_id ='".$user['User']['id']."' OR Easycase.assign_to ='".$user['User']['id']."')) AS Easycase LEFT JOIN (users User, projects Project) ON Easycase.assign_to=User.id AND Easycase.project_id=Project.id
		    ORDER BY Easycase.dt_created DESC,Easycase.priority DESC LIMIT 0,30";
		//AND (Easycase.legend !='3' OR Easycase.type_id ='10') 
                 //   echo $sql;exit;
                }
                else if(trim($type) == "all"){ 
                    $sql = "SELECT SQL_CALC_FOUND_ROWS Easycase.*,Project.uniq_id,User.short_name,
		    IF((Easycase.assign_to = 0 OR Easycase.assign_to ='".$user['User']['id']."'),'Me',User.short_name) AS Assigned, User.uniq_id AS assign_id  
		    FROM ( SELECT * FROM easycases as Easycase WHERE istype='1' AND Easycase.isactive=1 AND
		    Easycase.project_id='".$project['Project']['id']."' AND Easycase.project_id!=0 AND ".$clt_sql." ) AS Easycase LEFT JOIN (users User, projects Project) ON Easycase.assign_to=User.id AND Easycase.project_id=Project.id
		    ORDER BY Easycase.dt_created DESC,Easycase.priority DESC LIMIT 0,30";
		//AND (Easycase.legend !='3' OR Easycase.type_id ='10') 
                }
		
	    }else{
		$this->loadModel('Company');
		$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyId), array('id'));

		$sql = "SELECT SQL_CALC_FOUND_ROWS Easycase.*,Project.uniq_id,User.short_name,
		    IF((Easycase.assign_to = 0 OR Easycase.assign_to ='".$user['User']['id']."'),'Me',User.short_name) AS Assigned, User.uniq_id AS assign_id FROM 
		    ( SELECT * FROM easycases as Easycase WHERE Easycase.istype='1' AND Easycase.isactive=1 AND
		    Easycase.project_id!=0 AND Easycase.project_id IN (SELECT ProjectUser.project_id FROM 
		    project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id='" . $user['User']['id'] . "' AND 
		    ProjectUser.project_id=Project.id AND Project.isactive='1' AND ProjectUser.company_id='" . $company['Company']['id'] . "')  
		    ORDER BY  Easycase.project_id DESC) 
		    AS Easycase LEFT JOIN (users User, projects Project) ON Easycase.assign_to=User.id AND Easycase.project_id=Project.id
		    ORDER BY Easycase.dt_created DESC,Easycase.priority DESC LIMIT 0,30";
	    }
            #echo $sql;exit;
	    $this->loadModel('Easycase');
	    $Easycase = $this->Easycase->query($sql);
         //   pr($Easycase);exit;
            foreach($Easycase as $k => $val){
                $sqls="SELECT COUNT(Easycase.id) as comment_count FROM easycases AS Easycase WHERE Easycase.case_no=".$val['Easycase']['case_no']." AND Easycase.project_id =".$project['Project']['id']." AND Easycase.istype='2' AND Easycase.legend !='6'  AND (Easycase.message<>'' OR Easycase.format='1')";
                $Easycases[] = $this->Easycase->query($sqls);        
            }

	    if (isset($Easycase) && !empty($Easycase)) {
		$view = new View($this);
		$frmt = $view->loadHelper('Format');
		$tz = $view->loadHelper('Tmzone');
		$dt = $view->loadHelper('Datetime');
	
		foreach ($Easycase as $key => $value) {
		    //Set case details
		    $age = $this->getAge($timezone['Timezone'],$value['Easycase']['actual_dt_created'],$tz,$dt,0);
		    
		    //Getting owner of task or last updated user on a task.
		    $createdBy = $updatedBy = '';
		    if(intval($value['Easycase']['updated_by'])){
			if($user['User']['id'] == $value['Easycase']['updated_by']) {
			    $updatedBy = "me";
			}else{
			    $update_user = $this->User->getUserFields(array('User.id' => $value['Easycase']['updated_by']), array('short_name'));
			    $updatedBy = $update_user['User']['short_name'];
			}
		    }
                    if($user['User']['id'] == $value['Easycase']['user_id']) {
                        $createdBy = "me";
                        $createdId=$userId;
                    }else{
                        $update_user = $this->User->getUserFields(array('User.id' => $value['Easycase']['user_id']), array('short_name','uniq_id'));
                        $createdBy = $update_user['User']['short_name'];
                        $createdId=$update_user['User']['uniq_id'];
                    }
		    
		    
		    $updated_date = $this->getAge($timezone['Timezone'],$value['Easycase']['dt_created'],$tz,$dt,1);
		    if($value['Easycase']['priority']==0){
                        $priority="High";
                    }elseif($value['Easycase']['priority']==1){
                        $priority="Medium";
                    }else{
                        $priority="Low";
                    }
		    $case = array("id" => $value['Easycase']['uniq_id'], "tsk_id" => $value['Easycase']['id'], "title" => $value['Easycase']['title'],
			"priority" => $priority, "type" => $value['Easycase']['type_id'],
			"status" => $value['Easycase']['status'], "assign_to" => $value['0']['Assigned'],"assign_id" => $value['User']['assign_id'],
			"legend" => $value['Easycase']['legend'],"is_active" => $value['Easycase']['isactive'],
			"due_date" => $value['Easycase']['due_date'], "created_by" => $createdBy,"created_id"=>$createdId,
			"updated_by" => $updatedBy,"formatted_updated_date" => $updated_date,
			"created_date" => $value['Easycase']['actual_dt_created'],"updated_date" => $value['Easycase']['dt_created'],"age"=>$age,
			"case_no" => $value['Easycase']['case_no'],"case_count" => $Easycases[$key][0][0]['comment_count'],
			"project_id" => $value['Project']['uniq_id'],
			"message" => strip_tags($value['Easycase']['message']),
                        "comment_count" => $Easycases[$key][0][0]['comment_count']);
		    $attach = array();
		    if ($value['Easycase']['format'] != 2) {
			$CaseFiles = $this->Easycase->getCaseFiles($value['Easycase']['id']);
			if (count($CaseFiles)) {
			    foreach ($CaseFiles as $fkey => $getFiles) {
				//Set attachments detail
				$size = $frmt->getFileSize($getFiles['CaseFile']['file_size']);
				$attach[] = array("id" => $getFiles['CaseFile']['id'], "name" => $getFiles['CaseFile']['file'],
				    "size" => $size, "downloadurl" => $getFiles['CaseFile']['downloadurl']);
			    }
			}
		    }                  
                    
		    $data[$key] = $case;                                      
		    if (!empty($attach)) {
			$data[$key]['attachments'] = $attach;
		    }
		}
	    }
	}
	return $data;
    }
    
    /**
     * @method getAge
     * @param timezone:array, created date:date time, timezone object, datetime  object
     * @return string
     * @author Sunil
     */
    
    function getAge($timezone = NULL, $createdDt = NULL,$tz,$dt, $flag) {
	$age = '';
	if(isset($createdDt)){
	    $currentDt = $tz->GetDateTime($timezone['id'],$timezone['gmt_offset'],$timezone['dst_offset'],$timezone['code'],GMT_DATETIME,"datetime");
	    $actualDt = $tz->GetDateTime($timezone['id'],$timezone['gmt_offset'],$timezone['dst_offset'],$timezone['code'],$createdDt,"datetime");
	    if(intval($flag)){
		$age = $dt->dateFormatOutputdateTime_day($actualDt,$currentDt);
	    }else{
		$age = $dt->facebook_style($actualDt,$currentDt,'date');
	    }
	}
	return $age;
    }
    
    /**
     * @method getProjectsAndTasks
     * @param  auth token:string, company id:string
     * @return json string objects: the project details and task details with attachments.
     * @author Sunil
     */
    
    function getProjectsAndTasks() {
	$this->layout = "ajax";
	if (isset($this->request->data['auth_token']) && isset($this->request->data['companyId'])) {
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $this->request->data['auth_token']), array('id','uniq_id'));
	    if(!empty($user)){
		$companyId = $this->request->data['companyId'];
		$this->loadModel('Company');
		$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyId), array('id'));

		//Getting latest project for a company
		$this->loadModel('ProjectUser');
		$sql = "SELECT Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN 
		    (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id)
		    WHERE ProjectUser.user_id='".$user['User']['id']."' AND ProjectUser.company_id='".$company['Company']['id']."'
		    ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
		$ProjectUser = $this->ProjectUser->query($sql);
		
		//Getting project details
		$sql = "SELECT DISTINCT Project.id, Project.uniq_id, Project.name FROM project_users AS ProjectUser LEFT JOIN
		    projects AS Project ON (Project.id= ProjectUser.project_id) WHERE ProjectUser.user_id='" . $user['User']['id'] . "'
		    AND ProjectUser.company_id='" . $company['Company']['id'] . "' AND Project.isactive='1' ORDER BY Project.name ASC";
		$this->loadModel('Project');
		$Project = $this->Project->query($sql);
		
		$projectId = '';
		if (isset($ProjectUser) && !empty($ProjectUser)) {
		    $projectId = $ProjectUser['0']['Project']['uniq_id'];
		}else {
		    $projectId = $Project['0']['Project']['uniq_id'];
		}
		
		if (isset($Project) && !empty($Project)) {
		    //Set status
		    $data['code'] = 2000;
		    $data['status'] = "OK";
		
		    //Set project details
		    foreach ($Project as $key => $value) {
			$projectData = array("id" => $value['Project']['uniq_id'], "name" => $value['Project']['name']);
			if($value['Project']['uniq_id'] == $projectId){
			    $projectData = array_merge($projectData,array("selected"=>"1"));
			}
			$data['results']['projects'][$key] = $projectData;
		    }

		    //Getting all tasks.
		    if(isset($projectId) && trim($projectId)){
			$tasks = $this->getTasks($user['User']['uniq_id'], '', $projectId);
		    }
		    $data['results']['tasks'] = $tasks;
		}

	    }else{
		$data['code'] = 2004;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2007;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token or company id is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    /**
     * @method getTaskDetails
     * @param  auth token:string, project id:string, task id:string
     * @return json string objects: the task details with attachments.
     * @author Sunil
     */
    
    function getTaskDetails() {
	$this->layout = "ajax";
	$reqst_data= (array) $this->request->input(json_decode,true);
	
	if(empty($reqst_data)){
	    $reqst_data= $this->request->query;
	} 
	
	if (isset($reqst_data['auth_token']) && isset($reqst_data['project_id']) && isset($reqst_data['task_id']) && isset($reqst_data['company_id'])) {
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $reqst_data['auth_token']), array('id','timezone_id'));
	    if(!empty($user)){
		$this->loadModel('Project');
                $project = $this->Project->getProjectFields(array('Project.uniq_id' => $reqst_data['project_id']), array('id',"workflow_id"));
		$this->loadModel('Company');
		$this->loadModel('Timezone');
		$timezone = $this->Timezone->find('first', array("conditions" => array("Timezone.id" => $user['User']['timezone_id'])));
		$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $reqst_data['company_id']), array('id'));
		if(!empty($project)){ 
		    
		    $arg['prjid'] = $project['Project']['id'];
		    $arg['projFil'] = $reqst_data['project_id'];
		    $arg['caseUniqId'] = $reqst_data['task_id'];
			$arg['compId'] = $company['Company']['id'] ;
			$arg['user_id'] =$user['User']['id'];
			$arg['time_zone'] = $timezone['Timezone'];
			//print_r($arg);exit;
			
		    App::import('Controller', 'Easycases');
		    $Easycases = new EasycasesController();
		    //Load model, components.
		    $Easycases->constructClasses();
		    $case_details = $Easycases->case_details($arg);
                    //print_r($case_details);exit;
		    $estimated_hours=$case_details['estimated_hours']/3600;
		    //Set status						
		    $data['code'] = 2000;
		    $data['status'] = "OK";//print_r($case_details);exit;
		//    $data['results'][]=$case_details;
                   // csLgndRep
                   $data['results']['edit_task']=$this->getTaskPermission($reqst_data['company_id']);
                   $data['results']['user_type']=$this->getUserType($user['User']['id'],$reqst_data['company_id']);
			
                   $data['results']['task_title']= strip_tags($case_details['caseTitle']);
		     $data['results']['project_id'] = $case_details['projUniqId'];
		    $data['results']['hours'] = $case_details['hours'];
			$data['results']['estimated_hours'] = $estimated_hours;
		    $data['results']['completed_task'] = $case_details['completedtask'];
		    $data['results']['total'] = $case_details['total'];
		    
		    if(isset($case_details['allMems']) && !empty($case_details['allMems'])) {
			foreach($case_details['allMems'] as $key => $value){
                            $case_details['allMems'][$key]['User']['url']=(isset($value['User']['photo']) && !empty($value['User']['photo']))?HTTP_ROOT."users/image_thumb/?type=photos&file=".$value['User']['photo']."&sizex=50&sizey=50&quality=100":'';
			    $data['results']['users'][] = $case_details['allMems'][$key]['User'];
			}
		    }
			if(isset($case_details['taskUsrs']) && !empty($case_details['taskUsrs'])){
				foreach($case_details['taskUsrs'] as $keys => $val){
                                        $case_details['taskUsrs'][$keys]['User']['url']=(isset($val['User']['photo']) && !empty($val['User']['photo']))?HTTP_ROOT."users/image_thumb/?type=photos&file=".$val['User']['photo']."&sizex=50&sizey=50&quality=100":'';
					$data['results']['taskUsrs'][] =$case_details['taskUsrs'][$keys]['User'];
				}
			}
		    if(isset($case_details['caseMem']) && !empty($case_details['caseMem'])) {
			foreach($case_details['caseMem'] as $key => $value){
			    $data['results']['case_members'][] = $case_details['caseMem'][$key]['User'];
			}
		    }
		    
			//assigned id ;
			$user_unqid = $this->User->getUserFields(array('User.id' => $case_details['asgnUid']), array('uniq_id'));
			if($case_details['crtdtTtl'] == $case_details['lupdtTtl']){
				$data['results']['formatted_created_on'] = $case_details['crtdtTtl'];
			}
			else{
				$data['results']['formatted_last_updated'] = $case_details['lupdtTtl'];
			}
		    $data['results']['description_message'] = strip_tags($case_details['csMsgRep']);
		    $data['results']['created_on'] = $case_details['crtdt'];
		    
		    $data['results']['created_by'] = $case_details['csby'];
		    $data['results']['created_id'] = $case_details['usrUniqId'];
		    $data['results']['post_by'] = $case_details['shtNm'];
		    $data['results']['last_updated'] = $case_details['lupdtm'];
			$data['results']['last_updated_by'] = $case_details['lstUpdBy'];
		    
		    $data['results']['assign_to'] = $case_details['asgnTo'];
			$data['results']['assign_to_id'] = $user_unqid['User']['uniq_id'];
		    $data['results']['milestone'] = $case_details['mistn'];
		    $data['results']['due_date'] = $case_details['csDuDtFmtT'];
		    $data['results']['priority'] = $case_details['protyTtl'];
		    $data['results']['type'] = $case_details['typImage'];
			$data['results']['task_type'] =$case_details['taskTyp'];
			$data['results']['csNoRep'] = $case_details['csNoRep'];//----case_no
			$data['results']['tsk_legend'] = $case_details['csLgndRep'] ; // legend type
                        $this->loadModel('Status');
                        $legendDtl = $this->Status->find('first', array('conditions'=>array('Status.id'=>$case_details['csLgndRep']), 'fields'=>array('Status.name', 'Status.color')));
			$data['results']['tsk_legend_name'] = $legendDtl['Status']['name'] ; // legend type
			$data['results']['tsk_legend_color'] = $legendDtl['Status']['color'] ; // legend type
                        
			$data['results']['tsk_id'] = $case_details['csAtId']; // task id 
			$data['results']['is_active'] = $case_details['is_active']; // task id
                        
                        
                          /***Task status ****/
			$this->loadModel("Status");
			$status_type = $this->Status->find('all',array('conditions'=>array('workflow_id'=>$project['Project']['workflow_id']),'order'=>"seq_order ASC"));			
                        foreach ($status_type as $key => $value) {
				$statusTypeData = array("id" => $value['Status']['id'], "name" => $value['Status']['name'], "color" => $value['Status']['color'], "percentage" => $value['Status']['percentage'], "seq_order" => $value['Status']['seq_order']);
				$data['results']['status_type_details'][$key] = $statusTypeData;
			}
                        /*End*/
                        
                        
                        
                        
			if(isset($case_details['sqlcasedata']) && !empty($case_details['sqlcasedata'])) {
			foreach($case_details['sqlcasedata'] as $key => $value){
			    
			 //   unset($case_details['sqlcasedata'][$key]['Easycase']['rply_files']);
			 unset($value['Easycase']['rply_files']);//remove the files present in reply from array
                         
                         if(strpos($case_details['sqlcasedata'][$key]['Easycase']['replyCap'],'Modified') === false && $case_details['sqlcasedata'][$key]['Easycase']['wrap_msg'] !='' ){  
//                            $case_details['sqlcasedata'][$key]['Easycase']['message']=  strip_tags($case_details['sqlcasedata'][$key]['Easycase']['replyCap']);
//                         }else{
                           $case_details['sqlcasedata'][$key]['Easycase']['message']=  strip_tags($case_details['sqlcasedata'][$key]['Easycase']['wrap_msg']);
  //                         }
                           $case_details['sqlcasedata'][$key]['Easycase']['userArr']['User']['url']=(isset($value['Easycase']['userArr']['User']['photo']) && !empty($value['Easycase']['userArr']['User']['photo']))?HTTP_ROOT."users/image_thumb/?type=photos&file=".$value['Easycase']['userArr']['User']['photo']."&sizex=50&sizey=50&quality=100":'';

                           $data['results']['tasks'][] = $case_details['sqlcasedata'][$key]['Easycase'];
                         }
			}
		    } else{
				$data['results']['tasks']=array();
			}
                        $data['results']['comment_count']= $case_details['sqlcasedata']['comment_count'];
			$this->loadModel("Type");
			$companies_id=array("0",$company['Company']['id']); //use of this comany id to get the task types..
			$type = $this->Type->find('all',array('conditions'=>array('Type.company_id'=>$companies_id)));
			foreach ($type as $key => $value) {
				$typeData = array("id" => $value['Type']['id'], "name" => $value['Type']['name'], "short_name" => $value['Type']['short_name']);
				$data['results']['task_type_details'][$key] = $typeData;
			}                      

		}else{
		    $data['code'] = 2005;
		    $data['status'] = "failure";
		    $data['msg'] = "Project id is invalid!";
		}
	    }else{
		$data['code'] = 2004;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token or Project id or Task id is invalid!";
	}
	print json_encode($data);
	exit;
    }
    

/**
 *@method  Private Company_statistics(string $authtoken) It will return the statisicts of company.
 *@author GDR <support@orangescrum.com>
 * @return JSON json array of data
 */	
	function company_statistics(){
		$this->loadModel('UserSubscription');
		$comp_sts = $this->UserSubscription->getstatistics();
		$json_arr['basic'] = $comp_sts[1]; 
		$json_arr['premium'] = $comp_sts[2];
		$json_arr['pending'] = $comp_sts['pending'];
		$json_arr['conv_per'] = $comp_sts['conv_per'];
		$json_arr['total_conv'] = $comp_sts['total_conv'];
		if ($comp_sts) {
			//Set status
			$data['code'] = 2000;
			$data['status'] = "OK";
			$data['statistics'] = $json_arr;
		}else{
			$data['code'] = 2000;
			$data['status'] = "OK";
			$data['msg'] = "Not Record Found";
		}
		print json_encode($data);exit;
	}
	/*
	 function to get list of user associated with a certain projects
	 * @method get_user_list
     * @param  auth token:string, project id:string, company id:string
     * @return json string objects: the user details associated with projects.
	*/
	function get_user_list(){
		$this->layout= "ajax";
		$auth_token = $companyUniqId = $projectUniqId="";
		$rqst_data=(array) $this->request->input(json_decode,true);	
		if(empty($rqst_data)){
			$rqst_data= $this->request->query;
		}		
		if(isset($rqst_data['auth_token'])){
			$auth_token=$rqst_data['auth_token'];
		}
		if(isset($rqst_data['project_id'])){
			$projectUniqId=$rqst_data['project_id'];
		}
		if(isset($rqst_data['company_id'])){
			$companyUniqId=$rqst_data['company_id'];
		}		
		$this->loadModel('User');
		$this->loadModel('Project');
		$this->loadModel('Company');
		$this->loadModel('ProjectUser');
		$project_id=$this->Project->getProjectFields(array('Project.uniq_id' => $projectUniqId), array('id','workflow_id'));
                $workFlow=$project_id['Project']['workflow_id'];
		$project_id=$project_id['Project']['id'];
		$company_id=$this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('id'));
		$company_id=$company_id['Company']['id'];
		$sql = "SELECT User.id,User.uniq_id,User.name,User.last_name,User.short_name,User.email FROM project_users AS ProjectUser LEFT JOIN (users AS User ) ON (ProjectUser.user_id=User.id)
		    WHERE ProjectUser.project_id='".$project_id."' AND ProjectUser.company_id='".$company_id."' AND User.id IS NOT NULL
		     GROUP BY ProjectUser.user_id";	
                $ProjectUser = $this->ProjectUser->query($sql);
		if($ProjectUser){
		$data['code'] = 2000;
		$data['status'] = "OK";
		$data['results']['auth_token'] = $auth_token;
		//Set user details.		
			foreach($ProjectUser as $key=>$value)
			{
				$userData = array("id" => $value['User']['id'], "uniq_id"=>$value['User']['uniq_id'] ,"first_name" => $value['User']['name'],"last_name"=> $value['User']['last_name'],"short_name" => $value['User']['short_name'], "email" => $value['User']['email']);
				$data['results']['user']['info'][$key] =$userData;
			}
                        
                /***Task status ****/
                $this->loadModel("Status");
                $status_type = $this->Status->find('all',array('conditions'=>array('workflow_id'=>$workFlow),'order'=>"seq_order ASC"));			
                foreach ($status_type as $key => $value) {
                        $statusTypeData = array("id" => $value['Status']['id'], "name" => $value['Status']['name'], "color" => $value['Status']['color'], "percentage" => $value['Status']['percentage'], "seq_order" => $value['Status']['seq_order']);
                        $data['results']['status_type_details'][$key] = $statusTypeData;
                }
                /*End*/         
                        
                        
		$this->loadModel("Type");
		$companies_id=array("0",$company_id); //use of this comany id to get the task types..
		$type = $this->Type->find('all',array('conditions'=>array('Type.company_id'=>$companies_id)));
	//	pr($type);exit;
		foreach ($type as $key => $value) {
			$typeData = array("id" => $value['Type']['id'], "name" => $value['Type']['name'], "short_name" => $value['Type']['short_name']);
			$data['results']['task_type'][$key] = $typeData;
		}
		}
		else{
				$data['code'] = 2009;
			    $data['status'] = "failure";
			    $data['msg'] = "No user assigned";
		}
		print json_encode($data);
	exit;
	}
    /*
	 function to get list of user associated with a certain projects
	 * @method company_user_list
     * @param  auth token:string, company id:string
     * @return json string objects: the user details associated with Company.
	*/
    function company_user_list(){
        $this->layout="ajax";
        $auth_token = $companyUniqId = "";
	$rqst_data=(array) $this->request->input(json_decode,true);		
            if(isset($rqst_data['auth_token'])){
		$auth_token=$rqst_data['auth_token'];
            }            
            if(isset($rqst_data['company_id'])){
		$companyUniqId=$rqst_data['company_id'];
            }		
            $this->loadModel('User');            
            $this->loadModel('Company');
            $this->loadModel('CompanyUser');
            $sql = "SELECT User.id,User.uniq_id,User.name,User.last_name,User.short_name,User.email FROM company_users AS CompanyUser LEFT JOIN (users AS User ) ON (CompanyUser.user_id=User.id)
		    WHERE CompanyUser.company_uniq_id='".$companyUniqId."' GROUP BY CompanyUser.user_id";	
        //    echo $sql;exit;
            $CompanyUser = $this->CompanyUser->query($sql);
        //    pr($CompanyUser);exit;
            if($CompanyUser){
		$data['code'] = 2000;
		$data['status'] = "OK";
		$data['results']['auth_token'] = $auth_token;
                foreach($CompanyUser as $key=>$value){
                    $userData = array("id" => $value['User']['id'], "uniq_id"=>$value['User']['uniq_id'] ,"first_name" => $value['User']['name'],"last_name"=> $value['User']['last_name'],"short_name" => $value['User']['short_name'], "email" => $value['User']['email']);
                    $data['results']['user']['info'][$key] =$userData;
		}
            }
            else{
		$data['code'] = 2009;
                $data['status'] = "failure";
                $data['msg'] = "No user in the company";
            }
        echo json_encode($data);exit;                     
    }
    function casePost() {
	$this->layout = "ajax";
	$auth_token = $companyUniqId = $projectUniqId = $task_name = $msg = $due_date = $assign_to = $type_id = $priority = $email_user = $allfiles = $cmt_tsk_id='';
	
	
	$tsk_crt_data=(array) $this->request->input(json_decode,true);
	if(empty($tsk_crt_data)){
			$tsk_crt_data= $this->request->query;
		}
	if(isset($tsk_crt_data['auth_token'])){
		 $auth_token = $tsk_crt_data['auth_token'];
	}
	//pr($tsk_crt_data);exit;
	if(trim($auth_token)){//Check given auth token is valid or not.
	    //Getting user id
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id'));
	    if(!empty($user)){
		
		//Set company uniq id
		
		if(isset($tsk_crt_data['company_id'])){
			$companyUniqId = $tsk_crt_data['company_id'];
		}
		
		//Set project uniq id
		
		if(isset($tsk_crt_data['project_id'])){
			$projectUniqId = $tsk_crt_data['project_id'];
		}
		//Set title
		
		if(isset($tsk_crt_data['title'])){
			$task_name = $tsk_crt_data['title'];
		
		
		//Set due date according to condition
		
		if(isset($tsk_crt_data['due_date'])){
			$due_date = $tsk_crt_data['due_date'];
		}
		$userDate = $this->Tmzone->GetDateTime(SES_TIMEZONE,TZ_GMT,TZ_DST,TZ_CODE,gmdate("Y-m-d H:i:s"),"datetime");
		if($due_date == 'today') {
		    $CS_due_date = date('m/d/Y',strtotime($userDate));
		} else if($due_date == 'monday') {
		    $CS_due_date = date('m/d/Y',strtotime($userDate."next Monday"));
		} else if($due_date == 'friday') {
		    $CS_due_date = date('m/d/Y',strtotime($userDate."next Friday"));
		} else if($due_date == 'tomorrow') {
		    $CS_due_date = date('m/d/Y',strtotime($userDate."+1 day"));
		} else if($due_date == 'No Due Date') {
		    $CS_due_date = 'No Due Date';
		}
		
		//Set assign to and finding assiner id.
		
		if(isset($tsk_crt_data['assign_to']) && !empty($tsk_crt_data['assign_to'])){
			$assign_to = $tsk_crt_data['assign_to'];
		}
		else{
			$assign_to = $tsk_crt_data['auth_token'];
		}
		$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
		
		//Set task type
		
		if(isset($tsk_crt_data['type_id']) && !empty($tsk_crt_data['type_id'])){
			$type_id = $tsk_crt_data['type_id'];
		}
		else{
			$type_id = 2;
		}
		
		//Set priority
		
		if(isset($tsk_crt_data['priority']) && !empty($tsk_crt_data['priority'])){
			$priority = $tsk_crt_data['priority'];
		}
		else{
			$priority = 2;
		}
		//Set email id.
		
		if(isset($tsk_crt_data['email_user']) && !empty($tsk_crt_data['email_user'])){
			$email_user[] = $tsk_crt_data['email_user'];
		}
		//Getting email users id.
		if(isset($email_user) && !empty($email_user)) {
		    foreach($email_user as $key => $value) {
			$emailusers = $this->User->getUserFields(array('User.uniq_id' => $value), array('id'));
			$emailUser[] =  $emailusers['User']['id'];
		    }
		}
		
		//Set descriptions
		
		if(isset($tsk_crt_data['message']) && !empty($tsk_crt_data['message'])){
			$msg = $tsk_crt_data['message'];
		}
		//Set all attached files.
		
		if(isset($tsk_crt_data['allfiles']) && !empty($tsk_crt_data['allfiles'])){
			$allfiles = $tsk_crt_data['allfiles'];
		}
		if(isset($tsk_crt_data['estimated_hours']) && !empty($tsk_crt_data['estimated_hours'])){
			$estimated_hours = $tsk_crt_data['estimated_hours'];
		}
		//Getting project details.
		$this->loadModel('Project');
		$project = $this->Project->getProjectFields(array('Project.uniq_id'=>$projectUniqId),array());
		//Getting maximum case number of a project.
		 $this->loadModel('Easycase');
		$sql = "SELECT MAX(Easycase.case_no)+1 AS case_no  FROM `easycases` AS Easycase where  Easycase.project_id=".$project['Project']['id'];
		$caseNo = $this->Easycase->query($sql); 
				
		//Formating an array for easycase.
		$case['CS_project_id'] = $project['Project']['uniq_id'];
		$case['CS_user_id']= $user['User']['id'];
		$case['CS_type_id'] = $type_id;
		$case['CS_due_date'] = $CS_due_date;
		$case['estimated_hours']= $estimated_hours;
		$case['hours'] = 0;
		$case['completed'] = 0;
		$case['CS_priority'] = $priority;
		$case['CS_assign_to'] = $assignUser['User']['id'];
		$case['CS_legend'] = 1;
		$case['CS_message'] = $msg;
		$case['user_uniq_id'] = $auth_token;
		$case['CS_case_no'] = $caseNo['0']['0']['case_no'];
		$case['datatype'] = 0;
		$case['CS_id'] = 0;
		$case['CS_title'] = $task_name;
		$case['CS_istype'] = 1;//Post case
		$case['prelegend'] = 0;
		$case['emailUser'] = $emailUser;
		$case['CS_milestone'] = '';
		$case['allUser'] = '';
		$case['allFiles'] = $allfiles;
		$case['is_chrome_extension'] = 1;
		//Get company short name and set domain url for email content.
		$this->loadModel('Company');
		$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('seo_url','id'));
		$short_name = trim($company['Company']['seo_url']);
		$auth_domain = str_replace("app.",$short_name.".",HTTP_ROOT);
		$case['auth_domain'] = $auth_domain;
		
		App::import('Controller', 'Easycases');
		$Easycases = new EasycasesController();
		//Load model, components.
		$Easycases->constructClasses();
		
		
		//Post a case.
		$case_result = $Easycases->ajaxpostcase($case);
		
		$res = json_decode($case_result,true);//pr($res);exit;
		if(isset($res['success']) && ($res['success']=='success')) {
		    //Formating an array for email to users.
		    $email['projId'] = $res['projId'];
		    $email['emailUser'] = $emailUser;
		    $email['allfiles'] = $res['allfiles'];
		    $email['caseNo'] = $res['caseNo'];
		    $email['emailTitle'] = $res['emailTitle'];
		    $email['emailMsg'] = '';
		    $email['casePriority'] = $res['casePriority'];
		    $email['caseTypeId'] = $res['caseTypeId'];
		    $email['msg'] = $res['msg'];
		    $email['emailbody'] = $res['emailbody'];
		    $email['caseIstype'] = $res['caseIstype'];
		    $email['csType'] = $res['csType'];
		    $email['caUid'] = $res['caUid'];
		    $email['caseid'] = $res['caseid'];
		    $email['caseUniqId'] = $res['caseUniqId'];
		    $email['auth_domain'] = $auth_domain;
                    $email['company_id'] =$company['Company']['id'];
		    		    
		    //Sent email to users.
		    $email_result = $Easycases->ajaxemail($email);
		    $email_res = json_decode($email_result,true);
			//Set status
			$data['code'] = 2000;
			$data['status'] = "OK";
			$data['msg'] = "Your task has been posted.";
		   
		  } else{
			  $data['code'] = 2007;
			  $data['status'] = "failure";
		      $data['msg'] = "Error in task posting.";
		  }
		} else {
		    $data['code'] = 2007;
		    $data['status'] = "failure";
		    $data['msg'] = "Task title cannot be blank.";
		}
	    } else {
		$data['code'] = 2006;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token is invalid!";
	}
	print json_encode($data);
	exit;
    }
    
    function fileupload () {
	$this->layout = "ajax";
	$file = $this->params['data']['Easycase'];
	
	App::import('Controller', 'Easycases');
	$Easycases = new EasycasesController();
	//Load model, components.
	$Easycases->constructClasses();

	$data = $Easycases->fileupload($file);
	echo $data;exit;
    }
    
    function fileremove() {
	$this->layout = 'ajax';
	$filename = $this->params['data']['filename'];
	App::import('Controller', 'Easycases');
	$Easycases = new EasycasesController();
	//Load model, components.
	$Easycases->constructClasses();

	$Easycases->fileremove($filename);
	exit;
    }
    
    
    function taskPost() {
	$this->layout = "ajax";
	$auth_token = $companyUniqId = $projectUniqId = $task_name = $msg = $due_date = $assign_to = $type_id = $priority = $email_user = $allfiles = $cmt_tsk_id='';
	
	$tsk_crt_data=(array) $this->request->input(json_decode,true);
//        pr($tsk_crt_data);exit;
	if(empty($tsk_crt_data)){
            $tsk_crt_data= $this->request->query;
        }
	if(isset($tsk_crt_data['auth_token'])){
            $auth_token = $tsk_crt_data['auth_token'];
	}//echo $auth_token;exit;
	
        if(trim($auth_token)){//Check given auth token is valid or not.
	    //Getting user id
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id','timezone_id'));
            
	    if(!empty($user)){
		$view = new View($this);
		$tz = $view->loadHelper('Tmzone');
		//set timezone id
		$this->loadModel('Timezone');
                $timezone = $this->Timezone->find('first', array("conditions" => array("Timezone.id" => $user['User']['timezone_id'])));
		$timezone_id= $timezone['Timezone']['id'] ;
		$timezone_GMT= $timezone['Timezone']['gmt_offset'] ;
		$timezone_DST= $timezone['Timezone']['dst_offset'] ;
		$timezone_code= $timezone['Timezone']['code'] ;
                
		if(isset($tsk_crt_data['company_id'])){
                    $companyUniqId = $tsk_crt_data['company_id'];
		}
		if(isset($tsk_crt_data['project_id'])){
                    $projectUniqId = $tsk_crt_data['project_id'];
		}
                
		//Getting project details.
                $this->loadModel('Project');
                $project = $this->Project->getProjectFields(array('Project.uniq_id'=>$projectUniqId),array());
			
		//Getting company fields
		$this->loadModel('Company');
                $company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('seo_url','id'));
			
                //Getting maximum case number of a project.
               
		if(!isset($tsk_crt_data['CS_id']) && empty($tsk_crt_data['CS_id']) && !isset($tsk_crt_data['taskid']) && empty($tsk_crt_data['taskid'])){
			if(isset($tsk_crt_data['title'])){
				$task_name = $tsk_crt_data['title'];
			}
			if(isset($tsk_crt_data['due_date'])){
				$due_date = $tsk_crt_data['due_date'];
			}else{
				$due_date=NULL;
			}
			$userDate = $tz->GetDateTime($timezone_id, $timezone_GMT, $timezone_DST, $timezone_code, GMT_DATETIME,"datetime"); //gmdate("Y-m-d H:i:s")
			if($due_date == 'today') {
				$CS_due_date = date('m/d/Y',strtotime($userDate));
			} else if($due_date == 'monday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Monday"));
			} else if($due_date == 'friday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Friday"));
			} else if($due_date == 'tomorrow') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."+1 day"));
			} else if($due_date == 'No Due Date') {
				$CS_due_date = 'No Due Date';
			} else{
				$CS_due_date = $due_date;
			}
			if(isset($tsk_crt_data['assign_to']) && !empty($tsk_crt_data['assign_to'])){
				$assign_to = $tsk_crt_data['assign_to'];
				$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
				$assign_to = $assignUser['User']['id'];
			}
			else{
				$assign_to = $tsk_crt_data['auth_token'];
				$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
				$assign_to =$assignUser['User']['id'];
			}
			
			if(isset($tsk_crt_data['type_id']) && !empty($tsk_crt_data['type_id'])){
				$type_id = $tsk_crt_data['type_id'];
			}
			else{
				$type_id = 2;
			}			
			//Set priority
			if(isset($tsk_crt_data['priority']) && (!empty($tsk_crt_data['priority']) || $tsk_crt_data['priority']==0 )){
				$priority = $tsk_crt_data['priority'];
			}
			else{
				$priority = 2;
			}
			//Set email id.
			if(isset($tsk_crt_data['email_user']) && !empty($tsk_crt_data['email_user'])){
				$email_user = $tsk_crt_data['email_user'];
			}
			//Getting email users id.
			if(isset($email_user) && !empty($email_user)) { 
				foreach($email_user as $key => $value) {
				$emailusers = $this->User->getUserFields(array('User.uniq_id' => $value), array('id'));                             ;
				$emailUser[] =  $emailusers['User']['id'];
				}
			}                       
			//Set descriptions
			if(isset($tsk_crt_data['description_message']) && !empty($tsk_crt_data['description_message'])){
				$msg = $tsk_crt_data['description_message'];
			}
			//Set all attached files.
			if(isset($tsk_crt_data['allfiles']) && !empty($tsk_crt_data['allfiles'])){
				$allfiles = $tsk_crt_data['allfiles'];
			}
			//Set Estimated hour
			if(isset($tsk_crt_data['estimated_hours']) && !empty($tsk_crt_data['estimated_hours'])){
				$estimated_hours = $tsk_crt_data['estimated_hours'];
			}else{
				$estimated_hours=0;
			}
			//Set hours
			$hours=isset($tsk_crt_data['hours']) ? $tsk_crt_data['hours'] :0;
			//Set Completed
			$complete=isset($tsk_crt_data['completed']) ? $tsk_crt_data['completed'] :0;
			//Set legend
			$legend=isset($tsk_crt_data['CS_legend']) ? $tsk_crt_data['CS_legend'] :1;
			//set datatype
			$datatype=isset($tsk_crt_data['datatype']) ? $tsk_crt_data['datatype'] :0;
			//set Milestone
			$CS_milestone=isset($tsk_crt_data['CS_milestone']) ? $tsk_crt_data['CS_milestone'] :'';
			//set is client
			$is_client=isset($tsk_crt_data['is_client']) ? $tsk_crt_data['is_client'] :0;
			//Formating an array for easycase.
			$case['CS_project_id'] = $projectUniqId;
			$case['company_id'] = $company['Company']['id'];
			$case['CS_user_id']= $user['User']['id'];
			$case['CS_type_id'] = $type_id;
			$case['CS_due_date'] = $CS_due_date;
			$case['estimated_hours']= ($estimated_hours*3600);
			$case['hours'] = $hours;
			$case['completed'] = $complete;
			$case['CS_priority'] = $priority;
			$case['CS_assign_to'] = $assign_to;
			$case['CS_legend'] = $legend;
			$case['CS_message'] = $msg;
			$case['user_uniq_id'] = $auth_token;
		//	$case['CS_case_no'] = $caseNo['0']['0']['case_no'];
			$case['datatype'] = $datatype;
			$case['CS_id'] = 0;
			$case['CS_title'] = $task_name;
			$case['CS_istype'] = 1;//Post case
			$case['prelegend'] = 0;
			$case['emailUser'] = $emailUser;
			$case['CS_milestone'] = $CS_milestone;
			$case['allUser'] = '';
			$case['allFiles'] = $allfiles;
			$case['postdata'] = 'post';
			$case['is_client'] = $is_client;
			$case['is_chrome_extension'] = 0;
		//	pr($case);exit;
		}
		//if task id is given
		else if(isset($tsk_crt_data['CS_id']) && !empty($tsk_crt_data['CS_id'])){ 
			$CS_id=$tsk_crt_data['CS_id']; //task id of replied task
			$this->loadModel('Easycase');
			$tsk_details=$this->Easycase->query("SELECT easycase.* FROM easycases as easycase WHERE easycase.id='".$CS_id."'  AND easycase.project_id=".$project['Project']['id']);
			if(isset($tsk_crt_data['due_date'])){
				$due_date = $tsk_crt_data['due_date'];
			}else{
				$due_date=$tsk_details[0]['easycase']['due_date'];
			}
			$userDate = $tz->GetDateTime($timezone_id, $timezone_GMT, $timezone_DST, $timezone_code, GMT_DATETIME, "datetime");//gmdate("Y-m-d H:i:s")
			
			if($due_date == 'today') {
				$CS_due_date = date('m/d/Y',strtotime($userDate));
			} else if($due_date == 'monday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Monday"));
			} else if($due_date == 'friday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Friday"));
			} else if($due_date == 'tomorrow') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."+1 day"));
			} else if($due_date == 'No Due Date') {
				$CS_due_date = 'No Due Date';
			} else{
				$CS_due_date = $due_date;
			}
			
			if(isset($tsk_crt_data['assign_to']) && !empty($tsk_crt_data['assign_to'])){
				$assign_to = $tsk_crt_data['assign_to'];
				$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
				$assign_to = $assignUser['User']['id'];
			}
			else{
				
				$assign_to = $tsk_details[0]['easycase']['assign_to'];
				$assignUser = $this->User->getUserFields(array('User.id' => $assign_to), array('uniq_id'));
				$assign_to = $assignUser['User']['id'];				
			}
			if(isset($tsk_crt_data['type_id']) && !empty($tsk_crt_data['type_id'])){
				$type_id = $tsk_crt_data['type_id'];
			}
			else{
				$type_id = $tsk_details[0]['easycase']['type_id'];
			}			
			//Set priority
			if(isset($tsk_crt_data['priority']) && !empty($tsk_crt_data['priority'])){
				$priority = $tsk_crt_data['priority'];
			}
			else{
				$priority = $tsk_details[0]['easycase']['priority'];
			}
			//Set email id.
			if(isset($tsk_crt_data['email_user']) && !empty($tsk_crt_data['email_user'])){
				$email_user = $tsk_crt_data['email_user'];
			}
			//Getting email users id.
			if(isset($email_user) && !empty($email_user)) {
				foreach($email_user as $key => $value) {
				$emailusers = $this->User->getUserFields(array('User.uniq_id' => $value), array('id'));
				$emailUser[] =  $emailusers['User']['id'];
				}
			}			
			//Set descriptions
			if(isset($tsk_crt_data['message']) && !empty($tsk_crt_data['message'])){
				$msg = $tsk_crt_data['message'];
			}else{
				$msg ="";
			}
			//Set all attached files.
			if(isset($tsk_crt_data['allfiles']) && !empty($tsk_crt_data['allfiles'])){
				$allfiles = $tsk_crt_data['allfiles'];
			}
			//Set Estimated hour
			if(isset($tsk_crt_data['estimated_hours']) && !empty($tsk_crt_data['estimated_hours'])){
				$estimated_hours = $tsk_crt_data['estimated_hours'];
			}else{
				$estimated_hours=$tsk_details[0]['easycase']['estimated_hours']/3600;
			}
			//Set hours
			$hours=isset($tsk_crt_data['hours']) ? $tsk_crt_data['hours'] :$tsk_details[0]['easycase']['hours'];
			//Set Completed
			$complete=isset($tsk_crt_data['completed']) ? $tsk_crt_data['completed'] :$tsk_details[0]['easycase']['completed_task'];
			//Set legend
			$legend=isset($tsk_crt_data['CS_legend']) ? $tsk_crt_data['CS_legend'] :$tsk_details[0]['easycase']['legend'];
			//set datatype
			$datatype=isset($tsk_crt_data['datatype']) ? $tsk_crt_data['datatype'] :1;
			//set Milestone
			$CS_milestone=isset($tsk_crt_data['CS_milestone']) ? $tsk_crt_data['CS_milestone'] :'';
			//set is client
			$is_client=isset($tsk_crt_data['is_client']) ? $tsk_crt_data['is_client'] :$tsk_details[0]['easycase']['client_status'];
			//set case number
			if(isset($tsk_crt_data['CS_case_no']) && !empty($tsk_crt_data['CS_case_no'])){
				$CS_case_no =$tsk_crt_data['CS_case_no'] ;
			} 
			else{
				$CS_case_no =$tsk_details[0]['easycase']['case_no'];
			}
			//Formating an array for easycase.
			$case['CS_project_id'] = $projectUniqId;
			$case['CS_user_id']= $user['User']['id'];
			$case['company_id'] =$company['Company']['id'];
			$case['CS_type_id'] = $type_id;
			$case['CS_due_date'] = $CS_due_date;
			$case['estimated_hours']= $estimated_hours;
			$case['hours'] = $hours;
			$case['completed'] = $complete;
			$case['CS_priority'] = $priority;
			$case['CS_assign_to'] = $assign_to;
			$case['CS_legend'] = $legend;
			$case['CS_message'] = $msg;
			$case['user_uniq_id'] = $auth_token;
			$case['CS_case_no'] = $CS_case_no;
			$case['datatype'] = $datatype;
			$case['CS_id'] = $CS_id;
			$case['CS_title'] = "";
			$case['CS_istype'] = 2;//Post case
			$case['prelegend'] = 0;
			$case['emailUser'] = $emailUser;
			$case['CS_milestone'] = $CS_milestone;
			$case['allUser'] = '';
			$case['allFiles'] = $allfiles;
			$case['postdata'] = 'post';
			$case['is_client'] = $is_client;
			$case['timelog']='false';
			$case['is_chrome_extension'] = 0;
			//pr($case);exit;
		}
		
		//Edit of task
		else if(isset($tsk_crt_data['taskid']) && !empty($tsk_crt_data['taskid'])){ 
			$task_id=$tsk_crt_data['taskid']; //task id  
			$this->loadModel('Easycase');

			$tsk_details=$this->Easycase->query("SELECT easycase.* FROM easycases as easycase WHERE easycase.id='".$task_id."'  AND easycase.project_id=".$project['Project']['id']);
                        
			$tsk_unqid =$tsk_details[0]['easycase']['uniq_id'];
			
                        if(isset($tsk_crt_data['due_date'])){
				$due_date = $tsk_crt_data['due_date'];
			}else{
				$due_date=$tsk_details[0]['easycase']['due_date'];
			}
			$userDate = $tz->GetDateTime($timezone_id, $timezone_GMT, $timezone_DST, $timezone_code,GMT_DATETIME,"datetime");
			
                        //gmdate("Y-m-d H:i:s")
			if($due_date == 'today') {
				$CS_due_date = date('m/d/Y',strtotime($userDate));
			} else if($due_date == 'monday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Monday"));
			} else if($due_date == 'friday') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."next Friday"));
			} else if($due_date == 'tomorrow') {
				$CS_due_date = date('m/d/Y',strtotime($userDate."+1 day"));
			} else if($due_date == 'No Due Date') {
				$CS_due_date = 'No Due Date';
			} else{
				$CS_due_date = $due_date;
			}
			//set assign to user 
			if(isset($tsk_crt_data['assign_to']) && !empty($tsk_crt_data['assign_to'])){
				$assign_to = $tsk_crt_data['assign_to'];
				$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
				$assign_to = $assignUser['User']['id'];
			}
			else{
				
				$assign_to = $tsk_details[0]['easycase']['assign_to'];
			} 
		// set Title of the task
			if(isset($tsk_crt_data['title']) && !empty($tsk_crt_data['title'])){
				$task_name = $tsk_crt_data['title'];
			}
			else{
				$task_name = $tsk_details[0]['easycase']['title'];
			}
		//	$assignUser = $this->User->getUserFields(array('User.uniq_id' => $assign_to), array('id'));
			if(isset($tsk_crt_data['type_id']) && !empty($tsk_crt_data['type_id'])){
				$type_id = $tsk_crt_data['type_id'];
			}
			else{
				$type_id = $tsk_details[0]['easycase']['type_id'];
			}			
			//Set priority
			if(isset($tsk_crt_data['priority']) && (!empty($tsk_crt_data['priority']) || $tsk_crt_data['priority']==0) ){
                            $priority = $tsk_crt_data['priority'];
			}
			else{
                            $priority = $tsk_details[0]['easycase']['priority'];
			}
			//Set email id.
			if(isset($tsk_crt_data['email_user']) && !empty($tsk_crt_data['email_user'])){
				$email_user = $tsk_crt_data['email_user'];
			}
			//Getting email users id.
			if(isset($email_user) && !empty($email_user)) {
				foreach($email_user as $key => $value) {
				$emailusers = $this->User->getUserFields(array('User.uniq_id' => $value), array('id'));
				$emailUser[] =  $emailusers['User']['id'];
				}
			}                        
			//Set descriptions
			if(isset($tsk_crt_data['description_message']) && !empty($tsk_crt_data['description_message'])){
				$msg = $tsk_crt_data['description_message'];
			}else{
				$msg = $tsk_details[0]['easycase']['message'];
			}
			//Set all attached files.
			if(isset($tsk_crt_data['allfiles']) && !empty($tsk_crt_data['allfiles'])){
				$allfiles = $tsk_crt_data['allfiles'];
			}
			//Set Estimated hour
			if(isset($tsk_crt_data['estimated_hours']) && !empty($tsk_crt_data['estimated_hours'])){
				$estimated_hours = $tsk_crt_data['estimated_hours'];
			}else{
				$estimated_hours=$tsk_details[0]['easycase']['estimated_hours']/3600;
			}
			//Set hours
			$hours=(isset($tsk_crt_data['hours']) && !empty($tsk_crt_data['hours']))? $tsk_crt_data['hours'] :$tsk_details[0]['easycase']['hours'];
			//Set Completed
			$complete=(isset($tsk_crt_data['completed']) && !empty($tsk_crt_data['completed'])) ? $tsk_crt_data['completed'] :$tsk_details[0]['easycase']['completed_task'];
			//Set legend
			$legend=(isset($tsk_crt_data['CS_legend']) && !empty($tsk_crt_data['CS_legend'])) ? $tsk_crt_data['CS_legend'] :$tsk_details[0]['easycase']['legend'];
			//set datatype
			$datatype=(isset($tsk_crt_data['datatype']) && !empty($tsk_crt_data['datatype']))? $tsk_crt_data['datatype'] :1;
			//set Milestone
			$CS_milestone=(isset($tsk_crt_data['CS_milestone']) && !empty($tsk_crt_data['CS_milestone'])) ? $tsk_crt_data['CS_milestone'] :'';
			//set is client
			$is_client=(isset($tsk_crt_data['is_client']) && !empty($tsk_crt_data['CS_milestone'])) ? $tsk_crt_data['is_client'] :$tsk_details[0]['easycase']['client_status'];
			//set case number
			if(isset($tsk_crt_data['CS_case_no']) && !empty($tsk_crt_data['CS_case_no'])){
				$CS_case_no =$tsk_crt_data['CS_case_no'] ;
			} 
			else{
				$CS_case_no =$tsk_details[0]['easycase']['case_no'];
			}
			//Formating an array for easycase.
			$case['CS_project_id'] = $projectUniqId;
			$case['CS_user_id']= $user['User']['id'];
			$case['company_id'] =$company['Company']['id'];
			$case['CS_type_id'] = $type_id;
			$case['CS_due_date'] = $CS_due_date;
			$case['estimated_hours']= $estimated_hours;
			$case['hours'] = $hours;
			$case['completed'] = $complete;
			$case['CS_priority'] = $priority;
			$case['CS_assign_to'] = $assign_to;
			$case['CS_legend'] = $legend;
			$case['CS_message'] = $msg;
			$case['user_uniq_id'] = $auth_token;
			$case['CS_case_no'] = $CS_case_no;
			$case['datatype'] = $datatype;
		//	$case['CS_id'] = $CS_id;
			$case['task_uid'] = $tsk_unqid;
			$case['taskid']	= $task_id;		
			$case['CS_title'] = $task_name;
			$case['CS_istype'] = 1;//Post case
			$case['prelegend'] = 0;
			$case['emailUser'] = $emailUser;
			$case['CS_milestone'] = $CS_milestone;
			$case['allUser'] = '';
			$case['allFiles'] = $allfiles;
			$case['postdata'] = 'post';
			$case['is_client'] = $is_client;
			$case['timelog']='false';
			$case['is_chrome_extension'] = 0;
//			pr($case);exit;
		}
                
                //Get company short name and set domain url for email content.

                $short_name = trim($company['Company']['seo_url']);
                $auth_domain = str_replace("app.",$short_name.".",HTTP_ROOT);
                $case['auth_domain'] = $auth_domain;

                App::import('Controller', 'Easycases');
                $Easycases = new EasycasesController();
                //Load model, components.
                $Easycases->constructClasses();

//			pr($case);exit;
                //Post a case.
                $case_result = $Easycases->ajaxpostcase($case);
			
                $res = json_decode($case_result,true);//pr($res);exit;
                if(isset($res['success']) && ($res['success']=='success')) {
                        //Formating an array for email to users.
                        $email['projId'] = $res['projId'];
                        $email['emailUser'] = $emailUser;
                        $email['allfiles'] = $res['allfiles'];
                        $email['caseNo'] = $res['caseNo'];
                        $email['emailTitle'] = $res['emailTitle'];
                        $email['emailMsg'] = '';
                        $email['casePriority'] = $res['casePriority'];
                        $email['caseTypeId'] = $res['caseTypeId'];
                        $email['msg'] = $res['msg'];
                        $email['emailbody'] = $res['emailbody'];
                        $email['caseIstype'] = $res['caseIstype'];
                        $email['csType'] = $res['csType'];
                        $email['caUid'] = $res['caUid'];
                        $email['caseid'] = $res['caseid'];
                        $email['caseUniqId'] = $res['caseUniqId'];
                        $email['auth_domain'] = $auth_domain;
                        $email['company_id'] =$company['Company']['id'];			
                        $email['fromId'] = $case['CS_user_id'];						
                        //Sent email to users.
                        $email_result = $Easycases->ajaxemail($email); 
                        $email_res = json_decode($email_result,true);
                        //Set status
                        if(!isset($tsk_crt_data['CS_id']) && empty($tsk_crt_data['CS_id']) && !isset($tsk_crt_data['taskid']) && empty($tsk_crt_data['taskid'])){
                        $data['code'] = 2000;
                        $data['status'] = "OK";
                        $data['msg'] = "Your task has been posted.";
                        }
                        else if(isset($tsk_crt_data['CS_id']) && !empty($tsk_crt_data['CS_id'])){
                        $data['code'] = 2000;
                        $data['status'] = "OK";
                        $data['msg'] = "Your reply is posted.";
                        }
                        else if(isset($tsk_crt_data['taskid']) && !empty($tsk_crt_data['taskid'])){	 
                        #print_r($case);							
                            if($case['CS_priority'] != '' || $case['CS_priority']==0){ 
                                $this->loadModel('Easycase');
                                $this->Easycase->id=$tsk_crt_data['taskid'];
                                $this->Easycase->saveField('priority', $case['CS_priority']);
                            }
                            if(isset($case['CS_legend']) && !empty($case['CS_legend'])){
                                $this->loadModel('Easycase');
                                $this->Easycase->id=$case['taskid'];
                                $this->Easycase->saveField('legend', $case['CS_legend']);
                            }


                        $data['code'] = 2000;
                        $data['status'] = "OK";
                        $data['msg'] = "Your task has been updated.";
                        }

                  } else{
                          $data['code'] = 2007;
                          $data['status'] = "failure";
                          $data['msg'] = "Error in task posting.";
                  } 
            }
            else {
		$data['code'] = 2006;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2005;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token is invalid!";
	}

	print json_encode($data);
	exit;
    }
	
	/**
     * @method getCompanyAndProjects
     * @param 
     * @return json string objects: Companys , Projects details
     * @author Chandan
     */
    
    function getCompanyAndProjects() {
	$this->layout = "ajax";
	$auth_token = '';  
	
	$rqst_data=(array) $this->request->input(json_decode,true);	
	if(isset($rqst_data) && !empty($rqst_data)){
	    $auth_token = $rqst_data['auth_token'];
	}else{		
		$rqst_data=$this->request->query;
		$auth_token = $rqst_data['auth_token'];		
	}

	if(trim($auth_token)){//Check given auth token is valid or not.
	    //Getting user id
	    $this->loadModel('User');
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id','timezone_id'));
	    if(!empty($user)){
		//Getting all companies of a user.
		$this->loadModel('CompanyUser');
		$sql = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name, Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $user['User']['id'] . " AND CompanyUser.is_active=1";
		$CompanyUser = $this->CompanyUser->query($sql);
		if (isset($CompanyUser) && !empty($CompanyUser)) {
		    $companyId = $companyUniqId = '';		
			$companyUniqId = $rqst_data['companyId'];		    
		    //If company id has not given, then finding company id from latest activity of a project or campany.
		    if(trim($companyUniqId)){
			$this->loadModel('Company');
			$company = $this->Company->getCompanyFields(array('Company.uniq_id' => $companyUniqId), array('id'));
			$companyId = $company['Company']['id'];
		    } else {
			//Getting latest company or project
			$this->loadModel('ProjectUser');
			$sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='".$user['User']['id']."' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
			$ProjectUser = $this->ProjectUser->query($sql);		
			if (isset($ProjectUser) && !empty($ProjectUser)) {
			    $companyId = $ProjectUser['0']['Company']['id'];
			    $companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
			}else {
			    $companyId = $CompanyUser['0']['CompanyUser']['company_id'];
			    $companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
			}
		    }

		    //Set status
		    $data['code'] = 2000;
		    $data['status'] = "OK";

		    //Set company details
		    foreach ($CompanyUser as $key => $value) {
			$companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $value['Companies']['seo_url'], "user_type" => $value['CompanyUser']['user_type']);
			if($value['Companies']['uniq_id'] == $companyUniqId){
			    $companyData = array_merge($companyData,array("selected"=>"1"));
			}
			$data['results']['companies']['company'][$key] = $companyData;
		    }

		    //Getting project details
			
		    $sql = "SELECT DISTINCT Project.id, Project.uniq_id, Project.name,Project.company_id,Project.user_id,CONCAT_WS(' ',User.name,User.last_name) AS user_name, Project.default_assign,Project.dt_created,Project.dt_updated FROM project_users AS ProjectUser LEFT JOIN projects AS Project ON (Project.id= ProjectUser.project_id) LEFT JOIN users AS User ON (Project.user_id = User.id) WHERE ProjectUser.company_id='" . $companyId . "' AND Project.isactive='1' and ProjectUser.user_id='".$user['User']['id']."' ORDER BY Project.`dt_created` DESC";
		//	echo $sql;exit;
		    $this->loadModel('Project');
		    $Project = $this->Project->query($sql);
		//  pr($Project);exit;  
		$view = new View($this);
		$frmt = $view->loadHelper('Format');
		$tz = $view->loadHelper('Tmzone');
		$dt = $view->loadHelper('Datetime');
		$this->loadModel('Timezone');
                $this->loadModel("Easycase");
                
                         
	    $timezone = $this->Timezone->find('first', array("conditions" => array("Timezone.id" => $user['User']['timezone_id'])));
		    if (isset($Project) && !empty($Project)) {
			//Set project details
	
			foreach ($Project as $key => $value) {
				$locDT = $tz->GetDateTime($timezone['Timezone']['id'], $timezone['Timezone']['gmt_offset'], $timezone['Timezone']['dst_offset'], $timezone['Timezone']['code'], $value['Project']['dt_created'], "datetime");
					$gmdate = $tz->GetDateTime($timezone['Timezone']['id'], $timezone['Timezone']['gmt_offset'], $timezone['Timezone']['dst_offset'], $timezone['Timezone']['code'], GMT_DATETIME, "date");
					$dateTime = $dt->dateFormatOutputdateTime_day($locDT, $gmdate, 'time');
                                       $clt_sql = 1;
                                      
                                $companyUser= $this->CompanyUser->find("first",array("conditions"=>array('CompanyUser.company_id' => $value['Project']['company_id'],'CompanyUser.user_id'=>$user['User']['id']), "fields"=>array('is_client')));
                                
                                if ($companyUser['CompanyUser']['is_client'] == 1) {
                                    $clt_sql = "((Easycase.client_status = " . $companyUser['CompanyUser']['is_client'] . " AND Easycase.user_id = " . $user['User']['id'] . ") OR Easycase.client_status != " . $companyUser['CompanyUser']['is_client'] . ")";
                                }
                                $sql = "SELECT COUNT(Easycase.id) AS count from easycases AS Easycase WHERE Easycase.project_id='".$value['Project']['id']."' AND Easycase.project_id!=0 AND Easycase.istype=1 AND Easycase.isactive=1 AND ".$clt_sql;
                             
                                $taskscounts = $this->Easycase->query($sql);
                              
			    $data['results']['projects'][] = array("id" => $value['Project']['uniq_id'], "name" => $value['Project']['name'],"user_id"=>$value['Project']['user_id'],"user_name"=>$value[0]['user_name'],"created_date"=>$dateTime,"task_counts"=>$taskscounts[0][0]['count']);
}
			
			$res = $this->getCaseData($Project['0']['Project']['default_assign']);
			$data['results']['default_assign'] = $res['default_assign'];
			$data['results']['email_notification'] = $this->getMemebers($companyId,$Project['0']['Project']['uniq_id']);
		    }
		} else {
		    $data['code'] = 2000;
		    $data['status'] = "OK";
		    $data['msg'] = "No result founds!";
		}
	    } else {
		$data['code'] = 2006;
		$data['status'] = "failure";
		$data['msg'] = "Auth token is invalid!";
	    }
	}else{
	    $data['code'] = 2006;
	    $data['status'] = "failure";
	    $data['msg'] = "Auth token is invalid!";
	}
	print json_encode($data);
	exit;
    }
    /**
     * @method signupUser
     * @param 
     * @return json string objects: success message.
     * @author chandan
     */
    function signupUser(){
        $this->layout="ajax";
        $rqst_data=(array) $this->request->input(json_decode,true);
		if(empty($rqst_data)){
			$rqst_data= $this->request->query;
		}
        if(isset($rqst_data['email']) && !empty($rqst_data['email']))
        {
            $email=$rqst_data['email'] ;
        }
        if (trim($email)) {
	    $this->loadModel('User');
	    $user = $this->User->find('first', array('conditions' => array('User.email' => trim($email),'User.isactive' => 1)));
	    if (isset($user) && !empty($user)) {
		//Set status
		$data['code'] = 2000;
		$data['status'] = "OK";
		//Set user details.
		$data['results']['auth_token'] = $user['User']['uniq_id'];
		$data['results']['user']['info']['email'] = $user['User']['email'];
		$data['results']['user']['info']['first_name'] = $user['User']['name'];
		$data['results']['user']['info']['last_name'] = $user['User']['last_name'];
		$data['results']['user']['info']['short_name'] = $user['User']['short_name'];
		$data['results']['user']['info']['time_zone'] = $user['User']['timezone_id'];
		if(isset($user['User']['photo']) && !empty($user['User']['photo'])) {
		    $photo = DIR_USER_PHOTOS_S3.$user['User']['photo'];
		    $data['results']['user']['info']['img_url'] = $this->Image->generateTemporaryURL($photo);//$domain."users/image_thumb/?type=photos&file=".$user['User']['photo']."&sizex=45&sizey=50&quality=100";
		} else { 
		    $data['results']['user']['info']['img_url'] = "";
		}
		
		$this->loadModel('CompanyUser');
		$sql = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name,Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $user['User']['id'] . " AND CompanyUser.is_active=1";
		$CompanyUser = $this->CompanyUser->query($sql);

		if (isset($CompanyUser) && !empty($CompanyUser)) {
		    //Getting latest company or project
		    $this->loadModel('ProjectUser');
		    $sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='".$user['User']['id']."' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
		    $ProjectUser = $this->ProjectUser->query($sql);

		    $companyId = $companyUniqId = $projectId = '';
		    if (isset($ProjectUser) && !empty($ProjectUser)) {
			$companyId = $ProjectUser['0']['Company']['id'];
			$companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
			$projectId = $ProjectUser['0']['Project']['uniq_id'];
		    }else {
			$companyId = $CompanyUser['0']['CompanyUser']['company_id'];
			$companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
		    }

		    //Set company details
		    foreach ($CompanyUser as $key => $value) {
			$short_name = trim($value['Companies']['seo_url']);
			$companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $short_name, "user_type" => $value['CompanyUser']['user_type']);
			if($value['Companies']['uniq_id'] == $companyUniqId){
			    $companyData = array_merge($companyData,array("selected"=>"1"));
			}
			$data['results']['companies']['company'][$key] = $companyData;
		    }			
		}
	    }
            else{
                $this->loadModel('Industry');                
                $industries = $this->Industry->find('all', array('fields' => array('Industry.id', 'Industry.name')));
                $data['code'] = 2001;
                $data['status'] = "No user";
                foreach($industries as $key => $val){
                     $data['results']['Industry'][] = $val['Industry'];
                }
               
            }
        }
        print json_encode($data);exit;       
    }
    /**
     * @method registerUser
     * @param 
     * @return json string objects: success message.
     * @author chandan
     */
    function registerUser(){
        $this->layout="ajax";
        $this->loadModel('Company');
        $this->loadModel('User');
        App::import('Controller', 'Users');
        $Users = new UsersController();
                        //Load model, components.
        $Users->constructClasses();
        $requst_data =(array) $this->request->input(json_decode,true);
		if(empty($requst_data)){
			$requst_data= $this->request->query;
		}
        if(isset($requst_data['email'])){
            $email = $requst_data['email'] ;
        }
        if(isset($requst_data['name'])){
            $name = $requst_data['name'];
            $last = strripos($name, " ");
            $name = substr($name, 0, $last);
            $last_name = substr($name, $last + 1, strlen($name));
        }
        if(isset($requst_data['company'])){
            $company = urldecode($requst_data['company']) ;
            $seo_url=$this->Format->makeSeoUrl($company);
        }
        $checkCmpny = $this->Company->find('first',array('conditions'=>array('Company.seo_url'=>$seo_url),'fields'=>array('Company.id')));
        if(isset($checkCmpny) && !empty($checkCmpny)){
            $data['code'] = 2001;
            $data['status'] = "Failure";
            $data['msg'] ="Compnay already exits";            
        }
        else{
            $short_name = $this->Format->makeShortName($name, $last_name);
            $this->loadModel('Timezone');
            $getTmz = $this->Timezone->find('first', array('conditions' => array('Timezone.gmt_offset' => urldecode($requst_data['timezone']))));
            $timezone_id = $getTmz['Timezone']['id'];
            $plan_id = (isset($requst_data['plan_id']) && $requst_data['plan_id']) ? $requst_data['plan_id'] : CURRENT_FREE_PLAN;
            $this->loadModel('Subscription');
            $subScription = $this->Subscription->find('first', array('conditions' => array('Subscription.plan' => $plan_id)));
            $bt_profile_id = '';
            $credit_cardtoken = '';
            $cnumber = '';
            $expiry_date = '';
            $sub_type = 0;
            $referrer = (isset($requst_data['referrer']) && !empty($requst_data['referrer'])) ? $requst_data['referrer'] : '';
            $access_token = array('access_token'=> $requst_data['access_token']);
            $gaccess_token = (isset($requst_data['access_token']) && !empty($requst_data['access_token'])) ? json_encode($access_token) : '';
            if($email && $company){
                $comp['Company']['uniq_id'] = $this->Format->generateUniqNumber();
                $comp['Company']['seo_url'] = $seo_url;
                $comp['Company']['subscription_id'] = $subScription['Subscription']['id'];
                $comp['Company']['name'] = $company;
                if(isset($requst_data['industry_id'])){
                    $comp['Company']['industry_id'] = $requst_data['industry_id'];
                }               
                $sus_comp = $this->Company->save($comp);
                if ($sus_comp) {
                    $company_id = $this->Company->getLastInsertID();
                    $activation_id = $this->Format->generateUniqNumber();
                    $usr['User']['uniq_id'] = $this->Format->generateUniqNumber();
                    $usr['User']['email'] = $email;
                    $usr['User']['name'] = $name;
                    $usr['User']['last_name'] = $last_name;
                    $usr['User']['short_name'] = $short_name;
                    $usr['User']['istype'] = 2;
                    $usr['User']['isactive'] = 1;
                    $usr['User']['dt_created'] = GMT_DATETIME;
                    $usr['User']['dt_updated'] = GMT_DATETIME;
                    $usr['User']['query_string'] = $activation_id;
                    $vstr = md5(uniqid(rand()));
                    $usr['User']['verify_string']="";
                    $usr['User']['timezone_id'] = $timezone_id ? $timezone_id : 26;
                    $usr['User']['btprofile_id'] = $bt_profile_id;
                    $usr['User']['credit_cardtoken'] = $credit_cardtoken;
                    $usr['User']['card_number'] = $cnumber;
                    $usr['User']['expiry_date'] = $expiry_date;
                    $usr['User']['usersub_type'] = $sub_type;
                    $usr['User']['is_agree'] =1;
                    $ip = $this->Format->getRealIpAddr();
                    $usr['User']['ip'] = (isset($ip) && !empty($ip)) ? $ip : "";
                    $usr['User']['gaccess_token'] = $gaccess_token;
                    $sus_user = $this->User->save($usr);
                    if($sus_user){                       
                        $comp_usr['CompanyUser']['user_id'] = $this->User->getLastInsertID();
                        $comp_usr['CompanyUser']['company_id'] = $company_id;
                        $comp_usr['CompanyUser']['company_uniq_id'] = $comp['Company']['uniq_id'];
                        $comp_usr['CompanyUser']['user_type'] = 1;
                        $this->loadModel('CompanyUser');                    
                        $sus_companyuser = $this->CompanyUser->save($comp_usr);
                        if ($sus_companyuser) {
                            $price = $subScription['Subscription']['price'];
                            $companyUid = $this->CompanyUser->getLastInsertID();
                            $this->loadModel('UserSubscription');
                            $sub_usr['UserSubscription']['user_id'] = $comp_usr['CompanyUser']['user_id'];
                            $sub_usr['UserSubscription']['company_id'] = $company_id;
                            $sub_usr['UserSubscription']['subscription_id'] = $subScription['Subscription']['id'];
                            $sub_usr['UserSubscription']['storage'] = $subScription['Subscription']['storage'];
                            $sub_usr['UserSubscription']['project_limit'] = $subScription['Subscription']['project_limit'];
                            $sub_usr['UserSubscription']['user_limit'] = $subScription['Subscription']['user_limit'];
                            $sub_usr['UserSubscription']['milestone_limit'] = $subScription['Subscription']['milestone_limit'];
                            if (CURRENT_FREE_PLAN == $plan_id) {
                                $sub_usr['UserSubscription']['free_trail_days'] = FREE_TRIAL_PERIOD;
                            } else {
                                $sub_usr['UserSubscription']['free_trail_days'] = $subScription['Subscription']['free_trail_days'];
                            }
                            $sub_usr['UserSubscription']['price'] = $price;
                            $sub_usr['UserSubscription']['month'] = $subScription['Subscription']['month'];
                            $sub_usr['UserSubscription']['created'] = GMT_DATETIME;
                            //Insert a new record for user notification.
                            $notification['user_id'] = $comp_usr['CompanyUser']['user_id'];
                            $notification['type'] = 1;
                            $notification['value'] = 1;
                            $notification['due_val'] = 1;
                            ClassRegistry::init('UserNotification')->save($notification);
                            $json_arr['company_name'] = $comp['Company']['name'];
                            $json_arr['name'] = $usr['User']['name'];
                            $json_arr['user_type'] ='Free';
                            $json_arr['created'] = GMT_DATETIME;
                            $this->Postcase->eventLog($company_id, $comp_usr['CompanyUser']['user_id'], $json_arr, 1);
                            // sending email to the admin
                            $from = FROM_EMAIL;                       
                            $to = ADMIN_EMAIL;
                            $domain = "";                       
                            $utype = $GLOBALS['plan_types'][$plan_id];
                            $location = $this->Format->iptoloccation($ip);
                            $admin_subject = "A " . $utype . " User Registered";
                            $admin_message = "<p style='font-family:Arial;font-size:14px;'>
								    <p style='font-family:Arial;font-size:14px;'>Dear site administrator,<p>
								    <p style='font-family:Arial;font-size:14px;'>You're lucky today, a new user has registered with Orangescrum.</p>
								    <p>&nbsp;</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Company:</b> " . $company . "</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Email:</b> " . $email . "</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Name:</b> " . $name . "</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Location:</b> " . $location . "</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Industry:</b> " . $industry . "</p>
								    <p style='font-family:Arial;font-size:14px;'><b>Referrer:</b> " . $referrer . "</p>
								    </p>" . $domain;                       
                        $sendEmailAdmin = $this->Sendgrid->sub_sendgrid(FROM_EMAIL, ADMIN_EMAIL, $admin_subject, $admin_message, "", MARKETING_EMAIL, '');                                               
                        }
                        $arr_project = array();
                        $arr_project['name'] = 'Getting Started Orangescrum';
                        $arr_project['short_name'] = 'GSO';
                        $arr_project['validate'] = 1;
                        $arr_project['members'] = array($comp_usr['CompanyUser']['user_id']);
                        $prjid = $this->User->add_inline_project($arr_project, $comp_usr['CompanyUser']['user_id'], $company_id, $name, $this);

                        $arr_taskgroup = array();
                        $arr_taskgroup = Configure::read('DEFAULT_TASKGROUP_INPUT');
                        $this->User->new_inline_milestone($arr_taskgroup, $comp_usr['CompanyUser']['user_id'], $company_id, $prjid);
                        $user_id = $this->User->getLastInsertId();
                        $user = $this->User->find('first', array('conditions' => array('User.id' => $user_id,'User.isactive' => 1)));
                        if (isset($user) && !empty($user)) {
                            //Set status
                            $data['code'] = 2000;
                            $data['status'] = "OK";
                            $data['msg'] = "Sign up sucessful";
                            //Set user details.
                            $data['results']['auth_token'] = $user['User']['uniq_id'];
                            $data['results']['user']['info']['email'] = $user['User']['email'];
                            $data['results']['user']['info']['first_name'] = $user['User']['name'];
                            $data['results']['user']['info']['last_name'] = $user['User']['last_name'];
                            $data['results']['user']['info']['short_name'] = $user['User']['short_name'];
                            $data['results']['user']['info']['time_zone'] = $user['User']['timezone_id'];
                            if(isset($user['User']['photo']) && !empty($user['User']['photo'])) {
                                $photo = DIR_USER_PHOTOS_S3.$user['User']['photo'];
                                $data['results']['user']['info']['img_url'] = $this->Image->generateTemporaryURL($photo);//$domain."users/image_thumb/?type=photos&file=".$user['User']['photo']."&sizex=45&sizey=50&quality=100";
                            } else { 
                                $data['results']['user']['info']['img_url'] = "";
                            }                   
                            $sqls = "SELECT CompanyUser.company_id,CompanyUser.user_type,Companies.uniq_id,Companies.name,Companies.seo_url FROM company_users CompanyUser , companies Companies WHERE Companies.id = CompanyUser.company_id AND CompanyUser.user_id=" . $user['User']['id'] . " AND CompanyUser.is_active=1";
                            $CompanyUser = $this->CompanyUser->query($sqls);
                            if (isset($CompanyUser) && !empty($CompanyUser)) {
                                //Getting latest company or project
                                $this->loadModel('ProjectUser');
                                $sql = "SELECT Company.id, Company.uniq_id, Project.uniq_id FROM project_users AS ProjectUser LEFT JOIN (companies AS Company , projects  AS Project) ON (ProjectUser.company_id=Company.id AND ProjectUser.project_id=Project.id) WHERE ProjectUser.user_id='".$user['User']['id']."' ORDER BY ProjectUser.dt_visited DESC LIMIT 0, 1";
                                $ProjectUser = $this->ProjectUser->query($sql);

                                $companyId = $companyUniqId = $projectId = '';
                                if (isset($ProjectUser) && !empty($ProjectUser)) {
                                    $companyId = $ProjectUser['0']['Company']['id'];
                                    $companyUniqId = $ProjectUser['0']['Company']['uniq_id'];
                                    $projectId = $ProjectUser['0']['Project']['uniq_id'];
                                }else {
                                    $companyId = $CompanyUser['0']['CompanyUser']['company_id'];
                                    $companyUniqId = $CompanyUser['0']['Companies']['uniq_id'];
                                }

                                //Set company details
                                foreach ($CompanyUser as $key => $value) {
                                    $short_name = trim($value['Companies']['seo_url']);
                                    $companyData = array("id" => $value['Companies']['uniq_id'], "name" => $value['Companies']['name'], "short_name" => $short_name, "user_type" => $value['CompanyUser']['user_type']);
                                    if($value['Companies']['uniq_id'] == $companyUniqId){
                                        $companyData = array_merge($companyData,array("selected"=>"1"));
                                    }
                                    $data['results']['companies']['company'][$key] = $companyData;
                                }			
                            }
                        }
                    }
                }
                
            }
            else{
                $data['code'] = 2001;
                $data['status'] = "Failed";
                $data['msg'] = "Sign up failed";
            }
        }
        echo json_encode($data);exit;
    }
    /**
     * @method create_project
     * @param 
     * @return json string objects: success message.
     * @author chandan
     */
    function create_project(){
        $this->layout="ajax";
        $this->loadModel('Project');
        $this->loadModel('ProjectUser');
        $this->loadModel('User');
        $this->loadModel('Company');
        
        $requst_data =(array) $this->request->input(json_decode,true);
		if(empty($requst_data)){
			$requst_data= $this->request->query;
		}
        if((isset($requst_data['auth_token']) && !empty($requst_data['auth_token'])) && (isset($requst_data['company_id']) && !empty($requst_data['company_id']))){
           $company = $this->Company->getCompanyFields(array('Company.uniq_id' => $requst_data['company_id']), array('id','name'));
           $company_id = $company['Company']['id'];
           $company_name = $company['Company']['name'] ;
	   $user = $this->User->getUserFields(array('User.uniq_id' => $requst_data['auth_token']), array('id','timezone_id'));
           $user_id = $user['User']['id'];
           if (!empty($requst_data['start_date'])) {
            $postProject['Project']['start_date'] = date("Y-m-d", strtotime($requst_data['start_date']));
            }
            if (!empty($requst_data['end_date'])) {
            $postProject['Project']['end_date'] = date("Y-m-d", strtotime($requst_data['end_date']));
            }           
            $findName = $this->Project->find('first', array('conditions' => array('Project.name' => $requst_data['project_name'], 'Project.company_id' => $company_id), 'fields' => array('Project.id')));
            if ($findName) {
                $data['code'] = 2001;
                $data['status'] = "Failed";
                $data['msg'] = "Name already exits";
                echo json_encode($data);exit;                
            }
            $findShrtName = $this->Project->find('first', array('conditions' => array('Project.short_name' => $requst_data['short_name'], 'Project.company_id' => $company_id), 'fields' => array('Project.id')));
            if ($findShrtName) {
                $data['code'] = 2001;
                $data['status'] = "Failed";
                $data['msg'] = "Short name already exits";
                echo json_encode($data);exit; 
            }
            if(isset($requst_data['estimated_hr']) && !empty($requst_data['estimated_hr'])){
                $estimated_hr = $requst_data['estimated_hr'] ;
            }
            else{
                $estimated_hr = "" ;
            }          
            $postProject['Project']['short_name'] = trim($requst_data['short_name']);			
            $prjUniqId = md5(uniqid());
            $postProject['Project']['uniq_id'] = $prjUniqId;
            $postProject['Project']['user_id'] = $user_id;
            $postProject['Project']['project_type'] = 1;            
            $postProject['Project']['default_assign'] = $user_id;           
            $postProject['Project']['isactive'] = 1;
            $postProject['Project']['name'] = htmlspecialchars(trim($requst_data['project_name']), ENT_QUOTES);
            $postProject['Project']['description'] = trim($requst_data['description']);
            $postProject['Project']['status'] = 1;
            $postProject['Project']['dt_created'] = GMT_DATETIME;
            $postProject['Project']['company_id'] = $company_id;
            $postProject['Project']['estimated_hours'] = $estimated_hr ;
            if($this->Project->save($postProject)){
                $prjid = $this->Project->getLastInsertID();
                $this->ProjectUser->recursive = -1;
                $getLastId = $this->ProjectUser->query("SELECT MAX(id) as maxid FROM project_users");
                $lastid = $getLastId[0][0]['maxid'] + 1;                                 
                        $ProjUsr['ProjectUser']['id'] = $lastid;
                        $ProjUsr['ProjectUser']['project_id'] = $prjid;
                        $ProjUsr['ProjectUser']['user_id'] = $user_id;
                        $ProjUsr['ProjectUser']['company_id'] = $company_id;
                        $ProjUsr['ProjectUser']['default_email'] = 1;
                        $ProjUsr['ProjectUser']['istype'] = 1;
                        $ProjUsr['ProjectUser']['dt_visited'] = GMT_DATETIME;
                        $this->ProjectUser->save($ProjUsr);
                        $lastid = $lastid + 1;  
                        App::import('Controller', 'Projects');
                        $Projects = new ProjectsController();
                        //Load model, components.
                        $Projects->constructClasses();
                        $Projects->generateMsgAndSendPjMail($prjid, $user_id, $company_name ,$user_id);
                $data['code'] = 2000;
                $data['status'] = "OK";
                $data['msg'] = "Project created successfuly";                     
            }
            else{
            $data['code'] = 2001;
            $data['status'] = "Failed";
            $data['msg'] = "Project creation failed";
            }
        }      
       else{
           $data['code'] = 2001;
           $data['status'] = "Failed";
           $data['msg'] = "Invalid auth token";
       }
       echo json_encode($data);exit;
    }
    /**
     * @method edit_project
     * @param 
     * @return json string objects: success message.
     * @author chandan
     */
    function edit_project(){
        $this->layout = "ajax";
        $this->loadModel('Project');
        $this->loadModel('User');
        $this->loadModel('Company');
        $requst_data = (array) $this->request->input(json_decode,true);
		if(empty($requst_data)){
			$requst_data= $this->request->query;
		}
        if((isset($requst_data['auth_token']) && !empty($requst_data['auth_token'])) && (isset($requst_data['company_id']) && !empty($requst_data['company_id'])) && (isset($requst_data['project_id']) && !empty($requst_data['project_id']))){
               $project = $this->Project->getProjectFields(array('Project.uniq_id' => $requst_data['project_id']), array('id'));
               $prjct_id= $project['Project']['id'];
               $user = $this->User->getUserFields(array('User.uniq_id' => $requst_data['auth_token']), array('id','timezone_id'));
               $user_id = $user['User']['id'];
               $company = $this->Company->getCompanyFields(array('Company.uniq_id' => $requst_data['company_id']), array('id','name'));
               $company_id = $company['Company']['id'];
               if (!empty($requst_data['start_date'])) {
                    $postProject['Project']['start_date'] = date("Y-m-d", strtotime($requst_data['start_date']));
                 }
               if (!empty($requst_data['end_date'])) {
                $postProject['Project']['end_date'] = date("Y-m-d", strtotime($requst_data['end_date']));
                } 
                $findName = $this->Project->find('first', array('conditions' => array('Project.name' => $requst_data['project_name'], 'Project.company_id' => $company_id, 'Project.id !=' => $prjct_id), 'fields' => array('Project.id')));
             
                if ($findName) {
                    $data['code'] = 2001;
                    $data['status'] = "Failed";
                    $data['msg'] = "Name already exits";
                    echo json_encode($data);exit;                
                }
                $findShrtName = $this->Project->find('first', array('conditions' => array('Project.short_name' => $requst_data['short_name'], 'Project.company_id' => $company_id, 'Project.id !=' => $prjct_id), 'fields' => array('Project.id')));
                if ($findShrtName) {
                    $data['code'] = 2001;
                    $data['status'] = "Failed";
                    $data['msg'] = "Short name already exits";
                    echo json_encode($data);exit; 
                }
                if(isset($requst_data['estimated_hr']) && !empty($requst_data['estimated_hr'])){
                $estimated_hr = $requst_data['estimated_hr'] ;
                }
                         
                $postProject['Project']['short_name'] = trim($requst_data['short_name']);			               
                $postProject['Project']['uniq_id'] = $requst_data['project_id'];
                $postProject['Project']['user_id'] = $user_id;
                $postProject['Project']['project_type'] = 1;            
                $postProject['Project']['default_assign'] = $user_id;           
                $postProject['Project']['isactive'] = 1;
                $postProject['Project']['name'] = htmlspecialchars(trim($requst_data['project_name']), ENT_QUOTES);
                $postProject['Project']['description'] = trim($requst_data['description']);
                $postProject['Project']['status'] = 1;
                $postProject['Project']['dt_updated'] = GMT_DATETIME;
                $postProject['Project']['company_id'] = $company_id;
                $postProject['Project']['estimated_hours'] = $estimated_hr ;
                $postProject['Project']['id'] = $prjct_id ;
                if($this->Project->save($postProject)){
                    $data['code'] = 2000;
                    $data['status'] = "OK";
                    $data['msg'] = "Project updated successfuly";                                        
                }
                else{
                    $data['code'] = 2001;
                    $data['status'] = "Failure";
                    $data['msg'] = "Project updation failed"; 
                }               
        }       
        else{
           $data['code'] = 2001;
           $data['status'] = "Failed";
           $data['msg'] = "Invalid auth token";
        }
        echo json_encode($data);exit;
    }
    function delete_task(){
        $this->layout = "ajax";
        $requst_data = (array) $this->request->input(json_decode,true);
        if(empty($requst_data)){
                $requst_data= $this->request->query;
        }
        if((isset($requst_data['auth_token']) && !empty($requst_data['auth_token'])) && (isset($requst_data['taskid']) && !empty($requst_data['taskid']))  && (isset($requst_data['project_id']) && !empty($requst_data['project_id']))){
            $this->loadModel('User');
            $this->loadModel('Project');
            $this->loadModel('Easycase');
            $auth_token=$requst_data['auth_token'];
            $task_id=$requst_data['taskid'];
            $projectUniqId=$requst_data['project_id'];
            
	    $user = $this->User->getUserFields(array('User.uniq_id' => $auth_token), array('id'));
            //Getting project details.
            $project = $this->Project->getProjectFields(array('Project.uniq_id'=>$projectUniqId),array('id'));
            $projectId=$project['Project']['id'];
            if(!empty($user)){ 
		$tsk_details=$this->Easycase->query("SELECT easycase.* FROM easycases as easycase WHERE easycase.id='".$task_id."'");
                if(isset($tsk_details[0]['easycase']['id']) && !empty($tsk_details[0]['easycase']['id'])){
				
                    App::import('Controller', 'Easycases');
                    $Easycases = new EasycasesController();
                    //Load model, components.
                    $Easycases->constructClasses();
                    $case['id']=$tsk_details[0]['easycase']['id'];
                    $case['cno']=$tsk_details[0]['easycase']['case_no'];
                    $case['pid']=$projectId; 	
				    $reslt=$Easycases->delete_case($case);
                    if($reslt=='success'){
                        $data['code'] = 2000;
                        $data['status'] = "OK";
                        $data['msg'] = "Task deleted successfuly";        
                    }else{
                        $data['code'] = 2001;
                        $data['status'] = "Failure";
                        $data['msg'] = "Task cannot delete. Try again later."; 
                    }
                }else{
                    $data['code'] = 2003;
                    $data['status'] = "Failure";
                    $data['msg'] = "Task not found.";
                }
            }else{
                $data['code'] = 2002;
                $data['status'] = "Failed";
                $data['msg'] = "Invalid auth token";
            }
        }else{
            $data['code'] = 2002;
            $data['status'] = "Failed";
            $data['msg'] = "Invalid auth token";
      }
	 echo json_encode($data);exit;
    }
    
    function getTaskPermission($companyId){
//        $this->loadModel('TaskSetting');
//        $task_settings = $this->TaskSetting->getTaskSettings($companyId);
//        if (empty($task_settings)) {
//            $task_settings['TaskSetting']['edit_task'] = 1;
//        }
        /* 
         * In community version There is no task_settings table  
         * set the $task_settings['TaskSetting']['edit_task'] by default 1 (for all user allow edit);
         * 
         */
        return 1;
    }
    function getUserType($user_id,$company_id){
         $this->loadModel('Company');
         $getAppComp = $this->Company->query("SELECT CompanyUser.user_type,CompanyUser.company_id,Company.logo,Company.website,Company.name,Company.is_active,Company.is_deactivated,Company.created,Company.uniq_id,Company.twitted  FROM company_users AS CompanyUser,companies AS Company WHERE CompanyUser.company_id=Company.id AND CompanyUser.user_id='" . $user_id . "' AND Company.uniq_id='" . $company_id . "'"); //,Company.is_skipped not exists in community version
         return $getAppComp['0']['CompanyUser']['user_type'];
    }
    function googleLogin($email,$token){       
        
    }
    
}