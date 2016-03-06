<?php

  /**
   * Update bookmark and youtube video favorites
   *
   * @package activeCollab.modules.system
   * @subpackage migrations
   */
  class MigrateBookmarkYouTubeFavorites extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->execute('UPDATE ' . TABLE_PREFIX . 'favorites SET parent_type = ? WHERE parent_type IN (?)', 'Discussion', array('Bookmark', 'YouTubeVideo'));
    } // up

  }