<?php

/**
 * Class ModificationLogsController
 */
class HistoryOfChangesController extends Controller {

	/**
	 * Selected object
	 *
	 * @var Object
	 */
	protected $active_object;

	/**
	 * Prepare controller
	 */
	public function __before() {
		parent::__before();

		if(!($this->active_object instanceof IHistory)) {
			$this->response->badRequest();
		} // if

		// check if class has isnew method and check if object is found
		if (method_exists($this->active_object, 'isNew') && $this->active_object->isNew()) {
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

		// check if there is a method for permission to see this object
		if (method_exists($this->active_object, 'canView') && !$this->active_object->canView($logged_user)) {
			$this->response->forbidden();
		} // if
	} // __before

	/**
	 * List access logs
	 */
	function history_of_changes() {
		$this->response->assign(array(
			'active_object' => $this->active_object,
			'looged_user'   => Authentication::getLoggedUser()
		));
	} // history_of_changes

}