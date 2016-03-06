<?php

  /**
   * Forward thumbnail proxy
   * 
   * @package angie.frameworks.attachments
   * @subpackage proxies
   */
  class FwDownloadAttachmentProxy extends ProxyRequestHandler {
  	
  	/**
  	 * Id of attachment were going to download 
  	 * 
  	 * @var integer
  	 */
  	protected $id;

    /**
     * File size of the download
     *
     * @var integer
     */
    protected $size;
    
    /**
     * hash of the file
     * 
     * @var string
     */
    protected $md5;

    /**
     * Force download
     *
     * @var boolean
     */
    protected $force;
        
    /**
     * Construct proxy request handler
     * 
     * @param array $params
     */
    function __construct($params = null) {
      $this->id = isset($params['id']) && $params['id'] ? trim($params['id']) : null;
      $this->size = isset($params['size']) && $params['size'] ? (integer) $params['size'] : null;
      $this->md5 = isset($params['md5']) && $params['md5'] ?$params['md5'] : null;
      $this->force = isset($params['force']) && $params['force'];
    } // __construct
    
    /**
     * Forward image
     */
    function execute() {
    	if ($this->id === null || $this->size === null || $this->md5 === null) {
				$this->badRequest();   		
    	} // if
    	    	
    	// connect to database
    	$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

      if(empty($connection)) {
        $this->operationFailed();
      } // if

      $connection->set_charset('utf8');
      
      // create query
      $query = sprintf("SELECT location, name, mime_type FROM " . TABLE_PREFIX . "attachments WHERE id='%s' AND size='%s' AND md5='%s'",
        $connection->real_escape_string($this->id),
        $connection->real_escape_string($this->size),
        $connection->real_escape_string($this->md5)
      );
      
      // extract attachment
      $result = $connection->query($query);
      if ($result == false) {
				$this->notFound();
      } // if
      
      // fetch the attachment data
      $attachment = $result->fetch_assoc();
			if (!(isset($attachment['location']) && $attachment['location'])) {
				$this->notFound(); 
			} // if
			
			$file = UPLOAD_PATH . '/' . $attachment['location'];
			if (!is_file($file)) {
				$this->notFound();
			} // if
			
			$mime_type = isset($attachment['mime_type']) && $attachment['mime_type'] ? $attachment['mime_type'] : 'application/octet-stream';

			header('Content-type: ' . $mime_type);
      header("Cache-Control: public, max-age=315360000");
      header("Pragma: public");
      header("Etag: " . $this->md5);
      
      // cache file if we have same version in cache and on the server
      $cached_hash = isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] ? $_SERVER['HTTP_IF_NONE_MATCH'] : null;
      if ($cached_hash && $cached_hash == $this->md5) {
      	header("HTTP/1.1 304 Not Modified");
      	die();
      } // if
      
      require_once ANGIE_PATH . '/functions/general.php';
      require_once ANGIE_PATH . '/functions/errors.php';
      require_once ANGIE_PATH . '/functions/web.php';
			
			download_file($file, $mime_type, $attachment['name'], $this->force, true);
      die();
    } // execute
  }