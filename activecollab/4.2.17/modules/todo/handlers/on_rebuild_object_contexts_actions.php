<?php

  /**
   * on_rebuild_object_contexts_actions event handler
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_object_contexts_actions event
   * 
   * @param array $actions
   */
  function todo_handle_on_rebuild_object_contexts_actions(&$actions) {
    $actions[Router::assemble('object_contexts_admin_rebuild_todo_lists')] = lang('Rebuild to do lists object contexts');
  } // todo_handle_on_rebuild_object_contexts_actions