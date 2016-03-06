<?php

/**
 * Class AccessLogsController
 */
class AccessLogsController extends Controller {

	/**
	 * Selected object
	 *
	 * @var IAccessLog
	 */
	protected $active_object;

	/**
	 * Prepare controller
	 */
	public function __before() {
		parent::__before();

		if($this->active_object instanceof IAccessLog) {
			if($this->active_object->isNew()) {
				$this->response->notFound();
			} // if
		} else {
			$this->response->notFound();
		} // if

		$logged_user = Authentication::getLoggedUser();
		if ($this->active_object instanceof Project) {
			if (!$logged_user->isAdministrator() && !$this->active_object->isLeader($logged_user)) {
				$this->response->forbidden();
			} // if
		} elseif ($this->active_object instanceof ProjectObject) {
			if (!$logged_user->isAdministrator() && !$this->active_object->getProject()->isLeader($logged_user)) {
				$this->response->forbidden();
			} // if
		} elseif ($this->active_object instanceof Invoice) {
			if (!$logged_user->isAdministrator() && !$logged_user->isFinancialManager()) {
				$this->response->forbidden();
			} // if
		} else {
			if (!$logged_user->isAdministrator()) {
				$this->response->forbidden();
			} // if
		} // if
	} // __before

	/**
	 * List access logs
	 */
	function access_logs() {
		$this->response->assign(array(
			'data' => $this->active_object->accessLog()->getAll()
		));
	} // access_logs

}