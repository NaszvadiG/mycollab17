<?php

  /**
   * TestM2PForwarding
   */
  class TestM2PForwarding extends AngieModelTestCase {

  	/**
  	 * Loaded email

  	 * @var IncomingMail
  	 */
  	protected $email;

    /**
     * Set up test case
     */
    function setUp() {
      parent::setUp();
    } // setUp
    
    /**
     * Tear down test case
     */
    function tearDown() {
      parent::tearDown();
    } // tearDown
    
    /**
     * Load requested email resource and create body processor
     * 
     * @param string $resource_name
     * @return null
     */
    function loadEmailResource($resource_name) {
    	$resource_file = EMAIL_FRAMEWORK_PATH . '/tests/resources/' . $resource_name . '.eml';

    	// initialize connection to mailbox (eml file)
    	$manager = new PHPImapMailboxManager($resource_file);
    	$manager->connect();

    	// get the message
    	$mailbox_manager_email = $manager->getMessage(1, WORK_PATH);
      $manager->disconnect();

      $this->email = AngieApplication::incomingMail()->createPendingEmail($mailbox_manager_email);

    } // getEmailResource

    /**
     * Test
     */
    function testReplies() {

      $this->loadEmailResource('fw_gmail_to_m2p');
      $this->assertEqual($this->email->getForwardedInfoFromHeader(), array('gaxoman@gmail.com', 'activecollabt+m2p-f810847@gmail.com'), 'Can\'t find M2P address');

      $this->loadEmailResource('fw_yahoo_to_m2p');
      $this->assertEqual($this->email->getForwardedInfoFromHeader(), array('gaxoman@yahoo.com', 'activecollabt+m2p-f810847@gmail.com'), 'Can\'t find M2P address');


    } // testReplies
    
  } // TestM2PForwarding