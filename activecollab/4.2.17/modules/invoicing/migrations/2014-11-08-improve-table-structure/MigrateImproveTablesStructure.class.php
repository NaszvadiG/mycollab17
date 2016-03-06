<?php

  /**
   * Drop columns that aren't being used, add indexes, etc
   */
  class MigrateImproveTablesStructure {

    /**
     * Map Table => indexes (add index if it doesn't exist)
     *
     * @var array
     */
    private $check_indexes = array(
      'invoice_objects' => array(
        'state',
        'status',
        'date_field_1', // due on
        'date_field_2', // issued on
        'closed_on'
      ),
      'invoice_related_records' => array(
        'parent_id'
      )
    );

    function up(){
      DB::execute("ALTER TABLE " . TABLE_PREFIX . 'invoice_item_templates DROP `position`');
      DB::execute("ALTER TABLE " . TABLE_PREFIX . 'invoice_note_templates DROP `position`');

      foreach ($this->check_indexes as $table => $indexes) {
        $table_name = TABLE_PREFIX . $table;
        $existing_indexes = (array) DB::listTableIndexes($table_name);

        foreach ($indexes as $key => $value) {
          if (is_int($key)) {
            $index_name = $value;
            $index_columns = array($value);
          } elseif (is_string($key) && is_array($value) && count($value)) {
            $index_name = $key;
            $index_columns = $value;
          } // if

          if (isset($index_name) && isset($index_columns) && !in_array($index_name, $existing_indexes)) {
            DB::execute("ALTER TABLE `{$table_name}` ADD INDEX (`".implode("`,`", $index_columns)."`) `{$index_name}`");
          } // if
        } // foreach
      } // foreach
    } // up

  }
