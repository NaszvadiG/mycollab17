<?php

  /**
   * Class MigrateChangeDiffToLongText
   *
   * @package activecollab.modules.source
   * @subpackage migrations
   */
  class MigrateChangeDiffToLongText extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'source_commits CHANGE `diff` `diff` LONGTEXT NULL');
    } // up

    /**
     * Migrate down
     */
    function down() {
      DB::execute('ALTER TABLE ' . TABLE_PREFIX . 'source_commits CHANGE `diff` `diff` TEXT NULL');
    } // down
    
  } 