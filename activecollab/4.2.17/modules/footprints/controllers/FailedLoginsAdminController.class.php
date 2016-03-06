<?php

// Build on top of administration controller
AngieApplication::useController('admin', SYSTEM_MODULE);

/**
 * Failed Logins administration controller
 *
 * @package activeCollab.modules.footprints
 * @subpackage controllers
 */
class FailedLoginsAdminController extends AdminController {

	/**
	 * Execute before action
	 */
	function __before() {
		parent::__before();

		$this->wireframe->breadcrumbs->add('failed_logins_admin', lang('Failed Logins'), Router::assemble('failed_logins_admin'));
	} // __before

	/**
	 * Show index page
	 */
	function index() {
		$this->response->assign(array(
			'data' => UsersSecurity::findFailedLogins()
		));
	} // index

}