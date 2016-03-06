<?php

  // Foundation...
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Todo lists controller
   *
   * @package activeCollab.modules.todo
   * @subpackage controllers
   */
  class TodoListsController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = TODO_MODULE;
    
    /**
     * Selected todo list
     *
     * @var TodoList
     */
    protected $active_todo_list;
    
    /**
     * Complete controller delegate
     *
     * @var CompleteController
     */
    protected $complete_delegate;
    
    /**
     * Object state delegate
     *
     * @var StateController
     */
    protected $state_delegate;
    
    /**
     * Subtasks delegate instance
     *
     * @var SubtasksController
     */
    protected $subtasks_delegate;
    
    /**
     * Categories delegate instance
     *
     * @var CategoriesController
     */
    protected $categories_delegate;
    
    /**
     * Move to project delegate controller
     *
     * @var MoveToProjectController
     */
    protected $move_to_project_delegate;

    /**
     * Reminders controller instance
     *
     * @var RemindersController
     */
    protected $reminders_delegate;

    /**
     * Subscriptions controller delegate
     *
     * @var SubscriptionsController
     */
    protected $subscriptions_delegate;
    
    /**
     * Array of actions available through API
     *
     * @var array
     */
    protected $api_actions = array('index', 'view', 'add', 'edit');
    
    /**
     * Construct todo list controller instance
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'todo_lists') {
      	$this->complete_delegate = $this->__delegate('complete', COMPLETE_FRAMEWORK_INJECT_INTO, 'project_todo_list');
        $this->state_delegate = $this->__delegate('state', ENVIRONMENT_FRAMEWORK_INJECT_INTO, 'project_todo_list'); 
        $this->subtasks_delegate = $this->__delegate('subtasks', SUBTASKS_FRAMEWORK_INJECT_INTO, 'project_todo_list'); 
        $this->categories_delegate = $this->__delegate('categories', CATEGORIES_FRAMEWORK_INJECT_INTO, 'project_todo_list');
        $this->move_to_project_delegate = $this->__delegate('move_to_project', SYSTEM_MODULE, 'project_todo_list');
        $this->reminders_delegate = $this->__delegate('reminders', REMINDERS_FRAMEWORK_INJECT_INTO, 'project_todo_list');
        $this->subscriptions_delegate = $this->__delegate('subscriptions', SUBSCRIPTIONS_FRAMEWORK_INJECT_INTO, 'project_todo_list');
      } // if
    } // __construct
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!TodoLists::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $todo_lists_url = Router::assemble('project_todo_lists', array('project_slug' => $this->active_project->getSlug()));
      
      $this->wireframe->tabs->setCurrentTab('todo_lists');
      $this->wireframe->breadcrumbs->add('project_todo_lists', lang('Todo Lists'), $todo_lists_url);
      
      $todo_list_id = $this->request->getId('todo_list_id');
      if($todo_list_id) {
        $this->active_todo_list = ProjectObjects::findById($todo_list_id);
      } // if
      
      if($this->active_todo_list instanceof TodoList) {
        if (!$this->active_todo_list->isAccessible()) {
          $this->response->notFound();
        } // if

        if ($this->active_todo_list->getState() == STATE_ARCHIVED) {
          $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_todo_lists_archive', array('project_slug' => $this->active_project->getSlug())));
        } // if
        $this->wireframe->breadcrumbs->add('project_todo_list', $this->active_todo_list->getName(), $this->active_todo_list->getViewUrl());
      } else {
        $this->active_todo_list = new TodoList();
        $this->active_todo_list->setProject($this->active_project);
      } // if
      
      $this->smarty->assign(array(
        'active_todo_list' => $this->active_todo_list,
        'todo_lists_url' => $todo_lists_url
      ));

      // Page actions
      if(($this->request->isWebBrowser() || $this->request->isMobileDevice()) && in_array($this->request->getAction(), array('index', 'view'))) {
      	if(TodoLists::canAdd($this->logged_user, $this->active_project)) {
	        $this->wireframe->actions->add('new_todo_list', lang('New Todo List'), Router::assemble('project_todo_lists_add', array('project_slug' => $this->active_project->getSlug())), array(
	        	'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
	        	'onclick' => new FlyoutFormCallback('todo_list_created'),
	        	'primary' => true
	        ));
	      } // if
      } // if
      
      if($this->complete_delegate instanceof CompleteController) {
        $this->complete_delegate->__setProperties(array(
          'active_object' => &$this->active_todo_list
        ));
      } // if
      
      if($this->state_delegate instanceof StateController) {
        $this->state_delegate->__setProperties(array(
          'active_object' => &$this->active_todo_list, 
        ));
      } // if
      
      if($this->subtasks_delegate instanceof SubtasksController) {
        $this->subtasks_delegate->__setProperties(array(
          'active_object' => $this->active_todo_list, 
        ));
      } // if
      
      if($this->categories_delegate instanceof CategoriesController) {
        $this->categories_delegate->__setProperties(array(
          'categories_context' => &$this->active_project, 
          'routing_context' => 'project_todo_list', 
          'routing_context_params' => array('project_slug' => $this->active_project->getSlug()), 
          'category_class' => 'TodoListCategory',
          'active_object' => &$this->active_todo_list
        ));
      } // if
      
      if($this->move_to_project_delegate instanceof MoveToProjectController) {
      	$this->move_to_project_delegate->__setProperties(array(
          'active_project' => &$this->active_project,
          'active_object' => &$this->active_todo_list,
        ));
      } // if

      if ($this->reminders_delegate instanceof RemindersController) {
        $this->reminders_delegate->__setProperties(array(
          'active_object' => &$this->active_todo_list,
        ));
      } // if

      if($this->subscriptions_delegate instanceof SubscriptionsController) {
        $this->subscriptions_delegate->__setProperties(array(
          'active_object' => &$this->active_todo_list,
        ));
      } // if
    } // __before
    
    /**
     * Show todo lists page
     */
    function index() {
    	
    	// Server request made with mobile device
      if($this->request->isMobileDevice()) {	      
	      $this->response->assign(array(
          'todo_lists' => DB::execute("SELECT id, name, category_id, milestone_id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'TodoList' AND project_id = ? AND completed_on IS NULL AND state >= ? AND visibility >= ? ORDER BY position, created_on", $this->active_project->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility()),
        	'todo_list_url' => Router::assemble('project_todo_list', array('project_slug' => $this->active_project->getSlug(), 'todo_list_id' => '--TODOLISTID--')), 
        ));
      } // if
    	
      // API call
      if($this->request->isApiCall()) {
        $this->response->respondWithData(TodoLists::findByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()), array(
          'as' => 'todo_lists', 
        ));
        
      // Tablet device
      } elseif($this->request->isTablet()) {
      	throw new NotImplementedError(__METHOD__);
      
      // Regular web browser request
      } elseif($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        
        $this->smarty->assign(array(
        	'todo_lists' => TodoLists::findForObjectsList($this->active_project, $this->logged_user),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'TodoListCategory'),
          'can_add_todo_list' => TodoLists::canAdd($this->logged_user, $this->active_project),
        	'can_manage_todo_lists' => TodoLists::canManage($this->logged_user, $this->active_project),
          'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'TodoListCategory') ? Router::assemble('project_todo_list_categories', array('project_slug' => $this->active_project->getSlug())) : null,
          'in_archive' => false,
          'print_url' => Router::assemble('project_todo_lists', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));
        
        // mass manager
        if (Todolists::canManage($this->logged_user, $this->active_project)) {
        	$mass_manager = new MassManager($this->logged_user, $this->active_todo_list);        	
        	$this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if

      // Print interface
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', ''));
        $filter_by = $this->request->get('filter_by', 'active');
        
        // page title
        $filter_by_completion = array_var($filter_by, 'is_completed', null); 
        if ($filter_by_completion === '0') {
        	$page_title = lang('Open To Do Lists in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else if ($filter_by_completion === '1') {
					$page_title = lang('Archived To Do Lists in :project_name Project', array('project_name' => $this->active_project->getName()));
        } else {
        	$page_title = lang('Completed To Do Lists in :project_name Project', array('project_name' => $this->active_project->getName()));
        } // if
        
        // maps
        $map = array();
        
        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');
            
          	$getter = 'getMilestoneId';
          	$page_title.= ' ' . lang('Grouped by Milestone'); 
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'TodoListCategory');
            $map[0] = lang('Uncategorized');
            
          	$getter = 'getCategoryId';
          	$page_title.= ' ' . lang('Grouped by Category');
            break;
         
        }//switch
        
        $todo_lists = TodoLists::findForPrint($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility(), $group_by, $filter_by);
     
        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map, $todo_lists, $getter, false);
        
        $this->response->assignByRef('todo_lists', $print_list);
        $this->response->assignByRef('map', $map);
        
        $this->response->assign(array(
			'page_title' => $page_title,
        	'getter' => $getter
        ));
       
      } // if
    } // index
    
    /**
     * Show archived todo lists
     */
    function archive() {
      if($this->request->isMobileDevice()) {
        $this->response->assign('todo_lists', TodoLists::findCompletedByProject($this->active_project, STATE_VISIBLE, $this->logged_user->getMinVisibility()));
      } else if ($this->request->isWebBrowser()) {
        $this->wireframe->list_mode->enable();
        $this->wireframe->breadcrumbs->add('archive', lang('Archive'), Router::assemble('project_todo_lists_archive', array('project_slug' => $this->active_project->getSlug())));

        $this->smarty->assign(array(
          'todo_lists' => TodoLists::findForObjectsList($this->active_project, $this->logged_user, STATE_ARCHIVED),
          'milestones' => Milestones::getIdNameMap($this->active_project),
          'categories' => Categories::getidNameMap($this->active_project, 'TodoListCategory'),
          'can_add_todo_list' => TodoLists::canAdd($this->logged_user, $this->active_project),
          'can_manage_todo_lists' => TodoLists::canManage($this->logged_user, $this->active_project),
          'manage_categories_url' => $this->active_project->availableCategories()->canManage($this->logged_user, 'TodoListCategory') ? Router::assemble('project_todo_list_categories', array('project_slug' => $this->active_project->getSlug())) : null,
          'in_archive' => true,
          'print_url' => Router::assemble('project_todo_lists_archive', array('print' => 1, 'project_slug' => $this->active_project->getSlug()))
        ));

        // mass manager
        if (Todolists::canManage($this->logged_user, $this->active_project)) {
          $this->active_todo_list->setState(STATE_ARCHIVED);
          $mass_manager = new MassManager($this->logged_user, $this->active_todo_list);
          $this->response->assign('mass_manager', $mass_manager->describe($this->logged_user));
        } // if
      // Print interface
      } else if ($this->request->isPrintCall()) {
        $group_by = strtolower($this->request->get('group_by', ''));

        $page_title = lang('Archived To Do Lists in :project_name Project', array('project_name' => $this->active_project->getName()));

        // maps
        $map = array();

        switch ($group_by) {
          case 'milestone_id':
            $map = Milestones::getIdNameMap($this->active_project);
            $map[0] = lang('Unknown Milestone');

            $getter = 'getMilestoneId';
            $page_title.= ' ' . lang('Grouped by Milestone');
            break;
          case 'category_id':
            $map = Categories::getidNameMap($this->active_project, 'TodoListCategory');
            $map[0] = lang('Uncategorized');

            $getter = 'getCategoryId';
            $page_title.= ' ' . lang('Grouped by Category');
            break;

        }//switch

        $todo_lists = TodoLists::findForPrint($this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $group_by);

        //use thisa to sort objects by map array
        $print_list = group_by_mapped($map,$todo_lists,$getter);

        $this->response->assignByRef('todo_lists', $print_list);
        $this->response->assignByRef('map', $map);

        $this->response->assign(array(
          'page_title' => $page_title,
          'getter' => $getter
        ));

      } else {
        $this->response->badRequest();
      } // if
    } // archive
    
    /**
     * Mass Edit action
     */
    function mass_edit() {
			if ($this->getControllerName() == 'todo_lists') {
    		$this->mass_edit_objects = TodoLists::findByIds($this->request->post('selected_item_ids'), STATE_ARCHIVED, $this->logged_user->getMinVisibility());
    	} // if
    	parent::mass_edit();
    } // mass_edit
    
    /**
     * View specific todo list
     */
    function view() {
      if ($this->active_todo_list->isLoaded()) {
        if ($this->active_todo_list->canView($this->logged_user)) {
          $this->wireframe->setPageObject($this->active_todo_list, $this->logged_user);

          if ($this->request->isApiCall()) {
            $this->response->respondWithData($this->active_todo_list, array(
              'as' => 'todo_list',
              'detailed' => true,
            ));
          // Regular web browser request
          } elseif($this->request->isWebBrowser()) {
            $this->wireframe->print->enable();

            if($this->request->isSingleCall() || $this->request->isQuickViewCall()) {
              $this->render();
            } else {
              if ($this->active_todo_list->getState() == STATE_ARCHIVED) {
                $this->__forward('archive', 'archive');
              } else {
                $this->__forward('index', 'index');
              } // if
            } // if

          // Phone device request
          } elseif($this->request->isPhone()) {
            if($this->active_todo_list->subtasks()->canAdd($this->logged_user)) {
              $this->wireframe->actions->beginWith('new_subtask', lang('New Subtask'), $this->active_todo_list->subtasks()->getAddUrl(), array(
                'icon' => AngieApplication::getImageUrl('icons/navbar/add.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
              ));
            } // if

            $this->wireframe->actions->remove(array('pin_unpin', 'favorites_toggler'));
          } // if

          if($this->request->isPageCall()) {
            if($this->request->isSingleCall()) {
              $this->active_todo_list->accessLog()->log($this->logged_user);
            } // if

            $this->wireframe->print->enable();
            $this->smarty->assign('show_only_tasks', $this->request->get('show_only_tasks'));
          } // if
          
        } else {
          $this->response->forbidden();
        }
      } else {
        $this->response->notFound();
      }
    } // view
    
    /**
     * Create a new todo list
     */
    function add() {
      if($this->request->isApiCall() || $this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        if(!TodoLists::canAdd($this->logged_user, $this->active_project)) {
        	$this->response->forbidden();
        } // if
          
        $todo_list_data = $this->request->post('todo_list');
        if(!is_array($todo_list_data)) {
          $todo_list_data = array(
            'milestone_id' => $this->request->get('milestone_id'),
            'visibility' => $this->active_project->getDefaultVisibility(),
          );
        } // if
        $this->smarty->assign(array(
        	'add_todo_list_url' => Router::assemble('project_todo_lists_add', array('project_slug' => $this->active_project->getSlug())),
        	'todo_list_data' => $todo_list_data
        ));
        
        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Creating a todo list @ ' . __CLASS__);
            
            $this->active_todo_list = new TodoList();
          
            $this->active_todo_list->setAttributes($todo_list_data);
            $this->active_todo_list->setProjectId($this->active_project->getId());
            $this->active_todo_list->setState(STATE_VISIBLE);
            
            $this->active_todo_list->save();
            
            DB::commit('Todo list created @ ' . __CLASS__);

            $this->active_todo_list->subscriptions()->set(array_unique(array_merge(
              (array) $this->logged_user->getId(),
              (array) $this->active_project->getLeaderId(),
              (array) $this->request->post('notify_users')
            )), false);

            AngieApplication::notifications()
              ->notifyAbout('todo/new_todo_list', $this->active_todo_list, $this->logged_user)
              ->sendToSubscribers();

            if ($this->request->isPageCall()) {
              $this->flash->success('Todo list ":name" has been created', array('name' => $this->active_todo_list->getName()));
              $this->response->redirectToUrl($this->active_todo_list->getViewUrl());
            } else {
            	$this->response->respondWithData($this->active_todo_list, array(
            	  'as' => 'todo_list', 
            	  'detailed' => true, 
            	));
            } //if
          } catch(Exception $e) {
            DB::rollback('Failed to created todo list @ ' . __CLASS__);
						$this->response->exception($e);
          } // try
        } else {
          if($this->request->isApiCall()) {
            $this->response->badRequest();
          } // if
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add
    
    /**
     * Edit selected todo list
     */
    function edit() {
      $this->wireframe->hidePrintButton();
      
      if($this->request->isApiCall() && !$this->request->isSubmitted()) {
        $this->response->badRequest();
      } // if
      
      if($this->active_todo_list->isNew()) {
        $this->response->notFound();
      } // if
      
      if(!$this->active_todo_list->canEdit($this->logged_user)) {
      	$this->response->forbidden();
      } // if
                  
      $todo_list_data = $this->request->post('todo_list');
      if(!is_array($todo_list_data)) {
        $todo_list_data = array(
          'name' => $this->active_todo_list->getName(),
          'body' => $this->active_todo_list->getBody(),
          'visibility' => $this->active_todo_list->getVisibility(),
          'milestone_id' => $this->active_todo_list->getMilestoneId(),
          'category_id' => $this->active_todo_list->getCategoryId(),
        );
      } // if
      
      $this->smarty->assign('todo_list_data', $todo_list_data);
      
      if($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating todo list @ ' . __CLASS__);

          $this->active_todo_list->setAttributes($todo_list_data);
          
          $this->active_todo_list->save();
          
          DB::commit('Todo list updated @ ' . __CLASS__);
          if ($this->request->isPageCall()) {
	          $this->flash->success('Todo list ":name" has been updated', array('name' => $this->active_todo_list->getName()));
						$this->response->redirectToUrl($this->active_todo_list->getViewUrl());
          } else {
          	$this->response->respondWithData($this->active_todo_list, array(
          	  'as' => 'todo_list', 
          	  'detailed' => true, 
          	));
          } //if
        } catch(Exception $e) {
          DB::rollback('Failed to updated todo list @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } // if
    } // edit
    
    /**
     * Reorder tasks
     */
    function reorder() {
    	if (!$this->request->isSubmitted() || !$this->request->isAsyncCall()) {
    		$this->response->badRequest();
    	} // if
    	
    	$object_ids = $this->request->post('object_ids');
    	if (!is_foreachable($object_ids)) {
    		$this->response->badRequest();
    	} // if
    	
    	try {
	    	$counter = 0;
	    	foreach ($object_ids as $object_id) {
	    		DB::execute('UPDATE ' . TABLE_PREFIX . 'project_objects SET position = ? WHERE id = ? AND type = ?', $counter++, $object_id, 'TodoList');
	    	} // foreach
    		$this->response->ok();
    	} catch (Exception $e) {
    		$this->response->exception($e);
    	} // try
    } // reorder
    
  }