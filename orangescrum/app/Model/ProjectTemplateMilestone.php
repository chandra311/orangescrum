<?php
class ProjectTemplateMilestone extends AppModel{
	var $name = 'ProjectTemplateMilestone';

	public $hasMany = array(
        'ProjectTemplateCase' => array(
            'className' => 'ProjectTemplateCase',
            'foreignKey' => 'project_template_milestone_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => 'ProjectTemplateCase.id ASC',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );
}
?>
