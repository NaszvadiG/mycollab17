<?php

  /**
   * Past helper implementation
   *
   * @package activeCollab.modules.footprints
   * @subpackage helpers
   */

  /**
   * Render past from date value
   *
   * @param array $param
   * @return string
   */
  function smarty_function_past($param) {
    $user = array_required_var($param, 'user', true, 'User');
    $past = array_required_var($param, 'date');
    $offset = get_user_gmt_offset($user);

    if (!($past instanceof DateValue)) {
      $past = DateValue::makeFromString($past);
    } // if

    $user_formatted_date = $past->formatDateForUser($user);

    if($past->isToday($offset)) {
      return '<span class="today" title="'. $user_formatted_date .'">' . lang('Today') . '</span>';
    } elseif($past->isYesterday($offset)) {
      return '<span class="yesterday" title="' . $user_formatted_date . '">' . lang('Yesterday') . '</span>';
    } else {
      return '<span class="date">' . $user_formatted_date . '</span>';
    } // if
  } // smarty_function_past