<?php

  /**
   * Instant payment notification
   *
   * Instant notify on demand module about new payment
   *
   * @package angie.tools.cli.commands
   */

  class CLICommandIpn extends CLICommandExecutable {

    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Instantly notify on demand module about new payment';

    /**
     * Definition of command options
     *
     * @var array
     */
    var $option_definitions = array(
      array('d:', 'date:', 'Payment date'),
      array('r:', 'reference_id:', 'Reference ID'),
      array('a:', 'amount:', 'Amount'),
      array('t:', 'type', 'Type'),
      array('p:', 'plan:', 'Plan'),
      array('g:', 'gateway:', 'Gateway'),
    );

    /**
     * Execute the command
     *
     * Trigger this function to exeucte the command based on the input arguments
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);


      $date = $this->getOption(array('d', 'date'));
      if(empty($date)) {
        $output->abortWithMessage('Date value is required');
      } // if
      $reference_id = $this->getOption(array('r', 'reference_id'));
      if(empty($reference_id)) {
        $output->abortWithMessage('Reference ID value is required');
      } // if

      $amount = $this->getOption(array('a', 'amount'));
      if(empty($amount)) {
        $output->abortWithMessage('Amount value is required');
      } // if

      $type = $this->getOption(array('t', 'type'));
      if(empty($type)) {
        $output->abortWithMessage('Type value is required');
      } // if

      if (!in_array($type, array(OnDemandInvoice::INVOICE_TYPE_ORDER, OnDemandInvoice::INVOICE_TYPE_REFUND))) {
        $output->abortWithMessage("'{$type}' is not a valid payment type (expecting 'order' or 'refund'");
      } // if

      $plan = $this->getOption(array('p', 'plan'));
      if(empty($plan)) {
        $output->abortWithMessage('Plan value is required');
      } else {
        ConfigOptions::setValue('on_demand_plan', $plan);
      } //if

      $gateway = $this->getOption(array('g', 'gateway'), OnDemand::ON_DEMAND_GATEWAY_FASTSPRING);

      $data = array(
        'date' => $date,
        'reference_id' => $reference_id,
        'amount' => $amount,
        'plan' => $plan,
        'gateway' => $gateway,
        'type' => $type
      );

      $new_on_demand_invoice = new OnDemandInvoice();
      $new_on_demand_invoice->setAttributes($data);
      /**
      if($add_ons) {
        $new_on_demand_invoice->setAdditionalProperties($add_ons);
      } // if
       */

      $new_on_demand_invoice->save();
      $output->printMessage('New On Demand Invoice created');

    } // execute

  } // CLICommandIpn
