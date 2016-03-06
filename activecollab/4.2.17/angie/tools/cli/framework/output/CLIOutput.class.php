<?php

  /**
   * CLI output handler
   *
   * This handler will print data to the console directly and end it with a new 
   * line
   * 
   * @package angie.tools.cli
   * @subpackage output
   */
  class CLIOutput extends Output {
    
    /**
     * Max line lenght
     *
     * @var string
     */
    private $max_line_length = 60;

    /**
     * @param null|string $log_file_path
     */
    function __construct($log_output = false, $skip_default_filesystem_info = false) {
      parent::__construct($log_output, $skip_default_filesystem_info);
    } // construct
    
    /**
     * Print an empty line (or $num of lines)
     * 
     * @param integer $num
     */
    function printLine($num = 1) {
      $string = "";

      if($num < 1) {
        $string .= "\n";
      } else {
        for($i = 0; $i < $num; $i++) {
          $string .= "\n";
        } // for
      } // if

      $this->log_output ? Logger::log($string) : print $string;
    } // printLine
  
    /**
     * Print message to console
     *
     * @param string $message
     * @param string $type
     * @param bool $is_abort
     */
    function printMessage($message, $type = null, $is_abort = false) {
      if ($this->skip_default_filesystem_info && $type !== 'error' && !$is_abort) {
        $patterns = array(
          "/((File)|(Directory))+(.*)+(created|copied)/s",
          "/(Removed)+(.*)+(file|directory|link)/s",
          "/(Symlink that connects)+(.*)/s",
          "/(Changed permissions)+(.*)/s",
          "/(Work dir changed)+(.*)/s",
        );

        foreach ($patterns as $pattern) {
          if (preg_match($pattern, $message)) {
            return;
          } // if
        } // foreach
      } // if

      $string = trim($type) == '' ? $message . "\n" : "$type: $message\n";

      $log_type = $type == "error" ? Logger::ERROR : Logger::INFO;

      if ($this->log_output) {
        Logger::log($message, $log_type);
      } else {
        if ($type == 'error') {
          fwrite(STDERR, $string);
        } else {
          print $string;
        } // if
      } // if
    } // printMessage
    
    /**
     * Print table
     * 
     * @param array $columns
     * @param array $data
     */
    function printTable($columns, $data) {
      $lengths = array();
      
      // Initial column lengths are based on column names
      foreach($columns as $k => $v) {
        $lengths[$k] = mb_strlen($v);
      } // foreach
      
      // Adjust based on data that needs to be displayed
      foreach($data as $row) {
        foreach($row as $k => $v) {
          $length = mb_strlen($v);
          
          if(isset($lengths[$k])) {
            if($length > $lengths[$k]) {
              $lengths[$k] = $length;
            } // if
          } else {
            $lengths[$k] = $length;
          } // if
        } // if
      } // foreach
      
      foreach($lengths as $k => $v) {
        if($v > $this->max_line_length) {
          $lengths[$k] = $this->max_line_length;
        } // if
      } // foreach
      
      $this->printTableHeader($columns, $lengths);
      
      if(count($data)) {
        foreach($data as $row) {
          $this->printTableRow($columns, $lengths, $row);
        } // foreach
      } else {
        $this->printTableBar($columns, $lengths);
      } // if
    } // printTable
    
    /**
     * Print table header
     * 
     * @param array $columns
     * @param array $lenghts
     */
    private function printTableHeader($columns, $lengths) {
      $this->printTableBar($columns, $lengths);

      $string = "";
      foreach($columns as $k => $v) {
        $string .= "| " . $this->expandString($v, $lengths[$k] + 1);
      } // foreach
      $string .= "|\n";

      $this->log_output ? Logger::log($string) : print $string;
      
      $this->printTableBar($columns, $lengths);
    } // printTableHeader
    
    /**
     * Print single table row
     * 
     * @param array $columns
     * @param array $lengths
     * @param array $row
     */
    private function printTableRow($columns, $lengths, $row) {
      $max_height = 1;
      
      foreach($row as $k => $v) {
        $lines = explode("\n", str_replace(array("\r\n", "\n", "\r"), array("\n", "\n", "\n"), $v));
        
        $text = array();
        foreach($lines as $line => $line_text) {
          $text = array_merge($text, explode("\n", wordwrap($line_text, $this->max_line_length, "\n")));
        } // foreach
        
        $row[$k] = $text;
        
        if(count($text) > $max_height) {
          $max_height = count($text);
        } // if
      } // foreach

      $string = "";
      for($i = 0; $i < $max_height; $i++) {
        foreach($row as $k => $v) {
          $string .= '| ' . $this->expandString((isset($v[$i]) ? $v[$i] : ''), $lengths[$k] + 1);
        } // if
        $string .= "|\n";
      } // for

      $this->log_output ? Logger::log($string) : print $string;
      
      $this->printTableBar($columns, $lengths);
    } // printTableRow
    
    /**
     * Print table bar
     * 
     * @param array $columns
     * @param array $lengths
     */
    private function printTableBar($columns, $lengths) {
      $string = "";
      foreach($columns as $k => $v) {
        $string .= '+' . $this->createString('-', $lengths[$k] + 2);
      } // foreach
      $string .= "+\n";

      $this->log_output ? Logger::log($string) : print $string;
    } // printTableBar
    
    /**
     * Print content
     *
     * @param string $string
     */
    function printContent($string) {
      $this->log_output ? Logger::log($string) : print $string;
    } // printContent
    
    // ---------------------------------------------------
    //  Utility
    // ---------------------------------------------------
    
    /**
     * Expand string with whitespace to reach given lenght
     *
     * @param string $string
     * @param integer $lenght
     * @return string
     */
    private function expandString($string, $length) {
      $l = mb_strlen($string);
      
      for($i = $l; $i < $length; $i++) {
        $string .= ' ';
      } // for
      
      return $string;
    } // expandString
    
    /**
     * Create string of given length
     *
     * @param string $of
     * @param integer $lenght
     * @return string
     */
    private function createString($of, $length) {
      $result = '';
      for($i = 0; $i < $length; $i++) {
        $result .= $of;
      } // for
      
      return $result;
    } // createString
  
  }