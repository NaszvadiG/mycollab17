<?php

  /**
   * Todo lists module on_get_completable_project_object_types events handler
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */
  
  /**
   * Return completable todo lists module types
   *
   * @return string
   */
  function todo_handle_on_get_completable_project_object_types() {
    return 'TodoList';
  } // todo_handle_on_get_completable_project_object_types