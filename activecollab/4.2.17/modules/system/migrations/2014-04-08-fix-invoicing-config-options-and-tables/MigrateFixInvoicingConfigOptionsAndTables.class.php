<?php

  /**
   * Class MigrateFixInvoicingConfigOptionsAndTables
   *
   * Fix invoicing config options' "module" column or cleanup orhpaned config options if module isn't installed
   */
  class MigrateFixInvoicingConfigOptionsAndTables extends AngieModelMigration {

    /**
     * Invoicing module's current set of config options
     *
     * @var array
     */
    private $config_options = array(
      'prefered_currency',
      'on_invoice_based_on',
      'description_format_grouped_by_task',
      'description_format_grouped_by_project',
      'description_format_grouped_by_job_type',
      'description_format_separate_items',
      'first_record_summary_transformation',
      'second_record_summary_transformation',
      'invoicing_number_pattern',
      'invoicing_number_date_counters',
      'invoicing_number_counter_padding',
      'invoice_template',
      'print_invoices_as',
      'print_proforma_invoices_as',
      'invoicing_default_due',
      'invoice_second_tax_is_enabled',
      'invoice_second_tax_is_compound',
      'invoice_notify_on_payment',
      'invoice_notify_on_cancel',
      'invoice_notify_financial_managers',
      'invoice_notify_financial_manager_ids',
      'invoice_overdue_reminders_enabled',
      'invoice_overdue_reminders_send_first',
      'invoice_overdue_reminders_send_every',
      'invoice_overdue_reminders_first_message',
      'invoice_overdue_reminders_escalation_enabled',
      'invoice_overdue_reminders_escalation_messages',
      'invoice_overdue_reminders_dont_send_to',
    );

    /**
     * Invoicing module's current set of tables
     *
     * @var array
     */
    private $table_names = array(
      'invoice_objects',
      'invoice_object_items',
      'invoice_item_templates',
      'invoice_note_templates',
      'invoice_related_records',
      'tax_rates'
    );

    /**
     * Upgrade
     */
    function up() {
      $config_options_table = TABLE_PREFIX . "config_options";

      $invoicing_installed = DB::executeFirstCell("SELECT COUNT(name) FROM " . TABLE_PREFIX . "modules WHERE name = 'invoicing'") > 0;

      if ($invoicing_installed) {
        DB::execute("UPDATE {$config_options_table} SET module = 'invoicing' WHERE name IN (?)", $this->config_options);
      } else {
        DB::execute("DELETE FROM {$config_options_table} WHERE name IN (?)", $this->config_options);

        // add prefix to table names
        array_walk($this->table_names, function(&$table_name) {
          $table_name = TABLE_PREFIX . $table_name;
        });

        DB::execute("DROP TABLE IF EXISTS " . implode(", ", $this->table_names));
      } // if

      AngieApplication::cache()->remove("config_options");

      // schedule index rebuild because we need to fix object contexts for users, due to another issue that's fixed
      // in this release
      $this->scheduleIndexesRebuild();
    } // up
    
  } 