<?php

  class CLICommandExportToJson extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Export the whole system to JSON';

    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $user = Users::find(array(
        "conditions" => array("type = 'Administrator' AND state = ?", STATE_VISIBLE),
        "order" => "id ASC",
        "one" => true
      ));

      if ($user instanceof Administrator) {
        $projects = Projects::find(array(
          "conditions" => array("state >= ?", STATE_ARCHIVED),
          "order" => "id"
        ));

        if (is_foreachable($projects)) {
          $total = count($projects);
          $progress = 1;
          foreach ($projects as $project) {
            if ($project instanceof Project) {}

            echo "Exporting project {$progress}/{$total}...";
            $project->exportAsFile($user, false, null, true);
            echo " Done.\n";
            $progress++;
          } // foreach
        } // if

        Users::exportToFile();

        $export_items = "./people.json" . ($projects ? " ./projects" : "");

        exec("cd " . escapeshellarg(WORK_PATH) . " && zip -r system_export.zip {$export_items}");

        $output->printMessage("Export completed at");
      } // if

    } // execute

  } // CLICommandSubscription
