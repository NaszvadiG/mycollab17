<?php

  /**
   * Select password auto expire setting value
   *
   * @package activeCollab.modules.password_policy
   * @subpackage helpers
   */

  /**
   * Render select password auto expire picker
   *
   * @param string $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_password_auto_expire($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    return HTML::selectFromPossibilities($name, array(
      '0' => lang("Passwords Don't Expire Automatically"),
      '1' => lang('Every Month'),
      '3' => lang('Every 3 Months'),
      '6' => lang('Every 6 Months'),
      '12' => lang('Every Year'),
    ), $value, $params);
  } // smarty_function_select_password_auto_expire