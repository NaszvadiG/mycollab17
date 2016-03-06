<?php

  /**
   * Select min password length helper
   *
   * @package activeCollab.modules.password_policy
   * @subpackage helpers
   */

  /**
   * Render select min password length box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_min_password_length($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);

    return HTML::selectFromPossibilities($name, array(
      '0' => lang("Don't Check"),
      '5' => lang('5 letters'),
      '10' => lang('10 letters'),
      '15' => lang('15 letters'),
      '25' => lang('25 letters'),
    ), $value, $params);
  } // smarty_function_select_min_password_length