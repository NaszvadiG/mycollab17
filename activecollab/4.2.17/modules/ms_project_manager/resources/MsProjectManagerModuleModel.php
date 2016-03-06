<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Ms Project Manager module model definition
   *
   * @package activeCollab.modules.ms_project_manager
   * @subpackage resources
   */
  class MsProjectManagerModuleModel extends ActiveCollabModuleModel {
  
    /**
     * Construct source module model definition
     *
     * @param MsProjectManagerModule $parent
     */
    function __construct(MsProjectManagerModule $parent) {
      parent::__construct($parent);
      
    } //__construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      if (!is_dir(WORK_PATH . '/ms_project_export')) {
        create_dir(WORK_PATH . '/ms_project_export', true);
      } //if

      parent::loadInitialData($environment);
    } // loadInitialData
    
  }