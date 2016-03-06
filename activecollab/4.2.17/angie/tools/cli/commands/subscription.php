<?php

  /**
   * Subscription received
   *
   * Subscription changed/added
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandSubscription extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Working with subscription';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('s:', 'subscription:', 'Subscription Key'),
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

      $subscription_key = $this->getOption(array('s', 'subscription'));
      if(empty($subscription_key)) {
        $output->abortWithMessage('Subscription Key value is required');
      } // if

      OnDemand::refreshSubscription($subscription_key);

      $output->printMessage('Active Subscription changed');

    } // execute

  } // CLICommandSubscription
