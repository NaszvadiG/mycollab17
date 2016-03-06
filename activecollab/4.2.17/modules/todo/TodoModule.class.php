<?php

  // Include application specific module base
  require_once APPLICATION_PATH . '/resources/ActiveCollabProjectSectionModule.class.php';

  /**
   * Todo module definition
   *
   * @package activeCollab.modules.todo
   * @subpackage models
   */
  class TodoModule extends ActiveCollabProjectSectionModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'todo';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';
    
    /**
     * Name of the project object class (or classes) that this module uses
     *
     * @var string
     */
    protected $project_object_classes = 'TodoList';

    /**
     * Name of category class used by this section
     *
     * @var string
     */
    protected $category_class = 'TodoListCategory';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('project_todo_lists', 'projects/:project_slug/todo', array('controller' => 'todo_lists', 'action' => 'index'));
      Router::map('project_todo_lists_archive', 'projects/:project_slug/todo/archive', array('controller' => 'todo_lists', 'action' => 'archive'));
	  	Router::map('project_todo_lists_mass_edit', 'projects/:project_slug/todo/mass-edit', array('controller' => 'todo_lists', 'action' => 'mass_edit'));
	  	Router::map('project_todo_lists_reorder', 'projects/:project_slug/todo/reorder', array('controller' => 'todo_lists', 'action' => 'reorder'));
			
      Router::map('project_todo_lists_add', 'projects/:project_slug/todo/add', array('controller' => 'todo_lists', 'action' => 'add'));
      
      // Single todo list
      Router::map('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', array('controller' => 'todo_lists', 'action' => 'view'), array('todo_list_id' => Router::MATCH_ID));
      Router::map('project_todo_list_edit', 'projects/:project_slug/todo/:todo_list_id/edit', array('controller' => 'todo_lists', 'action' => 'edit'), array('todo_list_id' => Router::MATCH_ID));
      
      AngieApplication::getModule('categories')->defineCategoriesRoutesFor('project_todo_list', 'projects/:project_slug/todo', 'todo_lists', TODO_MODULE);
      AngieApplication::getModule('categories')->defineCategoryRoutesFor('project_todo_list', 'projects/:project_slug/todo', 'todo_lists', TODO_MODULE);
      AngieApplication::getModule('environment')->defineStateRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      AngieApplication::getModule('complete')->defineChangeStatusRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      AngieApplication::getModule('subtasks')->defineSubtasksRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      AngieApplication::getModule('reminders')->defineRemindersRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      AngieApplication::getModule('system')->defineMoveToProjectRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      AngieApplication::getModule('subscriptions')->defineSubscriptionRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));

      // Project todo-list footprints
      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      } // if
      
      // Milestone todo lists
      Router::map('milestone_todo_lists', 'projects/:project_slug/milestones/:milestone_id/todo-lists', array('controller' => 'milestone_todo_lists', 'action' => 'index'), array('milestone_id' => Router::MATCH_ID));
      
      // Subtasks
      AngieApplication::getModule('subtasks')->defineSubtasksRoutesFor('project_todo_list', 'projects/:project_slug/todo/:todo_list_id', 'todo_lists', TODO_MODULE, array('todo_list_id' => Router::MATCH_ID));
      
      Router::map('activity_logs_admin_rebuild_todo', 'admin/indices/activity-logs/rebuild/todo', array('controller' => 'activity_logs_admin', 'action' => 'rebuild_todo'));
      Router::map('object_contexts_admin_rebuild_todo_lists', 'admin/indices/object-contexts/rebuild/todo', array('controller' => 'object_contexts_admin', 'action' => 'rebuild_todo_lists'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_project_tabs', 'on_project_tabs');
      EventsManager::listen('on_available_project_tabs', 'on_available_project_tabs');
      EventsManager::listen('on_master_categories', 'on_master_categories');
      EventsManager::listen('on_milestone_sections', 'on_milestone_sections');
      EventsManager::listen('on_object_inserted', 'on_object_inserted');
      EventsManager::listen('on_project_export', 'on_project_export');
      EventsManager::listen('on_project_permissions', 'on_project_permissions');
      EventsManager::listen('on_get_completable_project_object_types', 'on_get_completable_project_object_types');
      EventsManager::listen('on_quick_add', 'on_quick_add');
      EventsManager::listen('on_build_project_search_index', 'on_build_project_search_index');
      EventsManager::listen('on_build_names_search_index_for_project', 'on_build_names_search_index_for_project');
      EventsManager::listen('on_project_subcontext_permission', 'on_project_subcontext_permission');
      EventsManager::listen('on_rebuild_activity_log_actions', 'on_rebuild_activity_log_actions');
      EventsManager::listen('on_rebuild_object_contexts_actions', 'on_rebuild_object_contexts_actions');
    } // defineHandlers
    
    // ---------------------------------------------------
    //  Enable / Disable
    // ---------------------------------------------------
    
    /**
     * This module can't be disabled
     *
     * @param User $user
     * @return boolean
     */
    function canDisable(User $user) {
      return false;
    } // canDisable
    
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('Todo Lists');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Adds todo lists to projects');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated and all todo lists from all projects will be deleted');
    } // getUninstallMessage

    /**
     * Return object types (class names) that this module is working with
     *
     * @return array
     */
    function getObjectTypes() {
      return array('TodoList');
    } // getObjectTypes
    
  }