<?php

  /**
   * Todo module on_available_project_tabs event handler
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Populate list of available project tabs
   * 
   * @param array $tabs
   */
  function todo_handle_on_available_project_tabs(&$tabs) {
    $tabs['todo_lists'] = lang('Todo');
  } // todo_handle_on_available_project_tabs