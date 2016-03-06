<?php

  /**
   * Init On Demand module
   *
   * Init On Demand via cli
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandInitOnDemand extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Init On Deman module for instance';

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

      if(AngieApplication::isOnDemand()) {
        $output->abortWithMessage('On Demand module already installed');
      } //if

      $on_demand_module = AngieApplication::getModule('on_demand', false);
      if($on_demand_module instanceof AngieModule) {
        $on_demand_module->install();
      } //if

      $users_table = TABLE_PREFIX . 'users';

      $admins_ids = Users::findAdministratorsIds();
      $owner_company = Companies::findOwnerCompany();

      $owner = DB::executeFirstRow('SELECT id, first_name, last_name, email FROM ' . $users_table . ' WHERE id IN (?) AND company_id = ?', $admins_ids, $owner_company->getId());
      if(!$owner) {
        $owner = DB::executeFirstRow('SELECT id, first_name, last_name, email FROM ' . $users_table . ' WHERE id IN (?)', $admins_ids);
      } //if

      ConfigOptions::setValue('on_demand_account_owner_id', $owner['id']);

      if($owner['first_name'] || $owner['last_name']) {
        $display_name = trim($owner['first_name'] . ' ' . $owner['last_name']);
      } else {
        $display_name = $owner['email'];
      } //if

      //return owner id so shepard know who is account owner
      $owner = $display_name . ' ' . '<' . $owner['email'] . '>';
      $output->printMessage($owner);

    } // execute

  } // CLICommandChangeAccountStatus
