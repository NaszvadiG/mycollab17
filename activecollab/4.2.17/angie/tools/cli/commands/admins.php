<?php

  /**
   * Manage admins
   *
   * Manage admins via cli
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandAdmins extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Manage administrators';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('a:', 'action', 'Action'),
      array('f:', 'first_name:', 'First Name'),
      array('l:', 'last_name:', 'last Name'),
      array('p:', 'password:','Password'),
      array('e:', 'email:', 'Email'),
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

      $action = $this->getOption(array('a', 'action'));
      switch($action) {
        case 'list':
          $admins = Users::findAdministrators();
          if(is_foreachable($admins)) {
            $list = '';
            foreach($admins as $admin) {
              if($admin instanceof User && $admin->isActive()) {
                $list .= $admin->getId() . ',' . $admin->getEmail() . ',' . $admin->getDisplayName() . PHP_EOL;
              } //if
            } //foreach
            $output->printMessage($list);
          } //if
          break;
        case 'remove':
          $email = $this->getOption(array('e', 'email'));
          if(!$email) {
            $output->abortWithMessage('Email is required for this action');
          } //if
          $user = Users::findByEmail($email);
          if($user instanceof User) {
            $user->state()->delete();
          } else {
            $output->abortWithMessage('Can\'t find user ' . $email);
          } // if

          break;
        case 'add':
          $email = $this->getOption(array('e', 'email'));
          if(empty($email) || !is_valid_email($email)) {
            $output->abortWithMessage('Email must be valid email address');
          } //if

          $password = $this->getOption(array('p', 'password'));
          if(empty($password) || strlen(trim($password)) < 3) {
            $output->abortWithMessage('Password is required and has to be at least 3 letters long');
          } //if

          Users::addAdministrator($email, $password, array(
            'first_name' => $this->getOption(array('f', 'first_name')),
            'last_name' => $this->getOption(array('l', 'last_name')),
          ));

          break;
        case 'reset-password':
          $email = $this->getOption(array('e', 'email'));
          if(!$email) {
            $output->abortWithMessage('Email is required for this action');
          } //if

          $password = $this->getOption(array('p', 'password'));
          if(!$password) {
            //if not provided, generate password
            $password = Authentication::getPasswordPolicy()->generatePassword();
          } //if
          if(strlen(trim($password)) < 3) {
            $output->abortWithMessage('Password is required and has to be at least 3 letters long');
          } //if

          $user = users::findByEmail($email);
          if(!($user instanceof User)) {
            $output->abortWithMessage("Can't find user with specific ID");
          } //if
          $user->setPassword($password);
          $user->save();

          CLI::initMailer();

          AngieApplication::notifications()
            ->notifyAbout(AUTHENTICATION_FRAMEWORK_INJECT_INTO . '/password_changed', $user)
            ->setNewPassword($password)
            ->sendToUsers($user);

          break;
        default:
          $output->abortWithMessage('Provided action not recognized');
          break;
      } //switch

    } // execute

  }