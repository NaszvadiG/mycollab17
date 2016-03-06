<?php

  class TestTodoModificationLog extends AngieModelTestCase {

    function testTrackedFields() {
      $todo_list = new TodoList();
      
      $this->assertIsA($todo_list, 'TodoList', 'Todo list instance');
      $this->assertIsA($todo_list->history(), 'IHistoryImplementation', 'Valid history helper instance');
      $this->assertIsA($todo_list->history()->getRenderer(), 'TodoListHistoryRenderer', 'Valid renderer instance');
      
      $fields = $todo_list->history()->getTrackedFields();
      
      $this->assertTrue(in_array('project_id', $fields));
      $this->assertTrue(in_array('milestone_id', $fields));
      $this->assertTrue(in_array('name', $fields));
      $this->assertTrue(in_array('body', $fields));
      $this->assertTrue(in_array('state', $fields));
      $this->assertTrue(in_array('visibility', $fields));
      $this->assertTrue(in_array('category_id', $fields));
    } // testTrackedFields
    
  }