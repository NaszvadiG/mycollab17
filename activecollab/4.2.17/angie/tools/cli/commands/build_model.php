<?php

  /**
   * Run model generator
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandBuildModel extends CLICommandGenerator {
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Build application model';
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $specific = $this->getArgument(1);
      
      if($specific) {
        $specific = explode(',', $specific);
      } else {
        if(!$output->ask("Are you sure that you want to rebuild ALL models from ALL modules?")) {
          $output->abortWithMessage("Operation aborted");
        } // if
      } // if

      AngieApplicationModel::load(explode(',', APPLICATION_FRAMEWORKS), explode(',', APPLICATION_MODULES));

      $generated_models = array();

      foreach(AngieApplicationModel::getModelBuilders() as $model_name => $model_builder) {
        if(empty($specific) || (is_array($specific) && in_array($model_name, $specific))) {
          $this->generateModelFiles($model_name, $model_builder, $output);

          $inject_into = $model_builder->getDestinationModuleName();

          if(isset($generated_models[$inject_into])) {
            $generated_models[$inject_into][] = $model_name;
          } else {
            $generated_models[$inject_into] = array($model_name);
          } // if
        } // if
      } // foreach

      if(empty($specific)) {
        foreach($generated_models as $module => $models) {
          $this->generateModelAutoloader($models, $module, $output);
        } // foreach
      } // if
    } // execute
  
  }