<?php

/**
 * history_of_changes helper implementation
 *
 * @package angie.frameworks.footprints
 * @subpackage helpers
 */

/**
 * Render object's history
 *
 * @param array $params
 * @param Smarty $smarty
 * @return string
 */
function smarty_function_history_of_changes($params, &$smarty) {
	$object = array_required_var($params, 'object', true, 'IHistory');
	$user = array_required_var($params, 'user', true, 'IUser');

	$modifications = $object->history()->render($user, $smarty);
	$smarty->assign(array(
		'_history_modifications' => $modifications,
		'_history_object' => $object,
	));

	return $smarty->fetch(get_view_path('_object_history', 'history_of_changes', FOOTPRINTS_MODULE));
} // smarty_function_object_history