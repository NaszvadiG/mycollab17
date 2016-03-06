<?php

  /**
   * MS Project Manager module on_object_options event handler
   *
   * @package activeCollab.modules.ms_project_manager
   * @subpackage handlers
   */
  
  /**
   * Handle on project options event
   *
   * @param ApplicationObject $object
   * @param User $user
   * @param NamedList $options
   * @param string $interface
   */
  function ms_project_manager_handle_on_object_options(&$object, &$user, &$options, $interface) {
    if($object instanceof Project && ($user->isProjectManager() || $object->isLeader($user))) {
      $options->add('ms_project_manager', array(
        'url' => Router::assemble('ms_project_manager', array('project_slug' => $object->getSlug())),
        'text' => lang('MS Project Manager'),
        'onclick' => new FlyoutCallback()
      ));
    } //
  } // project_exporter_handle_on_object_options