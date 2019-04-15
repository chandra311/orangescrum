<?php
class ProjectTemplateCase extends AppModel{
	var $name = 'ProjectTemplateCase';

	public $belongsTo = array(
        'ProjectTemplateMilestone' => array(
            'className' => 'ProjectTemplateMilestone',
            'foreignKey' => 'project_template_milestone_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );
}
?>
