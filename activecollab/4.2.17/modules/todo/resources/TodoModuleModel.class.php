<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * To do module model
   * 
   * @package activeCollab.modules.todo
   * @subpackage resources
   */
  class TodoModuleModel extends ActiveCollabModuleModel {
  
    /**
     * Load initial module data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('todo_list_categories', array('General'));
      
      parent::loadInitialData($environment);
    } // loadInitialData
    
  }