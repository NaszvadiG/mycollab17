<?php

AngieApplication::useController('users', SYSTEM_MODULE);

/**
 * Class UserSessionsController
 */
class UserSessionsController extends UsersController {

	/**
	 * Active module
	 *
	 * @var string
	 */
	protected $active_module = FOOTPRINTS_MODULE;

	/**
	 * All active sessions ids for selected user
	 *
	 * @var array
	 */
	protected $active_session_ids = array();

	/**
	 * Construct user_sessions controller
	 *
	 * @param Request $parent
	 * @param string $context
	 */
	function __construct(Request $parent, $context = null) {
		parent::__construct($parent, $context);
	} // __construct

	/**
	 * Prepare controller
	 */
	public function __before() {
		parent::__before();

		if ($this->active_user->isNew()) {
			$this->response->notFound();
		} // if

		// Only administrators and users which have privileges to access this account
		if ($this->active_user->getId() != $this->logged_user->getId() && !$this->logged_user->isAdministrator()) {
			$this->response->forbidden();
		} // if

		$sessions = UsersSecurity::findSessionsForList($this->active_user);
		if (is_foreachable($sessions)) {
			foreach ($sessions as $session) {
				$this->active_session_ids[] = array_var($session, 'id');
			} // foreach
		} // if
 		$this->response->assign(array(
			'sessions' => $sessions
		));
	} // __before

	/**
	 * User sessions
	 */
	public function index() {
		$this->response->assign(array(
			'sessions_remove_url' => Router::assemble('people_company_user_sessions_remove', $this->active_user->getRoutingContextParams())
		));
	} // index

	/**
	 * Remove selected session
	 */
	public function remove() {
		if ($this->request->isAsyncCall()) {
			$session_ids = $this->request->post('session_ids');

			if ($this->request->isSubmitted()) {
				if (is_foreachable($session_ids)) {
					$sessions_to_kill = array();
					foreach($session_ids as $session_id) {
						if (!in_array($session_id, $this->active_session_ids) || Authentication::getProvider()->isSessionActive($session_id)) {
							continue;
						} // if

						$sessions_to_kill[] = $session_id;
					} // foreach

					if ($sessions_to_kill) {
						Authentication::getProvider()->killSessions($sessions_to_kill);
					} // if

					$this->response->respondWithData($sessions_to_kill, array(
						'as' => 'session_ids'
					));
				} // if
			}// if

			$this->response->respondWithContent(lang('Please, first select session you want to be terminated.'));
		} else {
			$this->response->badRequest();
		} // if
	} // remove

}