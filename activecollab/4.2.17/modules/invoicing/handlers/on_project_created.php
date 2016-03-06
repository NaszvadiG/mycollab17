<?php

  /**
   * on_project_created event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_project_created event
   *
   * @param Project $project
   * @param User $user
   * @param $additional
   */
  function invoicing_handle_on_project_created(Project &$project, User &$user, $additional = null) {
    $project_based_on = $project->getBasedOn();

    if($project_based_on instanceof Quote) {
      $project_based_on->copyComments($project, $user);

      $template = $project->getTemplate();

      $create_milestones = array_var($additional,'create_milestones');

      if(!($template instanceof ProjectTemplate) && $create_milestones) {
        $project_based_on->createMilestones($project, $user);
      } // if
    } // if
  } // invoicing_handle_on_project_created