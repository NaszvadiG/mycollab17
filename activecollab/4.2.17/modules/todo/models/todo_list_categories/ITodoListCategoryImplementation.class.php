<?php

  /**
   * Todo list category implementation
   *
   * @package activeCollab.modules.todo
   * @subpackage models
   */
  class ITodoListCategoryImplementation extends IProjectObjectCategoryImplementation {

    /**
     * Name of the add category route
     *
     * @var string
     */
    protected $add_category_route = 'project_todo_list_categories_add';
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory $object) {
      if($object instanceof TodoList) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'TodoList');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return TodoListCategory
     */
    function newCategory() {
    	return new TodoListCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param Category $category
     * @return mixed
     */
    function set($category) {
      if($category) {
        if($category instanceof TodoListCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'TodoListCategory');
        }
      } else {
        $this->object->setCategoryId(null);
      } // if
    } // set

    /**
     * Get category context
     */
    function getCategoryContext() {
			return $this->object->getProject();    	
    } // getCategoryContext
    
  }