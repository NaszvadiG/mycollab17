<?php

  /**
   * on_master_categories handler definition
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Handle on_master_categories event
   *
   * @param array $categories
   */
  function todo_handle_on_master_categories(&$categories) {
  	$categories[] = array(
  	  'name' => 'todo_list_categories',
  	  'label' => lang('Todo List Categories'),
  	  'value' => ConfigOptions::getValue('todo_list_categories'),
  	  'type' => 'TodoListCategory', 
  	);
  } // todo_handle_on_master_categories