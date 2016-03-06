<?php

  /**
   * Todo lists manager
   *
   * @package activeCollab.modules.todo
   * @subpackage models
   */
  class TodoLists extends ProjectObjects {
    
    /**
     * Returns true if $user can access todo section of $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAccess(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAccess($user, $project, 'todo_list', ($check_tab ? 'todo_lists' : null));
    } // canAccess
    
    /**
     * Returns true if $user can create a new todolist in $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canAdd(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canAdd($user, $project, 'todo_list', ($check_tab ? 'todo_lists' : null));
    } // canAdd
    
    /**
     * Returns true if $user can manage todolists in $project
     *
     * @param IUser $user
     * @param Project $project
     * @param boolean $check_tab
     * @return boolean
     */
    static function canManage(IUser $user, Project $project, $check_tab = true) {
      return ProjectObjects::canManage($user, $project, 'todo_list', ($check_tab ? 'todo_lists' : null));
    } // canManage
    
    // ---------------------------------------------------
    //  Finders
    // ---------------------------------------------------
  
    /**
     * Return all todo lists for a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    static function findByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'TodoList', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findByProject
    
    /**
     * Return all active todo lists in a given project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findActiveByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL', $project->getId(), 'TodoList', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findActiveByProject
    
    /**
     * Return completed todo lists by project
     *
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @return array
     */
    function findCompletedByProject(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NOT NULL', $project->getId(), 'TodoList', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, created_on',
      ));
    } // findCompletedByProject
    
    /**
     * Find all todo lists by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @param integer $limit
     * @param array $exclude
     * @param int $timestamp
     * @return array
     */
    static function findByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $limit = null, $exclude = null, $timestamp = null) {
      $conditions = array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'TodoList', $min_state, $min_visibility); // Milestone ID + Project ID (integrity issue from activeCollab 2)
      if ($exclude && $timestamp) {
      	$conditions[0] .= ' AND id NOT IN (?) AND created_on < ?';
      	$conditions[] = $exclude;
      	$conditions[] = date(DATETIME_MYSQL, $timestamp); 
      }
    	return TodoLists::find(array(
        'conditions' => $conditions,
        'order' => 'priority DESC',
        'limit' => $limit,
      ));
    } // findByMilestone
    
    /**
     * Return number of todo lists by milestone
     *
     * @param Milestone $milestone
     * @param integer $min_state
     * @param integer $min_visibility
     * @return int
     */
    static function countByMilestone(Milestone $milestone, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return TodoLists::count(array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $milestone->getId(), $milestone->getProjectId(), 'TodoList', $min_state, $min_visibility)); // Milestone ID + Project ID (integrity issue from activeCollab 2)
    } // countByMilestone
    
    /**
     * Return todo lists by a todo list category
     *
     * @param TodoListCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     */
    function findByCategory(TodoListCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return ProjectObjects::find(array(
        'conditions' => array('category_id = ? AND type = ? AND state >= ? AND visibility >= ?', $category->getId(), 'TodoList', $min_state, $min_visibility),
        'order' => 'ISNULL(position) ASC, position, priority DESC',
      ));
    } // findByCategory
    
    /**
     * Return number of to do lists from a given category
     * 
     * @param TodoListCategory $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return integer
     */
    static function countByCategory(TodoListCategory $category, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
      return TodoLists::count(array('category_id = ? AND type IN (?) AND state >= ? AND visibility >= ?', $category->getId(), 'TodoList', $min_state, $min_visibility));
    } // countByCategory
    
    /**
     * Count todo lists by project
     * 
     * @param Project $project
     * @param Category $category
     * @param integer $min_state
     * @param integer $min_visibility
     * @return number
     */
    public function countByProject(Project $project, $category = null, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL) {
    	if ($category instanceof TodoListCategory) {
    		return TodoLists::count(array('project_id = ? AND type = ? AND category_id = ? AND state >= ? AND visibility >= ?', $project->getId(), 'TodoList', $category->getId(), $min_state, $min_visibility));
    	} else {
    		return TodoLists::count(array('project_id = ? AND type = ? AND state >= ? AND visibility >= ?', $project->getId(), 'TodoList', $min_state, $min_visibility));
    	} // if
    } // countByProject
    
    
    /**
     * Find all todolists in project, and prepare them for objects list
     * 
     * @param Project $project
     * @param User $user
     * @return array
     */
    function findForObjectsList(Project $project, $user, $state = STATE_VISIBLE) {
    	$result = array();
    	
    	$todo_lists = DB::execute("SELECT id, name, category_id, milestone_id, project_id, completed_on, state, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'TodoList' AND project_id = ? AND state = ? AND visibility >= ? ORDER BY position ASC, created_on", $project->getId(), $state, $user->getMinVisibility());
    	if ($todo_lists instanceof DBResult) {
    	  $todo_lists->setCasting(array(
    	    'id' => DBResult::CAST_INT, 
    	    'category_id' => DBResult::CAST_INT, 
    	    'milestone_id' => DBResult::CAST_INT, 
    	    'completed_on' => DBResult::CAST_DATE, 
    	  ));
    	  
    	  $todo_list_url = Router::assemble('project_todo_list', array('project_slug' => $project->getSlug(), 'todo_list_id' => '--TODOLISTID--'));
    	  $project_id = $project->getId();
    	  
    		foreach ($todo_lists as $todo_list) {
    		  list($total_subtasks, $open_subtasks) = ProjectProgress::getObjectProgress(array(
    		    'project_id' => $project_id, 
    		    'object_type' => 'TodoList', 
    		    'object_id' => $todo_list['id'], 
    		  ));
    		  
          $result[] = array(
            'id'              => $todo_list['id'],
            'name'            => $todo_list['name'],
            'project_id'      => $todo_list['project_id'],
            'category_id'     => $todo_list['category_id'],
            'milestone_id'    => $todo_list['milestone_id'],
            'is_completed'    => $todo_list['completed_on'] instanceof DateValue ? 1 : 0,
            'permalink'       => str_replace('--TODOLISTID--', $todo_list['id'], $todo_list_url),
            'total_subtasks'  => $total_subtasks,
            'open_subtasks'   => $open_subtasks,
            'is_favorite'     => Favorites::isFavorite(array('TodoList', $todo_list['id']), $user),
            'is_archived'     => $todo_list['state'] == STATE_ARCHIVED ? 1 : 0,
            'visibility'      => $todo_list['visibility']
          );
    		} // foreach
    	} // if
    	
    	return $result;
    } // findForObjectsList

    /**
     * Find todo for outline
     *
     * @param Project $project
     * @param User $user
     * @param int $state
     * @return array
     */
    static function findForOutline(Project $project, $milestone_id, User $user, $state = STATE_VISIBLE) {
      if ($milestone_id) {
        $todo_list_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL AND milestone_id = ?', $project->getId(), 'TodoList', $state, $user->getMinVisibility(), $milestone_id);
      } else {
        $todo_list_ids = DB::executeFirstColumn('SELECT id FROM ' . TABLE_PREFIX . 'project_objects WHERE project_id = ? AND type = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL AND milestone_id < 1', $project->getId(), 'TodoList', $state, $user->getMinVisibility());
      } // if

      if (!is_foreachable($todo_list_ids)) {
        return false;
      } // if

      $todo_lists = DB::execute('SELECT id, name, body, due_on, priority, date_field_1 AS start_on, assignee_id, visibility, created_by_id, label_id, milestone_id FROM ' . TABLE_PREFIX . 'project_objects WHERE ID IN(?)', $todo_list_ids);

      // casting
      $todo_lists->setCasting(array(
        'due_on'        => DBResult::CAST_DATE,
        'start_on'      => DBResult::CAST_DATE
      ));

      $todo_lists_id_prefix_pattern = '--TODO-LIST-ID--';
      $todo_list_url_params = array('project_slug' => $project->getSlug(), 'todo_list_id' => $todo_lists_id_prefix_pattern);
      $view_todo_list_url_pattern = Router::assemble('project_todo_list', $todo_list_url_params);
      $edit_todo_list_url_pattern = Router::assemble('project_todo_list_edit', $todo_list_url_params);
      $trash_todo_list_url_pattern = Router::assemble('project_todo_list_trash', $todo_list_url_params);
      $complete_todo_list_url_pattern = Router::assemble('project_todo_list_complete', $todo_list_url_params);

      // can_manage_todo_lists
      $can_manage_todo_lists = ($user->projects()->getPermission('todo_list', $project) >= ProjectRole::PERMISSION_MANAGE);

      // all subscriptions
      $user_subscriptions_on_todo_lists = DB::executeFirstColumn('SELECT parent_id FROM ' . TABLE_PREFIX . 'subscriptions WHERE parent_id IN (?) AND parent_type = ? AND user_id = ?', $todo_list_ids, 'TodoList', $user->getId());

      $results = array();
      foreach ($todo_lists as $subobject) {
        $todo_list_id = array_var($subobject, 'id');

        $results[] = array(
          'id'                  => $todo_list_id,
          'name'                => array_var($subobject, 'name'),
          'body'                => array_var($subobject, 'body'),
          'priority'            => array_var($subobject, 'priority'),
          'milestone_id'        => array_var($subobject, 'milestone_id', null),
          'class'               => 'TodoList',
          'start_on'            => array_var($subobject, 'start_on'),
          'due_on'              => array_var($subobject, 'due_on'),
          'assignee_id'         => array_var($subobject, 'assignee_id'),
          'user_is_subscribed'  => in_array($todo_list_id, $user_subscriptions_on_todo_lists),
          'event_names'         => array(
            'updated'             => 'todo_list_updated'
          ),
          'urls'                => array(
            'view'                => str_replace($todo_lists_id_prefix_pattern, $todo_list_id, $view_todo_list_url_pattern),
            'edit'                => str_replace($todo_lists_id_prefix_pattern, $todo_list_id, $edit_todo_list_url_pattern),
            'trash'               => str_replace($todo_lists_id_prefix_pattern, $todo_list_id, $trash_todo_list_url_pattern),
            'complete'            => str_replace($todo_lists_id_prefix_pattern, $todo_list_id, $complete_todo_list_url_pattern),
          ),
          'permissions'         => array(
            'can_edit'            => can_edit_project_object($subobject, $user, $project, $can_manage_todo_lists),
            'can_trash'           => can_trash_project_object($subobject, $user, $project, $can_manage_todo_lists),
          )
        );
      } // foreach

      return $results;
    } // findForOutline
    
    /**
     * Find todo lists for printing by grouping and filtering criteria
     * 
     * @param Project $project
     * @param integer $min_state
     * @param integer $min_visibility
     * @param string $group_by
     * @param array $filter_by
     * @return DBResult
     */
    public function findForPrint(Project $project, $min_state = STATE_VISIBLE, $min_visibility = VISIBILITY_NORMAL, $group_by = null, $filter_by = null) { 	
      // initial condition
      $conditions = array(
      	DB::prepare('(project_id = ? AND type = ? AND state = ? AND visibility >= ?)', $project->getId(), 'TodoList', $min_state, $min_visibility),
      );
      
      if (!in_array($group_by, array('milestone_id', 'category_id'))) {
      	$group_by = null;
      } // if
                
      // filter by completion status
      $filter_is_completed = array_var($filter_by, 'is_completed', null);
      
      if ($filter_is_completed === '0') {
				$conditions[] = '(completed_on IS NULL)';
      } else if ($filter_is_completed === '1') {
      	$conditions[] = '(completed_on IS NOT NULL)';
      } // if
      
      // do find todo lists
      $todo = TodoLists::find(array(
      	'conditions' => implode(' AND ', $conditions),
      	'order' => ($group_by ? $group_by . ', ' : '') . 'ISNULL(position) ASC, position, priority DESC'
      ));
    	
    	return $todo;
    } // findForPrint
    
    /**
     * Get all items from result and describes array for paged list 
     * 
     * @param DBResult $result
     * @param Project $active_project
     * @param User $logged_user
     * @param int $items_limit
     * @return Array
     */
    static function getDescribedTodoListArray(DBResult $result, Project $active_project, User $logged_user, $items_limit = null) {
    	$return_value = array();
    	
    	if ($result instanceof DBResult) {
    		
    		$user_ids = array();
    		foreach($result as $row) {
    			if ($row['created_by_id'] && !in_array($row['created_by_id'], $user_ids)) {
    				$user_ids[] = $row['created_by_id'];
    			} //if
    		} //if

        $users_array = count($user_ids) ? Users::findByIds($user_ids)->toArrayIndexedBy('getId') : array();
    		
    	  foreach($result as $row) {
    	    $todo_list = array();
    	    
      		// TodoList Details
      		$todo_list['id'] = $row['id'];
      		$todo_list['name'] = clean($row['name']);
      		$todo_list['is_favorite'] = Favorites::isFavorite(array('TodoList', $todo_list['id']), $logged_user);
      		$todo_list['is_completed'] = (datetimeval($row['completed_on']) instanceof DateTimeValue) ? 1 : 0;
      		
      		// Favorite
      		$favorite_params = $logged_user->getRoutingContextParams();
      		$favorite_params['object_type'] = $row['type'];
      		$favorite_params['object_id'] = $row['id'];

          // Urls
          $todo_list['urls']['remove_from_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_remove_from_favorites', $favorite_params);
          $todo_list['urls']['add_to_favorites'] = Router::assemble($logged_user->getRoutingContext() . '_add_to_favorites', $favorite_params);
          $todo_list['urls']['view'] = Router::assemble('project_todo_list', array('project_slug' => $active_project->getSlug(), 'todo_list_id' => $row['id']));
          $todo_list['urls']['edit'] = Router::assemble('project_todo_list_edit', array('project_slug' => $active_project->getSlug(), 'todo_list_id' => $row['id']));
          $todo_list['urls']['trash'] = Router::assemble('project_todo_list_trash', array('project_slug' => $active_project->getSlug(), 'todo_list_id' => $row['id']));

          // CRUD

          $todo_list['permissions']['can_edit'] = TodoLists::canManage($logged_user, $active_project);
          $todo_list['permissions']['can_trash'] = TodoLists::canManage($logged_user, $active_project);
      		
      		// User & datetime details
      		$todo_list['created_on'] = datetimeval($row['created_on']);
      		
      		if($row['created_by_id']) {
            $todo_list['created_by'] = $users_array[$row['created_by_id']];
          } elseif($row['created_by_email']) {
            $todo_list['created_by'] = new AnonymousUser($row['created_by_name'], $row['created_by_email']);
          } else {
            $todo_list['created_by'] = null;
          } // if
      		$return_value[] = $todo_list;
      		
      		if (count($return_value) === $items_limit) {
      			break;
      		} // if
    	  } // foreach
    	} // if
    	
    	return $return_value;
    } //getDescribedTodoListArray
  
  }