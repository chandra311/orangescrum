<?php

use ElephantIO\Engine\SocketIO\Version1X as Version1X;
use ElephantIO\Client as Client;

App::import('Component', 'Cookie');
App::import('Vendor', 's3', array('file' => 's3' . DS . 'S3.php'));

//use ElephantIO\Client as ElephantIOClient;

class PostcaseComponent extends CookieComponent {

    public $components = array('Session', 'Email', 'Cookie', 'Format', 'Sendgrid', 'Tmzone');

    function casePosting($formdata) {
        $pagename = $formdata['pagename'];
        $postParam['Easycase']['isactive'] = 1;
        $postParam['Easycase']['project_id'] = $formdata['CS_project_id'];
        $postParam['Easycase']['istype'] = $formdata['CS_istype'];
        $postParam['Easycase']['title'] = $formdata['CS_title'];
        $postParam['Easycase']['type_id'] = $formdata['CS_type_id'];
        $postParam['Easycase']['priority'] = $formdata['CS_priority'];
        $postParam['Easycase']['assign_to'] = $formdata['CS_assign_to'];
        $postParam['Easycase']['legend'] = $formdata['CS_legend'];
        $postParam['Easycase']['hours'] = $formdata['hours'];
        $postParam['Easycase']['gantt_start_date'] = $formdata['gantt_start_date'];
        if (isset($formdata['CS_isRecurring'])) {
            $postParam['Easycase']['is_recurring'] = $formdata['CS_isRecurring'];
        }
        if (defined('TLG') && TLG == 1) {
            $estimated_hours = $formdata['estimated_hours'];
            /* saving in secs */
            if (strpos($estimated_hours, ':') > -1) {
                $split_est = explode(':', $estimated_hours);
                $est_sec = ((($split_est[0]) * 60) + intval($split_est[1])) * 60;
            } else {
                $est_sec = $estimated_hours * 3600;
            }
            if (!isset($formdata['completed']) && defined('TSG') && TSG == 1) {
                $status = ClassRegistry::init('Status');
                $crnt_status = $status->find('first', array('conditions' => array('id' => $formdata['CS_legend'])));
                $formdata['completed'] = $crnt_status['Status']['percentage'];
            }
            $estimated_hours = $est_sec;
            $postParam['Easycase']['estimated_hours'] = $estimated_hours;
        } else {
            $postParam['Easycase']['estimated_hours'] = $formdata['estimated_hours'];
        }
        if (isset($formdata['depend'])) {
            if ($formdata['depend'] == "Yes" && isset($formdata['completed']) && !empty($formdata['completed'])) {
                $postParam['Easycase']['completed_task'] = $formdata['completed'];
            }
        } else {
            if (isset($formdata['completed']) && !empty($formdata['completed'])) {
                $postParam['Easycase']['completed_task'] = $formdata['completed'];
            }
        }
        if (defined('CR') && CR == 1) {
            $postParam['Easycase']['client_status'] = isset($formdata['is_client']) ? $formdata['is_client'] : 0;
        }
        $postParam['Easycase']['is_chrome_extension'] = (isset($formdata['is_chrome_extension'])) ? $formdata['is_chrome_extension'] : '0';
        $prelegend = $formdata['prelegend'];
        $user_id = isset($formdata['api_user_id']) && !empty($formdata['api_user_id']) ? intval($formdata['api_user_id']) : SES_ID;
        if (($user_id == 'SES_ID' || $user_id == '') && isset($formdata['CS_user_id']) && !empty($formdata['CS_user_id'])) {
            $user_id = $formdata['CS_user_id'];
        }
        $global_usr_id = $user_id;
        $company_id = isset($formdata['api_user_id']) && !empty($formdata['api_user_id']) ? intval($formdata['api_company_id']) : SES_COMP;
        if ($company_id == 'SES_COMP' && isset($formdata['company_id']) && !empty($formdata['company_id'])) {
            $company_id = $formdata['company_id'];
        }

        if (isset($formdata['datatype']) && $formdata['datatype'] == 1) {
            $postParam['Easycase']['message'] = $formdata['CS_message'];
        } else {
            $postParam['Easycase']['message'] = $formdata['CS_message'];
        }
        $postParam['Easycase']['due_date'] = $formdata['CS_due_date'];
        $postParam['Easycase']['postdata'] = $formdata['postdata'];

        if ($postParam['Easycase']['due_date'] == "No Due Date") {
            $postParam['Easycase']['due_date'] = NULL;
        }
        if (isset($formdata['CS_milestone']) && $formdata['CS_milestone']) {
            $milestone_id = $formdata['CS_milestone'];
        }
        if (isset($formdata['CS_id']) && $formdata['CS_id']) {
            $caseid = $formdata['CS_id'];
            $caseid = trim($caseid, "'");
        }
        if (isset($formdata['CS_case_no']) && $formdata['CS_case_no']) {
            $postParam['Easycase']['case_no'] = $formdata['CS_case_no'];
        }
        $emailUser = $formdata['emailUser'];
        $allUser = $formdata['allUser'];
        $fileArray = $formdata['allFiles'];
        $domain = isset($formdata['auth_domain']) ? $formdata['auth_domain'] : HTTP_ROOT;

        $cloud_storages = $formdata['cloud_storages']; //By Orangescrum

        $success = "fail";
        $emailTitle = "";
        $Easycase = ClassRegistry::init('Easycase');
        $Easycase->recursive = -1;
        $update = 0;
######## Check File Exists and Size
        $chk = 0;
        if (is_array($fileArray) && count($fileArray)) {
            $usedspace = $GLOBALS['usedspace'];
            foreach ($fileArray as $filename) {
                if ($filename && strstr($filename, "|")) {
                    $fl = explode("|", $filename);
                    if (isset($fl['0'])) {
                        $file = $fl['0'];
                        $filesize = number_format(($fl[1] / 1024), 2, '.', '');
                        if (strtolower($GLOBALS['Userlimitation']['storage']) == 'unlimited' || ($usedspace <= $GLOBALS['Userlimitation']['storage'])) {
                            $usedspace +=$filesize;
                            if (USE_S3 == 0) {
                                if (file_exists(DIR_CASE_FILES . $file)) {
                                    $chk++;
                                }
                            } else {
                                $s3 = new S3(awsAccessKey, awsSecretKey);
                                $info = $s3->getObjectInfo(BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER_TEMP . $file);
                                if ($info) {
                                    $chk++;
                                }
                            }
                        }
                    }
                }
            }
        }
###### Get Ptoject Id
        if ($formdata['CS_project_id'] != "all") {
            $Project = ClassRegistry::init('Project');
            $statuscls = ClassRegistry::init('Status');
            $Project->recursive = -1;
            $prjArr = $Project->find('first', array('conditions' => array('Project.uniq_id' => $formdata['CS_project_id']), 'fields' => array('Project.id', 'Project.name', 'Project.workflow_id')));
            $projId = $prjArr['Project']['id'];
//$projName = urlencode($prjArr['Project']['name']);
            $projName = $prjArr['Project']['name'];
            if ($prjArr['Project']['workflow_id'] != 0) {
                $legenddet = $statuscls->find('first', array('conditions' => array('Status.workflow_id' => $prjArr['Project']['workflow_id'], 'Status.seq_order' => '1')));
                #pr($legend['Status']['id']);exit;
            }
        } else {
            $projId = $formdata['pid'];
            $projName = 'All';
        }

####### Case Format
        if (isset($cloud_storages) && !empty($cloud_storages)) { //By Orangescrum
            $postParam['Easycase']['format'] = 1;
            $format = 1;
        } else {
            if (!$formdata['task_uid']) {
                if ($chk == 0) {
                    $postParam['Easycase']['format'] = 2;
                    $format = 2;
                } else {
                    $postParam['Easycase']['format'] = 1;
                    $format = 1;
                }
            } elseif ($chk != 0) {
                $postParam['Easycase']['format'] = 1;
                $format = 1;
            }
        }

        $emailTitle = $this->Format->convert_ascii($postParam['Easycase']['title']);
        $caseIstype = $postParam['Easycase']['istype'];

        if ($caseIstype == 1) {
####### Case Type (if not selected it is "2", if type is update priority is "0")
            if ($postParam['Easycase']['type_id'] == 10) {
                $postParam['Easycase']['priority'] = 0;
            }
            $casePriority = $postParam['Easycase']['priority'];
            $caseTypeId = $postParam['Easycase']['type_id'];

####### Case Message (can be NULL)
            if ($postParam['Easycase']['message'] == "Enter Description...") {
                $postParam['Easycase']['message'] = "";
            }
####### Due Date (can be NULL, change Date format)
            if ($postParam['Easycase']['due_date']) {
                $postParam['Easycase']['due_date'] = date("Y-m-d H:i:s", strtotime($postParam['Easycase']['due_date']));
            } else {
                $postParam['Easycase']['due_date'] = NULL;
            }

            $postParam['Easycase']['status'] = 1;
            if ($prjArr['Project']['workflow_id'] != 0) {
                $postParam['Easycase']['legend'] = $legenddet['Status']['id'];
            }

###### Get Case#
            if ($formdata['task_uid'] && $formdata['taskid']) {
                $emailbody = "Updated a task: ";
                $userCaseView = 1;
                $csType = "New";
                $caseNoArr = $Easycase->findByUniqId($formdata['task_uid']);
                $easy_id = $caseNoArr['Easycase']['id'];

                $caseNo = $caseNoArr['Easycase']['case_no'];
                $postParam['Easycase']['case_count'] = ($caseNoArr['Easycase']['case_count'] + 1);
                if (defined('TSG') && TSG == 1) {
                    $postParam['Easycase']['legend'] = $caseNoArr['Easycase']['legend'];
                }
                unset($caseNoArr['Easycase']['id']);
                $caseNoArr['Easycase']['legend'] = 6;
//$caseNoArr['Easycase']['updated_by']=SES_ID;
                $caseNoArr['Easycase']['hours'] = 0;
                $caseNoArr['Easycase']['estimated_hours'] = 0;
                $caseNoArr['Easycase']['istype'] = 2;
                $caseNoArr['Easycase']['dt_created'] = GMT_DATETIME;
                $caseNoArr['Easycase']['actual_dt_created'] = GMT_DATETIME;
                if (defined('CR') && CR == 1) {
                    $caseNoArr['Easycase']['client_status'] = isset($formdata['is_client']) ? $formdata['is_client'] : $caseNoArr['Easycase']['client_status'];
                }
                $Easycase->save($caseNoArr);

//Update updated_by in parent task
                $Easycase->id = $easy_id;
                $Easycase->saveField('updated_by', $user_id);
                $Easycase->id = '';
            } else {
                if ($update == 0) {
                    $caseNoArr = $Easycase->find('first', array('conditions' => array('Easycase.project_id' => $projId), 'fields' => array('MAX(Easycase.case_no) as caseno')));
                    $caseNo = $caseNoArr[0]['caseno'] + 1;
                    $postParam['Easycase']['case_no'] = $caseNo;
                } else {
                    $caseNo = $postParam['Easycase']['case_no'];
                }
##### Status & Email Settings
                $postParam['Easycase']['status'] = 1;
                if (defined('TSG') && TSG != 1) {
                    $postParam['Easycase']['legend'] = 1;
                }
                $msg = "<font color='#737373'><b>Status: </b></font><font color='#763532'>NEW</font>";

                if ($update == 0) {
                    $userCaseView = 1;
                    $csType = "New";
                    $emailbody = __("posted a new Task");
                }
                if ($postParam['Easycase']['type_id'] == 10) {
                    $msg = "";
                }
            }
        } else {
            $postParam['Easycase']['title'] = "";
            $caseTypeId = $postParam['Easycase']['type_id'];
            $casePriority = $postParam['Easycase']['priority'];
            $caseNo = $postParam['Easycase']['case_no'];

##### Status
            if ($postParam['Easycase']['legend'] == "") {
                
            } else {
                if ($postParam['Easycase']['legend'] == 3) {
                    $postParam['Easycase']['status'] = 2;
                    $status = 2;
                } else {
                    $postParam['Easycase']['status'] = 1;
                    $status = 1;
                }

                $postParam['Easycase']['legend'] = $postParam['Easycase']['legend'];
                $legend = $postParam['Easycase']['legend'];
                $legenddet = $statuscls->find('first', array('conditions' => array('Status.id' => $legend, 'Status.workflow_id' => $prjArr['Project']['workflow_id'])));
                $userCaseView = $postParam['Easycase']['legend'];

                if (defined('GNC') && GNC == 1) {
                    $getTitle = $Easycase->query("SELECT title,legend FROM easycases WHERE id='" . $caseid . "'");
                    $old_legend = $getTitle[0]['easycases']['legend'];
                }
##### Email Settings
                if ($prjArr['Project']['workflow_id'] == 0) {
                    if ($postParam['Easycase']['legend'] == 3) {
                        $msg = "<font color='#737373' style='font-weight:bold'>" . __('Status', true) . ":</font> <font color='green'>" . __('CLOSED', true) . "</font>";
                        $csType = "Close";
                        $emailbody = "<font color='green'>" . __('CLOSED', true) . "</font> " . __('the Task', true) . "";
                    } else if ($postParam['Easycase']['legend'] == 1) {
                        $userCaseView = 2;
                        $csType = "Replied";
                        $msg = "<font color='#737373' style='font-weight:bold'>" . __('Status') . ":</font> <font color='#EF6807' >" . __('REPLIED') . "</font>";
                        $emailbody = __("responded on the Task", true);
                    } else if ($postParam['Easycase']['legend'] == 2) {
                        $csType = "WIP";
                        $msg = "<font color='#737373' style='font-weight:bold'>" . __('Status') . ":</font> <font color='#EF6807'>" . __('In Progress') . "</font>";
                        $emailbody = __("responded on the Task");
                    } else if ($postParam['Easycase']['legend'] == 5) {

                        $csType = "Resolved";
                        $msg = "<font color='#737373' style='font-weight:bold'>" . __('Status') . ":</font> <font color='#EF6807'>" . __('RESOLVED') . "</font>";
                        $emailbody = "<font color='#EF6807'>" . __('RESOLVED') . "</font> " . __('the Task') . "";
                    } else if ($postParam['Easycase']['legend'] == 4) {
                        $csType = "Started";
                        $msg = "<font color='#737373' style='font-weight:bold'>" . __('Status') . ":</font> <font color='#55A0C7'>" . __('STARTED') . "</font>";
                        $emailbody = "<font color='#55A0C7'>" . __('STARTED') . "</font> " . __('the Task') . "";
                    }
                } else {
                    $msg = "<font color='#737373' style='font-weight:bold'>Status:</font> <font color='" . $legenddet['Status']['color'] . "'>" . $legenddet['Status']['name'] . "</font>";
                    $csType = "Other";
                    $emailbody = "changed the status of the task to <font color='" . $legenddet['Status']['color'] . "'>" . $legenddet['Status']['name'] . "</font>";
                }
            }
#### Update the status and legend of original case
            $dtcreated = GMT_DATETIME;
            $updquery = "";
            if ($postParam['Easycase']['assign_to']) {
                $updquery = ",assign_to='" . $postParam['Easycase']['assign_to'] . "'";
            }
            $updquery .= ",priority='" . $postParam['Easycase']['priority'] . "'";
            $qryFrmt = "";
            if ($format == 1) {
                $qryFrmt = "format='" . $format . "',";
            }

            /* $total = $Easycase->find('count', array('conditions'=>array('Easycase.case_no' => $postParam['Easycase']['case_no'],'Easycase.project_id' => $postParam['Easycase']['project_id'],'Easycase.isactive'=>1,'Easycase.id !='=>$cases['Easycase']['id']),'fields'=>'DISTINCT Easycase.id'));
              $case_count = $total+1; */
            if ((defined('TSG') && TSG == 1) || (defined('GNC') && GNC == 1)) {
                $legend_stat = isset($formdata['depend']) && $formdata['depend'] == "Yes" ? "legend='" . $legend . "', " : "";
                $task_completed = isset($formdata['depend']) && $formdata['depend'] == "Yes" && isset($formdata['completed']) ? $formdata['completed'] : 0;

                $com_t = "";
                if (isset($formdata['completed']) && !empty($formdata['completed'])) {
                    $com_t = " completed_task='" . $task_completed . "' ,";
                }
                $Easycase->query("UPDATE easycases SET status='" . $status . "',updated_by='" . $user_id . "',case_count=case_count+1, " . $legend_stat . $qryFrmt . $com_t . " dt_created='" . $dtcreated . "' " . $updquery . " WHERE id='" . $caseid . "'");
            } else {
                $Easycase->query("UPDATE easycases SET status='" . $status . "',updated_by='" . $user_id . "',case_count=case_count+1,legend='" . $legend . "', " . $qryFrmt . " dt_created='" . $dtcreated . "' " . $updquery . " WHERE id='" . $caseid . "'");
            }

            $getTitle = $Easycase->query("SELECT title FROM easycases WHERE id='" . $caseid . "'");
            $emailTitle = $this->Format->convert_ascii($getTitle[0]['easycases']['title']);
        }
        $emailMsg = $postParam['Easycase']['message'];

        if ($update == 0 && !$formdata['task_uid']) {
            $caseUniqId = md5(uniqid(mt_rand()));
            $postParam['Easycase']['uniq_id'] = $caseUniqId;
            $postParam['Easycase']['actual_dt_created'] = GMT_DATETIME;
            $postParam['Easycase']['isactive'] = 1;
            if (isset($formdata['CS_user_id']) && $formdata['CS_user_id']) {
                $postParam['Easycase']['user_id'] = $formdata['CS_user_id']; //it is used when reading from mail
            } else {
                $postParam['Easycase']['user_id'] = $user_id;
            }
            $postParam['Easycase']['user_short_name'] = "";
            $postParam['Easycase']['assign_short_name'] = "";
        } elseif ($formdata['task_uid']) {
            $postParam['Easycase']['id'] = $formdata['taskid'];
            $postParam['Easycase']['uniq_id'] = $formdata['task_uid'];
            $caseUniqId = $postParam['Easycase']['uniq_id'];
        } else {
            $caseUniqId = $postParam['Easycase']['uniq_id'];
        }
        $postParam['Easycase']['dt_created'] = GMT_DATETIME;
        $postParam['Easycase']['project_id'] = $projId;

        $postParam['Easycase']['title'] = $this->Format->convert_ascii(trim($postParam['Easycase']['title']));
        $postParam['Easycase']['message'] = $this->Format->convert_ascii(trim($postParam['Easycase']['message']));

        if ($formdata['user_auth_key']) {
            $postParam['Easycase']['user_id'] = $formdata['CS_user_id'];
            $postParam['Easycase']['estimated_hours'] = 0;
        }
        if (isset($formdata['seq_id']) && !empty($formdata['seq_id'])) {
            $postParam['Easycase']['seq_id'] = $formdata['seq_id'];
        }
        if (defined('RCT') && RCT == 1) {
            if ($formdata['CS_recurring_startDate'] != '') {
                $postParam['Easycase']['due_date'] = date('Y-m-d', strtotime($formdata['CS_recurring_startDate']));
            }
            if ($formdata['taskid'] != 0) {
                $recurdata = $Easycase->find('first', array('conditions' => array('Easycase.id' => $formdata['taskid']), 'fields' => array('Easycase.is_recurring')));
                $postParam['Easycase']['is_recurring'] = $recurdata['Easycase']['is_recurring'];
            }
        }
        //pr($postParam);exit;
        if ($Easycase->save($postParam)) {
            //update the task progress of main task 
            if (isset($postParam['Easycase']['completed_task']) && $postParam['Easycase']['completed_task'] != 0 && $postParam['Easycase']['istype'] == 2) {
                $Easycase->query('UPDATE easycases SET completed_task="' . $postParam['Easycase']['completed_task'] . '" WHERE case_no=' . $postParam['Easycase']['case_no'] . ' AND project_id=' . $postParam['Easycase']['project_id'] . ' AND istype=1');
            }
            //update the task legend of main task 
            if (isset($postParam['Easycase']['legend']) && $postParam['Easycase']['legend'] && $postParam['Easycase']['istype'] == 2) {
                $Easycase->query('UPDATE easycases SET legend="' . $postParam['Easycase']['legend'] . '" WHERE case_no=' . $postParam['Easycase']['case_no'] . ' AND project_id=' . $postParam['Easycase']['project_id'] . ' AND istype=1');
            }
            $Project = ClassRegistry::init('Project');
            $ProjectUser = ClassRegistry::init('ProjectUser');
            $ProjectUser->recursive = -1;
            $getUser = $ProjectUser->query("SELECT user_id FROM project_users WHERE project_id='" . $projId . "'");
            $prjuniq = $Project->query("SELECT uniq_id, short_name FROM projects WHERE id='" . $projId . "'");
            $prjuniqid = $prjuniq[0]['projects']['uniq_id']; //print_r($prjuniq);
            $projShName = strtoupper($prjuniq[0]['projects']['short_name']);

            if (isset($postParam['Easycase']['assign_to']) && !empty($postParam['Easycase']['assign_to'])) {
                //$Project->query("UPDATE projects SET default_assign='".$postParam['Easycase']['assign_to']."' WHERE id='".$projId."'");
            }

            if ($caseIstype == 2) { //if($postParam['Easycase']['message'] != '' && $caseIstype == 2)
                //socket.io implement start
                $channel_name = $prjuniqid;

                $pname = $this->Format->getProjectName($projId);
                $msgpub = "'Case Replay Available in '" . $postParam['Easycase']['title'] . "''";

                $this->iotoserver(array('channel' => $channel_name, 'message' => 'Updated.~~' . $user_id . '~~' . $postParam['Easycase']['case_no'] . '~~' . 'UPD' . '~~' . $emailTitle . '~~' . $projShName));
//socket.io implement end
            } else {
//socket.io implement start
                $channel_name = $prjuniqid;
                $pname = $this->Format->getProjectName($projId);
                $msgpub = "'New Case Available in " . $pname . "'";

                $this->iotoserver(array('channel' => $channel_name, 'message' => 'Updated.~~' . $user_id . '~~' . $postParam['Easycase']['case_no'] . '~~' . 'NEW' . '~~' . $postParam['Easycase']['title'] . '~~' . $projShName));
//socket.io implement end
            }
//return pr($Easycase->getLastInsertID());
            if (isset($milestone_id) && $milestone_id) {

                $EasycaseMilestone = ClassRegistry::init('EasycaseMilestone');
                $EasycaseMilestone->recursive = -1;
                if ($formdata['task_uid']) {
                    $milestone_dtls = $EasycaseMilestone->find('first', array('conditions' => array('easycase_id' => $formdata['taskid'], 'project_id' => $projId)));
                    if ($milestone_dtls) {
                        $EasycaseMiles['id'] = $milestone_dtls['EasycaseMilestone']['id'];
                    }
                    $EasycaseMiles['easycase_id'] = $formdata['taskid'];
                } else {
                    $EasycaseMiles['easycase_id'] = $Easycase->getLastInsertID();
                }
                $EasycaseMiles['milestone_id'] = $milestone_id;
                $EasycaseMiles['project_id'] = $projId;
                $EasycaseMiles['user_id'] = $user_id;
                $EasycaseMiles['dt_created'] = GMT_DATETIME;
                $EasycaseMilestone->saveAll($EasycaseMiles);
            }
            if ($update == 0) {
                if ($formdata['task_uid']) {
                    $caseid = $formdata['taskid'];
                } else {
                    $caseid = $Easycase->getLastInsertID();
                }
            }
            if ($caseIstype == 1) {
                $ProjectUser = ClassRegistry::init('ProjectUser');
                $ProjectUser->recursive = -1;
                $ProjectUser->query("UPDATE project_users SET dt_visited='" . GMT_DATETIME . "' "
                        . "WHERE project_id=" . $projId . " AND user_id=" . $user_id);
            }

//By Orangescrum
            if (isset($cloud_storages) && !empty($cloud_storages)) {
                $this->fileInfo($cloud_storages, $projId, $caseid, $company_id, $user_id);
            }

            $isUserModule = 0;

            if ($update == 1 || $formdata['task_uid']) {
                $CaseUserEmail = ClassRegistry::init('CaseUserEmail');
                $CaseUserEmail->query("DELETE FROM case_user_emails WHERE easycase_id=" . $caseid);
            }

            $caUid = "";
            $assignTo = "";
            if ($postParam['Easycase']['assign_to']) {
                $caUid = $postParam['Easycase']['assign_to'];
            }

            $due_date = "";
            $padd = "";
            if ($postParam['Easycase']['due_date']) {
                $due_date = $postParam['Easycase']['due_date'];
            }
            if ($caUid && $caUid != $user_id) {
                if ($isUserModule == 0) {
                    $User = ClassRegistry::init('User');
                    $User->recursive = -1;
                }
                $usrDtls2 = $User->find('first', array('conditions' => array('User.id' => $caUid, 'User.isactive' => 1), 'fields' => array('User.name')));
                if (count($usrDtls2) && $usrDtls2['User']['name']) {
                    $assignTo = "<tr><td align='left' style='color:#235889;line-height:20px;padding-top:10px'>" . __('This task is assigned to') . " <i>" . $usrDtls2['User']['name'] . "</i></td></tr>";
                }
            }
            if ($due_date != "NULL" && $due_date != "0000-00-00" && $due_date != "") {
                if (!$assignTo) {
                    $padd = "padding-top:10px;";
                }
                $assignTo.= "<tr><td align='left' style='" . $padd . "'>" . __('Due date') . ": <font color='#235889'>" . date("m/d/Y", strtotime($due_date)) . "</font></td></tr>";
            }
            $allfiles = array('allfiles' => '', 'storage' => '', 'file_error' => '');
            if (is_array($fileArray) && count($fileArray)) {
                $editRemovedFile = $formdata['editRemovedFile'];
                if ($editRemovedFile && $formdata['taskid']) {
                    $this->removeFiles($editRemovedFile, $formdata['taskid'], $company_id, $user_id);
                }
                $allfiles = $this->uploadAndInsertFile($fileArray, $caseid, 0, $projId, $domain, $editRemovedFile, $company_id, $user_id);
            }
            $isAssignedUserFree = 1;
            if (defined('GTLG') && GTLG == 1 && $estimated_hours != '' && $postParam['Easycase']['gantt_start_date'] != '' && $postParam['Easycase']['assign_to'] != 0) {
                if ($formdata['task_uid'] && $formdata['taskid']) {
                    $this->Format->delete_booked_hours(array('easycase_id' => $postParam['Easycase']['id'], 'project_id' => $postParam['Easycase']['project_id']));
                }
                $data_avl = false;
                $taskId = isset($formdata['taskid']) && $formdata['taskid'] != '' && $formdata['taskid'] != 0 ? $formdata['taskid'] : $Easycase->getLastInsertID();
                $postParam['Easycase']['company_work_hour'] = $formdata['company_work_hour'];
                $isAssignedUserFree = $this->setBookedData($postParam, $estimated_hours, $taskId, $company_id);
            }
            if(defined('GTLG') && GTLG == 1 && $postParam['Easycase']['istype'] == 2){
                $easycase_details = $Easycase->find('first',array('conditions'=>array('case_no'=>$postParam['Easycase']['case_no'],'project_id'=>$postParam['Easycase']['project_id'],'istype'=>1)));
                if($easycase_details['Easycase']['estimated_hours'] != '' && $easycase_details['Easycase']['gantt_start_date'] != '' && $postParam['Easycase']['assign_to'] != 0){
                    $this->Format->delete_booked_hours(array('easycase_id' => $easycase_details['Easycase']['id'], 'project_id' => $easycase_details['Easycase']['project_id']));
               
                $data_avl = false;
                $taskIds = isset($formdata['taskid']) && $formdata['taskid'] != '' && $formdata['taskid'] != 0 ? $formdata['taskid'] : $easycase_details['Easycase']['id'];
                $isAssignedUserFree = $this->setBookedData($easycase_details, $easycase_details['Easycase']['estimated_hours'], $taskIds, $company_id);
                }
                
         //echo "<pre>";print_r($easycase_details);exit;
            }
            if (defined('RCT') && RCT == 1 && $postParam['Easycase']['is_recurring'] == 1 && $formdata['CS_id'] == 0 && $formdata['taskid'] == 0) {
                $recurringEasycase = ClassRegistry::init('RecurringEasycase');
                /* if($formdata['taskid'] != 0){
                  $recurringEasycase->query("UPDATE recurring_easycases SET recurring_type = '".$formdata['CS_recurringType']."' WHERE easycase_id='" . $caseid . "'");
                  $getOccurrence = $recurringEasycase->query("SELECT occurrence, recurring_type, start_date FROM recurring_easycases WHERE easycase_id='" . $caseid . "'");
                  if($formdata['CS_occurrence'] == '' && date('Y-m-d', strtotime($formdata['CS_recurring_endDate'])) != $getOccurrence[0]['recurring_easycases']['end_date']){
                  $recurringEasycase->query("UPDATE recurring_easycases SET recurring_end_type = 'date',end_date='" .date('Y/m/d', strtotime($formdata['CS_recurring_endDate'])). "' WHERE easycase_id='" . $caseid . "'");
                  $recurringTask['RecurringEasycase']['end_date'] = date('Y-m-d', strtotime($formdata['CS_recurring_endDate']));
                  $days_diff = (strtotime($recurringTask['RecurringEasycase']['end_date']) - strtotime($getOccurrence[0]['recurring_easycases']['start_date'])) / (60*60*24);
                  if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Weekly') {
                  $occurrence = (int)($days_diff/7);
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Monthly') {
                  $occurrence = floor($days_diff/30);
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Quarterly') {
                  $occurrence = floor($days_diff/90);
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Yearly') {
                  $occurrence = floor($days_diff/365);
                  }
                  }else if($formdata['CS_recurring_endDate'] == '' && $getOccurrence[0]['recurring_easycases']['occurrence'] != $formdata['CS_occurrence']){
                  $recurringEasycase->query("UPDATE recurring_easycases SET recurring_end_type='occur', occurrence=" .$formdata['CS_occurrence']. " WHERE easycase_id='" . $caseid . "'");
                  $occurrence = $formdata['CS_occurrence'];
                  }
                  if($occurrence != $getOccurrence[0]['recurring_easycases']['occurrence']){
                  $repeatedTasks = $Easycase->query('SELECT id, title, case_count, due_date from easycases where project_id = "'.$projId.'" AND title LIKE "%' .$formdata["CS_title"]. ' - %" order by id DESC');
                  $c = 0;
                  if($getOccurrence[0]['recurring_easycases']['occurrence'] > $occurrence){
                  foreach($repeatedTasks as $k=>$task){
                  if($c <= $formdata['CS_occurrence']){
                  if($task['easycases']['case_count'] == 0){
                  $Easycase->query("DELETE FROM easycases WHERE id=" . $task['easycases']['id']);
                  $c++;
                  }
                  }
                  }
                  }else{
                  if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Weekly') {
                  $m = " +7days";
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Monthly') {
                  $m = " +30days";
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Quarterly') {
                  $m = " +90days";
                  } else if ($getOccurrence[0]['recurring_easycases']['recurring_type'] == 'Yearly') {
                  $m = " +365days";
                  }
                  for ($i = count($repeatedTasks); $i < $occurrence; $i++) {
                  unset($postParam['Easycase']['id']);
                  if($i == count($repeatedTasks)){
                  $postParam['Easycase']['due_date'] = date("Y-m-d", strtotime($repeatedTasks[0]['easycases']['due_date'] . $m));
                  }else{
                  $postParam['Easycase']['due_date'] = date("Y-m-d", strtotime($postParam['Easycase']['due_date'] . $m));
                  }
                  $postParam['Easycase']['title'] = $formdata['CS_title'] .' - '. ($i+1);
                  $postParam['Easycase']['uniq_id'] = md5(mt_rand().uniqid());
                  $postParam['Easycase']['is_recurring'] = 0;
                  $postParam['Easycase']['case_no'] = ($caseNo + $i+1);
                  $postParam['Easycase']['case_count'] = 0;
                  $postParam['Easycase']['legend'] = 1;
                  $postParam['Easycase']['user_id'] = SES_ID;
                  if($Easycase->saveAll($postParam)){
                  if (is_array($fileArray) && count($fileArray)) {
                  $editRemovedFile = $formdata['editRemovedFile'];
                  if ($editRemovedFile && $formdata['taskid']) {
                  $this->removeFiles($editRemovedFile, $formdata['taskid']);
                  }
                  $allfiles = $this->uploadAndInsertFile($fileArray, $Easycase->getLastInsertID(), 0, $projId, $domain, $editRemovedFile);
                  }
                  if (isset($cloud_storages) && !empty($cloud_storages)) {
                  $this->fileInfo($cloud_storages, $projId, $Easycase->getLastInsertID());
                  }
                  if (isset($milestone_id) && $milestone_id) {
                  if ($formdata['task_uid']) {
                  $milestone_dtls = $EasycaseMilestone->find('first', array('conditions' => array('easycase_id' => $Easycase->getLastInsertID(), 'project_id' => $projId)));
                  if ($milestone_dtls) {
                  $EasycaseMiles['id'] = $milestone_dtls['EasycaseMilestone']['id'];
                  }
                  $EasycaseMiles['easycase_id'] = $formdata['taskid'];
                  } else {
                  $EasycaseMiles['easycase_id'] = $Easycase->getLastInsertID();
                  }
                  $EasycaseMiles['milestone_id'] = $milestone_id;
                  $EasycaseMiles['project_id'] = $projId;
                  $EasycaseMiles['user_id'] = SES_ID;
                  $EasycaseMiles['dt_created'] = GMT_DATETIME;
                  $EasycaseMilestone->saveAll($EasycaseMiles);
                  }
                  }
                  }
                  }
                  }
                  }else if($formdata['taskid'] == 0){ */
                $rRule = $this->Format->getRRule($formdata['recurringData'], 'test');
                $rRuleDetails = $rRule->getRule();
                $recurringTask = array(
                    'easycase_id' => $Easycase->getLastInsertID(),
                    'project_id' => $postParam['Easycase']['project_id'],
                    'company_id' => SES_COMP,
                    'frequency' => $rRuleDetails['FREQ'],
                    'interval' => $rRuleDetails['INTERVAL'],
                    'bymonthday' => $rRuleDetails['BYMONTHDAY'],
                    'byday' => $rRuleDetails['BYDAY'],
                    'byweekno' => $rRuleDetails['BYWEEKNO'],
                    'bymonth' => $rRuleDetails['BYMONTH'],
                    'start_date' => $rRuleDetails['DTSTART'],
                    'occurrences' => $recurrenceDetail['recurrence_end_type'] != 'no' ? $rRuleDetails['COUNT'] : '',
                    'end_Date' => $recurrenceDetail['recurrence_end_type'] != 'no' ? $rRuleDetails['UNTIL'] : '',
                );
                $recurringEasycase->save($recurringTask);
//                $recurringTask['RecurringEasycase']['easycase_id'] = $Easycase->getLastInsertID();
//                $recurringTask['RecurringEasycase']['recurring_type'] = $formdata['CS_recurringType'];
//                $recurringTask['RecurringEasycase']['start_date'] = date("Y-m-d", strtotime($formdata['CS_recurring_startDate']));
//                $occurrence = 0;
//                if (isset($formdata['CS_recurring_endDate']) && $formdata['CS_recurring_endDate'] != '') {
//                    $recurringTask['RecurringEasycase']['end_date'] = date('Y-m-d', strtotime($formdata['CS_recurring_endDate']));
//                    $days_diff = (strtotime($recurringTask['RecurringEasycase']['end_date']) - strtotime($recurringTask['RecurringEasycase']['start_date'])) / (60 * 60 * 24);
//                    if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Weekly') {
//                        $occurrence = (int) ($days_diff / 7);
//                        $recurringTask['RecurringEasycase']['occurrence'] = $occurrence;
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Monthly') {
//                        $occurrence = floor($days_diff / 30);
//                        $recurringTask['RecurringEasycase']['occurrence'] = $occurrence;
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Quarterly') {
//                        $occurrence = floor($days_diff / 90);
//                        $recurringTask['RecurringEasycase']['occurrence'] = $occurrence;
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Yearly') {
//                        $occurrence = floor($days_diff / 365);
//                        $recurringTask['RecurringEasycase']['occurrence'] = $occurrence;
//                    }
//                }
//                if (isset($formdata['CS_occurrence']) && $formdata['CS_occurrence'] != '') {
//                    $recurringTask['RecurringEasycase']['occurrence'] = $formdata['CS_occurrence'];
//                    if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Weekly') {
//                        $daysToadd = " +" . (7 * (int) $formdata['CS_occurrence']) . "days";
//                        $recurringTask['RecurringEasycase']['end_date'] = date("Y-m-d", strtotime($recurringTask['RecurringEasycase']['start_date'] . $daysToadd));
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Monthly') {
//                        $daysToadd = " +" . (30 * (int) $formdata['CS_occurrence']) . "days";
//                        $recurringTask['RecurringEasycase']['end_date'] = date("Y-m-d", strtotime($recurringTask['RecurringEasycase']['start_date'] . $daysToadd));
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Quarterly') {
//                        $daysToadd = " +" . (90 * (int) $formdata['CS_occurrence']) . "days";
//                        $recurringTask['RecurringEasycase']['end_date'] = date("Y-m-d", strtotime($recurringTask['RecurringEasycase']['start_date'] . $daysToadd));
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Yearly') {
//                        $daysToadd = " +" . (365 * (int) $formdata['CS_occurrence']) . "days";
//                        $recurringTask['RecurringEasycase']['end_date'] = date("Y-m-d", strtotime($recurringTask['RecurringEasycase']['start_date'] . $daysToadd));
//                    }
//                    $occurrence = $recurringTask['RecurringEasycase']['occurrence'] - 1;
//                }
//                $recurringTask['RecurringEasycase']['recurring_end_type'] = $formdata['CS_recurringEndType'];
//                $recurringTask['RecurringEasycase']['created'] = GMT_DATETIME;
//                #print_r($recurringTask);exit;
//                if ($recurringEasycase->save($recurringTask)) {
//                    if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Weekly') {
//                        $m = " +7days";
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Monthly') {
//                        $m = " +30days";
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Quarterly') {
//                        $m = " +90days";
//                    } else if ($recurringTask['RecurringEasycase']['recurring_type'] == 'Yearly') {
//                        $m = " +365days";
//                    }
//                    for ($i = 1; $i <= $occurrence; $i++) {
//                        $postParam['Easycase']['due_date'] = date("Y-m-d", strtotime($postParam['Easycase']['due_date'] . $m));
//                        $postParam['Easycase']['title'] = $formdata['CS_title'] . ' - ' . $i;
//                        $postParam['Easycase']['uniq_id'] = md5(mt_rand() . uniqid());
//                        $postParam['Easycase']['is_recurring'] = 2;
//                        $postParam['Easycase']['case_no'] = ($postParam['Easycase']['case_no'] + 1);
//                        $postParam['Easycase']['case_count'] = 0;
//                        $postParam['Easycase']['legend'] = 1;
//                        $postParam['Easycase']['user_id'] = SES_ID;
//                        if ($Easycase->saveAll($postParam)) {
//                            if (is_array($fileArray) && count($fileArray)) {
//                                $editRemovedFile = $formdata['editRemovedFile'];
//                                if ($editRemovedFile && $formdata['taskid']) {
//                                    $this->removeFiles($editRemovedFile, $formdata['taskid']);
//                                }
//                                $allfiles = $this->uploadAndInsertFile($fileArray, $Easycase->getLastInsertID(), 0, $projId, $domain, $editRemovedFile);
//                            }
//                            if (isset($cloud_storages) && !empty($cloud_storages)) {
//                                $this->fileInfo($cloud_storages, $projId, $Easycase->getLastInsertID());
//                            }
//                            if (isset($milestone_id) && $milestone_id) {
//                                if ($formdata['task_uid']) {
//                                    $milestone_dtls = $EasycaseMilestone->find('first', array('conditions' => array('easycase_id' => $Easycase->getLastInsertID(), 'project_id' => $projId)));
//                                    if ($milestone_dtls) {
//                                        $EasycaseMiles['id'] = $milestone_dtls['EasycaseMilestone']['id'];
//                                    }
//                                    $EasycaseMiles['easycase_id'] = $formdata['taskid'];
//                                } else {
//                                    $EasycaseMiles['easycase_id'] = $Easycase->getLastInsertID();
//                                }
//                                $EasycaseMiles['milestone_id'] = $milestone_id;
//                                $EasycaseMiles['project_id'] = $projId;
//                                $EasycaseMiles['user_id'] = SES_ID;
//                                $EasycaseMiles['dt_created'] = GMT_DATETIME;
//                                $EasycaseMilestone->saveAll($EasycaseMiles);
//                            }
//                        }
//                    }
//                }
//                }
                    }
            $this->write('STATUS', "", '-365 days');
            $this->write('PRIORITY', "", '-365 days');
            $this->write('CS_TYPES', "", '-365 days');
            $this->write('MEMBERS', "", '-365 days');
            $this->write('IS_SORT', "", '-365 days');
            $this->write('ORD_DATE', "", '-365 days');
            $this->write('ORD_TITLE', "", '-365 days');
            $this->write('SEARCH', "", '-365 days');
            $success = "success";
        }
//}

        $ret_res = array('success' => $success, 'pagename' => $pagename, 'formdata' => $formdata['CS_project_id'], 'postParam' => $postParam['Easycase']['postdata'],
            'caseUniqId' => $caseUniqId, 'format' => $format, 'allfiles' => $allfiles['allfiles'], 'caseNo' => $caseNo, 'emailTitle' => $emailTitle,
            'emailMsg' => $emailMsg, 'casePriority' => $casePriority, 'caseTypeId' => $caseTypeId, 'msg' => $msg, 'emailbody' => $emailbody, 'assignTo' => $assignTo,
            'name_email' => $name_email, 'csType' => $csType, 'projId' => $projId, 'caseid' => $caseid, 'caUid' => $caUid, 'caseIstype' => $caseIstype,
            'projName' => $projName, "storage_used" => $allfiles['storage'], 'file_upload_error' => $allfiles['file_error'], 'isAssignedUserFree' => $isAssignedUserFree);
        if (isset($postParam['Easycase']['assign_to']) && !empty($postParam['Easycase']['assign_to'])) {
            $ret_res['cs_cur_assign_to'] = $postParam['Easycase']['assign_to'];
        }
        if (isset($formdata['depend']) && $formdata['depend'] != "Yes") {
            if ($old_legend != trim($postParam['Easycase']['legend'])) {
                $ret_res['depend_msg'] = 'Your reply is posted. But status and progress cannot be changed as dependant tasks are not closed.';
            }
        }
        if(defined('GTLG') && GTLG == 1 && $postParam['Easycase']['istype'] == 2){
            $ret_res['reply_strt_date'] = $easycase_details['Easycase']['gantt_start_date'];
            $ret_res['reply_due_date'] = $easycase_details['Easycase']['due_date'];
            $ret_res['reply_estimated'] = round($easycase_details['Easycase']['estimated_hours']/3600);
            $ret_res['reply_caseUniqId'] = $easycase_details['Easycase']['uniq_id'];
            $ret_res['reply_caseId'] = $easycase_details['Easycase']['id'];
        }
        return json_encode($ret_res);
    }

    /**
     * This method keeps file's information of google drive and dropbox.
     * 
     * @author Orangescrum
     * @method fileInfo
     * @params array, projectid, easycaseid
     * @return
     */
    function fileInfo($files, $project_id, $case_id, $company_id = '', $user_id = '') {
        $Case_file = ClassRegistry::init('CaseFile');
        $Case_file->recursive = -1;

        $case_file_drive = ClassRegistry::init('caseFileDrive');
        $case_file_drive->recursive = -1;

        if ($company_id == '') {
            $company_id = SES_COMP;
        }
        if ($user_id == '') {
            $user_id = SES_ID;
        }
        $caseFileDrives['project_id'] = $caseFile['project_id'] = $project_id;
        $caseFileDrives['easycase_id'] = $caseFile['easycase_id'] = $case_id;

        $caseFile['user_id'] = $user_id;
        $caseFile['company_id'] = $company_id;
        $caseFile['isactive'] = 1;

        foreach ($files as $key => $value) {
            $caseFileDrives['file_info'] = $value;
            $file = json_decode($value, true);
            $caseFile['file'] = $file['title'];
            $caseFile['downloadurl'] = $file['alternateLink'];

            $Case_file->saveAll($caseFile);
            $case_file_drive->saveAll($caseFileDrives);
        }
    }

    function uploadAndInsertFile($files, $caseid, $cmnt, $projId, $domain = HTTP_ROOT, $editRemovedFile = '', $company_id = '', $user_id = '') {
        $CaseFile = ClassRegistry::init('CaseFile');
        $CaseFile->recursive = -1;
        if ($company_id == '') {
            $company_id = SES_COMP;
            $api = false;
        } else {
            $api = true;
        }
        if ($user_id == '') {
            $user_id = SES_ID;
        }
        $CaseFile->cacheQueries = false;
        $sql = "SELECT SUM(file_size) AS file_size  FROM case_files   WHERE company_id = '" . $company_id . "'";
        $res1 = $CaseFile->query($sql);
        $fkb = $res1['0']['0']['file_size'];
        $allfiles = "";
        $filename = "";
        $sizeinkb = 0;
        $fileid = 0;
        $filecount = 0;
        foreach ($files as $file) {
            if ($file && strstr($file, "|")) {
                $filename = "";
                $sizeinkb = 0;
                $fileid = 0;
                $filecount = 0;
                $fl = explode("|", $file);
                if (isset($fl['0'])) {
                    $filename = $fl['0'];
                }
                if (isset($fl['1'])) {
                    $sizeinkb = $fl['1'];
                }
                if (isset($fl['2'])) {
                    $fileid = $fl['2'];
                }
                if (isset($fl['3'])) {
                    $filecount = $fl['3'];
                }
                if ($filecount && $fileid) {
###### Update case file table for same file
                    $csFile['id'] = $fileid;
                    $csFile['count'] = $filecount;
                    $CaseFile->saveAll($csFile);
                } elseif ($fileid) {
                    continue;
                }
                $res['file_error'] = 0;

                if ((strtolower($GLOBALS['Userlimitation']['storage']) == 'unlimited') || (($fkb / 1024) < $GLOBALS['Userlimitation']['storage']) || $api == true) {
                    $fkb += $sizeinkb;
                    ###### Insert to case file table
                    $csFiles['user_id'] = $user_id;
                    $csFiles['project_id'] = $projId;
                    $csFiles['company_id'] = $company_id;
                    $csFiles['easycase_id'] = $caseid;
                    $csFiles['file'] = $filename;
                    $csFiles['file_size'] = $sizeinkb;
                    $csFiles['comment_id'] = $cmnt;
                    if ($CaseFile->saveAll($csFiles)) {
                        if (USE_S3) {
                            $s3 = new S3(awsAccessKey, awsSecretKey);
                            $ret_res = $s3->copyObject(BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER_TEMP . $filename, BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER . $filename, S3::ACL_PRIVATE);
                            if ($ret_res) {
                                //$s3->deleteObject(BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER_TEMP.$filename, S3::ACL_PRIVATE);
                            }
                        }
                    }
                    $allfiles.= "<a href='" . $domain . "users/login/?file=" . $filename . "' target='_blank' style='text-decoration:underline;color:#0571B5;line-height:24px;'>" . $filename . "</a> <font style='color:#989898;font-size:12px;'>(" . number_format($sizeinkb, 1) . " kb)</font><br/>";
                } else {
                    $res['file_error'] = 1;
                    $res['efile'][] = $file;
                }
            }
        }
        $res['allfiles'] = $allfiles;
        $filesize = $fkb / 1024;
        $res['storage'] = number_format($filesize, 2);
        return $res;
    }

    function uploadFile($tmp_name, $name, $file_path) {
        if ($name) {
// Remove all non-ASCII special characters
            $output = preg_replace('/[^(\x20-\x7F)]*/', '', $name);

            $rep1 = str_replace("~", "_", $output);
            $rep2 = str_replace("!", "_", $rep1);
            $rep3 = str_replace("@", "_", $rep2);
            $rep4 = str_replace("#", "_", $rep3);
            $rep5 = str_replace("%", "_", $rep4);
            $rep6 = str_replace("^", "_", $rep5);
            $rep7 = str_replace("&", "_", $rep6);
            $rep11 = str_replace("+", "_", $rep7);
            $rep13 = str_replace("=", "_", $rep11);
            $rep14 = str_replace(":", "_", $rep13);
            $rep15 = str_replace("|", "_", $rep14);
            $rep16 = str_replace("\"", "_", $rep15);
            $rep17 = str_replace("?", "_", $rep16);
            $rep18 = str_replace(",", "_", $rep17);
            $rep19 = str_replace("'", "_", $rep18);
            $rep20 = str_replace("$", "_", $rep19);
            $rep21 = str_replace(";", "_", $rep20);
            $rep22 = str_replace("`", "_", $rep21);
            $rep23 = str_replace(" ", "_", $rep22);
            $rep28 = str_replace("/", "_", $rep23);

            $oldname = $rep28;
            $ext1 = substr(strrchr($oldname, "."), 1);

            $tot = strlen($oldname);
            $extcnt = strlen($ext1);
            $end = $tot - $extcnt - 1;
            $onlyfile = substr($oldname, 0, $end);

            $CaseFile = ClassRegistry::init('CaseFile');
            $CaseFile->recursive = -1;

            $checkFile = $CaseFile->query("SELECT id,count FROM case_files as CaseFile WHERE file='$oldname'");
            if (count($checkFile) >= 1) {
                $newCount = $checkFile['0']['CaseFile']['count'] + 1;

                $newFileName = $onlyfile . "(" . $newCount . ")." . $ext1;
                $updateData = "|" . $checkFile['0']['CaseFile']['id'] . "|" . $newCount;
            } else {
                $newFileName = $oldname;
                $updateData = "";
            }

            $file = $file_path . $newFileName;
            copy($tmp_name, $file);
            return $newFileName . $updateData;
        } else {
            return false;
        }
    }

    function sendEmail($from, $to, $subject, $message, $type) {
        $to = emailText($to);
        $subject = emailText($subject);
        $message = emailText($message);

        $message = str_replace("<script>", "&lt;script&gt;", $message);
        $message = str_replace("</script>", "&lt;/script&gt;", $message);
        $message = str_replace("<SCRIPT>", "&lt;script&gt;", $message);

        $message = str_replace("</SCRIPT>", "&lt;/script&gt;", $message);

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers.= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers.= 'From:' . $from . "\r\n";
        mail($to, $subject, $message, $headers);
    }

    function generateMsgAndSendMail($uid, $allfiles, $hid_caseno, $case_title, $respond, $hid_proj, $hid_priority, $hid_type, $msg, $emailbody, $assignTo, $name_email, $case_uniq_id, $type, $toEmail = NULL, $toName = NULL, $domain = HTTP_ROOT) {
        App::import('helper', 'Casequery');
        $csQuery = new CasequeryHelper(new View(null));

        App::import('helper', 'Format');
        $frmtHlpr = new FormatHelper(new View(null));

##### get User Details
        $to = "";
        $to_name = "";
        if (!$toEmail) {
            $toUsrArr = $csQuery->getUserDtls($uid);
            if (count($toUsrArr)) {
                $to = $toUsrArr['User']['email'];
                $to_name = $frmtHlpr->formatText($toUsrArr['User']['name']);
            }
        } else {
            $to = $toEmail;
            $to_name = $toName;
        }
##### get Sender Details
        $senderUsrArr = $csQuery->getUserDtls(SES_ID);
        $by_name = "";
        $by_name = "";
        if (count($senderUsrArr)) {
            $by_email = $senderUsrArr['User']['email'];
            $by_name = $frmtHlpr->formatText($senderUsrArr['User']['name']);
        }
//$from_name = preg_replace("/[^a-zA-Z0-9]+/", "", $by_name);
        $fromname = $frmtHlpr->formatText(trim($senderUsrArr['User']['name'] . " " . $senderUsrArr['User']['last_name']));

##### get Project Details
        $Project = ClassRegistry::init('Project');
        $Project->recursive = -1;
        $prjArr = $Project->find('first', array('conditions' => array('Project.id' => $hid_proj), 'fields' => array('Project.name', 'Project.short_name', 'Project.uniq_id')));
        $projName = "";
        $case_no = "";
        $projUniqId = "";
        if (count($prjArr)) {
            $projName = $frmtHlpr->formatText($prjArr['Project']['name']);
            $case_no = $frmtHlpr->formatText($prjArr['Project']['short_name']) . "-" . $hid_caseno;
            $projUniqId = $prjArr['Project']['uniq_id'];
        }
##### get Case Type
        $cseTyp = "";
        $csTypArr = $csQuery->getType($hid_type);
        if (count($csTypArr)) {
            $cseTyp = $csTypArr['Type']['name'];
        }
        if ($hid_type != 10) {
            $pri = "";
            if ($hid_priority == "NULL" || $hid_priority == "") {
                $pri = "<font  style='color:#AD9227;padding:0;margin:0;height:16px;'>" . __('LOW', true) . "</font>";
            } else if ($hid_priority == 0) {
                $pri = "<font style='color:#AE432E;padding:0;margin:0;height:16px;'>" . __('HIGH', true) . "</font>";
            } else if ($hid_priority == 1) {
                $pri = "<font style='color:#28AF51;padding:0;margin:0;height:16px;'>" . __('MEDIUM', true) . "</font>";
            } else if ($hid_priority >= 2) {
                $pri = "<font style='color:#AD9227;padding:0;margin:0;height:16px;'>" . __('LOW', true) . "</font>";
            }
            $priRity = "<font color='#737373'><b>" . __("Priority") . ":</b></font> " . $pri;
        } else {
            $priRity = "";
        }

        $postingName = "";
        if (SES_ID == $uid) {
            $postingName = __("You have", true);
        } elseif ($by_name) {
            $postingName = $by_name . __(" has", true);
        }
        $from = FROM_EMAIL_NOTIFY;
        if ($type == "Resolved") {
            $typ = "-" . strtoupper($type);
        } else if ($type == "Closed") {
            $typ = "-" . strtoupper($type);
        } else if ($type == "Started") {
            $typ = "-" . strtoupper($type);
        } else {
            $typ = "";
        }
        $projNameInSh = $projName;
        if (strlen($projNameInSh) > 10) {
//$projNameInSh = substr($projNameInSh,0,9).'...';
            $projNameInSh = $projNameInSh;
        }
        $shrt = $frmtHlpr->formatText($prjArr['Project']['short_name']);
        if ($shrt) {
            $projShortNcaseNumber = $hid_caseno . "(" . $shrt . ")";
        } else {
            $projShortNcaseNumber = $hid_caseno;
        }
        $subject = EMAIL_SUBJ . ":" . $projNameInSh . ":#" . $projShortNcaseNumber . "-" . stripslashes(html_entity_decode($case_title, ENT_QUOTES));

        $message = EMAIL_REPLY . "<body style='width:100%; margin:0; padding:0; -webkit-text-size-adjust:none; -ms-text-size-adjust:none; background-color:#ffffff;'>
        <table cellpadding='0' cellspacing='0' border='0' id='backgroundTable' style='height:auto !important; margin:0; padding:0; width:100% !important; background-color:#F0F0F0;color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:19px; margin-top:0; padding:0; font-weight:normal;'>
        <tr>
        <td>
        <div id='tablewrap' style='width:100% !important; max-width:600px !important; text-align:center; margin:0 auto;'>
        <table id='contenttable' width='600' align='center' cellpadding='0' cellspacing='0' border='0' style='background-color:#FFFFFF; margin:0 auto; text-align:center; border:none; width: 100% !important; max-width:600px !important;border-top:8px solid #5191BD'>
        <tr>
        <td width='100%'>
        <table bgcolor='#FFF' border='0' cellspacing='10' cellpadding='0' width='100%'>
        <tr>
        <td align='left' valign='top' style='line-height:22px;font:14px Arial;'>
        <font color='#737373'><b>Title: </b></font> <a href='" . $domain . "users/login/?dashboard#details/" . $case_uniq_id . "' target='_blank' style='text-decoration:underline;color:#F86A0C;'>" . stripslashes($case_title) . "</a>
        <br/><br/>
        <font color='#737373'><b>" . __('Project', true) . ":</b></font> " . $projName . "
        </td>
        </tr>
        <tr>
        <td>
        <table bgcolor='#FFF' border='0' cellspacing='0' cellpadding='0'>
        <tr>
        <td align='left' style='line-height:22px;font:14px Arial'>
        <font color='#737373'><b>" . __("Task", true) . "#:</b></font> " . $case_no . "
        </td>
        <td style='padding-left:10px;line-height:22px;font:14px Arial'>
        <font color='#737373'><b>" . __("Type", true) . ":</b></font> " . $cseTyp . "
        </td>
        </tr>
        <tr style='height:10px;'><td colspan='2'>&nbsp;</td></tr>
        <tr>
        <td align='left' style='line-height:22px;font:14px Arial'>" . $priRity . "</td>
        <td style='padding-left:10px;line-height:22px;font:14px Arial'>" . $msg . "</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        <table bgcolor='#F0F0F0' border='0' cellspacing='0' cellpadding='10' width='100%' style='border-top:2px solid #F0F0F0;margin-top:5px;text-align:left;'>
        <tr>
        <td width='100%' bgcolor='#ffffff' style='text-align:left;font:14px Arial'>
        <p>
        <font color='#737373'><b>" . $postingName . " " . $emailbody . "</b></font>
        </p>
        <p>
        " . stripslashes($respond) . "
        </p>
        <p>
        " . $allfiles . "
        </p>
        </td>	  
        </tr>
        " . $assignTo . "
        </table>
        <table bgcolor='#F0F0F0' border='0' cellspacing='0' cellpadding='10' width='100%' style='border-top:2px solid #F0F0F0;margin-top:10px;text-align:left;'>
        <tr>
        <td width='100%' bgcolor='#ffffff' style='text-align:left;font:14px Arial'>
        <p style='color:#676767; line-height:20px;'>
        " . __("To read the original message, view comments, reply & download attachment", true) . ": <br/> Link: <a href='" . $domain . "users/login/dashboard#details/" . $case_uniq_id . "' target='_blank'>" . $domain . "users/login/dashboard#details/" . $case_uniq_id . "/</a>
        </p>
        <p style='color:#676767; padding-top:2px;'>
        " . __("This email notification is sent by", true) . " " . $by_name . " to " . $name_email . "
        </p>

        </td>	  
        </tr>
        </table>
        <table bgcolor='#F0F0F0' border='0' cellspacing='0' cellpadding='10' width='100%' style='border-top:2px solid #F0F0F0;margin-top:5px;border-bottom:3px solid #2489B3'>
        <tr>
        <td width='100%' bgcolor='#ffffff' style='text-align:center;'>
        <p style='color:#222222; font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:14px; margin-top:0; padding:0; font-weight:normal;padding-top:5px;'>
        " . __("You are receiving this email notification because you have subscribed to Orangescrum, to unsubscribe, please email with subject 'Unsubscribe' to", true) . " <a href='mailto:" . SUPPORT_EMAIL . "'>" . SUPPORT_EMAIL . "</a>

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
        </body>";

//return $this->Sendgrid->sendEmail($from,$to,$subject,$message,$type);
        return $this->Sendgrid->sendGridEmail($from, $to, $subject, $message, $type, $fromname);
    }

###########################################
###### SEND EMAIL TO ASSIGNED USERS #######
###########################################

    function mailToUser($data = array(), $getEmailUser = array(), $type = 0) {
        $name_email = "";
        $ids = "";
        $usrArr = array();
        $emailToAssgnTo = 0;
        foreach ($getEmailUser as $usrMem) {
            if (isset($usrMem['User']['name']) && $usrMem['User']['name']) {
                array_push($usrArr, $usrMem['User']);
                $name_email.= trim($usrMem['User']['name']) . ", ";
                if ($data['caUid'] == $usrMem['User']['id']) {
                    $emailToAssgnTo = 1;
                }
            }
        }
        $name_email = trim(trim($name_email), ",");
        if (count($usrArr)) {

//By Orangescrum
//Getting case uniquid of parent from child node.
            if (isset($data['caseUniqId']) && trim($data['caseUniqId'])) {
                $caseUniqId = $data['caseUniqId'];

                $Easycase = ClassRegistry::init('Easycase');
                $Easycase->recursive = -1;
//$cases = $Easycase->find('first', array('conditions' => array('Easycase.uniq_id' => $data['caseUniqId'],'Easycase.project_id' => $data['projId'],'Easycase.case_no' => $data['caseNo'])));

                if (isset($data['caseIstype']) && $data['caseIstype'] == 2) {
                    $Easycase->recursive = -1;
                    $easycase_parent = $Easycase->find('first', array('conditions' => array('Easycase.case_no' => $data['caseNo'], 'Easycase.project_id' => $data['projId'], 'Easycase.istype' => 1)));
                    $caseUniqId = $easycase_parent['Easycase']['uniq_id'];
                }
            }//End


            $CaseUserEmail = ClassRegistry::init('CaseUserEmail');
            $CaseUserEmail->recursive = -1;
            foreach ($usrArr as $usr) {
                if ($usr['id']) {
                    if ($data['caseIstype'] == 1) {
###### Insert to Case User Email table
                        $userEmail['easycase_id'] = $data['caseid'];
                        $userEmail['user_id'] = $usr['id'];
                        $userEmail['ismail'] = 1;
                        $CaseUserEmail->saveAll($userEmail);
                    }
                    $domain = isset($data['auth_domain']) ? $data['auth_domain'] : HTTP_ROOT;
                    $this->generateMsgAndSendMail($usr['id'], $data['allfiles'], $data['caseNo'], $data['emailTitle'], $data['emailMsg'], $data['projId'], $data['casePriority'], $data['caseTypeId'], $data['msg'], $data['emailbody'], $data['assignTo'], $name_email, $caseUniqId, $data['csType'], $usr['email'], $usr['name'], $domain);
                }
            }
        }
    }

    /**
     * @method eventLog To log each event that a user did 
     * @return bool true/false
     */
    function eventLog($comp_id = SES_COMP, $user_id = SES_ID, $json_arr = array(), $activity_id) {
        $logactivity['LogActivity']['company_id'] = $comp_id;
        $logactivity['LogActivity']['user_id'] = $user_id;
        $logactivity['LogActivity']['log_type_id'] = $activity_id;
        $logactivity['LogActivity']['json_value'] = json_encode($json_arr);
        $logactivity['LogActivity']['ip'] = $_SERVER['REMOTE_ADDR'];
        $logactivity['LogActivity']['created'] = GMT_DATETIME;
        $logActivity = ClassRegistry::init('LogActivity');
        $logActivity->create();
        $logActivity->save($logactivity);
    }

//socket.io implement start
    function iotoserver($messageArr) {
        if (defined('NODEJS_HOST') && trim(NODEJS_HOST)) {
            require ROOT . '/app/Plugin/ElephantIO/vendor/autoload.php';
            //App::import('Vendor', 'ElephantIO', array('file' => 'ElephantIO' . DS . 'src' . DS . 'Client.php'));
            try {
                $client = new Client(new Version1X(NODEJS_HOST));
                $client->initialize();
                //$client->of('/iotoserver');
                $ret = $client->emit('iotoserver', $messageArr);
                $client->close();
            } catch (Exception $e) {
                //fwrite($jk,'erroror'.$e->getMessage());
            }
        }
    }

//socket.io implement end
//socket.io implement start
    function iotoserverchat($messageArr) {
        if (defined('NODEJS_HOST') && trim(NODEJS_HOST)) {
            require ROOT . '/app/Plugin/ElephantIO/vendor/autoload.php';
            //App::import('Vendor', 'ElephantIO', array('file' => 'ElephantIO' . DS . 'src' . DS . 'Client.php'));
            try {
                $client = new Client(new Version1X(NODEJS_HOST));
                $client->initialize();
                $ret = $client->emit('iotoserverchat', $messageArr);
                $client->close();
            } catch (Exception $e) {
                
            }
        }
    }

    function iotoserverlogout($messageArr) {
        if (defined('NODEJS_HOST') && trim(NODEJS_HOST)) {
            require ROOT . '/app/Plugin/ElephantIO/vendor/autoload.php';
            //App::import('Vendor', 'ElephantIO', array('file' => 'ElephantIO' . DS . 'src' . DS . 'Client.php'));
            try {
                $client = new Client(new Version1X(NODEJS_HOST));
                $client->initialize();
                $ret = $client->emit('iotoserverlogout', $messageArr);
                $client->close();
            } catch (Exception $e) {
                
            }
        }
    }

    function iotoservergroup($messageArr) {
        if (defined('NODEJS_HOST') && trim(NODEJS_HOST)) {
            require ROOT . '/app/Plugin/ElephantIO/vendor/autoload.php';
            //App::import('Vendor', 'ElephantIO', array('file' => 'ElephantIO' . DS . 'src' . DS . 'Client.php'));
            try {
                $client = new Client(new Version1X(NODEJS_HOST));
                $client->initialize();
                $ret = $client->emit('iotoservergroup', $messageArr);
                $client->close();
            } catch (Exception $e) {
                
            }
        }
    }

//socket.io implement end

    function dailyMail($user = NULL, $project = NULL, $date = NULL) {
        $from = FROM_EMAIL_NOTIFY;
        $to = $user['email'];
        App::import('helper', 'Format');
        $frmtHlpr = new FormatHelper(new View(null));
        $fromname = $frmtHlpr->formatText(trim($user['name'] . " " . $user['last_name']));
        $subject = ucfirst($project['name']) . " (" . strtoupper($project['short_name']) . ") Daily Catch-Up - " . $date;
        $message = "<table><tr><td><table cellpadding='0' cellspacing='0' align='left' border='0' style='border-collapse:collapse;border-spacing:0;text-align:left;width:600px;border:1px solid #5191BD'>
<tr style='background:#5191BD;height:50px;'>
<td style='font:bold 14px Arial;padding:10px;color:#FFFFFF;'>
<span style='font-size:18px;'>Orangescrum</span> - " . __("Daily Catch-Up Alert", true) . "
</td>
</tr>
<tr>
<td align='left' style='font:14px Arial;padding:10px;'>
Hi " . ucfirst(trim($user['name'])) . ",
</td>
</tr>
<tr>
<td style='font:14px Arial;padding:10px;'>
" . __("This is a reminder to post your today's updates to Orangescrum. Just reply to this email with the updates, it will be added to the project.", true) . "
<br/><br/><br/><b>" . __("NOTE", true) . ":</b> " . __("DO NOT change the SUBJECT while replying.", true) . "<br/><br/>
</td>
</tr>
<tr>
<td align='left' style='font:14px Arial;padding:15px 10px;border-top:1px solid #E1E1E1'>
Thanks,<br/>
Team Orangescrum
</td>	  
</tr>
</table></td></tr>
<tr><td>
<table style='margin-top:5px;width:600px;'>
<tr><td style='font:13px Arial;color:#737373;'>" . __("Don't want these emails? To unsubscribe, please contact your account administrator to turn off <b>Daily Catch-Up</b> alert for you.", true) . "</td></tr>
</table></td></tr>
";
        return $this->Sendgrid->sendGridEmail($from, $to, $subject, $message, '', $fromname);
    }

    /**
     * @method invitenewuser Inivite a list of user with email
     * @return array success and Failure email
     */
    function invitenewuser($mail_arr = array(), $prj_id = 0, $obj, $is_mobile_api = null, $compani_id = null, $user_id = null, $company_uniq_id = null, $cmp_name = null, $is_mobile = null) {
        App::import('Controller', 'Users');
        $userscontroller = new UsersController;

        $usercls = ClassRegistry::init('User');
        $CompanyUser = ClassRegistry::init('CompanyUser');
        $UserInvitation = ClassRegistry::init('UserInvitation');
        $err = 0;
//$mail_arr=explode(",",trim($email_list));
        $ucounter = count($mail_arr);
        /* foreach($mail_arr AS $key=>$val){
          if(trim($val) != ""){
          $ucounter ++;
          }
          } */
        $comp_id = ($compani_id) ? $compani_id : SES_COMP;
        $User_id = ($user_id) ? $user_id : SES_ID;
        $comp_name = ($cmp_name) ? $cmp_name : CMP_SITE;

        if (!$is_mobile_api) {
            $total_new_users = $ucounter + $GLOBALS['usercount'];
            if (strtolower($GLOBALS['Userlimitation']['user_limit']) != 'unlimited' && ($total_new_users > $GLOBALS['Userlimitation']['user_limit'])) {
                $this->Session->write("ERROR", "Sorry! You are exceeding your user limit");
                header('Location:' . HTTP_ROOT);
                exit;
            }
        }
//for($i=0;$i<count($mail_arr);$i++){
        foreach ($mail_arr as $key => $val) {
            if (trim($val) != "") {
                $val = trim($val);
                $findEmail = $usercls->find('first', array('conditions' => array('User.email' => $val), 'fields' => array('User.id')));
                if (@$findEmail['User']['id']) {
                    $userid = $findEmail['User']['id'];
                    $invitation_details = $UserInvitation->find('first', array('conditions' => array('user_id' => $findEmail['User']['id'], 'company_id' => $comp_id), 'fields' => array('id', 'project_id')));
                } else {
                    $userdata['User']['uniq_id'] = $this->Format->generateUniqNumber();
                    $userdata['User']['isactive'] = 2;
                    $userdata['User']['isemail'] = 1;
                    $userdata['User']['dt_created'] = GMT_DATETIME;
                    $userdata['User']['email'] = $val;
                    $usercls->saveAll($userdata);
                    $userid = $usercls->getLastInsertID();
                }
                if ($userid && $userid != $User_id) {
                    $cmpnyUsr = array();
                    $is_sub_upgrade = 1;
// Checking for a deleted user when gets invited again.
                    $compuser = $CompanyUser->find('first', array('conditions' => array('user_id' => $userid, 'company_id' => $comp_id)));
                    if ($compuser && $compuser['CompanyUser']['is_active'] == 0) {
                        if (!$is_mobile_api) {
                            $this->Session->write("ERROR", "Sorry! You are not allowed to add a disabled user to a the project");
                        }
                        continue;
                    }
                    $cmpnyUsr['CompanyUser']['is_active'] = 2;
                    $cmpnyUsr['CompanyUser']['user_type'] = 3;
                    if ($compuser) {
                        $is_sub_upgrade = 0;
                        $cmpnyUsr['CompanyUser']['user_type'] = $compuser['CompanyUser']['user_type'];
                        $cmpnyUsr['CompanyUser']['is_active'] = $compuser['CompanyUser']['is_active'];
                        if ($compuser['CompanyUser']['is_active'] == 3) {
// If that user deleted in the same billing month and invited again then that user will not paid 
                            if ($GLOBALS['Userlimitation']['btsubscription_id']) {
                                if (strtotime($GLOBALS['Userlimitation']['next_billing_date']) > strtotime($compuser['CompanyUser']['billing_end_date'])) {
                                    $is_sub_upgrade = 1;
                                }
                            }
                            $cmpnyUsr['CompanyUser']['user_type'] = 3;
                            $cmpnyUsr['CompanyUser']['is_active'] = 2;
                        }
                        $cmpnyUsr['CompanyUser']['id'] = $compuser['CompanyUser']['id'];
                    }
                    $cmpnyUsr['CompanyUser']['user_id'] = $userid;
                    $cmpnyUsr['CompanyUser']['company_id'] = $comp_id;
                    $cmpnyUsr['CompanyUser']['company_uniq_id'] = ($company_uniq_id) ? $company_uniq_id : $compuser['CompanyUser']['company_uniq_id'];
                    $cmpnyUsr['CompanyUser']['created'] = GMT_DATETIME;
                    if ($CompanyUser->saveAll($cmpnyUsr)) {
                        $qstr = $this->Format->generateUniqNumber();
                        if (@$findEmail['User']['id'] && @$invitation_details['UserInvitation']['id']) {
                            $InviteUsr['UserInvitation']['id'] = $invitation_details['UserInvitation']['id'];
                            $InviteUsr['UserInvitation']['project_id'] = $invitation_details['UserInvitation']['project_id'] ? $invitation_details['UserInvitation']['project_id'] . ',' . $prj_id : $prj_id;
                        } else {
                            $InviteUsr['UserInvitation']['project_id'] = $prj_id;
                        }
                        $InviteUsr['UserInvitation']['invitor_id'] = $User_id;
                        $InviteUsr['UserInvitation']['user_id'] = $userid;
                        $InviteUsr['UserInvitation']['company_id'] = $comp_id;
                        $InviteUsr['UserInvitation']['qstr'] = $qstr;
                        $InviteUsr['UserInvitation']['created'] = GMT_DATETIME;
                        $InviteUsr['UserInvitation']['is_active'] = 1;
                        $InviteUsr['UserInvitation']['user_type'] = 3;
                        if ($UserInvitation->saveAll($InviteUsr)) {

//Event log data and inserted into database in account creation--- Start
                            $json_arr['email'] = $val;
                            $json_arr['created'] = GMT_DATETIME;
                            $this->eventLog($comp_id, $User_id, $json_arr, 25);
//End 
//Subscription price update  if its a paid user -start 
                            $comp_user_id = $CompanyUser->getLastInsertID();

                            if ($is_sub_upgrade) {
                                //$userscontroller->update_bt_subscription($comp_user_id, SES_COMP, 1);
                            }
//end 
                            $to = $val;
                            $expEmail = explode("@", $val);
                            $expName = $expEmail[0];
                            $loggedin_users = $usercls->find('first', array('conditions' => array('User.id' => $User_id, 'User.isactive' => 1), 'fields' => array('User.name', 'User.email', 'User.id')));
                            $fromName = ucfirst($loggedin_users['User']['name']);
                            $fromEmail = $loggedin_users['User']['email'];
                            $ext_user = '';
//			    
                            if (@$findEmail['User']['id']) {
                                $subject = $fromName . __(" invited you to join ", true) . $comp_name . " on Orangescrum";
                                $ext_user = 1;
                            } else {
                                $subject = $fromName . __(" invited you to join Orangescrum", true);
                            }
                            $this->Email->delivery = EMAIL_DELIVERY;
                            $this->Email->to = $to;
                            $this->Email->subject = $subject;
                            $this->Email->from = FROM_EMAIL;
                            if ($is_mobile) {
                                $this->Email->template = 'invite_user_mobile';
                            } else {
                                $this->Email->template = 'invite_user';
                            }
                            $this->Email->sendAs = 'html';
                            $obj->set('expName', ucfirst($expName));
                            $obj->set('qstr', $qstr);
                            $obj->set('existing_user', $ext_user);
                            $obj->set('company_name', $comp_name);
                            $obj->set('fromEmail', $fromEmail);
                            $obj->set('fromName', $fromName);
                            try {
                                $this->Sendgrid->sendgridsmtp($this->Email);
                            } Catch (Exception $e) {
                                
                            }
                        }
                    }
                    $rarr['success'][] = $userid;
                } else {
                    $err = 1;
                    $rarr['error'][] = 1;
                }
            }
        }
        return $rarr;
    }

    /** @method removeFiles It will remove all the Uncheked files during edit & Update of a Task
     * @return bool true/false
     */
    function removeFiles($caseFileids, $easycaseid, $company_id = '', $user_id = '') {
        if (strstr($caseFileids, ',')) {
            $caseFileids = explode(',', $caseFileids);
        }
        $caseFile = ClassRegistry::init('CaseFile');
        $easycase = ClassRegistry::init('Easycase');
        $filedata = $caseFile->find('all', array('conditions' => array('CaseFile.id' => $caseFileids), 'field' => array('id,file,file_size')));
        if ($company_id == '') {
            $company_id = SES_COMP;
        }
        if ($user_id == '') {
            $user_id = SES_ID;
        }
        foreach ($filedata AS $key => $val) {
            $delids[] = $val['CaseFile']['id'];
            $s3 = new S3(awsAccessKey, awsSecretKey);
            $folder_orig_Name = 'files/case_files/' . trim($val['CaseFile']['file']);
//$info = $s3->getObjectInfo(BUCKET_NAME, $folder_orig_Name,S3::ACL_PRIVATE);
            $s3->deleteObject(BUCKET_NAME, $folder_orig_Name, S3::ACL_PRIVATE);
        }
       if ($caseFile->deleteAll(array('CaseFile.id' => $delids, 'CaseFile.company_id' => SES_COMP, 'CaseFile.easycase_id' => $easycaseid))) {
            $cur_data = $easycase->find('first', array('conditions' => array('Easycase.id' => $easycaseid), 'fields' => array('Easycase.case_no', 'Easycase.project_id')));
            $org_data = $easycase->find('list', array('conditions' => array('Easycase.project_id' => $cur_data['Easycase']['project_id'], 'Easycase.case_no' => $cur_data['Easycase']['case_no']), 'fields' => array('Easycase.id')));
            $files = $caseFile->find('all', array('conditions' => array('CaseFile.company_id' => SES_COMP, 'CaseFile.easycase_id' => $org_data)));
            if (empty($files)) {
                $easycase->updateAll(array('format' => 2), array('id' => $org_data, 'project_id' => $cur_data['Easycase']['project_id'], 'case_no' => $cur_data['Easycase']['case_no'], 'istype' => 1));
            }
            return true;
        } else {
            return false;
        }
    }

    /*
     * Author : Satyajeet
     * To check the date in leave or not
     */

    function checkDateInLeave($date, $leaves) {
        foreach ($leaves as $k => $leave) {
            if (strtotime($date) >= strtotime($leave['UserLeave']['start_date']) && strtotime($date) <= strtotime($leave['UserLeave']['end_date'])) {
                return 1;
            } else if (strtotime($date) <= strtotime($leave['UserLeave']['start_date']) && strtotime($date) >= strtotime($leave['UserLeave']['end_date'])) {
                return 0;
}
        }
    }

    /* 
     * set booked data
     */
    function setBookedData($postParam, $estimated_hours,$easycase_id, $company_id){
        $isAssignedUserFree = 1;
        $BookedResources = ClassRegistry::init('Timelog.ProjectBookedResource');
        $UserLeaves = ClassRegistry::init('Timelog.UserLeave');
        $Overload = ClassRegistry::init('Timelog.Overload');
        $leaves = $UserLeaves->find('all', array('conditions' => array('UserLeave.company_id' => SES_COMP, 'UserLeave.user_id' => $postParam['Easycase']['assign_to'])));
        if($isAssignedUserFree == 1){
            foreach ($leaves as $k => $leave) {
                if ((strtotime($leave['UserLeave']['start_date']) <= strtotime($postParam['Easycase']['gantt_start_date'])) && (strtotime($postParam['Easycase']['gantt_start_date']) <= strtotime($leave['UserLeave']['end_date']))) {
                    $isAssignedUserFree = 3;
                    return $isAssignedUserFree;
                }
            }
        }
        
        $perDayWorkSec = isset($GLOBALS['company_work_hour']) ? $GLOBALS['company_work_hour'] * 3600 : $postParam['Easycase']['company_work_hour'] * 3600;
        $no_of_days = ceil($estimated_hours / $perDayWorkSec);
        $startdate = $assigned_Resource_date = date('Y-m-d', strtotime($postParam['Easycase']['gantt_start_date']));
        $lastdate = date('Y-m-d', strtotime($assigned_Resource_date . " +" . ($no_of_days - 1) . "days"));
        $working_dates = array();
        $do = $no_of_days;
        while ($do > 0) {
            $inleave = $this->checkDateInLeave($assigned_Resource_date, $leaves);
            if (!$inleave) {
                if($startdate == date('Y-m-d', strtotime($postParam['Easycase']['due_date']))){ 
                    $working_dates[] = $assigned_Resource_date;break;
                }else{ 
                    $working_dates[] = $assigned_Resource_date;
                    $do--;
                }
            }
            $assigned_Resource_date = date('Y-m-d', strtotime($assigned_Resource_date . " +" . 1 . " days"));
        }
       
        $partial_days = array();
        foreach ($working_dates as $key => $value) {
            $query = "SELECT SUM(`ProjectBookedResource`.`booked_hours`) AS booked_hrs, `ProjectBookedResource`.`id`, `ProjectBookedResource`.`company_id`, `ProjectBookedResource`.`project_id`, `ProjectBookedResource`.`easycase_id`, `ProjectBookedResource`.`user_id`, `ProjectBookedResource`.`date` FROM `project_booked_resources` AS `ProjectBookedResource` INNER JOIN easycases AS Easycase on Easycase.id=`ProjectBookedResource`.`easycase_id` AND Easycase.isactive=1 WHERE `ProjectBookedResource`.`company_id` = " . SES_COMP . " AND `ProjectBookedResource`.`user_id` = " . $postParam['Easycase']['assign_to'] . " AND DATE(`ProjectBookedResource`.`date`) = '" . date('Y-m-d', strtotime($value)) . "' GROUP BY `ProjectBookedResource`.`date`";
            $hours_booked = $BookedResources->query($query);
           
            if (!empty($hours_booked)) {
                $booked_hours = $hours_booked[0][0]['booked_hrs'];
                $available_hrs = $perDayWorkSec - $booked_hours;
                if($estimated_hours > $available_hrs){
                    $partial_days[$value] = $booked_hours;
                }
            }
        }
        if (empty($partial_days)) {
            if ($estimated_hours > $perDayWorkSec) {
                foreach ($working_dates as $key => $value) {
                    $data[$key]['ProjectBookedResource']['company_id'] = $company_id;
                    $data[$key]['ProjectBookedResource']['user_id'] = $postParam['Easycase']['assign_to'];
                    $data[$key]['ProjectBookedResource']['project_id'] = $postParam['Easycase']['project_id'];
                    $data[$key]['ProjectBookedResource']['easycase_id'] = $easycase_id;
                    $data[$key]['ProjectBookedResource']['date'] = date('Y-m-d', strtotime($value));
                    $data[$key]['ProjectBookedResource']['booked_hours'] = $estimated_hours - ($perDayWorkSec * $key) >= $perDayWorkSec ? $perDayWorkSec : $estimated_hours - ($perDayWorkSec * $key);
                }
                $BookedResources->saveMany($data);
                if($startdate == date('Y-m-d', strtotime($postParam['Easycase']['due_date']))){
                    $overload_hr = $estimated_hours - $perDayWorkSec;
                    $overload = array('date'=>$startdate,'easycase_id'=>$easycase_id,'project_id'=>$postParam['Easycase']['project_id'],'user_id'=>$postParam['Easycase']['assign_to'],'company_id'=>SES_COMP,'overload'=>$overload_hr);
                    $Overload->save($overload);
                    
                }
            } else {
                $data['ProjectBookedResource']['company_id'] = $company_id;
                $data['ProjectBookedResource']['user_id'] = $postParam['Easycase']['assign_to'];
                $data['ProjectBookedResource']['project_id'] = $postParam['Easycase']['project_id'];
                $data['ProjectBookedResource']['easycase_id'] = $easycase_id;
                $data['ProjectBookedResource']['date'] = $postParam['Easycase']['gantt_start_date'];
                $data['ProjectBookedResource']['booked_hours'] = $estimated_hours;
                $BookedResources->save($data);
            }
            $isAssignedUserFree = 1;
        } else {
            $isAssignedUserFree = 2;
        }
        return $isAssignedUserFree;
    }
}
