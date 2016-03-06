<?php

  /**
   * Init todo module
   *
   * @package activeCollab.modules.todo
   */
  
  define('TODO_MODULE', 'todo');
  define('TODO_MODULE_PATH', APPLICATION_PATH . '/modules/todo');
  
  AngieApplication::setForAutoload(array(
    'TodoList' => TODO_MODULE_PATH . '/models/todo_lists/TodoList.class.php',
    'TodoLists' => TODO_MODULE_PATH . '/models/todo_lists/TodoLists.class.php',
    
    'ITodoListCategoryImplementation' => TODO_MODULE_PATH . '/models/todo_list_categories/ITodoListCategoryImplementation.class.php',
    'TodoListCategory' => TODO_MODULE_PATH . '/models/todo_list_categories/TodoListCategory.class.php',
    
    'TodoListHistoryRenderer' => TODO_MODULE_PATH . '/models/TodoListHistoryRenderer.class.php',
  
    'TodoProjectExporter' => TODO_MODULE_PATH . '/models/TodoProjectExporter.class.php',
  
    'ITodoListSearchItemImplementation' => TODO_MODULE_PATH . '/models/ITodoListSearchItemImplementation.class.php',

    'NewTodoListNotification' => TODO_MODULE_PATH . '/notifications/NewTodoListNotification.class.php',
  ));
  
  DataObjectPool::registerTypeLoader('TodoList', function($ids) {
    return TodoLists::findByIds($ids, STATE_TRASHED, VISIBILITY_PRIVATE);
  });