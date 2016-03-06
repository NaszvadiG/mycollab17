<?php

  /**
   * Todo lists module on_project_tabs event handler
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */
  
  /**
   * Handle on prepare project overview event
   *
   * @param NamedList $tabs
   * @param User $logged_user
   * @param Project $project
   * @param array $tabs_settings
   * @param string $interface
   */
  function todo_handle_on_project_tabs(&$tabs, &$logged_user, &$project, &$tabs_settings, $interface) {
    if(in_array('todo_lists', $tabs_settings) && TodoLists::canAccess($logged_user, $project, false)) {
      $tabs->add('todo_lists', array(
        'text' => lang('Todo'),
        'url' => Router::assemble('project_todo_lists', array('project_slug' => $project->getSlug())),
        'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/16x16/todo-tab-icon.png', TODO_MODULE) : AngieApplication::getImageUrl('icons/listviews/todolist.png', TODO_MODULE, AngieApplication::INTERFACE_PHONE)
      ));
    } // if
  } // todo_handle_on_project_tabs