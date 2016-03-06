<?php

  /**
   * New project command
   *
   * @package angie.tools.cli_commands
   */
  class CLICommandNewProject extends CLICommandGenerator {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Initialize project structure';

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      $cwd = getcwd();

      $short_project_name = $this->getArgument(1);

      if(empty($short_project_name)) {
        $short_project_name = basename($cwd);
      } // if

      $project_name = Inflector::camelize($short_project_name);
      $project_url = "http://$short_project_name.dev";
      $module_signature = make_string(40);
      $unique_key = sha1(make_string(15));

      $output->printMessage("Preparing environment for '$project_name' (short name '$short_project_name')");

      $this->createDir("$cwd/$short_project_name", $output);
      $this->createDir("$cwd/$short_project_name/current", $output);
      $this->createFromTemplate("$cwd/$short_project_name/current/$project_name.class.php", 'application_class', array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'UNIQUE_KEY' => $unique_key,
        'MODULE_SIGNATURE' => $module_signature,
      ), $output);

      $this->createDir("$cwd/$short_project_name/current/modules", $output);
      $this->createDir("$cwd/$short_project_name/current/resources", $output);
      $this->createFromTemplate("$cwd/$short_project_name/current/resources/defaults.php", 'defaults_file', array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'UNIQUE_KEY' => $unique_key,
      ), $output);

      $this->createDir("$cwd/$short_project_name/current/upgrade", $output);

      // Create system module
      $this->createModule('system', '1.0', $module_signature, "$cwd/$short_project_name/current/modules", $project_name, $short_project_name, $output);

      // Initialize instance
      $this->createDir("$cwd/instance", $output);

      $this->createDir("$cwd/instance/cache", $output);
      $this->htaccessDenyAccess("$cwd/instance/cache", $output);
      $this->gitIgnore("$cwd/instance/cache", array('*'), $output);

      $this->createDir("$cwd/instance/compile", $output);
      $this->htaccessDenyAccess("$cwd/instance/compile", $output);
      $this->gitIgnore("$cwd/instance/compile", array('*'), $output);

      // Configuration file
      $this->createDir("$cwd/instance/config", $output);
      $this->htaccessDenyAccess("$cwd/instance/config", $output);
      $this->gitIgnore("$cwd/instance/config", array('config.php', 'license.php', 'config.test.php'), $output);
      $this->createFromTemplate("$cwd/instance/config/config.empty.php", 'empty_config_file', array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'APPLICATION_URL' => $project_url,
      ), $output);

      $this->createFromTemplate("$cwd/instance/config/config.php", 'config_file', array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'APPLICATION_URL' => $project_url,
        'APPLICATION_FILES_PATH' => "$cwd/$short_project_name",
        'ANGIE_PATH' => ANGIE_PATH,
      ), $output);

      $this->createFromTemplate("$cwd/instance/config/defaults.php", 'include_defaults_files', array(
        'APPLICATION_NAME' => $project_name,
      ), $output);

      $this->createFromTemplate("$cwd/instance/config/version.php", 'version_file', array(
        'APPLICATION_NAME' => $project_name,
      ), $output);

      $this->createDir("$cwd/instance/custom", $output);

      $this->createDir("$cwd/instance/custom/auth_providers", $output);
      $this->htaccessDenyAccess("$cwd/instance/custom/auth_providers", $output);
      $this->gitIgnore("$cwd/instance/custom/auth_providers", null, $output);

      $this->createDir("$cwd/instance/custom/layouts", $output);
      $this->htaccessDenyAccess("$cwd/instance/custom/layouts", $output);
      $this->gitIgnore("$cwd/instance/custom/layouts", null, $output);

      $this->createDir("$cwd/instance/custom/modules", $output);
      $this->htaccessDenyAccess("$cwd/instance/custom/modules", $output);
      $this->gitIgnore("$cwd/instance/custom/modules", null, $output);

      $this->createDir("$cwd/instance/import", $output);
      $this->htaccessDenyAccess("$cwd/instance/import", $output);
      $this->gitIgnore("$cwd/instance/import", null, $output);

      $this->createDir("$cwd/instance/logs", $output);
      $this->htaccessDenyAccess("$cwd/instance/logs", $output);
      $this->gitIgnore("$cwd/instance/logs", array('*'), $output);

      // Tasks
      $this->createDir("$cwd/instance/tasks", $output);
      $this->createFromTemplate("$cwd/instance/tasks/init.php", 'tasks_init', array(
        'APPLICATION_NAME' => $project_name,
      ), $output);
      $this->createFromTemplate("$cwd/instance/tasks/frequently.php", 'tasks_task', array(
        'APPLICATION_NAME' => $project_name,
        'TASK_TYPE' => 'frequently',
      ), $output);
      $this->createFromTemplate("$cwd/instance/tasks/hourly.php", 'tasks_task', array(
        'APPLICATION_NAME' => $project_name,
        'TASK_TYPE' => 'hourly',
      ), $output);
      $this->createFromTemplate("$cwd/instance/tasks/daily.php", 'tasks_task', array(
        'APPLICATION_NAME' => $project_name,
        'TASK_TYPE' => 'daily',
      ), $output);

      $this->createDir("$cwd/instance/thumbnails", $output);
      $this->htaccessDenyAccess("$cwd/instance/thumbnails", $output);
      $this->gitIgnore("$cwd/instance/thumbnails", array('*'), $output);

      $this->createDir("$cwd/instance/upload", $output);
      $this->htaccessDenyAccess("$cwd/instance/upload", $output);
      $this->gitIgnore("$cwd/instance/upload", array('*'), $output);

      $this->createDir("$cwd/instance/work", $output);
      $this->htaccessDenyAccess("$cwd/instance/work", $output);
      $this->gitIgnore("$cwd/instance/work", array('*'), $output);

      // Public
      $this->createDir("$cwd/instance/public", $output);
      $this->createFromTemplate("$cwd/instance/public/api.php", 'public_api', array('APPLICATION_NAME' => $project_name), $output);
      $this->createFromTemplate("$cwd/instance/public/index.php", 'public_index', array('APPLICATION_NAME' => $project_name), $output);
      $this->createFromTemplate("$cwd/instance/public/proxy.php", 'public_proxy', array('APPLICATION_NAME' => $project_name), $output);

      $this->createDir("$cwd/instance/public/assets", $output);
      $this->gitIgnore("$cwd/instance/public/assets", array('*'), $output);
      $this->createDir("$cwd/instance/public/avatars", $output);
      $this->createDir("$cwd/instance/public/brand", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/login-page-logo.png', "$cwd/instance/public/brand/login-page-logo.png", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/favicon.ico', "$cwd/instance/public/brand/favicon.ico", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/logo.16x16.png', "$cwd/instance/public/brand/logo.16x16.png", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/logo.40x40.png', "$cwd/instance/public/brand/logo.40x40.png", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/logo.80x80.png', "$cwd/instance/public/brand/logo.80x80.png", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/logo.128x128.png', "$cwd/instance/public/brand/logo.128x128.png", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/logo.256x256.png', "$cwd/instance/public/brand/logo.256x256.png", $output);

      $this->createDir("$cwd/instance/public/upgrade", $output);
      $this->createFromTemplate("$cwd/instance/public/upgrade/index.php", 'upgrade_index', null, $output);
      $this->createDir("$cwd/instance/public/work", $output);
      $this->gitIgnore("$cwd/instance/public/work", null, $output);

      $output->printMessage('File structure has been initialised. cd to /instance folder, and run the following commands: init, apply_controllers, apply_proxies, apply_notifications and finally build_model');
    } // execute

  }