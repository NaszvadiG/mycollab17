<?php

  /**
   * Todo list record class
   *
   * @package activeCollab.modules.todo
   * @subpackage models
   */
  class TodoList extends ProjectObject implements ISubscriptions, ISubtasks, IComplete, ICategory, ISearchItem, ICanBeFavorite, IReminders {
    
    /**
     * Permission name
     * 
     * @var string
     */
    protected $permission_name = 'todo_list';
    
    /**
     * Define fields used by this project object
     *
     * @var array
     */
    protected $fields = array(
      'id', 
      'type', 
      'module', 
      'project_id', 'milestone_id', 'category_id', 
      'name', 'body', 
      'state', 'original_state', 'visibility', 'original_visibility', 
      'created_on', 'created_by_id', 'created_by_name', 'created_by_email',
      'updated_on', 'updated_by_id', 'updated_by_name', 'updated_by_email', 
      'completed_on', 'completed_by_id', 'completed_by_name', 'completed_by_email', 
      'version', 'position',
    );
    
    /**
     * Construct todo list
     *
     * @param mixed $id
     */
    function __construct($id = null) {
      $this->setModule(TODO_MODULE);
      parent::__construct($id);
    } // __construct
    
    /**
     * Return proper type name in user's language
     *
     * @param boolean $lowercase
     * @param Language $language
     * @return string
     */
    function getVerboseType($lowercase = false, $language = null) {
      return $lowercase ? lang('todo list', null, false, $language) : lang('Todo List', null, null, $language);
    } // getVerboseType
    
    // ---------------------------------------------------
    //  Context
    // ---------------------------------------------------
    
    /**
     * Return object path
     * 
     * @return string
     */
    function getObjectContextPath() {
      return parent::getObjectContextPath() . '/todo/' . ($this->getVisibility() == VISIBILITY_PRIVATE ? 'private' : 'normal') . '/' . $this->getId();
    } // getContextPath
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can create a new ticket in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    static function canAdd($user, $project) {
      return ProjectObject::canAdd($user, $project, 'todo_list');
    } // canAdd
    
    /**
     * Returns true if $user can create a new ticket in $project
     *
     * @param User $user
     * @param Project $project
     * @return boolean
     */
    function canReorder($user, $project) {
      return $user->projects()->getPermission('todo_list', $project) == ProjectRole::PERMISSION_MANAGE;
    } // canReorder
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Subtasks implementation instance for this object
     *
     * @var ISubtasksImplementation
     */
    private $subtasks;
    
    /**
     * Return subtasks implementation for this object
     *
     * @return IProjectObjectSubtasksImplementation
     */
    function subtasks() {
      if(empty($this->subtasks)) {
        $this->subtasks = new IProjectObjectSubtasksImplementation($this);
      } // if
      
      return $this->subtasks;
    } // subtasks
    
    /**
     * Cached complete implementation instance
     *
     * @var IProjectObjectCompleteImplementation
     */
    private $complete = false;
    
    /**
     * Return complete interface implementation
     *
     * @return IProjectObjectCompleteImplementation
     */
    function complete() {
      if($this->complete === false) {
        $this->complete = new IProjectObjectCompleteImplementation($this);
      } // if
      
      return $this->complete;
    } // complete

    /**
     * Subscriptions helper instance
     *
     * @var IProjectObjectSubscriptionsImplementation
     */
    private $subscriptions;

    /**
     * Return subscriptions helper for this object
     *
     * @return IProjectObjectSubscriptionsImplementation
     */
    function &subscriptions() {
      if(empty($this->subscriptions)) {
        $this->subscriptions = new IProjectObjectSubscriptionsImplementation($this);
      } // if

      return $this->subscriptions;
    } // subscriptions
    
    /**
     * Category implementation instance
     *
     * @var ITodoListCategoryImplementation
     */
    private $category = false;
    
    /**
     * Return category implementation
     *
     * @return ITodoListCategoryImplementation
     */
    function category() {
      if($this->category === false) {
        $this->category = new ITodoListCategoryImplementation($this);
      } // if
      
      return $this->category;
    } // category

    /**
     * Reminders helper instance
     *
     * @return IProjectObjectRemindersImplementation
     */
    private $reminders = false;

    /**
     * Return reminders helper for this task
     *
     * @return IProjectObjectRemindersImplementation
     */
    function reminders() {
      if($this->reminders === false) {
        $this->reminders = new IProjectObjectRemindersImplementation($this);
      } // if

      return $this->reminders;
    } // reminders
    
    /**
     * Return history helper instance
     *
     * @return IHistoryImplementation
     */
    function history() {
      return parent::history()->alsoSetRendererClass('TodoListHistoryRenderer');
    } // history
    
    /**
     * Cached search helper instance
     *
     * @var ITodoListSearchItemImplementation
     */
    private $search = false;
    
    /**
     * Return search helper instance
     * 
     * @return ITodoListSearchItemImplementation
     */
    function &search() {
      if($this->search === false) {
        $this->search = new ITodoListSearchItemImplementation($this);
      } // if
      
      return $this->search;
    } // search
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if(!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Todo list summary is required'), 'name');
      } // if
      
      parent::validate($errors, true);
    } // validate
  
  }