<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Password policy administration controller
   *
   * @package activeCollab.modules.password_policy
   * @subpackage controllers
   */
  class PasswordPolicyAdminController extends AdminController {

    /**
     * Display and process password policy settings form
     */
    function index() {
      if($this->request->isAsyncCall()) {
        $password_policy_data = $this->request->post('password_policy', ConfigOptions::getValue(array(
          'password_policy_min_length',
          'password_policy_require_numbers',
          'password_policy_require_mixed_case',
          'password_policy_require_symbols',
          'password_policy_auto_expire',
        )));

        $this->smarty->assign('password_policy_data', $password_policy_data);

        if($this->request->isSubmitted()) {
          try {
            DB::beginWork('Updating password policy settings @ ' . __CLASS__);

            $auto_expire = (integer) array_var($password_policy_data, 'password_policy_auto_expire');

            ConfigOptions::setValue(array(
              'password_policy_min_length' => (integer) array_var($password_policy_data, 'password_policy_min_length'),
              'password_policy_require_numbers' => (boolean) array_var($password_policy_data, 'password_policy_require_numbers'),
              'password_policy_require_mixed_case' => (boolean) array_var($password_policy_data, 'password_policy_require_mixed_case'),
              'password_policy_require_symbols' => (boolean) array_var($password_policy_data, 'password_policy_require_symbols'),
              'password_policy_auto_expire' => $auto_expire,
            ));

            // Make sure that auto-expiry is updated to existing accounts
            Users::autoExpiryUpdated($auto_expire);

            // Expire all user passwords
            if($this->request->post('expire_passwords')) {
              Users::expirePasswords($this->logged_user->getId());
            } // if

            DB::commit('Password policy settings updated @ ' . __CLASS__);

            $this->response->ok();
          } catch(Exception $e) {
            $this->response->exception($e);
          } // try
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // index

  }