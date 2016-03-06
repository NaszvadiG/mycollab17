<?php

  class MigrateTrackingIndexesImprovement extends AngieModelMigration {

    /**
     * Map Table => indexes (add index if it doesn't exist)
     *
     * @var array
     */
    private $check_indexes = array(
      'estimates' => array(
        'parent_id',
        'created_on',
        'job_type_id'
      ),
      'expenses' => array(
        'parent_id',
        'state',
        'billable_status',
        'created_on'
      ),
      'time_records' => array(
        'parent_id',
        'state',
        'billable_status',
        'created_on'
      ),
      'job_types' => array(
        'is_active'
      )
    );

    /**
     * Up up UP!
     */
    function up(){
      // check and add indexes that are missing
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
            DB::execute("ALTER TABLE `{$table_name}` ADD INDEX {$index_name} (".implode("`,`", $index_columns).")");
          } // if
        } // foreach
      } // foreach

    } // up

  }
