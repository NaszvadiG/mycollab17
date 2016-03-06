<?php

  /**
   * Todo lists handle on_object_inserted event
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */
  
  /**
   * Handle on_object_inserted event
   *
   * @param ProjectObject $object
   */
  function todo_handle_on_object_inserted(&$object) {
    if($object instanceof Subtask) {
      if($object->getParent() instanceof TodoList && $object->getParent()->complete()->isCompleted()) {
        $object->getParent()->complete()->open($object->getCreatedBy());
      } // if
    } // if
  } // todo_handle_on_object_inserted