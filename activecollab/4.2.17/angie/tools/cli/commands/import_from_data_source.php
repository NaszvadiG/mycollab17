<?php

  /**
   * Manage admins
   *
   * Manage admins via cli
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandImportFromDataSource extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Import Data from source';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('s:', 'source_id', 'Source ID'),
      array('u:', 'user_id', 'Logged User id'),
      array('pid:', 'project_id', 'Basecamp Project Id'),
      array('c:', 'check', 'Check if project already imported'),
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
      
      $source_id = $this->getOption(array('s', 'source_id'));
      $source = DataSources::findById($source_id);
      if(!$source instanceof DataSource) {
        $output->abortWithMessage('Data Source not found');
      } //if

      $user_id = $this->getOption(array('u', 'user_id'));
      $user = Users::findById($user_id);
      if(!$user instanceof User) {
        $output->abortWithMessage('Provide user id to use as logged user');
      } //if
      Authentication::setLoggedUser($user);

      if($source instanceof Basecamp) {

        //for Basecamp
        $project_id = $this->getOption(array('pid', 'project_id'), false);
        if(!$project_id) {
          $output->abortWithMessage('Project Id not provided');
        } //if

        $check = (boolean) $this->getOption(array('c', 'check'), true);
        if($check) {
          $project = DataSourceMappings::findObjectByExternalAndSource(DataSourceMappings::BASECAMP_EXTERNAL_TYPE_PROJECT, $project_id, $source);
          if($project instanceof Project) {
            $output->abortWithMessage('Project "' . $project->getName() . '" already imported. Please delete it first');
          } //if
        } //if

        $pa = [
          'action' => 'import_project',
          'project_id' => $project_id
        ];
        $can_import = $source->validate_import($pa);
        if($can_import !== true && is_array($can_import)) {
          $output->abortWithMessage($can_import['message']);
        } //if

        $source->importProject($project_id, $check);

      } else {
        $output->abortWithMessage('Not implemented');
      } //if

      $output->printMessage('Done');

    } // execute
  }