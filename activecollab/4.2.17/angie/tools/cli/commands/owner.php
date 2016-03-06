<?php

  /**
   * Manage admins
   *
   * Manage admins via cli
   *
   * @package angie.tools.cli.commands
   */
  class CLICommandOwner extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Manage account owner';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('a:', 'action', 'Action'),
      array('i:', 'id:', 'Id'),
      array('p:', 'password:','Password'),
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
      CLI::initMailer();

      $action = $this->getOption(array('a', 'action'));
      switch($action) {
        case 'change':
          $id = $this->getOption(array('i', 'id'));
          if(!$id) {
            $output->abortWithMessage('ID is required for this action');
          } //if
          $user = Users::findById($id);
          if(!$user instanceof User) {
            $output->abortWithMessage('Can\'t find user with specific Id');
          } //if
          if(!$user->isAdministrator()) {
            $output->abortWithMessage('Specific user is not administrator');
          } //if
          ConfigOptions::setValue('on_demand_account_owner_id', $user->getId());

          break;
        case 'reset-password':
          $password = $this->getOption(array('p', 'password'));
          if(!$password) {
            //if not provided, generate password
            $password = Authentication::getPasswordPolicy()->generatePassword();
          } //if
          if(strlen(trim($password)) < 3) {
            $output->abortWithMessage('Password is required and has to be at least 3 letters long');
          } //if

          $user = OnDemand::getAccountOwner();
          if(!$user instanceof User) {
            $output->abortWithMessage('Can\'t find account owner');
          } //if
          $user->setPassword($password);
          $user->save();

          AngieApplication::notifications()
            ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/password_changed', $user)
            ->setNewPassword($password)
            ->sendToUsers($user);

          break;
        default:
          $owner = OnDemand::getAccountOwner();
            if ($owner instanceof User) {
              $output->printMessage("{$owner->getDisplayName()} <{$owner->getEmail()}>");
            } else {
              $output->abortWithMessage("Account owner does not exist");
            } // if
          // $output->abortWithMessage('Provided action not recognized');
          break;
      } //switch

    } // execute

  }