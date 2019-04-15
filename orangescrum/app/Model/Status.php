<?php
class Status extends AppModel{
	var $name = 'Status';
	
	/**
    * Listing of task status
    * 
    * @method getAllStatus
    * @author Satyajeet
    * @return
    */
	function getAllStatus(){
		$sql = "SELECT GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS project_ids FROM projects WHERE company_id=". SES_COMP;
		$data = $this->query($sql);
		$res = "";
		if (isset($data['0']['0']['project_ids']) && !empty($data['0']['0']['project_ids'])) {
		   /* $sql = "SELECT Total.*, Type.* FROM (SELECT Easycase.type_id, COUNT(Easycase.id) AS cnt FROM easycases AS Easycase
			WHERE Easycase.project_id IN (".$data['0']['0']['project_ids'].") AND Easycase.istype=1 GROUP BY Easycase.type_id) AS Total
			RIGHT JOIN types AS Type ON (Type.id=Total.type_id) WHERE Type.company_id = ". SES_COMP ." OR Type.company_id = 0 ORDER BY Type.company_id DESC, Type.seq_order ASC";*/
			$sql = "SELECT Total.*, Status.* FROM (SELECT Easycase.legend AS status_id , COUNT(Easycase.id) AS cnt FROM easycases AS Easycase
			WHERE Easycase.project_id IN (".$data['0']['0']['project_ids'].") AND Easycase.istype=1 GROUP BY Easycase.legend) AS Total
			RIGHT JOIN statuses AS Status ON (Status.id=Total.status_id) WHERE Status.company_id = ". SES_COMP ." OR Status.company_id = 0 ORDER BY Status.company_id DESC,Status.name ASC";
			$res = $this->query($sql);
		}
		return $res;
	}
	
	/**
    * Listing of default task status
    * 
    * @method getDefaultStatus
    * @author Satyajeet
    * @return
    */
    function getDefaultStatus() {
		return $this->find("all", array("conditions" => array('Status.company_id' =>  0 )));
    }
    
    function changeColor($color, $sid){
        $this->id = $sid;
        if($this->saveField('color', $color)){
            return true;
        }else{
            return false;
        }
    }
    
    function changePercent($percent, $sid){
        $this->id = $sid;
        if($this->saveField('percentage', $percent)){
            return true;
        }else{
            return false;
        }
    }
    
    function updateLabel($sid, $label){
        $this->id = $sid;
        if($this->saveField('name', $label)){
            return true;
        }else{
            return false;
        }
    }
    
    function update_sequence($data, $wid){
        if(isset($data) && !empty($data)){
            $cnt = 0;
            foreach($data as $k=>$val){
                $sql = "UPDATE statuses AS Status SET Status.seq_order = ".$val['seq_odr']. " WHERE Status.workflow_id=" .$wid. " AND Status.id = " .$val['status_id']. "";
                #echo $sql;exit;
                $this->query($sql);
                $cnt++;
            }
            if($cnt == count($data)){
                return true;
            }else{
                return false;
            }
        }
    }
    
    function checkStausName($wfid,$label){
        $count = $this->find('count',array('conditions'=>array('Status.workflow_id' => $wfid,'BINARY(Status.name)'=> $label)));
        return $count;
}
}
?>