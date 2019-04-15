<?php

namespace Timelog\View\Helper;

use Cake\View\Helper;
App::import('Vendor', 's3', array('file' => 's3' . DS . 'S3.php'));

class FormatHelper extends AppHelper {

	function getTaskdetails($prjid, $tskid) {
        $Tasks = ClassRegistry::init('Easycase');
        //$Tasks->recursive = -1;
        $tskDtls = $Tasks->find('first', array('conditions' => array('Easycase.id' => $tskid, 'Easycase.project_id' => $prjid)));
        return $tskDtls;
    }

    function getTaskdetailsNew($tskid) {
        $Tasks = ClassRegistry::init('Easycase');
        //$Tasks->recursive = -1;
        $tskDtls = $Tasks->find('first', array('conditions' => array('Easycase.id' => $tskid)));
        return $tskDtls;
    }

    function getTaskType($tsktypid) {
        $Types = ClassRegistry::init('Type');
        //$Tasks->recursive = -1;
        $typDtls = $Types->find('first', array('conditions' => array('Type.id' => $tsktypid)));
        return $typDtls;
    }

    function frmtdata($str, $strt = 0, $len = 20) {
        if (!empty($str) && strlen($str) > $len) {
            $newstr = substr($str, $strt, $len);
            return $newstr . "...";
        } else {
            return $str;
        }
    }

    function chngdttime($lgdt, $lgtime) {
        $newdt = $lgdt . " " . $lgtime;
        return date("g:i A", strtotime($newdt));
    }
	
	/*Author: GKM
     * to format sec to hr min
     */
    function format_time_hr_min($totalsecs = '') {
        $hours = floor($totalsecs / 3600) > 0 ? floor($totalsecs / 3600) . " hr".(floor($totalsecs / 3600) > 1?'s':'')." " : '';
        $mins = round(($totalsecs % 3600) / 60) > 0 ? "" . round(($totalsecs % 3600) / 60) . " min".(round(($totalsecs % 3600) / 60) > 1?'s':'') : '';
        return $hours."".$mins;
    }
}
?>