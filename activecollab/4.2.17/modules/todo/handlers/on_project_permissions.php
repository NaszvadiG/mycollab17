<?php

  /**
   * Todo lists handle on_project_permissions event
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_project_permissions event
   *
   * @param array $permissions
   */
  function todo_handle_on_project_permissions(&$permissions) {
  	$permissions['todo_list'] = lang('Todo Lists');
  } // todo_handle_on_project_permissions