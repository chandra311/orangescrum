<?php

/* * *******************************************************************************
 * Orangescrum Community Edition is a web based Project Management software developed by
 * Orangescrum. Copyright (C) 2013-2014
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact Orangescrum, 2059 Camden Ave. #118, San Jose, CA - 95124, US. 
  or at email address support@orangescrum.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * Orangescrum" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by Orangescrum".
 * ****************************************************************************** */

class MilestonesController extends AppController {

    var $helpers = array('Html', 'Form', 'Casequery', 'Format');
    var $name = 'Milestone';
    public $components = array('Format');
    var $paginate = array();

    function assign_case($miles = null) {
        if (!empty($miles)) {
            $this->request->data = $miles;
        }
        $this->layout = 'ajax';
        $caseid = $this->request->data['caseid'];
        $project_id = $this->request->data['project_id'];
        $milestone_id = $this->request->data['milestone_id'];

        $this->loadModel('EasycaseMilestone');

        foreach ($caseid as $cid) {
            if ($cid) {
                $id_seq_arr = $this->EasycaseMilestone->query('SELECT MAX(id_seq) as id_seq FROM easycase_milestones WHERE milestone_id = ' . $milestone_id);
                $EasycaseMilestone['EasycaseMilestone']['easycase_id'] = $cid;
                $EasycaseMilestone['EasycaseMilestone']['milestone_id'] = $milestone_id;
                $EasycaseMilestone['EasycaseMilestone']['project_id'] = $project_id;
                $EasycaseMilestone['EasycaseMilestone']['user_id'] = SES_ID;
                if ($id_seq_arr['0'][0]['id_seq']) {
                    $EasycaseMilestone['EasycaseMilestone']['id_seq'] = (int) ($id_seq_arr['0'][0]['id_seq'] + 1);
                } else {
                    $EasycaseMilestone['EasycaseMilestone']['id_seq'] = 1;
                }
                $this->EasycaseMilestone->saveAll($EasycaseMilestone);
            }
        }
        if (!empty($miles)) {
            $arr['status'] = 1;
            $arr['msg'] = 'Task added successfully.';
            return $arr;
        } else {
        echo "success";
        exit;
    }
    }

    function remove_case($miles = null) {
        $this->layout = 'ajax';
        if (!empty($miles)) {
            $this->request->data = $miles;
        }
        $caseid = $this->request->data['caseid'];
        $project_id = $this->request->data['project_id'];
        $milestone_id = $this->request->data['milestone_id'];
        $this->loadModel('EasycaseMilestone');
        if ($this->EasycaseMilestone->deleteAll(array('project_id' => $project_id, 'milestone_id' => $milestone_id, 'easycase_id' => $caseid))) {
            if (!empty($miles)) {
                $arr['status'] = 1;
                $arr['msg'] = 'Task removed successfully.';
                return $arr;
            }
            echo 'success';
            exit;
        } else {
            if (!empty($miles)) {
                $arr['error'] = 1;
                $arr['msg'] = 'No task is selected to delete.';
                return $arr;
            }
            echo 'error';
            exit;
        }
    }

    function delete_milestone($uniqid = '', $page = NULL, $api_flag = '') {
        if (!empty($api_flag) && $api_flag == 1) {
            $this->request->data = $uniqid;
            unset($uniqid);
        }
        $uniqid = $uniqid ? $uniqid : $this->request->data['uniqid'];
        if (isset($uniqid) && $uniqid) {
            $checkQuery = "SELECT Milestone.id, Milestone.title FROM milestones AS Milestone,project_users AS ProjectUser WHERE Milestone.project_id=ProjectUser.project_id AND ProjectUser.user_id=" . SES_ID . " AND Milestone.uniq_id='" . $uniqid . "' AND Milestone.company_id='" . SES_COMP . "'";
            $checkMstn = $this->Milestone->query($checkQuery);
            if (count($checkMstn) && isset($checkMstn[0]['Milestone']['id']) && $checkMstn[0]['Milestone']['id']) {
                $id = $checkMstn[0]['Milestone']['id'];
                $this->Milestone->delete($id);
                $this->loadModel('EasycaseMilestone');
                $this->EasycaseMilestone->query("DELETE FROM easycase_milestones WHERE milestone_id='" . $id . "'");
                $arr['err'] = 0;
                $arr['msg'] = __("Milestone", true) . " '" . $checkMstn[0]['Milestone']['title'] . "' " . __("has been deleted", true) . ".";
                //$this->Session->write('SUCCESS',"Milestone '".$checkMstn[0]['Milestone']['title']."' has been deleted.");
            } else {
                $arr['err'] = 1;
                $arr['msg'] = __("Oops! Error occured in deletion of milestone.", true);
                //$this->Session->write('ERROR','Oops! Error occured in deletion of milestone');
            }
        } else {
            $arr['err'] = 1;
            $arr['msg'] = __("Oops! Error occured in deletion of milestone.", true);
            //$this->Session->write('ERROR','Oops! Error occured in deletion of milestone');
        }
        if (!empty($api_flag) && $api_flag == 1) {
            return $arr;
        } else {
        echo json_encode($arr);
        exit;
        }
        //$this->redirect($_SERVER['HTTP_REFERER']);exit;
        //$this->redirect(HTTP_ROOT."milestone");exit;
    }

    function case_listing() {
        $this->layout = 'ajax';
        $this->loadModel('EasycaseMilestone');

        $milestone_id = $this->params['data']['milestone_id'];
        $getCount = $this->params['data']['count'];
        $uid = $this->params['data']['uid'];
        if (isset($this->params['data']['msid']) && $this->params['data']['msid']) {
            $id = $this->params['data']['msid'];
            $allCases = $this->EasycaseMilestone->delete($id);
        }

        //$allCases = $this->EasycaseMilestone->find('all', array('conditions' => array('EasycaseMilestone.milestone_id' => $milestone_id),'order' => array('EasycaseMilestone.created DESC')));

        $allCases = $this->EasycaseMilestone->query("SELECT * FROM easycases as Easycase,easycase_milestones as EasycaseMilestone WHERE Easycase.isactive='1' AND Easycase.istype='1' AND  EasycaseMilestone.easycase_id=Easycase.id AND EasycaseMilestone.milestone_id=" . $milestone_id . " ORDER BY EasycaseMilestone.created DESC LIMIT 0,50");

        $this->set('allCases', $allCases);
        $this->set('getCount', $getCount);
        $this->set('uid', $uid);
    }

    function add_case($miles = null) {
        if (!empty($miles)) {
            $this->request->data = $miles;
        }
        $this->layout = 'ajax';
        $mstid = $this->request->data['mstid'];
        $projid = $this->request->data['projid'];
        $query = "";
        if (isset($this->request->data['title']) && trim($this->request->data['title'])) {
            $srchstr = addslashes($this->request->data['title']);
            //$query = "AND Easycase.title LIKE '%$srchstr%'";
            if (trim(urldecode($srchstr))) {
                $query = $this->Format->caseKeywordSearch($srchstr, 'title');
            }
        }
        $response = array();
        $milestone = $this->Milestone->findById($mstid);
        $response += $milestone;
        $this->loadModel('Easycase');
        $easycases = $this->Easycase->query("SELECT * FROM easycases as Easycase WHERE Easycase.project_id=" . $projid . " AND Easycase.isactive='1' AND Easycase.legend !='3' AND Easycase.legend !='5' AND Easycase.type_id !='10' AND Easycase.istype='1' " . $query . " AND NOT EXISTS(SELECT EasycaseMilestone.easycase_id FROM easycase_milestones AS EasycaseMilestone WHERE EasycaseMilestone.easycase_id=Easycase.id AND EasycaseMilestone.project_id=" . $projid . ") ORDER BY Easycase.dt_created DESC LIMIT 0,50");
        $response['tasks'] = $easycases;
        $this->set('milestone', $milestone);
        $this->set('easycases', $easycases);

        $curProjName = NULL;
        $curProjShortName = NULL;

        $this->loadModel('ProjectUser');
        $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
        $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.id' => $projid, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.name', 'Project.short_name')));

        if (count($projArr)) {
            $curProjName = $projArr['Project']['name'];
            $curProjShortName = $projArr['Project']['short_name'];
        }
        $response['project_name'] = $curProjName;
        $response['project_short_name'] = $curProjShortName;
        $response['mstid'] = $mstid;
        $response['projid'] = $mstid;
        if (!empty($miles)) {
            return $response;
        }
        $this->set('curProjName', $curProjName);
        $this->set('curProjShortName', $curProjShortName);
        $this->set('mstid', $mstid);
        $this->set('projid', $projid);
    }

    function removeCasesFromMilestone($miles = null) {
        $this->layout = 'ajax';
        if (!empty($miles)) {
            $mstid = $miles['mstid'];
            $projid = $miles['projid'];
        } else {
        $mstid = $this->params['data']['mstid'];
        $projid = $this->params['data']['projid'];
        }
        $query = "";
        if (isset($this->params['data']['title']) && trim($this->params['data']['title'])) {
            $srchstr = addslashes($this->params['data']['title']);
            //$query = "AND Easycase.title LIKE '%$srchstr%'";
            if (trim(urldecode($srchstr))) {
                $query = $this->Format->caseKeywordSearch($srchstr, 'title');
            }
        }

        $response = array();
        $milestone = $this->Milestone->findById($mstid);
        $response += $milestone;
        $this->loadModel('Easycase');
        $easycases = $this->Easycase->query("SELECT * FROM easycases as Easycase,easycase_milestones AS Em WHERE Easycase.id=Em.easycase_id AND Em.milestone_id = $mstid  AND Easycase.project_id=" . $projid . " AND Easycase.isactive='1' AND Easycase.type_id !='10' AND Easycase.istype='1' " . $query . " ORDER BY Easycase.dt_created DESC LIMIT 0,50");
        $response['tasks'] = $easycases;
        $this->set('milestone', $milestone);
        $this->set('easycases', $easycases);

        $curProjName = NULL;
        $curProjShortName = NULL;

        $this->loadModel('ProjectUser');
        $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
        $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.id' => $projid, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'Project.company_id' => SES_COMP), 'fields' => array('Project.name', 'Project.short_name')));

        if (count($projArr)) {
            $curProjName = $projArr['Project']['name'];
            $curProjShortName = $projArr['Project']['short_name'];
        }
        $response['project_name'] = $curProjName;
        $response['project_short_name'] = $curProjShortName;
        $response['mstid'] = $mstid;
        $response['projid'] = $mstid;
        if (!empty($miles)) {
            return $response;
        }
        $this->set('curProjName', $curProjName);
        $this->set('curProjShortName', $curProjShortName);
        $this->set('mstid', $mstid);
        $this->set('projid', $projid);
        //$this->render('add_case');
    }

    function ajax_milestone_menu() {
        $this->layout = 'ajax';
        if (isset($this->request['data']['project_id'])) {
            $query = "SELECT Milestone.id,Milestone.title FROM milestones AS Milestone,project_users AS ProjectUser WHERE Milestone.project_id=ProjectUser.project_id AND ProjectUser.user_id='" . SES_ID . "' AND Milestone.company_id='" . SES_COMP . "' AND Milestone.project_id='" . $this->request['data']['project_id'] . "' ORDER BY Milestone.start_date ASC";
            $milestone_all = $this->Milestone->query($query);
            $this->set('milestone_all', $milestone_all);
            $this->set('pjid', $this->request['data']['project_id']);
        }
    }

    function ajax_new_milestone($mileuniqid = null, $inpu_default = null, $api_flag = null) {
        $this->layout = 'ajax';
        if (!empty($inpu_default) && !empty($api_flag) && ($api_flag == 2 || $api_flag = 3)) {
            $this->request->data = $inpu_default;
            unset($inpu_default);
        }

        if (isset($this->data['mileuniqid']) && $this->data['mileuniqid']) {
        $mileuniqid = $this->data['mileuniqid'];
        }

        $this->loadModel('ProjectUser');
        $this->loadModel('Project');
        if (!empty($this->request->data['Milestone']) && $this->request->data['Milestone']['title'] && !$this->request->data['mileuniqid']) {
            $this->request->data['Milestone']['start_date'] = date('Y-m-d', strtotime($this->request->data['Milestone']['start_date']));
            $this->request->data['Milestone']['end_date'] = date('Y-m-d', strtotime($this->request->data['Milestone']['end_date']));
            if (strtotime($this->request->data['Milestone']['start_date']) > strtotime($this->request->data['Milestone']['end_date'])) {
                //$this->Session->write("ERROR", "Start date cannot exceed End date");
                $arr['error'] = 1;
                $arr['msg'] = __('Start date cannot exceed End date', true);
                echo json_encode($arr);
                exit;
//				if($_SERVER['HTTP_REFERER']==HTTP_ROOT.'dashboard'){
//					$this->redirect(HTTP_ROOT.'dashboard#milestone');exit;
//				}else{
//					$this->redirect($_SERVER['HTTP_REFERER']);exit;
//				}
            } else {
                if ($this->request->data['Milestone']['id']) {
                    $checkDuplicate = $this->Milestone->query("SELECT Milestone.id FROM milestones AS Milestone WHERE Milestone.title='" . addslashes($this->request->data['Milestone']['title']) . "' AND Milestone.project_id='" . $this->request->data['Milestone']['project_id'] . "' AND Milestone.id != '" . $this->request->data['Milestone']['id'] . "'");
                } else {
                    $mlUniqId = md5(uniqid());
                    $this->request->data['Milestone']['uniq_id'] = $mlUniqId;
                    $this->request->data['Milestone']['company_id'] = SES_COMP;
                    $checkDuplicate = $this->Milestone->query("SELECT Milestone.id FROM milestones AS Milestone WHERE Milestone.title='" . addslashes($this->request->data['Milestone']['title']) . "' AND Milestone.project_id='" . $this->request->data['Milestone']['project_id'] . "'");
                }

                if (isset($checkDuplicate[0]['Milestone']['id']) && $checkDuplicate[0]['Milestone']['id']) {
                    $arr['error'] = 1;
                    $arr['msg'] = __('Opps!, Milestone Title already exists..', true);
                    //$this->Session->write("ERROR", "Milestone Title already exists.");
                } else {
                    if ($this->Milestone->save($this->request->data)) {
                        $arr['success'] = 1;
                        $arr['milestone_id'] = $this->Milestone->getLastInsertId();
                        if ($this->request->data['Milestone']['id']) {
                            $arr['msg'] = __('Milestone updated successfully.', true);
                            //$this->Session->write("SUCCESS", "Milestone updated successfully.");
                        } else {
                            $arr['msg'] = __('Milestone added successfully.', true);
                            //$this->Session->write("SUCCESS", "Milestone added successfully.");
                            $this->ProjectUser->query("UPDATE project_users SET dt_visited='" . GMT_DATETIME . "' WHERE user_id=" . SES_ID . " and project_id='" . $this->request->data['Milestone']['project_id'] . "' and company_id='" . SES_COMP . "'");
                        }
                    } else {
                        $arr['error'] = 1;
                        $arr['msg'] = __('Sorry!, We are not able to post this Milestone. Try again.', true);
                        //$this->Session->write("ERROR", "Milestone can't be posted");
                    }
                }
                if (!empty($api_flag)) {
                    return $arr;
                } else {
                echo json_encode($arr);
                }
                exit;
//				if($_SERVER['HTTP_REFERER']==HTTP_ROOT.'dashboard'){
//					$this->redirect(HTTP_ROOT.'dashboard#milestone');exit;
//				}else{
//					$this->redirect($_SERVER['HTTP_REFERER']);exit;
//				}
            }
        }
        $projCond = '';
        $edit_data = array();
        if (isset($mileuniqid) && $mileuniqid) {
            $milearr = $this->Milestone->find('first', array('conditions' => array('Milestone.uniq_id' => $mileuniqid, 'Milestone.company_id' => SES_COMP)));
            $projCond = ' AND `Project`.`id`=' . $milearr['Milestone']['project_id'];
            $this->set('milearr', $milearr);
            $this->set('edit', 'edit');
        }
        if (!empty($api_flag) && $api_flag == 3) {
            $edit_data['Milestone'] = $milearr['Milestone'];
        }
        $this->set('mlstfrom', isset($this->data['mlstfrom']) ? $this->data['mlstfrom'] : '');
        $this->set('mileuniqid', $mileuniqid);

        $prjAllArr = $this->ProjectUser->query("SELECT Project.name,Project.id,Project.uniq_id FROM  `project_users` AS ProjectUser inner JOIN  `projects` AS `Project`  ON (`ProjectUser`.`user_id` = '" . SES_ID . "' AND `ProjectUser`.`company_id` = '" . SES_COMP . "' AND Project.isactive=1 AND `ProjectUser`.`project_id` = `Project`.`id` " . $projCond . ")");
        if (!empty($api_flag) && $api_flag == 3) {
            $edit_data += $prjAllArr;
            return $edit_data;
        }
        $this->set('projArr', $prjAllArr);
        $this->set('projUid', $this->data['projUid']);
    }

    function milestone_restore($uniqid = '', $page = NULL, $api_flag = '') {
        if (!empty($api_flag) && $api_flag == 1) {
            $this->request->data = $uniqid;
            unset($uniqid);
        }
        $uniqid = $uniqid ? $uniqid : $this->request->data['uniqid'];
        if ($uniqid) {
            $this->loadModel('Milestone');
            $qrr = $this->Milestone->query("UPDATE milestones SET isactive = '1',modified='" . GMT_DATETIME . "' WHERE milestones.uniq_id ='" . $uniqid . "'");
            $arr['success'] = 1;
            $arr['msg'] = __('Milestone has been restored.', true);
            //$this->Session->write('SUCCESS',"Milestone has been restored.");
        } else {
            $arr['error'] = 1;
            $arr['msg'] = __('Oops! Error occured in restoration of milestone', true);
            //$this->Session->write('ERROR','Oops! Error occured in restoration of milestone');
        }
        if (!empty($api_flag) && $api_flag == 1) {
            return $arr;
        } else {
        echo json_encode($arr);
        exit;
        }
        //$this->redirect(HTTP_ROOT."milestone");exit;
    }

    function milestone_archive($uniqid = '', $page = NULL, $api_flag = '') {
        if (!empty($api_flag) && $api_flag == 1) {
            $this->request->data = $uniqid;
            unset($uniqid);
        }
        $uniqid = $uniqid ? $uniqid : $this->request->data['uniqid'];
        if ($uniqid) {
            $this->loadModel('Milestone');
            $this->Milestone->query("UPDATE milestones SET isactive=0,modified='" . GMT_DATETIME . "' where uniq_id='" . $uniqid . "'");
            //$this->Session->write('SUCCESS',"Milestone has been completed.");
            $arr['success'] = 1;
            $arr['msg'] = __('Milestone has been completed.', true);
        } else {
            $arr['error'] = 1;
            $arr['msg'] = __('Oops! Error occured in completion of milestone', true);
            //$this->Session->write('ERROR','Oops! Error occured in completion of milestone');
        }
        if (!empty($api_flag) && $api_flag == 1) {
            return $arr;
        } else {
        echo json_encode($arr);
        exit;
        }
        //$this->redirect($_SERVER['HTTP_REFERER']);exit;
    }

    /**
     * @method manage_milestone Manage milestone listing
     * @author GDR
     * @return json 
     */
    function manage_milestone() {
        $milestone_search = $this->data['file_srch'];
        $milestone_search = "AND (Milestone.title LIKE '%$milestone_search%' OR Milestone.description LIKE '%$milestone_search%')";
        $page_limit = MILESTONE_PAGE_LIMIT;
        $page = (isset($this->data['page']) && $this->data['page']) ? $this->data['page'] : 1;
        $limit1 = $page * $page_limit - $page_limit;
        $limit2 = $page_limit;
        $mlsttype = (isset($this->data['mlsttype'])) ? $this->data['mlsttype'] : 1;
        $cond = "Milestone.isactive='" . $mlsttype . "'";
        $projUniq = $this->data['projFil']; // Project Uniq ID
        $projIsChange = $this->data['projIsChange']; // Project Uniq ID
        $this->loadModel('ProjectUser');
        $statuses = $maxstatus_name = $prjct_wrkflw = array();
        if ($projUniq != '' && $projUniq != 'all') {
            $allpj = $_GET['pj'];
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.uniq_id' => $projUniq, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('Project.id', 'Project.short_name', 'ProjectUser.id', 'Project.name', 'Project.workflow_id')));
            if (count($projArr)) {
                $projectId = $projArr['Project']['id'];
                //Updating ProjectUser table to current date-time
                if ($projIsChange != $projUniq) {
                    $ProjectUser['id'] = $projArr['ProjectUser']['id'];
                    $ProjectUser['dt_visited'] = GMT_DATETIME;
                    $this->ProjectUser->save($ProjectUser);
                    $projName = $projArr['Project']['name'];
                }
                $this->loadModel('Status');
                $prjct_wrkflw[$projArr['Project']['id']] = $projArr['Project']['workflow_id'];
                $workflow_id = intval($projArr['Project']['workflow_id']);
                if ($workflow_id > 0) {
                    $status_list = $this->Status->find('all', array('conditions' => array('Status.workflow_id' => $workflow_id), 'order' => 'seq_order DESC', 'limit' => 1));
                    $statuses[] = $status_list[0]['Status']['id'];
                    $status_name = $status_list[0]['Status']['name'];
                    $maxstatus_name[$projArr['Project']['id']] = $status_name;
                }
            }
        }

        $this->loadModel('Milestone');
        $sql = "SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,`Milestone`.`title`,`Milestone`.`project_id`,`Milestone`.`created`,`Milestone`.`modified`,`Milestone`.`uniq_id`,`Milestone`.`isactive`,`Milestone`.`user_id`,COUNT(c.easycase_id) AS totalcases,GROUP_CONCAT(c.easycase_id) AS `caseids`,GROUP_CONCAT(e.legend) AS `legend`,User.name FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN easycases AS e ON (c.easycase_id = e.id) LEFT JOIN users User ON Milestone.user_id=User.id WHERE " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . " " . $milestone_search;
        if ($projUniq && ($projUniq != "all")) {
            $sql .= " AND `Milestone`.`project_id` =" . $projectId . " AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . "  GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC LIMIT $limit1,$limit2";
        } else {
            $allcond = array('conditions' => array('ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP, 'Project.isactive' => 1), 'fields' => array('DISTINCT  Project.id', 'Project.workflow_id'), 'order' => array('ProjectUser.dt_visited DESC'));
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $allProjArr = $this->ProjectUser->find('all', $allcond);
            $ids = array();
            foreach ($allProjArr as $csid) {
                array_push($ids, $csid['Project']['id']);
                $prjct_wrkflw[$csid['Project']['id']] = $csid['Project']['workflow_id'];
            }
            $all_ids = implode(',', $ids);
            $sql .= " AND `Milestone`.`project_id` IN (" . $all_ids . ") AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . " GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC LIMIT $limit1,$limit2";
            /* find out the status list of all project which have highest sequence order */
            if (defined('TSG') && TSG == 1) {
                $statuses_list = $this->Milestone->query("SELECT p.id AS prjct_id,w.id As wrkflow_id,s.id as status_id,s.name  from projects as p LEFT JOIN workflows as w ON p.workflow_id = w.id LEFT JOIN statuses as s ON w.id = s.workflow_id where p.workflow_id !=0 AND w.is_active =1 AND s.id =(Select id from statuses where statuses.workflow_id = w.id ORDER BY statuses.seq_order DESC LIMIT 1 )");
                foreach ($statuses_list as $k => $v) {
                    $statuses[] = $v['s']['status_id'];
                    $maxstatus_name[$v['p']['prjct_id']] = $v['s']['name'];
                }
                array_unique($statuses);
            }
        }
        array_push($statuses, "3", "5");
        $milestones = $this->Milestone->query($sql);
        $tot = $this->Milestone->query("SELECT FOUND_ROWS() as total");

        //Finding number of closed case.
        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $dt = $view->loadHelper('Datetime');
        $frmt = $view->loadHelper('Format');
        foreach ($milestones as $key => $milestone) {
            if ($milestone['0']['legend']) {
                $legends = explode(",", $milestone['0']['legend']);
                //if(in_array(3,$legends)) {
                $close_cnt = 0;
                $resolve_cnt = 0;
                foreach ($legends as $value) {
                    foreach ($statuses as $sl => $sv) {
                        if ($value == $sv && $value != 5) {
                            $close_cnt = $close_cnt + 1;
                            break;
                        } else if ($value == 5) {
                            $resolve_cnt = $resolve_cnt + 1;
                            break;
                        }
                    }
                }
                $milestones[$key]['0']['closed'] = $close_cnt;
                $milestones[$key]['0']['resolved'] = $resolve_cnt;
            } else {
                $milestones[$key]['0']['closed'] = 0;
                $milestones[$key]['0']['resolved'] = 0;
            }
            $date = $milestone['Milestone']['created'];
            if ($milestone['Milestone']['modified']) {
                $date = $milestone['Milestone']['modified'];
            }
            $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
            $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $date, "date");
            $locDT = $dt->dateFormatOutputdateTime_day($updated, $curCreated, '', 1);
            $crted = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $milestone['Milestone']['created'], "date");
            $crt_dt = $dt->dateFormatOutputdateTime_day($crted, $curCreated, '', 1);
            $milestones[$key]['Milestone']['locDT'] = $locDT;
            $milestones[$key]['Milestone']['closed'] = $milestones[$key]['0']['closed'];
            $milestones[$key]['Milestone']['resolved'] = $milestones[$key]['0']['resolved'];
            $milestones[$key]['Milestone']['totalcases'] = $milestones[$key]['0']['totalcases'];
            //$milestones[$key]['Milestone']['hrSpent'] = $milestones[$key]['0']['hours_spent'];
            $milestones[$key]['Milestone']['crtUser'] = '<i>' . __('Created by', true) . ':</i> ' . $frmt->splitwithspace($milestones[$key]['User']['name']) . ' ' . __('on', true) . ' ' . $crt_dt;
        }
        //echo "<pre>";print_r($milestones);exit;
        $pgShLbl = $frmt->pagingShowRecords($tot[0][0]['total'], $page_limit, $page);
        $mlstArr['pgShLbl'] = $pgShLbl;
        $mlstArr['milestoneAll'] = $milestones;
        $mlstArr['caseCount'] = $tot[0][0]['total'];
        $mlstArr['csPage'] = $page;
        $mlstArr['page_limit'] = $page_limit;
        $mlstArr['mlsttype'] = $mlsttype;
        $mlstArr['projName'] = $projName;
        $mlstArr['projUniq'] = $projUniq;
        $mlstArr['file_srch'] = $this->data['file_srch'];
        $mlstArr['max_sts_names'] = $maxstatus_name;
        $mlstArr['prjct_wrkflw'] = $prjct_wrkflw;
        $this->set('resMilestone', json_encode($mlstArr));
        //$this->set("milestones",$milestones);
        //$this->set('caseCount',$tot[0][0]['total']);
        //$this->set('page_limit',$page_limit);
        //$this->set('casePage',$page);
        //$this->set('pageprev',$pageprev);
        //$this->set('type',$type);
        //$this->set('projId',$allpj);
        //$this->set('projName',$projName);
    }

    function milestone($type = NULL) {
        $page_limit = 5;
        $page = 1;
        $pageprev = 1;
        if (isset($_GET['page']) && $_GET['page']) {
            $page = $_GET['page'];
        }
        if (isset($this->data['page']) && $this->data['page']) {
            $page = $this->data['page'];
        }
        $limit1 = $page * $page_limit - $page_limit;
        $limit2 = $page_limit;

        $cond = "Milestone.isactive='1'";
        if ($type == "completed" || (isset($this->data['mlsttype']) && $this->data['mlsttype'] == 0)) {
            $cond = "Milestone.isactive='0'";
        }

        $is_ajax = 0;
        $this->loadModel('ProjectUser');
        if ($_GET['pj'] && $_GET['pj'] != 'all') {
            $allpj = $_GET['pj'];
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.uniq_id' => $allpj, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('Project.id', 'Project.short_name', 'ProjectUser.id')));
            if (count($projArr)) {
                $projectId = $projArr['Project']['id'];
                //Updating ProjectUser table to current date-time
                if ($projIsChange != $projUniq) {
                    $ProjectUser['id'] = $projArr['ProjectUser']['id'];
                    $ProjectUser['dt_visited'] = GMT_DATETIME;
                    $this->ProjectUser->save($ProjectUser);
                }
            }
        } else if ($_GET['pj'] && $_GET['pj'] == 'all') {
            $allpj = 'all';
        }
        if (isset($this->params['data']['project_id'])) {
            $is_ajax = 1;
            $this->layout = "ajax";
            if ($this->params['data']['project_id'] !== 'all') {
                $allpj = $projectId = $this->params['data']['project_id'];
                $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
                $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.id' => $allpj, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('Project.id', 'Project.short_name', 'Project.name', 'ProjectUser.id')));
                if (count($projArr)) {
                    //Updating ProjectUser table to current date-time
                    $ProjectUser['id'] = $projArr['ProjectUser']['id'];
                    $ProjectUser['dt_visited'] = GMT_DATETIME;
                    $this->ProjectUser->save($ProjectUser);
                    $projName = $projArr['Project']['name'];
                }
            } else {
                $allpj = "all";
                $projName = 'All';
            }
        } else if ($_COOKIE['ALL_PROJECT'] == 'all') {
            $allpj = $_COOKIE['ALL_PROJECT'];
            $projName = 'All';
        } else {
            $allpj = $projectId = $GLOBALS['getallproj'][0]['Project']['id'];
            //$allpj = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
            $projName = $GLOBALS['getallproj'][0]['Project']['name'];
//			$getallproj = $this->ProjectUser->query("SELECT DISTINCT Project.id,Project.uniq_id,Project.name FROM project_users AS ProjectUser,projects AS Project WHERE Project.id= ProjectUser.project_id AND ProjectUser.user_id=".SES_ID." AND Project.isactive='1' AND Project.company_id='".SES_COMP."' ORDER BY ProjectUser.dt_visited DESC LIMIT 1");
//			if(count($getallproj) == 1){
//				$allpj = $getallproj[0]['Project']['uniq_id'];
//				$projectId = $getallproj[0]['Project']['id'];
//			} else {
//				$allpj = "all";
//			}
        }

        $this->loadModel('Milestone');
        $sql = "SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,`Milestone`.`title`,`Milestone`.`project_id`,`Milestone`.`created`,`Milestone`.`modified`,`Milestone`.`uniq_id`,`Milestone`.`isactive`,`Milestone`.`user_id`,COUNT(c.easycase_id) AS totalcases,GROUP_CONCAT(c.easycase_id) AS `caseids`,GROUP_CONCAT(e.legend) AS `legend` FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN easycases AS e ON (c.easycase_id = e.id) WHERE " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP;
        if ($allpj != "all") {
            $sql .= " AND `Milestone`.`project_id` =" . $projectId . " AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . "  GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC LIMIT $limit1,$limit2";
        } else {
            $allcond = array('conditions' => array('ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP, 'Project.isactive' => 1), 'fields' => array('DISTINCT  Project.id'), 'order' => array('ProjectUser.dt_visited DESC'));
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $allProjArr = $this->ProjectUser->find('all', $allcond);
            $ids = array();
            foreach ($allProjArr as $csid) {
                array_push($ids, $csid['Project']['id']);
            }
            $all_ids = implode(',', $ids);
            $sql .= " AND `Milestone`.`project_id` IN (" . $all_ids . ") AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . " GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC LIMIT $limit1,$limit2";
        }

        $milestones = $this->Milestone->query($sql);
        $tot = $this->Milestone->query("SELECT FOUND_ROWS() as total");

        //Finding number of closed case.
        foreach ($milestones as $key => $milestone) {
            if ($milestone['0']['legend']) {
                $legends = explode(",", $milestone['0']['legend']);
                if (in_array(3, $legends)) {
                    $cnt = 0;
                    foreach ($legends as $value) {
                        if ($value == 3) {
                            $cnt = $cnt + 1;
                        }
                    }
                    $milestones[$key]['0']['closed'] = $cnt;
                } else {
                    $milestones[$key]['0']['closed'] = 0;
                }
            } else {
                $milestones[$key]['0']['closed'] = 0;
            }
        }
        $this->set("milestones", $milestones);
        $this->set('caseCount', $tot[0][0]['total']);
        $this->set('page_limit', $page_limit);
        $this->set('casePage', $page);
        $this->set('pageprev', $pageprev);
        $this->set('type', $type);
        $this->set('projId', $allpj);
        $this->set('projName', $projName);

        if ($is_ajax) {
            $this->render('listing');
        }
        //print '<pre>';print_r($milestones);exit;
    }

    /**
     * @method milestonelist Kanban view of Milestone 
     * @return json 
     */
    function milestonelist() {
        
    }

    function ajax_milestonelist() {
        $this->loadModel('Easycase');
        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $dt = $view->loadHelper('Datetime');
        $cq = $view->loadHelper('Casequery');
        $frmt = $view->loadHelper('Format');
        $milestone_search = $this->params['data']['file_srch'];
        $caseMenuFilters = $this->data['caseMenuFilters'];
        if ($caseMenuFilters) {
            setcookie('CURRENT_FILTER', $caseMenuFilters, COOKIE_REM, '/', DOMAIN_COOKIE, false, false);
        } else {
            setcookie('CURRENT_FILTER', $caseMenuFilters, COOKIE_REM, '/', DOMAIN_COOKIE, false, false);
        }
        $data = $this->Easycase->ajax_milestonelist($this->data, $frmt, $dt, $tz, $cq, $milestone_search);
//              
//                  pr($data);
//                exit;
        $this->set('resCaseProj', json_encode($data));
//		echo json_encode($data);exit;
    }

    function milestone_list() {
        $this->layout = 'ajax';
        $projuid = $this->data['project_id'];
        $prj_cls = ClassRegistry::init('Project');
        $project_dtls = $prj_cls->find('first', array('conditions' => array('Project.uniq_id' => $projuid), 'fields' => 'Project.id'));
        $milestones = $this->Milestone->find('list', array('conditions' => array('project_id' => $project_dtls['Project']['id'], 'isactive' => 1), 'order' => 'title ASC'));
        if ($milestones) {
            echo json_encode($milestones);
            exit;
        } else {
            echo '';
            exit;
        }
    }

    function moveTaskMilestone() {
        $this->layout = 'ajax';
        $taskid = $this->data['taskid'];
        $mlstid = $this->data['mlstid'];
        $project_id = $this->data['project_id'];
        if (!$mlstid) {
            $emcls = ClassRegistry::init('EasycaseMilestone');
            $mlstdetails = $emcls->find('first', array('conditions' => array('easycase_id' => $taskid, 'project_id' => $project_id)));
            if ($mlstdetails) {
                $mlstid = $mlstdetails['EasycaseMilestone']['milestone_id'];
            }
        }

        $milestones = $this->Milestone->find('all', array('conditions' => array('project_id' => $project_id, 'isactive' => 1), 'order' => 'title ASC'));
        $this->set('milestones', $milestones);
        $this->set('mlst_id', $taskid);
        $this->set('project_id', $project_id);
        $this->set('mlstid', $mlstid);
        $this->set('task_no', $this->data['task_no']);
    }

    function removeTaskMilestone() {
        $this->layout = 'ajax';
        $taskid = $this->data['taskid'];
        $mlstid = $this->data['mlstid'];
        $project_id = $this->data['project_id'];
        $this->loadModel('EasycaseMilestone');
        $this->EasycaseMilestone->deleteAll(array('easycase_id' => $taskid));
        echo 'success';
        exit;
    }

    function switchTaskToMilestone() {
        $this->layout = 'ajax';
        $old_mlst_id = $this->data['ext_mlst_id'];
        $project_id = $this->data['project_id'];
        $taskid = $this->data['taskid'];
        $curr_mlst_id = $this->data['curr_mlst_id'];
        $em_cls = ClassRegistry::init('EasycaseMilestone');
        if (!is_numeric($project_id)) {
            $this->loadModel('Project');
            $conditons = array('uniq_id' => $project_id);
            $fields = array('id');
            $prjctId = $this->Project->getProjectFields($conditons, $fields);
            $project_id = $prjctId['Project']['id'];
        }
        if ($taskid == 0) {
            $task_uniq_id = $this->data['taskuid'];
            $this->loadModel('Easycase');
            $data = $this->Easycase->find('first', array('conditions' => array('Easycase.uniq_id' => $task_uniq_id, 'Easycase.project_id' => $project_id)));
            $taskid = $data['Easycase']['id'];
        }
        if ($old_mlst_id && $old_mlst_id != 'null') {
            $em_cls->deleteAll(array('project_id' => $project_id, 'milestone_id' => $old_mlst_id, 'easycase_id' => $taskid));
        }
        $arr = null;
        $arr['milestone_id'] = $curr_mlst_id;
        $arr['easycase_id'] = $taskid;
        $arr['project_id'] = $project_id;
        $arr['user_id'] = SES_ID;
        $arr['dt_created'] = GMT_DATETIME;
        if ($em_cls->saveAll($arr)) {
            echo 'success';
            exit;
        } else {
            echo 'error';
            exit;
        }
    }

    function saveMilestoneTitle() {
        $this->layout = 'ajax';
        if ($this->request->data['mid']) {
            $milearr = $this->Milestone->find('first', array('conditions' => array('Milestone.id' => $this->request->data['mid'])));
            if ($milearr) {
                $milearr['Milestone']['title'] = trim($this->request->data['title']);
            }
            $this->Milestone->save($milearr);
        }
        echo 1;
        exit;
    }

    function assign_to_user($miles = null, $api_flag = '') {
        $this->layout = 'ajax';
        if (!empty($miles) && !empty($api_flag) && $api_flag == 1) {
            $this->request->data = $miles;
            unset($miles);
        }
        $mileuniqid = $this->request->data['mileuniqid'];
        $this->loadModel('ProjectUser');
        $this->loadModel('Project');
        $this->loadModel('User');
        if (!empty($this->request->data['Milestone']) && !$this->request->data['mileuniqid']) {
            $assign_id = !empty($this->request->data['Milestone']['assign_to']) ? $this->request->data['Milestone']['assign_to'] : '';
            $id = $this->request->data['Milestone']['id'];
            $this->Milestone->query("UPDATE milestones SET assign_id='" . $assign_id . "' WHERE id= $id");
            if (!empty($api_flag) && $api_flag == 1) {
                $arr['status'] = 1;
                $arr['msg'] = 'User assigned successfully.';
                return $arr;
		}
            $redict = !empty($this->request->data['Milestone']['urlname']) ? $this->request->data['Milestone']['urlname'] : 'milestonelist';
            $this->redirect("/dashboard#" . $redict);
        }

        $projCond = '';
        if (isset($mileuniqid) && $mileuniqid) {
            $milearr = $this->Milestone->find('first', array('conditions' => array('Milestone.uniq_id' => $mileuniqid, 'Milestone.company_id' => SES_COMP)));
            $projCond = ' AND `Project`.`id`=' . $milearr['Milestone']['project_id'];
            $this->set('milearr', $milearr);
            $this->set('edit', 'edit');
        }
        $this->set('mlstfrom', isset($this->data['mlstfrom']) ? $this->data['mlstfrom'] : '');
        $this->set('mileuniqid', $mileuniqid);

        $prjAllArr = $this->ProjectUser->query("SELECT Project.name,Project.id,Project.uniq_id,Project.user_id FROM  `project_users` AS ProjectUser inner JOIN  `projects` AS `Project`  ON (`ProjectUser`.`user_id` = '" . SES_ID . "' AND `ProjectUser`.`company_id` = '" . SES_COMP . "' AND Project.isactive=1 AND `ProjectUser`.`project_id` = `Project`.`id` " . $projCond . ")");
        $this->set('projArr', $prjAllArr);
        // pr($prjAllArr); exit;
        $projectuser = $this->ProjectUser->find('all', array('conditions' => array('project_id' => $prjAllArr[0]['Project']['id'])));
        $user_list = array();
        foreach ($projectuser as $key => $value) {
            $user = $this->User->find('first', array('conditions' => array('id' => $value['ProjectUser']['user_id'])));
            if (!empty($user)) {
                $user_list[$value['ProjectUser']['user_id']] = $user['User']['name'];
            }
        }
        $this->set(compact('user_list'));
        $this->set('projUid', $this->data['projUid']);
    }

    function user_data() {
        if (!empty($this->request->data['project_id'])) {
            $project_id = $this->request->data['project_id'];
            $projectuser = $this->ProjectUser->find('list', array('fields' => array('ProjectUser.id', 'ProjectUser.user_id'), 'conditions' => array('ProjectUser.project_id' => $project_id, 'ProjectUser.company_id' => SES_COMP)));
            $user_data = implode(',', $projectuser);
            $user_list = $this->User->find('list', array('fields' => array('User.id', 'User.name'), 'conditions' => array("User.id IN ($user_data)")));
            $str = '';
            $str .= "<option>Choose One</option>";
            foreach ($user_list as $key => $val) {
                $str .= "<option value=$key>" . $val . "</option>";
}
            echo $str;
            exit;
        }
    }

    function api_milestone_menu($proId = '') {
        $this->layout = 'ajax';
        $this->loadModel('EasycaseMilestone');
        $this->loadModel('Easycase');
        $project_id = !empty($this->request['data']['project_id']) ? $this->request['data']['project_id'] : $proId;
        if (isset($project_id) && !empty($project_id)) {
            $query = "SELECT * FROM milestones AS Milestone,project_users AS ProjectUser WHERE Milestone.project_id=ProjectUser.project_id AND ProjectUser.user_id='" . SES_ID . "' AND Milestone.company_id='" . SES_COMP . "' AND Milestone.project_id='" . $project_id . "' ORDER BY Milestone.modified DESC";
            $milestone_all = $this->Milestone->query($query);
            $in_con = array();
            if (!empty($milestone_all)) {
                foreach ($milestone_all as $key => $val) {
                    $count = $this->EasycaseMilestone->find('count', array('conditions' => array('EasycaseMilestone.milestone_id' => $val['Milestone']['id'], 'EasycaseMilestone.project_id' => $project_id),
                        'joins' => array(
                            array('table' => 'easycases',
                                'alias' => 'Easycase',
                                'type' => 'inner',
                                'conditions' => array('EasycaseMilestone.easycase_id = Easycase.id', 'Easycase.istype' => 1)))));
                    $milestone_all[$key]['Milestone']['no_task'] = $count;
                }
            }
            $tasks = $this->Easycase->find('all', array('fields' => array('Easycase.id', 'Easycase.project_id'), 'conditions' => array('Easycase.project_id' => $project_id, 'Easycase.istype' => 1)));
            $default_task = array();
            $default_count = 0;
            foreach ($tasks as $tkey => $tval) {
                $easycase_milestone = $this->EasycaseMilestone->find('first', array('conditions' => array('EasycaseMilestone.easycase_id' => $tval['Easycase']['id'], 'EasycaseMilestone.project_id' => $tval['Easycase']['project_id'])));
                if (empty($easycase_milestone)) {
                    $default_count++;
                }
            }
            if (empty($key)) {
                $key = 0;
            }
            if (!empty($default_count)) {
                $milestone_all[$key + 1]['Milestone']['no_task'] = $default_count;
                $milestone_all[$key + 1]['Milestone']['title'] = 'Default Task Group';
                $milestone_all[$key + 1]['Milestone']['uniq_id'] = 'default';
                $milestone_all[$key + 1]['Milestone']['project_id'] = $project_id;
                $milestone_all[$key + 1]['Milestone']['user_id'] = SES_ID;
                $milestone_all[$key + 1]['Milestone']['company_id'] = SES_COMP;
            }
            if (!empty($proId)) {
                return $milestone_all;
            }
            $this->set('milestone_all', $milestone_all);
            $this->set('pjid', $this->request['data']['project_id']);
        }
    }

    function api_manage_milestone($request = null) {
        $milestone_search = $request['file_srch'];
        $milestone_search = "AND (Milestone.title LIKE '%$milestone_search%' OR Milestone.description LIKE '%$milestone_search%')";
        $page_limit = MILESTONE_PAGE_LIMIT;
        $page = (isset($request['page']) && $request['page']) ? $request['page'] : 1;
        $limit1 = $page * $page_limit - $page_limit;
        $limit2 = $page_limit;
        $mlsttype = (isset($request['mlsttype'])) ? $request['mlsttype'] : 1;
        $cond = "Milestone.isactive='" . $mlsttype . "'";
        $projUniq = $request['projFil']; // Project Uniq ID
        $projIsChange = $request['projIsChange']; // Project Uniq ID
        $this->loadModel('ProjectUser');
        $statuses = $maxstatus_name = $prjct_wrkflw = array();
        if ($projUniq != '' && $projUniq != 'all') {
            $allpj = $_GET['pj'];
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $projArr = $this->ProjectUser->find('first', array('conditions' => array('Project.uniq_id' => $projUniq, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('Project.id', 'Project.short_name', 'ProjectUser.id', 'Project.name', 'Project.workflow_id')));
            if (count($projArr)) {
                $projectId = $projArr['Project']['id'];
                //Updating ProjectUser table to current date-time
                if ($projIsChange != $projUniq) {
                    $ProjectUser['id'] = $projArr['ProjectUser']['id'];
                    $ProjectUser['dt_visited'] = GMT_DATETIME;
                    $this->ProjectUser->save($ProjectUser);
                    $projName = $projArr['Project']['name'];
                }
                $this->loadModel('Status');
                $prjct_wrkflw[$projArr['Project']['id']] = $projArr['Project']['workflow_id'];
                $workflow_id = intval($projArr['Project']['workflow_id']);
                if ($workflow_id > 0) {
                    $status_list = $this->Status->find('all', array('conditions' => array('Status.workflow_id' => $workflow_id), 'order' => 'seq_order DESC', 'limit' => 1));
                    $statuses[] = $status_list[0]['Status']['id'];
                    $status_name = $status_list[0]['Status']['name'];
                    $maxstatus_name[$projArr['Project']['id']] = $status_name;
                }
            }
        }
        $this->loadModel('Milestone');
        $sql = "SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,`Milestone`.`title`,`Milestone`.`assign_id`,`Milestone`.`project_id`,`Milestone`.`created`,`Milestone`.`modified`,`Milestone`.`uniq_id`,`Milestone`.`isactive`,`Milestone`.`user_id`,COUNT(c.easycase_id) AS totalcases,GROUP_CONCAT(c.easycase_id) AS `caseids`,GROUP_CONCAT(e.legend) AS `legend`,User.name FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN easycases AS e ON (c.easycase_id = e.id) LEFT JOIN users User ON Milestone.user_id=User.id WHERE " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . " " . $milestone_search;
        if ($projUniq && ($projUniq != "all")) {
            $sql .= " AND `Milestone`.`project_id` =" . $projectId . " AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . "  GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC";
        } else {
            $allcond = array('conditions' => array('ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP, 'Project.isactive' => 1), 'fields' => array('DISTINCT  Project.id', 'Project.workflow_id'), 'order' => array('ProjectUser.dt_visited DESC'));
            $this->ProjectUser->unbindModel(array('belongsTo' => array('User')));
            $allProjArr = $this->ProjectUser->find('all', $allcond);
            $ids = array();
            foreach ($allProjArr as $csid) {
                array_push($ids, $csid['Project']['id']);
                $prjct_wrkflw[$csid['Project']['id']] = $csid['Project']['workflow_id'];
            }
            $all_ids = implode(',', $ids);
            $sql .= " AND `Milestone`.`project_id` IN (" . $all_ids . ") AND " . $cond . " AND `Milestone`.`company_id` = " . SES_COMP . " GROUP BY Milestone.id ORDER BY `Milestone`.`modified` DESC ";
            /* find out the status list of all project which have highest sequence order */
            if (defined('TSG') && TSG == 1) {
                $statuses_list = $this->Milestone->query("SELECT p.id AS prjct_id,w.id As wrkflow_id,s.id as status_id,s.name  from projects as p LEFT JOIN workflows as w ON p.workflow_id = w.id LEFT JOIN statuses as s ON w.id = s.workflow_id where p.workflow_id !=0 AND w.is_active =1 AND s.id =(Select id from statuses where statuses.workflow_id = w.id ORDER BY statuses.seq_order DESC LIMIT 1 )");
                foreach ($statuses_list as $k => $v) {
                    $statuses[] = $v['s']['status_id'];
                    $maxstatus_name[$v['p']['prjct_id']] = $v['s']['name'];
                }
                array_unique($statuses);
            }
        }
        array_push($statuses, "3", "5");
        $milestones = $this->Milestone->query($sql);
        $tot = $this->Milestone->query("SELECT FOUND_ROWS() as total");

        //Finding number of closed case.
        $view = new View($this);
        $tz = $view->loadHelper('Tmzone');
        $dt = $view->loadHelper('Datetime');
        $frmt = $view->loadHelper('Format');
        foreach ($milestones as $key => $milestone) {
            if ($milestone['0']['legend']) {
                $legends = explode(",", $milestone['0']['legend']);
                //if(in_array(3,$legends)) {
                $close_cnt = 0;
                $resolve_cnt = 0;
                foreach ($legends as $value) {
                    foreach ($statuses as $sl => $sv) {
                        if ($value == $sv && $value != 5) {
                            $close_cnt = $close_cnt + 1;
                            break;
                        } else if ($value == 5) {
                            $resolve_cnt = $resolve_cnt + 1;
                            break;
                        }
                    }
                }
                $milestones[$key]['0']['closed'] = $close_cnt;
                $milestones[$key]['0']['resolved'] = $resolve_cnt;
            } else {
                $milestones[$key]['0']['closed'] = 0;
                $milestones[$key]['0']['resolved'] = 0;
            }
            $date = $milestone['Milestone']['created'];
            if ($milestone['Milestone']['modified']) {
                $date = $milestone['Milestone']['modified'];
            }
            $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
            $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $date, "date");
            $locDT = $dt->dateFormatOutputdateTime_day($updated, $curCreated, '', 1);
            $crted = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $milestone['Milestone']['created'], "date");
            $crt_dt = $dt->dateFormatOutputdateTime_day($crted, $curCreated, '', 1);
            $milestones[$key]['Milestone']['locDT'] = $locDT;
            $milestones[$key]['Milestone']['closed'] = $milestones[$key]['0']['closed'];
            $milestones[$key]['Milestone']['resolved'] = $milestones[$key]['0']['resolved'];
            $milestones[$key]['Milestone']['totalcases'] = $milestones[$key]['0']['totalcases'];
            //$milestones[$key]['Milestone']['hrSpent'] = $milestones[$key]['0']['hours_spent'];
            $milestones[$key]['Milestone']['crtUser'] = $frmt->splitwithspace($milestones[$key]['User']['name']) . ' ' . __('on', true) . ' ' . $crt_dt;
        }
        //echo "<pre>";print_r($milestones);exit;
        $pgShLbl = $frmt->pagingShowRecords($tot[0][0]['total'], $page_limit, $page);
        $mlstArr['pgShLbl'] = $pgShLbl;
        $mlstArr['milestoneAll'] = $milestones;
        $mlstArr['caseCount'] = $tot[0][0]['total'];
        $mlstArr['csPage'] = $page;
        $mlstArr['page_limit'] = $page_limit;
        $mlstArr['mlsttype'] = $mlsttype;
        $mlstArr['projName'] = $projName;
        $mlstArr['projUniq'] = $projUniq;
        $mlstArr['file_srch'] = $request['file_srch'];
        $mlstArr['max_sts_names'] = $maxstatus_name;
        $mlstArr['prjct_wrkflw'] = $prjct_wrkflw;
        return $mlstArr;
    }

}
