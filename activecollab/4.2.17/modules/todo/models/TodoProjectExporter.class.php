<?php

  class TodoProjectExporter extends ProjectExporter {

  	/**
  	 * active module
  	 * 
  	 * @var string
  	 */
  	protected $active_module = TODO_MODULE;

    /**
     * Relative path where exported files will be stored
     * 
     * @var string
     */
    protected $relative_path = 'todo';

    /**
     * Export the todo
     * 
     * @param void
     * @return null
     */
    public function export() {
      parent::export();
      if ($this->section == 'todo') {
	      $todo_lists_count = TodoLists::countByProject($this->project, null, STATE_ARCHIVED, $this->getObjectsVisibility());
	      $per_query = 500;
	      $loops = ceil($todo_lists_count / $per_query);
	      
	      // create single todo_list page for every todo_list in the project
	      $current_iteration = 0;
	      while ($current_iteration < $loops) {
	      	$result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'TodoList' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_iteration * $per_query . ", $per_query", $this->project->getId(), STATE_ARCHIVED, $this->getObjectsVisibility());
	      	if ($result instanceof DBResult) {
	      		foreach ($result as $row) {
	      			$todo_list = new TodoList();
		    			$todo_list->loadFromRow($row);
		    			$this->smarty->assignByRef('todo_list', $todo_list);
		    			$this->renderTemplate('todo_list', $this->getDestinationPath('todo_list_' . $todo_list->getId() . '.html'));
		    			$this->smarty->clearAssign('todo_list');
		    			unset($row);
	      		} //foreach
	      	} //if
  			set_time_limit(30);
  			$current_iteration++;
	      } // while

		  $categories = Categories::findBy($this->project, 'TodoListCategory');
		  $categories_for_helper = Categories::findBy($this->project, 'TodoListCategory');
		  $this->smarty->assignByRef('categories', $categories_for_helper);

	      // render todo_list index page     
	      $this->renderTemplate('todo_lists_index', $this->getDestinationPath('index.html'));

	      // export categories
	      if (is_foreachable($categories)) {
	      	foreach ($categories as $category) {
    		  $this->smarty->assignByRef('category', $category);
    		  $this->renderTemplate('todo_lists_index', $this->getDestinationPath('category_' . $category->getId() . '.html'));
    		  $this->smarty->clearAssign('category');
	      	} // foreach      	
	      } // if				
      } // if

	  return true;
    } // export

  } // ProjectExporter