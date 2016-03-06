<?php

  /**
   * on_milestone_sections event handler implementation
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Populate chekclist section information for milestone details page
   *
   * @param Project $project
   * @param Milestone $milestone
   * @param User $user
   * @param NamedList $sections
   * @param string $interface
   */
  function todo_handle_on_milestone_sections(&$project, &$milestone, &$user, &$sections, $interface) {
  	if(TodoLists::canAccess($user, $project) && array_key_exists('todo_lists', $project->getTabs($user,$interface)->toArray())) {
  		$section = array(
        'text' => lang('Todo Lists'),
        'url' => Router::assemble('milestone_todo_lists', array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone->getId())),
        'options' => array(),
      );
      
      $sections->add('todo_lists', $section);
  	} // if
  } // todo_handle_on_milestone_sections