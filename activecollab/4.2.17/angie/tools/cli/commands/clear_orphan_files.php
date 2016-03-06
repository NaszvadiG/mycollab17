<?php

  /**
   * Instant payment notification
   *
   * Instant notify on demand module about new payment
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandClearOrphanFiles extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Clear Orphan files';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(

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

      $output->abortWithMessage("Cleaning up orphaned files is disabled until further notice");

      list($deleted_files, $delete_failures) = DiskSpace::removeOrphanFiles();
      $output->printMessage(count($deleted_files) . ' Orphan files deleted (' . count($delete_failures) . ' failed)');
    } // execute

  } // CLICommandIpn
