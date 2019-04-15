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
/* * *******************************************************************************
 * Description:  Defines the Cron jobs for all CLI actions
 * Portions created by Orangescrum are Copyright (C) Orangescrum.
 * All Rights Reserved.
 * ****************************************************************************** */

App::uses('AppController', 'Controller');
App::import('Vendor', 's3', array('file' => 's3' . DS . 'S3.php'));

class CronController extends AppController {

    public $name = 'Cron';
    public $uses = array('Easycase', 'User', 'Company', 'CompanyUser', 'UserNotification', 'Status', 'Project');
    public $components = array('Format', 'Sendgrid', 'Postcase', 'Tmzone');

    function beforeFilter() {
        $this->Auth->allow();
		define('TLG', $this->is_AddonInstalled('TLG'));
		define('TPAY', $this->is_AddonInstalled('TPAY'));
		define('GTLG', $this->is_AddonInstalled('GTLG'));
		define('INV', $this->is_AddonInstalled('INV'));
		define('TSG', $this->is_AddonInstalled('TSG'));
		define('GNC', $this->is_AddonInstalled('GNC'));
		define('RCT', $this->is_AddonInstalled('RCT'));
		define('API', $this->is_AddonInstalled('API'));
		define('CHAT', $this->is_AddonInstalled('CHAT'));
		define('PT', $this->is_AddonInstalled('PT'));
		define('CR', $this->is_AddonInstalled('CR'));
		define('MAPI', $this->is_AddonInstalled('MAPI'));
		define('LANG', $this->is_AddonInstalled('LANG'));
    }

    function test_cron() {
        exit('Executed');
    }

    function test_email() {
        $everythingisfine = 0;

        echo "<pre>";

        if (defined('SMTP_PWORD') && SMTP_PWORD != "******") {

            if (!in_array('openssl', get_loaded_extensions())) {
                die('<div style="color:red">you have to enable php_openssl in php.ini to use this service</div><br />');
            } else {
                echo "php_openssl in php.ini is enabled <br /><br />";
                $everythingisfine = 1;
            }

            $host = SMTP_HOST;
            $ports[] = SMTP_PORT;

            foreach ($ports as $port) {
                $connection = @fsockopen($host, $port);
                if (is_resource($connection)) {
                    echo '<b>' . $host . ':' . $port . '</b> ' . '(' . getservbyport($port, 'ssl') . ') is open.<br /><br />' . "\n";
                    fclose($connection);
                    $everythingisfine = 1;
                } else {
                    echo '<div style="color:red"><b>' . $host . ':' . $port . '</b> is not responding.</div><br /><br />' . "\n";
                }
            }
            if ($everythingisfine && $_GET['to']) {
                $emailDetails = SMTP_HOST . ":" . SMTP_PORT . " Username: " . SMTP_UNAME;

                try {
                    $response1 = $this->Sendgrid->sendGridEmail(SUPPORT_EMAIL, urldecode($_GET['to']), "Testing SMTP Simple Email -" . time(), $emailDetails, '');

                    echo "SMTP Simple Email Respond: ";
                    print_r($response1);
                    echo "<br/><br/>";
                } Catch (Exception $e) {
                    echo 'Simple Email Caught exception: ', $e->getMessage(), "\n<br/>";
                }

                $this->Email->delivery = EMAIL_DELIVERY;
                $this->Email->to = urldecode($_GET['to']);
                $this->Email->subject = "Testing SMTP Template Email -" . time();
                $this->Email->from = FROM_EMAIL;
                $this->Email->template = 'test_email_template';
                $this->Email->sendAs = 'html';
                $this->set('message', $emailDetails);

                try {
                    $response2 = $this->Sendgrid->sendgridsmtp($this->Email);
                    echo "<br/>SMTP Template Email Respond: ";
                    print_r($response2);
                    exit;
                } Catch (Exception $e) {
                    echo 'Template Email Caught exception: ', $e->getMessage(), "\n";
                }
            }
        } else {
            echo "Provide the details of SMTP email sending options in `app/Config/constants.php`";
        }
        exit;
    }

    function email_notification() {
        $this->layout = 'ajax';

        #$Easycase = ClassRegistry::init('Easycase');
        #$User = ClassRegistry::init('User');
        #$Company = ClassRegistry::init('Company');
        #$CompanyUser = ClassRegistry::init('CompanyUser');
        #$UserNotification = ClassRegistry::init('UserNotification');

        $cancelled = $this->Company->find('list', array('conditions' => array('Company.is_active' => 2), 'fields' => 'Company.id'));
        $cancelledUser = $this->CompanyUser->find('list', array("conditions" => array('CompanyUser.company_id' => $cancelled), 'fields' => 'CompanyUser.user_id'));
        $getAllNot = $this->UserNotification->find('all', array('conditions' => array('UserNotification.type' => 1, 'UserNotification.value !=' => 0, 'UserNotification.user_id !=' => $cancelledUser)));

        $user_id = Hash::extract($getAllNot, "{n}.UserNotification.user_id");
        $userInfo = $this->User->find('all', array('conditions' => array('User.isactive' => 1, 'User.id' => $user_id)));
        #pr($userInfo);exit;
        #$comp = $this->CompanyUser->query("SELECT seo_url FROM companies AS Company,company_users AS CompanyUser WHERE Company.id=CompanyUser.company_id AND CompanyUser.user_id IN ('" . implode("','", $user_id) . "')");
        $params = array(
            'conditions' => array("CompanyUser.user_id" => $user_id),
            'fields' => array('Company.seo_url', 'Company.id', 'CompanyUser.user_id'),
            'joins' => array(
                array('table' => 'companies', 'alias' => 'Company', 'type' => 'inner', 'conditions' => array("Company.id=CompanyUser.company_id"))
            )
        );
        $comp = $this->CompanyUser->find("all", $params);

        $workflow_id = Hash::extract($userInfo, "{n}.Project.{n}.workflow_id");
        #pr($workflow_id);exit;
        $status_list = $this->Status->find('all', array('conditions' => array('Status.workflow_id' => array_unique($workflow_id)), 'order' => array('Status.workflow_id ASC', 'Status.seq_order ASC')));
        $proj_workflow_id = Hash::combine($userInfo, "{n}.Project.{n}.id", "{n}.Project.{n}.workflow_id");
        $status_arr = Hash::combine($status_list, "{n}.Status.id", "{n}.Status");
        #pr($status_arr);exit;
        $userInfoArr = Hash::combine($userInfo, "{n}.User.id", "{n}");
        $compArr = Hash::combine($comp, "{n}.CompanyUser.user_id", "{n}");

        foreach ($getAllNot as $usr) {
            $day = gmdate('D', strtotime(GMT_DATE));
            $lastDate = gmdate('Y-m-t');

            if ($usr['UserNotification']['value'] == 0) {
                continue;
            }

            if ($usr['UserNotification']['value'] == 1 || ($usr['UserNotification']['value'] == 2 && $day == "Fri") || ($usr['UserNotification']['value'] == 3 && $lastDate == GMT_DATE)) {
                #$userInfo = $this->User->find('first', array('conditions' => array('User.isactive' => 1, 'User.id' => $usr['UserNotification']['user_id'])));
                $userInfo = $userInfoArr[$usr['UserNotification']['user_id']];

                #$comp = $this->CompanyUser->query("SELECT seo_url FROM companies AS Company,company_users AS CompanyUser ". "WHERE Company.id=CompanyUser.company_id AND CompanyUser.user_id='" . $usr['UserNotification']['user_id'] . "'");
                $comp = array($compArr[$usr['UserNotification']['user_id']]);

                $comp_url = $comp['0']['Company']['seo_url'] . DOMAIN_COOKIE;

                $projArr = array();
                $projShNmArr = array();
                if (count($userInfo['Project'])) {
                    foreach ($userInfo['Project'] as $pu) {
                        if (strtolower(trim($pu['short_name'])) == 'wcos') {
                            continue;
                        }
                        array_push($projArr, $pu['id']);
                        $projShNmArr[$pu['id']] = $pu['short_name'];
                    }
                }
                #pr($projArr);exit;
                //$projIds = implode(",",$projArr);
                if ($usr['UserNotification']['value'] == 1) {
                    $arr = array('DATE(Easycase.dt_created)' => GMT_DATE);
                } elseif ($usr['UserNotification']['value'] == 2) {
                    $upto = date('Y-m-d', strtotime(GMT_DATE . "-7 day"));
                    $arr = array("AND" => array('AND' => array('DATE(Easycase.dt_created) >' => $upto), array('DATE(Easycase.dt_created) <=' => GMT_DATE)));
                } elseif ($usr['UserNotification']['value'] == 3) {
                    $upto = date('Y-m-d', strtotime(GMT_DATE . "-30 day"));
                    $arr = array("AND" => array('AND' => array('DATE(Easycase.dt_created) >' => $upto), array('DATE(Easycase.dt_created) <=' => GMT_DATE)));
                }

                $query_All = $this->Easycase->find('count', array('conditions' => array('Easycase.isactive' => 1, 'Easycase.project_id' => $projArr, 'Easycase.istype' => 1, 'Easycase.type_id !=' => 10, $arr), 'fields' => 'DISTINCT Easycase.id'));

                if ($query_All) {

                    $case_all = $this->Easycase->find('all', array('conditions' => array('Easycase.isactive' => 1, 'Easycase.project_id' => $projArr, 'Easycase.istype' => 1, 'Easycase.type_id !=' => 10, $arr), 'fields' => array('Easycase.case_no', 'Easycase.project_id', 'Easycase.title', 'Easycase.legend', 'Easycase.uniq_id')));

                    /* commented by GM - 2016-07-01 */
                    #$query_New = $Easycase->find('count', array('conditions' => array('Easycase.legend' => 1, 'Easycase.type_id !=' => 10, 'Easycase.isactive' => 1, 'Easycase.istype' => 1, 'Easycase.project_id' => $projArr, $arr), 'fields' => 'DISTINCT Easycase.id'));
                    #$query_Open = $Easycase->find('count', array('conditions' => array('Easycase.legend' => 2, 'Easycase.type_id !=' => 10, 'Easycase.isactive' => 1, 'Easycase.istype' => 1, 'Easycase.project_id' => $projArr, $arr), 'fields' => 'DISTINCT Easycase.id'));
                    #$query_Close = $Easycase->find('count', array('conditions' => array('Easycase.legend' => 3, 'Easycase.type_id !=' => 10, 'Easycase.isactive' => 1, 'Easycase.istype' => 1, 'Easycase.project_id' => $projArr, $arr), 'fields' => 'DISTINCT Easycase.id'));
                    #$query_Resolve = $Easycase->find('count', array('conditions' => array('Easycase.legend' => 5, 'Easycase.type_id !=' => 10, 'Easycase.isactive' => 1, 'Easycase.istype' => 1, 'Easycase.project_id' => $projArr, $arr), 'fields' => 'DISTINCT Easycase.id'));
                    //$query_Start = $Easycase->find('count', array('conditions'=>array('Easycase.legend' => 4, 'Easycase.type_id !=' => 10,'Easycase.isactive' => 1,'Easycase.istype' => 1,'Easycase.project_id' => $projArr,$arr),'fields' => 'DISTINCT Easycase.id'));
                    //$query_Attch = $Easycase->find('count', array('conditions'=>array('Easycase.format' => 1,'Easycase.isactive' => 1,'Easycase.istype' => 1,'Easycase.project_id' => $projArr,$arr),'fields' => 'DISTINCT Easycase.id'));
                    //$query_Upd = $Easycase->find('count', array('conditions'=>array('Easycase.type_id' => 10,'Easycase.isactive' => 1,'Easycase.istype' => 1,'Easycase.project_id' => $projArr,$arr),'fields' => 'DISTINCT Easycase.id'));

                    if ($query_All) {
                        $fill = "(" . round((($query_Close / $query_All) * 100)) . "%)";
                    } else {
                        $fill = "";
                    }

                    $to = $userInfo['User']['email'];
                    $name = $userInfo['User']['name'];
                    if ($query_All != 0) {
                        $str_case = "<table style='border:1px solid #ccc; border-radius:5px;width:100% !important; max-width:600px !important;border-collapse:collapse;font-family:Arial;font-size:14px;box-shadow:0px 0px 7px #ccc;' cellpadding='4'><tr style='background-color:#F0F0F0;font-weight:bold;'><td style='border-bottom:1px solid #ccc'>Project - Task#</td><td style='border-bottom:1px solid #ccc'>Title</td><td style='border-bottom:1px solid #ccc'>Status</td></tr>";

                        $typ_arr = array('1' => '<font color="#763532">New</font>', '2' => '<font color="#244F7A">In Progress</font>', '3' => '<font color="#387600">Closed</font>', '4' => '<font color="#55A0C7">Start</font>', '5' => '<font color="#EF6807">Resolved</font>');
                        $status_count = array();
                        foreach ($case_all as $case) {
                            $prj_id = $case['Easycase']['project_id'];
                            $prj_shortname = $projShNmArr[$prj_id];
                            $case_no = strtoupper($prj_shortname) . " - " . $case['Easycase']['case_no'];
                            $case_title = "<a href='" . HTTP_ROOT . "dashboard#details/" . $case['Easycase']['uniq_id'] . "' target='_blank'>" . $case['Easycase']['title'] . "</a>";
                            $case_status1 = $case['Easycase']['legend'] == 4 ? 2 : $case['Easycase']['legend'];
                            #$case_status = $typ_arr[$case_status1];
                            $case_status = isset($status_arr[$case_status1]) ? $status_arr[$case_status1]['name'] : '---';
                            $status_count[$case_status1] = isset($status_count[$case_status1]) ? $status_count[$case_status1] + 1 : 1;
                            $str_case .= "<tr style='height:30px'><td nowrap='nowrap' valign='top'>$case_no</td><td valign='top'>$case_title</td><td valign='top'>$case_status</td></tr>";
                        }
                        $str_case .= "</table>";
                    } else {
                        $str_case = "";
                    }

                    $status_html = '';
                    if (is_array($status_count) && count($status_count) > 0) {
                        $cntr = 0;
                        foreach ($status_count as $skey => $sval) {
                            $status_html .= "<td>
                                                <table cellpadding='2' cellspacing='2' style='font:bold 14px Arial;'>
                                                    <tr>
                                                        <td style='color:#FFF;background:" . (isset($status_arr[$skey]['color']) ? $status_arr[$skey]['color'] : '#ccc') . ";min-width:25px;text-align:center;webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;'>" . $sval . "</td>
                                                        <td>" . (isset($status_arr[$skey]['name']) ? $status_arr[$skey]['name'] : '---') . "</td>
                                                    </tr>
                                                </table>
                                            </td>";
                            $cntr++;
                            if ($cntr % 4 == 0 && $cntr < count($status_count)) {
                                $status_html .= "</tr><tr>";
                            }
                        }
                    }

                    $sub = '';
                    if ($query_Close) {
                        if ($query_Close == 1) {
                            $sub .= $query_Close . ' Closed';
                        } else {
                            $sub .= $query_Close . ' Closed';
                        }
                    }
                    if ($query_Resolve) {
                        if ($sub != '') {
                            if ($query_New || $query_Open) {
                                $sub .= ', ' . $query_Resolve . ' Resolved';
                            } else {
                                $sub .= ' and ' . $query_Resolve . ' Resolved';
                            }
                        } else {
                            if ($query_Resolve == 1) {
                                $sub .= $query_Resolve . ' Resolved';
                            } else {
                                $sub .= $query_Resolve . ' Resolved';
                            }
                        }
                    }
                    if ($query_New) {
                        if ($sub != '') {
                            if ($query_Open) {
                                $sub .= ', ' . $query_New . ' New';
                            } else {
                                $sub .= ' and ' . $query_New . ' New';
                            }
                        } else {
                            if ($query_New == 1) {
                                $sub .= $query_New . ' New';
                            } else {
                                $sub .= $query_New . ' New';
                            }
                        }
                    }
                    if ($query_Open) {
                        if ($sub != '') {
                            $sub .= ' and ' . $query_Open . ' In Progress';
                        } else {
                            if ($query_Open == 1) {
                                $sub .= $query_Open . ' In Progress';
                            } else {
                                $sub .= $query_Open . ' In Progress';
                            }
                        }
                    }

                    if ($usr['UserNotification']['value'] == 1) {
                        $sb_title = 'Daily Task Status Updates';
                        if ($sub) {
                            $subject = $sub . " Tasks on Orangescrum - " . date("m/d", strtotime(GMT_DATE));
                        } else {
                            $subject = 'Orangescrum Daily Task Status - ' . date("m/d", strtotime(GMT_DATE));
                        }
                    } elseif ($usr['UserNotification']['value'] == 2) {
                        $sb_title = 'Weekly Task Status Updates';
                        if ($sub) {
                            $subject = $sub . " on Orangescrum - " . date("m/d", strtotime($upto)) . " - " . date("m/d", strtotime(GMT_DATE));
                        } else {
                            $subject = 'Orangescrum Weekly Task Status - ' . date("m/d", strtotime($upto)) . " - " . date("m/d", strtotime(GMT_DATE));
                        }
                    } elseif ($usr['UserNotification']['value'] == 3) {
                        $sb_title = 'Monthly Task Status Updates';
                        if ($sub) {
                            $subject = $sub . " on Orangescrum - " . date("m/d", strtotime($upto)) . " - " . date("m/d", strtotime(GMT_DATE));
                        } else {
                            $subject = 'Orangescrum Monthly Task Status - ' . date("m/d", strtotime($upto)) . " - " . date("m/d", strtotime(GMT_DATE));
                        }
                    }

                    $message = "<table cellpadding='0' cellspacing='0' align='left' style='width:100%'>
                        
							<tr>
								<td align='left'>
									<table cellpadding='4' cellspacing='4' style='border:1px solid #CCCCCC;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;box-shadow:4px 0px 4px rgba(0,0,0,0.3);-moz-box-shadow:4px 0px 4px rgba(0,0,0,0.3);-webkit-box-shadow:4px 0px 4px rgba(0,0,0,0.3);margin:10px 0;width:100% !important; max-width:600px !important;'>
										<tr>
                                                    $status_html
													</tr>
												</table>
											</td>
													</tr>
                                    
                                    <tr><td>" . $str_case . "</td></tr>
                                    <tr><td align='left' style='padding:5px 0px'><hr style='border: none; height: 0.1em; color:#DBDBDB;background:#DBDBDB;'/></td></tr>
													<tr>
								<td align='left' style='font:10px Arial;padding-top:2px;color:#737373'>
									You are receiving this email notification because you have subscribed to Orangescrum Task Status E-mail notification, to unsubscribe, please click <a href='" . HTTP_ROOT . "users/email_notifications' target='_blank'>Unsubscribe Email Notification</a>
								</td>	  
							</tr>
						</table>";
                    #echo $message;
                    #exit;
                    if (!defined('CRON_DISPATCHER')) {
                        $this->Sendgrid->sendGridEmail(FROM_EMAIL_NOTIFY, $to, $subject, $message, "notification");
                        echo "To: " . $to;
                        echo "<br/>";
                        echo $subject;
                        echo "<br/>";
                        echo $message;
                        echo "<br/>";
                        exit;
                    } else {
                        //$subject.=" - CRON";
                        $this->Sendgrid->sendGridEmail(FROM_EMAIL_NOTIFY, $to, $subject, $message, "notification");
                    }
                }
            }
        }
        exit;
    }

    function update_email() {
        $this->layout = 'ajax';

        #$Easycase = ClassRegistry::init('Easycase');
        #$User = ClassRegistry::init('User');
        $this->loadModel('Timezone');
        $this->loadModel('Project');
        //$User->recursive=-1;
        //start USer loop
        #$UserNotification = ClassRegistry::init('UserNotification');
        $getAllNot = $this->UserNotification->find('all', array('conditions' => array('UserNotification.due_val' => 1)));
        $user_id = Hash::extract($getAllNot, "{n}.UserNotification.user_id");
        $usr = $this->User->find('all', array('conditions' => array('User.isactive' => 1, 'User.name !=' => '', 'User.id' => $user_id),
            'order' => array('User.id ASC')));
        #'fields' => array('User.name', 'User.short_name', 'User.email', 'User.id', 'User.timezone_id'),
        $params = array(
            'conditions' => array("CompanyUser.user_id" => $user_id),
            'fields' => array('Company.seo_url', 'Company.id', 'CompanyUser.user_id'),
            'joins' => array(
                array('table' => 'companies', 'alias' => 'Company', 'type' => 'inner', 'conditions' => array("Company.id=CompanyUser.company_id"))
            )
        );
        $comp = $this->CompanyUser->find("all", $params);
        $workflow_id = Hash::extract($usr, "{n}.Project.{n}.workflow_id");
        $status_list = $this->Status->find('all', array('conditions' => array('Status.workflow_id' => array_unique($workflow_id)),
            'order' => array('Status.workflow_id ASC', 'Status.seq_order ASC')));

        $status_arr = Hash::combine($status_list, "{n}.Status.id", "{n}.Status");
        $compArr = Hash::combine($comp, "{n}.CompanyUser.user_id", "{n}");
        $userInfoArr = Hash::combine($usr, "{n}.User.id", "{n}");

        foreach ($getAllNot as $emlNot) {

            #$usr = $User->find('all', array('conditions' => array('User.isactive' => 1, 'User.name !=' => '', 'User.id' => $emlNot['UserNotification']['user_id']), 'fields' => array('User.name', 'User.short_name', 'User.email', 'User.id', 'User.timezone_id'), 'order' => array('User.id ASC')));
            $usr = $userInfoArr[$emlNot['UserNotification']['user_id']];
            $to = $usr['User']['email'];
            $uid = $usr['User']['id'];

            $timezn = $this->Timezone->find('first', array('conditions' => array('Timezone.id' => $usr['User']['timezone_id']), 'fields' => array('Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.code')));
            App::import('Helper', 'Tmzone');
            $tmzone = new TmzoneHelper(new View(null));
            $gmt_datetime = gmdate('Y-m-d H:i:s');
            App::import('Helper', 'Datetime');
            $Datetime = new DatetimeHelper(new View(null));
            $dateCurnt = $tmzone->GetDateTime($usr['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "datetime");
            $min = date('i', strtotime($dateCurnt));
            $hour = date('H', strtotime($dateCurnt));

            if ($hour == 6 && $min < 30) {
                #if (1 == 1) {
                #$userInfo = $this->User->find('first', array('conditions' => array('User.isactive' => 1, 'User.id' => $usr['User']['id']), 'recursive' => 1));
                $userInfo = $userInfoArr[$uid];

                #$comp = ClassRegistry::init('CompanyUser')->query("SELECT seo_url FROM companies AS Company,company_users AS CompanyUser WHERE Company.id=CompanyUser.company_id AND CompanyUser.user_id='" . $usr['User']['id'] . "'");
                $comp = array($compArr[$uid]);
                $comp_url = $comp['0']['Company']['seo_url'] . DOMAIN_COOKIE;

                $projArr = array();
                foreach ($userInfo['Project'] as $pu) {
                    array_push($projArr, $pu['id']);
                }

                $dateCurnt = date('Y-m-d', strtotime($dateCurnt));
                $upto = date('Y-m-d', strtotime($dateCurnt . "-7 day"));
                $upcom = date('Y-m-d', strtotime($dateCurnt . "+7 day"));

                $arr_late = array("AND" => array('AND' => array('DATE(Easycase.due_date) >=' => $upto), array('DATE(Easycase.due_date) <' => $dateCurnt)));

                $arr_cming = array("AND" => array('AND' => array('DATE(Easycase.due_date) >=' => $dateCurnt), array('DATE(Easycase.due_date) <=' => $upcom)));

                $arr4 = array("OR" => array('AND' => array('Easycase.assign_to' => $uid), array('Easycase.assign_to' => '0', 'Easycase.user_id' => $uid)));

                $status_condition = "SELECT  MAX(Status.id) AS id FROM `statuses` AS `Status`  WHERE `Status`.`workflow_id` = Project.workflow_id  GROUP BY `Status`.`workflow_id`";

                //Late cases
                $case_late = $this->Easycase->find('all', array(
                    'conditions' => array('Easycase.isactive' => 1, 'Easycase.assign_to' => $uid, 'Easycase.due_date !=' => NULL,
                        'Easycase.project_id' => $projArr, 'Easycase.istype' => 1, 'Easycase.type_id !=' => 10,
                        "Easycase.legend NOT IN ($status_condition)", $arr_late, $arr4),
                    'fields' => array('Easycase.case_no', 'Easycase.project_id', 'Easycase.title', 'Easycase.legend', 'Easycase.due_date'),
                    'joins' => array(
                        array('table' => 'projects', 'alias' => 'Project', 'type' => 'left', 'conditions' => array('Project.id = Easycase.project_id')),
                    )
                        )
                );
                //upcoming cases
                $case_cming = $this->Easycase->find('all', array(
                    'conditions' => array('Easycase.isactive' => 1, 'Easycase.assign_to' => $usr['User']['id'], 'Easycase.project_id' => $projArr,
                        'Easycase.istype' => 1, 'Easycase.type_id !=' => 10,
                        "Easycase.legend NOT IN ($status_condition)", $arr_cming, $arr4),
                    'fields' => array('Easycase.case_no', 'Easycase.project_id', 'Easycase.title', 'Easycase.legend', 'Easycase.due_date'),
                    'joins' => array(
                        array('table' => 'projects', 'alias' => 'Project', 'type' => 'left', 'conditions' => array('Project.id = Easycase.project_id')),
                    )
                        )
                );


                if (count($case_cming) || count($case_late)) {

                    if (count($case_late) != 0) {
                        $str_case = "<tr><td><table cellpadding='2' cellspacing='2' style='font:bold 14px Arial;'><tr><td style='color:#FF0000'>Overdue tasks</td></tr><tr><td><table style='border:1px solid #ccc; border-radius:5px;width:100%;border-collapse:collapse;font-size:14px;font-family:Arial;box-shadow:0px 0px 7px #ccc;' cellpadding='4' cellspacing='4'><tr><td style='border-bottom:1px solid #ccc'>Task#</td><td style='border-bottom:1px solid #ccc'>Title</td><td style='border-bottom:1px solid #ccc'>Status</td><td style='border-bottom:1px solid #ccc'>Due Date</td><td style='border-bottom:1px solid #ccc;width: 90px;'>Project</td><td style='border-bottom:1px solid #ccc;width: 90px;'>Late By</td></tr>";

                        $typ_arr = array('1' => '<font color="#763532">New</font>', '2' => '<font color="#244F7A">In Progress</font>', '3' => '<font color="#387600">Closed</font>', '4' => '<font color="#55A0C7">Start</font>', '5' => '<font color="#EF6807">Resolved</font>');

                        foreach ($case_late as $case) {

                            $case_no = $case['Easycase']['case_no'];
                            $case_title = $case['Easycase']['title'];
                            $case_status1 = $case['Easycase']['legend'] == 4 ? 2 : $case['Easycase']['legend'];
                            #$case_status = $typ_arr[$case_status1];
                            $case_status = isset($status_arr[$case_status1]) ? $status_arr[$case_status1]['name'] : '---';
                            $due_dt = $this->Format->dateFormatReverse($case['Easycase']['due_date']);
                            $date1 = $case['Easycase']['due_date'];
                            $date2 = $tmzone->GetDateTime($usr['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "date");
                            $diff = abs(strtotime($date2) - strtotime($date1));
                            $years = floor($diff / (365 * 60 * 60 * 24));
                            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
                            $late_dt = $days . " day(s)";
                            $pjnam = $this->Project->find('first', array('conditions' => array('Project.id' => $case['Easycase']['project_id']), 'fields' => array('Project.name')));
                            $pj_name = $pjnam['Project']['name'];
                            $str_case .= "<tr><td align='right' style='padding-right:10px;'>$case_no</td><td>$case_title</td><td>$case_status</td><td>$due_dt</td><td style='padding-left:10px;'>$pj_name</td><td style='padding-left:10px;'>$late_dt</td></tr>";
                        }
                        $str_case .= "</table></td></tr></table></td></tr>";
                    } else {
                        $str_case = "";
                    }
                    if (count($case_cming) != 0) {
                        $str_case_cming = "<tr><td><table cellpadding='2' cellspacing='2' style='font:bold 14px Arial;'><tr><td style='color:#047C04'>Upcoming tasks</td></tr><tr><td><table style='border:1px solid #ccc; border-radius:5px;width:100%;border-collapse:collapse;font-size:14px;font-family:Arial;box-shadow:0px 0px 7px #ccc;' cellpadding='4' cellspacing='4'><tr><td style='border-bottom:1px solid #ccc'>Task#</td><td style='border-bottom:1px solid #ccc'>Title</td><td style='border-bottom:1px solid #ccc'>Status</td><td style='border-bottom:1px solid #ccc'>Due Date</td><td style='border-bottom:1px solid #ccc;width: 90px;'>Project</td><td style='border-bottom:1px solid #ccc;width: 90px;'>Coming up in</td></tr>";

                        $typ_arr = array('1' => '<font color="#763532">New</font>', '2' => '<font color="#244F7A">In Progress</font>', '3' => '<font color="#387600">Closed</font>', '4' => '<font color="#55A0C7">Start</font>', '5' => '<font color="#EF6807">Resolved</font>');

                        foreach ($case_cming as $case) {

                            $case_no = $case['Easycase']['case_no'];
                            $case_title = $case['Easycase']['title'];
                            $case_status1 = $case['Easycase']['legend'] == 4 ? 2 : $case['Easycase']['legend'];
                            #$case_status = $typ_arr[$case_status1];
                            $case_status = isset($status_arr[$case_status1]) ? $status_arr[$case_status1]['name'] : '---';
                            $due_dt = $this->Format->dateFormatReverse($case['Easycase']['due_date']);
                            $date1 = $case['Easycase']['due_date'];
                            $date2 = $tmzone->GetDateTime($usr['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "date");
                            $diff = abs(strtotime($date1) - strtotime($date2));
                            $years = floor($diff / (365 * 60 * 60 * 24));
                            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                            $cming_day = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24)) . " day(s)";

                            $pjnam = $this->Project->find('first', array('conditions' => array('Project.id' => $case['Easycase']['project_id']), 'fields' => array('Project.name')));
                            $pj_name = $pjnam['Project']['name'];
                            $str_case_cming .= "<tr><td align='right' style='padding-right:10px;'>$case_no</td><td>$case_title</td><td>$case_status</td><td>$due_dt</td><td style='padding-left:10px;'>$pj_name</td><td style='padding-left:10px;'> $cming_day</td></tr>";
                        }
                        $str_case_cming .= "</table></td></tr></table></td></tr>";
                    } else {
                        $str_case_cming = "";
                    }

                    $totalDueCases = count($case_cming);
                    if ($totalDueCases > 1) {
                        $totalDueCases = $totalDueCases . " Upcoming Tasks";
                    } else {
                        $totalDueCases = $totalDueCases . " Upcoming Task";
                    }

                    $totaloverDueCases = count($case_late);
                    if ($totaloverDueCases > 1) {
                        $totaloverDueCases = $totaloverDueCases . " Overdue";
                    } else {
                        $totaloverDueCases = $totaloverDueCases . " Overdue";
                    }

                    $sub = '';
                    if ($totalDueCases && $totaloverDueCases) {
                        $sub = $totaloverDueCases . " and " . $totalDueCases;
                    } elseif ($totalDueCases && !$totaloverDueCases) {
                        $sub = $totalDueCases;
                    } elseif (!$totalDueCases && $totaloverDueCases) {
                        $sub = $totaloverDueCases;
                    }
                    $taskduedt = $this->Format->dateFormatReverse($tmzone->GetDateTime($usr['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "date"));
                    //$subject = "Orangescrum Task Due notification: ".$this->Format->dateFormatReverse($tmzone->GetDateTime($usr['User']['timezone_id'],$timezn['Timezone']['gmt_offset'],$timezn['Timezone']['dst_offset'],$timezn['Timezone']['code'],$gmt_datetime,"date"));

                    $subject = $sub . " on Orangescrum - " . date("m/d", strtotime($taskduedt));
                    $message = "<table cellpadding='0' cellspacing='0' align='left' width='100%'>
								<tr style='height:25px;'><td>&nbsp;</td></tr>
								<tr>
									<td align='left'>
										<table cellpadding='4' cellspacing='4' style='border:1px solid #666666;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;box-shadow:4px 0px 4px rgba(0,0,0,0.3);-moz-box-shadow:4px 0px 4px rgba(0,0,0,0.3);-webkit-box-shadow:4px 0px 4px rgba(0,0,0,0.3);margin:10px 0;'>
												" . $str_case_cming . "
										</table>
									</td>
								</tr>
								<tr>
									<td align='left'>
										<table cellpadding='4' cellspacing='4' style='border:1px solid #666666;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;box-shadow:4px 0px 4px rgba(0,0,0,0.3);-moz-box-shadow:4px 0px 4px rgba(0,0,0,0.3);-webkit-box-shadow:4px 0px 4px rgba(0,0,0,0.3);margin:10px 0;'>
												" . $str_case . "
										</table>
									</td>
								</tr>
								<tr style='height:25px;'><td>&nbsp;</td></tr>
								
                                <tr><td align='left' style='padding:5px 0px'><hr style='border: none; height: 0.1em; color:#DBDBDB;background:#DBDBDB;'/></td></tr>
								<tr>
									<td align='left' style='font:10px Arial;padding-top:2px;color:#737373'>
									You are receiving this email notification because you have subscribed to Orangescrum, to unsubscribe, please click <a href='" . HTTP_ROOT . "users/email_notifications' target='_blank'>Unsubscribe Email Notification</a>
								</td>
								</tr>
							</table>";


                    #echo $message;exit;
                    $this->Sendgrid->sendGridEmail(FROM_EMAIL_NOTIFY, $to, $subject, $message, "notification");
                }
            }
        }
        //End user loop
        exit;
    }

    function test() {
        $this->layout = 'ajax';
        echo "Running Cron Job...";
        exit;
    }

    function dailyupdate_notifications() {
        $this->layout = 'ajax';
        echo "Running Cron Job...";

        #$Easycase = ClassRegistry::init('Easycase');
        $this->loadModel('Timezone');
        $this->loadModel('Project');
        $DailyupdateNotification = ClassRegistry::init('DailyupdateNotification');

        $getAllNot = $DailyupdateNotification->find('all', array(
            'conditions' => array('DailyupdateNotification.dly_update' => 1, 'User.isactive' => 1, 'User.name !=' => ''),
            'fields' => array(
                'DailyupdateNotification.id',
                'DailyupdateNotification.user_id',
                'DailyupdateNotification.notification_time',
                'DailyupdateNotification.proj_name',
                'DailyupdateNotification.mail_sent',
                'User.email',
                'User.timezone_id'
            ),
            'order' => array('User.id ASC')
            )
        );

        App::import('Helper', 'Tmzone');
        $tmzone = new TmzoneHelper(new View(null));
        if (is_array($getAllNot) && count($getAllNot) > 0) {
            $project_id = Hash::extract($getAllNot, "{n}.DailyupdateNotification.proj_name");
            $project_idArr = array_unique(explode(',', implode(',', $project_id)));
            $projArr = $this->Project->find('all', array('conditions' => array('Project.id' => $project_idArr, 'Project.isactive' => 1),
                'fields' => array('Project.id', 'Project.name', 'Project.short_name', 'Project.workflow_id'), 'recursive' => false));

            $workflow_id = Hash::extract($projArr, "{n}.Project.workflow_id");
            $status_list = $this->Status->find('all', array('conditions' => array('Status.workflow_id' => array_unique($workflow_id)),
                'order' => array('Status.workflow_id ASC', 'Status.seq_order ASC')));
            $status_arr = Hash::combine($status_list, "{n}.Status.id", "{n}.Status");
            $status_list_arr = Hash::combine($status_list, "{n}.Status.id", "{n}.Status.name");
            $timezone_id = Hash::extract($getAllNot, "{n}.User.timezone_id");
            $timezn = $this->Timezone->find('all', array(
                'conditions' => array('Timezone.id' => $timezone_id),
                'fields' => array('Timezone.id', 'Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.code')
                )
        );
            $timeznArr = Hash::combine($timezn, "{n}.Timezone.id", "{n}");

        foreach ($getAllNot as $emlNot) {
            $to = $emlNot['User']['email'];
                /* $timezn = $this->Timezone->find('first', array(
                  'conditions' => array('Timezone.id' => $emlNot['User']['timezone_id']),
                  'fields' => array('Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.code')
                )
                  ); */
                $timezn = $timeznArr[$emlNot['User']['timezone_id']];

            $gmt_datetime = gmdate('Y-m-d H:i:s');
            $dateCurnt = $tmzone->GetDateTime($emlNot['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "datetime");
            $dateToday = $tmzone->GetDateTime($emlNot['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $gmt_datetime, "date");

            $dateSent = '';
            if ($emlNot['DailyupdateNotification']['mail_sent']) {
                $dateSent = $tmzone->GetDateTime($emlNot['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $emlNot['DailyupdateNotification']['mail_sent'], "date");
            }

            $time = date('H:i', strtotime($dateCurnt));
            $curtime = strtotime($time);
            $nottime = strtotime($emlNot['DailyupdateNotification']['notification_time']);

            if ($dateSent != $dateToday && $nottime <= $curtime) {

                $projArr = '';
                $projidarr = explode(",", $emlNot['DailyupdateNotification']['proj_name']);

                $this->Project->recursive = -1;
                    $projArr = $this->Project->find('all', array('conditions' => array('Project.id' => $projidarr, 'Project.isactive' => 1),
                        'fields' => array('Project.id', 'Project.name', 'Project.short_name', 'Project.workflow_id')));

                $comp = ClassRegistry::init('CompanyUser')->query("SELECT seo_url FROM companies AS Company,company_users AS CompanyUser WHERE Company.id=CompanyUser.company_id AND CompanyUser.user_id='" . $emlNot['DailyupdateNotification']['user_id'] . "'");
                $comp_url = $comp['0']['Company']['seo_url'] . DOMAIN_COOKIE;

                foreach ($projArr as $prj) {

                    $prjId = $prj['Project']['id'];
                    $projName = $prj['Project']['name'];
                    $projShName = $prj['Project']['short_name'];

                    $rngFrom = date('Y-m-d', $nottime);
                    $rngFromGMT = $tmzone->GetDateTime(27, (-1 * $timezn['Timezone']['gmt_offset']), 0, NULL, $rngFrom, "datetime");
                    $rngTo = date('Y-m-d H:i:s', $nottime);
                    $rngToGMT = $tmzone->GetDateTime(27, (-1 * $timezn['Timezone']['gmt_offset']), 0, NULL, $rngTo, "datetime");


                    $arr = array('Easycase.dt_created BETWEEN ? AND ?' => array($rngFromGMT, $rngToGMT));
                    $leg = array(2, 3, 4, 5);
                        $status_condition = "SELECT  Status.id FROM `statuses` AS `Status`  WHERE `Status`.`workflow_id` = Project.workflow_id  GROUP BY `Status`.`workflow_id`";

                        $query_All = $this->Easycase->find('count', array(
                        'conditions' => array(
                            'Easycase.isactive' => 1,
                            'Easycase.project_id' => $prjId,
                            'Easycase.istype' => 1,
                            'Easycase.type_id !=' => 10,
                                "OR" => array(
                            'Easycase.legend' => $leg,
                                    "Easycase.legend IN ($status_condition)",
                                ),
                            $arr
                        ),
                            'fields' => 'DISTINCT Easycase.id',
                            'joins' => array(
                                array('table' => 'projects', 'alias' => 'Project', 'type' => 'left', 'conditions' => array('Project.id = Easycase.project_id')),
                            )
                                )
                    );

                    if ($query_All) {
                            $case_all = $this->Easycase->find('all', array(
                            'conditions' => array(
                                'Easycase.isactive' => 1,
                                'Easycase.project_id' => $prjId,
                                'Easycase.istype' => 1,
                                'Easycase.type_id !=' => 10,
                                    "OR" => array(
                                'Easycase.legend' => $leg,
                                        "Easycase.legend IN ($status_condition)",
                                    ),
                                $arr
                            ),
                                'fields' => array('Easycase.case_no', 'Easycase.title', 'Easycase.legend', 'Easycase.message'),
                                'joins' => array(
                                    array('table' => 'projects', 'alias' => 'Project', 'type' => 'left', 'conditions' => array('Project.id = Easycase.project_id')),
                            )
                                )
                        );



                        $str_case = "<table style='width:80%;border-collapse:collapse;border-spacing:0;font-family:Arial;font-size:14px;'>";

                        $typ_arr = array('1' => '<font color="#AE432E">New</font>', '2' => '<font color="#244F7A">In Progress</font>', '3' => '<font color="#387600">Closed</font>', '4' => '<font color="#244F7A">Started</font>', '5' => '<font color="#EF6807">Resolved</font>');

                        foreach ($case_all as $case) {
                            $prj_shortname = $projShName;
                            $case_no = $case['Easycase']['case_no'];
                            $case_title = $case['Easycase']['title'];
                                $case_status1 = $case['Easycase']['legend'] == 4 ? 2 : $case['Easycase']['legend'];
                                #$case_status = $typ_arr[$case_status1];
                                $case_status = isset($status_arr[$case_status1]) ? $status_arr[$case_status1]['name'] : '---';

                                $case_replyAll = $this->Easycase->find('all', array(
                                    'conditions' => array('Easycase.project_id' => $prjId, 'Easycase.istype' => 2, 'Easycase.case_no' => $case_no, $arr),
                                    'fields' => array('Easycase.message')
                                )
                            );



                            if (array_key_exists($case['Easycase']['legend'], $status_list_arr) || in_array($case['Easycase']['legend'], $leg)) { 
                                $str_case .= "<tr><td><b>Task#&nbsp; $case_no:&nbsp; </b>$case_title</td></tr>";
                                if ($case_replyAll) {
                                    $str_case .= "<tr><td><ul>";
                                    foreach ($case_replyAll as $casereply) {
                                        if ($casereply['Easycase']['message']) {
                                            $caserly = $casereply['Easycase']['message'];
                                            $str_case .= "<li>$caserly</li>";
                                        }
                                    }
                                    $str_case .= "</ul></td></tr>";
                                }
                                $str_case .= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status:&nbsp;&nbsp;$case_status</td></tr><tr style='height:25px;'><td>&nbsp;</td></tr>";
                            }
                        }
                        $str_case .= "</table>";

                        if (count($case_all) > 1) {
                            $casecount = count($case_all) . " Tasks";
                        } else {
                            $casecount = count($case_all) . " Task";
                        }

                        if (count($case_all) == 0) {
                            $sb_title = "No Tasks Today<br/><br/><br/>";
                        }
                        $sub = $casecount . " Updated on " . $projName;
                        $subject = $sub . " - " . date("m/d", strtotime(GMT_DATE));
                        $message = "<tr><td><table style='border-collapse:collapse;border-spacing:0;text-align:left;width:600px;border:1px solid #5191BD'>
								<tr style='background:#5191BD;height:50px;'>
									<td style='font:bold 14px Arial;padding:10px;color:#FFFFFF;'>
										<span style='font-size:18px;'>Orangescrum</span> - Daily Task Updates
									</td>
								</tr>
								<tr>
									<td style='padding:10px;'>
										" . str_replace('<tr><td><ul></ul></td></tr>', '', $str_case) . "
									</td>
								</tr>
                                            <tr><td align='left' style='font:14px Arial;padding:10px;border-top:1px solid #E1E1E1'>Thanks,<br/>Team Orangescrum</td></tr>
							</table></td></tr>
							<tr><td>
							<table style='margin-top:5px;width:600px;'>
								<tr><td style='font:13px Arial;color:#737373;'>Don't want these emails? To unsubscribe, please click <a href='" . HTTP_ROOT . "users/email_notifications' target='_blank'>Unsubscribe</a> and trun off <b>Daily Update Report</b> E-mail notification.</td></tr>
							</table></td></tr>
							";

                            #echo $message;

                        $this->Sendgrid->sendGridEmail(FROM_EMAIL_NOTIFY, $to, $subject, $message, "notification");
                    }
                }
                    $DailyupdateNotification->query('UPDATE `dailyupdate_notifications` SET `mail_sent`= \'' . GMT_DATETIME . '\' WHERE `id` =' . $emlNot['DailyupdateNotification']['id']);
            }
        }
        }
        echo "Success";
        exit;
    }

    function dailyUpdateMail() {

        $this->layout = 'ajax';
        echo "Running Daily Update Alert...";

        //Getting all project ids and user's ids
        $this->loadModel('DailyUpdate');
        $this->loadModel('Timezone');
        #$this->loadModel('Project');
        #$this->loadModel('User');

        $usersubscription = ClassRegistry::init('UserSubscription');
        $cancelled = $usersubscription->find('list', array('conditions' => array('UserSubscription.is_cancel' => 1), 'fields' => 'UserSubscription.company_id'));

        $allData = $this->DailyUpdate->find('all', array("conditions" => array('DailyUpdate.company_id !=' => $cancelled)));

        if (isset($allData) && !empty($allData)) {

            $timezone_id = Hash::extract($allData, "{n}.DailyUpdate.timezone_id");
            $timezn = $this->Timezone->find('all', array(
                'conditions' => array('Timezone.id' => $timezone_id),
                'fields' => array('Timezone.id', 'Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.code')
                    )
            );
            $timeznArr = Hash::combine($timezn, "{n}.Timezone.id", "{n}");

            foreach ($allData as $key => $value) {
                //Getting date and time with respect to timezone.
                #$timezone = $this->Timezone->find('first', array("conditions" => array("Timezone.id" => $value['DailyUpdate']['timezone_id'])));
                $timezn = $timeznArr[$value['DailyUpdate']['timezone_id']];

                $locDT = $this->Tmzone->GetDateTime($timezn['Timezone']['id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], gmdate('Y-m-d H:i:s'), "datetime");
                $dt = explode(" ", $locDT);
                $dateArr = explode("-", $dt['0']);

                $date = $dateArr['1'] . "/" . $dateArr['2'];
                $time = substr($dt['1'], 0, strrpos($dt['1'], ':', -3)) . ":00"; //Curent time
                if ($value['DailyUpdate']['cron_email_date'] != $dt['0']) {//Send an email once a day. Check current date
                    $days = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday"); //Official working days.
                    //If work is going on weekend
                    if ($value['DailyUpdate']['days'] == 6) {
                        array_push($days, "Saturday");
                    } elseif ($value['DailyUpdate']['days'] == 7) {
                        array_push($days, "Saturday", "Sunday");
                    }

                    if (in_array(date('l', strtotime($locDT)), $days)) {//Check weekdays.
                        if (strtotime($time) >= strtotime($value['DailyUpdate']['notification_time'])) {
                            //Getting project details
                            $project = $this->Project->getProjectFields(array('Project.id' => $value['DailyUpdate']['project_id']), array('id', 'name', 'short_name', 'workflow_id'));

                            $usr = explode(",", $value['DailyUpdate']['user_id']); //Getting user ids.
                            foreach ($usr as $key1 => $value1) {
                                //Getting user details
                                $user = $this->User->getUserFields(array('User.id' => $value1), array('id', 'name', 'last_name', 'email'));
                                $this->Postcase->dailyMail($user['User'], $project['Project'], $date); //Send mail to each users
                            }

                            //Updating cron email date when email send for each day. So that it will send once a day.
                            $this->DailyUpdate->id = $value['DailyUpdate']['id'];
                            $dailyUpdate['cron_email_date'] = $locDT;
                            $this->DailyUpdate->save($dailyUpdate);
                            print "Sent emails to user...";
                        }
                    }
                }
            }
        }
        echo "Success";
        exit;
    }

    function getDays($date = NULL) {
        $date = gmdate("Y-m-d", strtotime($date));
        $last_dt = new DateTime($date);
        $today = new DateTime(gmdate('Y-m-d'));
        $interval = $today->diff($last_dt);
        return $interval->days;
    }

    function getLinks($user = NULL, $seo_url = NULL) {
        $activationLink = HTTP_ROOT . "users/confirmation/" . $user['query_string'];
        $helpLink = "<a href='" . HTTP_ROOT . "help' target='_blank'>HELP</a>";
        $return['activationLink'] = $activationLink;
        $return['helpLink'] = $helpLink;
        return $return;
    }

	
	
	function usagestatistics($prjlist,$user_val) {
        $easycasecls = ClassRegistry::init('Easycase');
		if(!empty($prjlist)){
			$project_idlists = implode(',',$prjlist);
			$prv_date = date('Y-m-d', strtotime('last monday'));
			$last_week_date = date('Y-m-d', strtotime('last monday', strtotime($prv_date)));
			if ($project_idlists) {
				$project_idcond = " FIND_IN_SET(Easycase.project_id,'" . $project_idlists . "') ";
			} else {
				$project_idcond = " !Easycase.project_id ";
			}

			$clt_sql = '';
			if (defined('CR') && CR==1 && $user_val['CompanyUser']['is_client'] == 1) {
				$clt_sql = " AND ((Easycase.client_status = " . $user_val['CompanyUser']['is_client'] . " AND Easycase.user_id = " . $user_val['User']['id'] . ") OR Easycase.client_status != " . $user_val['CompanyUser']['is_client'] . ")";
			}

			if(defined(TSG) && TSG ==1){
				// to list out the id of task status group which have highest sequence order
				$this->loadModel('Workflow');
				$this->loadModel('Project');
				$wrflw_lgndId = array();
				$statuses_maxlist = $this->Project->query("SELECT p.id AS prjct_id,w.id As wrkflow_id,s.id as status_id,s.name  from projects as p LEFT JOIN workflows as w ON p.workflow_id = w.id LEFT JOIN statuses as s ON w.id = s.workflow_id where p.workflow_id !=0 AND w.is_active =1 AND s.id =(Select id from statuses where statuses.workflow_id = w.id ORDER BY statuses.seq_order DESC LIMIT 1 )");
				foreach ($statuses_maxlist as $k => $v) {
					$wrflw_lgndId[] = $v['s']['status_id'];
				}
				array_push($wrflw_lgndId, 3);
			}
			$total_task_cr_prv_week = 0;
			$total_task_upd_prv_week = 0;
			$prv_wk_tot_hr_spent = 0;
			$prev_wk_proj_idlist = '';
			$prev_wk_closed_tasks = 0;
			$prev_wk_storage_usage = 0;
			$prev_wk_ecase_idlist = '';
			$prev_wk_ecase_idlists = array();
			$prev_wk_proj_idlists = array();
			if (defined('TLG') && TLG == 1) {
				$taskid_sql = "SELECT id AS task_ids FROM easycases AS Easycase "
						. "WHERE Easycase.isactive=1 AND " . $project_idcond . "" . $clt_sql . " "
						. "AND (DATE(Easycase.dt_created) BETWEEN '" . $last_week_date . "' AND '" . $prv_date . "') ";

				$sql = "SELECT COUNT(Easycase.id) AS cnt,
			(SELECT SUM(LogTime.total_hours) AS hours 
						FROM log_times AS LogTime 
						LEFT JOIN easycases AS Easycase1 ON LogTime.task_id=Easycase1.id AND LogTime.project_id=Easycase1.project_id 
						WHERE Easycase1.isactive=1 AND FIND_IN_SET(LogTime.task_id,GROUP_CONCAT(DISTINCT Easycase.id))) AS hr_spent,"
						. "GROUP_CONCAT(DISTINCT Easycase.project_id) AS project_ids, "
						. "GROUP_CONCAT(DISTINCT Easycase.id) AS easycase_ids, Easycase.istype, DATE(Easycase.dt_created) AS created_date "
						. "FROM easycases as Easycase "
						. "WHERE Easycase.id IN ($taskid_sql) "
						. "GROUP BY Easycase.istype";
				#echo $sql;exit;
				$lastweektask = $easycasecls->query($sql);
			} else {
				$lastweektask = $easycasecls->query("SELECT COUNT(Easycase.id) as cnt,SUM(Easycase.hours)as hr_spent,GROUP_CONCAT(Easycase.project_id) as project_ids, GROUP_CONCAT(Easycase.id) as easycase_ids  ,Easycase.istype, DATE(Easycase.dt_created) as created_date FROM easycases as Easycase WHERE Easycase.isactive=1 "  . $clt_sql . " AND Easycase.project_id!=0 AND " . $project_idcond . " AND (DATE(Easycase.dt_created )< '" . $prv_date . "' AND DATE(Easycase.dt_created )>= '" . $last_week_date . "' ) GROUP BY Easycase.istype");
			}			
			if ($lastweektask) {
				$prv_wk_tot_hr_spent = @$lastweektask[0][0]['hr_spent'] + @$lastweektask[1][0]['hr_spent'];
				if (@$lastweektask[0]['Easycase']['istype'] == 1) {
					$total_task_cr_prv_week = @$lastweektask[0][0]['cnt'];
				} elseif (@$lastweektask[0]['Easycase']['istype'] == 2) {
					$total_task_upd_prv_week = @$lastweektask[0][0]['cnt'];
					;
				}
				if (@$lastweektask[1]['Easycase']['istype'] == 1) {
					$total_task_cr_prv_week = @$lastweektask[1][0]['cnt'];
				} elseif (@$lastweektask[1]['Easycase']['istype'] == 2) {
					$total_task_upd_prv_week = @$lastweektask[1][0]['cnt'];
				}
				$prev_wk_proj_idlist = @$lastweektask[0][0]['project_ids'] . "," . @$lastweektask[1][0]['project_ids'];
				$prev_wk_ecase_idlist = @$lastweektask[0][0]['easycase_ids'] . "," . @$lastweektask[1][0]['easycase_ids'];
				if ($prev_wk_proj_idlist) {
					$prev_wk_proj_idlist = trim($prev_wk_proj_idlist, ',');
					if (strstr($prev_wk_proj_idlist, ',')) {
						$prev_wk_proj_idlists = array_unique(explode(',', $prev_wk_proj_idlist));
					} else {
						$prev_wk_proj_idlists[] = $prev_wk_proj_idlist;
					}
					if ($prev_wk_proj_idlist) {
						//$prev_wk_proj_idlist = explode(',',$prev_wk_proj_idlist);
						if (defined('TLG') && TLG == 1) {
							$last_week_closed_cases = $easycasecls->query("SELECT count(easycases.id) as tot from easycases "
									. "WHERE FIND_IN_SET(easycases.project_id,'" . implode(',', $prev_wk_proj_idlists) . "') "
									. "AND easycases.istype='1' AND easycases.isactive='1' AND easycases.legend='3'"
									. " " . $clt_sql . " "
									. "AND (DATE(easycases.dt_created) BETWEEN '" . $last_week_date . "' AND '" . $prv_date . "')");
						} else {
							$last_week_closed_cases = $easycasecls->query("SELECT count(easycases.id) as tot from easycases WHERE FIND_IN_SET(easycases.project_id,'" . implode(',', $prev_wk_proj_idlists) . "') and easycases.istype='1' ".$clt_sql." AND easycases.isactive='1' AND easycases.legend='3'AND (DATE(easycases.dt_created) <'" . $prv_date . "' AND DATE(easycases.dt_created) >='" . $last_week_date . "')");
						}
						if ($last_week_closed_cases) {
							$prev_wk_closed_tasks = $last_week_closed_cases[0][0]['tot'];
						}
					}
				}
				// Calculating Prevous week storage usage	
				if ($prev_wk_ecase_idlist) {
					$prev_wk_ecase_idlist = trim($prev_wk_ecase_idlist, ',');
					if (strstr($prev_wk_ecase_idlist, ',')) {
						$prev_wk_ecase_idlist = explode(',', $prev_wk_ecase_idlist);
						$prev_wk_ecase_idlists = array_unique($prev_wk_ecase_idlist);
					} else {
						$prev_wk_ecase_idlists[] = $prev_wk_ecase_idlist;
					}
					if ($prev_wk_ecase_idlist) {
						$casefilecls = ClassRegistry::init('CaseFile');
						$last_week_used_storage = $casefilecls->query("SELECT SUM(file_size) AS file_size  FROM case_files   WHERE FIND_IN_SET(easycase_id,'" . implode(',', $prev_wk_ecase_idlists) . "')");
						if ($last_week_used_storage) {
							$prev_wk_storage_usage = round(($last_week_used_storage[0][0]['file_size'] / 1024), 2);
						}
					}
				}
			}
			$json_arr['prev_wk_closed_tasks'] = $prev_wk_closed_tasks;
			$json_arr['prev_wk_storage_usage'] = $prev_wk_storage_usage;
			$json_arr['prv_wk_tot_hr_spent'] = $prv_wk_tot_hr_spent;
			$json_arr['total_task_cr_prv_week'] = $total_task_cr_prv_week;
			$json_arr['total_task_upd_prv_week'] = $total_task_upd_prv_week;
			return $json_arr;
		}
		$json_arr['prev_wk_closed_tasks'] = 0;
        $json_arr['prev_wk_storage_usage'] = 0;
        $json_arr['prv_wk_tot_hr_spent'] = 0;
        $json_arr['total_task_cr_prv_week'] = 0;
        $json_arr['total_task_upd_prv_week'] = 0;
		return $json_arr;
    }
	
    /**
     * @method weeklyusagedetails sends weekly usage of the company with respect to the project
     */
    function weeklyusagedetails() {	
        $this->Project->recursive = -1;
        $Timezone = ClassRegistry::init('Timezone');
        $usernotificationcls = ClassRegistry::init('UserNotification');

        $user_ids = $this->CompanyUser->find('list', array('conditions' => array('user_type < ' => 3, 'is_active' => 1, 'user_id'), 'fields' => array('id', 'user_id')));
        $user_lists = $usernotificationcls->find('list', array('conditions' => array('user_id' => $user_ids, 'weekly_usage_alert' => 1), 'fields' => array('id', 'user_id')));

        $this->CompanyUser->recursive = -1;
        $client_cond = '';
        if (defined('CR') && CR==1){            
            $client_cond = ",CompanyUser.is_client";
        }

        $user_details = $this->CompanyUser->find('all', array('joins' => array(
                array('table' => 'users',
                    'alias' => 'User',
                    'type' => 'inner',
                    'conditions' => array('CompanyUser.user_id = User.id', 'User.id' => $user_lists, 'CompanyUser.is_active' => 1, 'CompanyUser.user_type < ' => 3)),
                array('table' => 'companies',
                    'alias' => 'Company',
                    'type' => 'inner',
                    'conditions' => array('CompanyUser.company_id=Company.id', 'Company.is_active=1'))), 'fields' => "Company.id,DATE(Company.created) AS dt_created,User.timezone_id,User.id,User.name,User.last_name,User.email,Company.name,Company.seo_url".$client_cond));

			#print_r($user_details);          exit; 

        $timezn = $Timezone->find('all', array('fields' => array('Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.id')));

        foreach ($timezn AS $k => $tz) {
            $tzone[$tz['Timezone']['id']] = $tz['Timezone'];
        }
        $prv_date = date('Y-m-d', strtotime('-1 week'));
        $last_week_date = date('Y-m-d', strtotime('-2 week'));
        for ($i = 1; $i <= 7; $i++) {
            $last7days[] = date('Y-m-d', strtotime('-' . $i . ' day'));
        }
        foreach ($user_details AS $key => $val) {
            $message = '<div><img src="' . HTTP_ROOT . 'img/images/logo_outer.png"/><br/>';
            //$message .= "<div style='font-family:verdana;font-size:12px;'>Hi ".$val['User']['name'].'</div><br/>';
            $timezone_details = '';
            $timezone_details = $tzone[$val['User']['timezone_id']];
            $dateCurnt = $this->Tmzone->GetDateTime($val['User']['timezone_id'], $tzone[$val['User']['timezone_id']]['gmt_offset'], $tzone[$val['User']['timezone_id']]['dst_offset'], '', GMT_DATETIME, "datetime");
            $dateCurnt1 = explode(' ', $dateCurnt);
            $tim = $dateCurnt1['0'];
            $min = date('i', strtotime($dateCurnt));
            $hour = date('H', strtotime($dateCurnt));
            $day = gmdate('N', strtotime($dateCurnt)); // Day number in numeric value
            $dt = gmdate('j', strtotime($dateCurnt)); //Date in single numeric value
            $month = gmdate('m', strtotime($dateCurnt));
            $lastDate = gmdate('Y-m-d');
            $frmdt = date("m/d/Y", (strtotime($dateCurnt) - (7 * 24 * 60 * 60)));
            $todt = date("m/d/Y", strtotime($dateCurnt) - (24 * 60 * 60));
            $subject = "Orangescrum Usage Report " . $frmdt . " - " . $todt;
            $header = '<div style="font-family:verdana;font-size:12px;color:#333;padding:0;margin:0;border:1px solid #ccc;float:left;width:600px;">
			<div style="background:#555555;padding:5px 10px;margin-bottom:15px;">
				<div style="float:left;color:#FFF;font-size:26px;font-weight:bold;">' . ucfirst($val['Company']['name']) . '</div>
				<div style="float:right;color:#fff;font-size:14px;">
				<div style="font-size:12px;text-align:right;padding-top:7px;font-weight:bold">' . date("D, M d", (strtotime($dateCurnt) - (7 * 24 * 60 * 60))) . "&nbsp;-&nbsp;" . date("D, M d", strtotime($dateCurnt) - (24 * 60 * 60)) . ' </div>
			</div>
			<div style="clear:both"></div>	
			</div><div style="padding:10px">';
            $message_top = '<div style="font-family:verdana;font-size:12px;">Hi ' . $val['User']['name'] . ',<br/><br/>Here is your weekly usage report,</div><br/><div style="clear:both"></div> ';
            #echo $prv_date . "<br/>";
            if ($day == 1 && $hour == '07' && $min < 30) {
                //if($hour=='07' && $min<30){
                $userlogin = $this->CompanyUser->query('SELECT COUNT(u.id) as notlogged,(SELECT COUNT(*) FROM company_users WHERE company_id=' . $val['Company']['id'] . ' AND is_active=1) AS tot FROM users u , company_users cu WHERE u.id=cu.user_id AND cu.is_active=1 AND cu.company_id=' . $val['Company']['id'] . ' AND DATE(u.dt_last_logout)<="' . $prv_date . '" ');
                //echo 'SELECT COUNT(u.id) FROM users u , company_users cu WHERE u.id=cu.user_id AND cu.company_id='.$val['Company']['id'].' AND DATE(u.dt_last_logout)<="'.$prv_date.'" ';
                //if($userlogin[0][0]['notlogged']){
                if ($userlogin[0][0]['notlogged'] == $userlogin[0][0]['tot']) {
                    $logedin_color = '#696969';
                    $loggedin_per = 0;
                    $message .='<div style="font-size: 17px;">No User has logged in to the system since last week.</div><br/><br/>';
                } else {
                    $loggedin_users = $userlogin[0][0]['tot'] - $userlogin[0][0]['notlogged'];
                    $loggedin_per = round(($loggedin_users / $userlogin[0][0]['tot']) * 100);
                    if ($userlogin[0][0]['notlogged'] <= ($userlogin[0][0]['tot'] / 2)) {
                        $logedin_color = '#078BCB';
                    } else {
                        $logedin_color = '#ED7C16';
                    }
                    $message .='<div style="font-size: 17px;"><b>' . $userlogin[0][0]['notlogged'] . '</b> Out of <b>' . $userlogin[0][0]['tot'] . '</b> User has not logged in to the system since last week.</div><br/><br/>';
                }
                //}
				$projectidlists = null;
				$projectidlists = $this->Project->find("list", array('conditions' => array('Project.company_id' => $val['Company']['id'], 'isactive' => 1), 'fields' => array('Project.id')));
				$project_idcond = ' ';
				
				if ($projectidlists) {
					$project_idcond = ' FIND_IN_SET(Easycase.project_id ,"' . implode(',', $projectidlists) . '") ';
				} else {
					$project_idcond = " !Easycase.project_id ";
				}
				if (defined('TLG') && TLG == 1) {
					$taskid_sql = "SELECT id AS task_ids FROM easycases AS Easycase "
                    . "WHERE Easycase.isactive=1 AND DATE(Easycase.dt_created )>='" . $prv_date . "' AND " . $project_idcond . "";
					
					$caseAll = $this->Easycase->query("SELECT COUNT(Easycase.id) AS cnt,"
                    . "(SELECT ROUND((SUM(LogTime.total_hours)/3600),1) as hours "
                    . "FROM log_times as LogTime "
                    . "LEFT JOIN easycases AS Easycase1 ON LogTime.task_id=Easycase1.id AND LogTime.project_id=Easycase1.project_id "
                    . "WHERE Easycase1.isactive=1 AND FIND_IN_SET(LogTime.task_id,GROUP_CONCAT(DISTINCT Easycase.id))) AS hr_spent, "
                    . "GROUP_CONCAT(DISTINCT Easycase.project_id) AS project_ids, "
                    . "GROUP_CONCAT(DISTINCT Easycase.id) AS easycase_ids, "
                    . "Easycase.istype, DATE(Easycase.dt_created) AS created_date "
                    . "FROM easycases as Easycase "
                    . "WHERE Easycase.id IN ($taskid_sql) "
                    . "GROUP BY Easycase.istype,DATE(Easycase.dt_created)");
				} else {
					$caseAll = $this->Easycase->query("SELECT COUNT(Easycase.id) as cnt,SUM(Easycase.hours)as hr_spent,GROUP_CONCAT(Easycase.project_id) as project_ids, GROUP_CONCAT(Easycase.id) as easycase_ids  ,Easycase.istype, DATE(Easycase.dt_created) as created_date FROM easycases as Easycase WHERE Easycase.isactive=1 AND Easycase.project_id!=0 AND " . $project_idcond . " AND DATE(Easycase.dt_created )>='" . $prv_date . "' GROUP BY Easycase.istype,DATE(Easycase.dt_created)");
				}

				$json_static = $this->usagestatistics($projectidlists,$val);
				list($prev_wk_closed_tasks, $prev_wk_storage_usage, $prv_wk_tot_hr_spent,$total_task_cr_prv_week,$total_task_upd_prv_week) =  array_values($json_static);
				
                $message .="</div>";
				
				$project_idlist = '';
				$easycase_idlist = '';
				$total_task_cr_current_week = 0;
				$total_task_upd_current_week = 0;
				$curr_wk_tot_hr_spent = 0;
				foreach ($last7days as $key1 => $val1) {
					$no_of_tasks = 0;
					$no_of_tasks_upd = 0;
					$total_hr_spent = 0;
					foreach ($caseAll AS $k => $value) {
						if (strtotime($value[0]['created_date']) == strtotime($val1)) {
							if ($value['Easycase']['istype'] == 1) {
								$no_of_tasks = $value[0]['cnt'];
							} else {
								$no_of_tasks_upd = $value[0]['cnt'];
							}
							$project_idlist .= $value[0]['project_ids'] . ',';
							$easycase_idlist .= $value[0]['easycase_ids'] . ',';
						}
					}
					$total_task_cr_current_week +=$no_of_tasks;
					$total_task_upd_current_week +=$no_of_tasks_upd;
				}
                $proj_cond = " ";
                $casefiles_cond = " ";
                if ($project_idlist) {
                    $project_idlist = trim($project_idlist, ',');
                    $project_idlist = explode(',', $project_idlist);
                    $project_idlist = array_unique($project_idlist);
                    $proj_cond .=" OR  FIND_IN_SET(Project.id,'" . implode(',', $project_idlist) . "')";
                }
                if ($easycase_idlist) {
                    $easycase_idlist = trim($easycase_idlist, ',');
                    $easycase_idlist = explode(',', $easycase_idlist);
                    $easycase_idlist = array_unique($easycase_idlist);
                    $casefiles_cond .=" AND  FIND_IN_SET(case_files.easycase_id,'" . implode(',', $easycase_idlist) . "')";
                } else {
                    $casefiles_cond .=" AND !case_files.easycase_id ";
                }
                // Project details 
                $status_condition = "SELECT MAX(Status.id) AS id FROM `statuses` AS `Status`  WHERE `Status`.`workflow_id` = Project.workflow_id  GROUP BY `Status`.`workflow_id`";

				if (defined('TSG') && TSG == 1) {
					$wrflw_lgndId = array();
					$statuses_maxlist = $this->Project->query("SELECT p.id AS prjct_id,w.id As wrkflow_id,s.id as status_id,s.name  from projects as p LEFT JOIN workflows as w ON p.workflow_id = w.id LEFT JOIN statuses as s ON w.id = s.workflow_id where p.workflow_id !=0 AND w.is_active =1 AND s.id =(Select id from statuses where statuses.workflow_id = w.id ORDER BY statuses.seq_order DESC LIMIT 1 )");
					foreach ($statuses_maxlist as $k => $v) {
						$wrflw_lgndId[] = $v['s']['status_id'];
					}
					array_push($wrflw_lgndId, 3);
				}
				
				if (defined('TSG') && TSG == 1 && defined('TLG') && TLG == 1) {			
					$getProj = $this->Project->query("SELECT id,uniq_id,dt_created,name,user_id,project_type,short_name,isactive,workflow_id,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "') as totalcase,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' AND easycases.isactive='1' AND easycases.legend IN (" . implode(',', $wrflw_lgndId) . ") AND DATE(easycases.dt_created) >='" . $prv_date . "') as closedcase, (SELECT SUM(LogTime.total_hours) AS hours FROM log_times as LogTime LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id WHERE Easycase.isactive=1 AND LogTime.project_id=Project.id AND DATE(LogTime.start_datetime) >='" . $prv_date . "' AND DATE(LogTime.start_datetime) <='$lastDate') as totalhours, (SELECT SUM(case_files.file_size) AS file_size  FROM case_files   WHERE case_files.project_id=Project.id AND 1 " . $casefiles_cond . ") AS storage_used FROM projects AS Project WHERE  Project.company_id=" . $user_details['0']['Company']['id'] . " AND Project.short_name!='WCOS' AND (Project.dt_created >='" . $prv_date . "' " . $proj_cond . ") ORDER BY Project.name ASC");
										
				} else if (defined('TSG') && TSG == 1) {
					$getProj = $this->Project->query("SELECT id,uniq_id,dt_created,name,user_id,project_type,short_name,isactive,workflow_id,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "') as totalcase,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' AND easycases.isactive='1' AND easycases.legend IN (" . implode(',', $wrflw_lgndId) . ") AND DATE(easycases.dt_created) >='" . $prv_date . "') as closedcase,(select ROUND(SUM(easycases.hours), 1) as hours from easycases where easycases.project_id=Project.id and easycases.istype='2' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "' ) as totalhours,(SELECT SUM(case_files.file_size) AS file_size  FROM case_files   WHERE case_files.project_id=Project.id AND 1 " . $casefiles_cond . ") AS storage_used FROM projects AS Project WHERE  Project.company_id=" . $user_details['0']['Company']['id'] . " AND Project.short_name!='WCOS' AND (Project.dt_created >='" . $prv_date . "' " . $proj_cond . ") ORDER BY Project.name ASC");
										
				} else if (defined('TLG') && TLG == 1) {
					$pjsql = "SELECT id,uniq_id,dt_created,name,user_id,project_type,short_name,isactive,"
							. "(SELECT count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "') as totalcase,"
							. "(SELECT count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' AND easycases.isactive='1' AND easycases.legend='3'AND DATE(easycases.dt_created) >='" . $prv_date . "') as closedcase,"
							. "(SELECT SUM(LogTime.total_hours) AS hours "
							. "FROM log_times as LogTime "
							. "LEFT JOIN easycases AS Easycase ON LogTime.task_id=Easycase.id AND LogTime.project_id=Easycase.project_id "
							. "WHERE Easycase.isactive=1 AND LogTime.project_id=Project.id "
							. "AND DATE(LogTime.start_datetime) >='" . $prv_date . "' AND DATE(LogTime.start_datetime) <='$lastDate') as totalhours,"
							. "(SELECT SUM(case_files.file_size) AS file_size  FROM case_files   WHERE case_files.project_id=Project.id AND case_files.isactive=1 AND 1 " . $casefiles_cond . ") AS storage_used "
							. "FROM projects AS Project "
							. "WHERE  Project.company_id=" . $user_details['0']['Company']['id'] . " AND Project.short_name!='WCOS' "
							. "AND (Project.dt_created >='" . $prv_date . "' " . $proj_cond . ") "
							. "ORDER BY Project.name ASC";
					#print($pjsql);exit;
					$getProj = $this->Project->query($pjsql);
				} else {
					$getProj = $this->Project->query("SELECT id,uniq_id,dt_created,name,user_id,project_type,short_name,isactive,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "') as totalcase,(select count(easycases.id) as tot from easycases where easycases.project_id=Project.id and easycases.istype='1' AND easycases.isactive='1' AND easycases.legend='3'AND DATE(easycases.dt_created) >='" . $prv_date . "') as closedcase,(select ROUND(SUM(easycases.hours), 1) as hours from easycases where easycases.project_id=Project.id and easycases.istype='2' and easycases.isactive='1' AND DATE(easycases.dt_created) >='" . $prv_date . "' ) as totalhours,(SELECT SUM(case_files.file_size) AS file_size  FROM case_files   WHERE case_files.project_id=Project.id AND 1 " . $casefiles_cond . ") AS storage_used FROM projects AS Project WHERE  Project.company_id=" . $user_details['0']['Company']['id'] . " AND Project.short_name!='WCOS' AND (Project.dt_created >='" . $prv_date . "' " . $proj_cond . ") ORDER BY Project.name ASC");
				}
				
                $curr_wk_tot_closed_tasks = 0;
                $curr_wk_tot_storage_usage = 0;
                if ($getProj) {
                    $message .= '<div style="clear:both"></div><br/><div style="padding:10px;background:#F3F3F3;font-size:15px;font-weight:bold;">Project Status of the Week</div><br/><div style="clear:both"></div>';
                    $message .= '<div style="border:1px solid #EEEEEE;background:#F8F8F8;box-shadow:0px 0px 1px #fff inset">
					<div style="width:140px;height:20px;border:1px solid #fff;float:left;text-align:center;padding:5px;font-weight:bold">Project</div>
					<div style="width:140px;height:20px;border:1px solid #fff;float:left;text-align:center;padding:5px;font-weight:bold">Closed/Total Tasks</div>
					<div style="width:65px;height:20px;border:1px solid #fff;float:left;text-align:center;padding:5px;font-weight:bold">Hours</div>
					<div style="width:65px;height:20px;border:1px solid #fff;float:left;text-align:center;padding:5px;font-weight:bold">Usage</div>
					<div style="clear:both"></div>';
                    foreach ($getProj AS $pkey => $pval) {
					$tot_cases = $pval[0]['totalcase'] ? $pval[0]['totalcase'] : 0;
					$tot_hrs = $pval[0]['totalhours'] ? round($pval[0]['totalhours']/3600,1) : '0.0';
					$curr_wk_tot_hr_spent += $tot_hrs;
					$tot_close = $pval[0]['closedcase'] ? $pval[0]['closedcase'] : 0;
					$curr_wk_tot_closed_tasks +=$tot_close;
					$tot_users = $pval[0]['totusers'] ? $pval[0]['totusers'] : 0;
					if ($pval[0]['storage_used']) {
						$tot_storage = number_format(($pval[0]['storage_used'] / 1024), 2);
						$curr_wk_tot_storage_usage +=$tot_storage;
						if ($tot_storage >= 1024) {
							$tot_storage = number_format(($tot_storage / 1024), 2) . " Gb";
						} else {
							$tot_storage .=" Mb";
						}
					} else {
						$tot_storage = "0 Mb";
					}

                        $tot_cases = $pval[0]['totalcase'] ? $pval[0]['totalcase'] : 0;
                        //$message .='<div style="width:85px;height:20px;border:1px solid #fff;float:left;text-align:left;padding:5px">'.date('D, M d',strtotime($pval['Project']['dt_created'])).'</div>';
                        $message .='<div style="width:140px;height:20px;border:1px solid #fff;float:left;text-align:left;padding:5px">' . $pval['Project']['name'] . '</div>';
                        $message .='<div style="width:140px;height:20px;border:1px solid #fff;float:left;text-align:right;padding:5px"><b>' . $tot_close . '</b>/' . $tot_cases . '</div>';
                        $message .='<div style="width:65px;height:20px;border:1px solid #fff;float:left;text-align:right;padding:5px">' . $tot_hrs . '</div>';
                        $message .='<div style="width:65px;height:20px;border:1px solid #fff;float:left;text-align:right;padding:5px">' . $tot_storage . '</div>';
                        $message .='<div style="clear:both"></div>';
                    }
                    $message .='</div>';
                } else {
                    $message .='<div style="clear:both"></div><br/><div style="padding:10px;background:#F3F3F3;font-size:15px;font-weight:bold;">No Project Status on last week</div><br/><div style="clear:both"></div>';
                }
                #echo $curr_wk_tot_closed_tasks . "---" . $curr_wk_tot_storage_usage . "<br/>";
                // All statistics
                $progress_flag = 1;
                if (strtotime($val[0]['dt_created']) >= strtotime($prv_date)) {
                    $progress_flag = 0;
                }
                $statistics_div = '<div style="padding:5px;background:#ECECEC;font-size:15px;font-weight:bold">Statistics - <span style="font-size:12px;;color:#676767">SO FAR THIS WEEK</span></div><br/><div>';
                //Logged in user Statistics 
                $logged_in_statistics = '<div style="float:left">
											<div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $logedin_color . ';">
												<div style="font-size:28px;color:' . $logedin_color . ';font-weight:bold">' . ($userlogin[0][0]["tot"] - $userlogin[0][0]['notlogged']) . '</div><div style="clear:both"></div>
												<div style="color:#666666;margin-top:5px;font-weight:bold">Logged in User</div>
												<div style="clear:both"></div>';
                if ($progress_flag) {
                    $logged_in_statistics .='<div style="width:120px;background:' . $logedin_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
										' . $loggedin_per . '% of Total ' . $userlogin[0][0]["tot"] . '
										</div><div style="color:#666666;margin-top:3px;">Last Week to Date</div>';
                }
                $logged_in_statistics .='</div></div>';

                //Task Created Statistics 
                if ($total_task_cr_current_week || $total_task_cr_prv_week) {
                    if ($total_task_cr_prv_week > 0) {
                        $taskper = round((($total_task_cr_current_week - $total_task_cr_prv_week) / $total_task_cr_prv_week) * 100);
                        if ($taskper > 0) {
                            $task_color = '#078BCB';
                            $task_text = "Up";
                        } else {
                            $task_color = '#ED7C16';
                            $task_text = "Down";
                        }
                    } else {
                        $task_text = "Up";
                        $task_color = '#078BCB';
                        $taskper = $total_task_cr_current_week * 100;
                    }
                } else {
                    $task_text = '';
                    $task_color = '#696969';
                    $taskper = 0;
                }
                $task_statistics = '<div style="float:left">
				<div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $task_color . ';border-left:4px solid #FFF">
					<div style="font-size:28px;color:' . $task_color . ';font-weight:bold">' . $total_task_cr_current_week . '</div>
					<div style="clear:both"></div>
					<div style="color:#666666;margin-top:5px;font-weight:bold">
						Tasks Created
					</div>
					<div style="clear:both"></div>';
                if ($progress_flag) {
                    $task_statistics .='<div style="width:120px;background:' . $task_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
						' . $task_text . ' ' . abs($taskper) . '%  from ' . $total_task_cr_prv_week . '</div>
					<div style="color:#666666;margin-top:3px;">	Last Week to Date</div>';
                }
                $task_statistics .='</div></div>';
                //Task Updated Statistics 	
                if ($total_task_upd_current_week || $total_task_upd_prv_week) {
                    if ($total_task_upd_prv_week > 0) {
                        $taskupdper = round((($total_task_upd_current_week - $total_task_upd_prv_week) / $total_task_upd_prv_week) * 100);
                        if ($taskupdper > 0) {
                            $task_upd_color = '#078BCB';
                            $task_upd_text = 'Up';
                        } else {
                            $task_upd_color = '#ED7C16';
                            $task_upd_text = 'Down';
                        }
                    } else {
                        $task_upd_color = '#078BCB';
                        $task_upd_text = 'Up';
                        $taskupdper = $total_task_upd_current_week * 100;
                    }
                } else {
                    $task_upd_text = '';
                    $task_upd_color = '#696969';
                    $taskupdper = 0;
                }
                $task_upd_statistics = '<div style="float:left">
				<div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $task_upd_color . ';border-left:4px solid #FFF">
					<div style="font-size:28px;color:' . $task_upd_color . ';font-weight:bold">' . $total_task_upd_current_week . '</div>
					<div style="clear:both"></div>
					<div style="color:#666666;margin-top:5px;font-weight:bold">Tasks Updated</div>
					<div style="clear:both"></div>';
                if ($progress_flag) {
                    $task_upd_statistics .='<div style="width:120px;background:' . $task_upd_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
						' . $task_upd_text . ' ' . abs($taskupdper) . '% from ' . $total_task_upd_prv_week . '
					</div>
					<div style="color:#666666;margin-top:3px;">Last Week to Date</div>';
                }
                $task_upd_statistics .='</div></div><div style="clear:both"></div></div><div style="clear:both"></div><br/>';
                // Closed Task Statistics
                if ($curr_wk_tot_closed_tasks || $prev_wk_closed_tasks) {
                    if ($prev_wk_closed_tasks > 0) {
                        $ctaskper = round((($curr_wk_tot_closed_tasks - $prev_wk_closed_tasks) / $prev_wk_closed_tasks) * 100);
                        if ($ctaskper > 0) {
                            $ctask_color = '#078BCB';
                            $ctask_text = 'Up';
                        } else {
                            $ctask_color = '#ED7C16';
                            $ctask_text = 'Down';
                        }
                    } else {
                        $ctask_color = '#078BCB';
                        $task_upd_text = 'Up';
                        $ctaskper = $curr_wk_tot_closed_tasks * 100;
                    }
                } else {
                    $ctask_text = '';
                    $ctask_color = '#696969';
                    $ctaskper = 0;
                }
                $task_closed_statistics = '<div><div style="float:left"><div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $ctask_color . ';">
					<div style="font-size:28px;color:' . $ctask_color . ';font-weight:bold">' . $curr_wk_tot_closed_tasks . '</div>
					<div style="clear:both"></div>
					<div style="color:#666666;margin-top:5px;font-weight:bold">Tasks Closed</div>
					<div style="clear:both"></div>';
                if ($progress_flag) {
                    $task_closed_statistics .='<div style="width:120px;background:' . $ctask_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
						' . $ctask_text . ' ' . abs($ctaskper) . '% from ' . $prev_wk_closed_tasks . '
					</div>
					<div style="color:#666666;margin-top:3px;">Last Week to Date</div>';
                }
                $task_closed_statistics .='</div></div>';

                // Hours Spent Statistics 
                if ($curr_wk_tot_hr_spent || $prv_wk_tot_hr_spent) {
                    if ($prv_wk_tot_hr_spent > 0) {
                        $hstaskper = round((($curr_wk_tot_hr_spent - $prv_wk_tot_hr_spent) / $prv_wk_tot_hr_spent) * 100);
                        if ($hstaskper > 0) {
                            $hstask_color = '#078BCB';
                            $hstask_text = 'Up';
                        } else {
                            $hstask_color = '#ED7C16';
                            $hstask_text = 'Down';
                        }
                    } else {
                        $hstask_color = '#078BCB';
                        $hstask_text = 'Up';
                        $hstaskper = $curr_wk_tot_hr_spent * 100;
                    }
                } else {
                    $hstask_text = '';
                    $hstask_color = '#696969';
                    $hstaskper = 0;
                }
                $hrs_spent_statistics = '<div style="float:left">
				<div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $hstask_color . ';border-left:4px solid #FFF">
				<div style="font-size:28px;color:' . $hstask_color . ';font-weight:bold">' . $curr_wk_tot_hr_spent . '</div>
					<div style="clear:both"></div>
					<div style="color:#666666;margin-top:5px;font-weight:bold">Hours Spent</div>
					<div style="clear:both"></div>';
                if ($progress_flag) {
                    $hrs_spent_statistics .='<div style="width:120px;background:' . $hstask_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
					' . $hstask_text . ' ' . abs($hstaskper) . '% from ' . $prv_wk_tot_hr_spent . '
				</div>
				<div style="color:#666666;margin-top:3px;">Last Week to Date</div>';
                }
                $hrs_spent_statistics .='</div></div>';

                //Storage Usage Statistics
                if ($curr_wk_tot_storage_usage || $prev_wk_storage_usage) {
                    if ($prev_wk_storage_usage > 0) {
                        $storageper = round((($curr_wk_tot_storage_usage - $prev_wk_storage_usage) / $prev_wk_storage_usage) * 100);
                        if ($storageper > 0) {
                            $storage_color = '#078BCB';
                            $storage_text = 'Up';
                        } else {
                            $storage_color = '#ED7C16';
                            $storage_text = 'Down';
                        }
                    } else {
                        $storage_color = '#078BCB';
                        $storage_text = 'Up';
                        $storageper = $curr_wk_tot_storage_usage;
                    }
                } else {
                    $storage_text = '';
                    $storage_color = '#696969';
                    $storageper = 0;
                }
                $storage_usage_statistics = '<div style="float:left">
				<div style="width:190px;padding:10px 0;text-align:center;background:#F8F8F8;border-top:1px solid ' . $storage_color . ';border-left:4px solid #FFF">
					<div style="font-size:28px;color:' . $storage_color . ';font-weight:bold">' . $curr_wk_tot_storage_usage . ' <span style="font-size:18px;">Mb</span></div>
					<div style="clear:both"></div>
					<div style="color:#666666;margin-top:5px;font-weight:bold">Storage Used</div>
					<div style="clear:both"></div>';
                if ($progress_flag) {
                    $storage_usage_statistics .='<div style="width:130px;background:' . $storage_color . ';padding:4px;color:#FFFFFF;font-size:11px;margin:0 auto;-moz-border-radius: 2px;border-radius: 2px;margin-top:10px;">
						' . $storage_text . ' ' . abs($storageper) . '% from ' . $prev_wk_storage_usage . ' Mb
					</div>
					<div style="color:#666666;margin-top:3px;">Last Week to Date</div>';
                }
                $storage_usage_statistics .='</div></div>';

                $statistics_div .=$logged_in_statistics . $task_statistics . $task_upd_statistics . $task_closed_statistics . $hrs_spent_statistics . $storage_usage_statistics;
                $statistics_div .='<div style="clear:both"></div></div><br/><br/>';
                $message .='<div style="clear:both"></div><br/><div style="padding:5px;background:#ECECEC;font-size:15px;font-weight:bold">Summary</div>
		<div><ul><li><b>' . $userlogin[0][0]['notlogged'] . '</b> Out of <b>' . $userlogin[0][0]['tot'] . '</b> User has not logged in to the system since last week.
				</li>
				<li>
					<b>' . $total_task_cr_current_week . '</b> tasks created and <b>' . $total_task_upd_current_week . '</b> updated on last week
				</li>
				<li>
					<b>' . $curr_wk_tot_closed_tasks . '</b> closed out of <b>' . $total_task_cr_current_week . '</b> tasks, <b>' . $curr_wk_tot_hr_spent . '</b> hours spent and <b>' . $curr_wk_tot_storage_usage . '</b> Mb storage used on all projects
				</li>
			</ul>
		</div><br/>';
                $message .="</div></div>";
                $message .="<div style='clear:both'></div><div style='font-size:11px;padding-top:10px;color:#737373'>Don't want to receive this email?<br/>Go to the Orangescrum <a href='" . HTTP_ROOT . "users/email_notifications'>notification settings</a> and say NO to <b>Weekly Usage Report</b></div>";
                $mail_body = $message_top . $header . $statistics_div . $message;
                $to = $val['User']['email'];
                $mail_body = $message_top . $header . $statistics_div . $message;
                #echo $mail_body;exit;
                $this->Sendgrid->sendGridEmail(FROM_EMAIL, $to, $subject, $mail_body, "usagedetails");
            }
        }
        echo 'success';
        exit;
    }

    /**
     * @method delDownloadTask will check the .zip file and delete those file which are created b4 2days.
     * @return bool 
     */
    function delDownloadTask() {
        $files = array();
        $arr = array();
        $path = DOWNLOAD_TASK_PATH . 'zipTask/';
        foreach (new DirectoryIterator($path) as $fileInfo) {
            if ($fileInfo->getFileName() == '.' || $fileInfo->getFileName() == '..')
                continue;
            $files[$fileInfo->getFileName()] = $fileInfo->getCTime();
            if ((time() - ($fileInfo->getCTime())) >= (12 * 60 * 60)) {
                $arr[] = $fileInfo->getFileName();
                unlink($path . $fileInfo->getFileName());
            }
        }
        exit;
        //arsort($files);
        //echo "<pre>";print_r($files);print_r($arr);exit;
    }

    /**
     * @method removePreviousZips - will check the .zip file and delete those file which are created b4 24 hours.
     * @return bool 
     */
    function removePreviousZips() {
        try {
            $s3 = new S3(awsAccessKey, awsSecretKey);
            // Get the contents of our bucket
            $contents = $s3->getBucket(DOWNLOAD_BUCKET_NAME);
            $date_before_24hr = date('F_dS_Y', strtotime('-24 hours', time()));
            if (isset($contents) && $contents != '') {
                foreach ($contents as $file) {
                    $fname = $file['name'];
                    $t_fname = explode('_', $fname);
                    $file_created_date = $t_fname[3] . '_' . $t_fname[4] . '_' . substr($t_fname[5], 0, 4);
                    if ($date_before_24hr == $file_created_date) {
                        $s3->deleteObject(DOWNLOAD_BUCKET_NAME, $fname);
                    }
                }
                die();
            }
        } catch (Exception $e) {
            //print $e->getMessage();exit;
        }
    }

    /**
     * @method removeTempFiles From S3 - will check the temp file and delete those file which are created b4 24 hours.
     * @return bool 
     */
    function removeTempFilesFromS3() {
        try {
            $s3 = new S3(awsAccessKey, awsSecretKey);
            // Get the contents of our bucket
            $contents = $s3->getBucket(BUCKET_NAME, DIR_CASE_FILES_S3_FOLDER_TEMP);
            $date_before_24hr = date('F_dS_Y', strtotime('-24 hours', time()));
            if (isset($contents) && $contents != '') {
                foreach ($contents as $file) {
                    $fname = $file['name'];
                    $content1s = $s3->getObjectInfo(BUCKET_NAME, $fname);
                    if ($content1s) {
                        $file_created_date = date('F_dS_Y', $content1s['time']);
                        if ($date_before_24hr == $file_created_date) {
                            $s3->deleteObject(BUCKET_NAME, $fname);
                        }
                    }
                }
                die();
            }
        } catch (Exception $e) {
            //print $e->getMessage();exit;
        }
    }
    
    /*
     * Author Satyajeet
     * To send all types users alert email regarding 
     */
    function TimelogWeeklyReminder(){
        $this->loadModel('Addon');
        $tlg = $this->Addon->find('first', array('conditions' => array('Addon.name' => 'TLG'), 'fields' => array('Addon.isactive')));
        if(!empty($tlg) && $tlg['Addon']['isactive'] == 1){
            $this->loadModel('Easycase');
            $endDate = date('Y-m-d', strtotime(GMT_DATETIME));
            $startDate = date('Y-m-d', strtotime($endDate . " -7 days"));
            $sql = "SELECT Easycase.*, Project.name, CONCAT(' ', User.name, User.last_name) AS name, User.email from easycases AS Easycase inner join projects as project on Easycase.project_id=Project.id inner join users AS User on Easycase.assign_to=User.id where Easycase.id not in(select LogTime.task_id from log_times AS LogTime) AND DATE(Easycase.dt_created) >='$startDate' AND DATE(Easycase.dt_created) <='$endDate' AND Easycase.istype=1 AND Easycase.legend != 1 AND Easycase.isactive=1 group by Project.company_id, Easycase.project_id, Easycase.assign_to, Easycase.id Order by Easycase.dt_created ASC";
            $easycases = $this->Easycase->query($sql);
            $finalData = array();
            foreach($easycases as $k => $val){
                $finalData[$val['User']['email']][] = $val;
            }
            foreach($finalData as $email => $data){
                try{
                    $this->Email->delivery = EMAIL_DELIVERY;
                    $this->Email->to = $email;
                    $this->Email->from = FROM_EMAIL_NOTIFY;
                    $this->Email->subject = "Orangescrum - Timelog Weekly Alert from ".date('d M, Y', strtotime($startDate)). " to ".date('d M, Y', strtotime($endDate));
                    $this->Email->sendAs = 'html';
                    $this->Email->template = "timelog_weekly_alert";
                    $this->set(compact('data', 'startDate', 'endDate'));
                    $this->Sendgrid->sendgridsmtp($this->Email);
                } catch (Exception $e){
                    echo $e->getMessage(). "\n";
                }
            }
            echo "Success";
        }else{
            if(empty($tlg)){
                echo "You have not installed Timelog Gold Add-on yet.";
            }else{
                echo "Timelog Gold Add-on is not active.";
            }
        }
        exit;
    }

/*
     * Author: Satyajeet
     * Create reoccurrences of tasks 
     */

    function createRecurringTasks() {
        $this->loadModel('Addon');
        $rct = $this->Addon->find('first', array('conditions' => array('Addon.name' => 'RCT'), 'fields' => array('Addon.isactive')));
        if (!empty($rct) && $rct['Addon']['isactive'] == 1) {
            $this->loadModel('Easycase');
            $this->Easycase->bindModel(array(
               'hasOne' => array(
                   'RecurringEasycase' => array(
                       'className' => 'RecurringEasycase',
                       'foreignKey' => 'easycase_id'
                   )
               ) 
            ));
            $date = date('Y-m-d', strtotime("+1 day"));
            $curdate = date('Y-m-d');
            $this->loadModel('Timezone');
            $recurringTasks = $this->Easycase->find('all', array('conditions' => array('Easycase.is_recurring' => 1, 'Easycase.istype' => 1, 'Easycase.case_no !=' => 0)));
            foreach ($recurringTasks as $k => $recurringTask) {
                $this->User->unbindModel(array('hasAndBelongsToMany' => array('Project')));
                $userInfo = $this->User->find('first', array('conditions' => array('User.isactive' => 1, 'User.id' => $recurringTask['Easycase']['user_id'])));
                $timezn = $this->Timezone->find('first', array('conditions' => array('Timezone.id' => $userInfo['User']['timezone_id']), 'fields' => array('Timezone.gmt_offset', 'Timezone.dst_offset', 'Timezone.code')));
                App::import('Helper', 'Tmzone');
                $tmzone = new TmzoneHelper(new View(null));
                if (defined('GTLG') && GTLG == 1) {
                    $CompanyHoliday = ClassRegistry::init('CompanyHoliday');
                    $holidayLists = $CompanyHoliday->find('list', array('fields' => array('id', 'holiday'), 'conditions' => array('or' => array('holiday >=' => $curdate)), 'order' => array('created ASC')));
                    $newholidayLists = array();
                    foreach ($holidayLists as $hKey => $hVal) {
                        $holidate = $tmzone->GetDateTime($userInfo['User']['timezone_id'], $timezn['Timezone']['gmt_offset'], $timezn['Timezone']['dst_offset'], $timezn['Timezone']['code'], $hVal, "date");
                        $newholidayLists[$hKey] = $holidate;
                    }
                    $Company = ClassRegistry::init('Company');
                    $companyWorkHours = $Company->find('first', array('fields' => array('Company.work_hour,Company.is_allowed,Company.week_ends')));
                    $weekendArr = explode(',', $companyWorkHours['Company']['week_ends']);
                } else {
                    $weekendArr = array(0, 6);
                }
                if (defined('GTLG') && GTLG == 1 && !in_array($curdate, $newholidayLists) && !in_array($day_name, $weekendArr)) {
                $dateInRecurring = $this->Format->checkDateInRecurring($recurringTask['RecurringEasycase'], $date);
                    if ($dateInRecurring > 0) {
                    unset($recurringTask['Easycase']['id'], $recurringTask['RecurringEasycase']);
                        $sql = "SELECT MAX(Easycase.case_no)+1 AS case_no  FROM `easycases` AS Easycase where  Easycase.project_id=" . $recurringTask['Easycase']['project_id'];
                    $caseNo = $this->Easycase->query($sql);
                    $recurringTask['Easycase']['case_no'] = $caseNo[0][0]['case_no'];
                    $recurringTask['Easycase']['case_count'] = 0;
                        $recurringTask['Easycase']['uniq_id'] = md5(uniqid(mt_rand()));
                        $recurringTask['Easycase']['title'] = $recurringTask['Easycase']['title'] . ' - ' . $dateInRecurring;
                    $recurringTask['Easycase']['due_date'] = date('Y-m-d H:i:s', strtotime($date));
                    $recurringTask['Easycase']['hours'] = '0.0';
                    $recurringTask['Easycase']['istype'] = 1;
                    $recurringTask['Easycase']['format'] = 2;
                    $recurringTask['Easycase']['is_recurring'] = 2;
                    $recurringTask['Easycase']['dt_created'] = GMT_DATETIME;
                    $recurringTask['Easycase']['actual_dt_created'] = GMT_DATETIME;
                    $this->Easycase->save($recurringTask);
}
                } else if (!in_array($day_name, $weekendArr)) {
                    $dateInRecurring = $this->Format->checkDateInRecurring($recurringTask['RecurringEasycase'], $date);
                    if ($dateInRecurring > 0) {
                        unset($recurringTask['Easycase']['id'], $recurringTask['RecurringEasycase']);
                        $sql = "SELECT MAX(Easycase.case_no)+1 AS case_no  FROM `easycases` AS Easycase where  Easycase.project_id=" . $recurringTask['Easycase']['project_id'];
                        $caseNo = $this->Easycase->query($sql);
                        $recurringTask['Easycase']['case_no'] = $caseNo[0][0]['case_no'];
                        $recurringTask['Easycase']['case_count'] = 0;
                        $recurringTask['Easycase']['uniq_id'] = md5(uniqid(mt_rand()));
                        $recurringTask['Easycase']['title'] = $recurringTask['Easycase']['title'] . ' - ' . $dateInRecurring;
                        $recurringTask['Easycase']['due_date'] = date('Y-m-d H:i:s', strtotime($date));
                        $recurringTask['Easycase']['hours'] = '0.0';
                        $recurringTask['Easycase']['istype'] = 1;
                        $recurringTask['Easycase']['format'] = 2;
                        $recurringTask['Easycase']['is_recurring'] = 2;
                        $recurringTask['Easycase']['dt_created'] = GMT_DATETIME;
                        $recurringTask['Easycase']['actual_dt_created'] = GMT_DATETIME;
                        $this->Easycase->save($recurringTask);
            }
                }
            }
            echo "Success";
        } else {
            if (empty($rct)) {
                echo "You have not installed Recurring Task Add-on yet.";
            } else {
                echo "Recurring Task Add-on is not active.";
            }
        }
        exit;
    }

}

?>
