<?php

  /**
   * List all system permissions defined in this application
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandSystemPermissions extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List all system permissions defined in this application';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $available_user_instances = Users::getAvailableUserInstances();
      
      if($available_user_instances) {
        $data = array();
        
        foreach($available_user_instances as $available_user_instance) {
          $permissions = $available_user_instance->getAvailableCustomPermissions();

          $permissions_names_string = $permissions_string = '--';

          if(is_foreachable($permissions)) {
            $permissions_names_string = $permissions_string = array();

            foreach($permissions as $permission => $permission_details) {
              $permissions_names_string[] = $permission;
              $permissions_string[] = $permission_details['name'];
            } // foreach

            $permissions_names_string = implode("\n", $permissions_names_string);
            $permissions_string = implode("\n", $permissions_string);
          } // if

          $data[] = array($available_user_instance->getRoleName(), $permissions_names_string, $permissions_string);
        } // foreach
        
        $output->printTable(array('Role', 'Permission Names', 'Custom Permissions'), $data);
      } else {
        $output->printMessage('There are no system permissions defined');
      } // if
    } // execute
    
  }