<?php

  /**
   * Configuration file
   */
  define('ROOT', '/Build/Workspaces/QA/activeCollab/activecollab');
  define('ANGIE_PATH', '/Build/Workspaces/QA/angie');


  define('DB_HOST', 'localhost');
  define('DB_USER', 'ac');
  define('DB_PASS', 'ac');
  define('DB_NAME', 'ac');
  define('DB_CAN_TRANSACT', true);
  define('DB_CHARSET', 'utf8');
  define('TABLE_PREFIX', 'ac_');
  
  // Application URL
  define('ROOT_URL', 'http://ac.com/public');
  define('ADMIN_EMAIL', 'mail@mail.com');
  define('APPLICATION_MODE', 'in_development');
  
  define('USE_UNPACKED_FILES', true);
  define('APPLICATION_MODULES', 'system,discussions,milestones,files,calendar,tasks,project_exporter,status,documents,tracking,invoicing,notebooks');

  require_once dirname(__FILE__) . '/version.php';
  require_once dirname(__FILE__) . '/license.php';
  require_once dirname(__FILE__) . '/defaults.php';

