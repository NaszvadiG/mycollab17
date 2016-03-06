<?php
  /**
   * Drop in-app help
   *
   * @package angie
   * @subpackage migrations
   */
  class MigrateDropInAppHelp extends AngieModelMigration
  {
    /**
     * Migrate up
     */
    function up()
    {
      $this->removeConfigOption('help_search_index_version');
      $this->dropTable('search_index_for_help');
    }
  }