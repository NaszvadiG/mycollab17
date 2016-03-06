<?php

  /**
   * Remove backup tables created during v3.3 to v4 migration and invoicing enhancements
   *
   * @package activeCollab.modules.invoicing
   * @subpackage migrations
   */
  class MigrateCleanUpInvoicingBackupTables extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->dropTable('backup_invoice_items', 'backup_invoice_related_records', 'backup_invoices', 'backup_payments', 'backup_quote_items', 'backup_quotes', 'backup_recurring_profile_items', 'backup_recurring_profiles');
    } // up

  }