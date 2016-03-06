<?php

  /**
   * Rebuild language index
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandCountLines extends CLICommandExecutable {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Count lines in project';
    
    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('l', 'list', 'List All Files'),
      array('f:', 'file:', 'List All Files'),
    );
    
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $list_files = $this->getOption(array('l', 'list'));
      $output_file = $this->getOption(array('f', 'file'));

      $extensions = array(
        'tpl',
        'php',
        'css',
        'js',
        'html',
      );
      
      $scan_folders = array(
        'angie' => array(
          'include' => array(ANGIE_PATH),
          'exclude' => array(
						ANGIE_PATH . '/vendor/',
            ANGIE_PATH . '/classes/xml/',
            ANGIE_PATH . '/classes/UTF8Converter/',
            ANGIE_PATH . '/classes/tcpdf/',
            ANGIE_PATH . '/classes/swiftmailer/',
            ANGIE_PATH . '/classes/smarty/',
            ANGIE_PATH . '/classes/json/',
            ANGIE_PATH . '/classes/icalendar/',
            ANGIE_PATH . '/classes/http/',
            ANGIE_PATH . '/classes/fpdf/',
            ANGIE_PATH . '/classes/diff/',
            ANGIE_PATH . '/classes/captcha/',
          ),          
        ),
        'activecollab' => array(
          'include' => array(ROOT),
          'exclude' => array(
						ROOT . '/localization',
          ),
        ),
        'instance' => array(
          'include' => array(ENVIRONMENT_PATH),
          'exclude' => array(
						ENVIRONMENT_PATH . '/work',
            ENVIRONMENT_PATH . '/compile',
            ENVIRONMENT_PATH . '/cache',
            ENVIRONMENT_PATH . '/public/assets',
          ),
        ),
      );
      
      $parse_files = array();
      if (is_foreachable($scan_folders)) {
        foreach ($scan_folders as $scan_folder) {
        	$include_folders = array_var($scan_folder, 'include');
        	$exclude_folders = array_var($scan_folder, 'exclude');
        	
          if (is_foreachable($include_folders)) {
            foreach ($include_folders as $include_folder) {
            	$files = get_files($include_folder, $extensions, true);
            	
            	if (is_foreachable($files)) {
            	  foreach ($files as $file) {
                	if (is_foreachable($exclude_folders)) {
                	  $exclude_file = false;
                	  foreach ($exclude_folders as $exclude_folder) {
                	    if (strpos($file,$exclude_folder) === 0) {
                	      $exclude_file = true;
                	    } // if
                	  } // foreach
                	  if (!$exclude_file) {
                	    $parse_files[] = $file;  
                	  }
                	} else {
                	  $parse_files[] = $file;
                	} // if
            	  } // if
            	}
            } // foreach
          } // if
        } // for
      } // if
      
      $count_clientside_lines = 0;
      $count_serverside_lines = 0;
      $count_clientside = 0;
      $count_serverside = 0;
      
      if ($output_file) {
        $fhandle = fopen($output_file, 'w+');
      } // if
      
      if (is_foreachable($parse_files)) {
        foreach ($parse_files as $parse_file) {
        	$pathinfo = pathinfo($parse_file);
          
        	if (in_array(array_var($pathinfo, 'extension'), array('css','js','html'))) {
        	  $count_clientside_lines += count(file($parse_file));
        	  $count_clientside ++;
        	} else {
        	  $count_serverside_lines += count(file($parse_file));
        	  $count_serverside ++;
        	} // if  
        	      	
      	  if ($list_files) {
        	  if ($output_file && $fhandle) {
        	    $result = fwrite($fhandle, "\n$parse_file");
        	  } // if
      	    echo "\n$parse_file";
      	  } // if
        } // foreach
      } // if
      
      $total_files = $count_clientside + $count_serverside;
      $total_lines = $count_clientside_lines + $count_serverside_lines;
      
      if ($list_files) {
        if ($output_file && $fhandle) {
          fwrite($fhandle, "\n");
        }
        echo "\n";
      } // if
      echo "\nClientside files: $count_clientside\n";
      echo "Lines in Clientside files: $count_clientside_lines\n\n";
      echo "Serverside files: $count_serverside\n";
      echo "Lines in Serverside files: $count_serverside_lines\n\n";
      echo "Total files: $total_files\n";
      echo "Total lines: $total_lines\n\n";
      
      if ($output_file && $fhandle) {
        fwrite($fhandle, "\nClientside files: $count_clientside\n");
        fwrite($fhandle, "Lines in Clientside files: $count_clientside_lines\n\n");
        fwrite($fhandle, "Serverside files: $count_serverside\n");
        fwrite($fhandle, "Lines in Serverside files: $count_serverside_lines\n\n");
        fwrite($fhandle, "Total files: $total_files\n");
        fwrite($fhandle, "Total lines: $total_lines");
        fclose($fhandle);
      } // if
    } // execute
  
  }