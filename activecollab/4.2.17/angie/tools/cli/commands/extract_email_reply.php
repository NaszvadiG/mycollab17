<?php

/**
 * Extract reply from email
 *
 * @package angie.tools.cli_commands
 */
class CLICommandExtractEmailReply extends CLICommandExecutable {
  /**
   * Command description
   *
   * @var string
   */
  var $description = 'Extract Reply from email';

  /**
   * Execute the command
   *
   * @param Output $output
   */
  function execute(Output $output) {
    CLI::initEnvironment($output);

    $input_file = $this->getArgument(1);
    if (!$input_file) {
      die("File in eml format not specified.\n");
    } // if

    if (!is_file($input_file)) {
      die("Specified file ($input_file) does not exist.\n");
    } // if

    $file_permission = substr(sprintf('%o', fileperms($input_file)), -4);
    if (!in_array($file_permission, array('0777', '1777', '0666', '1666'))) {
      die("File ($input_file) has to have 0777 permissions.\n");
    } // if

    // requirements
    require_once ANGIE_PATH . '/classes/mailboxmanager/MailboxManagerEmail.class.php';
    require_once EMAIL_FRAMEWORK_PATH . '/models/incoming_mail_body_processors/IncomingMailBodyProcessor.class.php';

    // construct manager
    $manager = new PHPImapMailboxManager($input_file);
    $manager->connect();

    // get the email
    $email = $manager->getMessage(1, WORK_PATH);

    // load the processor
    $processor = new IncomingMailBodyProcessor($email);

    // extract the body
    $body = $processor->extractReply();
    echo "BODY\n";
    var_dump($body);
    echo "ATTACHMENTS\n";
    var_dump($email->getAttachments());
    echo "\n\n";
    die();
  } // execute

}