<?php

  /**
   * New todo list notification
   *
   * @package activeCollab.modules.todo
   * @subpackage notifications
   */
  class NewTodoListNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("To do List ':name' has been created", array(
        'name' => $this->getParent() instanceof TodoList ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

  }