<?php

  /**
   * Microsoft project importer/exporter module definition
   *
   * @package activeCollab.modules.ms_project_manager
   * @subpackage models
   */
  class MsProjectManagerModule extends AngieModule {
    
    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'ms_project_manager';
    
    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';
    
    // ---------------------------------------------------
    //  Events and Routes
    // ---------------------------------------------------
    
    /**
     * Define module routes
     */
    function defineRoutes() {
    	Router::map('ms_project_manager', 'projects/:project_slug/ms_project_manager', array('controller' => 'ms_project_manager', 'action' => 'index'));
      Router::map('ms_project_manager_upload', 'projects/:project_slug/ms_project_manager/upload', array('controller' => 'ms_project_manager', 'action' => 'upload'));
    	Router::map('ms_project_manager_import', 'projects/:project_slug/ms_project_manager/import', array('controller' => 'ms_project_manager', 'action' => 'import'));
    	Router::map('ms_project_manager_download', 'projects/:project_slug/ms_project_manager/download', array('controller' => 'ms_project_manager', 'action' => 'download'));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_object_options', 'on_object_options');
    } // defineHandlers
        
    // ---------------------------------------------------
    //  Names
    // ---------------------------------------------------
    
    /**
     * Get module display name
     *
     * @return string
     */
    function getDisplayName() {
      return lang('MS Project Manager');
    } // getDisplayName
    
    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Import or export MS Project XML file');
    } // getDescription
    
    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Files created with this module will not be deleted');
    } // getUninstallMessage
    
  }