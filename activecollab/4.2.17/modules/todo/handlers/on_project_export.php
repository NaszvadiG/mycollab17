<?php

  /**
   * Todo lists module on_project_export event handler
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle project exporting
   *
   * @param array $exportable_modules
   * @param Project $project
   * @param array $project_tabs
   * @return null 
   */
  function todo_handle_on_project_export(&$exportable_modules, $project, $project_tabs) {
    if (in_array('todo_lists', $project_tabs, true)) {
  		$exportable_modules['todo'] = array(
    		'name' => lang('Todo Lists'),
    		'exporter' => 'TodoProjectExporter',
    	);
    } //if  	
  } // todo_handle_on_project_export