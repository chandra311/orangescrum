<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 's3', array('file' => 's3' . DS . 'S3.php'));

App::import('Vendor', 'ElephantIO', array('file' => 'ElephantIO' . DS . 'Client.php'));

use ElephantIO\Client as ElephantIOClient;

class LogTimesController extends TimelogAppController {

    public $name = 'LogTimes';
    public $components = array('Format', 'Postcase', 'Sendgrid', 'Tmzone');
    public $helpers = array('Format');

    public function beforeFilter() {
        parent::beforeFilter();
    }

    public function add_tasklog($data = Null, $log_id = '') {

        $this->loadModel('Project');
        $this->loadModel('LogTime');
        $this->loadModel('Easycase');
        
        $logdata = isset($data) && !empty($data) ? $data : $this->data;
        $task_id = isset($logdata['task_id']) ? trim($logdata['task_id']) : intval($logdata['hidden_task_id']);
        $this->Format = $this->Components->load('Format');
        if (defined('GNC') && GNC == 1) {
            $allowed = $this->task_dependency($task_id);
        } else {
            $allowed = 'Yes';
        }
        if ($allowed == 'No') {
            $resCaseProj = null;
            $resCaseProj['success'] = 'No';
            $resCaseProj['dependerr'] = __('Dependant tasks are not closed.', true);
            if (!empty($data)) {
                $resCaseProj['success'] = 'depend';
                return json_encode($resCaseProj);
            } else {
                echo json_encode($resCaseProj);
                exit;
            }
        } else {
            $log_id = !empty(trim($this->data['log_id'])) ? trim($this->data['log_id']) : $log_id;
            $mode = $log_id > 0 ? "edit" : "add";
            $slashes = $log_id > 0 ? '"' : '';
            $this->Project->recursive = -1;
            $projid = $this->Project->find('first', array('conditions' => array('Project.uniq_id' => $logdata['project_id']), 'fields' => array('Project.id')));
            $project_id = $projid['Project']['id'];

            $users = $logdata['user_id'];
            $task_dates = $logdata['task_date'];
            $start_time = $logdata['start_time'];
            $end_time = $logdata['end_time'];
            $totalbreak = $logdata['totalbreak'];
            $totalduration = $logdata['totalduration'];
            $description = $this->Format->convert_ascii(trim($logdata['description']));
            $easycase = array();

            $task_details = $this->Easycase->find('first', array('conditions' => array('Easycase.id' => $task_id), 'fields' => array('Easycase.*')));
            $easycase_uniq_id = $task_details['Easycase']['uniq_id'];
            
            $caseuniqid = $this->Format->generateUniqNumber();
            $reply_type = isset($logdata['task_id']) ? 10 : 11;
            $easycase['Easycase']['uniq_id'] = $caseuniqid;
            $easycase['Easycase']['case_no'] = $task_details['Easycase']['case_no'];
            $easycase['Easycase']['case_count'] = 0;
            $easycase['Easycase']['project_id'] = $task_details['Easycase']['project_id'];
            $easycase['Easycase']['user_id'] = SES_ID;
            $easycase['Easycase']['updated_by'] = 0;
            $easycase['Easycase']['type_id'] = $task_details['Easycase']['type_id'];
            $easycase['Easycase']['priority'] = $task_details['Easycase']['priority'];
            $easycase['Easycase']['title'] = '';
            $easycase['Easycase']['message'] = $description;
            $easycase['Easycase']['hours'] = 0;
            $easycase['Easycase']['assign_to'] = $task_details['Easycase']['assign_to'];
            $easycase['Easycase']['istype'] = 2;
            $easycase['Easycase']['status'] = $task_details['Easycase']['status'];
            $easycase['Easycase']['legend'] = $task_details['Easycase']['legend'];
            $easycase['Easycase']['dt_created'] = GMT_DATETIME;
            $easycase['Easycase']['actual_dt_created'] = GMT_DATETIME;
            $easycase['Easycase']['reply_type'] = $reply_type;
          //  $this->Easycase->query("INSERT INTO easycases SET uniq_id='" . $caseuniqid . "', case_no = '" . $task_details['Easycase']['case_no'] . "', 	case_count=0, project_id='" . $task_details['Easycase']['project_id'] . "', user_id='" . SES_ID . "', updated_by=0, type_id='" . $task_details['Easycase']['type_id'] . "', priority='" . $task_details['Easycase']['priority'] . "', title='', message='" . $description . "', hours='0', assign_to='" . $task_details['Easycase']['assign_to'] . "', istype='2',format='2', status='" . $task_details['Easycase']['status'] . "', legend='" . $task_details['Easycase']['legend'] . "', isactive=1, dt_created='" . GMT_DATETIME . "',actual_dt_created='" . GMT_DATETIME . "',reply_type=" . $reply_type . "");
            $this->Easycase->save($easycase);
            $task_status = 0;
            $cntr = count($logdata['totalduration']);
            $chkids = isset($data) && !empty($data) ? $logdata['chked_ids'] : @array_flip(explode(",", rtrim($logdata['chked_ids'], ",")));
            if (defined('GINV') && GINV == 1) {
                $chkautos = isset($data) && !empty($data) ? $logdata['chked_autos'] : @array_flip(explode(",", rtrim($logdata['chked_autos'], ",")));
            }
            $LogTime = array();
            for ($i = 0; $i < $cntr; $i++) {
                $task_date = date('Y-m-d', strtotime($task_dates[$i]));
                if ($mode != 'edit') {
                    $LogTime[$i]['project_id'] = $project_id;
                    $LogTime[$i]['task_id'] = $task_id;
                    if ($users[$i] != '') {
                        $LogTime[$i]['user_id'] = $users[$i];
                    }
                    $LogTime[$i]['task_status'] = $task_status;
                    $LogTime[$i]['ip'] = $_SERVER['REMOTE_ADDR'];
                }

                /* start time set start */
                $spdts = explode(':', $start_time[$i]);
                #converted to min
                if (strpos($start_time[$i], 'am') === false) {
                    $nwdtshr = ($spdts[0] != 12) ? ($spdts[0] + 12) : $spdts[0];
                    $dt_start = strstr($nwdtshr . ":" . $spdts[1], 'pm', true) . ":00";
                } else {
                    $nwdtshr = ($spdts[0] != 12) ? ($spdts[0] ) : '00';
                    $dt_start = strstr($nwdtshr . ":" . $spdts[1], 'am', true) . ":00";
                }
                $minute_start = ($nwdtshr * 60) + $spdts[1];
                /* start time set end */

                /* end time set start */
                $spdte = explode(':', $end_time[$i]);
                #converted to min
                if (strpos($end_time[$i], 'am') === false) {
                    $nwdtehr = ($spdte[0] != 12) ? ($spdte[0] + 12) : $spdte[0];
                    $dt_end = strstr($nwdtehr . ":" . $spdte[1], 'pm', true) . ":00";
                } else {
                    $nwdtehr = ($spdte[0] != 12) ? ($spdte[0]) : '00';
                    $dt_end = strstr($nwdtehr . ":" . $spdte[1], 'am', true) . ":00";
                }
                $minute_end = ($nwdtehr * 60) + $spdte[1];
                /* end time set end */

                /* checking if start is greater than end then add 24 hr in end i.e. 1440 min */
                $duration = $minute_end >= $minute_start ? ($minute_end - $minute_start) : (($minute_end + 1440) - $minute_start);
                $task_end_date = $minute_end >= $minute_start ? $task_date : date('Y-m-d', strtotime($task_date . ' +1 day'));

                /* total working */
                $break_time = trim($totalbreak[$i]);
                if (strpos($break_time, '.')) {
                    $split_break = ($break_time * 60);
                    $break_hour = (intval($split_break / 60) < 10 ? "0" : "") . intval($split_break / 60);
                    $break_min = (intval($split_break % 60) < 10 ? "0" : "") . intval($split_break % 60);
                    $break_time = $break_hour . ":" . $break_min;
                    $minute_break = $split_break;
                } elseif (strpos($break_time, ':')) {
                    $split_break = explode(':', $break_time);
                    #converted to min
                    $minute_break = ($split_break[0] * 60) + $split_break[1];
                } else {
                    $break_time = $break_time . ":00";
                    $minute_break = $break_time;
                }
                $minute_break = $duration < $minute_break ? 0 : $minute_break;
                /* break ends */

                /* total hrs start */
                $total_duration = $duration - $minute_break;
                $total_hours = $total_duration * 60;
                /* total hrs end */

                $LogTime[$i]['task_date'] = $slashes . $task_date . $slashes;
                $LogTime[$i]['start_time'] = $slashes . $dt_start . $slashes;
                $LogTime[$i]['end_time'] = $slashes . $dt_end . $slashes;

                /* here we are convering time to UTC as the date has been selected by user to in local time */
                #converted to UTC
                $this->Tmzone = $this->Components->load('Tmzone');
                $LogTime[$i]['start_datetime'] = $slashes . $this->Tmzone->convert_to_utc(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $task_date . " " . $dt_start, "datetime") . $slashes;
                $LogTime[$i]['end_datetime'] = $slashes . $this->Tmzone->convert_to_utc(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $task_end_date . " " . $dt_end, "datetime") . $slashes;

                #stored in sec
                $LogTime[$i]['break_time'] = $minute_break * 60;
                #stored in sec
                $LogTime[$i]['total_hours'] = $total_hours;
                $LogTime[$i]['is_billable'] = $logdata['is_billable'][$i];
                if (isset($data) && !empty($data)) {
                    $LogTime[$i]['is_billable'] = isset($logdata['is_billable'][$i]) && !empty($logdata['is_billable'][$i]) ? 1 : 0;
                    if (defined('GINV') && GINV) {
                        #$LogTime[$i]['auto_generate_invoice'] = $chkautos[$i];
                        $LogTime[$i]['auto_generate_invoice'] = isset($chkautos[$i]) ? 1 : 0;
                    }
                } else {
                    $LogTime[$i]['is_billable'] = isset($chkids[$i]) ? 1 : 0;
                    if (defined('GINV') && GINV) {
                        $LogTime[$i]['auto_generate_invoice'] = isset($chkautos[$i]) ? 1 : 0;
                    }
                }
                $LogTime[$i]['description'] = $slashes . addslashes(trim($logdata['description'])) . $slashes;
            }
            $valid = $this->validate_time_log($logdata, $project_id);
            if (is_array($valid) && $valid['success'] == 'No') {
                if (isset($data) && !empty($data)) {
                    return json_encode($valid);
                } else {
                    echo json_encode($valid);
                    exit;
                }
            }

            if ($log_id > 0) {
                $this->LogTime->updateAll($LogTime[0], array('LogTime.log_id' => $log_id));
            } else {
                $this->LogTime->saveAll($LogTime);
            }
            /* $log = $this->LogTime->getDataSource()->showLog(false);debug($log); */

            /* update easycases task dt_created */
            if (intval($task_id) > 0) {
                $this->Easycase->id = $task_id;
                $this->Easycase->save(array('dt_created' => date('Y-m-d H:i:s')));
            }

            /* update last project user visited */
            $this->loadModel('ProjectUser');
            $this->ProjectUser->recursive = -1;
            $this->ProjectUser->updateAll(array('ProjectUser.dt_visited' => "'" . GMT_DATETIME . "'"), array('ProjectUser.project_id' => $projid['Project']['id'], 'ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP));
            /* $ProjectUser['id'] = $projid['Project']['id'];
              $ProjectUser['dt_visited'] = GMT_DATETIME;
              $this->ProjectUser->save($ProjectUser); */
            if ($logdata['page_type'] == 'details') {
                echo json_encode(array('success' => true, 'task_id' => $easycase_uniq_id));
            } else {
                if (isset($data)) {
                    return json_encode(array('status' => 'success'));
                } else {
                    $this->redirect(HTTP_ROOT . 'easycases/timelog');
                }
            }
        }
        exit;
    }

    /* Author GKM
     *  this method is not used anymore
     */
    /* function timelog() {
      $prjid = $GLOBALS['getallproj'][0]['Project']['id'];
      $prjuniqueid = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
      $usr_id = "";
      $usid = "";
      $stdt = "";
      $st_dt = "";
      if ($this->data['projuniqid'] || $this->data['usrid'] || $this->data['strddt'] || $this->data['enddt']) {
      $this->layout = 'ajax';
      if ($prjuniqueid != $this->data['projuniqid']) {
      $this->loadModel('Project');
      $projid = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.uniq_id' => $this->data['projuniqid'])));
      $prjid = $projid['Project']['id'];
      }
      } else {
      $prjusr = $GLOBALS['projUser'][$prjuniqueid];
      foreach ($prjusr as $p => $k) {
      $rsrclist[$k['User']['id']] = $k['User']['name'] . " " . $k['User']['last_name'];
      }
      $this->set('rsrclist', $rsrclist);
      }
      if ($this->data['usrid']) {
      $usrid = $this->data['usrid'];
      $usr_id = " AND `LogTime`.`user_id` = $usrid";
      $usid = " AND user_id = '" . $usrid . "'";
      }
      if ($this->data['strddt'] && $this->data['enddt']) {
      $stdt = " AND `LogTime`.`task_date` BETWEEN '" . date('Y-m-d', strtotime($this->data['strddt'])) . "' AND '" . date('Y-m-d', strtotime($this->data['enddt'])) . "'";
      $st_dt = " AND task_date BETWEEN '" . date('Y-m-d', strtotime($this->data['strddt'])) . "' AND '" . date('Y-m-d', strtotime($this->data['enddt'])) . "'";
      }
      if ($this->data['projuniqid'] && !(isset($this->data['usrid']) && isset($this->data['strddt']) && isset($this->data['enddt']))) {
      $this->layout = 'ajax';
      if ($prjuniqueid != $this->data['projuniqid']) {
      $this->loadModel('Project');
      $projid = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.uniq_id' => $this->data['projuniqid'])));
      $prjid = $projid['Project']['id'];
      }
      $this->loadModel('ProjectUser');
      $this->ProjectUser->recursive = -1;
      $this->ProjectUser->updateAll(array('ProjectUser.dt_visited' => "'" . GMT_DATETIME . "'"), array('ProjectUser.project_id' => $prjid, 'ProjectUser.user_id' => $_SESSION['Auth']['User']['id']));
      }
      // print $usr_id;
      $this->loadModel('LogTime');
      $logtimes = $this->LogTime->find('all', array('conditions' => array("LogTime.project_id = $prjid" . $usr_id . $stdt), 'order' => 'created DESC'));
      $this->set('logtimes', $logtimes);
      $cntlog = $this->LogTime->query('SELECT sum(total_hours) as secds,is_billable FROM log_times WHERE is_billable = 1 and project_id = "' . $prjid . '" ' . $usid . $st_dt . ' GROUP BY project_id  UNION SELECT sum(total_hours) as secds, is_billable FROM log_times WHERE is_billable = 0 and project_id ="' . $prjid . '" ' . $usid . $st_dt . ' GROUP BY project_id ');

      $thoursbillable = floor($cntlog[0][0]['secds'] / 3600) . " hrs " . round((($cntlog[0][0]['secds'] % 3600) / 60), 2) . "mins";
      $thours = ($cntlog[0][0]['secds'] + $cntlog[1][0]['secds']);
      $thrs = floor($thours / 3600) . " hrs " . round((($thours % 3600) / 60), 2) . " mins";
      $this->set('thoursbillable', $thoursbillable);
      $this->set('thrs', $thrs);

      $this->loadModel('Easycase');
      $cntestmhrs = $this->Easycase->query('SELECT sum(estimated_hours) as hrs FROM easycases WHERE project_id = "' . $prjid . '"');
      $this->set('cntestmhrs', $cntestmhrs[0][0]['hrs']);
      if ($this->data['projuniqid'] && $this->data['usrid']) {
      echo $this->render('/Elements/timelog');
      exit;
      } else if ($this->data['strddt'] && $this->data['enddt']) {
      echo $this->render('/Elements/timelog');
      exit;
      } else if ($this->data['projuniqid']) {
      $projUser = $this->Easycase->getMemebers($this->data['projuniqid']);
      $this->set('resCaseProj', $projUser);
      echo $this->render('/Elements/timelog');
      exit;
      }
      } */

    function existing_task() {
        $this->layout = 'ajax';

        $this->loadModel('Project');
        $page = $this->request->data['page'] ? $this->request->data['page'] : '';
        $this->Project->recursive = -1;
        $projid = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.uniq_id' => $this->data['projuniqid'])));

        $this->loadModel('Easycase');
        $tsktitles = $this->Easycase->find('list', array('fields' => array("Easycase.id", "Easycase.title"), 'conditions' => array('Easycase.project_id' => $projid['Project']['id'], 'Easycase.title != ""', 'Easycase.isactive=1', 'istype=1')));

        $this->set('tsklist', $tsktitles);
        $page != '' ? $this->set('page', $page) : '';
    }

    function deleteInvoiceTimeLog() {
        $id = $this->request['data']['v'];
        $this->loadModel('InvoiceLog');
        $this->InvoiceLog->id = $id;
        $log_id = $this->InvoiceLog->field('log_id');

        if ($log_id > 0) {
            $this->loadModel('LogTime');
            $this->LogTime->query('update log_times set task_status=0 where log_id=' . $log_id);
        }

        if ($this->InvoiceLog->delete($id))
            echo 1;
        else
            echo 0;
        exit;
    }

    function ajaxTimeList() {
        //$prjid = $GLOBALS['getallproj'][0]['Project']['id'];
        $this->layout = 'ajax';
        $this->loadModel('ProjectUser');
        $projid = $this->ProjectUser->find('first', array('conditions' => array('ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('ProjectUser.project_id'), 'order' => 'dt_visited DESC'));
        $prjid = $projid['ProjectUser']['project_id'];
        /* Start Andola Pageination */
        $page_limit = CASE_PAGE_LIMIT;
        $page = 1;
        if (isset($_GET['page']) && $_GET['page']) {
            $page = $_GET['page'];
        }
        $limit1 = $page * $page_limit - $page_limit;
        $limit2 = $page_limit;
        $this->loadModel('LogTime');
        $logtimes = $this->LogTime->find('all', array('conditions' => array('LogTime.project_id' => $prjid, 'LogTime.is_billable' => 1, 'LogTime.task_status' => 0), 'order' => 'created DESC', 'limit' => $limit2, 'offset' => $limit1));
        $tot = $this->LogTime->find('count', array('conditions' => array('LogTime.project_id' => $prjid, 'LogTime.is_billable' => 1, 'LogTime.task_status' => 0), 'order' => 'created DESC'));
        $this->set('caseCount', $tot);
        $this->set('page_limit', $page_limit);
        $this->set('page', $page);
        $this->set('casePage', $page);
        $this->set('logtimes', $logtimes);
        /* End Andola Pageination */

        $cntlog = $this->LogTime->query('SELECT sum(total_hours) as secds,is_billable FROM log_times WHERE is_billable = 1 and project_id = "' . $prjid . '" GROUP BY project_id  UNION SELECT sum(total_hours) as secds, is_billable FROM log_times WHERE is_billable = 0 and project_id ="' . $prjid . '" GROUP BY project_id ');

        $thoursbillable = floor($cntlog[0][0]['secds'] / 3600) . " hours " . (($cntlog[0][0]['secds'] % 3600) / 60) . "minutes";
        $thours = ($cntlog[0][0]['secds'] + $cntlog[1][0]['secds']);
        $thrs = floor($thours / 3600) . " hours " . (($thours % 3600) / 60) . " minutes";
        $this->set('thoursbillable', $thoursbillable);
        $this->set('thrs', $thrs);
        $this->loadModel('Easycase');
        $cntestmhrs = $this->Easycase->query('SELECT sum(estimated_hours) as hrs FROM easycases WHERE project_id = "' . $prjid . '"');
        $this->set('cntestmhrs', $cntestmhrs[0][0]['hrs']);

        $this->loadModel('Invoice');
        $invoice = $this->Invoice->find('list', array('fields' => array('Invoice.id', 'Invoice.invoice_no'), 'recursive' => 0));
        $this->set('invoice', $invoice);

        $invoicecount = $this->Invoice->find('count', array('conditions' => array('Invoice.user_id' => SES_ID)));
        $this->set('invoiceCount', $invoicecount);
    }

    /* Girish: for timelog paging */

    function time_log() {
        if (isset($this->request->data['page']) && $this->request->data['page'] == 'log') {
            $this->layout = '';
        }
        $this->loadModel('Easycase');
        $this->loadModel('LogTime');
        $this->loadModel('Project');
        $project_id = array();
        $projFil = $this->request->data['projFil'];
        if ($projFil == "0") {
            $projFil = "all";
        }
        if ($_COOKIE['All_Project'] && ($_COOKIE['All_Project'] == 'all')) {
            $projFil = "all";
        } else {
            $project_id[] = $GLOBALS['getallproj'][0]['Project']['id'];
            $prjuniqueid = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
        }

        /* $project_id = $GLOBALS['getallproj'][0]['Project']['id'];
          $prjuniqueid = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
          $projFil = $this->request->data['projFil']; */
        $usid = '';
        $st_dt = '';
        $where = '';
        $filter_text = "";

        if ($this->request->data['projFil'] && !(isset($this->request->data['usrid']) && isset($this->request->data['strddt']) && isset($this->request->data['enddt']))) {
            if ($prjuniqueid != $projFil && $projFil != "all") {
                $this->loadModel('Project');
                $projid = $this->Project->find('first', array('fields' => array('Project.id'), 'conditions' => array('Project.uniq_id' => $projFil)));
                $prjid = $projid['Project']['id'];
            }
            #print($prjid);exit;
            $this->loadModel('ProjectUser');
            $this->ProjectUser->recursive = -1;
            $this->ProjectUser->updateAll(array('ProjectUser.dt_visited' => "'" . GMT_DATETIME . "'"), array('ProjectUser.project_id' => $prjid, 'ProjectUser.user_id' => $_SESSION['Auth']['User']['id']));
        }

        $page_limit = CASE_PAGE_LIMIT;
        #$page_limit = 10;
        if ($this->request->data['casePage']) {
            $casePage = $this->request->data['casePage']; // Pagination
        } else {
            $casePage = 1;
        }
        $pageprev = 1;
        $limit1 = $casePage * $page_limit - $page_limit;
        $limit2 = $page_limit;

        /* project details */
        if (isset($projFil) && !empty($projFil)) {
            $this->Project->recursive = -1;
            if ($projFil != 'all') {
                $projArr = $this->Project->find('first', array('conditions' => array('Project.uniq_id' => $projFil, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.id')));
                unset($project_id);
                $project_id[] = $projArr['Project']['id'];
            } else {
                $projArr = $this->Project->find('all', array('conditions' => array('Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.id')));
                foreach ($projArr as $k => $pro) {
                    $project_id[] = $pro['Project']['id'];
                }
            }
        }
		 
        if ($this->request->data['strddt']) {
            $startdate = $this->request->data['strddt'];
        } else {
            $startdate = $_COOKIE['logstrtdt'];
        }
        if ($this->request->data['enddt']) {
            $enddate = $this->request->data['enddt'];
        } else {
            $enddate = $_COOKIE['logenddt'];
        }
        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $curDateTime = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
        $dateFilter = isset($this->request->data['datelog']) && $this->request->data['datelog'] != '' ? $this->request->data['datelog'] : $_COOKIE['datelog'];
        if (strpos($dateFilter, ':')) {
            $dt = explode(':', $dateFilter);
            $date['strddt'] = $dt[0];
            $date['enddt'] = $dt[1];
            $startdate = $dt[0];
            $enddate = $dt[1];
       
        } else {
            $date = $this->Format->date_filter($dateFilter, $curDateTime);
            $startdate = $date['strddt'];
            $enddate = $date['enddt'];

        }
        if ($this->request->data['usrid']) {
            $usrid = $this->request->data['usrid'];
            $user_name = $this->request->data['user_name'];
            setcookie("user_name", $user_name, time() + 3600 * 24 * 365);
        } else {
            $usrid = trim($_COOKIE['rsrclog'],'-');
            if (strpos($usrid, '-')) {
                $usrid = explode('-', $usrid);
            }
            $user_name = $_COOKIE["user_name"];
        }
        if ($this->request->data['date']) {
            $startdate = $this->request->data['date'];
            $enddate = $this->request->data['date'];
            $date['strddt'] = $this->request->data['date'];
            $date['enddt'] = $this->request->data['date'];
        }

        if ($date['strddt'] && $date['enddt']) {
            $where .= " AND `LogTime`.`task_date` BETWEEN '" . date('Y-m-d', strtotime($date['strddt'])) . "' AND '" . date('Y-m-d', strtotime($date['enddt'])) . "'";
            $st_dt = " AND task_date BETWEEN '" . date('Y-m-d', strtotime($date['strddt'])) . "' AND '" . date('Y-m-d', strtotime($date['enddt'])) . "'";
            if ($date['strddt'] != $date['enddt']) {
                $filter_text .= __("from", true) . " " . date('d M, Y', strtotime($date['strddt'])) . " " . __("to", true) . " " . date('d M, Y', strtotime($date['enddt']));
            } else {
                $filter_text .= "for " . $date['strddt'];
            }
        } elseif ($date['strddt']) {
            $where .= " AND `LogTime`.`task_date` >= '" . date('Y-m-d', strtotime($date['strddt'])) . "'";
            $st_dt = " AND task_date >= '" . date('Y-m-d', strtotime($date['strddt'])) . "'";
            $filter_text .= __("from", true) . " " . date('d M, Y', strtotime($date['strddt']));
        } elseif ($date['enddt']) {
            $where .= " AND `LogTime`.`task_date` <= '" . date('Y-m-d', strtotime($date['enddt'])) . "'";
            $st_dt = " AND task_date <= '" . date('Y-m-d', strtotime($date['enddt'])) . "'";
            $filter_text .= __("up to", true) . " " . date('d M, Y', strtotime($date['enddt']));
        }
        if ($usrid) {
            if (is_array($usrid)) {
                $usrin = rtrim(implode(',', $usrid), ',');
                $where .= " AND `LogTime`.`user_id` IN (" . $usrin . ") ";
                $usid = " AND user_id = '" . $usrid . "'";
                $count_usid = " AND LogTime.user_id IN (" . $usrin . ") ";
            } else {
                $where .= " AND LogTime.user_id = '" . $usrid . "'";
                $usid = " AND user_id = '" . $usrid . "'";
                $count_usid = " AND LogTime.user_id = '" . $usrid . "'";
            }
            $filter_text .= " " . __("of user", true) . " ";
            if (strstr($usrid, "-")) {
                $expst4 = explode("-", $usrid);
                $cbymems = $this->Format->caseMemsList(array_unique($expst4));
                foreach ($cbymems as $key => $st4) {
                    $filter_text .= $st4 . " and";
                }
                $filter_text = rtrim($filter_text, ' and');
            } else if(is_array($usrid)){ 
                if(count(array_unique($usrid)) > 1 ){
                    $cbymems = $this->Format->caseMemsList(array_unique($usrid));
                    foreach ($cbymems as $key => $st4) {
                        $filter_text .= $st4 . " and ";
                    }
                    $filter_text = rtrim($filter_text, ' and');
                } else{
                    $filter_text .= $this->Format->caseMemsList($usrid[0]);
                }
            } else {
                $filter_text .= $this->Format->caseMemsList($usrid);
            }
        }
        if (SES_TYPE == 3) {
            $where .= " AND `LogTime`.`user_id`=" . SES_ID;
        }
        //for users
        if ($this->request->data['csid']) {
            $where .=" AND `LogTime`.`task_id` =" . $this->request->data['csid'];
            $filter_text .= " " . __("for task", true) . " '" . $this->request->data['cstitle'] . "'";
        }
        $curCaseId = "0";
        $extra_condition = "";
        $sort_cond = " ORDER BY `LogTime`.`created` DESC";
        $reset = 0;
        if (isset($this->request->data['type']) && $this->request->data['type'] == 'Date' || $_COOKIE['timelogsort'] == 'Date') {
            $sort_cond = " ORDER BY `task_date`";
            $reset = 1;
            $filter_text .= "order by date";
        } else if (isset($this->request->data['type']) && $this->request->data['type'] == 'Name' || $_COOKIE['timelogsort'] == 'Name') {
            $sort_cond = " ORDER BY `user_id`";
            $reset = 1;
            $filter_text .= "order by Name";
        } else if (isset($this->request->data['type']) && $this->request->data['type'] == 'Task' || $_COOKIE['timelogsort'] == 'Task') {
            $sort_cond = " ORDER BY `task_id`";
            $reset = 1;
            $filter_text .= "order by Task";
        } else if (isset($this->request->data['type']) && $this->request->data['type'] == 'Project' || $_COOKIE['timelogsort'] == 'Project') {
            $sort_cond = " ORDER BY `project_id`";
            $reset = 1;
            $filter_text .= "order by Project";
        }
        if ($_COOKIE['datelog'] || $_COOKIE['rsrclog']) {
            $reset = 1;
        }
        if (isset($this->request->data['sort']) && $this->request->data['sort'] == 'ASC') {
            $sort_cond = $sort_cond . ' ASC';
        } else if ($this->request->data['sort'] == 'DESC') {
            $sort_cond = $sort_cond . ' DESC';
        }
        $usrCndn = '';
        if (SES_TYPE == 3) {
            $usrCndn = ' AND LogTime.user_id=' . SES_ID;
        }
        $logsql = "SELECT SQL_CALC_FOUND_ROWS LogTime.*,Project.name AS project_name,"
                . " DATE_FORMAT(LogTime.start_datetime,'%M %d %Y %H:%i:%s') AS start_datetime_v1,"
                . "(SELECT CONCAT_WS(' ',User.name,User.last_name) FROM users AS `User` WHERE `User`.id=LogTime.user_id) AS user_name, "
                . "(SELECT CONCAT_WS('||',title,uniq_id) FROM easycases AS `Easycase` WHERE `Easycase`.id=LogTime.task_id LIMIT 1) AS task_name "
                . "FROM `log_times` AS `LogTime` "
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id LEFT JOIN projects AS Project ON  LogTime.project_id=Project.id "
                . "WHERE LogTime.project_id IN (" . implode(',', $project_id) . ") AND Easycase.isactive=1 " . $where . " " . $sort_cond . " LIMIT $limit1, $limit2";
        #echo $logsql;exit;
        $logtimes = $this->LogTime->query($logsql);
        #pr($logtimes);exit;
        if (is_array($logtimes) && count($logtimes) > 0) {
            foreach ($logtimes as $key => $val) {#May 05 2015 11:05:00
                $logtimes[$key]["LogTime"]['start_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[$key]["LogTime"]['start_datetime'], "datetime");
                $logtimes[$key]["LogTime"]['end_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[$key]["LogTime"]['end_datetime'], "datetime");
                $logtimes[$key][0]['start_datetime_v1'] = date('M d Y H:i:s', strtotime($logtimes[$key]["LogTime"]['start_datetime']));

                $logtimes[$key]['LogTime']['start_time'] = date('H:i:s', strtotime($logtimes[$key]['LogTime']['start_datetime']));
                $logtimes[$key]['LogTime']['end_time'] = date('H:i:s', strtotime($logtimes[$key]['LogTime']['end_datetime']));
            }
        }
        $tot = $this->LogTime->query("SELECT FOUND_ROWS() as total");
        $caseCount = $tot[0][0]['total'];
        $count_sql = 'SELECT SUM(hrs.secds) as seconds,hrs.is_billable from (SELECT sum(total_hours) as secds,is_billable '
                . 'FROM log_times AS `LogTime` '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 1 AND Easycase.isactive =1 AND LogTime.project_id IN (' . implode(",", $project_id) . ') ' . $usrCndn . ' ' . $count_usid . $st_dt . ' '
                . 'GROUP BY LogTime.project_id  '
                . 'UNION '
                . 'SELECT sum(total_hours) as secds, is_billable '
                . 'FROM log_times AS LogTime '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 0 AND Easycase.isactive =1 AND LogTime.project_id IN (' . implode(",", $project_id) . ') ' . $usrCndn . ' ' . $count_usid . $st_dt . ' '
                . 'GROUP BY LogTime.project_id ) as hrs GROUP BY hrs.is_billable ';
        $cntlog = $this->LogTime->query($count_sql);
        if ($cntlog[0]['hrs']['is_billable'] == 1) {
            $billablehours = $cntlog[0][0]['seconds'];
        }
        if (isset($cntlog[1]['hrs']['is_billable']) && $cntlog[1]['hrs']['is_billable'] == 1) {
            $billablehours = $cntlog[1][0]['seconds'];
        }

        $thoursbillable = ($billablehours);
        $thours = ($cntlog[0][0]['seconds'] + ((isset($cntlog[1][0]['seconds']) && !empty($cntlog[1][0]['seconds'])) ? $cntlog[1][0]['seconds'] : 0 ));
        $thrs = ($thours);
        $nonBillableHrs = ($thours - $billablehours);
        $tasks = (trim($usid) != '' || trim($st_dt) != '') ? ' AND id IN (SELECT task_id FROM log_times WHERE project_id IN(' . implode(",", $project_id) . ')' . $usrCndn . ' ' . $usid . $st_dt . ')' : '';
        $estsql = "SELECT SUM(estimated_hours) AS hrs FROM easycases WHERE isactive=1 AND project_id IN(" . implode(',', $project_id) . ") AND istype=1 " . $tasks;
        $cntestmhrs = $this->Easycase->query($estsql);
        $caseTitleRep = '';
        $pgShLbl = '';
        $logtimesArr = array('logs' => $logtimes,
            'task_id' => $curCaseId,
            'task_title' => $caseTitleRep,
            'pgShLbl' => $pgShLbl,
            'csPage' => $casePage,
            'page_limit' => $page_limit,
            'caseCount' => $caseCount,
            'showTitle' => 'Yes',
            'page' => 'timelog',
            'calltype' => $this->request->data['page'],
            'check_sort' => $this->request->data['sort'],
            'reset' => $reset,
            'details' => array(
                'totalHrs' => $thrs,
                'billableHrs' => $thoursbillable,
                'nonbillableHrs' => $nonBillableHrs,
                'estimatedHrs' => trim($cntestmhrs[0][0]['hrs']),
        ));
        $projUser = array();
        if ($projFil) {
            $projUser = $this->Easycase->getMemebers($projFil);
        } else {
            $projUser = $this->Easycase->getMemebers($prjuniqueid);
        }
        #pr($projUser);exit;
        $caseDetail['projUser'] = $projUser;

        //pr($logtimesArr);exit;
        $caseDetail['logtimes'] = $logtimesArr;
        #pr($caseDetail);exit;
        //echo json_encode($caseDetail);
        //exit;
        $this->set("filter_text", $filter_text);
        $this->set('resCaseProj', $projUser);
        $this->set('startdate', $startdate);
        $this->set('enddate', $enddate);
        $this->set('usrid', array_unique($usrid));
        $this->set('prjctId', $projFil);
        $logtimesArr['details']['billableHrs'] = $this->Format->format_time_hr_min($logtimesArr['details']['billableHrs']);
        $logtimesArr['details']['nonbillableHrs'] = $this->Format->format_time_hr_min($logtimesArr['details']['nonbillableHrs']);
        $logtimesArr['details']['totalHrs'] = $this->Format->format_time_hr_min($logtimesArr['details']['totalHrs']);
        $logtimesArr['details']['estimatedHrs'] = $this->Format->format_time_hr_min($logtimesArr['details']['estimatedHrs']);
        $logtimesArr['details']['break_time'] = $this->Format->format_time_hr_min($logtimesArr['details']['break_time']);
        $this->set('logtimesArr', $logtimesArr);
    }

    /* Author: GKM 
      use: to fetch timelog details and populate in edit mode
     */

    function timelog_details() {
        if (isset($this->data['logid'])) {
            $this->loadModel('LogTime');
            $log_id = intval($this->data['logid']);
            $logtimes = $this->LogTime->find('all', array('fields' => array("LogTime.*,LogTime.log_id as id" . ", DATE_FORMAT(LogTime.start_datetime,'%M %d %Y %H:%i:%s') AS start_datetime_v1"), 'conditions' => array('LogTime.log_id' => $log_id)));
            #$logtimes[0]['LogTime']['start_datetime_v1']=$logtimes[0][0]['start_datetime_v1'];
            $logtimes[0]['LogTime']['srt_datetime_v1'] = $logtimes[0]['LogTime']['start_datetime'];
            $logtimes[0]['LogTime']['end_datetime_v1'] = $logtimes[0]['LogTime']['end_datetime'];
            $logtimes[0]['LogTime']['start_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[0]['LogTime']['start_datetime'], "datetime");
            $logtimes[0]['LogTime']['end_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[0]['LogTime']['end_datetime'], "datetime");

            $logtimes[0]['LogTime']['start_datetime_v1'] = date('M d Y H:i:s', strtotime($logtimes[0]['LogTime']['start_datetime']));
            $logtimes[0]['LogTime']['start_time'] = date('H:i:s', strtotime($logtimes[0]['LogTime']['start_datetime']));
            $logtimes[0]['LogTime']['end_time'] = date('H:i:s', strtotime($logtimes[0]['LogTime']['end_datetime']));
            //pr($logtimes);exit;
            unset($logtimes[0]['LogTime']['ip']);
            unset($logtimes[0]['LogTime']['project_id']);

            echo json_encode($logtimes[0]['LogTime']);
        }
        exit;
    }

    /* Author: GKM 
      use: to delete timelog details
     */

    function delete_timelog($logid = Null) {
        $log_id = !empty($this->data['logid']) ? intval($this->data['logid']) : $logid;
        if (!empty($log_id)) {
            $this->loadModel('Timelog.LogTime');
            $this->loadModel('Easycase');
            /* $this->LogTime->log_id = $log_id;
              $res = $this->LogTime->delete(); */
            $log_time = $this->LogTime->find('first', array('conditions' => array("LogTime.log_id" => $log_id)));
            $task_details = $this->Easycase->find('first', array('conditions' => array('Easycase.id' => $log_time['LogTime']['task_id'])));
            $res = $this->LogTime->query("DELETE FROM log_times WHERE log_id='" . $log_id . "'");
            #$retArr = array('success' => ($res ? 1 : 0));
            $retArr = array('success' => 1);
        } else {
            $retArr = array('success' => 0);
        }
        if (!empty($logid)) {
            return $retArr;
        } else {
            echo json_encode($retArr);
        }
        exit;
    }

    /* Author: GKM
     * use: to check overlaping log times 
     */

    function validate_time_log($data, $project_id) {
        $this->loadModel('LogTime');
        $this->loadModel('Project');
        #pr($data);echo $project_id;exit;
        $return_val = true;
        if (!is_array($data) && trim($project_id) == '') {
            $data = $this->data;
            $this->Project->recursive = -1;
            $projid = $this->Project->find('first', array('conditions' => array('Project.uniq_id' => $data['project_id']), 'fields' => array('Project.id')));
            $project_id = $projid['Project']['id'];
            $return_val = false;
        }
        $log_id = trim($data['log_id']);
        $mode = $log_id > 0 ? "edit" : "add";
        if ($mode == 'edit') {
            $logtimes = $this->LogTime->find('all', array('fields' => array("LogTime.*,LogTime.log_id as id"), 'conditions' => array('LogTime.log_id' => $log_id)));
            $task_id = $logtimes[0]['LogTime']['task_id'];
            $users[0] = $logtimes[0]['LogTime']['user_id'];
        } else {
            #$task_id = trim($data['task_id']);
            $task_id = isset($data['task_id']) ? trim($data['task_id']) : intval($data['hidden_task_id']);
            $users = $data['user_id'];
        }
        $task_dates = $data['task_date'];
        $start_time = $data['start_time'];
        $end_time = $data['end_time'];
        $totalbreak = $data['totalbreak'];
        $totalduration = $data['totalduration'];
        $cntr = count($data['totalduration']);
        $user_id_arr = array();

        /* formating logtime array */
        $LogTime = array();
        for ($i = 0; $i < $cntr; $i++) {
            $task_date = date('Y-m-d', strtotime($task_dates[$i]));
            $user_id_arr[] = $users[$i];
            $LogTime[$i]['user_id'] = $users[$i];
            $LogTime[$i]['project_id'] = $project_id;
            $LogTime[$i]['task_id'] = $task_id;

            /* start time set start */
            $spdts = explode(':', $start_time[$i]);
            #converted to min

            if (strpos($start_time[$i], 'am') === false) {
                $nwdtshr = ($spdts[0] != 12) ? ($spdts[0] + 12) : trim($spdts[0]);
                $dt_start = strstr($nwdtshr . ":" . $spdts[1], 'pm', true) . ":00";
            } else {
                $nwdtshr = ($spdts[0] != 12) ? ($spdts[0] < 10 ? "0" : "") . $spdts[0] : '00';
                $dt_start = strstr($nwdtshr . ":" . $spdts[1], 'am', true) . ":00";
            }
            $minute_start = ($nwdtshr * 60) + $spdts[1];
            /* start time set end */

            /* end time set start */
            $spdte = explode(':', $end_time[$i]);
            #converted to min

            if (strpos($end_time[$i], 'am') === false) {
                $nwdtehr = ($spdte[0] != 12) ? ($spdte[0] + 12) : $spdte[0];
                $dt_end = strstr($nwdtehr . ":" . $spdte[1], 'pm', true) . ":00";
            } else {
                $nwdtehr = ($spdte[0] != 12) ? ($spdte[0] < 10 ? "0" : "") . $spdte[0] : '00';
                $dt_end = strstr($nwdtehr . ":" . $spdte[1], 'am', true) . ":00";
            }
            $minute_end = ($nwdtehr * 60) + $spdte[1];
            /* end time set end */

            /* checking if start is greater than end then add 24 hr in end i.e. 1440 min */
            $duration = $minute_end >= $minute_start ? ($minute_end - $minute_start) : (($minute_end + 1440) - $minute_start);
            $task_end_date = $minute_end >= $minute_start ? $task_date : date('Y-m-d', strtotime($task_date . ' +1 day'));

            /* total working */
            $break_time = trim($totalbreak[$i]);
            if (strpos($break_time, '.')) {
                $split_break = ($break_time * 60);
                $break_hour = (intval($split_break / 60) < 10 ? "0" : "") . intval($split_break / 60);
                $break_min = (intval($split_break % 60) < 10 ? "0" : "") . intval($split_break % 60);
                $break_time = $break_hour . ":" . $break_min;
                $minute_break = $split_break;
            } elseif (strpos($break_time, ':')) {
                $split_break = explode(':', $break_time);
                #converted to min
                $minute_break = ($split_break[0] * 60) + $split_break[1];
            } else {
                $break_time = $break_time . ":00";
                $minute_break = $break_time * 60;
            }
            $minute_break = $duration < $minute_break ? 0 : $minute_break;
            /* break ends */

            /* total hrs start */
            $total_duration = $duration - $minute_break;
            /* $total_hrs = floor($total_duration / 60);
              $total_mins = (intval($total_duration % 60) < 10 ? "0" : "") . intval($total_duration % 60);
              $total_hours = $total_hrs . ":" . $total_mins; */
            $total_hours = $total_duration;
            /* total hrs end */

            $LogTime[$i]['task_date'] = $task_date;
            $LogTime[$i]['start_time'] = $dt_start;
            $LogTime[$i]['end_time'] = $dt_end;

            /* not converted to utc as we are validating with current times only */
            $LogTime[$i]['start_datetime'] = $task_date . " " . $dt_start;
            $LogTime[$i]['end_datetime'] = $task_end_date . " " . $dt_end;
            #converted to UTC
            #$LogTime[$i]['start_datetime'] = $this->Tmzone->convert_to_utc(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $task_date . " " . $dt_start, "datetime");
            #$LogTime[$i]['end_datetime'] = $this->Tmzone->convert_to_utc(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $task_end_date . " " . $dt_end, "datetime");
            #stored in sec
            $LogTime[$i]['break_time'] = $minute_break * 60;
            #stored in sec
            $LogTime[$i]['total_hours'] = $total_hours * 60;
            $log_final_arr[$LogTime[$i]['user_id']][] = $LogTime[$i];
        }
        $LogTime = $log_final_arr;

        /* fetching all old records of the task from log_times table */
        $user_ids = array_unique($user_id_arr);
        $condition = array("LogTime.user_id IN (" . (is_array($user_ids) && count($user_ids) > 0 ? implode(',', $user_ids) : "''") . ")", "LogTime.task_id" => $task_id);
        $existing_data = $this->LogTime->find('all', array('fields' => array("LogTime.*,LogTime.log_id as id"), 'conditions' => $condition));
        #$log = $this->LogTime->getDataSource()->showLog(false);debug($log);
        $existing_dates = array();
        $existing_logtime = array();
        if (is_array($existing_data) && count($existing_data) > 0) {
            foreach ($existing_data as $key => $val) {
                $existing_logtime[$val['LogTime']['user_id']][] = array(
                    'id' => $val['LogTime']['id'],
                    'user_id' => $val['LogTime']['user_id'],
                    'task_id' => $val['LogTime']['task_id'],
                    'task_date' => $val['LogTime']['task_date'],
                    'start_time' => $val['LogTime']['start_time'],
                    'end_time' => $val['LogTime']['end_time'],
                    'start_datetime' => $val['LogTime']['start_datetime'], #these from db, so are in UTC
                    'end_datetime' => $val['LogTime']['end_datetime'], #these from db, so are in UTC
                );
            }
        }
        $overrlap = false;
        $overlap_msg = array();
        if (is_array($LogTime) && count($LogTime) > 0) {
            /* loop of users */
            foreach ($LogTime as $userkey => $plog) {
                /* loop of user logs */
                foreach ($plog as $pKey => $pVal) {
                    /* compare with new time log data */
                    if (isset($LogTime[$userkey])) {
                        foreach ($LogTime[$userkey] as $cKey => $cVal) {
                            #pr($cVal);pr($pVal);
                            if ($pKey != $cKey) {
                                $start_datetime = $cVal['start_datetime']; #converted time in UTC
                                $end_datetime = $cVal['end_datetime']; #converted time in UTC
                                if (
                                        ($start_datetime < $pVal['start_datetime'] && $end_datetime > $pVal['start_datetime']) || ($start_datetime < $pVal['end_datetime'] && $end_datetime > $pVal['end_datetime']) || ($start_datetime == $pVal['start_datetime'] && $end_datetime == $pVal['end_datetime']) || ($start_datetime > $pVal['start_datetime'] && $end_datetime < $pVal['end_datetime']) || ($start_datetime > $pVal['start_datetime'] && $end_datetime == $pVal['end_datetime']) || ($start_datetime == $pVal['start_datetime'] && $end_datetime < $pVal['end_datetime'])
                                ) {
                                    $overrlap = true;
                                    $overlap_msg[$userkey][$pVal['start_datetime'] . '||' . $pVal['end_datetime']] = array(
                                        'user_id' => $userkey,
                                        'task_date' => date('M d, Y', strtotime($pVal['start_datetime'])),
                                        'start_time' => trim(date('h:ia', strtotime($pVal['start_datetime'])), '0'),
                                        'end_time' => trim(date('h:ia', strtotime($pVal['end_datetime'])), '0')
                                    );
                                }
                            }
                        }
                    }
                    /* end */
                    /* compare with db records */
                    if (isset($existing_logtime[$userkey])) {
                        foreach ($existing_logtime[$userkey] as $cKey => $cVal) {
                            #pr($cVal);pr($pVal);
                            #echo $log_id." != ".$cVal['id']."<br>";
                            if ($mode != 'edit' || ($mode == 'edit' && $log_id != $cVal['id'])) {
                                $start_datetime = trim($cVal['start_datetime']) > 0 ? $cVal['start_datetime'] : $cVal['task_date'] . " " . $cVal['start_time']; #this is from db, so is in UTC
                                $end_datetime = trim($cVal['end_datetime']) > 0 ? $cVal['end_datetime'] : $cVal['task_date'] . " " . $cVal['end_time']; #this is from db, so is in UTC
                                /* converting date time got from db to user's local time to check overlaping time */
                                $start_datetime = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $start_datetime, "datetime");
                                $end_datetime = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $end_datetime, "datetime");
                                #echo $start_datetime." < ".$pVal['start_datetime']." && ".$end_datetime." > ".$pVal['end_datetime']."<br>";
                                if (
                                        ($start_datetime < $pVal['start_datetime'] && $end_datetime > $pVal['start_datetime']) || ($start_datetime < $pVal['end_datetime'] && $end_datetime > $pVal['end_datetime']) || ($start_datetime == $pVal['start_datetime'] && $end_datetime == $pVal['end_datetime']) || ($start_datetime > $pVal['start_datetime'] && $end_datetime < $pVal['end_datetime']) || ($start_datetime > $pVal['start_datetime'] && $end_datetime == $pVal['end_datetime']) || ($start_datetime == $pVal['start_datetime'] && $end_datetime < $pVal['end_datetime'])
                                ) {
                                    $overrlap = true;
                                    $overlap_msg[$userkey][$pVal['start_datetime'] . '||' . $pVal['end_datetime']] = array(
                                        'log_id' => $cVal['id'],
                                        'user_id' => $userkey,
                                        'task_date' => date('M d, Y', strtotime($pVal['start_datetime'])),
                                        'start_time' => trim(date('h:ia', strtotime($pVal['start_datetime'])), '0'),
                                        'end_time' => trim(date('h:ia', strtotime($pVal['end_datetime'])), '0'),
                                        'db_task_date' => date('Y-m-d', strtotime($start_datetime)),
                                        'db_start_time' => trim(date('h:ia', strtotime($start_datetime)), '0'),
                                        'db_end_time' => trim(date('h:ia', strtotime($end_datetime)), '0')
                                    );
                                }
                            }
                        }
                    }
                    /* end */
                }
            }
        }
        if (is_array($overlap_msg) && count($overlap_msg) > 0) {
            foreach ($overlap_msg as $key => $val) {
                $overlap_msg[$key] = array_values($val);
            }
        }
        $ret_arr = array(
            'success' => $overrlap ? 'No' : 'Yes',
            'data' => $overlap_msg
        );
        #pr($overlap_msg);exit;
        #pr($existing_logtime);pr($LogTime);exit;
        if ($return_val) {
            return $ret_arr;
        } else {
            echo json_encode($ret_arr);
            exit;
        }
    }

    /* Author: GKM
     * it is used to prepare logtime data while add, edit and reply of tasks
     */

    function prepare_log_time_from_reply($arr, $task_details = array()) {
        $LogTime = array();
        $logdata = $arr['timelog'];

        #$task_date = date('Y-m-d',strtotime($logdata['taskdate']));
        #$task_date = date('Y-m-d');
        /* utc has been converted to users time zone */
        $task_date = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, date('Y-m-d H:i:s'), "date");

        $task_id = $arr['CS_id'] > 0 ? $arr['CS_id'] : ($arr['taskid'] > 0 ? $arr['taskid'] : intval($task_details['caseid']));
        $LogTime['task_id'] = $task_id;

        $LogTime['project_id'] = $arr['pid'];
        $LogTime['task_status'] = $arr['CS_legend'];

        $LogTime['user_id'][] = $arr['CS_assign_to'];
        $LogTime['task_date'][] = $task_date;
        $LogTime['start_time'][] = $logdata['start_time'];
        $LogTime['end_time'][] = $logdata['end_time'];

        $LogTime['totalbreak'][] = $logdata['break_time'];
        $LogTime['totalduration'][] = $logdata['hours'];

        $LogTime['is_billable'][] = isset($logdata['is_bilable']) && trim($logdata['is_bilable']) == 'Yes' ? 1 : 0;
        $LogTime['description'] = addslashes(trim($arr['CS_message']));

        return $LogTime;
    }

    /* author: GKM
     * to update time spent for project or task selected
     */

    function project_time_details() {
        $this->loadModel('Easycase');
        $this->loadModel('LogTime');
        $this->loadModel('Project');

        $prjid = $GLOBALS['getallproj'][0]['Project']['id'];
        $prjuniqueid = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
        $project_id = $this->data['proid'];
        $task_id = intval($this->data['tskid']) > '0' ? trim($this->data['tskid']) : '';
        $prjunid = $this->data['prjunid'];

        /* project details */
        $this->Project->recursive = -1;
        $projArr = $this->Project->find('first', array('conditions' => array('Project.uniq_id' => $prjunid, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.id')));
        $project_id = $projArr['Project']['id'];

        $task_condition = trim($task_id) != '' ? " AND task_id = $task_id" : "";
        $es_task_condition = trim($task_id) != '' ? " AND id = $task_id" : "";
        /* $cntlog = $this->LogTime->query('SELECT SUM(total_hours) as secds,is_billable FROM log_times WHERE is_billable = 1 and project_id = "' . $project_id . '" ' . $task_condition . ' GROUP BY project_id  '
          . 'UNION '
          . 'SELECT SUM(total_hours) as secds, is_billable FROM log_times WHERE is_billable = 0 and project_id ="' . $project_id . '" ' . $task_condition . ' GROUP BY project_id ');

          #pr($cntlog); */
        $count_sql = 'SELECT SUM(total_hours) as secds,is_billable '
                . 'FROM log_times AS `LogTime` '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 1 AND Easycase.isactive =1 AND LogTime.project_id = "' . $project_id . '" ' . $task_condition . ' '
                . 'GROUP BY LogTime.project_id  '
                . 'UNION '
                . 'SELECT sum(total_hours) as secds, is_billable '
                . 'FROM log_times AS LogTime '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 0 AND Easycase.isactive =1 AND LogTime.project_id ="' . $project_id . '" ' . $task_condition . ' '
                . 'GROUP BY LogTime.project_id ';
        #echo $count_sql;exit;
        $cntlog = $this->LogTime->query($count_sql);
        #print_r($cntlog);exit;
        $billable_hours = $cntlog[0][0]['is_billable'] > 0 ? $cntlog[0][0]['secds'] : 0;
        $nonbillable_hours = $cntlog[1][0]['is_billable'] == 0 ? $cntlog[1][0]['secds'] : 0;
        $total_spent = ($cntlog[0][0]['secds'] + $cntlog[1][0]['secds']);

        /* /estimated hours/ */
        $est_sql = "SELECT SUM(estimated_hours) AS hrs "
                . "FROM easycases AS Easycase "
                . "WHERE project_id = '" . $project_id . "' AND istype=1 AND Easycase.isactive=1 " . $es_task_condition;
        $estimated = $this->Easycase->query($est_sql);
        $total_estimated = $estimated[0][0]['hrs'];
        #pr($total_estimated);exit;
        echo json_encode(array('billable_hours' => $billable_hours, 'total_spent' => $total_spent, 'nonBillableHrs' => $nonbillable_hours, 'total_estimated' => $total_estimated));
        exit;
    }

    function export_csv_timelog() {
        $this->loadModel('Easycase');
        $this->loadModel('LogTime');
        $this->LogTime = ClassRegistry::init('LogTime');
        $this->loadModel('Project');
        $this->loadModel('User');
        $view = new View($this);
        $frmt = $view->loadHelper('Format');
        $data = $this->params->query;
        $from_date = trim($data['strddt']);
        $to_date = trim($data['enddt']);
        $user_id = (isset($data['usrid']) && !empty($data['usrid'])) ? trim($data['usrid']) : SES_ID;
        $prjid = $GLOBALS['getallproj'][0]['Project']['id'];
        $prjuniqueid = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
        $projFil = trim($data['projuniqid']);
        $usid = '';
        $st_dt = '';
        $where = '';
        $project_id = array();
        /* project details */
        if ($projFil != '') {
            $this->Project->recursive = -1;
            if ($projFil != 'all') {
                $params = array(
                    'conditions' => array('Project.uniq_id' => $projFil, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP),
                    'fields' => array('Project.id'));

                $projArr = $this->Project->find('first', $params);
                $project_id[] = $projArr['Project']['id'];
            } else {
                $params = array(
                    'conditions' => array('Project.isactive' => 1, 'Project.company_id' => SES_COMP),
                    'fields' => array('Project.id'));
                $projArr = $this->Project->find('all', $params);
                foreach ($projArr as $ky => $vp) {
                    $project_id[] = $vp['Project'][id];
                }
            }
        } else {
            $project_id = $prjid;
            $projFil = $prjuniqueid;
        }
       
        if (!empty($user_id) && is_numeric($user_id)) { 
            $usrid = $user_id;
            $where .= " AND `LogTime`.`user_id` = $usrid";
            $usid = " AND user_id = '" . $usrid . "'";
            $count_usid = " AND LogTime.user_id = '" . $usrid . "'";
        } else if(!empty($user_id) && !is_numeric($user_id)){
            $usrid = $user_id;
            $where .= " AND `LogTime`.`user_id` IN (".$usrid.")";
            $usid = " AND user_id IN( '" . $usrid . "')";
            $count_usid = " AND LogTime.user_id IN (" . $usrid . ")";
        }
        if ($from_date != '' && $to_date != '') {
            $where .= " AND DATE(`LogTime`.`start_datetime`) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
            $st_dt = " AND DATE(start_datetime) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
        } elseif ($from_date != '') {
            $where .= " AND DATE(`LogTime`.`start_datetime`) >= '" . date('Y-m-d', strtotime($from_date)) . "'";
            $st_dt = " AND DATE(start_datetime) >= '" . date('Y-m-d', strtotime($from_date)) . "'";
        } elseif ($to_date != '') {
            $where .= " AND DATE(`LogTime`.`start_datetime`) <= '" . date('Y-m-d', strtotime($to_date)) . "'";
            $st_dt = " AND DATE(start_datetime) <= '" . date('Y-m-d', strtotime($to_date)) . "'";
        }

        $this->loadModel('User');
        $options = array();
        $options['fields'] = array("LogTime.*", "DATE_FORMAT(LogTime.start_datetime,'%M %d %Y %H:%i:%s') AS start_datetime_v1",
            "(SELECT CONCAT_WS(' ',User.name,User.last_name) FROM users AS `User` WHERE `User`.id=LogTime.user_id) AS user_name",
            "(SELECT title FROM easycases AS `Easycase` WHERE Easycase.id=LogTime.task_id LIMIT 1) AS task_name");
        $options['joins'] = array(array('table' => 'easycases', 'alias' => 'Easycase', 'type' => 'LEFT', 'conditions' => array('Easycase.id=LogTime.task_id', 'LogTime.project_id=Easycase.project_id')));
        $options['conditions'] = array("LogTime.project_id" => $project_id, "Easycase.isactive" => 1, trim(trim($where), 'AND'));
        $options['order'] = 'created DESC';
        $logtimes = $this->LogTime->find('all', $options);
        $caseCount = $this->LogTime->find('count', $options);
        if (is_array($logtimes) && count($logtimes) > 0) {
            foreach ($logtimes as $key => $val) {
                $logtimes[$key]["LogTime"]['start_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[$key]["LogTime"]['start_datetime'], "datetime");
                $logtimes[$key]["LogTime"]['end_datetime'] = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $logtimes[$key]["LogTime"]['end_datetime'], "datetime");
                $logtimes[$key][0]['start_datetime_v1'] = date('M d Y H:i:s', strtotime($logtimes[$key]["LogTime"]['start_datetime']));

                $logtimes[$key]['LogTime']['start_time'] = date('H:i:s', strtotime($logtimes[$key]['LogTime']['start_datetime']));
                $logtimes[$key]['LogTime']['end_time'] = date('H:i:s', strtotime($logtimes[$key]['LogTime']['end_datetime']));
            }
        }
        // pr($logtimes);exit;
        $view = new View();
        $project = '';
        $frmt = $view->loadHelper('Format');
        if (count($project_id) > 1) {
            $project = 'Project,';
        }
        $content = __($project . 'Date,Name,Task,Note,Start,End,Break,Billable,Hours', true);
        $content .= "\n";
        $total_billable_hours = 0;
        $total_non_billable_hours = 0;
        if (is_array($logtimes) && count($logtimes) > 0) {
            foreach ($logtimes as $key => $val) {
                if (count($project_id) > 1) {
                    $project_name = $frmt->getprjctUnqid($val['LogTime']['project_id']);
                    $content .=trim($project_name['Project']['name']);
                }
                if (count($project_id) > 1) {
                    $content .="," . '"' . str_replace('"', '""', date('d/m/Y', strtotime($val[0]['start_datetime_v1']))) . '"';
                } else {
                    $content .= date('d/m/Y', strtotime($val[0]['start_datetime_v1']));
                }
                $content .="," . '"' . str_replace('"', '""', trim($val[0]['user_name'])) . '"';
                $content .="," . '"' . str_replace('"', '""', trim($val[0]['task_name'])) . '"';
                $content .="," . '"' . stripslashes(str_replace('"', '""', trim($val['LogTime']['description']))) . '"';
                $content .="," . '"' . $this->Format->format_24hr_to_12hr($val['LogTime']['start_time']) . '"';
                $content .="," . '"' . $this->Format->format_24hr_to_12hr($val['LogTime']['end_time']) . '"';
                $content .="," . '"' . $this->Format->format_time_hr_min($val['LogTime']['break_time']) . '"';
                $content .="," . '"' . ($val['LogTime']['is_billable'] == '1' ? 'Yes' : 'No') . '"';
                $content .="," . '"' . $this->Format->format_time_hr_min($val['LogTime']['total_hours']) . '"';
                $content .="\n";
                ($val['LogTime']['is_billable'] == '1' ? $total_billable_hours+= $val['LogTime']['total_hours'] : $total_non_billable_hours+= $val['LogTime']['total_hours']);
            }
        }
        $content .= "\n" . __("Export Date,", true) . $this->Format->dateFormatReverse(GMT_DATETIME);
        $content .= "\n" . __("Total,", true) . $caseCount . " records";
        $content .= "\n" . __("Total Billable Hours,", true) . $this->Format->format_time_hr_min($total_billable_hours) . " ";
        $content .= "\n" . __("Total Non-Billable Hours,", true) . $this->Format->format_time_hr_min($total_non_billable_hours) . " ";
        $content .= "\n" . __("Total Hours,", true) . $this->Format->format_time_hr_min($total_billable_hours + $total_non_billable_hours) . " ";
        if (!is_dir(LOGTIME_CSV_PATH)) {
            @mkdir(LOGTIME_CSV_PATH, 0777, true);
        }

        $name = $projFil;
        if (trim($name) != '' && strlen($name) > 25) {
            $name = substr($name, 0, 24) . "_" . date('m-d-Y', strtotime(GMT_DATE)) . "_timelog.csv";
        } else {
            $name .= (trim($name) != '' ? "_" : '') . date('m-d-Y', strtotime(GMT_DATE)) . "_timelog.csv";
        }
        $download_name = date('m-d-Y', strtotime(GMT_DATE)) . "_timelog.csv";

        $file_path = LOGTIME_CSV_PATH . $name;
        $fp = @fopen($file_path, 'w+');
        fwrite($fp, $content);
        fclose($fp);

        $this->response->file($file_path, array('download' => true, 'name' => urlencode($download_name)));
        return $this->response;
    }

    /* to Save Timer data as log time */

    function saveTimer() {
        $data = $this->request->data['params'];
        $data1 = array();
        $data1['project_id'] = $data['project_id'];
        $data1['task_id'] = $data['task_id'];
        $data1['description'] = $data['description'];
        $data1['task_date'][0] = date('Y-m-d', $data['start_time'] / 1000);
        $start_time = date('Y-m-d H:ia', $data['start_time'] / 1000);
        $start_time = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $start_time, "datetime");
        $start_time = explode(' ', $start_time);
        $data1['start_time'][0] = $this->Tmzone->convert12hourformat($start_time[1]);
        $end_time = date('Y-m-d H:ia', $data['end_time'] / 1000);
        $end_time = $this->Tmzone->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $end_time, "datetime");
        $end_time = explode(' ', $end_time);
        $data1['end_time'][0] = $this->Tmzone->convert12hourformat($end_time[1]);
        $data1['totalduration'][0] = (int) ($data['totalduration'] / 1000);
        $duration = (int) (($data['end_time'] - $data['start_time']) / 1000);
        $data1['totalbreak'][0] = (int) (($duration - $data1['totalduration'][0]) / 60);
        $data1['user_id'][0] = SES_ID;
        $data1['chked_ids'][0] = $data['chked_ids'];
        $Easycases = ClassRegistry::init('EasycasesController');
        $result = $this->add_tasklog($data1);
        echo $result;
        exit;
    }

    /*
     * @method resource_utilization
     * Author Satyajeet
     */

    function resource_utilization() {
        if (SES_TYPE > 2) {
            $this->redirect(HTTP_ROOT . 'timelog');
        }
        //$this->layout = 'ajax';
    }

    function ajax_resource_utilization() {
        $this->layout = 'ajax';
        $this->loadModel('Easycase');
        $this->loadModel('Project');
        $this->loadModel('User');
        $this->loadModel('CompanyUser');
        $this->loadModel('LogTime');
        $cond = "";
        $qry = '';
        $arr = array();
        $log_condition = '';
        $filter_msg = array();
        $dt_arr = array();
        $curDate = date('Y-m-d H:i:s');
        if (isset($_COOKIE['utilization_date_filter']) && $_COOKIE['utilization_date_filter'] != '' && $_COOKIE['utilization_date_filter'] != 'all') {
            $filter = $_COOKIE['utilization_date_filter'];
        }
        $sts_filter = $_COOKIE['utilization_status_filter'];
        $prj_filter = $_COOKIE['utilization_project_filter'];
        $usr_filter = $_COOKIE['utilization_resource_filter'];
        if (isset($sts_filter) && $sts_filter != '' && $sts_filter != 'all') {
            $qry.= $this->Format->statusFilter($sts_filter);
            if (substr($sts_filter, -1) == '-') {
                $sts = explode('-', $sts_filter);
                foreach ($sts as $k => $val) {
                    
                   /* if (!empty($val) && $val == 1) {
                        $msg = '<div class="fl filter_opn"><span>New</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeStatus(1)">X</span></div>';
                    } else if (!empty($val) && $val == 2) {
                        $msg = '<div class="fl filter_opn"><span>In Progress</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeStatus(2)">X</span></div>';
                    } else if (!empty($val) && $val == 3) {
                        $msg = '<div class="fl filter_opn"><span>Closed</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeStatus(3)">X</span></div>';
                    } else if (!empty($val) && $val == 5) {
                        $msg = '<div class="fl filter_opn"><span>Resolved</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeStatus(5)">X</span></div>';
                    } */
                    if($val != ''){
                        $msg = '<div class="fl filter_opn"><span>' . $this->formatstsname($val) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeStatus(' . $val . ')">X</span></div>';
                    $filter_msg['status'][] = $msg;
                }
                }
                $filter_msg['status'] = array_unique($filter_msg['status']);
            }
        }
        if (isset($prj_filter) && $prj_filter != '' && $prj_filter != 'all') {
            $qry.= $this->projectFilter($prj_filter, 'utilization');
            $prj = explode('-', $prj_filter);
            foreach ($prj as $k => $val) {
                $msg = '<div class="fl filter_opn"><span>' . $this->formatprjnm($val) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeProject(' . $val . ')">X</span></div>';
                $filter_msg['project'][] = $msg;
            }
            $filter_msg['project'] = array_unique($filter_msg['project']);
        }
        if (isset($usr_filter) && $usr_filter != '' && $usr_filter != 'all') {
            $qry.= $this->arcUserFilter($usr_filter, 'utilization');
            $usr = explode('-', $usr_filter);
            foreach ($usr as $k => $val) {
                $msg = '<div class="fl filter_opn"><span>' . $this->Format->caseMemsList($val) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:3px;cursor:pointer;" onclick="removeResource(' . $val . ')">X</span></div>';
                $filter_msg['resource'][] = $msg;
            }
            $filter_msg['resource'] = array_unique($filter_msg['resource']);
        }
        if (!isset($filter)) {
            $date = $this->Format->date_filter('thismonth', $curDate);
        } else {
            if (strpos($filter, ':') == false) {
                $date = $this->Format->date_filter($filter, $curDate);
            } else {
                $arr = explode(':', $filter);
                $date['strddt'] = $arr[0];
                $date['enddt'] = $arr[1];
            }
        }
        #pr($date);exit;
        $limit = $this->data['rowCount'] ? $this->data['rowCount'] : 50;
        $offset = ($this->data['current'] > 1 ? $this->data['current'] - 1 : 0) * $limit;
        $current = $this->data['current'] > 1 ? $this->data['current'] : 1;
        $searchPhrase = $this->data['searchPhrase'];
        $search_cond = '';
        $sort_cond = ' order by LogTime.user_id ASC';
        $sort_cond1 = ' order by Result.user_id ASC';
        #echo $this->data['sort']['resource'];exit;
        if (isset($this->data['sort']['resource'])) {
            if ($this->data['sort']['resource'] == 'asc') {
                $sort_cond = " order by User.name ASC";
                $sort_cond1 = ' order by Result.name ASC';
            } else {
                $sort_cond = " order by User.name DESC";
                $sort_cond1 = " order by Result.name DESC";
            }
        } elseif (isset($this->data['sort']['project'])) {
            if ($this->data['sort']['project'] == 'asc') {
                $sort_cond = " order by Project.name ASC";
                $sort_cond1 = " order by Result.pname ASC";
            } else {
                $sort_cond = " order by Project.name DESC";
                $sort_cond1 = " order by Result.pname DESC";
            }
        } elseif (isset($this->data['sort']['date'])) {
            if ($this->data['sort']['date'] == 'asc') {
                $sort_cond = " order by LogTime.start_datetime ASC";
                $sort_cond1 = " order by Result.start_datetime ASC";
            } else {
                $sort_cond = " order by LogTime.start_datetime DESC";
                $sort_cond1 = " order by Result.start_datetime DESC";
            }
        } elseif (isset($this->data['sort']['task_title'])) {
            if ($this->data['sort']['task_title'] == 'asc') {
                $sort_cond = " order by Easycase.title ASC";
                $sort_cond1 = " order by Result.title ASC";
            } else {
                $sort_cond = " order by Easycase.title DESC";
                $sort_cond1 = " order by Result.title DESC";
            }
        } elseif (isset($this->data['sort']['hours'])) {
            if ($this->data['sort']['hours'] == 'asc') {
                $sort_cond = " order by hours ASC";
                $sort_cond1 = " order by Result.hours ASC";
            } else {
                $sort_cond = " order by hours DESC";
                $sort_cond1 = " order by Result.hours DESC";
            }
        } elseif (isset($this->data['sort']['esthrs'])) {
            if ($this->data['sort']['esthrs'] == 'asc') {              
                $sort_cond1 = " order by est_hrs ASC";
            } else {
                $sort_cond1 = " order by est_hrs DESC";
        }
        } elseif (isset($this->data['sort']['task_group'])) {
            if ($this->data['sort']['task_group'] == 'asc') {              
                $sort_cond1 = " order by Result.mlstn_name ASC";
            } else {
                $sort_cond1 = " order by Result.mlstn_name DESC";
            }
        }
        if (isset($searchPhrase) && trim($searchPhrase) != '') {
            $search_cond = " AND (User.name LIKE '%" . $searchPhrase . "%'";
            if (in_array('project', $this->data['check'])) {
                $search_cond .= " OR Project.name LIKE '%" . $searchPhrase . "%'";
            }
            if (in_array('task_title', $this->data['check'])) {
                $search_cond .= " OR Easycase.title LIKE '%" . $searchPhrase . "%'";
            }
            $search_cond .=") ";
        }
        if (!empty($date['strddt']) && !empty($date['enddt'])) {
            $cond .= " AND DATE(Easycase.actual_dt_created) >= '" . $dt . "' ";
            $log_condition .= " AND DATE(start_datetime) >= '" . date('Y-m-d', strtotime($date['strddt'])) . "' AND DATE(start_datetime) <= '" . date('Y-m-d', strtotime($date['enddt'])) . "' ";
            $filter_msg['date'] = '<div class="fl filter_opn"><span>' . date('M d, Y', strtotime($date['strddt'])) . " to " . date('M d, Y', strtotime($date['enddt'])) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:5px;;cursor:pointer;" onclick="removeDate()">X</span></div>';
            $days = (strtotime($date['enddt']) - strtotime($date['strddt'])) / (60 * 60 * 24);
        } else if (!empty($date['strddt'])) {
            $cond .= " AND DATE(Easycase.actual_dt_created) >= '" . $dt . "' ";
            $log_condition .= " AND DATE(start_datetime) = '" . date('Y-m-d', strtotime($date['strddt'])) . "' ";
            $filter_msg['date'] = '<div class="fl filter_opn"><span>' . date('M d, Y', strtotime($date['strddt'])) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:5px;;cursor:pointer;" onclick="removeDate()">X</span></div>';
        } else if (isset($date['enddt']) && !empty($date['enddt'])) {
            $dt = date('Y-m-d', strtotime($date['enddt']));
            $cond .= " AND DATE(Easycase.actual_dt_created) <= '" . $dt . "' ";
            $log_condition .= " AND DATE(start_datetime) = '" . $dt . "' ";
            $filter_msg['date'] = '<div class="fl filter_opn"><span>' . date('M d, Y', strtotime($date['enddt'])) . '</span><span class="ico-close" rel="tooltip" title="Reset Filter" style="margin-left:5px;;cursor:pointer;" onclick="removeDate()">X</span></div>';
        }

        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $dtm = $view->loadHelper('Datetime');
        $fmt = $view->loadHelper('Format');
        $grpby = $grpby1 = '';
        $groupbyarr = array('date' => 'DATE(LogTime.start_datetime)', 'resource' => 'LogTime.user_id',
            'project' => 'LogTime.project_id', 'task_title' => 'Easycase.id', 'hours' => 'hours', 'is_billable' => 'billable');
        $groupbyarr1 = array('date' => 'DATE(Result.start_datetime)', 'resource' => 'Result.user_id',
            'project' => 'Result.project_id', 'task_title' => 'Result.id', 'hours' => 'Result.hours', 'is_billable' => 'Result.billable');
        $grpby1 = $grpby = 'GROUP BY ';
        $str1 = $str = '';
        foreach ($this->data['check'] as $k => $val) {
            if ($val != 'task_status' && $val != 'hours' && $val != 'task_type' && $val != 'task_group' && $val != 'esthrs') {
                $str = $str . $groupbyarr['' . $val . ''] . ',';
                $str1 = $str1 . $groupbyarr1['' . $val . ''] . ',';
            }
        }
        $str = rtrim($str, ',');
        $str1 = rtrim($str1, ',');
        $grpby = (!empty($str)) ? $grpby . $str : '';
        $grpby1 = (!empty($str1)) ? $grpby1 . $str1 : '';
        $usr_cond = '';
        if (SES_TYPE < 3) {
            $usr_cond = "LogTime.user_id >0";
        } elseif (SES_TYPE == 3) {
            $usr_cond = "LogTime.user_id = " . SES_ID;
        }
        $log_sql_inner = "SELECT LogTime.user_id, SUM(LogTime.total_hours) AS hours, GROUP_CONCAT(Distinct LogTime.task_id)  AS esthrs, "
                . "if(LogTime.is_billable=1, 'Yes', 'No') AS billable, User.name, User.last_name, Project.name as pname, Easycase.id, Easycase.title, Easycase.legend, Easycase.type_id, LogTime.start_datetime,LogTime.project_id, Milestone.title AS mlstn_name "
                . "FROM log_times AS LogTime "
                . "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
                . "LEFT JOIN easycase_milestones AS EasycaseMilestone ON LogTime.task_id = EasycaseMilestone.easycase_id "
                . "LEFT JOIN milestones AS Milestone ON EasycaseMilestone.milestone_id=Milestone.id "
                . "LEFT JOIN users AS User ON LogTime.user_id = User.id "
                . "LEFT JOIN projects AS Project ON LogTime.project_id= Project.id "
                . "WHERE Easycase.isactive=1 AND " . $usr_cond . " " . $log_condition . " " . $qry . " " . $search_cond . " AND Project.company_id=" . SES_COMP . " AND Easycase.id IS NOT NULL "
                . "$grpby $sort_cond  LIMIT $offset, $limit";

        $log_sql = "SELECT Result.*,sum(p.estimated_hours) as est_hrs FROM ($log_sql_inner) AS Result LEFT JOIN easycases AS p ON find_in_set(p.id,Result.esthrs) LEFT JOIN projects pr ON p.project_id = pr.id WHERE pr.company_id =" . SES_COMP . " AND p.id IS NOT NULL AND Result.id IS NOT NULL $grpby1 $sort_cond1 ";
        //print $log_sql_inner;exit; 
        $logtime = $this->LogTime->query($log_sql);
        $count_sql = "SELECT SQL_CALC_FOUND_ROWS if(LogTime.is_billable=1, 'Yes', 'No') AS billable "
                . "FROM log_times AS LogTime "
                . "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
                . "LEFT JOIN easycase_milestones AS EasycaseMilestone ON LogTime.task_id = EasycaseMilestone.easycase_id "
                . "LEFT JOIN milestones AS Milestone ON EasycaseMilestone.milestone_id=Milestone.id "
                . "LEFT JOIN users AS User ON LogTime.user_id = User.id "
                . "LEFT JOIN projects AS Project ON LogTime.project_id= Project.id "
                . "WHERE Easycase.isactive=1 AND " . $usr_cond . " " . $log_condition . " " . $qry . " " . $search_cond . " AND Project.company_id=" . SES_COMP . " AND Easycase.id IS NOT NULL "
                . "$grpby $sort_cond";
        $total_count = $this->LogTime->query($count_sql);
        $tot_od = $this->LogTime->query("SELECT FOUND_ROWS() as tot_od");
        #$esthrsdata = $this->LogTime->query($log_sql);
        #echo "<pre>";print_r($logtime);exit;
        $data = array("current" => $current,
            "rowCount" => $limit,
            "rows" => array(),
            "total" => $tot_od[0][0]['tot_od'],
            "filter_msg" => $filter_msg);
        if ($logtime) {
            $legnds = Hash::extract($logtime, '{n}.Result.legend');
			foreach($legnds as $kl => $vl){
                if($vl == 4){
                    $legnds[$kl] = 2 ;
                }
            }
            $this->loadModel('Status');
            $legendDetails = $this->Status->find('list', array('conditions' => array('Status.id' => $legnds), 'fields' => array('Status.id', 'Status.name')));
			$legendDetails[4] = 'In Progress';
        }
        foreach ($logtime as $key => $value) {
            $hour = $this->Format->format_time_hr_min($value['Result']['hours']);
            $esthrs = $this->Format->format_time_hr_min($value['0']['est_hrs']);
            $name = $value['Result']['name'] . " " . $value['Result']['last_name'];
            $caseDtUploaded = $value['Result']['start_datetime'];
            $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $caseDtUploaded, "datetime");
            $updatedCur = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
            $displayTime = $dtm->dateFormatOutputdateTime_day($updated, $updatedCur); //Nov 25, Thu at 1:25 pm
            $typeNm = $this->Format->getRequireTypeName($value['Result']['type_id']);
            $legend = $value['Result']['legend'];
            $legendname = $legendDetails[$legend];
            $data['rows'][] = array('date' => date('M d, Y', strtotime($updated)), 'resource' => $name,
                'project' => $fmt->formatTitle($value['Result']['pname']),
                'task_title' => $fmt->formatTitle($value['Result']['title']),
                'task_group' => $value['Result']['mlstn_name'] != '' ? $fmt->formatTitle($value['Result']['mlstn_name']) : 'Default Task Group',
                'task_status' => $legendname, 'task_type' => $typeNm, 'hours' => $hour,
                'esthrs' => $esthrs, 'is_billable' => $value['Result']['billable']);
        }
        echo json_encode($data);
        exit;
    }

    function ajax_resource_utilization_export_csv() {
        $this->layout = 'ajax';
        $this->loadModel('Easycase');
        $this->loadModel('Project');
        $this->loadModel('User');
        $this->loadModel('CompanyUser');
        $this->loadModel('LogTime');
        $cond = "";
        $arr = array();
        $log_condition = '';
        $curDate = date('Y-m-d H:i:s');
        if (isset($_COOKIE['utilization_date_filter']) && $_COOKIE['utilization_date_filter'] != '' && $_COOKIE['utilization_date_filter'] != 'all') {
            $filter = $_COOKIE['utilization_date_filter'];
        }

        $sts_filter = isset($_COOKIE['utilization_status_filter']) ? $_COOKIE['utilization_status_filter'] : '';
        $prj_filter = isset($_COOKIE['utilization_project_filter']) ? $_COOKIE['utilization_project_filter'] : '';
        $usr_filter = isset($_COOKIE['utilization_resource_filter']) ? $_COOKIE['utilization_resource_filter'] : '';

        if (isset($sts_filter) && $sts_filter != '' && $sts_filter != 'all') {
            $qry.= $this->Format->statusFilter($sts_filter);
        }
        if (isset($prj_filter) && $prj_filter != '' && $prj_filter != 'all') {
            $qry.= $this->projectFilter($prj_filter, 'utilization');
        }
        if (isset($usr_filter) && $usr_filter != '' && $usr_filter != 'all') {
            $qry.= $this->arcUserFilter($usr_filter, 'utilization');
        }
        if (!isset($filter)) {
            $date = $this->Format->date_filter('thismonth', $curDate);
        } else {
            if (strpos($filter, ':') == false) {
                $date = $this->Format->date_filter($filter, $curDate);
            } else {
                $arr = explode(':', $filter);
                $date['strddt'] = $arr[0];
                $date['enddt'] = $arr[1];
            }
        }

        $check = explode(',', $this->params->query['check']);
        $searchPhrase = $this->params->query['search'];
        $search_cond = '';
        $sort_cond = ' order by LogTime.user_id ASC';
        if (isset($searchPhrase) && trim($searchPhrase) != '') {
            $search_cond = " AND (User.name LIKE '%" . $searchPhrase . "%'";
            if (in_array('project', $check)) {
                $search_cond .= " OR Project.name LIKE '%" . $searchPhrase . "%'";
            }
            if (in_array('task_title', $check)) {
                $search_cond .= " OR Easycase.title LIKE '%" . $searchPhrase . "%'";
            }
            $search_cond .=") ";
        }

        if (!empty($date['strddt']) && !empty($date['enddt'])) {
            //$log_condition .= " AND DATE(start_datetime) >= '" . date('Y-m-d', strtotime($date['strddt'])) . "' AND DATE(start_datetime) <= '" . date('Y-m-d', strtotime($date['enddt'])) . "' ";
            $log_condition .= " AND DATE(start_datetime) BETWEEN '" . date('Y-m-d', strtotime($date['strddt'])) . "' AND '" . date('Y-m-d', strtotime($date['enddt'])) . "' ";
        } else if (!empty($date['strddt'])) {
            //here checking equal to date as only one date value is given and same is happening below
            $log_condition .= " AND DATE(start_datetime) = '" . date('Y-m-d', strtotime($date['strddt'])) . "' ";
        } else if (!empty($date['enddt'])) {
            $dt = date('Y-m-d', strtotime($date['enddt']));
            $log_condition .= " AND DATE(start_datetime) = '" . $dt . "' ";
        }

        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $dtm = $view->loadHelper('Datetime');
        $fmt = $view->loadHelper('Format');
        $grpby1 = $grpby = '';

        $groupbyarr = array('date' => 'DATE(LogTime.start_datetime)', 'resource' => 'LogTime.user_id',
            'project' => 'LogTime.project_id', 'task_title' => 'Easycase.id', 'hours' => 'hours', 'is_billable' => 'billable');
        $groupbyarr1 = array('date' => 'DATE(Result.start_datetime)', 'resource' => 'Result.user_id',
            'project' => 'Result.project_id', 'task_title' => 'Result.id', 'hours' => 'Result.hours', 'is_billable' => 'Result.billable');
        $grpby1 = $grpby = 'GROUP BY ';
        $str1 = $str = '';
        foreach ($check as $k => $val) {
            if ($val != 'task_status' && $val != 'hours' && $val != 'task_type' && $val != 'task_group' && $val != 'esthrs') {
                $str.= $groupbyarr[$val] . ',';
                $str1.= $groupbyarr1[$val] . ',';
            }
        }
        $str = rtrim($str, ',');
        $str1 = rtrim($str1, ',');

        $grpby = (!empty($str)) ? $grpby . $str : '';
        $grpby1 = (!empty($str1)) ? $grpby1 . $str1 : '';

        $usr_cond = '';
        if (SES_TYPE < 3) {
            $usr_cond = "LogTime.user_id >0";
        } elseif (SES_TYPE == 3) {
            $usr_cond = "LogTime.user_id = " . SES_ID;
        }

        $log_sql_inner = "SELECT LogTime.user_id, SUM(LogTime.total_hours) AS hours, GROUP_CONCAT(Distinct LogTime.task_id)  AS esthrs, "
                . "if(LogTime.is_billable=1, 'Yes', 'No') AS billable, User.name, User.last_name, Project.name as pname, Easycase.id, Easycase.title, Easycase.legend, Easycase.type_id, LogTime.start_datetime,LogTime.project_id, Milestone.title AS mlstn_name "
                . "FROM log_times AS LogTime "
                . "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
                . "LEFT JOIN easycase_milestones AS EasycaseMilestone ON LogTime.task_id = EasycaseMilestone.easycase_id "
                . "LEFT JOIN milestones AS Milestone ON EasycaseMilestone.milestone_id=Milestone.id "
                . "LEFT JOIN users AS User ON LogTime.user_id = User.id "
                . "LEFT JOIN projects AS Project ON LogTime.project_id= Project.id "
                . "WHERE Easycase.isactive=1 AND " . $usr_cond . " " . $log_condition . " " . $qry . " " . $search_cond . " AND Project.company_id=" . SES_COMP . " AND Easycase.id IS NOT NULL "
                . "$grpby $sort_cond ";

        $log_sql = "SELECT SQL_CALC_FOUND_ROWS Result.*,sum(p.estimated_hours) as est_hrs FROM ($log_sql_inner) AS Result LEFT JOIN easycases AS p ON find_in_set(p.id,Result.esthrs) "
                . " LEFT JOIN projects pr ON p.project_id = pr.id "
                . " WHERE p.id IS NOT NULL AND Result.id IS NOT NULL AND pr.company_id=" . SES_COMP . " " . $grpby1;

        $logtime = $this->LogTime->query($log_sql);
        $tot_od = $this->LogTime->query("SELECT FOUND_ROWS() as tot_od");
        #pr($logtime);exit;
        $data = array();
        if (is_array($logtime) && count($logtime) > 0) {
            if ($logtime) {
                $legnds = Hash::extract($logtime, '{n}.Result.legend');
                $this->loadModel('Status');
                $legendDetails = $this->Status->find('list', array('conditions' => array('Status.id' => $legnds), 'fields' => array('Status.id', 'Status.name')));
				$legendDetails[4] = 'In Progress';
            }
            foreach ($logtime as $key => $val) {
                //$hour = $this->Format->format_time_hr_min_new($val['0']['hours']);				
                $hour = round(($val['Result']['hours'] / 3600), 2);
                $estimatedHour = round(($val[0]['est_hrs'] / 3600), 2);
                $name = $val['Result']['name'] . " " . $val['Result']['last_name'];
                $caseDtUploaded = $val['Result']['start_datetime'];
                $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $caseDtUploaded, "datetime");
                $updatedCur = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
                $displayTime = $dtm->dateFormatOutputdateTime_day($updated, $updatedCur); //Nov 25, Thu at 1:25 pm
                $typeNm = $this->Format->getRequireTypeName($val['Result']['type_id']);
                $legend = $val['Result']['legend'];
                $legendname = $legendDetails[$legend];
                $data[$key]['resource'] = $name;
                $data[$key]['date'] = date('M d, Y', strtotime($updated));
                $data[$key]['hours'] = $hour;
                if (in_array('esthrs', $check)) {
                    $data[$key]['esthrs'] = $estimatedHour;
                }
                $data[$key]['is_billable'] = $val['Result']['billable'];
                if (in_array('project', $check)) {
                    $data[$key]['project'] = addslashes(html_entity_decode($val['Result']['pname']));
                }
                if (in_array('task_title', $check)) {
                    $data[$key]['task_title'] = addslashes(html_entity_decode($val['Result']['title']));
                    $data[$key]['task_group'] = $val['Result']['mlstn_name'] != '' ? html_entity_decode($val['Result']['mlstn_name'], ENT_QUOTES) : 'Without Milestone';
                    $data[$key]['task_status'] = $legendname;
                    $data[$key]['task_type'] = $typeNm;
                }
            }
        }
        #pr($data);exit; 

        $content = '';
        if (in_array('date', $check)) {
            $content = 'Date';
        }
        if (in_array('resource', $check)) {
            if ($content == '')
                $content .= 'Resource';
            else
                $content .= ',Resource';
        }
        if (in_array('project', $check)) {
            if ($content == '')
                $content .= 'Project';
            else
                $content .= ',Project';
        }
        if (in_array('task_title', $check)) {
            if ($content == '')
                $content .= 'Task';
            else
                $content .= ',Task';

            if (in_array('task_group', $check)) {
                $content == '' ? $content .= 'Milestone' : $content .=',Milestone';
            }
            if (in_array('task_status', $check)) {
                $content == '' ? $content .= 'Status' : $content .=',Status';
            }
            if (in_array('task_type', $check)) {
                $content == '' ? $content .= 'Task Type' : $content .=',Task Type';
            }
        }

        if (in_array('hours', $check)) {
            if ($content == '')
                $content .= 'Hour(s) Spent';
            else
                $content .= ',Hour(s) Spent';
        }

        if (in_array('esthrs', $check)) {
            if ($content == '')
                $content .= 'Estimated Hour(s)';
            else
                $content .= ',Estimated Hour(s)';
        }

        if (in_array('is_billable', $check)) {
            if ($content == '')
                $content .= 'Billable';
            else
                $content .= ',Billable';
        }

        $content .= "\n";
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $key => $val) {

                if (in_array('date', $check)) {
                    $content .= date('d/m/Y', strtotime($val['date'])) . ',';
                }
                if (in_array('resource', $check)) {
                    $content .='"' . str_replace('"', '""', trim($val['resource'])) . '",';
                }
                if (in_array('project', $check)) {
                    $content .='"' . str_replace('"', '""', trim($val['project'])) . '",';
                }
                if (in_array('task_title', $check)) {
                    $content .= '"' . str_replace('"', '""', trim($val['task_title'])) . '",';
                    if (in_array('task_group', $check)) {
                        $content .= '"' . str_replace('"', '""', trim($val['task_group'])) . '",';
                    }
                    if (in_array('task_status', $check)) {
                        $content .='"' . str_replace('"', '""', trim($val['task_status'])) . '",';
                    }
                    if (in_array('task_type', $check)) {
                        $content .='"' . str_replace('"', '""', trim($val['task_type'])) . '",';
                    }
                }
                if (in_array('hours', $check)) {
                    $content .= '"' . $val['hours'] . '",';
                }
                if (in_array('esthrs', $check)) {
                    $content .= '"' . $val['esthrs'] . '",';
                }
                if (in_array('is_billable', $check)) {
                    $content .= '"' . $val['is_billable'] . '",';
                }
                $content = trim($content, ',');
                $content .="\n";
            }
        }

        if (!is_dir(RESOURCE_UTILIZATION_CSV_PATH)) {
            @mkdir(RESOURCE_UTILIZATION_CSV_PATH, 0777, true);
        }

        $name = $this->params->query['projuniqid'];
        if (trim($name) != '' && strlen($name) > 25) {
            $name = substr($name, 0, 24) . "_" . date('m-d-Y', strtotime(GMT_DATE)) . "_resource_utilization.csv";
        } else {
            $name .= (trim($name) != '' ? "_" : '') . date('m-d-Y', strtotime(GMT_DATE)) . "_resource_utilization.csv";
        }
        $download_name = date('m-d-Y', strtotime(GMT_DATE)) . "_resource_utilization.csv";

        $file_path = RESOURCE_UTILIZATION_CSV_PATH . $name;
        $fp = @fopen($file_path, 'w+');
        fwrite($fp, $content);
        fclose($fp);

        $this->response->file($file_path, array('download' => true, 'name' => urlencode($download_name)));
        return $this->response;
    }

    function arcUserFilter($usrid, $type = NULL) {
        $qry = "";
        $qryTyp = "";
        if ($usrid != "all") {
            if (strstr($usrid, "-")) {
                $typArr = explode("-", $usrid);
                foreach ($typArr as $typChk) {
                    if ($type == 'utilization') {
                        $qryTyp.="LogTime.user_id=" . $typChk . " OR ";
                    } elseif ($type == 'invoice') {
                        $qryTyp.="LogTime.user_id=" . $typChk . " OR ";
                    } else {
                        $qryTyp.="Archive.user_id=" . $typChk . " OR ";
                    }
                }
                $qryTyp = substr($qryTyp, 0, -3);
                if ($type != 'invoice') {
                    $qry.=" AND (" . $qryTyp . ")";
                } else {
                    $qry.=" (" . $qryTyp . ")";
                }
            } else {

                if ($type == 'utilization') {
                    $qry.=" AND LogTime.user_id=" . $usrid;
                } elseif ($type == 'invoice') {
                    $qry.="LogTime.user_id=" . $usrid;
                } else {
                    $qry.=" AND Archive.user_id=" . $usrid;
                }
            }
        }
        return $qry;
    }

    function projectFilter($prjid) {
        $qry = "";
        $qryTyp = "";
        if ($prjid != "all") {
            if (strstr($prjid, "-")) {
                $typArr = explode("-", $prjid);
                //foreach ($typArr as $typChk) {
                if (!empty($typArr)) {
                    $typ = implode(",", $typArr);
                    $qry.="AND Easycase.project_id IN (" . $typ . ")";
                }
                //}
                //$qryTyp = substr($qryTyp, 0, -3);
                //$qry.=" AND (" . $qryTyp . ")";
            } else {
                $qry.=" AND Easycase.project_id=" . $prjid;
            }
        }
        return $qry;
    }

    function formatprjnm($prjid) {
        $prj = ClassRegistry::init('Project');
        //$prjsname = $prj->find('fisrt', array('conditions'=>array('Project.id'=>$prjid, 'Project.company_id'=>SES_COMP), 'fields'=>array('Project.short_name')));
        $prjsname = $prj->query("SELECT Project.short_name FROM projects AS Project WHERE Project.id=" . $prjid . " AND Project.company_id=" . SES_COMP . "");
        return $prjsname['0']['Project']['short_name'];
    }

    function formatstsname($stsid){
        $sts = ClassRegistry::init('Status');
        $statusname = $sts->find('first',array('conditions'=>array('Status.id' => $stsid))); //("SELECT Status.name FROM statuses AS Status WHERE Status.id=" . $stsid);
        return $statusname['Status']['name'];
    }

    /*
     * Author: Orangescrum
     * For Dashboard Time log graph
     *     */

    function timelog_graph() {
        $this->layout = 'ajax';
        $this->loadModel('Project');
        $this->Project->recursive = -1;
        if (SES_TYPE < 3) {
            $user_id = 'ProjectUser.user_id > 0';
        } else {
            $user_id = 'ProjectUser.user_id=' . SES_ID;
        }
        $projId = $this->data['projid'];
        $projQry = '';
        if ($projId != 'all') {
            $projid = $this->Project->find('first', array('conditions' => array('Project.uniq_id' => $projId), 'fields' => array('Project.id')));
            $project_id = $projid['Project']['id'];
            $projQry = " AND LogTime.project_id = $project_id";
        } else {
            $projQry = " AND LogTime.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE " . $user_id . " AND ProjectUser.company_id=" . SES_COMP . " AND ProjectUser.project_id=Project.id AND Project.isactive='1') ";
        }
        $dates = $this->Format->date_filter('last30days');
        $days = (strtotime($dates['enddt']) - strtotime($dates['strddt'])) / (60 * 60 * 24);
        $x = floor($days);
        if ($x < 7) {
            $interval = 1;
        } elseif ($x > 80) {
            $interval = ceil($x / 10);
        } else {
            $interval = 7;
        }
        $this->set('tinterval', $interval);
        $dt_arr = array();
        $dts_arr = array();
        for ($i = 0; $i <= $x; $i++) {
            $m = " +" . $i . "day";
            $dt = date('Y-m-d', strtotime(date("Y-m-d", strtotime($dates['strddt'])) . $m));
            $dts = date('M d, Y', strtotime(date("Y-m-d H:i:s", strtotime($dates['strddt'])) . $m));
            $times = explode(" ", GMT_DATETIME);
            array_push($dt_arr, $dt);
            array_push($dts_arr, $dts);
        }
        $dateCond = " AND DATE(LogTime.start_datetime) BETWEEN '" . date('Y-m-d', strtotime($dates['strddt'])) . "' AND '" . date('Y-m-d', strtotime($dates['enddt'])) . "'";


        $this->loadModel('LogTime');
        /* find total billable and non-billable time */
        $count_sql = 'SELECT sum(total_hours) as secds,is_billable,DATE(LogTime.start_datetime) AS date '
                . 'FROM log_times AS `LogTime` '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 1 AND Easycase.isactive =1 ' . $projQry . ' ' . $dateCond . '  '
                . 'GROUP BY DATE(LogTime.start_datetime)  '
                . 'UNION '
                . 'SELECT sum(total_hours) as secds, is_billable,DATE(LogTime.start_datetime) AS date '
                . 'FROM log_times AS LogTime '
                . "LEFT JOIN easycases AS Easycase ON Easycase.id=LogTime.task_id AND LogTime.project_id=Easycase.project_id "
                . 'WHERE is_billable = 0 AND Easycase.isactive =1 ' . $projQry . ' ' . $dateCond . '  '
                . 'GROUP BY DATE(LogTime.start_datetime) ';
        #echo $count_sql;exit;
        $cntlog = $this->LogTime->query($count_sql);

        if (is_array($cntlog) && count($cntlog) > 0) {
            $billablearr = array();
            $nonbillablearr = array();
            foreach ($cntlog as $k => $val) {
                if ($val[0]['is_billable'] == 1) {
                    $billablearr[$val[0]['date']] = $val[0];
                } else {
                    $nonbillablearr[$val[0]['date']] = $val[0];
                }
            }
            foreach ($dt_arr as $key => $dt) {
                $nonbillable_series['name'] = 'Non-billable';
                $nonbillable_series['color'] = '#C5C5C5';
                if (array_key_exists($dt, $nonbillablearr)) {
                    $nonbillable_series['data'][] = ($nonbillablearr[$dt]['secds'] / 3600);
                } else {
                    $nonbillable_series['data'][] = 0;
                }
                $billable_series['name'] = 'Billable';
                $billable_series['color'] = '#00A2FF';
                if (array_key_exists($dt, $billablearr)) {
                    $billable_series['data'][] = ($billablearr[$dt]['secds'] / 3600);
                } else {
                    $billable_series['data'][] = 0;
                }
            }
            $series[0] = $nonbillable_series;
            $series[1] = $billable_series;

            $this->set('dt_arr', json_encode($dts_arr));
            $this->set('series', json_encode($series));
        } else {
            echo "<img src='" . HTTP_ROOT . "img/dbord_timelog.jpg' height='285px' width='98%'>";
            exit;
        }
    }

    function getlastLog($projUniq = '', $taskid = '') {
        $this->layout = 'ajax';
        $proj_uniq_id = !empty($this->data['projUniq']) ? $this->data['projUniq'] : $projUniq;
        $taskid = !empty($this->data['taskid']) ? $this->data['taskid'] : $taskid;
        if ($proj_uniq_id != 'all') {
            $this->loadModel('Timelog.LogTime');
            $this->LogTime->bindModel(array('belongsTo' => array('Project')));
            $cond = array('Project.uniq_id' => $proj_uniq_id, 'Project.isactive' => 1, 'LogTime.created >' => date('Y-m-d 00:00:00'));
            $cond1 = array('Project.uniq_id' => $proj_uniq_id, 'Project.isactive' => 1);
            if (!empty($taskid)) {
                $cond['LogTime.task_id'] = $taskid;
                $cond1['LogTime.task_id'] = $taskid;
            }
            if (SES_TYPE == 3) {
                $cond['LogTime.user_id'] = SES_ID;
                $cond1['LogTime.user_id'] = SES_ID;
            }
            $this->loadModel('Timelog.LogTime');
            $projArr = $this->LogTime->find('all', array('conditions' => $cond, 'fields' => array('LogTime.created', 'LogTime.total_hours'), 'order' => array('LogTime.created DESC')));
            $this->LogTime->create();
            $this->LogTime->bindModel(array('belongsTo' => array('Project')));
            $latedittime = $this->LogTime->find('first', array('conditions' => $cond1, 'fields' => array('LogTime.created'), 'order' => array('LogTime.created DESC')));
            $total_hour = 0;
            $total_hour_format = '0 hr(s)';
            $created_on = '';
            if (count($projArr) > 0) {
                foreach ($projArr as $k => $v) {
                    $total_hour += intval($v['LogTime']['total_hours']);
                }
            }
            $total_hour_format = floor($total_hour / 3600) . ' hr(s) ';
            $mins = round(($total_hour % 3600) / 60);
            if ($mins > 0) {
                $total_hour_format .= $mins . " min(s) ";
            }
            $view = new View($this);
            $dt = $view->loadHelper('Datetime');
            $tz = $view->loadHelper('Tmzone');
            if (isset($latedittime['LogTime']['created'])) {
                $curDateTz = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
                $locDT1 = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $latedittime['LogTime']['created'], "datetime");
                $created_on = $dt->facebook_style_date_time($locDT1, $curDateTz);
                if (!empty($projUniq)) {
                    $log_time['logged'] = $total_hour_format;
                    $log_time['last_entry'] = $created_on;
                    return $log_time;
//                    return "Logged: <b>{$total_hour_format} today</b>. Last entry: <b>{$created_on}</b>";
                } else {
                    echo "Logged: <b>{$total_hour_format} today</b>. Last entry: <b>{$created_on}</b>";
                }
            } else {
                if (!empty($projUniq)) {
                    $log_time['logged'] = $total_hour_format;
                    $log_time['last_entry'] = $created_on;
                    return $log_time;
//                    return "Logged: <b>{$total_hour_format} today</b>. Last entry: <b>none</b>";
                } else {
                    echo "Logged: <b>{$total_hour_format} today</b>. Last entry: <b>none</b>";
                }
            }
        }
        if (!empty($projUniq)) {
            return true;
        } else {
            exit;
        }
    }

}
