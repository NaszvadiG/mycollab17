<?php

  /**
   * Create new application instance
   *
   * @package Shepherd.commands
   */
  class CLICommandHelpdeskConversation extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Create/comment conversation from shepherd';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('t:', 'ticket_id:', 'Ticket ID'),
      array('s:', 'subject:', 'Conversation subject'),
      array('m:', 'message:', 'Conversation message'),
      array('u:', 'user:', 'Coma sepparated values email and name "email@domain.com,john doe"'),
      array('a:', 'attachments:', 'Attachments as serialize array (path, filename, type)')
    );


    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);

      $user_values = $this->getOption(array('u', 'user'), null);
      list($user_email, $user_name) = explode(",", $user_values);

      //just in case that shepherd user is replier..
      $user = Users::findByEmail($user_email);
      if(!$user instanceof User) {
        $user = new AnonymousUser($user_name, $user_email);
      } //if
      try {
        $ticket_id = $this->getOption(array('t', 'ticket_id'), null);
        $message = $this->getOption(array('m', 'message'), null);
        $subject = $this->getOption(array('s', 'subject'), null);
        $attachments = $this->getOption(array('a', 'attachments'), null);
        $attachments = $attachments ? unserialize($attachments) : null;

        $conversation = HelpdeskConversations::findByTicketId($ticket_id);

        if($conversation instanceof HelpdeskConversation) {
          //reply
          $additional['is_from_shepherd'] = true;
          $additional['attach_files'] = $attachments ? $attachments : null;
          $conversation->comments()->submit($message, $user, $additional);
         
        } else {
          //new conversation - check this later
          $conversation = new HelpdeskConversation();
          $conversation->setAttributes(array(
            'body' => $message,
            'subject' => $subject,
            'state' => STATE_VISIBLE,
            'status' => HelpdeskConversation::STATUS_NEW,
            'ticket_id' => $ticket_id
          ));

          $conversation->setCreatedBy($user);
          if($attachments) {
            $conversation->attachments()->attachFromArray($attachments);
          } //if
          $conversation->save();
        } //if

        $output->printMessage("{$conversation->getId()}");
      } catch (Error $e) {
        //var_dump($e->getMessage());
        //die();
        throw $e;
      }


    } // execute

  }