<?php

  /**
   * Add config options required for displaying release notes and upgraded warnings before running auto-upgrade
   *
   * @package activecollab.modules.system
   * @subpackage migrations
   */
  class MigrateAddConfigOptionsForReleaseNotes extends AngieModelMigration {

    /**
     * Add options
     */
    function up() {
      DB::execute("INSERT INTO ".TABLE_PREFIX."config_options (name, module, value) VALUES (?, ?, ?), (?, ?, ?)", "release_notes", "system", "N;", "upgrade_warnings", "system", "N;");
      AngieApplication::cache()->remove('config_options');
    } // up

    /**
     * Remove options
     */
    function down() {
      DB::execute("DELETE FROM " . TABLE_PREFIX . "config_options WHERE name IN ('release_notes', 'upgrade_warnings')");
      AngieApplication::cache()->remove('config_options');
    } // down
    
  }
