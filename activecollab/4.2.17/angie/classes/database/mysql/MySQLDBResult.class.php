<?php

  /**
   * MySQL database result
   *
   * @package angie.library.database
   * @subpackage mysql
   */
  class MySQLDBResult extends DBResult {

    /**
     * Result resource
     *
     * @var mysqli_result
     */
    protected $resource;

    /**
     * Returns true if $resource is valid result resource
     *
     * @param mixed $resource
     * @return bool
     */
    protected function isValidResource($resource) {
      return $resource instanceof mysqli_result && $resource->num_rows > 0;
    } // isValidResource
    
    /**
     * Set cursor to a given position in the record set
     *
     * @param integer $num
     * @return boolean
     */
    public function seek($num) {
      if($num >= 0 && $num <= $this->count() - 1) {
        if(!$this->resource->data_seek($num)) {
          return false;
        } // if
        
        $this->cursor_position = $num;
        return true;
      } // if
      
      return false;
    } // seek
    
    /**
     * Return next record in result set
     *
     * @return array
     * @throws DBError
     */
    function next() {
      if($this->cursor_position < $this->count() && $row = $this->resource->fetch_assoc()) { // Not count() - 1 because we use this for getting the current row
        $this->setCurrentRow($row);
        $this->cursor_position++;
        return true;
      } // if
      
      return false;
    } // next
    
    /**
     * Return number of records in result set
     *
     * @return integer
     */
    function count() {
      return $this->resource->num_rows;
    } // count
    
    /**
     * Free resource when we are done with this result
     *
     * @return boolean
     */
    public function free() {
      if($this->resource instanceof mysqli_result) {
        $this->resource->close();
      } // if
    } // free
    
  }