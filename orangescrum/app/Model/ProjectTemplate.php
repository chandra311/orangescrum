<?php
class ProjectTemplate extends AppModel{
	var $name = 'ProjectTemplate';

	public $hasMany = array(
        'ProjectTemplateMilestone' => array(
            'className' => 'ProjectTemplateMilestone',
            'foreignKey' => 'project_template_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => 'ProjectTemplateMilestone.id ASC',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
}
?>
