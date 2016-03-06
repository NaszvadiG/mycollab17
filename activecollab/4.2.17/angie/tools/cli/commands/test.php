<?php

  /**
   * Run framework and project tests
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandTest extends CLICommandExecutable {
  
    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('a', 'angie', 'Run only Angie tests'),
      array('b', 'backtrace', 'Print exception backtrace'),
      array('e:', 'exclude:', 'Comma separated list of test cases that need to be skipped'),
    );
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Run framework and project tests (project test can be turned off by providing --angie switch)';
    
    /**
     * Execute command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      $execution_started = microtime(true);
      
      require_once ANGIE_PATH . '/classes/tests/init.php';
      
      define('TESTS_PATH', ANGIE_PATH . '/tests');
      
      if(CLI::initEnvironment($output, 'test')) {
        $output->printMessage(AngieApplication::getName() . ' v' . AngieApplication::getVersion() . ' bootstrapped for test');
        
        $framework_only = $this->getOption(array('a', 'angie'));
        $print_backgrace = $this->getOption(array('b', 'backtrace'));
        
        $test_folders = array(ANGIE_PATH . '/tests', 'framework');
        
        foreach(AngieApplication::getFrameworks() as $framework) {
          $test_folders[] = $framework->getPath() . '/tests';
        } // foreach
        
        if(!$framework_only) {
          foreach(AngieApplication::getEnabledModules() as $module) {
            $test_folders[] = $module->getPath() . '/tests';
          } // foreach
        } // if
        
        $only_cases = $this->getArgument(1);
        
        if($only_cases) {
          $only_cases = explode(',', $only_cases);
        } // if

        $auto_exclude = array();

        if(!in_array('TestEmailBodyProcessor', $only_cases)) {
          $auto_exclude[] = 'TestEmailBodyProcessor';
        }

        if(!in_array('TestRouter', $only_cases)) {
          $auto_exclude[] = 'TestRouter';
        } // if

        $exclude = $this->getOption(array('e', 'exclude'));
        $exclude = $exclude ? explode(',', $exclude) : array();
        
        $test_suite = new TestSuite(AngieApplication::getName() . ' tests');
        foreach($test_folders as $folder_path) {
          $files = get_files($folder_path, 'php');

          if(is_array($files)) {
            foreach($files as $file) {
              $filename = basename($file);

              if(str_ends_with($filename, '.class.php')) {
                $filename = str_replace('.class.php', '', $filename);
              } // if
              
              if(str_starts_with($filename, 'Test')) {
                if($only_cases && !in_array($filename, $only_cases)) {
                  continue;
                } // if

                if(count($exclude) && in_array($filename, $exclude) || in_array($filename, $auto_exclude)) {
                  $output->printMessage("Excluded '$filename' test");
                  continue;
                } // if
                
                $test_suite->addFile($file);
              } // if
            } // foreach
          } // if
        } // foreach

        $test_suite->run(new AngieTestReporter($print_backgrace));
        
        print "Execution time: " . number_format(microtime(true) - $execution_started, 2) . "s\n";
      } // if
    } // execute
  
  }