<?php

  /**
   * Footprints module on_object_options event handler
   *
   * @package activeCollab.modules.footprints
   * @subpackage handlers
   */
  
  /**
   * Return array of options $logged_user can do to $user account
   *
   * @param ApplicationObject $object
   * @param IUser $user
   * @param NamedList $options
   * @param string $interface
   */
  function footprints_handle_on_object_options(&$object, &$user, &$options, $interface) {
	  if ($interface == AngieApplication::INTERFACE_DEFAULT) {
		  if ($object instanceof IAccessLog) {
			  if ($user->isAdministrator() || ($object instanceof Project && $object->isLeader($user)) || ($object instanceof ProjectObject && $object->getProject()->isLeader($user)) || ($object instanceof Invoice && $user->isFinancialManager())) {
				  $options->add('access_logs', array(
					  'url' => Router::assemble($object->getRoutingContext() . '_access_logs', $object->getRoutingContextParams()),
					  'text' => lang('Access Logs'),
					  'onclick' => new FlyoutCallback(array(
						  'width' => '650'
					  )),
				  ));
			  } // if
		  } // if

		  if ($object instanceof IHistory && !($object instanceof IUser)) {
			  if ($user->isAdministrator() || ($object instanceof Project && $object->isLeader($user)) || ($object instanceof ProjectObject && $object->getProject()->isLeader($user)) || ($object instanceof Invoice && $user->isFinancialManager())) {
				  $options->add('history_of_changes', array(
					  'url' => Router::assemble($object->getRoutingContext() . '_history_of_changes', $object->getRoutingContextParams()),
					  'text' => lang('History of Changes'),
					  'onclick' => new FlyoutCallback(array(
						  'width' => '850'
					  )),
				  ));
			  } // if
		  } // if

		  if ($object instanceof ISecurityLog && ($user->isAdministrator() || ($object->getId() == $user->getId()))) {
			  $options->add('security_logs', array(
				  'url' => Router::assemble('security_logs', $object->getRoutingContextParams()),
				  'text' => lang('Security Logs'),
				  'onclick' => new FlyoutCallback(array(
					  'width' => '850'
				  )),
			  ));
		  } // if

		  if ($object instanceof User && ($user->isAdministrator() || ($object->getId() == $user->getId()))) {
			  $options->add('user_sessions', array(
				  'url' => Router::assemble($object->getRoutingContext() . '_user_sessions', $object->getRoutingContextParams()),
				  'text' => lang('User Sessions'),
				  'onclick' => new FlyoutFormCallback('user_session_removed', array(
					  'width'   => '600',
					  'height'  => '400',
					  'success_message' => lang("Selected sessions has been terminated")
				  )),
			  ));
		  } // if
	  } // if
  } // footprints_handle_on_object_options