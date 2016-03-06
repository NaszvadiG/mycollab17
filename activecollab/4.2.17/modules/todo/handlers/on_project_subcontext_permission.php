<?php

  /**
   * on_project_subcontext_permission event handler implementation
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_project_subcontext_permission event
   * 
   * @param array $map
   */
  function todo_handle_on_project_subcontext_permission(&$map) {
    $map['todo'] = 'todo_list';
  } // todo_handle_on_project_subcontext_permission