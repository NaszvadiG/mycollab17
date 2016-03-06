<?php

  /**
   * Archive projects via CLI
   *
   * @package angie.tools.cli.commands
   */
  class CLICommandArchiveCompletedProjects extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Archive projects';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = [
      [ 'c:', 'count:', 'Number of projects to archive at once' ],
    ];

    /**
     * Execute the command
     *
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $count = (integer) $this->getOption([ "c", "count" ], 1);
      if ($count <= 0) {
        $count = 1;
      } // if

      $completed_projects_count = DB::executeFirstCell("SELECT count(id) FROM acx_projects WHERE completed_on IS NOT NULL AND state = ?", STATE_VISIBLE);
      $loops = ceil($completed_projects_count / $count);

      if ($loops <= 0) {
        $output->abortWithMessage("There are no completed projects that need to be archived");
      } // if

      $archived = 0;

      for ($i = 1; $i <= $loops; $i++) {
        $projects_to_archive = Projects::find(array(
          "conditions" => array("completed_on IS NOT NULL AND state = ?", STATE_VISIBLE),
          "limit" => 5
        ));

        if (is_foreachable($projects_to_archive)) {
          foreach ($projects_to_archive as $project) {
            if ($project instanceof Project) {
              $project->state()->archive();
              $archived++;
              $output->printMessage("Archived {$project->getName()} ({$archived}/{$completed_projects_count})");
            } // if
          } // foreach
        } else {
          $output->printMessage("No more projects to archive");
        } // if
      } // for
    } // execute

  }