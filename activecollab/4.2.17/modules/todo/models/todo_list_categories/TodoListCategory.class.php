<?php

  /**
   * Todo list category impelementation
   *
   * @package activeCollab.modules.todo
   * @subpackage models
   */
  class TodoListCategory extends ProjectObjectCategory {
    
    /**
     * Return todo lists posted in this category
     *
     * @param IUser $user
     * @return DBResult
     */
    function getItems(IUser $user) {
      return TodoLists::findByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // getItems
    
    /**
     * Return number of items in this category
     * 
     * @param IUser $user
     * @return integer
     */
    function countItems(IUser $user) {
      return TodoLists::countByCategory($this, STATE_VISIBLE, $user->getMinVisibility());
    } // countItems
    
    /**
     * Routing context name
     *
     * @var string
     */
    private $routing_context = false;
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'project_todo_list_category';
    } // getRoutingContext
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can delete this category
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($user instanceof User) {
        return parent::canDelete($user) || TodoLists::canManage($user, $this->getParent());
      } else {
        return false;
      } // if
    } // canDelete
    
  }