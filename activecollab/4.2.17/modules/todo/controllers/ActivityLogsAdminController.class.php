<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_activity_logs_admin', ACTIVITY_LOGS_FRAMEWORK);

  /**
   * Activity logs controller
   * 
   * @package activeCollab.modules.todo
   * @subpackage controllers
   */
  class ActivityLogsAdminController extends FwActivityLogsAdminController {
    
    /**
     * Rebuild todo entries
     */
    function rebuild_todo() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ActivityLogs::rebuildProjectObjectActivityLogs(array('TodoList'), 'todo', array('TodoList' => 'todo_list/created'), 'todo_list/completed', 'todo_list/reopened', false, true);
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_todo
    
  }