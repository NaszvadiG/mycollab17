<?php

  /**
   * select_assignee helper
   *
   * @package angie.frameworks.assignees
   * @subpackage helpers
   */
  
  /**
   * Render select assignee select box
   * 
   * Parameters:
   * 
   * - parent - Parent object
   * - user - User who'll be using the form
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignee($params, &$smarty) {
    $parent = array_required_var($params, 'parent', true, 'IAssignees');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $value = array_var($params, 'value', null, true);
    $exclude_ids = array_var($params, 'exclude', null, true);

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_assignee');
    } // if
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_assignee';
    } else {
      $params['class'] = 'select_assignee';
    } // if

    $grouped = $parent->assignees()->getAvailableUsersForSelect($user, $exclude_ids);

    // add assignee to select picker in case it's not present
    // this is required for case when client isn't allowed to assign objects to other non-client users, because otherwise the list doesn't include existing assignee
    // and the object gets unassigneed after the form is submitted
    if (AngieApplication::getName() == 'activeCollab' && $parent->assignees()->getAssignee() instanceof User && !isset($grouped[$parent->assignees()->getAssignee()->getCompany()->getName()])) {
      $assignee_option = array(
        $parent->assignees()->getAssignee()->getCompany()->getName() => array(
          $parent->assignees()->getAssignee()->getId() => $parent->assignees()->getAssignee()->getDisplayName()
        )
      );

      if (!is_array($grouped)) {
        $grouped = array();
      } // if

      $grouped += $assignee_option;
      ksort($grouped);
    } // if
    
    $options = array();
    if(is_foreachable($grouped)) {
      foreach($grouped as $group_name => $users) {
        $group_options = array();
        
        foreach($users as $user_id => $user_display) {
          $group_options[] = HTML::optionForSelect($user_display, $user_id, $user_id == $value);
        } // foreach
        
        $options[] = HTML::optionGroup($group_name, $group_options);
      } // foreach
    } // if
    
    return array_var($params, 'optional', true, true) ?
      HTML::optionalSelect($params['name'], $options, $params, lang('Nobody')) :  
      HTML::select($params['name'], $options, $params);
  } // smarty_function_select_assignee