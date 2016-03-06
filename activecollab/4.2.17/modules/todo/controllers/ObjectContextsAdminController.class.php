<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_object_contexts_admin', ENVIRONMENT_FRAMEWORK);

  /**
   * Object contexts controller
   * 
   * @package activeCollab.modules.todo
   * @subpackage controllers
   */
  class ObjectContextsAdminController extends FwObjectContextsAdminController {
    
    /**
     * Rebuild todo list entries
     */
    function rebuild_todo_lists() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        try {
          ApplicationObjects::rebuildProjectObjectContexts(array('TodoList'), 'todo');
          $this->response->ok();
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // rebuild_todo_lists
    
  }