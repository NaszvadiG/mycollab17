<?php

  /**
   * Uninstall/Install module
   *
   * Install/Uninstall module
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandModule extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Install/Uninstall module via CLI';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('n:', 'name:', 'Module Name'),
      array('a:', 'action:', 'Action'),
    );

    /**
     * Execute the command
     *
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $module_name = $this->getOption(array('n', 'name'));
      if(empty($module_name)) {
        $output->abortWithMessage('Module Name is required');
      } // if

      $action = $this->getOption(array('a', 'action'));
      if(empty($action)) {
        $output->abortWithMessage('Action value is required');
      } // if

      if (!in_array(strtolower($action), array('install', 'uninstall', 'disable', 'enable'))) {
        $output->abortWithMessage("'{$action}' is not a valid action (expecting 'install', 'uninstall', 'disable', 'enable'");
      } // if

      $module = AngieApplication::getModule($module_name, false);
      if(!$module instanceof AngieModule) {
        $output->abortWithMessage('Module ' . $module_name . ' must be instance of AngieModule');
      } //if

      switch (strtolower($action)) {
        case 'install':
          if(!$module->isInstalled()) {
            $module->install();
            $action_name = "Installed";
          } //if
          break;
        case 'uninstall':
          if($module->isInstalled()) {
            $module->uninstall();
            $action_name = "Uninstalled";
          } //if
          break;
        case 'disable':
          if($module->isEnabled()) {
            $module->disable();
            $action_name = "Disabled";
          } //if
          break;
        case 'enable':
          if(!$module->isEnabled()) {
            $module->enable();
            $action_name = "Enabled";
          } //if
          break;
      } //switch

      if($action_name) {
        $output->printMessage('Module ' . $module_name . ' ' . $action_name);
      } else {
        $output->printMessage('No action performed');
      } //if

    } // execute

  } // CLICommandIpn
