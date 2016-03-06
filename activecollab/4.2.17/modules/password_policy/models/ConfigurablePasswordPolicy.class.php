<?php

  /**
   * Class description
   *
   * @package
   * @subpackage
   */
  class ConfigurablePasswordPolicy extends PasswordPolicy {

    /**
     * Return min password length. If this function returns null, system will not check password length
     *
     * @return mixed
     */
    function getMinLength() {
      return (integer) ConfigOptions::getValue('password_policy_min_length', 15);
    } // getMinLength

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireNumbers() {
      return (boolean) ConfigOptions::getValue('password_policy_require_numbers');
    } // requireNumbers

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireMixedCase() {
      return (boolean) ConfigOptions::getValue('password_policy_require_mixed_case');
    } // requireMixedCase

    /**
     * Returns true if system requires that passwords contain numbers
     *
     * @return bool
     */
    function requireSymbols() {
      return (boolean) ConfigOptions::getValue('password_policy_require_symbols');
    } // requireSymbols

    /**
     * Return number of months that password is valid
     *
     * @return int
     */
    function getAutoExpire() {
      return (integer) ConfigOptions::getValue('password_policy_auto_expire');
    } // getAutoExpire

    /**
     * Returns true if there are rules set
     *
     * @return boolean
     */
    function hasRules() {
      return $this->getMinLength() || $this->requireNumbers() || $this->requireMixedCase() || $this->requireSymbols();
    } // hasRules

    /**
     * Generate a new password
     *
     * @param integer $length
     * @return string
     */
    function generatePassword($length = null) {
      $length = empty($length) ? 20 : (integer) $length;

      if($length < $this->getMinLength()) {
        $length = $this->getMinLength();
      } // if

      $iteration = 1;

      do {
        if($iteration > 100) {
          break; // escape from here, 100 tries is enough
        } else {
          $iteration++;
        } // if

        $password = parent::generatePassword($length);

        $errors = new ValidationErrors();
        $this->validateUserPassword($password, $errors);
      } while($errors->hasErrors());

      return $password;
    } // generatePassword

  }