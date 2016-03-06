<?php

  /**
   * on_build_names_search_index_for_project event handler
   * 
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_build_names_search_index_for_project event
   * 
   * @param NamesSearchIndex $search_index
   * @param Project $project
   */
  function todo_handle_on_build_names_search_index_for_project(NamesSearchIndex &$search_index, Project &$project) {
    $todo_lists = DB::execute("SELECT id, type, name, body, visibility FROM " . TABLE_PREFIX . "project_objects WHERE type = 'TodoList' AND project_id = ? AND state >= ?", $project->getId(), STATE_VISIBLE);
    
    if($todo_lists) {
      $project_id = $project->getId();
      
      foreach($todo_lists as $todo_list) {
        $todo_list_id = (integer) $todo_list['id'];
        $visibility = $todo_list['visibility'] == VISIBILITY_PRIVATE ? 'private' : 'normal';
        
        Search::set($search_index, array(
          'class' => 'TodoList', 
          'id' => $todo_list_id,
        	'context' => "projects:projects/$project_id/todo/$visibility/$todo_list[id]", 
          'name' => $todo_list['name'], 
          'visibility' => $todo_list['visibility'],
        ));
      } // foreach
    } // if
  } // todo_handle_on_build_names_search_index_for_project