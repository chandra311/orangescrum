<?php

class Easycase extends AppModel {

    var $name = 'Easycase';
    public $virtualFields = array(
        'srttitle' => "IF(LENGTH(Easycase.title) > 90, CONCAT(SUBSTRING(Easycase.title,1,90),'...'), Easycase.title)"
    );

    function formatCases($caseAll, $caseCount, $caseMenuFilters, $closed_cases, $milestones, $projUniq, $usrDtlsArr, $frmt, $dt, $tz, $cq, $chk = null) {

        $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
        $curdtT = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
        $curTime = date('H:i:s', strtotime($curCreated));
        //$count_spchk_hrs = 0;
        $count_sp_hrs = 0;
        $count_est_hrs = 0;
        $count_csp_hrs = 0;
        $milestone_hrs_arr = null;
        if ($caseCount) {
            $chkDateTime = $chkDateTime1 = $projIdcnt = $newpjcnt = $repeatcaseTypeId = $repeatLastUid = $repeatAssgnUid = "";
            $pjname = '';
            $sql = "SELECT Type.* FROM types AS Type WHERE Type.company_id = 0 OR Type.company_id =" . SES_COMP;
            $typeArr = $this->query($sql);
            $cnt_new_chk = 0;
            $lgndarr = null;
            foreach ($caseAll as $caseKey => $getdata) {
                $projId = $getdata['Easycase']['project_id'];
                $newpjcnt = $projId;
                $cnt_new_chk++;
                $actuallyCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $getdata['Easycase']['actual_dt_created'], "datetime");
                $newdate_actualdate = explode(" ", $actuallyCreated);
                $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $getdata['Easycase']['dt_created'], "datetime");
                $newdate = explode(" ", $updated);
                // code for checking whether the task have any files or not
                $CaseFile = ClassRegistry::init('CaseFile');
                $org_data = $this->find('list', array('conditions' => array('Easycase.project_id' => $getdata['Easycase']['project_id'], 'Easycase.case_no' => $getdata['Easycase']['case_no']), 'fields' => array('Easycase.id')));
                $tot_files = $CaseFile->find('count', array('conditions' => array('CaseFile.company_id' => SES_COMP, 'CaseFile.easycase_id' => $org_data)));
                // echo "<pre>";print_r($org_data);
                // $tot_files = $CaseFile->find('count',array('conditions'=>array('CaseFile.easycase_id'=>$getdata['Easycase']['id'])));
                if ($tot_files == 0) {
                    $caseAll[$caseKey]['Easycase']['format'] = 2;
                }
                // end
                if ($chk) {
                    $t_a_ar = null;
                    $t_a_ar = array_merge($getdata['Easycase'], $getdata['EasycaseMilestone'], $getdata['Milestone']);
                    $getdata['Easycase'] = $t_a_ar;
                    $getdata['Easycase']['sphours'] = $getdata[0]['sphours'];
                    $caseAll[$caseKey]['Easycase'] = $getdata['Easycase'];
                    unset($getdata['Milestone']);
                    unset($getdata['EasycaseMilestone']);
                    unset($getdata[0]['sphours']);
                }
                if ($projUniq == 'all') {
                    if (!$lgndarr) {
                        $count_sp_hrs += $getdata['Easycase']['sphours'];
                        $lgndarr[] = $getdata['Easycase']['case_no'];
                    } else if (!in_array($getdata['Easycase']['case_no'], $lgndarr)) {
                        $count_sp_hrs += $getdata['Easycase']['sphours'];
                        array_push($lgndarr, $getdata['Easycase']['case_no']);
                    }
                } else {
                    $count_sp_hrs += $getdata['Easycase']['sphours'];
                }
                if ($getdata['Easycase']['legend'] == 3 || $getdata['Easycase']['legend'] == 5) {
                    $count_csp_hrs += $getdata['Easycase']['sphours'];
                }
                $count_est_hrs += $getdata['Easycase']['estimated_hours'];

                if (empty($getdata['Easycase']['Mid'])) {
                    if (!isset($milestone_hrs_arr['milestone_hrs']['closed_task'])) {
                        $milestone_hrs_arr['milestone_hrs']['tot_task'] = 0;
                        $milestone_hrs_arr['milestone_hrs']['closed_task'] = 0;
                    }
                    if ($getdata['Easycase']['legend'] == 3) {
                        $milestone_hrs_arr['milestone_hrs']['closed_task'] += 1;
                    }
                    $milestone_hrs_arr['milestone_hrs']['tot_task'] += 1;
                }
                #print_r($getdata['Easycase']);
                #print count($caseAll).' == '.$cnt_new_chk;exit;
                if (isset($caseAll[$cnt_new_chk]) && ($getdata['Easycase']['Mid'] != $caseAll[$cnt_new_chk]['Milestone']['Mid'])) {
                    if (empty($getdata['Easycase']['Mid'])) {
                        $milestone_hrs_arr['milestone_hrs']['NA']['tot_est_hr'] = $count_est_hrs;
                        $milestone_hrs_arr['milestone_hrs']['NA']['tot_spt_hr'] = $count_sp_hrs;
                        $milestone_hrs_arr['milestone_hrs']['NA']['count_csp_hrs'] = $count_csp_hrs;
                    } else {
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['tot_est_hr'] = $count_est_hrs;
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['tot_spt_hr'] = $count_sp_hrs;
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['count_csp_hrs'] = $count_csp_hrs;
                    }
                    $count_sp_hrs = 0;
                    $count_est_hrs = 0;
                    $count_csp_hrs = 0;
                } else if (count($caseAll) == $cnt_new_chk) {
                    if (empty($getdata['Easycase']['Mid'])) {
                        $milestone_hrs_arr['milestone_hrs']['NA']['tot_est_hr'] = $count_est_hrs;
                        $milestone_hrs_arr['milestone_hrs']['NA']['tot_spt_hr'] = $count_sp_hrs;
                        $milestone_hrs_arr['milestone_hrs']['NA']['count_csp_hrs'] = $count_csp_hrs;
                    } else {
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['tot_est_hr'] = $count_est_hrs;
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['tot_spt_hr'] = $count_sp_hrs;
                        $milestone_hrs_arr['milestone_hrs'][$getdata['Easycase']['Mid']]['count_csp_hrs'] = $count_csp_hrs;
                    }
                }


                /* if($count_spchk_hrs != 0 && !empty($getdata['Easycase']['Mid']) && $count_spchk_hrs != $getdata['Easycase']['Mid']){
                  if($count_spchk_hrs == -1){
                  $caseAll['milestone_hrs']['NA']['tot_est_hr'] = $count_est_hrs;
                  $caseAll['milestone_hrs']['NA']['tot_spt_hr'] = $count_sp_hrs;
                  }else{
                  $caseAll['milestone_hrs']['__'.$count_spchk_hrs]['tot_est_hr'] = $count_est_hrs;
                  $caseAll['milestone_hrs']['__'.$count_spchk_hrs]['tot_spt_hr'] = $count_sp_hrs;
                  }
                  $count_sp_hrs = 0;
                  $count_est_hrs =0;
                  }
                  $count_spchk_hrs = empty($getdata['Easycase']['Mid'])?-1:$getdata['Easycase']['Mid'];
                  $count_sp_hrs += $getdata['Easycase']['estimated_hours'];
                  $count_est_hrs += $getdata['Easycase']['sphours']; */

                if ($caseMenuFilters == "milestone" && count($milestones)) {
                    $mdata[] = $getdata['Easycase']['Mid'];
                    if ($chkMstone != $getdata['Easycase']['Mid']) {
                        $endDate = $getdata['Easycase']['end_date'] . " " . $curTime;
                        $days = $dt->dateDiff($endDate, $curCreated);

                        $mlstDT = $dt->dateFormatOutputdateTime_day($getdata['Easycase']['end_date'], GMT_DATETIME, 'week');

                        $totalCs = $milestones[$getdata['Easycase']['Mid']]['totalcases'];
                        $totalClosedCs = 0;
                        if (isset($closed_cases[$getdata['Easycase']['Mid']])) {
                            $totalClosedCs = $closed_cases[$getdata['Easycase']['Mid']]['totalclosed'];
                        }
                        $fill = 0;
                        if ($totalClosedCs != 0) {
                            $fill = round((($totalClosedCs / $totalCs) * 100));
                        }

                        $caseAll[$caseKey]['Easycase']['intEndDate'] = strtotime($endDate);
                        $caseAll[$caseKey]['Easycase']['days_diff'] = $days;
                        $caseAll[$caseKey]['Easycase']['mlstDT'] = $mlstDT;
                        $caseAll[$caseKey]['Easycase']['mlstFill'] = $fill;
                        $caseAll[$caseKey]['Easycase']['totalClosedCs'] = $totalClosedCs;
                        $caseAll[$caseKey]['Easycase']['totalCs'] = $totalCs;
                    }
                    if ($projIdcnt != $newpjcnt && $projUniq == 'all') {
                        $pjname = $cq->getProjectName($projId);
                        $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                        $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                    } elseif ($projUniq != 'all') {
                        if (!$pjname) {
                            $pjname = $cq->getProjectName($projId);
                        }
                        $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                        $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                    }
                    if (isset($caseAll[$caseKey]['Milestone']['Massign'])) {
                        $usrDtls = $frmt->getUserDtls($caseAll[$caseKey]['Milestone']['Massign']);
                        $caseAll[$caseKey]['Easycase']['MAssignUser'] = $usrDtls['User']['name'];
                    }

                    //$getdata['Easycase']['Mid'];
                } else {
                    if ($projIdcnt != $newpjcnt && $projUniq == 'all') {
                        $pjname = $cq->getProjectName($projId);
                        $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                        $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                    } elseif ($projUniq != 'all') {
                        if (!$pjname) {
                            $pjname = $cq->getProjectName($projId);
                        }
                        $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                        $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                    }

                    if ($caseCreateDate) {
                        if (($chkDateTime1 != $newdate_actualdate[0])) {
                            $caseAll[$caseKey]['Easycase']['newActuldt'] = $dt->dateFormatOutputdateTime_day($actuallyCreated, $curCreated, 'date');
                        }
                    } else {
                        if (($chkDateTime != $newdate[0]) || ($projIdcnt != $newpjcnt && $projUniq == 'all')) {
                            $caseAll[$caseKey]['Easycase']['newActuldt'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated, 'date');
                        }
                    }
                }

                //case type start
                $caseTypeId = $getdata['Easycase']['type_id'];
                if ($repeatcaseTypeId != $caseTypeId) {

                    //$types = $cq->getTypeArr($caseTypeId,$GLOBALS['TYPE']);
                    $types = $cq->getTypeArr($caseTypeId, $typeArr);
                    if (count($types)) {
                        $typeShortName = $types['Type']['short_name'];
                        $typeName = $types['Type']['name'];
                    } else {
                        $typeShortName = "";
                        $typeName = "";
                    }
                }
                $iconExist = 0;
                if (trim($typeShortName) && file_exists(WWW_ROOT . "img/images/types/" . $typeShortName . ".png")) {
                    $iconExist = 1;
                }
                //$caseAll[$caseKey]['Easycase']['csTdTyp'] = $frmt->todo_typ($typeShortName,$typeName);
                $caseAll[$caseKey]['Easycase']['csTdTyp'] = array($typeShortName, $typeName, $iconExist);
                //case type end
                //Updated column start
                $caseAll[$caseKey]['Easycase']['fbActualDt'] = $dt->facebook_datetimestyle($updated);
                $caseAll[$caseKey]['Easycase']['updted'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated, 'week');
                //Updated column end
                //Title Caption start
                if ($getdata['Easycase']['case_count']) {
                    $getlastUid = $getdata['Easycase']['updated_by'];
                } else {
                    $getlastUid = $getdata['Easycase']['user_id'];
                }

                if ($repeatLastUid != $getlastUid) {
                    if ($getlastUid && $getlastUid != SES_ID) {
                        $usrDtls = $cq->getUserDtlsArr($getlastUid, $usrDtlsArr);
                        $usrName = $frmt->formatText($usrDtls['User']['name']);
                        //$usrShortName = strtoupper($usrDtls['User']['short_name']);
                        $usrShortName = ucfirst($usrDtls['User']['name']);
                    } else {
                        $usrName = "";
                        $usrShortName = __("me", true);
                    }
                }
                $caseAll[$caseKey]['Easycase']['usrName'] = $usrName; //case status title caption name
                $caseAll[$caseKey]['Easycase']['usrShortName'] = $usrShortName; //case status title caption sh_name
                $caseAll[$caseKey]['Easycase']['updtedCapDt'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated); //case status title caption date
                $caseAll[$caseKey]['Easycase']['fbstyle'] = $dt->facebook_style($updated, $curCreated, 'time'); //case status title caption date
                if ($caseMenuFilters == 'milestone') {
                    $caseAll[$caseKey]['Easycase']['proImage'] = $frmt->formatprofileimage($usrDtlsArr[$getlastUid]['User']['photo']); //case status title caption sh_name
                }
                //Title Caption end
                //case status start
                $caseLegend = $getdata['Easycase']['legend'];
                if (defined('TSG') && TSG == 1) {
                    $caseAll[$caseKey]['Easycase']['csSts'] = $frmt->getStatus($caseTypeId, $caseLegend);
                    $caseAll[$caseKey]['Easycase']['stsgrp'] = $frmt->getStatus($caseTypeId, $caseLegend, 'groupby');
                }
                //case status end
                //assign info start
                $caseUserId = $getdata['Easycase']['user_id'];
                $caseAssgnUid = $getdata['Easycase']['assign_to'];
                if ($caseAssgnUid && $repeatAssgnUid != $caseAssgnUid) {
                    if ($caseAssgnUid != SES_ID) {
                        $usrAsgn = $cq->getUserDtlsArr($caseAssgnUid, $usrDtlsArr);
                        $asgnName = $frmt->formatText($usrAsgn['User']['name']);
                        //$asgnShortName = strtoupper($usrAsgn['User']['short_name']);
                        $asgnShortName = $frmt->shortLength(ucfirst($usrAsgn['User']['name']), 13);
                    } else {
                        $asgnShortName = __('me', true);
                        $asgnName = "";
                        if ($caseAssgnUid == 0) {
                            $asgnShortName = __('Unassigned', true);
                            $asgnName = "";
                        }
                    }
                }
                if ($caseAssgnUid == 0) {
                    $asgnShortName = __('Unassigned', true);
                    $asgnName = "";
                } else if (!$caseAssgnUid && $caseUserId == SES_ID) {
                    $asgnShortName = __('me', true);
                    $asgnName = "";
                } elseif (!$caseAssgnUid) {
                    $usrAsgn = $cq->getUserDtlsArr($caseUserId, $usrDtlsArr);
                    $asgnName = $frmt->formatText($usrAsgn['User']['name']);
                    //$asgnShortName = strtoupper($usrAsgn['User']['short_name']);
                    $asgnShortName = $frmt->shortLength(ucfirst($usrAsgn['User']['name']), 10);
                }
                $caseAll[$caseKey]['Easycase']['asgnName'] = $asgnName;
                $caseAll[$caseKey]['Easycase']['asgnShortName'] = $asgnShortName;
                //assign info end
                //Created date start
                //$actualDt1 = $tz->GetDateTime(SES_TIMEZONE,TZ_GMT,TZ_DST,TZ_CODE,$getdata['Easycase']['actual_dt_created'],"datetime");
                //$caseAll[$caseKey]['Easycase']['actualDt1FbDtT'] = $dt->facebook_datetimestyle($actualDt1);
                //$caseAll[$caseKey]['Easycase']['actualDt1FbDt'] = $dt->facebook_style($actualDt1,$curCreated,'date');
                //Created date end
                if (defined('TSG') && TSG != 1 && ($caseLegend == 3 || $caseLegend == 5)) {
                    $caseDueDate = $getdata['Easycase']['due_date'];
                    if ($caseDueDate != "NULL" && date('Y-m-d', strtotime($caseDueDate)) != "0000-00-00" && $caseDueDate != "" && date('Y-m-d', strtotime($caseDueDate)) != "1970-01-01" && $caseDueDate != "0000-00-00 00:00:00") {
                        $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                        $csDuDtFmt = $dt->dateFormatOutputdateTime_day($caseDueDate, $curCreated, 'week');
                    } else {
                        $csDuDtFmtT = '';
                        $csDuDtFmt = '';
                    }
                    $csDueDate = $csDuDtFmt;
                } else {
                    $caseDueDate = $getdata['Easycase']['due_date'];
                    if ($caseDueDate != "NULL" && date('Y-m-d', strtotime($caseDueDate)) != "0000-00-00" && $caseDueDate != "" && date('Y-m-d', strtotime($caseDueDate)) != "1970-01-01" && $caseDueDate != "0000-00-00 00:00:00") {
                        if ($caseDueDate < $curdtT) {
                            $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                            $csDuDtFmt = '<span class="over-due">' . __('Overdue', true) . '</span>';
                            $csDueDate = $dt->dateFormatOutputdateTime_day($caseDueDate, $curCreated, 'week');
                        } else {
                            $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                            $csDuDtFmt = $dt->dateFormatOutputdateTime_day($caseDueDate, $curCreated, 'week');
                            $csDueDate = $csDuDtFmt;
                        }
                    } else {
                        $csDuDtFmtT = '';
                        $csDuDtFmt = '<span class="set-due-dt">' . __('Set Due Date', true) . '</span>';
                        $csDueDate = '';
                    }
                }
                $caseAll[$caseKey]['Easycase']['csDuDtFmtT'] = $csDuDtFmtT;
                $caseAll[$caseKey]['Easycase']['csDuDtFmt'] = $csDuDtFmt;
                $caseAll[$caseKey]['Easycase']['csDueDate'] = $csDueDate;

                $caseAll[$caseKey]['Easycase']['title'] = htmlentities($frmt->convert_ascii($frmt->longstringwrap($getdata['Easycase']['title'])), ENT_QUOTES, 'UTF-8');

                $repeatLastUid = $getlastUid;
                $repeatAssgnUid = $caseAssgnUid;
                $repeatcaseTypeId = $caseTypeId;
                $chkDateTime = $newdate[0];
                $chkDateTime1 = $newdate_actualdate[0];
                $projIdcnt = $newpjcnt;
                if (intval($caseAll[$caseKey]['Easycase']['case_count'])) {
                    $caseAll[$caseKey]['Easycase']['reply_cnt'] = $this->getReplyCount($caseAll[$caseKey]['Easycase']['project_id'], $caseAll[$caseKey]['Easycase']['case_no']);
                } else {
                    $caseAll[$caseKey]['Easycase']['reply_cnt'] = 0;
                }
                unset(
                        $caseAll[$caseKey]['Easycase']['updated_by'], $caseAll[$caseKey]['Easycase']['message'], $caseAll[$caseKey]['Easycase']['hours'], $caseAll[$caseKey]['Easycase']['completed_task'], $caseAll[$caseKey]['Easycase']['due_date'], $caseAll[$caseKey]['Easycase']['istype'], $caseAll[$caseKey]['Easycase']['status'], $caseAll[$caseKey]['Easycase']['dt_created'], $caseAll[$caseKey]['Easycase']['actual_dt_created'], $caseAll[$caseKey]['Easycase']['reply_type'], $caseAll[$caseKey]['Easycase']['id_seq'], $caseAll[$caseKey]['Easycase']['end_date'], $caseAll[$caseKey]['Easycase']['Mproject_id'], $caseAll[$caseKey][0], $caseAll[$caseKey]['User']
                );
            }
        }

        if ($caseMenuFilters == "milestone" && count($milestones)) {
            foreach ($milestones AS $key => $ms) {
                if (!$ms['totalcases']) {
                    $endDate = $ms['end_date'] . " " . $curTime;
                    $days = $dt->dateDiff($endDate, $curCreated);

                    $milestones[$key]['days_diff'] = $days;

                    $mlstDT = $dt->dateFormatOutputdateTime_day($ms['end_date'], GMT_DATETIME, 'week');
                    $milestones[$key]['mlstDT'] = $mlstDT;
                    $milestones[$key]['intEndDate'] = strtotime($ms['end_date']);
                } /* else {
                  unset(
                  $milestones[$key]['title'],
                  $milestones[$key]['uinq_id'],
                  $milestones[$key]['isactive'],
                  $milestones[$key]['user_id']
                  );
                  } */

//				unset(
//					$milestones[$key]['end_date']
//				);
            }
        }
        if ($chk) {
            if (defined('TLG') && TLG === 1) {
                $milestone_hrs_arr = $this->convertTohrMin($milestone_hrs_arr, $frmt);
                return array('caseAll' => $caseAll, 'milestones' => $milestones, 'milestone_hrs_arr' => $milestone_hrs_arr);
            } else {
                return array('caseAll' => $caseAll, 'milestones' => $milestones, 'milestone_hrs_arr' => $milestone_hrs_arr);
            }
        } else {
            return array('caseAll' => $caseAll, 'milestones' => $milestones);
        }
    }

    function convertTohrMin($milestone_hrs_arr, $frmt) {
        foreach ($milestone_hrs_arr['milestone_hrs'] as $kh => $vh) {
            if ($kh != 'tot_task' && $kh != 'closed_task') {
                $milestone_hrs_arr['milestone_hrs'][$kh]['tot_est_hr'] = $frmt->format_time_hrmin($milestone_hrs_arr['milestone_hrs'][$kh]['tot_est_hr']);
                $milestone_hrs_arr['milestone_hrs'][$kh]['tot_spt_hr'] = $frmt->format_time_hrmin($milestone_hrs_arr['milestone_hrs'][$kh]['tot_spt_hr']);
            }
        }
        return $milestone_hrs_arr;
    }

    function getReplyCount($projectId = NULL, $caseNo = NULL) {
        if (isset($projectId) && isset($caseNo)) {
            $sql = "SELECT COUNT(case_no) AS reply_cnt FROM easycases WHERE project_id=" . $projectId . " AND case_no=" . $caseNo . " AND message !='' AND istype=2 GROUP BY case_no";
            $reply = $this->query($sql);
            if (isset($reply) && !empty($reply))
                return $reply['0']['0']['reply_cnt'];
            else
                return 0;
        } else {
            return 0;
        }
    }

    function formatKanbanTask($statusTasklist, $caseCount, $caseMenuFilters, $closed_cases, $milestones, $projUniq, $usrDtlsArr, $frmt, $dt, $tz, $cq) {
        $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
        $curdtT = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");
        $curTime = date('H:i:s', strtotime($curCreated));
        foreach ($statusTasklist as $taskkey => $caseAll) {
            $chkDateTime = $chkDateTime1 = $projIdcnt = $newpjcnt = $repeatcaseTypeId = $repeatLastUid = $repeatAssgnUid = "";
            $pjname = '';
            foreach ($caseAll as $caseKey => $getdata) {
                $projId = $getdata['Easycase']['project_id'];
                $newpjcnt = $projId;
                $actuallyCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $getdata['Easycase']['actual_dt_created'], "datetime");
                $newdate_actualdate = explode(" ", $actuallyCreated);
                $updated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $getdata['Easycase']['dt_created'], "datetime");
                $newdate = explode(" ", $updated);
                if ($projIdcnt != $newpjcnt && $projUniq == 'all') {
                    $pjname = $cq->getProjectName($projId);
                    $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                    $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                } elseif ($projUniq != 'all') {
                    if (!$pjname) {
                        $pjname = $cq->getProjectName($projId);
                    }
                    $caseAll[$caseKey]['Easycase']['pjname'] = $pjname['Project']['name'];
                    $caseAll[$caseKey]['Easycase']['pjUniqid'] = $pjname['Project']['uniq_id'];
                }

                if ($caseCreateDate) {
                    if (($chkDateTime1 != $newdate_actualdate[0])) {
                        $caseAll[$caseKey]['Easycase']['newActuldt'] = $dt->dateFormatOutputdateTime_day($actuallyCreated, $curCreated, 'date');
                    }
                } else {
                    if (($chkDateTime != $newdate[0]) || ($projIdcnt != $newpjcnt && $projUniq == 'all')) {
                        $caseAll[$caseKey]['Easycase']['newActuldt'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated, 'date');
                    }
                }

                //				}
                //case type start
                $caseTypeId = $getdata['Easycase']['type_id'];
                if ($repeatcaseTypeId != $caseTypeId) {
                    $types = $cq->getTypeArr($caseTypeId, $GLOBALS['TYPE']);
                    if (count($types)) {
                        $typeShortName = $types['Type']['short_name'];
                        $typeName = $types['Type']['name'];
                    } else {
                        $typeShortName = "";
                        $typeName = "";
                    }
                }
                $caseAll[$caseKey]['Easycase']['csTdTyp'] = array($typeShortName, $typeName);
                //case type end
                //Updated column start
                $caseAll[$caseKey]['Easycase']['fbActualDt'] = $dt->facebook_datetimestyle($updated);
                $caseAll[$caseKey]['Easycase']['updted'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated, 'week');
                //Updated column end
                //Title Caption start
                if ($getdata['Easycase']['case_count']) {
                    $getlastUid = $getdata['Easycase']['updated_by'];
                    //$caseAll[$caseKey]['Easycase']['proImage'] = $frmt->formatprofileimage($usrDtlsArr[$getlastUid]['User']['photo']); //case status title caption sh_name
                } else {
                    $getlastUid = $getdata['Easycase']['user_id'];
                    //$caseAll[$caseKey]['Easycase']['proImage'] = $frmt->formatprofileimage($getdata['User']['photo']); //case status title caption sh_name
                }
                $caseAll[$caseKey]['Easycase']['proImage'] = $frmt->formatprofileimage($usrDtlsArr[$getlastUid]['User']['photo']); //case status title caption sh_name
                if ($repeatLastUid != $getlastUid) {
                    if ($getlastUid && $getlastUid != SES_ID) {
                        $usrDtls = $cq->getUserDtlsArr($getlastUid, $usrDtlsArr);
                        $usrName = $frmt->formatText($usrDtls['User']['name']);
                        //$usrShortName = strtoupper($usrDtls['User']['short_name']);
                        $usrShortName = ucfirst($usrDtls['User']['name']);
                    } else {
                        $usrName = "";
                        $usrShortName = __("me", true);
                    }
                }
                $caseAll[$caseKey]['Easycase']['usrName'] = $usrName; //case status title caption name
                $caseAll[$caseKey]['Easycase']['usrShortName'] = $usrShortName; //case status title caption sh_name
                //$caseAll[$caseKey]['Easycase']['proImage'] = $frmt->formatprofileimage($getdata['User']['photo']); //case status title caption sh_name
                $caseAll[$caseKey]['Easycase']['updtedCapDt'] = $dt->dateFormatOutputdateTime_day($updated, $curCreated, '', '', 'kanban'); //case status title caption date
                //Title Caption end
                //case status start
                $caseLegend = $getdata['Easycase']['legend'];
                $status = ClassRegistry::init('Status');
                $legend = $status->find('first', array('conditions' => array('Status.id' => $caseLegend)));
                $caseAll[$caseKey]['Easycase']['legendName'] = $legend['Status']['name'];
                $caseAll[$caseKey]['Easycase']['legendColor'] = $legend['Status']['color'];
                //case status end
                //assign info start
                $caseUserId = $getdata['Easycase']['user_id'];
                $caseAssgnUid = $getdata['Easycase']['assign_to'];
                if ($caseAssgnUid && $repeatAssgnUid != $caseAssgnUid) {
                    if ($caseAssgnUid != SES_ID) {
                        $usrAsgn = $cq->getUserDtlsArr($caseAssgnUid, $usrDtlsArr);
                        $asgnName = $frmt->formatText($usrAsgn['User']['name']);
                        //$asgnShortName = strtoupper($usrAsgn['User']['short_name']);
                        $asgnShortName = $frmt->shortLength(ucfirst($usrAsgn['User']['name']), 7);
                    } else {
                        $asgnShortName = __('me', true);
                        $asgnName = "";
                    }
                }
                if (!$caseAssgnUid && $caseUserId == SES_ID) {
                    $asgnShortName = __('me', true);
                    $asgnName = "";
                    if ($caseAssgnUid == 0) {
                        $asgnShortName = __('Unassigned', true);
                        $asgnName = "";
                    }
                } elseif (!$caseAssgnUid) {
                    $usrAsgn = $cq->getUserDtlsArr($caseUserId, $usrDtlsArr);
                    $asgnName = $frmt->formatText($usrAsgn['User']['name']);
                    $asgnShortName = $frmt->shortLength(ucfirst($usrAsgn['User']['name']), 10);
                }
                $caseAll[$caseKey]['Easycase']['asgnName'] = $asgnName;
                $caseAll[$caseKey]['Easycase']['asgnShortName'] = $asgnShortName;
                //assign info end

                if ($caseTypeId == 10 || $caseLegend == 3 || $caseLegend == 5) {
                    $caseDueDate = $getdata['Easycase']['due_date'];
                    if ($caseDueDate != "NULL" && $caseDueDate != "0000-00-00" && $caseDueDate != "" && $caseDueDate != "1970-01-01") {
                        $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                        $csDuDtFmt = $dt->dateFormatOutputdateTime_day($caseDueDate, $curCreated, 'week');
                    } else {
                        $csDuDtFmtT = '';
                        $csDuDtFmt = __('No Due Date', true);
                    }
                } else {
                    $caseDueDate = $getdata['Easycase']['due_date'];
                    if ($caseDueDate != "NULL" && $caseDueDate != "0000-00-00" && $caseDueDate != "" && $caseDueDate != "1970-01-01") {
                        if ($caseDueDate < $curdtT) {
                            $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                            $csDuDtFmt = '<span class="over-due">' . __('Overdue', true) . '</span>';
                        } else {
                            $csDuDtFmtT = $dt->facebook_datestyle($caseDueDate);
                            $csDuDtFmt = $dt->dateFormatOutputdateTime_day($caseDueDate, $curCreated, 'week');
                        }
                    } else {
                        $csDuDtFmtT = '';
                        $csDuDtFmt = '<span class="set-due-dt">' . __('Set Due Date', true) . '</span>';
                    }
                }
                $caseAll[$caseKey]['Easycase']['csDuDtFmtT'] = $csDuDtFmtT;
                $caseAll[$caseKey]['Easycase']['csDuDtFmt'] = $csDuDtFmt;
                $caseAll[$caseKey]['Easycase']['title'] = htmlentities($frmt->shortLength($frmt->formatText(ucfirst($frmt->convert_ascii($frmt->longstringwrap($getdata['Easycase']['title'])))), 50), ENT_QUOTES, 'UTF-8');
                $repeatLastUid = $getlastUid;
                $repeatAssgnUid = $caseAssgnUid;
                $repeatcaseTypeId = $caseTypeId;
                $chkDateTime = $newdate[0];
                $chkDateTime1 = $newdate_actualdate[0];
                $projIdcnt = $newpjcnt;
                unset(
                        $caseAll[$caseKey]['Easycase']['updated_by'], $caseAll[$caseKey]['Easycase']['message'], $caseAll[$caseKey]['Easycase']['hours'], $caseAll[$caseKey]['Easycase']['completed_task'], $caseAll[$caseKey]['Easycase']['due_date'], $caseAll[$caseKey]['Easycase']['istype'], $caseAll[$caseKey]['Easycase']['status'], $caseAll[$caseKey]['Easycase']['dt_created'], $caseAll[$caseKey]['Easycase']['actual_dt_created'], $caseAll[$caseKey]['Easycase']['reply_type'], $caseAll[$caseKey]['Easycase']['id_seq'], $caseAll[$caseKey]['Easycase']['end_date'], $caseAll[$caseKey]['Easycase']['Mproject_id'], $caseAll[$caseKey][0], $caseAll[$caseKey]['User']
                );
            }
            $retarr[$taskkey] = $caseAll;
        }
        return $retarr;
    }

    function formatReplies($sqlcasedata, $allUserArr, $frmt, $cq, $tz, $dt, $chk = null) {
        $CSrepcount = 0;
        //App::import('Component', 'Format');
        //$format = new FormatComponent();
        foreach ($sqlcasedata as $caseKey => $getdata) {
            $caseDtUid = $getdata['Easycase']['user_id'];
            $csUsrDtlArr = $cq->getUserDtlsArr($caseDtUid, $allUserArr);
            $by_photo = $csUsrDtlArr['User']['photo'];

            $csUsrDtlArr['User']['photo_exist'] = 0;
            if (trim($by_photo)) {
                $csUsrDtlArr['User']['photo_exist'] = 1; //$frmt->pub_file_exists(DIR_USER_PHOTOS_S3_FOLDER,$by_photo);
            }

            $sqlcasedata[$caseKey]['Easycase']['userArr'] = $csUsrDtlArr;
            $getdata['Easycase']['message'] = preg_replace('/<script.*>.*<\/script>/ims', '', $getdata['Easycase']['message']);
            if ($getdata['Easycase']['legend'] == 6) {
                $sqlcasedata[$caseKey]['Easycase']['wrap_msg'] = '';
            } else {
                if ($getdata['Easycase']['message']) {
                    $CSrepcount++;
                }
                $sqlcasedata[$caseKey]['Easycase']['wrap_msg'] = $frmt->html_wordwrap($frmt->formatCms($getdata['Easycase']['message']), 75);
            }
            $caseDtId = $getdata['Easycase']['id'];
            $rplyFilesArr = $this->getCaseFiles($caseDtId);
            foreach ($rplyFilesArr as $fkey => $getFiles) {
                $caseFileName = $getFiles['CaseFile']['file'];

                $rplyFilesArr[$fkey]['CaseFile']['is_exist'] = 0;
                if (trim($caseFileName)) {
                    $rplyFilesArr[$fkey]['CaseFile']['is_exist'] = 1; //$frmt->pub_file_exists(DIR_CASE_FILES_S3_FOLDER,$caseFileName);
                }

                if (stristr($getFiles['CaseFile']['downloadurl'], 'www.dropbox.com')) {
                    $rplyFilesArr[$fkey]['CaseFile']['format_file'] = 'db'; //'<img src="'.HTTP_IMAGES.'images/db16x16.png" alt="Dropbox" title="'.$caseFileName.'" width="16" height="16" border="0" style="border:0px solid #C3C3C3" />';
                } elseif (stristr($getFiles['CaseFile']['downloadurl'], 'docs.google.com')) {
                    $rplyFilesArr[$fkey]['CaseFile']['format_file'] = 'gd'; //'<img src="'.HTTP_IMAGES.'images/gd16x16.png" alt="Google" title="'.$caseFileName.'" width="16" height="16" border="0" style="border:0px solid #C3C3C3" />';
                } else {
                    $rplyFilesArr[$fkey]['CaseFile']['format_file'] = substr(strrchr(strtolower($caseFileName), "."), 1); //str_replace(array('"','\''), array('\'','"'), $frmt->imageType($caseFileName,25,10,1));
                }
                $rplyFilesArr[$fkey]['CaseFile']['is_ImgFileExt'] = $frmt->validateImgFileExt($caseFileName);

                if ($rplyFilesArr[$fkey]['CaseFile']['is_ImgFileExt']) {
                    if (USE_S3 == 0) {
                        $rplyFilesArr[$fkey]['CaseFile']['fileurl'] = HTTP_CASE_FILES . $caseFileName;
                    } else {
                        $rplyFilesArr[$fkey]['CaseFile']['fileurl'] = $frmt->generateTemporaryURL(DIR_CASE_FILES_S3 . $caseFileName);
                    }
                } else if ($chk) { // for mobile api
                    if (USE_S3 == 0) {
                        $rplyFilesArr[$fkey]['CaseFile']['fileurl'] = HTTP_CASE_FILES . $caseFileName;
                    } else {
                        $rplyFilesArr[$fkey]['CaseFile']['fileurl'] = $frmt->generateTemporaryURL(DIR_CASE_FILES_S3 . $caseFileName);
                    }
                }

                //$rplyFilesArr[$fkey]['CaseFile']['file_shname'] = $frmt->shortLength($caseFileName,37);
                $rplyFilesArr[$fkey]['CaseFile']['file_size'] = $frmt->getFileSize($getFiles['CaseFile']['file_size']);
            }
            $sqlcasedata[$caseKey]['Easycase']['rply_files'] = $rplyFilesArr;

            $caseReplyType = $getdata['Easycase']['reply_type'];
            $caseDtMsg = $getdata['Easycase']['message'];
            $caseDtLegend = $getdata['Easycase']['legend'];
            $caseAssignTo = $getdata['Easycase']['assign_to'];
            $taskhourspent = $getdata['Easycase']['hours'];
            $taskcompleted = $getdata['Easycase']['completed_task'];

            $replyCap = '';
            $asgnTo = '';
            $sts = '';
            $hourspent = '';
            $completed = '';
            if ($caseReplyType == 0 && $caseDtMsg != '') {
                if ($caseDtLegend == 1) {
                    $sts = '<b class="new">' . __('New') . '</b>';
                } elseif ($caseDtLegend == 2 || $caseDtLegend == 4) {
                    $sts = '<b class="wip">' . __('In Progress') . '</b>';
                } elseif ($caseDtLegend == 3) {
                    $sts = '<b class="closed">' . __('Closed') . '</b>';
                } elseif ($caseDtLegend == 5) {
                    $sts = '<b class="resolved">' . __('Resolved') . '</b>';
                }

                $userArr1 = $cq->getUserDtlsArr($caseAssignTo, $allUserArr);

                $by_id1 = $userArr1['User']['id'];
                $by_email1 = $userArr1['User']['email'];
                $by_name_assign1 = $userArr1['User']['name'];
                $by_photo1 = $userArr1['User']['photo'];
                $short_name_assign1 = $userArr1['User']['short_name'];
                //$replyCap .= ',&nbsp;&nbsp;Assigned To: <font color="black">'.$by_name_assign1.'('.$short_name_assign1.')</font>';
                $asgnTo = $by_name_assign1; //.' ('.$short_name_assign1.')';

                if ($taskhourspent != "0.0") {
                    $hourspent = $taskhourspent;
                }

                if ($taskcompleted != "0") {
                    $completed = $taskcompleted;
                }

                //$replyCap .= '<br />';
            }

            if ($caseReplyType == 0 && ($caseDtMsg == '' || $caseDtLegend == 6)) {
                if ($caseDtLegend == 3) {
                    $replyCap = '<b class="closed">' . __('Closed', true) . '</b> ' . __('the Task', true);
                } elseif ($caseDtLegend == 4) {
                    $replyCap = '<b class="wip">' . __('Started', true) . '</b> ' . __('the Task', true);
                } elseif ($caseDtLegend == 5) {
                    $replyCap = '<b class="resolved">' . __('Resolved', true) . '</b> ' . __('the Task', true);
                } elseif ($caseDtLegend == 6) {
                    $replyCap = '<b class="resolved">' . __('Modified', true) . '</b> ' . __('the Task', true);
                }
            } else {
                if ($caseReplyType == 1) {
                    $caseDtTyp = $getdata['Easycase']['type_id'];
                    $prjtype_name = $cq->getTypeArr($caseDtTyp, $GLOBALS['TYPE']);
                    $name = $prjtype_name['Type']['name'];
                    $sname = $prjtype_name['Type']['short_name'];
                    $image = $caseDtTyp > 12 ? '' : $frmt->todo_typ($sname, $name);
                    $replyCap = __('Task Type changed to', true) . ' ' . $image . ' <b>' . $name . '</b>';
                } elseif ($caseReplyType == 2) {
                    $userArr = $cq->getUserDtlsArr($caseAssignTo, $allUserArr);
                    $by_id = $userArr['User']['id'];
                    $by_email = $userArr['User']['email'];
                    $by_name_assign = $userArr['User']['name'];
                    $by_last_name_assign = $userArr['User']['last_name'];
                    $by_photo = $userArr['User']['photo'];
                    //$short_name_assign = $userArr['User']['short_name'];

                    $replyCap = __('Task re-assigned to', true) . ' <b class="ttc">' . $by_name_assign . ' ' . $by_last_name_assign . '</b>';
                    if ($caseAssignTo == 0) {
                        $replyCap = __('Task has been ', true) . ' <b class="ttc"> Unassigned </b>';
                    }
                } elseif ($caseReplyType == 3) {
                    $caseDtDue = $getdata['Easycase']['due_date'];
                    $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
                    if ($caseDtDue != "NULL" && $caseDtDue != "0000-00-00" && $caseDtDue != "" && $caseDtDue != "1970-01-01") {
                        $due_date = $dt->dateFormatOutputdateTime_day($caseDtDue, $curCreated, 'week');
                        $replyCap = __('Due Date changed to', true) . ' <b>' . $due_date . '</b>';
                    } else {
                        $replyCap = __('Due Date', true) . ': <i>' . __('No Due Date', true) . '</i>';
                    }
                } elseif ($caseReplyType == 4) {
                    $casePriority = $getdata['Easycase']['priority'];
                    if ($casePriority == 0) {
                        $replyCap = __('Priority changed to', true) . ' <b class="pr_high">' . __('High', true) . '</b><br/>';
                    } elseif ($casePriority == 1) {
                        $replyCap = __('Priority changed to', true) . ' <b class="pr_medium">' . __('Medium', true) . '</b><br/>';
                    } elseif ($casePriority == 2) {
                        $replyCap = __('Priority changed to', true) . ' <b class="pr_low">' . __('Low', true) . '</b><br/>';
                    }
                } elseif ($caseReplyType == 5) {
                    $caselegend = $getdata['Easycase']['legend'];
                    $legend = ClassRegistry::init('Status');
                    $legendDetails = $legend->find('first', array('conditions' => array('Status.id' => $caselegend)));
                    $replyCap = __('Status changed to', true) . ' <b style="color:' . $legendDetails['Status']['color'] . '">' . $legendDetails['Status']['name'] . '</b><br/>';
                } elseif ($caseReplyType == 6) {
                    $caseEstHour = $frmt->format_time_hr_min($getdata['Easycase']['estimated_hours']);
                    $replyCap = __('Estimated Hour(s) changed to', true) . ' <b>' . $caseEstHour . '</b>';
                } else if ($caseReplyType == 10) {
                    $replyCap = __('Added time log');
                } else if ($caseReplyType == 11) {
                    $replyCap = __('Updated time log');
                } else if ($caseReplyType == 15) {
                    $caseDtStart = $getdata['Easycase']['gantt_start_date'];
                    $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
                    if ($caseDtStart != "NULL" && $caseDtStart != "0000-00-00" && $caseDtStart != "" && $caseDtStart != "1970-01-01") {
                        $start_date = $dt->dateFormatOutputdateTime_day($caseDtStart, $curCreated, 'week');
                        $replyCap = __('Start Date changed to', true) . ' <b>' . $start_date . '</b>';
                    } else {
                        $replyCap = __('Start Date', true) . ': <i>' . __('No Start Date', true) . '</i>';
                    }
                }
            }
            $sqlcasedata[$caseKey]['Easycase']['sts'] = $sts;
            $sqlcasedata[$caseKey]['Easycase']['asgnTo'] = $asgnTo;
            $sqlcasedata[$caseKey]['Easycase']['hourspent'] = $hourspent;
            $sqlcasedata[$caseKey]['Easycase']['completed'] = $completed;
            $sqlcasedata[$caseKey]['Easycase']['replyCap'] = $replyCap;
            $caseDtActdT = $getdata['Easycase']['dt_created'];
            //$updated_by = $getdata['Easycase']['updated_by'];
            $replyDt = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $caseDtActdT, "datetime");
            $curDate = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "date");

            if ($caseDtUid == SES_ID && 0) {
                $usrName = "me";
            } else {
                $usrName = $csUsrDtlArr['User']['name'];
            }
            $sqlcasedata[$caseKey]['Easycase']['usrName'] = $usrName;
            $sqlcasedata[$caseKey]['Easycase']['rply_dt'] = $dt->dateFormatOutputdateTime_day($replyDt, $curDate);

            unset(
                    //$sqlcasedata[$caseKey]['Easycase']['uniq_id'],
                    $sqlcasedata[$caseKey]['Easycase']['case_no'], $sqlcasedata[$caseKey]['Easycase']['case_count'], $sqlcasedata[$caseKey]['Easycase']['updated_by'], $sqlcasedata[$caseKey]['Easycase']['type_id'], $sqlcasedata[$caseKey]['Easycase']['priority'], $sqlcasedata[$caseKey]['Easycase']['title'], $sqlcasedata[$caseKey]['Easycase']['reply_type'], $sqlcasedata[$caseKey]['Easycase']['assign_to'], $sqlcasedata[$caseKey]['Easycase']['completed_task'], $sqlcasedata[$caseKey]['Easycase']['hours'], $sqlcasedata[$caseKey]['Easycase']['due_date'], $sqlcasedata[$caseKey]['Easycase']['istype'], $sqlcasedata[$caseKey]['Easycase']['status'], $sqlcasedata[$caseKey]['Easycase']['isactive'], $sqlcasedata[$caseKey]['Easycase']['dt_created'], $sqlcasedata[$caseKey]['Easycase']['actual_dt_created'], $sqlcasedata[$caseKey]['Easycase']['caseReplyType'], $sqlcasedata[$caseKey]['Easycase']['userArr']['User']['id'], $sqlcasedata[$caseKey]['Easycase']['userArr']['User']['email'], $sqlcasedata[$caseKey]['Easycase']['userArr']['User']['istype']
            );
        }
        $arr['CSrepcount'] = $CSrepcount;
        $arr['sqlcasedata'] = $sqlcasedata;
        //$sqlcasedata['CSrepcount']=$CSrepcount;
        //return $sqlcasedata;
        return $arr;
    }

    //From CasequeryHelper.php
    function getMilestoneName($caseid) {
        $Milestone = ClassRegistry::init('Milestone');
        $Milestone->recursive = -1;

        $milestones = $Milestone->query("SELECT Milestone.title as title FROM milestones as Milestone,easycase_milestones AS EasycaseMilestone WHERE EasycaseMilestone.milestone_id=Milestone.id AND EasycaseMilestone.easycase_id='" . $caseid . "'");
        if (isset($milestones['0']['Milestone']['title']) && $milestones['0']['Milestone']['title']) {
            return $milestones['0']['Milestone']['title'];
        } else {
            return false;
        }
    }

    function getCaseFiles($cid) {
        App::import('Model', 'CaseFile');
        $CaseFile = new CaseFile();
        $CaseFile->recursive = -1;
        $caseFiles = $CaseFile->find('all', array('conditions' => array('CaseFile.easycase_id' => $cid, 'CaseFile.comment_id' => 0, 'CaseFile.isactive' => 1), 'fields' => array('CaseFile.id', 'CaseFile.file', 'CaseFile.file_size', 'CaseFile.downloadurl'), 'order' => array('CaseFile.file ASC')));
        return $caseFiles;
    }

    function getAllCaseFiles($pid, $cno) {
        if (!$pid || !$cno)
            return false;

        App::import('Model', 'CaseFile');
        $CaseFile = new CaseFile();
        $CaseFile->bindModel(array(
            'belongsTo' => array(
                'Easycase' => array(
                    'className' => 'Easycase',
                    'foreignKey' => 'easycase_id'
                )
            )
                ), false);
        $filesArr = $CaseFile->find('all', array('conditions' => array('Easycase.project_id' => $pid, 'Easycase.case_no' => $cno, 'CaseFile.isactive' => 1), 'fields' => array('CaseFile.id', 'CaseFile.file', 'CaseFile.file_size', 'CaseFile.downloadurl', 'Easycase.actual_dt_created'), 'order' => array('Easycase.actual_dt_created DESC', 'CaseFile.file ASC')));
        return $filesArr;
    }

    function formatFiles($filesArr, $frmt, $tz, $dt, $chk = null) {
        if ($filesArr) {
            $curDateTz = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");

            foreach ($filesArr as $fkey => $getFiles) {
                $caseFileName = $getFiles['CaseFile']['file'];

                $filesArr[$fkey]['CaseFile']['is_exist'] = 0;
                if (trim($caseFileName)) {
                    $filesArr[$fkey]['CaseFile']['is_exist'] = 1;
                }

                $downloadurl = $getFiles['CaseFile']['downloadurl'];
                if (isset($downloadurl) && trim($downloadurl)) {
                    if (stristr($downloadurl, 'www.dropbox.com')) {
                        $filesArr[$fkey]['CaseFile']['format_file'] = 'db'; //'<img src="'.HTTP_IMAGES.'images/db16x16.png" alt="Dropbox" title="'.$caseFileName.'" width="16" height="16" border="0" style="border:0px solid #C3C3C3" />';
                    } else {
                        $filesArr[$fkey]['CaseFile']['format_file'] = 'gd'; //'<img src="'.HTTP_IMAGES.'images/gd16x16.png" alt="Google" title="'.$caseFileName.'" width="16" height="16" border="0" style="border:0px solid #C3C3C3" />';
                    }
                } else {
                    $filesArr[$fkey]['CaseFile']['format_file'] = substr(strrchr(strtolower($caseFileName), "."), 1); //str_replace(array('"','\''), array('\'','"'), $frmt->imageType($caseFileName,25,10,1));
                    $filesArr[$fkey]['CaseFile']['is_ImgFileExt'] = $frmt->validateImgFileExt($caseFileName);
                    if ($filesArr[$fkey]['CaseFile']['is_ImgFileExt']) {
                        if (USE_S3 == 0) {
                            $filesArr[$fkey]['CaseFile']['fileurl'] = HTTP_CASE_FILES . $caseFileName;
                        } else {
                            $filesArr[$fkey]['CaseFile']['fileurl'] = $frmt->generateTemporaryURL(DIR_CASE_FILES_S3 . $caseFileName);
                        }
                    } else if ($chk) { // for mobile api
                        if (USE_S3 == 0) {
                            $filesArr[$fkey]['CaseFile']['fileurl'] = HTTP_CASE_FILES . $caseFileName;
                        } else {
                            $filesArr[$fkey]['CaseFile']['fileurl'] = $frmt->generateTemporaryURL(DIR_CASE_FILES_S3 . $caseFileName);
                        }
                    }
                    $filesArr[$fkey]['CaseFile']['file_size'] = $frmt->getFileSize($getFiles['CaseFile']['file_size']);
                }

                $caseDtActdT = $getFiles['Easycase']['actual_dt_created'];
                $replyDt = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, $caseDtActdT, "datetime");
                $filesArr[$fkey]['CaseFile']['file_date'] = $dt->dateFormatOutputdateTime_day($replyDt, $curDateTz);
            }
        }
        return $filesArr;
    }

    function getUserEmail($id) {
        $CaseUserEmail = ClassRegistry::init('CaseUserEmail');
        $CaseUserEmail->recursive = -1;
        $userIds = $CaseUserEmail->find('all', array('conditions' => array('CaseUserEmail.easycase_id' => $id, 'CaseUserEmail.ismail' => 1), 'fields' => array('CaseUserEmail.user_id')));
        return $userIds;
    }

    //End CasequeryHelper.php
    //From FormatComponent.php
    function getMemebers($projId, $type = NULL, $comp_id = null) {
        $ProjectUser = ClassRegistry::init('ProjectUser');
        $company_id = ($comp_id) ? $comp_id : SES_COMP;
        if (defined('CR') && CR == 1) {
            $user = ClassRegistry::init('User');
            if ($projId == 'all') {
                $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id,CompanyUser.is_client, User.name, User.last_name, User.email, User.istype,User.short_name,User.photo, UserNotification.new_case FROM users as User,project_users as ProjectUser,company_users as CompanyUser, user_notifications as UserNotification WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $company_id . "'  AND User.isactive='1' AND ProjectUser.user_id=User.id AND UserNotification.user_id=User.id ORDER BY User.name");
            } else {
                $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id,CompanyUser.is_client, User.name, User.last_name, User.email, User.istype,User.short_name,User.photo, UserNotification.new_case  FROM users as User,project_users as ProjectUser,company_users as CompanyUser,projects as Project, user_notifications as UserNotification WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $company_id . "' AND Project.uniq_id='" . $projId . "' AND Project.id=ProjectUser.project_id AND User.isactive='1' AND ProjectUser.user_id=User.id AND UserNotification.user_id=User.id  ORDER BY User.name");
            }
            $t_arr = array();
            if ($quickMem) {
                foreach ($quickMem as $k => $v) {
                    if ($v['User']['photo'] == '') {
                        $quickMem[$k]['User']['asgnbgcolor'] = $user->getProfileBgColr($v['User']['id']);
                    }
                    $quickMem[$k]['User']['is_client'] = $v['CompanyUser']['is_client'];
                    if (!in_array($quickMem[$k]['User']['id'], $t_arr)) {
                        array_push($t_arr, $quickMem[$k]['User']['id']);
                    } else {
                        unset($quickMem[$k]);
                    }
                }
            }
        } else {
            if ($projId == 'all') {
                $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id, User.name, User.email, User.istype,User.short_name FROM users as User,project_users as ProjectUser,company_users as CompanyUser WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $company_id . "'  AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.name");
            } else {
                $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id, User.name, User.email, User.istype,User.short_name FROM users as User,project_users as ProjectUser,company_users as CompanyUser,projects as Project WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $company_id . "' AND Project.uniq_id='" . $projId . "' AND Project.id=ProjectUser.project_id AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.name");
            }
        }

        return $quickMem;
    }

    function getMemebersid($projId, $cmpny_id = null) {

        $cmp_id = ($cmpny_id) ? $cmpny_id : SES_COMP;
        $ProjectUser = ClassRegistry::init('ProjectUser');

        //$quickMem = $ProjectUser->find('all', array('conditions' => array('Project.id' => $projId,'User.isactive' => 1,'Project.company_id' => SES_COMP),'fields' => array('DISTINCT User.id','User.name','User.istype','User.email','User.short_name'),'order' => array('User.name')));
        if (defined('CR') && CR == 1) {
            $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id, User.name, User.last_name, User.email, User.istype,User.short_name,CompanyUser.is_client, User.photo FROM users as User,project_users as ProjectUser,company_users as CompanyUser WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $cmp_id . "' AND ProjectUser.project_id='" . $projId . "' AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.name");
        } else {
            $quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id, User.name, User.last_name, User.email, User.istype,User.short_name, User.photo FROM users as User,project_users as ProjectUser,company_users as CompanyUser WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . $cmp_id . "' AND ProjectUser.project_id='" . $projId . "' AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.name");
        }
        #$quickMem = $ProjectUser->query("SELECT DISTINCT User.id,User.uniq_id, User.name, User.last_name, User.email, User.istype,User.short_name, User.photo FROM users as User,project_users as ProjectUser,company_users as CompanyUser WHERE CompanyUser.user_id=ProjectUser.user_id AND CompanyUser.is_active='1' AND CompanyUser.company_id='" . SES_COMP . "' AND ProjectUser.project_id='" . $projId . "' AND User.isactive='1' AND ProjectUser.user_id=User.id ORDER BY User.name");

        return $quickMem;
    }

    //End FormatComponent.php
    function getCaseNo($case_uniq_id) {
        return $this->find('first', array('conditions' => array('Easycase.uniq_id' => $case_uniq_id), 'fields' => array('Easycase.case_no')));
    }

    function getCaseTitle($project_id, $case_no) {
        $caseTitle = '';
        $csTtl = $this->find('first', array('conditions' => array('Easycase.project_id' => $project_id, 'Easycase.case_no' => $case_no, 'istype' => 1), 'fields' => array('Easycase.title')));
        if ($csTtl) {
            $caseTitle = $csTtl['Easycase']['title'];
        }
        return $caseTitle;
    }

    function getLastResolved($projId, $caseNo) {
        return $this->find('first', array(
                    'conditions' => array('Easycase.project_id' => $projId, 'Easycase.case_no' => $caseNo, 'Easycase.legend' => '5'),
                    'fields' => array('Easycase.dt_created'),
                    'order' => 'Easycase.dt_created DESC'
                        )
        );
    }

    function getLastClosed($projId, $caseNo) {
        return $this->find('first', array(
                    'conditions' => array('Easycase.project_id' => $projId, 'Easycase.case_no' => $caseNo, 'Easycase.legend' => '3'),
                    'fields' => array('Easycase.dt_created'),
                    'order' => 'Easycase.dt_created DESC'
                        )
        );
    }

    function getEasycase($case_uniq_id) {
        $thisCase = $this->find('first', array('conditions' => array('Easycase.uniq_id' => $case_uniq_id), 'fields' => array('Easycase.id', 'Easycase.case_no', 'Easycase.project_id', 'Easycase.isactive', 'Easycase.istype', 'Easycase.completed_task')));

        if ($thisCase['Easycase']['istype'] != 1) {
            $thisCase = $this->find('first', array('conditions' => array('Easycase.case_no' => $thisCase['Easycase']['case_no'], 'Easycase.project_id' => $thisCase['Easycase']['project_id'], 'istype' => 1), 'fields' => array('Easycase.id', 'Easycase.case_no', 'Easycase.project_id', 'Easycase.isactive', 'Easycase.completed_task')));
        }

        return $thisCase;
    }

    function getTaskUser($projId, $caseNo) {
        if (!$projId || !$caseNo)
            return false;

        return $this->query("SELECT DISTINCT User.id, User.name, User.last_name, User.email, User.istype,User.short_name,User.photo FROM users as User,easycases as Easycase WHERE (Easycase.user_id=User.id || Easycase.updated_by=User.id || Easycase.assign_to=User.id) AND Easycase.project_id='" . $projId . "' AND Easycase.case_no='" . $caseNo . "' AND Easycase.istype IN('1','2') ORDER BY User.short_name");
    }

    /**
     * @method public actionOntask($easycase_id, $caseuid,$type)

     * @return JSON
     */
    function actionOntask($caseid, $caseuid, $type) {
        if ($caseid) {
            $checkStatus = $this->find('first', array('conditions' => array('Easycase.id' => $caseid, 'Easycase.uniq_id' => $caseuid, 'Easycase.isactive' => 1)));
            if ($checkStatus) {
                if ($checkStatus['Easycase']['legend'] == 1) {
                    $status = '<font color="#737373" style="font-weight:bold">' . __('Status', true) . ':</font> <font color="#763532" style="font:normal 12px verdana;">' . __('NEW', true) . '</font>';
                } elseif ($checkStatus['Easycase']['legend'] == 4) {
                    $status = '<font color="#737373" style="font-weight:bold">' . __('Status', true) . ':</font> <font color="#55A0C7" style="font:normal 12px verdana;">' . __('STARTED', true) . '</font>';
                } elseif ($checkStatus['Easycase']['legend'] == 5) {
                    $status = '<font color="#737373" style="font-weight:bold">' . __('Status', true) . ':</font> <font color="#EF6807" style="font:normal 12px verdana;">' . __('RESOLVED', true) . '</font>';
                } elseif ($checkStatus['Easycase']['legend'] == 3) {
                    $status = '<font color="#737373" style="font-weight:bold">' . __('Status', true) . ':</font> <font color="green" style="font:normal 12px verdana;">' . __('CLOSED', true) . '</font>';
                }
                //Action wrt type
                if ($type == 'start') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Start";
                    $msg = '<font color="#737373" style="font-weight:bold">' . __('Status', true) . ':</font> <font color="#55A0C7" style="font:normal 12px verdana;">' . __('STARTED', true) . '</font>';
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('STARTED', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'resolve') {
                    $csSts = 1;
                    $csLeg = 5;
                    $acType = 3;
                    $cuvtype = 5;
                    $emailType = "Resolve";
                    $msg = '<font color="#737373" style="font-weight:bold">Status:</font> <font color="#EF6807" style="font:normal 12px verdana;">' . __('RESOLVED', true) . '</font>';
                    $emailbody = '<font color="#EF6807" style="font:normal 12px verdana;">' . __('RESOLVED', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'close') {
                    $csSts = 2;
                    $csLeg = 3;
                    $acType = 1;
                    $cuvtype = 3;
                    $emailType = "Close";
                    $msg = '<font color="#737373" style="font-weight:bold">Status:</font> <font color="green" style="font:normal 12px verdana;">CLOSED</font>';
                    $emailbody = '<font color="green" style="font:normal 12px verdana;">' . __('CLOSED', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'tasktype') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Type";
                    $caseChageType1 = 1;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed the type of', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'duedate') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Duedate";
                    $caseChageDuedate1 = 3;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed the due date of', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'priority') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Priority";
                    $caseChagePriority1 = 2;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed the priority of', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'assignto') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Assignto";
                    $caseChangeAssignto1 = 4;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed the assigned to of', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'esthour') {
                    $csSts = 1;
                    $csLeg = 1;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Estimated Hour(s)";
                    $caseChangeEstHour = 5;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed estimated hour(s) of', true) . '</font> ' . __('the Task', true) . '.';
                } elseif ($type == 'cmpltsk') {
                    $csSts = 1;
                    $csLeg = 4;
                    $acType = 2;
                    $cuvtype = 4;
                    $emailType = "Change Task Progress";
                    $caseChangeCmplTask = 6;
                    $msg = $status;
                    $emailbody = '<font color="#55A0C7" style="font:normal 12px verdana;">' . __('changed progress of', true) . '</font> ' . __('the Task', true) . '.';
                }
                $commonAllId = "";
                $caseid_list = $caseid . ',';
                $done = 1;
                if ($caseChageType1 || $caseChageDuedate1 || $caseChagePriority1 || $caseChangeAssignto1 || $caseChangeEstHour || $caseChangeCmplTask) {
                    //socket.io implement start
                    $Project = ClassRegistry::init('Project');
                    $ProjectUser = ClassRegistry::init('ProjectUser');
                    $ProjectUser->recursive = -1;

                    //$getUser = $ProjectUser->query("SELECT user_id FROM project_users WHERE project_id='".$closeStsPid."'");
                    $actionStsPid = $checkStatus['Easycase']['project_id'];
                    $caseStsNo = $checkStatus['Easycase']['case_no'];
                    $closeStsTitle = $checkStatus['Easycase']['title'];

                    $prjuniq = $Project->query("SELECT uniq_id, short_name FROM projects WHERE id='" . $actionStsPid . "'");
                    $prjuniqid = $prjuniq[0]['projects']['uniq_id'];
                    $projShName = strtoupper($prjuniq[0]['projects']['short_name']);
                    $channel_name = $prjuniqid;

                    if (channel_name) {
                        $msgpub = 'Updated.~~' . SES_ID . '~~' . $caseStsNo . '~~' . 'UPD' . '~~' . $closeStsTitle . '~~' . $projShName;
                        $pub_msg = array('channel' => $channel_name, 'message' => $msgpub);
                    }
                    //socket.io implement end
                } else {
                    $done = 1;
                    $caseDataArr = $checkStatus;
                    if (($caseDataArr['Easycases']['legend'] == 3) || ($csLeg == 4 && ($caseDataArr['Easycases']['legend'] == 4)) || ($csLeg == 5 && ($caseDataArr['Easycases']['legend'] == 5))) {
                        $done = 0;
                    }
                    if ($done) {
                        $caseid_list = $caseid . ',';
                        $caseStsId = $caseDataArr['Easycase']['id'];
                        $caseStsNo = $caseDataArr['Easycase']['case_no'];
                        $closeStsPid = $caseDataArr['Easycase']['project_id'];
                        $closeStsTyp = $caseDataArr['Easycase']['type_id'];
                        $closeStsPri = $caseDataArr['Easycase']['priority'];
                        $closeStsTitle = $caseDataArr['Easycase']['title'];
                        $closeStsUniqId = $caseDataArr['Easycase']['uniq_id'];
                        $caUid = $caseDataArr['Easycase']['assign_to'];

                        $this->query("UPDATE easycases SET case_no='" . $caseStsNo . "',updated_by='" . SES_ID . "',case_count=case_count+1, project_id='" . $closeStsPid . "', type_id='" . $closeStsTyp . "', priority='" . $closeStsPri . "', status='" . $csSts . "', legend='" . $csLeg . "', dt_created='" . GMT_DATETIME . "' WHERE id=" . $caseStsId . " AND isactive='1'");
                        $caseuniqid = md5(uniqid() . mt_rand());
                        $this->query("INSERT INTO easycases SET uniq_id='" . $caseuniqid . "', user_id='" . SES_ID . "', format='2', istype='2', actual_dt_created='" . GMT_DATETIME . "', case_no='" . $caseStsNo . "', project_id='" . $closeStsPid . "', type_id='" . $closeStsTyp . "', priority='" . $closeStsPri . "', status='" . $csSts . "', legend='" . $csLeg . "', dt_created='" . GMT_DATETIME . "'");

                        //socket.io implement start
                        $Project = ClassRegistry::init('Project');
                        $ProjectUser = ClassRegistry::init('ProjectUser');
                        $ProjectUser->recursive = -1;

                        //$getUser = $ProjectUser->query("SELECT user_id FROM project_users WHERE project_id='".$closeStsPid."'");
                        $prjuniq = $Project->query("SELECT uniq_id, short_name FROM projects WHERE id='" . $closeStsPid . "'");
                        $prjuniqid = $prjuniq[0]['projects']['uniq_id']; //print_r($prjuniq);
                        $projShName = strtoupper($prjuniq[0]['projects']['short_name']);
                        $channel_name = $prjuniqid;
                        $msgpub = 'Updated.~~' . SES_ID . '~~' . $caseStsNo . '~~' . 'UPD' . '~~' . $closeStsTitle . '~~' . $projShName;

                        $pub_msg = array('channel' => $channel_name, 'message' => $msgpub);
                        //socket.io implement end
                    }
                }
                $_SESSION['email']['email_body'] = $emailbody;
                $_SESSION['email']['msg'] = $msg;
                $email_notification = array('caseNo' => $caseStsNo, 'closeStsTitle' => $closeStsTitle, 'emailMsg' => $emailMsg, 'closeStsPid' => $closeStsPid, 'closeStsPri' => $closeStsPri, 'closeStsTyp' => $closeStsTyp, 'assignTo' => $assignTo, 'usr_names' => $usr_names, 'caseuniqid' => $caseuniqid, 'csType' => $emailType, 'closeStsPid' => $closeStsPid, 'caseStsId' => $caseStsId, 'caseIstype' => 5, 'caseid_list' => $caseid_list, 'caseUniqId' => $closeStsUniqId); // $caseuniqid
                $arr['succ'] = 1;
                $arr['msg'] = 'Succes';
                $arr['data'] = json_encode($email_notification);
                $arr['pub_msg'] = $pub_msg;
                return $arr;
            } else {
                $arr['err'] = 1;
                $arr['msg'] = __('No Task found with the selected id', true);
                return $arr;
            }
        }
    }

    /**
     * @method ajax_milestonelist to retrive the latest 3 Milestone and respective tasks

     * @return array()
     */
    function ajax_milestonelist($data = array(), $frmt, $dt, $tz, $cq, $milestone_search = '') {
        $milestone_search = "AND (Milestone.title LIKE '%$milestone_search%' OR Milestone.description LIKE '%$milestone_search%')";
        $caseStatus = $data['caseStatus']; // Filter by Status(legend)
        $priorityFil = $data['priFil']; // Filter by Priority
        $caseTypes = $data['caseTypes']; // Filter by case Types
        $caseUserId = $data['caseMember']; // Filter by Member
        $caseAssignTo = $data['caseAssignTo']; // Filter by AssignTo
        $caseDate = $data['caseDate']; // Sort by Date
        $caseSrch = $data['caseSearch']; // Search by keyword
        $casePage = $data['casePage']; // Pagination
        $caseUniqId = $data['caseId']; // Case Uniq ID to close a case
        $caseTitle = $data['caseTitle']; // Case Uniq ID to close a case
        $caseDueDate = $data['caseDueDate']; // Sort by Due Date
        $isActive = isset($data['isActive']) ? $data['isActive'] : 1; //to distinguish between active and completed
        $caseNum = $data['caseNum']; // Sort by Due Date
        $caseLegendsort = $data['caseLegendsort']; // Sort by Case Status
        $caseAtsort = $data['caseAtsort']; // Sort by Case Status
        $startCaseId = $data['startCaseId']; // Start Case
        $caseResolve = $data['caseResolve']; // Resolve Case

        $caseMenuFilters = $data['caseMenuFilters']; // Resolve Case
        $milestoneIds = $data['milestoneIds']; // Resolve Case
        $caseCreateDate = $data['caseCreateDate']; // Sort by Created Date
        @$case_srch = $data['case_srch'];
        @$case_date = $data['case_date'];
        @$case_duedate = $data['case_due_date'];
        @$milestone_type = $data['mstype'];
        $changecasetype = $data['caseChangeType'];
        $caseChangeDuedate = $data['caseChangeDuedate'];
        $caseChangePriority = $data['caseChangePriority'];
        $caseChangeAssignto = $data['caseChangeAssignto'];
        $customfilterid = $data['customfilter'];
        $detailscount = $data['data']['detailscount'];
        $msQuery = "";
        $ispaginate = $data['ispaginate'];
        $mlimit = isset($data['mlimit']) ? $data['mlimit'] : 0;
        if ($ispaginate && $ispaginate == 'prev') {
            $mlimit -= (2 * MILESTONE_PER_PAGE);
        } elseif ($ispaginate == '' && $mlimit) {
            $mlimit -= MILESTONE_PER_PAGE;
        }
        $projUniq = $data['projFil'];
        $projIsChange = $data['projIsChange'];
        $statuscls = ClassRegistry::init('Status');
        $projects = ClassRegistry::init('Project');


        $clt_sql = 1;
        if (defined('CR') && CR == 1 && CakeSession::read("Auth.User.is_client") == 1) {
            $clt_sql = "((Easycase.client_status = " . CakeSession::read("Auth.User.is_client") . " AND Easycase.user_id = " . CakeSession::read("Auth.User.id") . ") OR Easycase.client_status != " . CakeSession::read("Auth.User.is_client") . ")";
        }


        if ($projUniq != 'all') {
            //$prj_cls = ClassRegistry::init('Project');
            $prj_usercls = ClassRegistry::init('ProjectUser');
            $prj_usercls->unbindModel(array('belongsTo' => array('User')));
            $projArr = $prj_usercls->find('first', array('conditions' => array('Project.uniq_id' => $projUniq, 'ProjectUser.user_id' => SES_ID, 'Project.isactive' => 1, 'ProjectUser.company_id' => SES_COMP), 'fields' => array('Project.id', 'Project.short_name', 'ProjectUser.id', 'Project.workflow_id')));
            //$projectDetails = $prj_cls->find('first',array('conditions'=>array('Project.uniq_id'=>$projUniq)));
            if ($projArr) {
                //Updating ProjectUser table to current date-time
                if ($projIsChange != $projUniq) {
                    $ProjectUser['id'] = $projArr['ProjectUser']['id'];
                    $ProjectUser['dt_visited'] = GMT_DATETIME;
                    $prj_usercls->save($ProjectUser);
                }
                if (isset($projArr['Project']['workflow_id']) && !empty($projArr['Project']['workflow_id'])) {
                    $workflow_id = $projArr['Project']['workflow_id'];
                    $status_list = $statuscls->find('all', array('conditions' => array('Status.workflow_id' => $projArr['Project']['workflow_id']), 'order' => 'seq_order DESC', 'limit' => 1));
                    $lgnd_val = $status_list[0]['Status']['id'];
                    $status_name = $status_list[0]['Status']['name'];
                }
            }
            $curProjId = $projArr['Project']['id'];
        } else if ($projUniq == 'all') {
            if (empty($statuses_list)) {
                $statuses = array();
            } else {
                foreach ($statuses_list as $k => $v) {
                    $status_list[$v['p']['prjct_id']] = $v['s']['status_id'];
                    $statuses[] = $v['s']['status_id'];
                }
            }
        } else {
            $projUniq = $GLOBALS['getallproj'][0]['Project']['uniq_id'];
            $curProjId = $GLOBALS['getallproj'][0]['Project']['id'];
        }
        // 3 Milestone wrt Sequence
        $milestone_cls = ClassRegistry::init('Milestone');
        if ($projUniq != 'all' && trim($projUniq)) {
            $milestones = $milestone_cls->query("SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,Milestone.assign_id,User.id,User.name,`Milestone`.`title`,`Milestone`.`project_id`,`Milestone`.`end_date`,`Milestone`.`uniq_id`,`Milestone`.`isactive`,`Milestone`.`user_id`,COUNT(c.easycase_id) AS totalcases,GROUP_CONCAT(c.easycase_id) AS `caseids`  FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN users AS User ON User.id=Milestone.assign_id WHERE `Milestone`.`isactive` =" . $isActive . " AND `Milestone`.`project_id` =" . $curProjId . " AND `Milestone`.`company_id` = " . SES_COMP . " $milestone_search GROUP BY Milestone.id ORDER BY Milestone.title ASC LIMIT " . $mlimit . ',' . MILESTONE_PER_PAGE);
            if (!$milestones) {
                $milestones_all = $milestone_cls->query("SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,Milestone.assign_id,User.id,User.name,`Milestone`.`isactive` FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN users AS User ON User.id=Milestone.assign_id WHERE `Milestone`.`project_id` =" . $curProjId . " AND `Milestone`.`company_id` = " . SES_COMP . " GROUP BY Milestone.id ORDER BY Milestone.title ASC");
            }

//            $milestones = $milestone_cls->find('all',array('conditions'=>array('isactive' =>$isActive,'project_id' =>$curProjId,'company_id' => SES_COMP)));
        } elseif ($projUniq == 'all') {

            $milestones = $milestone_cls->query("SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,Milestone.assign_id,User.id,User.name,`Milestone`.`title`,`Milestone`.`project_id`,`Milestone`.`end_date`,`Milestone`.`uniq_id`,`Milestone`.`isactive`,`Milestone`.`user_id`,COUNT(c.easycase_id) AS totalcases,GROUP_CONCAT(c.easycase_id) AS `caseids`  FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN users AS User ON User.id=Milestone.assign_id LEFT JOIN projects Project on Project.id=Milestone.project_id WHERE `Milestone`.`isactive` =" . $isActive . " AND Milestone.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser LEFT JOIN projects AS Project ON ProjectUser.project_id=Project.id WHERE ProjectUser.user_id=" . SES_ID . " AND Project.isactive='1' AND ProjectUser.company_id='" . SES_COMP . "') AND Milestone.isactive=$isActive AND `Milestone`.`company_id` = " . SES_COMP . "  $milestone_search GROUP BY Milestone.id ORDER BY Milestone.title ASC LIMIT " . $mlimit . ',' . MILESTONE_PER_PAGE);

            if (!$milestones) {
                $milestones_all = $milestone_cls->query("SELECT SQL_CALC_FOUND_ROWS `Milestone`.`id`,Milestone.assign_id,User.id,User.name,`Milestone`.`isactive` FROM milestones AS `Milestone` LEFT JOIN easycase_milestones AS c ON Milestone.id = c.milestone_id LEFT JOIN users AS User ON User.id=Milestone.assign_id WHERE `Milestone`.`company_id` = " . SES_COMP . " GROUP BY Milestone.id ORDER BY Milestone.title ASC");
            }
        }
        $totmlst = $milestone_cls->query("SELECT FOUND_ROWS() as mtotal");
        $resCaseProj['totalMlstCnt'] = $totmlst[0][0]['mtotal'];
        $resCaseProj['mlimit'] = $mlimit + MILESTONE_PER_PAGE;

        //$milestones = $milestone_cls->find('all',array('conditions'=>array('Milestone.project_id'=>$curProjId),'order'=>array('id_seq ASC, end_date DESC'),'limit'=>'3'));
        if ($milestones) {
            $milestone_ids = '';
            foreach ($milestones AS $keys => $values) {
                $milestone_ids .= "'" . $values['Milestone']['id'] . "', ";
            }
            $milestone_ids = trim($milestone_ids, ', ');
            $mstype = isset($data['msType']) ? $data['msType'] : 1;
            if ($projUniq) {
                if ($projUniq != 'all') {

                    if (defined('TLG') && TLG === 1) {
                        $caseAll = $this->query("SELECT SQL_CALC_FOUND_ROWS Easycase.*,(SELECT SUM(tc.total_hours) FROM log_times as tc WHERE tc.task_id = Easycase.id GROUP BY tc.task_id) as sphours,User.short_name,IF((Easycase.assign_to = 0 OR Easycase.assign_to =" . SES_ID . "),'Me',User.short_name) AS Assigned,EasycaseMilestone.id AS Emid, EasycaseMilestone.milestone_id AS Em_milestone_id,EasycaseMilestone.user_id AS Em_user_id,EasycaseMilestone.id_seq,Milestone.id as Mid,Milestone.title AS Mtitle , Milestone.assign_id AS Massign,Milestone.end_date,Milestone.isactive AS Misactive,Milestone.project_id AS Mproject_id,Milestone.uniq_id AS Muinq_id FROM easycases as Easycase LEFT JOIN easycase_milestones AS EasycaseMilestone ON EasycaseMilestone.easycase_id=Easycase.id LEFT JOIN milestones AS Milestone ON Milestone.id=EasycaseMilestone.milestone_id $milestone_search AND EasycaseMilestone.project_id=" . $curProjId . $msQuery . " AND Milestone.isactive=" . $isActive . " AND Milestone.id IN(" . $milestone_ids . ") LEFT JOIN users AS User ON Easycase.assign_to=User.id WHERE Easycase.istype='1' AND Easycase.isactive=1 AND ".$clt_sql." AND Easycase.project_id='$curProjId' AND Easycase.project_id!=0 ORDER BY Milestone.end_date ASC,Mtitle ASC");
                    } else {
                        $caseAll = $this->query("SELECT SQL_CALC_FOUND_ROWS Easycase.*,(SELECT SUM(ec.hours) FROM easycases as ec WHERE ec.case_no = Easycase.case_no AND ec.project_id='$curProjId' GROUP BY ec.case_no) as sphours,User.short_name,IF((Easycase.assign_to = 0 OR Easycase.assign_to =" . SES_ID . "),'Me',User.short_name) AS Assigned,EasycaseMilestone.id AS Emid, EasycaseMilestone.milestone_id AS Em_milestone_id,EasycaseMilestone.user_id AS Em_user_id,EasycaseMilestone.id_seq,Milestone.id as Mid,Milestone.title AS Mtitle , Milestone.assign_id AS Massign,Milestone.end_date,Milestone.isactive AS Misactive,Milestone.project_id AS Mproject_id,Milestone.uniq_id AS Muinq_id FROM easycases as Easycase LEFT JOIN easycase_milestones AS EasycaseMilestone ON EasycaseMilestone.easycase_id=Easycase.id LEFT JOIN milestones AS Milestone ON Milestone.id=EasycaseMilestone.milestone_id $milestone_search AND EasycaseMilestone.project_id=" . $curProjId . $msQuery . " AND Milestone.isactive=" . $isActive . " AND Milestone.id IN(" . $milestone_ids . ") LEFT JOIN users AS User ON Easycase.assign_to=User.id WHERE Easycase.istype='1' AND ".$clt_sql." AND Easycase.isactive=1 AND Easycase.project_id='$curProjId' AND Easycase.project_id!=0 ORDER BY Milestone.end_date ASC,Mtitle ASC");
                    }
                }
                if ($projUniq == 'all') {
                    //echo "SELECT SQL_CALC_FOUND_ROWS Easycase.*,User.short_name,IF((Easycase.assign_to = 0 OR Easycase.assign_to =".SES_ID."),'Me',User.short_name) AS Assigned FROM ( SELECT  Easycase.*,EasycaseMilestone.id AS Emid, EasycaseMilestone.milestone_id AS Em_milestone_id,EasycaseMilestone.user_id AS Em_user_id,EasycaseMilestone.id_seq,Milestone.id as Mid,Milestone.title AS Mtitle ,Milestone.end_date,Milestone.isactive AS Misactive,Milestone.project_id AS Mproject_id,Milestone.uniq_id AS Muinq_id FROM easycases as Easycase,easycase_milestones AS EasycaseMilestone,milestones AS Milestone WHERE EasycaseMilestone.easycase_id=Easycase.id AND Milestone.id=EasycaseMilestone.milestone_id AND Easycase.istype='1' AND Easycase.isactive=1 AND Milestone.isactive=".$mstype." AND Milestone.id IN(".$milestone_ids.") AND Easycase.project_id!=0 AND Easycase.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=".SES_ID." AND ProjectUser.project_id=Project.id AND Project.isactive='1' AND ProjectUser.company_id='".SES_COMP."') ".$searchcase." ".trim($qry)." AND EasycaseMilestone.easycase_id=Easycase.id AND EasycaseMilestone.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=".SES_ID." AND ProjectUser.project_id=Project.id AND Project.isactive='1')".$msQuery." ) AS Easycase LEFT JOIN users User ON Easycase.assign_to=User.id ORDER BY Easycase.end_date ASC,Easycase.Mtitle ASC";exit;
                    if (defined('TLG') && TLG === 1) {
                        $caseAll = $this->query("SELECT SQL_CALC_FOUND_ROWS Easycase.*,(SELECT SUM(tc.total_hours) FROM log_times as tc WHERE tc.task_id = Easycase.id GROUP BY tc.task_id) as sphours,User.short_name,IF((Easycase.assign_to = 0 OR Easycase.assign_to =" . SES_ID . "),'Me',User.short_name) AS Assigned,EasycaseMilestone.id AS Emid, EasycaseMilestone.milestone_id AS Em_milestone_id,EasycaseMilestone.user_id AS Em_user_id,EasycaseMilestone.id_seq,Milestone.id as Mid,Milestone.title AS Mtitle, Milestone.assign_id AS Massign, Milestone.end_date,Milestone.isactive AS Misactive,Milestone.project_id AS Mproject_id,Milestone.uniq_id AS Muinq_id FROM easycases as Easycase LEFT JOIN easycase_milestones AS EasycaseMilestone ON EasycaseMilestone.easycase_id=Easycase.id LEFT JOIN milestones AS Milestone ON Milestone.id=EasycaseMilestone.milestone_id $milestone_search AND EasycaseMilestone.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive=1) " . $msQuery . " AND Milestone.isactive=" . $isActive . " AND Milestone.id IN(" . $milestone_ids . ") LEFT JOIN users AS User ON Easycase.assign_to=User.id WHERE Easycase.istype='1' AND ".$clt_sql." AND Easycase.isactive=1 AND Easycase.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive=1) AND Easycase.project_id!=0 ORDER BY Milestone.end_date ASC,Mtitle ASC");
                    } else {
                        $caseAll = $this->query("SELECT SQL_CALC_FOUND_ROWS Easycase.*,(SELECT SUM(ec.hours) FROM easycases as ec WHERE ec.case_no = Easycase.case_no AND ec.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive=1) GROUP BY ec.case_no) as sphours,User.short_name,IF((Easycase.assign_to = 0 OR Easycase.assign_to =" . SES_ID . "),'Me',User.short_name) AS Assigned,EasycaseMilestone.id AS Emid, EasycaseMilestone.milestone_id AS Em_milestone_id,EasycaseMilestone.user_id AS Em_user_id,EasycaseMilestone.id_seq,Milestone.id as Mid,Milestone.title AS Mtitle, Milestone.assign_id AS Massign, Milestone.end_date,Milestone.isactive AS Misactive,Milestone.project_id AS Mproject_id,Milestone.uniq_id AS Muinq_id FROM easycases as Easycase LEFT JOIN easycase_milestones AS EasycaseMilestone ON EasycaseMilestone.easycase_id=Easycase.id LEFT JOIN milestones AS Milestone ON Milestone.id=EasycaseMilestone.milestone_id $milestone_search AND EasycaseMilestone.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive=1) " . $msQuery . " AND Milestone.isactive=" . $isActive . " AND Milestone.id IN(" . $milestone_ids . ") LEFT JOIN users AS User ON Easycase.assign_to=User.id WHERE Easycase.istype='1' AND Easycase.isactive=1 AND ".$clt_sql." AND Easycase.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive=1) AND Easycase.project_id!=0 ORDER BY Milestone.end_date ASC,Mtitle ASC");
                    }
                }
                $tot = $this->query("SELECT FOUND_ROWS() as total");
                $CaseCount = $tot[0][0]['total'];
                $msQ = "";

                if (!empty($caseAll)) {
                    $milestone_es_cls = ClassRegistry::init('EasycaseMilestone');
                    $prj_all_eids = Hash::extract($caseAll, '{n}.Easycase.id');
                    $prj_all_eids = implode(',', $prj_all_eids);
                    $prj_all_milestones = $milestone_es_cls->query("SELECT SQL_CALC_FOUND_ROWS `EasycaseMilestone`.`id`,EasycaseMilestone.milestone_id,`EasycaseMilestone`.`easycase_id` FROM easycase_milestones AS `EasycaseMilestone` WHERE EasycaseMilestone.easycase_id IN(" . $prj_all_eids . ")");
                    if (!empty($prj_all_milestones)) {
                        $prj_all_milestones_t = Hash::combine($prj_all_milestones, '{n}.EasycaseMilestone.easycase_id', '{n}.EasycaseMilestone.milestone_id');
                        $resCaseProj['easy_mileston'] = $prj_all_milestones_t;
                    } else {
                        $resCaseProj['easy_mileston'] = array();
                    }
                }
                if ($projUniq != 'all') {
                    foreach ($milestones as $mls) {
                        $mid .= $mls['Milestone']['id'] . ',';
                        $m[$mls['Milestone']['id']]['id'] = $mls['Milestone']['id'];
                        $m[$mls['Milestone']['id']]['caseids'] = $mls[0]['caseids'];
                        $m[$mls['Milestone']['id']]['assign_id'] = $mls['Milestone']['assign_id'];
                        $m[$mls['Milestone']['id']]['MAssignUser'] = $mls['User']['name'];
                        $m[$mls['Milestone']['id']]['totalcases'] = $mls[0]['totalcases'];
                        $m[$mls['Milestone']['id']]['title'] = $mls['Milestone']['title'];
                        $m[$mls['Milestone']['id']]['project_id'] = $mls['Milestone']['project_id'];
                        $m[$mls['Milestone']['id']]['end_date'] = $mls['Milestone']['end_date'];
                        $m[$mls['Milestone']['id']]['uinq_id'] = $mls['Milestone']['uniq_id'];
                        $m[$mls['Milestone']['id']]['isactive'] = $mls['Milestone']['isactive'];
                        $m[$mls['Milestone']['id']]['user_id'] = $mls['Milestone']['user_id'];
                    }
                    $c = array();
                    if ($mid) {
                        $legnd_val = (isset($lgnd_val)) ? $lgnd_val : 3;
                        $closed_cases = $this->query("SELECT EasycaseMilestone.milestone_id,COUNT(Easycase.id) as totcase FROM easycase_milestones AS EasycaseMilestone LEFT JOIN easycases as Easycase ON   EasycaseMilestone.easycase_id=Easycase.id WHERE Easycase.istype='1' AND Easycase.isactive='1' AND Easycase.legend='" . $legnd_val . "' AND EasycaseMilestone.milestone_id IN(" . trim($mid, ',') . ") GROUP BY  EasycaseMilestone.milestone_id");
                        foreach ($closed_cases as $key => $val) {
                            $c[$val['EasycaseMilestone']['milestone_id']]['totalclosed'] = $val[0]['totcase'];
                        }
                    }
                    $resCaseProj['milestones'] = $m;
                }
                if ($projUniq == 'all') {
                    array_push($statuses, "3");
                    array_unique($statuses);
                    $cond = array('conditions' => array('ProjectUser.user_id' => SES_ID, 'ProjectUser.company_id' => SES_COMP, 'Project.isactive' => 1), 'fields' => array('DISTINCT  Project.id'), 'order' => array('ProjectUser.dt_visited DESC'));
                    $mid = '';
                    foreach ($milestones as $k => $v) {
                        $mid .= $v['Milestone']['id'] . ',';
                        $m[$v['Milestone']['id']]['id'] = $v['Milestone']['id'];
                        $m[$v['Milestone']['id']]['caseids'] = $v[0]['caseids'];
                        $m[$v['Milestone']['id']]['assign_id'] = $v['Milestone']['assign_id'];
                        $m[$v['Milestone']['id']]['MAssignUser'] = $v['User']['name'];
                        $m[$v['Milestone']['id']]['totalcases'] = $v[0]['totalcases'];
                        $m[$v['Milestone']['id']]['title'] = $v['Milestone']['title'];
                        $m[$v['Milestone']['id']]['project_id'] = $v['Milestone']['project_id'];
                        $m[$v['Milestone']['id']]['end_date'] = $v['Milestone']['end_date'];
                        $m[$v['Milestone']['id']]['uinq_id'] = $v['Milestone']['uniq_id'];
                        $m[$v['Milestone']['id']]['isactive'] = $v['Milestone']['isactive'];
                        $m[$v['Milestone']['id']]['user_id'] = $v['Milestone']['user_id'];
                    }
                    $c = array();
                    if ($mid) {
                        $closed_cases = $this->query("SELECT EasycaseMilestone.milestone_id,COUNT(Easycase.id) as totcase FROM easycase_milestones AS EasycaseMilestone LEFT JOIN easycases as Easycase ON   EasycaseMilestone.easycase_id=Easycase.id WHERE Easycase.istype='1' AND " . $clt_sql . " AND Easycase.isactive='1' AND Easycase.legend IN (" . implode(',', $statuses) . ") AND EasycaseMilestone.milestone_id IN (" . trim($mid, ',') . ") GROUP BY  EasycaseMilestone.milestone_id");
                        foreach ($closed_cases as $key => $val) {
                            $c[$val['EasycaseMilestone']['milestone_id']]['totalclosed'] = $val[0]['totcase'];
                        }
                    }
                    $resCaseProj['milestones'] = $m;
                }

                $ProjectUser = ClassRegistry::init('ProjectUser');
                if ($projUniq != 'all') {
                    $usrDtlsAll = $ProjectUser->query("SELECT DISTINCT User.id, User.name, User.email, User.istype,User.email,User.short_name,User.photo FROM users as User,easycases as Easycase WHERE (Easycase.user_id=User.id || Easycase.updated_by=User.id || Easycase.assign_to=User.id) AND Easycase.project_id='" . $curProjId . "' AND Easycase.isactive='1' AND " . $clt_sql . " AND Easycase.istype IN('1','2') ORDER BY User.short_name");
                } else {
                    $usrDtlsAll = $ProjectUser->query("SELECT DISTINCT User.id, User.name, User.email, User.istype,User.email,User.short_name,User.photo FROM users as User,easycases as Easycase WHERE (Easycase.user_id=User.id || Easycase.updated_by=User.id || Easycase.assign_to=User.id) AND Easycase.project_id IN (SELECT ProjectUser.project_id FROM project_users AS ProjectUser,projects as Project WHERE ProjectUser.user_id=" . SES_ID . " AND ProjectUser.project_id=Project.id AND Project.isactive='1' AND ProjectUser.company_id='" . SES_COMP . "') AND Easycase.isactive='1' AND " . $clt_sql . " AND Easycase.istype IN('1','2') ORDER BY User.short_name");
                }
                $usrDtlsArr = array();
                $usrDtlsPrj = array();
                foreach ($usrDtlsAll as $ud) {
                    $usrDtlsArr[$ud['User']['id']] = $ud;
                }
                $resCaseProj['status_lists'] = (isset($status_list) && !empty($status_list)) ? $status_list : "";
                $resCaseProj['wrkflw_id'] = (isset($workflow_id) && !empty($workflow_id)) ? $workflow_id : "0";
                $resCaseProj['page_limit'] = $page_limit;
                $resCaseProj['csPage'] = $casePage;
                $resCaseProj['caseUrl'] = $caseUrl;
                $resCaseProj['projUniq'] = $projUniq;
                $resCaseProj['csdt'] = $caseDate;
                $resCaseProj['csTtl'] = $caseTitle;
                $resCaseProj['csDuDt'] = $caseDueDate;
                $resCaseProj['csCrtdDt'] = $caseCreateDate;
                $resCaseProj['csNum'] = $caseNum;
                $resCaseProj['csLgndSrt'] = $caseLegendsort;
                $resCaseProj['csAtSrt'] = $caseAtsort;
                $resCaseProj['caseMenuFilters'] = $caseMenuFilters;
                $frmtCaseAll = $this->formatCases($caseAll, $CaseCount, 'milestone', $c, $m, $projUniq, $usrDtlsArr, $frmt, $dt, $tz, $cq, 1);

                $resCaseProj['caseAll'] = $frmtCaseAll['caseAll'];
                $resCaseProj['status_name'] = (isset($status_name) && !empty($status_name)) ? $status_name : 'Closed';
                $resCaseProj['lgnd_max'] = (isset($lgnd_val) && !empty($lgnd_val)) ? $lgnd_val : 0;
                $resCaseProj['milestones'] = $frmtCaseAll['milestones'];
                $resCaseProj['milestone_hrs'] = $frmtCaseAll['milestone_hrs_arr']['milestone_hrs'];

                //$pgShLbl = $frmt->pagingShowRecords($CaseCount,$page_limit,$casePage);
                //$resCaseProj['pgShLbl'] = $pgShLbl;

                $curCreated = $tz->GetDateTime(SES_TIMEZONE, TZ_GMT, TZ_DST, TZ_CODE, GMT_DATETIME, "datetime");
                $friday = date('Y-m-d', strtotime($curCreated . "next Friday"));
                $monday = date('Y-m-d', strtotime($curCreated . "next Monday"));
                $tomorrow = date('Y-m-d', strtotime($curCreated . "+1 day"));

                $resCaseProj['intCurCreated'] = strtotime($curCreated);
                $resCaseProj['mdyCurCrtd'] = date('m/d/Y', strtotime($curCreated));
                $resCaseProj['mdyFriday'] = date('m/d/Y', strtotime($friday));
                $resCaseProj['mdyMonday'] = date('m/d/Y', strtotime($monday));
                $resCaseProj['mdyTomorrow'] = date('m/d/Y', strtotime($tomorrow));

                if ($projUniq != 'all') {
                    $projUser = array();
                    if ($projUniq) {
                        $projUser = array($projUniq => $this->getMemebers($projUniq));
                    }
                    $resCaseProj['projUser'] = $projUser;
                }

                $resCaseProj['error'] = 0;
                return $resCaseProj;
            }
        } else {
            $total_exist = 0;
            $total_active = 0;
            $total_inactive = 0;
            if ($milestones_all) {
                $total_exist = count($milestones_all);
                foreach ($milestones_all as $k => $v) {
                    if ($v['Milestone']['isactive']) {
                        $total_active++;
                    } else {
                        $total_inactive++;
                    }
                }
            }
            $arr['total_exist'] = $total_exist;
            $arr['total_active'] = $total_active;
            $arr['total_inactive'] = $total_inactive;
            $arr['mile_type'] = $isActive;
            $arr['error'] = "No milestone";
            return $arr;
        }
    }

    function usedSpace($curProjId = NULL, $company_id = SES_COMP) {
        App::import('Model', 'CaseFile');
        $CaseFile = new CaseFile();
        $CaseFile->recursive = -1;
        $cond = " 1 ";
        if ($company_id) {
            $cond .= " AND company_id=" . $company_id;
        }
        if ($curProjId) {
            $cond .= " AND project_id=" . $curProjId;
        }
        $sql = "SELECT SUM(file_size) AS file_size  FROM case_files   WHERE " . $cond;
        $res1 = $CaseFile->query($sql);
        $filesize = $res1['0']['0']['file_size'] / 1024;
        return number_format($filesize, 2);
    }

    /* Function to find highest legend id of task status group */

    function getHighestStatus($prj_id) {
        $status = ClassRegistry::init('Status');
        if ($prj_id == 'all') {
            $prj_cond = '1';
            //$sql = "SELECT * FROM `statuses` WHERE `workflow_id` IN (SELECT workflow_id FROM projects WHERE " . $prj_cond . " AND company_id = " . SES_COMP . " LIMIT 1) ORDER BY seq_order DESC LIMIT 1";
        } else {
            $prj_cond = "(id LIKE '" . $prj_id . "' OR uniq_id LIKE '" . $prj_id . "')";
        }
        $sql = "SELECT * FROM `statuses` WHERE `workflow_id`= (SELECT workflow_id FROM projects WHERE " . $prj_cond . " LIMIT 1) ORDER BY seq_order DESC LIMIT 1";
        $legends = $status->query($sql);
        if ($legends) {
            return $legends[0]['statuses'];
        }
    }

    function getDetailsofTask($case_id) {
        $case_det = $this->find('first', array('conditions' => array('Easycase.id' => $case_id), 'fields' => array('Easycase.title', 'Easycase.message')));
        return $case_det;
    }

    function getTaskMilestone($case_id, $project_id) {
        App::import('Model', 'Milestone');
        $Milestone = new Milestone();
        $case_mile = $this->query('SELECT id,uniq_id,title FROM milestones WHERE id=(SELECT milestone_id FROM easycase_milestones WHERE easycase_id=' . $case_id . ' AND project_id=' . $project_id . '  LIMIT 1) LIMIT 1');
        if ($case_mile) {
            return $case_mile[0]['milestones'];
        } else {
            return 0;
        }
    }
	function getEasycaseFields($condition, $fields) {
        $this->recursive = -1;
        return $this->find('first', array('conditions' => $condition, 'fields' => $fields));
	}
	

}
