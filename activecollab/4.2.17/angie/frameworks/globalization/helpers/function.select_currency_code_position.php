<?php

/**
 * Select Currency Code Position
 *
 * @package activeCollab.modules.system
 * @subpackage helpers
 */

/**
 * Render select currency code position
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_select_currency_code_position($params, &$smarty) {
  $name = array_required_var($params, 'name', true);
  $value = array_var($params, 'value', false, true);
  $optional = array_var($params, 'optional', false, true);

  if ($value === false || $value === null) {
    $value = '';
  } // if

  $decimal_rounding_options = array(
    Currency::CURRENCY_CODE_POSITION_LEFT      => lang('Before amount'),
    Currency::CURRENCY_CODE_POSITION_RIGHT      => lang('After amount'),
  );

  return HTML::selectFromPossibilities($name, $decimal_rounding_options, $value, $params);
} // smarty_function_select_currency_code_position