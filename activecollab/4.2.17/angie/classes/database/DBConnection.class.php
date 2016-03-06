<?php

  /**
   * Abstract DBConnection class
   *
   * @package angie.db.connections
   */
  abstract class DBConnection {
    
    /**
     * Count of executed queries
     *
     * @var integer
     */
    private $query_counter = 0;
    
    /**
     * Return number of queries executed with this connection
     *
     * @return integer
     */
    function getQueryCount() {
      return $this->query_counter;
    } // getQueryCount
    
    /**
     * Query log keeps last 10 queries that are executed in the current request
     *
     * @var array
     */
    private $query_log = array();
    
    /**
     * Add given SQL query to query log
     * 
     * @param string $sql
     * @return string
     */
    protected function addToQueryLog($sql) {
      $this->query_counter++;
      
      if(count($this->query_log) == 10) {
        array_shift($this->query_log);
      } // if
      
      $this->query_log[] = $sql;
    } // addToQueryLog
    
    /**
     * Return content of query log
     * 
     * @return array
     */
    function getQueryLog() {
      return $this->query_log;
    } // getQueryLog
    
    /**
     * Return last query from query log
     * 
     * @return string
     */
    function getLastQuery() {
      $count = count($this->query_log);
      
      if($count) {
        return $this->query_log[$count - 1];
      } else {
        return null;
      } // if
    } // getLastQuery
    
    /**
     * TRUE if we have established connection to the database
     *
     * @var boolean
     */
    protected $is_connected = false;
    
    /**
     * Open connection to the server
     *
     * @param array $parameters
     */
    abstract function connect($parameters);
    
    /**
     * Reconnect to the server
     */
    abstract function reconnect();
    
    /**
     * Disconnect from the server
     */
    abstract function disconnect();
    
    /**
     * Returns true if we have a connection to the database
     *
     * @return boolean
     */
    function isConnected() {
      return $this->is_connected;
    } // isConnected
    
    /**
     * Return number of affected rows
     *
     * @param void
     * @return integer
     */
    abstract function affectedRows();
    
    /**
     * Return last insert ID
     *
     * @param void
     * @return integer
     */
    abstract function lastInsertId();
    
    /**
     * Begin transaction
     *
     * @param void
     * @return boolean
     */
    abstract function beginWork();
    
    /**
     * Commit transaction
     *
     * @param void
     * @return boolean
     */
    abstract function commit();
    
    /**
     * Rollback transaction
     *
     * @return boolean
     */
    abstract function rollback();
    
    /**
     * Return true if system is in transaction
     * 
     * @return boolean
     */
    abstract function inTransaction();
    
    /**
     * Execute SQL query and optionally prepare arguments
     *
     * @param string $sql
     * @param array $arguments
     * @param integer $load
     * @param integer $return_mode
     * @param string $return_class_or_field
     * @return DBResult
     * @throws DBQueryError
     */
    abstract function execute($sql, $arguments = null, $load = DB::LOAD_ALL_ROWS, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null);
    
    /**
     * Escape value and prepare it for use in the query
     *
     * @param string $unescaped
     * @return string
     */
    abstract function escape($unescaped);
    
    /**
     * Escape table field name
     *
     * @param string $unescaped
     * @return string
     */
    abstract function escapeFieldName($unescaped);
    
    /**
     * Escape table name
     *
     * @param string $unescaped
     * @return string
     */
    abstract function escapeTableName($unescaped);
    
    /**
     * Execute query and return first row. If there is no first row NULL is returned
     *
     * @param string $sql
     * @param array $arguments
     * @param int $return_mode
     * @param null $return_class_or_field
     * @return array
     */
    public function executeFirstRow($sql, $arguments = null, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null) {
      return $this->execute($sql, $arguments, DB::LOAD_FIRST_ROW, $return_mode, $return_class_or_field);
    } // execute_one
    
    /**
     * Return values from the first column as an array
     *
     * @param string $sql
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    public function executeFirstColumn($sql, $arguments = null) {
      return $this->execute($sql, $arguments, DB::LOAD_FIRST_COLUMN);
    } // executeFirstColumn
    
    /**
     * Return value from the first cell
     *
     * @param string $sql
     * @param array $arguments
     * @return mixed
     * @throws DBQueryError
     */
    public function executeFirstCell($sql, $arguments = null) {
      return $this->execute($sql, $arguments, DB::LOAD_FIRST_CELL);
    } // executeFirstCell
    
    /**
     * Prepare SQL (replace ? with data from $arguments array)
     *
     * @param string $sql
     * @param array $arguments
     * @return string
     */
    function prepare($sql, $arguments = null) {
      if(is_foreachable($arguments)) {
        $offset = 0;
        foreach($arguments as $argument) {
          $question_mark_pos = strpos_utf($sql, '?', $offset);
          if($question_mark_pos !== false) {
            $escaped = $this->escape($argument);
            $escaped_len = strlen_utf($escaped);
            
            $sql = substr_utf($sql, 0, $question_mark_pos) . $escaped . substr_utf($sql, $question_mark_pos + 1, strlen_utf($sql));
            
            $offset = $question_mark_pos + $escaped_len;
          } // if
        } // foreach
      } // if
      
      return $sql;
    } // prepare

    /**
     * Convert row to expected result
     *
     * @param array $row
     * @param integer $return_mode
     * @param integer $return_class_or_field
     * @return mixed
     * @throws InvalidInstanceError
     */
    protected function rowToResult($row, $return_mode, $return_class_or_field) {
      switch($return_mode) {

        // We have class name provided as a parameter
        case DB::RETURN_OBJECT_BY_CLASS:
          $class_name = $return_class_or_field;

          $object = new $class_name();
          if($object instanceof Dataobject) {
            $object->loadFromRow($row);
            return $object;
          } else {
            throw new InvalidInstanceError('object', $object, 'DataObject');
          } // if

        // Get class from field value, and contruct and hidrate object
        case DB::RETURN_OBJECT_BY_FIELD:
          $class_name = $row[$return_class_or_field];

          $object = new $class_name();
          if($object instanceof DataObject) {
            $object->loadFromRow($row);
            return $object;
          } else {
            throw new InvalidInstanceError('object', $object, 'DataObject');
          } // if

        // Plain assoc array
        default:
          return $row;

      } // switch
    } // rowToResult
    
    // ---------------------------------------------------
    //  Table management
    // ---------------------------------------------------
    
    /**
     * Returns true if table $name exists
     * 
     * @param string $name
     * @return boolean
     */
    abstract function tableExists($name);
    
    /**
     * Create new table instance
     *
     * @param string $name
     */
    abstract function createTable($name);
    
    /**
     * Load table information
     *
     * @param boolean $name
     */
    abstract function loadTable($name);
    
    /**
     * Return array of tables from the database
     *
     * @param string $prefix
     * @return array
     */
    abstract function listTables($prefix = null);
    
    /**
     * Return list of fields from given table
     *
     * @param string $table_name
     * @return array
     */
    abstract function listTableFields($table_name);
    
    /**
     * Drop one or more tables
     *
     * @param array $tables
     * @param string $prefix
     */
    abstract function dropTables($tables, $prefix = '');

    /**
     * List indexes form a given table name
     *
     * @abstract
     * @param $table_name
     * @return array
     */
    abstract function listTableIndexes($table_name);
    
    /**
     * Drop all tables from database
     *
     * @return boolean
     */
    abstract function clearDatabase();
    
    // ---------------------------------------------------
    //  File export / import
    // ---------------------------------------------------
    
    /**
     * Do a database dump of specified tables
     * 
     * If $table_name is empty it will dump all tables in current database
     *
     * @param array $tables
     * @param string $output_file
     * @param boolean $dump_structure
     * @param boolean $dump_data
     */
    abstract function exportToFile($tables, $output_file, $dump_structure = true, $dump_data = true);
    
    // ---------------------------------------------------
    //  Server variables
    // ---------------------------------------------------
    
    /**
     * Get server variable value
     *
     * @param string $variable_name
     * @return mixed
     */
    abstract function getServerVariable($variable_name);
    
    /**
     * Return version of the server
     *
     * @return string
     */
    abstract function getServerVersion();
    
  }