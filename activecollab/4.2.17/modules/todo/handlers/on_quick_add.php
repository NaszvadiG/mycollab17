<?php

  /**
   * Todo module on_quick_add event handler
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */
  
  /**
   * Handle on quick add event
   *
   * @param NamedList $items
   * @param NamedList $subitems
   * @param array $map
   * @param User $logged_user
   * @param DBResult $projects 
   * @param DBResult $companies
   * @param string $interface
   */
  function todo_handle_on_quick_add($items, $subitems, &$map, $logged_user, $projects, $companies, $interface = AngieApplication::INTERFACE_DEFAULT) {
  	$item_id = 'todo';
  	
  	if(is_foreachable($projects)) {
  		foreach($projects as $project) {
  			if(TodoLists::canAdd($logged_user, $project)) {
  				$map[$item_id][] = 'project_' . $project->getId();
  			} // if
  		} // foreach
  		
  		if(isset($map[$item_id])) {
		  	$items->add($item_id, array(
		  		'text' => lang('Todo List'),
		  		'title' => lang('Add Todo List to the Project'),
		  		'dialog_title' => lang('Add Todo List to the :name Project'),
		  		'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/32x32/todo.png', TODO_MODULE) : AngieApplication::getImageUrl('icons/96x96/todolist.png', TODO_MODULE, $interface),
		  		'url' => Router::assemble('project_todo_lists_add', array('project_slug' => '--PROJECT-SLUG--')),
		    	'group' => QuickAddCallback::GROUP_PROJECT,
		    	'event' => 'todo_list_created',
		  	));
  		} // if
  	} // if

  } // todo_handle_on_project_tabs