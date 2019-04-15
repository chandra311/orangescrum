<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');

/**
 * CakePHP restsController
 * @author Star 1
 */
class restsController extends AppController {

    public $name = 'Rests';
    public $uses = array();
    public $components = array('Auth', 'Session', 'Email', 'Cookie', 'Tmzone', 'Image', 'Format', 'Postcase', 'Security', 'RequestHandler');
    public $helpers = array('Html', 'Form');
    public $format = '';
    public $company_id = 0;
    public $user_id = 0;
    static $status = array(
        'key_missing' => array("status" => 601, 'message' => "Api key can't be empty"),
        'title_missing' => array("status" => 601, 'message' => "Task title can't be empty"),
        'user_missing' => array("status" => 602, 'message' => 'User not exists. Please check the API key'),
        'project_missing' => array("status" => 603, 'message' => 'Project not exists. Please check the API key'),
        'invalid_api_key' => array("status" => 603, 'message' => 'Invalid API key'),
        'empty_request' => array("status" => 604, 'message' => "Empty Request"),
    );

    public function beforeFilter() {
        $this->layout = '';
        $this->Auth->allow('edit', 'delete', 'add', 'view', 'create_task', 'login', 'getTaskData', 'fileupload', 'fileremove', 'casePost', 'getCompanyAndProjects', 'getProjectsAndTasks', 'getAllTasks', 'getProjectTasks', 'getTaskDetails', 'company_statistics');

        parent::beforeFilter();
        $this->Security->unlockedActions = array('edit', 'delete', 'add', 'view');
        $this->format = $this->params['ext'];
    }

    public function index($id) {
        #pr($this->params);
        exit;
    }

    public function add() {
        exit;
        echo $this->format;
        if ($this->request->is('post')) {
            pr($this->params);
            exit;
            /* $this->Phone->create();
              if ($this->Phone->save($this->request->data)) {
              $this->Session->setFlash(__('The phone has been saved.'));
              return $this->redirect(array('action' => 'index'));
              }
              $this->Session->setFlash(__('Unable to add your phone.')); */
        }
        echo 'out';
        exit;
    }

    public function create_task() {
        #pr($this->params);exit;
        #pr($this->data);exit;
        try {
            $this->loadModel('CompanyApi');
            $this->loadModel('Project');
            $this->loadModel('Easycase');
            $this->loadModel('User');

            #$query = $this->params->query;
            $query = $this->data;

            if (empty($query)) {
                throw new Exception('empty_request');
            }


            $api_key = trim($query['api']);
            if ($api_key == '') {
                throw new Exception("key_missing");
            }

            $title = trim($query['title']);
            if ($title == '') {
                throw new Exception("title_missing");
            }

            $api = $this->CompanyApi->find('first', array('conditions' => array('api_key' => $api_key)));
            #pr($api);exit;
            if (is_array($api) && count($api) > 0 && $api['CompanyApi']['is_active'] == 1) {

                $company_id = $api['CompanyApi']['company_id'];
                $project_id = $api['CompanyApi']['project_id'];
                $user_id = $api['CompanyApi']['user_id'];

                $this->User->recursive = false;
                $user = $this->User->find('first', array('conditions' => array('id' => $user_id)));
                #pr($user);exit;
                if (intval($user_id) > 0 && is_array($user) && count($user) > 0) {
                    // no action
                } else {
                    throw new Exception('user_missing');
                }
                $this->Project->recursive = false;
                $project = $this->Project->find('first', array('conditions' => array('Project.id' => $project_id, 'Project.company_id' => $company_id)));
                #pr($project);exit;
                if (is_array($project) && count($project) > 0 && $project['Project']['isactive'] == 1) {
                    $CS_project_id = $project['Project']['uniq_id'];
                    $files = $this->params['form'];

                    #pr(filesize($files['file1']['tmp_name']));exit;
                    // remotely post the information to the server
                    $link = HTTP_ROOT . 'easycases/fileupload';
                    $allFiles = null;
                    $data = null;
                    $httpSocket = new HttpSocket();
                    if (is_array($files) && count($files) > 0) {
                        foreach ($files as $key => $val) {
                            $data = array(
                                'files' => $val,
                                'api_user_id' => $user_id,
                                'api_company_id' => $company_id,
                                'api_project_id' => $project_id,
                            );

                            $response = $httpSocket->post($link, $data);
                            $FilesResponse = json_decode($response->body, true);
                            $allFiles[] = $FilesResponse['filename'];
                        }
                    }

                    #pr($query);exit;
                    $arr = array(
                        'CS_project_id' => $CS_project_id,
                        'pid' => $project_id,
                        'CS_istype' => '1',
                        "CS_title" => trim($query['title']),
                        'CS_type_id' => '2',
                        'CS_priority' => '2',
                        'CS_message' => trim($query['description']),
                        'CS_assign_to' => $user_id,
                        'CS_due_date' => trim($query['due_date']),
                        'CS_milestone' => trim($query['milestone']),
                        'postdata' => 'Post',
                        'pagename' => 'mydashboard',
                        'CS_id' => '0',
                        'datatype' => '0',
                        'CS_legend' => '1',
                        'prelegend' => '',
                        'hours' => '0',
                        'estimated_hours' => trim($query['estimated_hour']),
                        'completed' => '0',
                        'taskid' => '0',
                        'task_uid' => '',
                        'editRemovedFile' => '',
                        'is_client' => '0',
                        'allFiles' => $allFiles,
                        'timelog' => false,
                        'CS_user_id' => $user_id,
                        'api_user_id' => $user_id,
                        'api_company_id' => $company_id,
                        'api_project_id' => $project_id,
                    );

                    #pr(json_encode($arr));exit;
                    $value = $this->Postcase->casePosting($arr);
                    $status = json_decode($value, true);
                    
                    $message = "Task created successfully";
                } else {
                    throw new Exception('project_missing');
                }
            } else {
                throw new Exception('invalid_api_key');
            }

            #pr($api);exit;
            #pr($this->params->query);exit;
        } catch (Exception $exc) {
            #echo $exc->getTraceAsString();
            $stat = $exc->getMessage();
            if (isset($this->status[$stat]) && is_array($this->status[$stat])) {
                header('HTTP/1.1 ' . $this->status[$stat]['status'] . ' Unauthorized', true, $this->status[$stat]['status']);
                $message = $this->status[$stat]['message'];
            } else {
                header('HTTP/1.1 404 Unauthorized', true, 404);
                $message = $stat;
            }

            echo json_encode(array('success' => 0, 'message' => $message));
            exit;
        }
        echo json_encode(array('success' => 1, 'message' => $message));
        exit;
    }

    function settings() {
        $this->loadModel('CompanyApi');
        $company_id = intval(SES_COMP) > 0 ? SES_COMP : 1;
        $mode = 'generate';
        if (isset($this->data['mode'])) {
            if (trim($this->data['mode']) == 'delete') {
                $this->CompanyApi->id = $this->data['id'];
                if ($this->CompanyApi->delete()) {
                    echo json_encode(array('success' => 1, 'message' => "Api key deleted successfully"));
                } else {
                    echo json_encode(array('success' => 0, 'message' => "Api key not deleted"));
                }
                exit;
            }
        } else if (isset($this->data['Api'])) {
            $post_data = $this->data;
            if (is_array($post_data['Api'])) {
                foreach ($post_data['Api'] as $key => $val) {
                    $data = array(
                        'id' => $key,
                        'is_active' => (isset($val['is_active']) ? 1 : 0)
                    );

                    $this->CompanyApi->save($data);
                }
            }
            $this->Session->write("SUCCESS", "Api key updated successfully");
            $this->redirect(HTTP_ROOT . 'api-settings');
            exit;
        }

        $params = array(
            'conditions' => array('company_id' => $company_id),
            "fields" => array('CompanyApi.*',
                '(SELECT CONCAT_WS(" ",users.name,users.last_name) FROM users WHERE id=CompanyApi.user_id) AS username',
                '(SELECT projects.name FROM projects WHERE id=CompanyApi.project_id) AS projectname')
        );
        $data = $this->CompanyApi->find('all', $params);
        #pr($data);exit;
        $this->set('data', $data);
        $this->set('mode', $mode);
    }

    function ajax_generate_key($api_id = '') {
        $this->loadModel('ProjectUser');
        $this->loadModel('CompanyApi');
        if (isset($this->data['CompanyApi'])) {
            #pr($this->data['CompanyApi']);exit;

            $data = array(
                'company_id' => SES_COMP,
                'id' => $this->data['CompanyApi']['api_id'],
                'api_key' => $this->data['CompanyApi']['api_key'],
                'user_id' => $this->data['CompanyApi']['api_users'],
                'project_id' => $this->data['CompanyApi']['project_id'],
                    #'is_active' => 1,
            );
            $params = array('conditions' => array(
                    'user_id' => $data['user_id'],
                    'project_id' => $data['project_id'],
                    'is_active' => 1
            ));
            $exist = $this->CompanyApi->find("first", $params);
            if (is_array($exist) && count($exist) > 0) {
                echo json_encode(array('success' => 0, 'message' => "Api key for this user exist"));
                exit;
            }
            if ($data['id'] > 0) {
                unset($data['api_key']);
                unset($data['project_id']);
            }
            #pr($data);exit;
            $this->CompanyApi->save($data);
            #$this->Session->write("SUCCESS", "Api key generated successfully");
            #$this->redirect(HTTP_ROOT . 'api-settings');
            echo json_encode(array('success' => 1, 'message' => "Api key generated successfully"));
            exit;
        }

        $this->layout = 'ajax';
        $projCond = '';
        #$api_id = '';
        $this->set('mode', 'add');
        if (isset($api_id) && $api_id > 0) {
            $apiArr = $this->CompanyApi->find('first', array('conditions' => array('CompanyApi.id' => $api_id)));
            if ($apiArr['CompanyApi']['project_id'] > 0) {
                $projCond = " AND `Project`.`id`='" . $apiArr['CompanyApi']['project_id'] . "'";
            }
            #pr($apiArr);exit;
            $this->set('apiarr', $apiArr);
            $this->set('mode', 'edit');
            $this->set('api_key', $apiArr['CompanyApi']['api_key']);
        } else {
            $this->set('api_key', $this->Format->generateUniqNumber());
        }

        $sql = "SELECT Project.name,Project.id,Project.uniq_id "
                . "FROM  `project_users` AS ProjectUser "
                . "INNER JOIN `projects` AS `Project` ON Project.isactive=1 AND `ProjectUser`.`project_id` = `Project`.`id` " . $projCond . " "
                . "WHERE `ProjectUser`.`user_id` = '" . SES_ID . "' AND `ProjectUser`.`company_id` = '" . SES_COMP . "' ";
        $prjAllArr = $this->ProjectUser->query($sql);
        $this->set('projArr', $prjAllArr);
    }

    function ajax_test_api($api_id = '') {
        $this->loadModel('CompanyApi');
        $this->loadModel('Project');
        if (isset($this->data['CompanyApi'])) {
            #pr($_FILES);exit;
            $data = $this->data['CompanyApi'];
            $api = $this->CompanyApi->find('first', array('conditions' => array('api_key' => trim($data['api_key']))));
            if (is_array($api) && count($api) > 0) {
                $project = $this->Project->find('first', array('conditions' => array('Project.id' => $api['CompanyApi']['project_id'])));
            }
            $curl_arr = array(
                'api' => trim($data['api_key']),
                'title' => trim($data['title']),
                'description' => trim($data['description']),
                'due_date' => trim($data['due_date']),
                'estimated_hour' => floatval($data['estimated_hour']),
            );
            $counter = 1;
            if (is_array($_FILES) && count($_FILES) > 0 && isset($_FILES['data']['name']['CompanyApi'])) {
                foreach ($_FILES['data']['name']['CompanyApi'] as $key => $val) {
                    if ($val != '') {
                        $tmp_name = $_FILES['data']['tmp_name']['CompanyApi'][$key];
                        $name = $val;
                        $curl_arr['file' . $counter] = '@' . realpath($tmp_name) . ";filename=" . str_replace(" ", "_", basename($name));
                        $counter++;
                    }
                }
            }
            #print_r($curl_arr);exit;
            $res = $this->ajax_api_call($curl_arr);
            $res['proj_name'] = $project['Project']['name'];
            $res['proj_id'] = $project['Project']['uniq_id'];
            echo json_encode($res);
            exit;
        }

        $this->layout = 'ajax';
        if (isset($api_id) && $api_id > 0) {
            $apiArr = $this->CompanyApi->find('first', array('conditions' => array('CompanyApi.id' => $api_id)));
            $this->set('apiarr', $apiArr);
            $this->set('api_key', $apiArr['CompanyApi']['api_key']);
        } else {
            echo "Invalid request";
            exit;
        }
    }

    function ajax_api_call($curl_arr = array()) {
        if (is_array($curl_arr) && count($curl_arr) > 0) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,  Configure::read('API_BASE_URL')."create_task.json");
            

            curl_setopt($ch, CURLOPT_POST, 1);
            #curl_setopt($ch, CURLOPT_POST, true);
            #curl_setopt($ch, CURLOPT_POSTFIELDS, "postvar1=value1&postvar2=value2&postvar3=value3");
            // in real life you should use something like:
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 
            // http_build_query(array('postvar1' => 'value1')));
            // send a file
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curl_arr);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            // receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);

            // Then, after your curl_exec call:
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($server_output, 0, $header_size);
            $body = substr($server_output, $header_size);

            // Check if any error occurred
            if (!curl_errno($ch)) {
                $info = curl_getinfo($ch);
                $msg = 'Took ' . $info['total_time'] . ' seconds to send a request to server '; # . $info['url'];
            }

            curl_close($ch);
            $ret = array('message' => $msg, 'response' => $body);
            return $ret;
        } else {
            exit("Empty Request");
        }
    }

}
