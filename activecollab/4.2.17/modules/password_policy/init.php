<?php

  /**
   * Init password policy module
   *
   * @package activeCollab.modules.password_policy
   */
  
  define('PASSWORD_POLICY_MODULE', 'password_policy');
  define('PASSWORD_POLICY_MODULE_PATH', APPLICATION_PATH . '/modules/password_policy');

  AngieApplication::setForAutoload(array(
    'ConfigurablePasswordPolicy' => PASSWORD_POLICY_MODULE_PATH . '/models/ConfigurablePasswordPolicy.class.php',
  ));