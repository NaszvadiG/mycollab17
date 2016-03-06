<?php

  /**
   * MS Project Manager module initialization file
   * 
   * @package activeCollab.modules.ms_project_manager
   */
  
  const MS_PROJECT_MANAGER_MODULE = 'ms_project_manager';
  const MS_PROJECT_EXPORTER_MODULE_PATH = __DIR__;
  
  AngieApplication::setForAutoload(array(
    'MsProjectManagerImporter' => MS_PROJECT_EXPORTER_MODULE_PATH . '/models/MsProjectManagerImporter.class.php',
    'MsProjectManagerExporter' => MS_PROJECT_EXPORTER_MODULE_PATH . '/models/MsProjectManagerExporter.class.php',
  ));
  
  // path for tem files
  defined('MSPROJECT_EXPORT_PATH') or define('MSPROJECT_EXPORT_PATH', WORK_PATH . '/ms_project_export');
  
  if (!defined('MS_PROJECT_EXPORT_WORK_PATH')) {
    define('MS_PROJECT_EXPORT_WORK_PATH', WORK_PATH . '/ms_project_export');
  } // if