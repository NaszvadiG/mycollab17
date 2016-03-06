<?php

  /**
   * Database connection
   *
   * @package angie.library.database
   * @subpackage mysql
   */
  class MySQLDBConnection extends DBConnection {

    /**
     * MySQLi connection
     *
     * @var MySQLi
     */
    protected $link;

    /**
     * Cached connection parameters
     *
     * @var array
     */
    protected $connection_parameters;
    
    /**
     * Construct MySQLDBConnection instance
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param boolean $persist
     * @param string $charset
     */
    function __construct($host, $user, $pass, $database, $persist = false, $charset = null) {
      $this->connect(array(
        'host' => $host, 
        'user' => $user, 
        'pass' => $pass, 
        'db_name' => $database, 
        'persist' => $persist, 
        'charset' => $charset
      ));
    } // __construct
    
    /**
     * Connect to database
     *
     * @param array $parameters
     * @return boolean
     * @throws DBConnectError
     */
    function connect($parameters) {
      $this->link = new MySQLi($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['db_name']);

      if($this->link->connect_errno) {
        throw new DBConnectError($parameters['host'], $parameters['user'], $parameters['pass'], $parameters['db_name'], 'Failed to select database. Reason: ' . $this->link->connect_error);
      } // if

      $this->link->set_charset(DB_CHARSET);

      $this->connection_parameters = $parameters;
      $this->is_connected = true;
    } // connect
    
    /**
     * Reopen connection, in case that connection has been lost
     *
     * @throws DBReconnectError
     */
    function reconnect() {
      if($this->connection_parameters && is_foreachable($this->connection_parameters)) {
        $this->connect($this->connection_parameters);
      } else {
        throw new DBReconnectError('Connection parameters not found');
      } // if
    } // reconnect
    
    /**
     * Disconnect
     *
     * @return boolean
     */
    function disconnect() {
      if($this->link instanceof MySQLi) {
        $this->link->close();
      } // if
    } // disconnect

    /**
     * Execute SQL query
     *
     * @param string $sql
     * @param mixed $arguments
     * @param int $load
     * @param int $return_mode
     * @param string $return_class_or_field
     * @return array|bool|DBResult|mixed|MySQLDBResult|null
     * @throws DBQueryError
     * @throws DBNotConnectedError
     * @throws Exception
     */
    function execute($sql, $arguments = null, $load = DB::LOAD_ALL_ROWS, $return_mode = DB::RETURN_ARRAY, $return_class_or_field = null) {
      $log_query_execution_details = AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment();
      $query_result = $this->executeQuery($sql, $arguments);

      // Handle query error
      if($query_result === false) {
        if($log_query_execution_details) {
          Logger::log('SQL error. MySQL said: ' . $this->link->error . "\n($sql)", Logger::ERROR, 'sql');
        } // if

        switch($this->link->errno) {

          // Non-transactional tables not rolled back!
          case 1196:
            if($log_query_execution_details) {
              Logger::log('Non-transactional tables not rolled back!', Logger::WARNING, 'sql');
            } // if

            return null;

          // Server gone away
          case 2006:
          case 2013:
            $query_result = $this->handleMySqlGoneAway($sql, $arguments, $log_query_execution_details); break;

          // Deadlock detection and retry
          case 1213:
            $query_result = $this->handleDeadlock($sql, $arguments, $log_query_execution_details); break;

          // Other error
          default:
            throw new DBQueryError($sql, $this->link->errno, $this->link->error);

        } // switch
      } // if

      if($log_query_execution_details) {
        $this->addToQueryLog($sql);
      } // if

      if($query_result instanceof mysqli_result) {
        if($query_result->num_rows > 0) {
          switch($load) {
            case DB::LOAD_FIRST_ROW:
              $result = self::rowToResult($query_result->fetch_assoc(), $return_mode, $return_class_or_field); break;

            case DB::LOAD_FIRST_COLUMN:
              $result = array();

              if($query_result->num_rows > 0) {
                while($row = $query_result->fetch_assoc()) {
                  $result[] = array_shift($row);
                } // if
              } // if

              break;

            case DB::LOAD_FIRST_CELL:
              $result = array_shift($query_result->fetch_assoc()); break;
            default:
              return new MySQLDBResult($query_result, $return_mode, $return_class_or_field); // Don't close result, we need it
          } // switch
        } else {
          $result = null;
        } // if

        $query_result->close();

        return $result;
      } elseif($query_result === true) {
        return true;
      } else {
        throw new DBQueryError($sql, $this->link->errno, $this->link->error);
      } // if
    } // execute

    /**
     * Prepare (if needed) and execute SQL query
     *
     * @param string $sql
     * @param array|null $arguments
     * @return bool|mysqli_result
     * @throws DBNotConnectedError
     */
    private function executeQuery($sql, $arguments = null) {
      if(empty($this->link)) {
        throw new DBNotConnectedError();
      } // if

      return $this->link->query($this->prepare($sql, $arguments));

//      if(empty($arguments)) {
//        return $this->link->query($sql);
//      } else {
//
//      } // if
    } // executeQuery

    /**
     * Try to survive MySQL has gone away errors
     *
     * @param string $sql
     * @param array $arguments
     * @param bool $log
     * @return bool|mysqli_result
     * @throws DBQueryError
     * @throws Exception
     */
    private function handleMySqlGoneAway($sql, $arguments = null, $log = false) {
      if(defined('DB_AUTO_RECONNECT') && DB_AUTO_RECONNECT > 0) {
        for($i = 1; $i <= DB_AUTO_RECONNECT; $i++) {
          if($log) {
            Logger::log("Trying to reconnect, attempt #$i", Logger::INFO, 'sql');
          } // if

          try {
            $this->reconnect();
            $query_result = $this->executeQuery($sql, $arguments);
            if($query_result !== false) {
              return $query_result;
            } // if
          } catch(Exception $e) {
            throw $e; // rethrow exception
          } // try
        } // for
      } // if

      // Not executed after reconnects?
      throw new DBQueryError($sql, $this->link->errno, $this->link->error);
    } // handleMySqlGoneAway

    /**
     * Try to survive deadlock
     *
     * @param string $sql
     * @param array|null $arguments
     * @param bool $log
     * @return bool|mysqli_result
     * @throws DBQueryError
     */
    private function handleDeadlock($sql, $arguments = null, $log = false) {
      if(defined('DB_DEADLOCK_RETRIES') && DB_DEADLOCK_RETRIES) {
        for($i = 1; $i <= DB_DEADLOCK_RETRIES; $i++) {
          if($log) {
            Logger::log("Deadlock detected, retrying (attempt #$i)", Logger::INFO, 'sql');
          } // if

          // Seconds to miliseconds, and sleep
          usleep(DB_DEADLOCK_SLEEP * 1000000);

          $query_result = $this->executeQuery($sql, $arguments);
          if($query_result !== false) {
            return $query_result;
          } // if
        } // for
      } // if

      // Not executed after retries?
      throw new DBQueryError($sql, $this->link->errno, $this->link->error);
    } // handleDeadlock
    
    /**
     * Return number of affected rows
     *
     * @return integer
     */
    function affectedRows() {
      return $this->link->affected_rows;
    } // affectedRows
    
    /**
     * Return last insert ID
     *
     * @return integer
     */
    function lastInsertId() {
      return $this->link->insert_id;
    } // lastInsertId

    /**
     * Transaction level
     *
     * @var integer
     */
    private $transaction_level = 0;
    
    /**
     * Begin transaction
     *
     * @param string $message
     * @return boolean
     */
    function beginWork($message = null) {
      if($this->transaction_level == 0) {
        $this->execute('BEGIN WORK');
      } // if
      $this->transaction_level++;
      
      if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
        Logger::log('Transaction level increased to ' . $this->transaction_level . ". Message: $message", Logger::INFO, 'sql');
      } // if
    } // beginWork
    
    /**
     * Commit transaction
     *
     * @param string $message
     * @return boolean
     */
    function commit($message = null) {
      if($this->transaction_level) {
        $this->transaction_level--;
        if($this->transaction_level == 0) {
          $this->execute('COMMIT');
        } else {
          if(AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) {
            Logger::log('Transaction level decreased to ' . $this->transaction_level . ". Message: $message", Logger::INFO, 'sql');
          } // if
        } // if
      } // if
    } // commit
    
    /**
     * Rollback transaction
     *
     * @param string $message
     * @return boolean
     */
    function rollback($message = null) {
      if($this->transaction_level) {
        $this->transaction_level = 0;
        $this->execute('ROLLBACK');
      } // if
      
      if((AngieApplication::isInDebugMode() || AngieApplication::isInDevelopment()) && $message) {
        Logger::log("Rolling back the transaction. Reason: $message", Logger::INFO, 'sql');
      } // if
    } // rollback
    
    /**
     * Return true if system is in transaction
     * 
     * @return boolean
     */
    function inTransaction() {
      return $this->transaction_level > 0;
    } // inTransaction
    
    /**
     * Escape string before we use it in query...
     *
     * @param string $unescaped String that need to be escaped
     * @return string
     * @throws InvalidParamError
     */
    function escape($unescaped) {

      // Date time value
      if($unescaped instanceof DateTimeValue) {
        return "'" . $this->link->real_escape_string(date(DATETIME_MYSQL, $unescaped->getTimestamp())) . "'";
        
      // Date value
      } elseif($unescaped instanceof DateValue) {
        return "'" . $this->link->real_escape_string(date(DATE_MYSQL, $unescaped->getTimestamp())) . "'";
        
      // Float
      } elseif(is_float($unescaped)) {
        return "'" . str_replace(',', '.', (float) $unescaped) . "'"; // replace , with . for locales where comma is used by the system (German for example)
        
      // Boolean (maps to TINYINT(1))
      } elseif(is_bool($unescaped)) {
        return $unescaped ? "'1'" : "'0'";
        
      // NULL
      } elseif(is_null($unescaped)) {
        return 'NULL';
        
      // Escape first cell of each row
      } elseif($unescaped instanceof DBResult) {
        $escaped = array();
        foreach($unescaped as $v) {
          $escaped[] = $this->escape(array_shift($v));
        } // foreach
        
        return implode(', ', $escaped);
        
      // Escape each array element
      } elseif(is_array($unescaped)) {
        $escaped = array();
        foreach($unescaped as $v) {
          $escaped[] = $this->escape($v);
        } // foreach
        
        return implode(', ', $escaped);
        
      // Regular string and integer escape
      } else {
        if(!is_scalar($unescaped)) {
          throw new InvalidParamError('unescaped', $unescaped, '$unescaped is expected to be scalar, array, or instance of: DateValue, DateTimeValue, DBResult');
        } // if

      	return "'" . $this->link->real_escape_string($unescaped) . "'";
      } // if
    } // escape
    
    /**
     * Escape table field name
     *
     * @param string $unescaped
     * @return string
     */
    function escapeFieldName($unescaped) {
      return "`$unescaped`";
    } // escapeFieldName
    
    /**
     * Escape table name
     *
     * @param string $unescaped
     * @return string
     */
    function escapeTableName($unescaped) {
      return "`$unescaped`";
    } // escapeTableName
    
    // ---------------------------------------------------
    //  Table management
    // ---------------------------------------------------
    
    /**
     * Returns true if table $name exists
     * 
     * @param string $name
     * @return boolean
     */
    function tableExists($name) {
      if($name) {
        $result = $this->execute('SHOW TABLES LIKE ?', array($name));
        
        return $result instanceof DBResult && $result->count() == 1;
      } // if
      
      return false;
    } // tableExists
    
    /**
     * Create new table instance
     *
     * @param string $name
     * @return MySQLDBTable
     */
    function createTable($name) {
      return new MySQLDBTable($name);
    } // createTable
    
    /**
     * Load table information
     *
     * @param boolean $name
     * @return MySQLDBTable
     */
    function loadTable($name) {
      return new MySQLDBTable($name, true);
    } // laodTable
    
    /**
     * Return array of tables from selected database
     *
     * If there is no tables in database empty array is returned
     *
     * @param string $prefix
     * @return array
     */
    function listTables($prefix = null) {
      if($prefix) {
        $rows = $this->execute("SHOW TABLES LIKE '$prefix%'");
      } else {
        $rows = $this->execute('SHOW TABLES');
      } // if
      
      if(is_foreachable($rows)) {
        $tables = array();
        foreach($rows as $row) {
          $tables[] = array_shift($row);
        } // foreach
        return $tables;
      } else {
      	return null;
      } // if
    } // listTables
    
    /**
     * List names of the table
     *
     * @param string $table_name
     * @return array
     */
    function listTableFields($table_name) {
      $rows = $this->execute("DESCRIBE $table_name");
      if(is_foreachable($rows)) {
        $result = array();
        foreach($rows as $row) {
          $result[] = $row['Field'];
        } // foreach
        return $result;
      } // if
      
      return array();
    } // listTableFields
    
    /**
     * Drop list of tables
     *
     * @param array $tables
     * @param string $prefix
     * @throws DBQueryError
     */
    function dropTables($tables, $prefix = '') {
      if(!empty($tables)) {
        $tables = (array) $tables;
      
        foreach($tables as $k => $v) {
          $tables[$k] = $this->escapeTableName($prefix . $v);
        } // foreach
        
        $this->execute('DROP TABLES ' . implode(', ', $tables));
      } // if
    } // dropTables

    /**
     * Return array of table indexes
     *
     * @param string $table_name
     * @return array
     */
    function listTableIndexes($table_name) {
      $rows = $this->execute("SHOW INDEXES FROM $table_name");
      if(is_foreachable($rows)) {
        $result = array();
        foreach($rows as $row) {
          $key_name = $row['Key_name'];

          if(!in_array($key_name, $result)) {
            $result[] = $key_name;
          } // if
        } // foreach
        return $result;
      } // if

      return array();
    } // listTableIndexes
    
    /**
     * Drop all tables from database
     *
     * @return boolean
     */
    function clearDatabase() {
      $tables = $this->listTables();
      if(is_foreachable($tables)) {
        return $this->execute('DROP TABLES ' . implode(', ', $tables));
      } else {
        return true; // it's already clear
      } // if
    } // clearDatabase


    /**
     * Gets maximum packet size allowed to be inserted into database
     *
     * @return int
     */
    function getMaxPacketSize() {
      return intval($this->getServerVariable('max_allowed_packet'));
    } //if
    
    // ---------------------------------------------------
    //  File import / export
    // ---------------------------------------------------
    
    /**
     * Do a mysql dump of specified tables
     * 
     * If $table_name is empty it will dump all tables in current database
     *
     * @param array $tables
     * @param string $output_file
     * @param boolean $dump_structure
     * @param boolean $dump_data
     * @return boolean
     * @throws Error
     */
    function exportToFile($tables, $output_file, $dump_structure = true, $dump_data = true) {
      $max_query_length = 838860; // maximum query length
      
      if(empty($tables)) {
        $tables = $this->listTables();
      } // if
            
      if(is_foreachable($tables)) {
        $handle = fopen($output_file, 'w');
        
        if(empty($handle)) {
          throw new Error("Cannot create output file: '$output_file'");
        } // if
        
        foreach($tables as $table_name) {
          
          // Dump_structure
        	if($dump_structure) {
        	  $create_table = $this->executeFirstRow("SHOW CREATE TABLE $table_name");
        	  fwrite($handle, "DROP TABLE IF EXISTS $table_name;\n".$create_table['Create Table'].";\n\n");
        	} // if
        	
        	// Dump_data
        	if($dump_data) {
            fwrite($handle, "/*!40000 ALTER TABLE $table_name DISABLE KEYS */;\n");

            $query_result = $this->link->query("SELECT * FROM $table_name");
            
            $inserted_values = '';
            while($row = $query_result->fetch_array(MYSQLI_NUM)) {
              $values = '';
              
              foreach($row as $field) {
                if($values) {
                  $values .= ',';
                } // if
                
                $values .= $field === null ? "NULL" : "'" . $this->link->real_escape_string($field) . "'";
              } // foreach
              
              $inserted_values.= ($inserted_values ? ',' : '');
            	$inserted_values.='('.$values.')';
            	
              if(strlen($inserted_values) > $max_query_length) {
                fwrite($handle, "INSERT INTO $table_name VALUES $inserted_values;\n");
                $inserted_values = '';
              } // if
            } // while
            
            if($inserted_values) {
              fwrite($handle, "INSERT INTO $table_name VALUES $inserted_values;\n");
            } // if
            fwrite($handle, "/*!40000 ALTER TABLE $table_name ENABLE KEYS */;\n");
        	} // if
        } // foreach
        
        fclose($handle);
      } // if
    } // exportToFile
    
    /**
     * Get MySQL variable value
     *
     * @param string $variable_name
     * @return mixed
     */
    function getServerVariable($variable_name) {
      $variable = $this->executeFirstRow("SHOW VARIABLES LIKE '$variable_name'");
      
      return is_array($variable) && isset($variable['Value']) ? $variable['Value'] : null;
    } // getVariableValue
    
    /**
     * Return version of the server
     *
     * @return string
     */
    function getServerVersion() {
      return $this->link->get_server_info();
    } // getServerVersion
    
    /**
     * Returns true if server we are connected to supports collation
     *
     * @return boolean
     */
    function supportsCollation() {
      return version_compare($this->getServerVersion(), '4.1') >= 0;
    } // supportsCollation

    /**
     * Return true if we have InnoDB support
     *
     * @return boolean
     */
    function hasInnoDBSupport() {
      $engines = DB::execute('SHOW ENGINES');

      if($engines) {
        foreach($engines as $engine) {
          if(strtolower($engine['Engine']) == 'innodb' && in_array(strtolower($engine['Support']), array('yes', 'default'))) {
            return true;
          } // if
        } // foreach
      } // if

      return false;
    } // hasInnoDBSupport
    
  }