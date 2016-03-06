<?php

  /**
   * Password policy module definition
   *
   * @package activeCollab.modules.password_policy
   */
  class PasswordPolicyModule extends AngieModule {

    /**
     * Plain module name
     *
     * @var string
     */
    protected $name = 'password_policy';

    /**
     * Module version
     *
     * @var string
     */
    protected $version = '4.0';

    /**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('password_policy_admin', 'admin/password-policy', array('controller' => 'password_policy_admin'));
    } // defineRoutes

    /**
     * Define module event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_admin_panel', 'on_admin_panel');
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
      return lang('Password Policy');
    } // getDisplayName

    /**
     * Return module description
     *
     * @return string
     */
    function getDescription() {
      return lang('Set rules that passwords need to meet in order for system to accept them. Also set password expiry interval, or invalidate all passwords with one click');
    } // getDescription

    /**
     * Return module uninstallation message
     *
     * @return string
     */
    function getUninstallMessage() {
      return lang('Module will be deactivated. Users will be able to use any password from this point on');
    } // getUninstallMessage

  }