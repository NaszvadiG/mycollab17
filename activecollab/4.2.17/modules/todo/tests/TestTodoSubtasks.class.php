<?php

  class TestTodoSubtasks extends AngieModelTestCase {
  
    private $logged_user;
    
    private $active_project;
    
    private $active_todo_list;
    
    function setUp() {
      parent::setUp();
      
      $this->logged_user = Users::findById(1);
      
      $this->active_project = new Project();
      $this->active_project->setAttributes(array(
        'name' => 'Application', 
        'leader_id' => 1, 
        'company_id' => 1, 
      ));
      $this->active_project->save();
      
      $this->active_todo_list = new TodoList();
      $this->active_todo_list->setName('Todo List 1');
      $this->active_todo_list->setProject($this->active_project);
      $this->active_todo_list->setCreatedBy($this->logged_user);
      $this->active_todo_list->setState(STATE_VISIBLE);
      $this->active_todo_list->setVisibility(VISIBILITY_NORMAL);
      $this->active_todo_list->save();
    } // setUp
    
    function tearDown() {
      $this->logged_user = null;
      $this->active_project = null;
      $this->active_todo_list = null;
    } // tearDown
    
    function testInitialization() {
      $this->assertTrue($this->logged_user->isLoaded(), 'Test user loaded');
      $this->assertTrue($this->active_project->isLoaded(), 'Test project created');
      $this->assertTrue($this->active_todo_list->isLoaded(), 'Test todo list created');
    } // testInitialization
    
    function testCreation() {
      $subtask1 = $this->active_todo_list->subtasks()->newSubtask();
      
      $subtask1->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask1->setCreatedBy($this->logged_user);
      $subtask1->setState(STATE_VISIBLE);
      $subtask1->save();
      
      $this->assertTrue($subtask1->isLoaded(), 'Subtask created');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::ANY, false), 1, 'Subtask is saved to database');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 1, 'Number of open subtasks is OK');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 0, 'Number of completed subtasks is OK');
      
      $subtask2 = $this->active_todo_list->subtasks()->newSubtask();
      
      $subtask2->setAttributes(array(
        'body' => 'Subtask text 2', 
      ));
      $subtask2->setCreatedBy($this->logged_user);
      $subtask2->setState(STATE_VISIBLE);
      $subtask2->save();
      
      $this->assertTrue($subtask2->isLoaded(), 'Subtask is created');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::ANY, false), 2, 'Subtask is saved to database');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 2, 'Number of subtasks is properly updated');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 0, 'Number of completed subtasks is OK');
    } // testCreation
    
    function testSubtaskCompletion() {
      $this->assertNull($this->active_todo_list->getCompletedOn(), 'Completed on field has OK value');
      $this->assertFalse($this->active_todo_list->complete()->isCompleted(), 'List is open');
      
      $subtask1 = $this->active_todo_list->subtasks()->newSubtask();
      
      $subtask1->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask1->setCreatedBy($this->logged_user);
      $subtask1->setState(STATE_VISIBLE);
      $subtask1->save();
      
      $this->assertTrue($subtask1->isLoaded(), 'Subtask is created');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::ANY, false), 1, 'Subtask is saved to database');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 1, 'Number of open subtasks is OK');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 0, 'Number of completed subtasks is OK');
      
      $subtask1->complete()->complete($this->logged_user);
      
      $this->assertIsA($subtask1->getCompletedOn(), 'DateTimeValue', 'Subtask completed_on has OK value');
      $this->assertTrue($subtask1->complete()->isCompleted(), 'Subtask is completed');
      
      $this->active_todo_list = TodoLists::findById($this->active_todo_list->getId()); // Reload
      
      $this->assertNull($this->active_todo_list->getCompletedOn(), 'List completed_on has OK value');
      $this->assertFalse($this->active_todo_list->complete()->isCompleted(), 'List is open');
      
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 0, 'Number of open subtasks is updated');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 1, 'Number of completed subtasks is updated');
    } // testSubtaskCompletion
    
    function testTodolistCompletesSubtasks() {
      $this->assertNull($this->active_todo_list->getCompletedOn(), 'List completed_on has OK value');
      $this->assertFalse($this->active_todo_list->complete()->isCompleted(), 'List is open');
      
      $subtask1 = $this->active_todo_list->subtasks()->newSubtask();
      
      $subtask1->setAttributes(array(
        'body' => 'Subtask text', 
      ));
      $subtask1->setCreatedBy($this->logged_user);
      $subtask1->setState(STATE_VISIBLE);
      $subtask1->save();
      
      $this->assertTrue($subtask1->isLoaded(), 'Subtask is created');
      
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 1, 'Only one open subtaks');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 0, 'No completed subtasks');
      
      $this->active_todo_list->complete()->complete($this->logged_user);
      
      $this->assertIsA($this->active_todo_list->getCompletedOn(), 'DateTimeValue', 'List completed_on has OK value');
      $this->assertTrue($this->active_todo_list->complete()->isCompleted(), 'List is open');
      
      $subtask1 = Subtasks::findById($subtask1->getId());
      
      $this->assertIsA($subtask1->getCompletedOn(), 'DateTimeValue', 'Subtask was automatically completed');
      $this->assertTrue($subtask1->complete()->isCompleted(), 'Subtask was automatically completed');
      
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::OPEN), 0, 'Number of open subtasks has been updated');
      $this->assertEqual($this->active_todo_list->subtasks()->count($this->logged_user, ISubtasksImplementation::COMPLETED), 1, 'Number of completed subtasks has been updated');
    } // testTodolistCompletesSubtasks
    
  }