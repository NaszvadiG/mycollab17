<?php

  /**
   * Todo module on_rebuild_activity_log_actions event handler implementation
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_rebuild_activity_log_actions event
   * 
   * @param array $actions
   */
  function todo_handle_on_rebuild_activity_log_actions(&$actions) {
    $actions[Router::assemble('activity_logs_admin_rebuild_todo')] = lang('Rebuild to do log entries');
  } // todo_handle_on_rebuild_activity_log_actions