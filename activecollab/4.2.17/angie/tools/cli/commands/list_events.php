<?php

  /**
   * Rebuild language index
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandListEvents extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'List all events that can be called grouped by modules';
      
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initProject(null, 'config');
      
      $rules = array(       
        'php' => array(
          array("pattern" => "/event_trigger\('(.*?)'.*?\)/is", "result_index" => 1),
          array("pattern" => '/event_trigger\("(.*?)".*?\)/is', "result_index" => 1),
        )
      );
      
      $extensions = array_keys($rules);
      
      $scan_folders = array(
        'angie' => array(ANGIE_PATH),
      );
      
      $dir = dir(APPLICATION_PATH . '/modules');
      while(($entry = $dir->read()) !== false) {
        if (!in_array($entry, array('.','..'))) {
          if(!is_array($scan_folders[$entry])) {
            $scan_folders[$entry] = array();
          } // if
          $scan_folders[$entry][] = APPLICATION_PATH . "/modules/$entry";
        } // if
      } // while
      
      $output = array();
      
      foreach($scan_folders as $module_name => $folders) {
        $langs = array();
        foreach($folders as $folder) {
          foreach($rules as $extension => $patterns) {
            $files = get_files($folder, $extension, true);
            if(is_foreachable($files)) {
              foreach($files as $file) {
                $content = file_get_contents($file);
                foreach($patterns as $search_pattern) {
                  $tmp_results = null;
              		preg_match_all($search_pattern["pattern"], $content, $tmp_results);
              		foreach($tmp_results[$search_pattern["result_index"]] as $possible_result) {
                  		if ($possible_result && !in_array($possible_result, $langs)) {
                			  $langs[] = $possible_result;
                			} // if
              		} // foreach
              	} // foreach
              } // foreach
            } // if            
          } // foreach
        } // foreach
        if (is_foreachable($langs)) {
          $output[$module_name] = $langs;
        }
      } // foreach
      
      if (is_foreachable($output)) {
        $output_text = '';
        foreach ($output as $module_name => $single_output) {
          $output_text.= "***********************************\n";
          $output_text.= "\t$module_name\n";
          $output_text.= "***********************************\n";
          foreach ($single_output as $event) {
            $output_text.="\t$event\n";
          } // foreach
          $output_text.="\n\n";
        } // foreach

        file_put_contents(DEVELOPMENT_PATH.'/list_events_output.txt', $output_text);
      } // if
    } // execute
  
  }