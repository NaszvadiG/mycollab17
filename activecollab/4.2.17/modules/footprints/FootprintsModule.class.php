<?php

/**
 * Footprints module
 *
 * @package activeCollab.modules.footprints
 * @subpackage models
 */
class FootprintsModule extends AngieModule {

	/**
	 * Plain module name
	 *
	 * @var string
	 */
	protected $name = 'footprints';

	/**
	 * Module version
	 *
	 * @var string
	 */
	protected $version = '4.0';

	// ---------------------------------------------------
	//  Events and Routes
	// ---------------------------------------------------

	/**
	 * Define module routes
	 */
	function defineRoutes() {
		Router::map('security_logs', 'people/:company_id/users/:user_id/security-logs', array('controller' => 'users_security'));
		Router::map('security_logs_performed_by_this_user', 'people/:company_id/users/:user_id/security-logs/performed-by-this-user', array('controller' => 'users_security', 'action' => 'performed_by_this_user'));
		Router::map('security_logs_performed_on_this_account', 'people/:company_id/users/:user_id/security-logs/performed-on-this-account', array('controller' => 'users_security', 'action' => 'performed_on_this_account'));
		Router::map('people_company_user_user_sessions', 'people/:company_id/users/:user_id/sessions', array('controller' => 'user_sessions', 'action' => 'index'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
		Router::map('people_company_user_sessions_remove', 'people/:company_id/users/:user_id/sessions-remove', array('controller' => 'user_sessions', 'action' => 'remove'), array('company_id' => Router::MATCH_ID, 'user_id' => Router::MATCH_ID));
		Router::map('modification_log_diffs', 'modification/log/:log_id/diffs', array('controller' => 'history_of_changes', 'action' => 'diffs', array('log_if' => Router::MATCH_ID)));

		// Administration
		Router::map('failed_logins_admin', 'admin/failed-logins', array('controller' => 'failed_logins_admin'));
	} // defineRoutes

	/**
	 * Define access log routes for given context
	 *
	 * @param $context
	 * @param $context_path
	 * @param $controller_name
	 * @param $module_name
	 * @param null $context_requirements
	 */
	function defineAccessLogRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
		Router::map("{$context}_access_logs", "$context_path/access-logs", array('controller' => $controller_name, 'action' => "{$context}_access_logs", 'module' => $module_name), $context_requirements);
	} // defineScheduleRoutesFor

	/**
	 * Define history of changes routes for given context
	 *
	 * @param $context
	 * @param $context_path
	 * @param $controller_name
	 * @param $module_name
	 * @param null $context_requirements
	 */
	function defineHistoryOfChangesRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
		Router::map("{$context}_history_of_changes", "$context_path/history-of-changes", array('controller' => $controller_name, 'action' => "{$context}_history_of_changes", 'module' => $module_name), $context_requirements);
	} // defineModificationLogRoutesFor

	/**
	 * Define event handlers
	 */
	function defineHandlers() {
		EventsManager::listen('on_object_options', 'on_object_options');
		EventsManager::listen('on_object_inspector', 'on_object_inspector');
		EventsManager::listen('on_admin_panel', 'on_admin_panel');
	} // defineHandlers

	// ---------------------------------------------------
	//  Name
	// ---------------------------------------------------

	/**
	 * Get module display name
	 *
	 * @return string
	 */
	function getDisplayName() {
		return lang('Footprints');
	} // getDisplayName

	/**
	 * Return module description
	 *
	 * @return string
	 */
	function getDescription() {
		return lang('Set of logs and tools that enable better monitoring of user activity in the system.');
	} // getDescription

}