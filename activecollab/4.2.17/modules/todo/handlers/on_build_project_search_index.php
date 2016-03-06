<?php

  /**
   * on_build_project_search_index event handler implementation
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * on_build_project_search_index event handler
   *
   * @param ProjectObjectsSearchIndex $search_index
   * @param Project $project
   * @param array $users_map
   * @param array $milestones_map
   */
  function todo_handle_on_build_project_search_index(ProjectObjectsSearchIndex &$search_index, Project &$project, &$users_map, &$milestones_map) {
    $todo_lists = DB::execute("SELECT id, category_id, milestone_id, name, body, visibility, completed_on FROM " . TABLE_PREFIX . "project_objects WHERE type = 'TodoList' AND project_id = ? AND state >= ?", $project->getId(), STATE_ARCHIVED);
    
    if($todo_lists) {
      $project_id = $project->getId();
      $project_name = $project->getName();

      $item_context = "projects:projects/$project_id/todo";
      
      $categories_map = Categories::getIdNameMap($project, 'TodoListCategory');
      
      foreach($todo_lists as $todo_list) {
        $milestone_id = $todo_list['milestone_id'] ? (integer) $todo_list['milestone_id'] : null;
        $category_id = $todo_list['category_id'] ? (integer) $todo_list['category_id'] : null;
        
        Search::set($search_index, array(
          'class' => 'TodoList', 
          'id' => (integer) $todo_list['id'],
        	'context' => $item_context . '/' . ($todo_list['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $todo_list['id'],
          'project_id' => $project_id, 
  				'project' => $project_name, 
          'milestone_id' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestone_id : null, 
          'milestone' => $milestone_id && $milestones_map && isset($milestones_map[$milestone_id]) ? $milestones_map[$milestone_id] : null,
          'category_id' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $category_id : null, 
          'category' => $category_id && $categories_map && isset($categories_map[$category_id]) ? $categories_map[$category_id] : null, 
          'name' => $todo_list['name'], 
          'body' => $todo_list['body'] ? $todo_list['body'] : null,
          'visibility' => $todo_list['visibility'],  
          'completed_on' => $todo_list['completed_on'], 
        ));
      } // foreach
    } // if
  } // todo_handle_on_build_project_search_index