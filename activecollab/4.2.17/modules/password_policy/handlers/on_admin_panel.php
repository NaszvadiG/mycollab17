<?php

  /**
   * on_admin_panel event handlers
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_admin_panel event
   * 
   * @param AdminPanel $admin_panel
   */
  function password_policy_handle_on_admin_panel(AdminPanel &$admin_panel) {
    $admin_panel->addToTools('password_policy_admin', lang('Password Policy'), Router::assemble('password_policy_admin'), AngieApplication::getImageUrl('admin_panel/password-policy-settings.png', PASSWORD_POLICY_MODULE), array(
      'onclick' => new FlyoutFormCallback(array(
        'success_event' => 'password_policy_settings_updated',
        'success_message' => lang('Settings updated'),
      )),
    ));
  } // password_policy_handle_on_admin_panel