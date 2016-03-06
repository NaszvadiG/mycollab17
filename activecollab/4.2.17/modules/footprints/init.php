<?php

  /**
   * Init footprints module
   *
   * @package activeCollab.modules.footprints
   */
  
  define('FOOTPRINTS_MODULE', 'footprints');
  define('FOOTPRINTS_MODULE_PATH', APPLICATION_PATH . '/modules/footprints');

  AngieApplication::setForAutoload(array(
	  'UsersSecurity' => FOOTPRINTS_MODULE_PATH . '/models/users_security/UsersSecurity.class.php',
	  'FootprintAccessLogs' => FOOTPRINTS_MODULE_PATH . '/models/footprint_access_logs/FootprintAccessLogs.class.php',
	  'NoOfDownloadsInspectorProperty' => FOOTPRINTS_MODULE_PATH .'/models/NoOfDownloadsInspectorProperty.class.php',
  ));