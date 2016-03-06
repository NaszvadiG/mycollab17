<?php

AngieApplication::useController('users', SYSTEM_MODULE);

/**
 * Class SecurityLogsController
 */
class UsersSecurityController extends UsersController {

	/**
	 * Prepare controller
	 */
	public function __before() {
		parent::__before();

		if (!($this->active_user instanceof ISecurityLog)) {
			$this->response->badRequest();
		} // if

		if ($this->active_user->isNew()) {
			$this->response->notFound();
		} // if

		// Only administrators and users which have privileges to access this account
		if ($this->active_user->getId() != $this->logged_user->getId()) {
			if (!$this->logged_user->isAdministrator()) {
				$this->response->forbidden();
			} // if
		} // if
	} // __before

	/**
	 *
	 */
	function index() {
		if (!$this->request->isAsyncCall()) {
			$this->response->badRequest();
		} // if

		$inline_tabs = new NamedList();
		$inline_tabs->add('performed_by_this_user', array(
			'title' => lang('Action performed by this user'),
			'url'   => Router::assemble('security_logs_performed_by_this_user', $this->active_user->getRoutingContextParams())
		));
		$inline_tabs->add('performed_on_this_account', array(
			'title' => lang('Action performed on this account'),
			'url'   => Router::assemble('security_logs_performed_on_this_account', $this->active_user->getRoutingContextParams())
		));

		$this->response->assign(array(
			'inline_tabs' => $inline_tabs
		));
	} // index

	/**
	 * Security Logs performed by this user
	 */
	function performed_by_this_user() {
		if (!$this->request->isAsyncCall()) {
			$this->response->badRequest();
		} // if

		$this->response->assign(array(
			'data' => UsersSecurity::findRecentBy($this->logged_user, $this->active_user)
		));
	} // performed_by_this_user

	/**
	 * Security Logs performed on this account
	 */
	function performed_on_this_account() {
		if (!$this->request->isAsyncCall()) {
			$this->response->badRequest();
		} // if

		$this->response->assign(array(
			'data' => UsersSecurity::findRecentOn($this->active_user)
		));
	} // performed_on_this_account

}