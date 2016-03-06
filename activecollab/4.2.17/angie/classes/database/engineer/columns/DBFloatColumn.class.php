<?php

  /**
   * Class that represents FLOAT/DOUBLE database columns
   *
   * @package angie.library.database
   * @subpackage engineer
   */
  class DBFloatColumn extends DBNumericColumn {
    
    /**
     * Column scale
     *
     * @var integer
     */
    private $scale = 2;
    
    /**
     * Construct float column
     *
     * @param string $name
     * @param integer $lenght
     * @param integer $scale
     * @param mixed $default
     */
    function __construct($name, $lenght = 12, $scale = 2, $default = null) {
      if($default !== null) {
        $default = (float) $default;
      } // if
      
    	parent::__construct($name, $lenght, $default);
    	
    	$this->scale = (integer) $scale;
    } // __construct
    
    /**
     * Create and return float column
     *
     * @param string $name
     * @param integer $lenght
     * @param integer $scale
     * @param mixed $default
     * @return DBFloatColumn
     */
    static public function create($name, $lenght = 12, $scale = 2, $default = null) {
      return new DBFloatColumn($name, $lenght, $scale, $default);
    } // create
    
    /**
     * Process additional field parameters
     *
     * @param array $additional
     */
    function processAdditional($additional) {
      parent::processAdditional($additional);
      
      if(is_array($additional) && isset($additional[1]) && $additional[1]) {
    	  $this->scale = (integer) $additional[1];
    	} // if
    } // processAdditional
    
    /**
     * Prepare type definition
     *
     * @return string
     */
    function prepareTypeDefinition() {
      $result = 'float(' . $this->length . ', ' . $this->scale . ')';
      if($this->unsigned) {
        $result .= ' unsigned';
      } // if
      return $result;
    } // prepareTypeDefinition
    
    /**
     * Prepare default value
     *
     * @return string
     */
    function prepareDefault() {
    	return parent::prepareDefault() === null ? null : (float) parent::prepareDefault();
    } // prepareDefault
    
    /**
     * Return model definition code for this column
     *
     * @return string
     */
    function prepareModelDefinition() {
      $default = $this->getDefault() === null ? '' : ', ' . var_export($this->getDefault(), true);
      
      $result = "DBFloatColumn::create('" . $this->getName() ."', " . $this->getLength() . ', ' . $this->getScale() . "$default)";
      
      if($this->unsigned) {
        $result .= '->setUnsigned(true)';
      } // if
      
      return $result;
    } // prepareModelDefinition

    // ---------------------------------------------------
    //  Model generator
    // ---------------------------------------------------

    /**
     * Return verbose PHP type
     *
     * @return string
     */
    function getPhpType() {
      return 'float';
    } // getPhpType

    /**
     * Return PHP bit that will cast raw value to proper value
     *
     * @param string $var
     * @return string
     */
    function getCastingCode() {
      return '(float) $value';
    } // getCastingCode
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Return scale
     *
     * @return integer
     */
    function getScale() {
    	return $this->scale;
    } // getScale
    
    /**
     * Set scale
     *
     * @param integer $value
     * @return DBFloatColumn
     */
    function &setScale($value) {
      $this->scale = (integer) $value;
      
      return $this;
    } // setScale
    
  }