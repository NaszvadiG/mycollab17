<?php

  /**
   * Change account status
   *
   * Change account status on demand
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandChangeAccountStatus extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Change account status - on demand modul';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('s:', 'status:', 'New Account Status'),
      array('d:', 'date', 'Date when new status will expire')
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

      $status = $this->getOption(array('s', 'status'));
      if(empty($status)) {
        $output->abortWithMessage('Status value is required');
      } // if

      //check if status is one of the possible
      if(!array_key_exists($status, OnDemand::getStatusMap())) {
        $output->abortWithMessage('Invalid status');
      } // if

      $date = $this->getOption(array('d', 'date')); // date is not required for all statuses; if not provided then set it as 'null'
      $expires_on = $date ? new DateTimeValue($date) : null;

      OnDemand::setAccountStatus($status, $expires_on);
      if(OnDemand::subscriptionExists() && in_array($status, array(OnDemand::STATUS_PENDING_DELETION, Ondemand::STATUS_SUSPENDED_PAID, Ondemand::STATUS_RETIRED_PAID))) {
        OnDemand::cancelSubscription();
      } //if

      AngieApplication::cache()->clear();

      $output->printMessage('Account status changed to ' . OnDemand::getVerboseAccountStatus());

    } // execute

  } // CLICommandChangeAccountStatus
