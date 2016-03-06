<?php

  class MigrateSystemModuleIndexesImprovement extends AngieModelMigration {

    /**
     * Map Table => indexes (add index if it doesn't exist)
     *
     * @var array
     */
    private $check_indexes = array(
      'announcements' => array(
        'expiration_type',
        'target_type',
        'is_enabled',
        'position'
      ),
      'announcement_target_ids' => array(
        'target_id',
        'announcement_id'
      ),
      'announcement_dismissals' => array(
        'user_id',
        'announcement_id'
      ),
      'comments' => array(
        'created_on',
        'parent' => array(
          'parent_type',
          'parent_id'
        ),
        'state'
      ),
      'attachments' => array(
        'parent' => array(
          'parent_type',
          'parent_id'
        ),
        'state'
      ),
      'code_snippets' => array(
        'parent' => array(
          'parent_type',
          'parent_id'
        )
      ),
      'day_offs' => array(
        'event_date'
      ),
      'companies' => array(
        'state'
      ),
      'modification_logs' => array(
        'created_by_id'
      ),
      'related_tasks' => array(
        'related_task_id'
      ),
      'subtasks' => array(
        'completed_by_id',
        'created_by_id',
        'state'
      ),
      'users' => array(
        'state'
      )
    );

    /**
     * Map Table => Indexes (remove index if it exists)
     *
     * @var array
     */
    private $remove_indexes = array(
      "announcements" => array(
        'subject' // never used
      ),
      "attachments" => array(
        "parent_id" // never used alone, always in composite parent_type + parent_id
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
            DB::execute("ALTER TABLE `{$table_name}` ADD INDEX `{$index_name}` (`".implode("`,`", $index_columns)."`)");
          } // if
        } // foreach
      } // foreach

      // remove useless indexes
      foreach ($this->remove_indexes as $table => $indexes) {
        $table_name = TABLE_PREFIX . $table;
        $existing_indexes = (array) DB::listTableIndexes($table_name);

        foreach ($indexes as $index) {
          if (in_array($index, $existing_indexes)) {
            DB::execute("ALTER TABLE `{$table_name}` DROP INDEX {$index}");
          } // if
        } // foreach
      } // foreach

    } // up

  }
