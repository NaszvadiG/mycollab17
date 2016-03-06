<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Class description
   *
   * @package
   * @subpackage
   */
  class PasswordPolicyModuleModel extends ActiveCollabModuleModel {

    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('password_policy_min_length', 0);
      $this->addConfigOption('password_policy_require_numbers', false);
      $this->addConfigOption('password_policy_require_mixed_case', false);
      $this->addConfigOption('password_policy_require_symbols', false);
      $this->addConfigOption('password_policy_auto_expire');

      parent::loadInitialData($environment);
    } // loadInitialData

  }