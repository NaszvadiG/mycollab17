<?php

  /**
   * Generator console command
   * 
   * Executable console command with features used for generation of files and 
   * folders. It has built in interface to template engine and implemented 
   * function for file and folder creation
   * 
   * @package angie.tools.cli
   * @subpackage errors
   */
  abstract class CLICommandGenerator extends CLICommandExecutable {

    /**
     * Construct new command instance
     */
    function __construct() {
      $this->cwd_length = strlen(getcwd());
    } // __construct
  
    /**
     * Generate a file
     *
     * This function will try to generate file at $target_path and put $content
     * in it. If file already exists content will be written only if $force is
     * set to true.
     *
     * Progress is written to the output object. If you don't want it printed use
     * silent output
     *
     * @param string $target_path
     * @param string $content
     * @param Output $output
     * @param boolean $force
     * @return boolean
     */
    function createFile($target_path, $content, $output, $force = false) {
      if(file_exists($target_path)) {
        if($force) {
          if(file_put_contents($target_path, $content)) {
            $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Overwrite.");
          } else {
            $output->abortWithMessage("Failed to overwrite '{$this->relativeToCwd($target_path)}'");
            return false;
          }
        } else {
          $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Skip.");
        } // if
      } else {
        if(file_put_contents($target_path, $content)) {
          $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' created.");
        } // if
      } // if

      return true;
    } // createFile

    /**
     * Copy file from given location to a new location
     *
     * @param string $source_path
     * @param string $target_path
     * @param Output $output
     * @param boolean $force
     * @return bool
     */
    function copyFile($source_path, $target_path, Output $output, $force = false) {
      if(file_exists($target_path)) {
        if($force) {
          if(copy($source_path, $target_path)) {
            $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Overwrite.");
          } else {
            $output->abortWithMessage("Failed to copy '$source_path' to '" . $this->relativeToCwd($target_path) . "'");
            return false;
          } // if
        } else {
          $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Skip.");
        } // if
      } else {
        if(copy($source_path, $target_path)) {
          $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' copied.");
        } else {
          $output->abortWithMessage("Failed to copy '$source_path' to '" . $this->relativeToCwd($target_path) . "'");
          return false;
        } // if
      } // if

      return true;
    } // copyFile

    /**
     * Create from dumb tempalte
     *
     * @param string $target_path
     * @param string $template
     * @param array $variables
     * @param Output $output
     * @param bool $force
     * @return bool
     */
    function createFromTemplate($target_path, $template, $variables, Output $output, $force = false) {
      if(!is_writable(dirname($target_path))) {
        $output->abortWithMessage("Target '" . $this->relativeToCwd(dirname($target_path)) . "' is not writable");
        return false;
      } // if

      // Try to find template in project
      $template_path = CLI::getProjectTemplatesPath() . "/$template.dumb.template";

      // If not found, look in the framework
      if(!is_file($template_path)) {
        $template_path = CLI_TOOL_PATH . "/templates/$template.dumb.template";
      } // if

      if(is_file($template_path)) {
        $template_content = file_get_contents($template_path);

        if(is_array($variables)) {
          foreach($variables as $k => $v) {
            $template_content = str_replace("##$k##", $v, $template_content);
          } // foreach
        } // if

        if(file_exists($target_path)) {
          if($force) {
            if(file_put_contents($target_path, $template_content)) {
              $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Overwrite.");
            } else {
              $output->abortWithMessage("Failed to overwrite '{$this->relativeToCwd($target_path)}'");
              return false;
            }
          } else {
            $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' already exist. Skip.");
          } // if
        } else {
          if(file_put_contents($target_path, $template_content)) {
            $output->printMessage("File '" . $this->relativeToCwd($target_path) . "' created.");
          } else {
            $output->abortWithMessage("Failed to create '" . $this->relativeToCwd($target_path) . "'");
            return false;
          } // if
        } // if

      } else {
        $output->abortWithMessage("Failed to load '$template' template (expected to find it in '$template_path')");
        return false;
      } // if

      return true;
    } // createFromTemplate
    
    /**
     * Create directory in a $target_path
     *
     * @param string $target_path
     * @param Output $output
     * @return boolean
     */
    function createDir($target_path, $output) {
      if(is_dir($target_path)) {
        $output->printMessage('Directory "' . $this->relativeToCwd($target_path) . '" already exist. Skip.');
      } else {
        if(mkdir($target_path)) {
          $output->printMessage('Directory "' . $this->relativeToCwd($target_path) . '" created.');
        } else {
          $output->abortWithMessage('Failed to create "' . $this->relativeToCwd($target_path) . '" directory.', 'error');
          return false;
        } // if
      } // if

      return true;
    } // createDir

    /**
     * Recursively remove a specific dir
     *
     * @param string $dir_path
     * @param Output $output
     * @param bool $force
     * @return bool
     */
    function removeDir($dir_path, Output $output, $force = false) {
      if(is_dir($dir_path)) {
        $dh = opendir($dir_path);
        while($file = readdir($dh)) {
          if(($file != ".") && ($file != "..")) {
            $fullpath = "$dir_path/$file";

            if(is_link($fullpath)) {
              if(unlink("$dir_path/$file")) {
                $output->printMessage('Removed "' . $this->relativeToCwd($fullpath) . '" link');
              } else {
                $output->printMessage('Failed to remove "' . $this->relativeToCwd($fullpath) . '" link', 'notice');
              } // if
            } elseif(is_dir($fullpath)) {
              $this->removeDir($fullpath, $output, $force);
            } else {
              if(unlink("$dir_path/$file")) {
                $output->printMessage('Removed "' . $this->relativeToCwd($fullpath) . '" file');
              } else {
                $output->printMessage('Failed to remove "' . $this->relativeToCwd($fullpath) . '" file', 'notice');
              } // if
            } // if
          } // if
        } // while

        closedir($dh);

        if(rmdir($dir_path)) {
          $output->printMessage('Removed "' . $this->relativeToCwd($dir_path) . '" directory');
        } else {
          $output->abortWithMessage('Failed to remove "' . $this->relativeToCwd($dir_path) . '" directory', 'notice');
          return false;
        } // if
      } else {
        $output->printMessage('Directory "' . $this->relativeToCwd($dir_path) . '" does not exist', 'notice');
      } // if

      return true;
    } // removeDir

    /**
     * Remove a file
     *
     * @param string $path
     * @param Output $output
     */
    function removeFile($path, Output $output) {
      if (unlink($path)) {
        $output->printMessage('Removed "' . $path . '" file');
      } else {
        $output->printMessage('Failed to remove "' . $path . '" file', 'notice');
      } // if
    } // removeFile

    /**
     * Create a symlink
     *
     * @param string $point_from
     * @param string $point_to
     * @param Output $output
     * @param boolean $force
     * @return boolean
     */
    function createDirSymlink($point_from, $point_to, Output $output, $force = false) {
      if((is_link($point_from) || is_dir($point_from) || is_file($point_from)) && $force === false) {
        $output->printMessage($this->relativeToCwd($point_to) . '" already exists. Skipping symlink creation.');
        return true;
      } else {
        // cleanup
        if ($force === true) {
          if (is_link($point_from) || is_file($point_from)) {
            @unlink($point_from);
          } // if

          if (is_dir($point_from)) {
            $this->removeDir($point_from, $output, true);
          } // if
        } // if

        if(symlink($point_to, $point_from)) {
          $output->printMessage('Symlink that connects "' . $this->relativeToCwd($point_to) . '" to "' . $this->relativeToCwd($point_to) . "' has been created");
          return true;
        } else {
          $output->abortWithMessage('Failed to create symlink at "' . $this->relativeToCwd($point_to) . '" that points to "' . $this->relativeToCwd($point_to) . "'");
          return false;
        } // if
      } // if
    } // createDirSymlink
    
    /**
     * Copy files and deirectories based on a $from file structure
     *
     * @param string $from
     * @param string $to
     * @param Output $output
     * @param bool $force
     * @return bool
     */
    function copyStructure($from, $to, Output $output, $force = false) {
      if(!is_dir($to)) {
        $this->createDir($to, $output);
      } // if

      if(is_dir($to)) {
        $dir = dir($from);
        while(($entry = $dir->read()) !== false) {
          if($entry == '.' || $entry == '..') {
            continue;
          } // if

          $from_path = $from . '/' . $entry;
          $to_path = $to . '/' . $entry;

          if(is_dir($from_path)) {
            $this->createDir($to_path, $output);
            $this->copyStructure($from_path, $to_path, $output);
          } elseif(is_file($from_path)) {
            $this->copyFile($from_path, $to_path, $output, $force);
          } else {
            $output->printMessage("'$from_path' is not a file or directory. Skipping", 'notice');
          } // if
        } // while

        $dir->close();
        return true;
      } else {
        $output->abortWithMessage("Target '$to' does not exist and could not be created");
        return false;
      } // if
    } // copyStructure

    /**
     * Change permissions of a given directory or a file
     *
     * @param string $target
     * @param int $permissions
     * @param Output $output
     * @param boolean $recursive
     * @return boolean
     */
    function changeDirPermissions($target, $permissions = 0600, Output $output, $recursive = true) {

      // Link
      if(is_link($target)) {
        $output->printMessage('Skipping "' . $this->relativeToCwd($target) . '" from chmod because it is a link, not a file or directory', 'notice');

      // Directory
      } elseif(is_dir($target)) {

        if($recursive) {
          $dh = opendir($target);
          while($file = readdir($dh)) {
            if(($file != ".") && ($file != "..")) {
              $fullpath = "$target/$file";

              if(is_link($fullpath)) {
                $output->printMessage('Skipping "' . $this->relativeToCwd($fullpath) . '" from chmod because it is a link, not a file or directory', 'notice');
              } elseif(is_dir($fullpath)) {
                $this->changeDirPermissions($fullpath, $permissions, $output, $recursive);
              } else {
                $this->changeFilePermissions($fullpath, $permissions, $output);
              } // if
            } // if
          } // while

          closedir($dh);
        } // if

        if(chmod($target, $permissions)) {
          $output->printMessage('Changed permissions of "' . $this->relativeToCwd($target) . '" directory to "' . $permissions . '"');
        } else {
          $output->printMessage('Failed to change permissions of "' . $this->relativeToCwd($target) . '" directory', 'notice');
          return false;
        } // if

      // File
      } elseif(is_file($target)) {
        $this->changeFilePermissions($target, $permissions, $output);

      // Not found
      } else {
        $output->printMessage('Target "' . $this->relativeToCwd($target) . '" does not exist', 'notice');
      } // if

      return true;
    } // changeDirPermissions

    /**
     * Change file permissions
     *
     * @param string $target
     * @param int $permissions
     * @param Output $output
     * @return bool
     */
    function changeFilePermissions($target, $permissions = 0600, Output $output) {
      if(chmod($target, $permissions)) {
        $output->printMessage('Changed "' . $this->relativeToCwd($target) . '" permissions to "' . $permissions . '"');
        return true;
      } else {
        $output->printMessage('Failed to change permissions of "' . $this->relativeToCwd($target) . '" file', 'notice');
        return false;
      } // if
    } // changeFilePermissions

    /**
     * Run PHP script
     *
     * @param string $script_path
     * @param string $run_from
     * @param Output $output
     * @param array $error_strings
     * @param array $additional_params
     */
    function runPhpScript($script_path, $run_from, Output $output, $error_strings = array(), $additional_params = array()) {
      if(is_file($script_path)) {
        $current_cwd = getcwd();

        if($run_from && is_dir($run_from) && $run_from != $current_cwd) {
          chdir($run_from);
          $output->printMessage('Work dir changed to "' . $run_from . "'");
        } // if

        $response = array();

        $output->printMessage('Running "' . $this->relativeToCwd($script_path) . '" PHP script');

        $params = "";
        if (is_foreachable($additional_params)) {
          $params = array();
          foreach ($additional_params as $additional_param) {
            $params[] = escapeshellarg($additional_param);
          } // foreach

          $params = " " . implode(" ", $params);
        } // if

        $error_str = null;
        exec("php " . escapeshellarg($script_path) . $params, $response);

        if(count($response)) {
          $output->printMessage('Script response:');

          foreach($response as $response_line) {
            $output->printMessage($response_line);

            if (is_foreachable($error_strings) && is_null($error_str)) {
              foreach ($error_strings as $error_string) {
                if (strpos($response_line, $error_string) !== false) {
                  $error_str = $error_string;
                  break;
                } // if
              } // foreach
            } // if
          } // foreach
        } else {
          $output->printMessage('Script return no response', 'notice');
        } // if

        if($run_from && is_dir($run_from) && $run_from != $current_cwd) {
          chdir($current_cwd);
          $output->printMessage('Work dir changed back to "' . $current_cwd . "'");
        } // if

        if (!is_null($error_str)) {
          $output->abortWithMessage("Aborting process: " . var_export($error_str, true));
        } // if
      } else {
        $output->abortWithMessage('File "' . $this->relativeToCwd($script_path) . '" not found', 'error');
      } // if
    } // runPhpScript

    /**
     * Create a module in a given folder
     *
     * @param string $short_name
     * @param string $version
     * @param string $module_signature
     * @param string $target
     * @param string $project_name
     * @param string $short_project_name
     * @param Output $output
     */
    function createModule($short_name, $version, $module_signature, $target, $project_name, $short_project_name, Output $output) {
      $module_name = Inflector::camelize($short_name);

      $this->createDir("$target/$short_name", $output);

      // Module definition and initialization
      $init_file_template = $short_name == 'system' ? 'module_system_init' : 'module_init';

      $this->createFromTemplate("$target/$short_name/init.php", $init_file_template, array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'MODULE_NAME' => $short_name,
        'MODULE_VERSION' => $version,
        'MODULE_NAME_UPPERCASE' => strtoupper($module_name),
      ), $output);

      $this->createFromTemplate("$target/$short_name/signature.php", 'module_signature', array(
        'APPLICATION_NAME' => $project_name,
        'MODULE_SIGNATURE' => $module_signature,
      ), $output);

      $module_class = "{$module_name}Module";

      $this->createFromTemplate("$target/$short_name/$module_class.class.php", 'module_class', array(
        'APPLICATION_NAME' => $project_name,
        'SHORT_APPLICATION_NAME' => $short_project_name,
        'MODULE_NAME' => $short_name,
        'MODULE_VERSION' => $version,
        'MODULE_CLASS' => $module_class,
      ), $output);

      // Assets
      $this->createDir("$target/$short_name/assets", $output);
      $this->createDir("$target/$short_name/assets/default", $output);
      $this->createDir("$target/$short_name/assets/default/images", $output);
      $this->copyFile(CLI_TOOL_PATH . '/files/module.png', "$target/$short_name/assets/default/images/module.png", $output);
      $this->createDir("$target/$short_name/assets/default/stylesheets", $output);
      $this->createDir("$target/$short_name/assets/default/javascript", $output);

      // Controllers
      $this->createDir("$target/$short_name/controllers", $output);

      if($short_name == 'system') {
        $this->createFromTemplate("$target/$short_name/controllers/ApplicationController.class.php", 'application_controller', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);
      } // if

      // Layouts
      $this->createDir("$target/$short_name/layouts", $output);
      if($short_name == 'system') {
        $this->createFromTemplate("$target/$short_name/layouts/application.tpl", 'layout_application', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/backend.tpl", 'layout_backend', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/error.tpl", 'layout_error', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/frontend.tpl", 'layout_frontend', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/inline.tpl", 'layout_inline', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/print.tpl", 'layout_print', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/quick_view.tpl", 'layout_quick_view', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/layouts/single.tpl", 'layout_single', array('APPLICATION_NAME' => $project_name), $output);
      } // if

      // Models
      $this->createDir("$target/$short_name/models", $output);
      $this->createDir("$target/$short_name/notifications", $output);

      if($short_name == 'system') {
        $this->createDir("$target/$short_name/models/controller", $output);

        $this->createFromTemplate("$target/$short_name/models/controller/Request.class.php", 'request_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);
        $this->createFromTemplate("$target/$short_name/models/controller/Response.class.php", 'response_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);

        $this->createFromTemplate("$target/$short_name/models/AdminPanel.class.php", 'admin_panel_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/Assignments.class.php", 'assignments_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/AnonymousUser.class.php", 'anonymous_user_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/CustomFields.class.php", 'custom_fields_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/ColorSchemes.class.php", 'color_schemes_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/ControlTower.class.php", 'control_tower_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/DiskSpace.class.php", 'disk_space_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/HistoryRenderer.class.php", 'history_renderer_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/Favorites.class.php", 'favorites_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/MainMenu.class.php", 'main_menu_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/MassManager.class.php", 'mass_manager_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/OutgoingMessageDecorator.class.php", 'outgoing_message_decorator_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/ReportsPanel.class.php", 'reports_panel_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/StatusBar.class.php", 'status_bar_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/Thumbnails.class.php", 'thumbnails_class', array('APPLICATION_NAME' => $project_name), $output);
        $this->createFromTemplate("$target/$short_name/models/Trash.class.php", 'trash_class', array('APPLICATION_NAME' => $project_name), $output);

        $this->createDir("$target/$short_name/models/response", $output);

        $this->createFromTemplate("$target/$short_name/models/response/BackendWebInterfaceResponse.class.php", 'backend_web_interface_class', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
        ), $output);
        $this->createFromTemplate("$target/$short_name/models/response/FrontendWebInterfaceResponse.class.php", 'fontend_web_interface_class', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
        ), $output);

        // Search
        $this->createDir("$target/$short_name/models/search", $output);

        $this->createFromTemplate("$target/$short_name/models/search/UsersSearchIndex.class.php", 'users_search_index_class', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
        ), $output);

        $this->createFromTemplate("$target/$short_name/models/search/IUserSearchItemImplementation.class.php", 'user_search_item_implementation_class', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
        ), $output);

        // Wireframes
        $this->createDir("$target/$short_name/models/wireframe", $output);

        $this->createFromTemplate("$target/$short_name/models/wireframe/Wireframe.class.php", 'wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
        $this->createFromTemplate("$target/$short_name/models/wireframe/BackendWireframe.class.php", 'backend_wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
        $this->createFromTemplate("$target/$short_name/models/wireframe/PhoneBackendWireframe.class.php", 'phone_backend_wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
        $this->createFromTemplate("$target/$short_name/models/wireframe/TabletBackendWireframe.class.php", 'tablet_backend_wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
        $this->createFromTemplate("$target/$short_name/models/wireframe/WebBrowserBackendWireframe.class.php", 'web_browser_backend_wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
        $this->createFromTemplate("$target/$short_name/models/wireframe/FrontendWireframe.class.php", 'frontend_wireframe_class', array('APPLICATION_NAME' => AngieApplication::getName()), $output);

        // Application objects
        $this->createDir("$target/$short_name/models/application_objects", $output);

        $this->createFromTemplate("$target/$short_name/models/application_objects/ApplicationObject.class.php", 'application_object_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);
        $this->createFromTemplate("$target/$short_name/models/application_objects/ApplicationObjects.class.php", 'application_objects_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);

        // Roles
        $this->createDir("$target/$short_name/models/user_roles", $output);

        $this->createFromTemplate("$target/$short_name/models/user_roles/Administrator.class.php", 'administrator_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);

        $this->createFromTemplate("$target/$short_name/models/user_roles/Member.class.php", 'member_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);

        // Homescreen and Role Dashboards
        $this->createFromTemplate("$target/$short_name/models/Homescreens.class.php", 'homescreens_class', array(
          'APPLICATION_NAME' => $project_name,
          'MODULE_NAME' => $short_name,
        ), $output);
      } // if

      // Other folders
      $this->createDir("$target/$short_name/emails", $output);
      $this->createDir("$target/$short_name/handlers", $output);
      $this->createDir("$target/$short_name/helpers", $output);

      // Proxies
      $this->createDir("$target/$short_name/proxies", $output);

      $this->createFromTemplate("$target/$short_name/proxies/CaptchaProxy.class.php", 'captcha_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
      $this->createFromTemplate("$target/$short_name/proxies/CollectJavaScriptProxy.class.php", 'collect_javascript_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
      $this->createFromTemplate("$target/$short_name/proxies/CollectStylesheetsProxy.class.php", 'collect_stylesheets_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
      $this->createFromTemplate("$target/$short_name/proxies/DownloadAttachmentProxy.class.php", 'download_attachment_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
      $this->createFromTemplate("$target/$short_name/proxies/ForwardThumbnailProxy.class.php", 'forward_thumbnail_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);
      $this->createFromTemplate("$target/$short_name/proxies/RepairAvatarProxy.class.php", 'repair_avatar_proxy', array('APPLICATION_NAME' => AngieApplication::getName()), $output);

      $this->createDir("$target/$short_name/resources", $output);
      $this->createDir("$target/$short_name/tests", $output);

      // Views
      $this->createDir("$target/$short_name/views", $output);
      $this->createDir("$target/$short_name/views/default", $output);
    } // createModule

    /**
     * Apply framework controllers to a given module
     *
     * @param AngieFramework $framework
     * @param string $target_module
     * @param Output $output
     * @param bool $force
     */
    function applyFrameworkControllers(AngieFramework $framework, $target_module, Output $output, $force = false) {
      $controllers = get_files($framework->getPath() . '/controllers', 'php');

      if($controllers) {
        $target = APPLICATION_PATH . "/modules/$target_module/controllers";

        foreach($controllers as $controller_path) {
          $slash_pos = strrpos($controller_path, '/');
          $excension_pos = strpos($controller_path, '.');

          $controller_name = substr($controller_path, $slash_pos + 3, $excension_pos - $slash_pos - 13); // Remove path/to/controller/Fw and Controller.class.php

          $this->createFromTemplate("$target/{$controller_name}Controller.class.php", 'controller', array(
            'SHORT_CONTROLLER_NAME' => Inflector::underscore($controller_name),
            'CONTROLLER_NAME' => $controller_name . 'Controller',
            'APPLICATION_NAME' => AngieApplication::getName(),
            'FRAMEWORK_NAME' => $framework->getName(),
            'IS_ABSTRACT' => $controller_name == 'Application' ? 'abstract ' : '', // Application controllers needs to be abstract
          ), $output, $force);
        } // foreach
      } // if
    } // applyFrameworkControllers

    /**
     * Apply framework proxies to a given module
     *
     * @param AngieFramework $framework
     * @param string $target_module
     * @param Output $output
     * @param bool $force
     */
    function applyFrameworkProxies(AngieFramework $framework, $target_module, Output $output, $force = false) {
      $proxies = get_files($framework->getPath() . '/proxies', 'php');

      if($proxies) {
        $target = APPLICATION_PATH . "/modules/$target_module/proxies";

        foreach($proxies as $proxy_path) {
          if(substr(basename($proxy_path), 0, 2) != 'Fw') {
            continue;
          } // if

          $slash_pos = strrpos($proxy_path, '/');
          $excension_pos = strpos($proxy_path, '.');

          $proxy_name = substr($proxy_path, $slash_pos + 3, $excension_pos - $slash_pos - 8); // Remove path/to/proxies/Fw and Proxy.class.php

          $this->createFromTemplate("$target/{$proxy_name}Proxy.class.php", 'proxy', array(
            'SHORT_PROXY_NAME' => Inflector::underscore($proxy_name),
            'PROXY_NAME' => $proxy_name . 'Proxy',
            'APPLICATION_NAME' => AngieApplication::getName(),
            'FRAMEWORK_NAME' => $framework->getName(),
          ), $output, $force);
        } // foreach
      } // if
    } // applyFrameworkProxies

    /**
     * Apply framework controllers to a given module
     *
     * @param AngieFramework $framework
     * @param string $target_module
     * @param array $applied_notifications
     * @param Output $output
     * @param bool $force
     */
    function applyFrameworkNotifications(AngieFramework $framework, $target_module, &$applied_notifications, Output $output, $force = false) {
      $notifications = get_files($framework->getPath() . '/notifications', 'php');

      if($notifications) {
        $target = APPLICATION_PATH . "/modules/$target_module/notifications";

        foreach($notifications as $notification_path) {
          $notification_class_name = str_replace('.class.php', '', basename($notification_path));

          if(!str_starts_with($notification_class_name, 'Fw')) {
            continue;
          } // if

          $notification_name = substr($notification_class_name, 2, strlen($notification_class_name) - 14);

          $this->createFromTemplate("$target/{$notification_name}Notification.class.php", 'notification', array(
            'SHORT_NOTIFICATION_NAME' => Inflector::underscore($notification_name),
            'NOTIFICATION_NAME' => "{$notification_name}Notification",
            'IS_ABSTRACT' => str_starts_with($notification_name, 'Base') ? 'abstract ' : '',
            'APPLICATION_NAME' => AngieApplication::getName(),
            'FRAMEWORK_NAME' => $framework->getName(),
          ), $output, $force);

          $applied_notifications[] = "{$notification_name}Notification";
        } // foreach
      } // if
    } // applyFrameworkNotifications

    /**
     * Generate model files
     *
     * @param string
     * @param AngieFrameworkModelBuilder $model_build
     * @param Output $output
     * @throws Error
     * @throws InvalidInstanceError
     */
    function generateModelFiles($model_name, AngieFrameworkModelBuilder $model_build, Output $output) {
      $object_class = Inflector::camelize(Inflector::singularize($model_name));
      $manager_class = Inflector::camelize($model_name);

      $destination_dir = $model_build->getDestinationModulePath();

      if(is_writable($destination_dir)) {
        $model_dir = "$destination_dir/models/$model_name";

        if($this->createDir($model_dir, $output)) {
          $primary_key = array('id');
          $auto_increment = true;

          $smarty =& SmartyForAngie::getInstance();

          $old_left_delimiter = $smarty->left_delimiter;
          $old_right_delimiter = $smarty->right_delimiter;

          $smarty->left_delimiter = '<{';
          $smarty->right_delimiter = '}>';

          $field_names = array();

          foreach($model_build->getFields() as $field) {
            if($field instanceof DBCompositeColumn) {
              foreach($field->getColumns() as $composite_column_field) {
                $field_names[] = var_export($composite_column_field->getName(), true);
              } // foreach
            } elseif($field instanceof DBColumn) {
              $field_names[] = var_export($field->getName(), true);
            } else {
              throw new InvalidInstanceError('field', $field, 'DBColumn');
            } // if
          } // foreach

          $has_many_associations = array();
          $has_one_associations = array();
          $belongs_to_associations = array();

          foreach($model_build->getAssociations() as $association_name => $association) {
            switch($association['type']) {

              // Has many or has and belongs to many
              case AngieFrameworkModelBuilder::HAS_MANY:
              case AngieFrameworkModelBuilder::HAS_AND_BELONGS_TO_MANY:
                $association_for_cli = array(
                  'name' => $association_name,
                  'method_name' => lcfirst(Inflector::camelize($association_name)),
                  'class_name' => $this->getAssociationClassName($object_class, $association_name, $association['type']),
                  'dir_name' => $this->getAssociationDirName($model_name, $association_name, $association['type']),
                );

                unset($association['type']);
                unset($association['target_model_name']);

                $association_for_cli['params'] = count($association) ? $association : null;

                $has_many_associations[] = $association_for_cli;

                break;

              // Has one
              case AngieFrameworkModelBuilder::HAS_ONE:
                $association_for_cli = array(
                  'name' => $association_name,
                  'getter_name' => 'get' . Inflector::camelize($association_name),
                  'setter_name' => 'set' . Inflector::camelize($association_name),
                  'key_field_name' => isset($association['field_name']) && $association['field_name'] ? $association['field_name'] : Inflector::singularize($model_name) . '_id',
                  'object_class' => Inflector::camelize(Inflector::singularize($association['target_model_name'])),
                  'manager_class' => Inflector::camelize($association['target_model_name']),
                  'required' => isset($association['required']) && $association['required'],
                );

                unset($association['type']);
                unset($association['target_model_name']);

                $association_for_cli['params'] = count($association) ? $association : null;

                $has_one_associations[] = $association_for_cli;

                break;

              // Belongs to one
              case AngieFrameworkModelBuilder::BELONGS_TO:
                $association_for_cli = array(
                  'name' => $association_name,
                  'getter_name' => 'get' . Inflector::camelize($association_name),
                  'setter_name' => 'set' . Inflector::camelize($association_name),
                  'key_field_name' => isset($association['field_name']) && $association['field_name'] ? $association['field_name'] : $association['target_model_name'] . '_id',
                  'object_class' => Inflector::camelize(Inflector::singularize($association['target_model_name'])),
                  'required' => isset($association['required']) && $association['required'],
                );

                unset($association['type']);
                unset($association['target_model_name']);

                $association_for_cli['params'] = count($association) ? $association : null;

                $belongs_to_associations[] = $association_for_cli;
                break;
              default:
                throw new Error('Unknown association: "' . $association['type'] . '"');
            } // switch
          } // foreach

          $smarty->assign('model_name', $manager_class);
          $smarty->assign('model_name_underscore', Inflector::underscore($model_name));
          $smarty->assign('model_name_singular', $object_class);
          $smarty->assign('model_name_singular_underscore', Inflector::underscore($object_class));
          $smarty->assign('object', Inflector::singularize($model_name));
          $smarty->assign('objects', $model_name);
          $smarty->assign('object_class', $object_class);
          $smarty->assign('manager_class', $manager_class);
          $smarty->assign('table_name', $model_name);
          $smarty->assign('fields', $model_build->getFields());
          $smarty->assign('field_names_as_string', implode(', ', $field_names));
          $smarty->assign('has_many_associations', $has_many_associations);
          $smarty->assign('has_one_associations', $has_one_associations);
          $smarty->assign('belongs_to_associations', $belongs_to_associations);
          $smarty->assign('primary_key', "'id'");
          $smarty->assign('auto_increment', $auto_increment);
          $smarty->assign('base_object_extends', $model_build->getBaseObjectExtends());
          $smarty->assign('base_manager_extends', $model_build->getBaseManagerExtends());
          $smarty->assign('object_is_abstract', $model_build->getObjectIsAbstract());
          $smarty->assign('manager_is_abstract', $model_build->getManagerIsAbstract());
          $smarty->assign('module', $model_build->getDestinationModuleName());
          $smarty->assign('application', APPLICATION_NAME);
          $smarty->assign('generate_permissions', $model_build->getGeneratePermissions());
          $smarty->assign('generate_urls', $model_build->getGenerateUrls());
          $smarty->assign('order_by', $model_build->getOrderBy());

          if($model_build->getTypeFromField()) {
            $smarty->assign('class_name_from', 'DataManager::CLASS_NAME_FROM_FIELD');
            $smarty->assign('load_object_class_name', $model_build->getTypeFromField());
            $smarty->assign('class_name_from_field', $model_build->getTypeFromField());
          } else {
            $smarty->assign('class_name_from', 'DataManager::CLASS_NAME_FROM_TABLE');
            $smarty->assign('load_object_class_name', $object_class);
            $smarty->assign('class_name_from_field', null);
          } // if

          $this->createFile("{$model_dir}/Base{$object_class}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/model/base_object.tpl'), $output, true);
          $this->createFile("{$model_dir}/Base{$manager_class}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/model/base_manager.tpl'), $output, true);

          // If model is defined in framework, make sure that we connect with Fw classes from the framework
          $smarty->assign('object_class_extends', $model_build->getModel() instanceof AngieModuleModel ? "Base{$object_class}" : "Fw{$object_class}");
          $smarty->assign('manager_class_extends', $model_build->getModel() instanceof AngieModuleModel ? "Base{$manager_class}" : "Fw{$manager_class}");

          $this->createFile("{$model_dir}/{$object_class}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/model/object.tpl'), $output);
          $this->createFile("{$model_dir}/{$manager_class}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/model/manager.tpl'), $output);

          // ---------------------------------------------------
          //  Associations
          // ---------------------------------------------------

          $smarty->assign('source_underscore_signular', Inflector::singularize($model_name));
          $smarty->assign('source_underscore_plural', $model_name);
          $smarty->assign('source_singular', $object_class);
          $smarty->assign('source_plural', $manager_class);

          foreach($model_build->getAssociations() as $association_name => $association) {
            if($association['type'] == AngieFrameworkModelBuilder::HAS_MANY || $association['type'] == AngieFrameworkModelBuilder::HAS_AND_BELONGS_TO_MANY) {
              $association_dir = "$destination_dir/models/" . $this->getAssociationDirName($model_name, $association_name, $association['type']);

              if($this->createDir($association_dir, $output)) {
                $smarty->assign('target_underscore_signular', Inflector::singularize($association['target_model_name']));
                $smarty->assign('target_underscore_plural', $association['target_model_name']);
                $smarty->assign('target_singular', Inflector::camelize(Inflector::singularize($association['target_model_name'])));
                $smarty->assign('target_plural', Inflector::camelize($association['target_model_name']));

                $association_class_name = $this->getAssociationClassName($object_class, $association_name, $association['type']);

                $smarty->assign('association_class_name', $association_class_name);

                if($association['type'] == AngieFrameworkModelBuilder::HAS_MANY) {
                  $this->createFile("{$association_dir}/Base{$association_class_name}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/associations/base_has_many.tpl'), $output, true);
                  $this->createFile("{$association_dir}/{$association_class_name}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/associations/has_many.tpl'), $output);
                } else {
                  $this->createFile("{$association_dir}/Base{$association_class_name}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/associations/base_has_and_belongs_to_many.tpl'), $output, true);
                  $this->createFile("{$association_dir}/{$association_class_name}.class.php", $smarty->fetch(CLI_TOOL_PATH . '/templates/associations/has_and_belongs_to_many.tpl'), $output);
                } // if
              } // if
            } // if
          } // foreach

          // Revert back to old delimiters
          $smarty->left_delimiter = $old_left_delimiter;
          $smarty->right_delimiter = $old_right_delimiter;
        } // if
      } else {
        $output->printMessage("Framework or Module folder '$destination_dir' is not writable", 'error');
      } // if
    } // generateModelFiles

    /**
     * Return association dir name
     *
     * @param string $source_model_name
     * @param string $association_name
     * @param string $association_type
     * @return string
     */
    protected function getAssociationDirName($source_model_name, $association_name, $association_type) {
      $prefix = Inflector::singularize($source_model_name);

      switch($association_type) {
        case AngieFrameworkModelBuilder::HAS_MANY:
          return "{$prefix}_has_many_{$association_name}";
        case AngieFrameworkModelBuilder::HAS_AND_BELONGS_TO_MANY:
          return "{$prefix}_has_and_belongs_to_many_{$association_name}";
        default:
          return '';
      } // if
    } // getAssociationDirName

    /**
     * Return association class name
     *
     * @param string $source_instance_class
     * @param string $association_name
     * @param string $association_type
     * @return string
     */
    protected function getAssociationClassName($source_instance_class, $association_name, $association_type) {
      switch($association_type) {
        case AngieFrameworkModelBuilder::HAS_MANY:
          return "{$source_instance_class}HasMany" . Inflector::camelize($association_name);
        case AngieFrameworkModelBuilder::HAS_AND_BELONGS_TO_MANY:
          return "{$source_instance_class}HasAndBelongsToMany" . Inflector::camelize($association_name);
        default:
          return '';
      } // if
    } // getAssociationClassName

    /**
     * Generate model auto-loader
     *
     * @param array $models
     * @param string $module
     * @param Output $output
     */
    function generateModelAutoloader($models, $module, Output $output) {
      if(is_array($models) && count($models) && $module) {
        $model_names = array();

        foreach($models as $model) {
          $model_names[] = var_export($model, true);
        } // foreach

        $this->createFromTemplate(APPLICATION_PATH . "/modules/$module/resources/autoload_model.php", 'autoload_model_file', array(
          'APPLICATION_NAME' => AngieApplication::getName(),
          'MODULE_NAME' => $module,
          'MODELS' => implode(', ', $model_names),
        ), $output, true);
      } // if
    } // generateModelAutoloader

    /**
     * Deny access in given directory
     *
     * @param string $target_dir
     * @param Output $output
     */
    function htaccessDenyAccess($target_dir, Output $output) {
      $this->createFile("$target_dir/.htaccess", "Deny from All", $output);
    } // htaccessDenyAccess

    /**
     * Add git ignore file
     *
     * @param string $target_dir
     * @param string $patterns
     * @param Output $output
     */
    function gitIgnore($target_dir, $patterns = null, Output $output) {
      $ignore = $patterns ? (array) $patterns : array();

      $ignore[] = '!.gitignore';
      $ignore[] = '!.htaccess';

      $this->createFile("$target_dir/.gitignore", implode("\n", $ignore), $output);
    } // gitIgnore

    /**
     * Cached CWD length
     *
     * @var integer
     */
    private $cwd_length = false;

    /**
     * Return path relative to CWD path
     *
     * @param string $path
     * @return string
     */
    function relativeToCwd($path) {
      if(substr($path, 0, $this->cwd_length) != getcwd()) {
        return $path;
      } else {
        return substr($path, $this->cwd_length);
      } // if
    } // relativeToCwd
  
  }