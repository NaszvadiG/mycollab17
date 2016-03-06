<?php

  // Extend milestones controller
  AngieApplication::useController('milestones', SYSTEM_MODULE);

  /**
   * Milestone todo lists controller implementation
   *
   * @package activeCollab.modules.todo
   * @subpackage controllers
   */
  class MilestoneTodoListsController extends MilestonesController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_milestone->isNew()) {
        $this->response->notFound();
      } // if
      
      $add_todo_list_url = false;
      if(TodoLists::canAdd($this->logged_user, $this->active_project)) {
        $add_todo_list_url = Router::assemble('project_todo_lists_add', array(
          'project_slug' => $this->active_project->getSlug(), 
          'milestone_id' => $this->active_milestone->getId()
        ));
        
        $this->wireframe->actions->add('new_todo_list', lang('New Todo List'), $add_todo_list_url);
      } // if
      
      $this->smarty->assign('add_todo_list_url', $add_todo_list_url);
    } // __construct
    
    /**
     * Show milestone todo lists
     */
    function index() {
    	// Serve request made with web browser
      if($this->request->isWebBrowser()) {
      	$items_per_page = 30;
	      
	    	$this->response->assign('more_results_url', Router::assemble('milestone_todo_lists', array(
	    		'project_slug' => $this->active_project->getSlug(), 
	    	  'milestone_id' =>$this->active_milestone->getId())
	    	));
	    	
	    	if($this->request->get('paged_list')) {
	    		$exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
	    		$timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'TodoList' AND state >= ? AND visibility >= ? AND id NOT IN (?) AND created_on < ? ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC LIMIT $items_per_page", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility(), $exclude, date(DATETIME_MYSQL, $timestamp));
	    		$this->response->respondWithData(TodoLists::getDescribedTodoListArray($result, $this->active_project, $this->logged_user, $items_per_page));
	    	} else {
	    		$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'TodoList' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(completed_on) DESC, priority DESC, created_on DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility());
		      $todo_lists = TodoLists::getDescribedTodoListArray($result, $this->active_project, $this->logged_user, $items_per_page);
		      $this->response->assign(array(
		      	'todo_lists' => $todo_lists,
		      	'items_per_page'  => $items_per_page,
		      	'total_items' => ($result instanceof DBResult) ? $result->count() : 0,
		      	'milestone_id' => $this->active_milestone->getId()
		      ));
	    	} //if
	    	
      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
      	$this->response->assign(array(
          'todo_lists' => DB::execute("SELECT id, name, category_id, milestone_id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'TodoList' AND milestone_id = ? AND completed_on IS NULL AND state >= ? AND visibility >= ? ORDER BY priority DESC, created_on DESC", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility()),
        	'todo_list_url' => Router::assemble('project_todo_list', array('project_slug' => $this->active_project->getSlug(), 'todo_list_id' => '--TODOLISTID--')), 
        ));
      } // if
    } // index
    
  }