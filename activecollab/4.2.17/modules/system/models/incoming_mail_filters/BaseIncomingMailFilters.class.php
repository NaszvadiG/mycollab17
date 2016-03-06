<?php

  /**
   * BaseIncomingMailFilters class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  abstract class BaseIncomingMailFilters extends DataManager {

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @return string
     */
    static function getModelName($underscore = false) {
      return $underscore ? 'incoming_mail_filters' : 'IncomingMailFilters';
    } // getModelName

    /**
     * Return name of the table where system will persist model instances
     *
     * @param boolean $with_prefix
     * @return string
     */
    static function getTableName($with_prefix = true) {
      return $with_prefix ? TABLE_PREFIX . 'incoming_mail_filters' : 'incoming_mail_filters';
    } // getTableName

    /**
     * Return class name of a single instance
     *
     * @return string
     */
    static function getInstanceClassName() {
      return 'IncomingMailFilter';
    } // getInstanceClassName

    /**
     * Return whether instance class name should be loaded from a field, or based on table name
     *
     * @return string
     */
    static function getInstanceClassNameFrom() {
      return DataManager::CLASS_NAME_FROM_FIELD;
    } // getInstanceClassNameFrom

    /**
     * Return name of the field from which we will read instance class
     *
     * @return string
     */
    static function getInstanceClassNameFromField() {
      return 'type';
    } // getInstanceClassNameFrom

    /**
     * Return name of this model
     *
     * @return string
     */
    static function getDefaultOrderBy() {
      return 'position';
    } // getDefaultOrderBy
  
  }