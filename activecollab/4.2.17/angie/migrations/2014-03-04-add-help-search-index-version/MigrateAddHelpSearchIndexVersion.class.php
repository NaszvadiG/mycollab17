<?php

  /**
   * Add help search index version
   *
   * @package angie.migrations
   */
  class MigrateAddHelpSearchIndexVersion extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->addConfigOption('help_search_index_version');
    } // up

    /**
     * Migrate down
     */
    function down() {
      $this->removeConfigOption('help_search_index_version');
    } // down

  }