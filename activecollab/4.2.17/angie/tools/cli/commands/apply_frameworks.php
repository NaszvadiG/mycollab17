<?php

  /**
   * Apply framework resources (controllers, notifications, proxies etc) to the system module
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandApplyFrameworks extends CLICommandGenerator {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Apply framework resources to the system module';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('f', 'force', 'Force new files'),
      array('t:', 'target:', 'Name of the target module (system is default)'),
    );

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $force = $this->getOption(array('f', 'force'), false);
      $target_module = $this->getOption(array('t', 'target'));

      if(empty($target_module)) {
        $target_module = 'system';
      } // if

      if($force) {
        if(!$output->ask("Are you sure that you want to forcefully apply framework resources to the '$target_module' module?")) {
          $output->abortWithMessage("Operation aborted");
        } // if
      } // if

      $notifications_map = array();

      foreach(AngieApplication::getFrameworks() as $framework) {
        $this->applyFrameworkControllers($framework, $target_module, $output, $force);
        $this->applyFrameworkNotifications($framework, $target_module, $notifications_map, $output, $force);
        $this->applyFrameworkProxies($framework, $target_module, $output, $force);
      } // foreach

      if(count($notifications_map)) {
        $target = APPLICATION_PATH . "/modules/$target_module/resources/autoload_notifications.php";
        $autoload_notifications = array();

        foreach($notifications_map as $notification_class) {
          $autoload_notifications[] = "    '{$notification_class}' => " . strtoupper($target_module) . "_MODULE_PATH . '/notifications/{$notification_class}.class.php', ";
        } // if

        $this->createFromTemplate($target, 'autoload_notifications', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
          'MODULE_NAME' => $target_module,
          'AUTOLOAD_CLASSES' => implode("\n", $autoload_notifications)
        ), $output, true);
      } // if
    } // execute

  }