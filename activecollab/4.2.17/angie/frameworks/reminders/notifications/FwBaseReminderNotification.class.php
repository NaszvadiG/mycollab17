<?php

  /**
   * Base reminder notification class
   *
   * @package angie.frameworks.reminders
   * @subpackage notifications
   */
  abstract class FwBaseReminderNotification extends Notification {

    /**
     * Return reminder instance
     *
     * @return Reminder
     */
    function getReminder() {
      return DataObjectPool::get('Reminder', $this->getAdditionalProperty('reminder_id'));
    } // getReminder

    /**
     * Set reminder instance
     *
     * @param Reminder $reminder
     * @return BaseReminderNotification
     */
    function &setReminder(Reminder $reminder) {
      $this->setAdditionalProperty('reminder_id', $reminder->getId());

      return $this;
    } // setReminder

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'reminder' => $this->getReminder(),
      );
    } // getAdditionalTemplateVars

    /**
     * This notification should not be displayed in web interface
     *
     * @param NotificationChannel $channel
     * @param IUser $recipient
     * @return bool
     */
    function isThisNotificationVisibleInChannel(NotificationChannel $channel, IUser $recipient) {
      if($channel instanceof EmailNotificationChannel) {
        return true; // Always deliver email notifications about reminders
      } // if

      return parent::isThisNotificationVisibleInChannel($channel, $recipient);
    } // isThisNotificationVisibleInChannel

  }
