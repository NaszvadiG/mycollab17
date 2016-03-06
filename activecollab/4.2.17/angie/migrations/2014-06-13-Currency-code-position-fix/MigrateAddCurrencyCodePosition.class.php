<?php

  /**
   * Add 'code_position' column to currencies table
   *
   * @package angie
   * @subpackage migrations
   */
  class MigrateAddCurrencyCodePosition extends AngieModelMigration {

    /**
     * Migrate up
     */
    function up() {
      $this->execute("ALTER TABLE " . TABLE_PREFIX . "currencies ADD code_position ENUM('left', 'right') DEFAULT 'left'");
    } // up

  }