<?php

  /**
   * BaseInvoiceItemTemplates class
   *
   * @package ActiveCollab.modules.invoicing
   * @subpackage models
   */
  abstract class BaseInvoiceItemTemplates extends DataManager {

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @return string
     */
    static function getModelName($underscore = false) {
      return $underscore ? 'invoice_item_templates' : 'InvoiceItemTemplates';
    } // getModelName

    /**
     * Return name of the table where system will persist model instances
     *
     * @param boolean $with_prefix
     * @return string
     */
    static function getTableName($with_prefix = true) {
      return $with_prefix ? TABLE_PREFIX . 'invoice_item_templates' : 'invoice_item_templates';
    } // getTableName

    /**
     * Return class name of a single instance
     *
     * @return string
     */
    static function getInstanceClassName() {
      return 'InvoiceItemTemplate';
    } // getInstanceClassName

    /**
     * Return whether instance class name should be loaded from a field, or based on table name
     *
     * @return string
     */
    static function getInstanceClassNameFrom() {
      return DataManager::CLASS_NAME_FROM_TABLE;
    } // getInstanceClassNameFrom

    /**
     * Return name of the field from which we will read instance class
     *
     * @return string
     */
    static function getInstanceClassNameFromField() {
      return '';
    } // getInstanceClassNameFrom

    /**
     * Return name of this model
     *
     * @return string
     */
    static function getDefaultOrderBy() {
      return 'description';
    } // getDefaultOrderBy
  
  }