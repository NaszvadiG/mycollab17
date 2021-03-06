<?php

  /**
   * Render select currency box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_invoice_status($params, &$smarty){
    $status_map = Invoices::getStatusMap();
    $value = array_var($params, 'value', NULL, True);

    $options = array();
    foreach ($status_map as $k=>$v ) {
      $option_attributes = $k == $value ? array('selected' => True) : Null;
      $options[] = option_tag($v, $k, $option_attributes);
    }
    return select_box( $options, $params);
  } // smarty_function_select_invoice_status
  
?>