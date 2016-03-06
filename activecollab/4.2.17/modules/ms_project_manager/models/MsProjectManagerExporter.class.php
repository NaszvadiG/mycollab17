<?php

	/**
   * Class for exporting MS Project XML files
   * MS Project Manager module
   * 
   * @package activeCollab.modules.ms_project_manager
   * @subpackage models
   *
   */
  class MsProjectManagerExporter {
  	
  	/**
     * Project which will be exported into XML file
     *
     * @var Project $active_project
     */
    protected $active_project;

    /**
     * Logged User
     *
     * @var User $logged_user
     */
    protected $logged_user;
    
    /**
     * Determines visibility of the exported project objects
     *
     * @var integer
     */
    private $visibility;
    
    /**
     * Counter for MS Project UID
     *
     * @var integer
     */
    private $uid = 1;
    
    /**
     * Counter for MS Project First Outline Number
     *
     * @var integer
     */
    private $first_outline_number = 0;
    
    /**
     * Counter for MS Project Second Outline Number
     *
     * @var integer
     */
    private $second_outline_number = 0;
    
    /**
     * Counter for MS Project Third Outline Number
     *
     * @var integer
     */
    private $third_outline_number = 0;
    
    /**
     * Determines if the string 'Task: ' will be written in front of task names in exported project
     *
     * @var boolean
     */
    private $write_task;
    
    /**
     * Determines if the string 'Todolist: ' will be written in front of todolist names in exported project
     *
     * @var boolean
     */
    private $write_todolist;
    
    /**
     * Constructor MsProjectManagerExporter
     *
     * @param Project $project
     * @param User $user
     * @param integer $visibility
     * @param boolean $write_task
     * @param boolean $write_todolist
     * @return MsProjectManagerExporter
     */
  function __construct($project,$user,$visibility,$write_task,$write_todolist) {

		$this->active_project = $project;
    $this->logged_user = $user;
		$this->visibility = $visibility;
		$this->write_task = $write_task;
		$this->write_todolist = $write_todolist;
	}//__construct
    
    /**
     * Exports a project for a download
     *
     * @param void
     * @return null
     */
	public function exportProject() {
		$empty_project_path = MS_PROJECT_EXPORTER_MODULE_PATH. '/xml/empty_project.xml';
		$xml = simplexml_load_file($empty_project_path);
		$sxe = new SimpleXMLElement($xml->asXML());
		$this->ImportProjectDetails($sxe);
		$this->importMilestones($sxe);

		$this->importTasks($sxe);
		//$this->importTodoLists($sxe);
		$file_path = WORK_PATH.'/ms_project_export/'.str_replace('/', '_', $this->active_project->getName()).'.xml';
		$sxe->asXML($file_path);
		download_file($file_path,'application/octet-stream',null,true);
		
	}// exportProject
	
	/**
     * Exports a project for a download
     *
     * @param void
     * @return null
     */
	private function importProjectDetails($sxe) {

		$sxe->Name = $this->active_project->getName();
		$sxe->Author = $this->active_project->getCreatedByName();
		if ($this->active_project->getCreatedOn() != null) {
			$sxe->CreationDate = str_replace(' ','T',$this->active_project->getCreatedOn()->format('Y-m-d H:i:s')); 
		} else {
			$sxe->CreationDate ='';
		} //if
		if ($this->active_project->getUpdatedOn() != null) {
			$sxe->LastSaved = str_replace(' ','T',$this->active_project->getUpdatedOn()->format('Y-m-d H:i:s')); 
		} else {
			$sxe->LastSaved ='';
		} //if
		$sxe->StartDate ='';	
		$sxe->CurrentDate = str_replace(' ','T',date('Y-m-d H:i:s',time()+get_user_gmt_offset()));
	}//importProjectDetails
	
	/**
     * Imports milestones and all of its subtasks into a SimpleXMLElement object
     *
     * @param SimpleXMLElement $sxe
     * @return null
     */
	private function importMilestones($sxe) {
		
		$milestones = new Milestones();
		foreach ($milestones->findAllByProject($this->active_project,$this->visibility) as $milestone) {
			$milestone_for_import = $this->getEmptyTask();
			$milestone_for_import->UID = $this->uid;
			$milestone_for_import->ID = $this->uid;
			$milestone_for_import->Name = str_replace('&','&amp;',$milestone->getName());
			$milestone_for_import->CreateDate = str_replace(' ','T',$milestone->getCreatedOn()->format('Y-m-d H:i:s')); 
			$milestone_for_import->Priority = $this->transformPriority($milestone->getPriority());

			if ($milestone->getStartOn()) {
        $this->simplexmlInsertNewAfter('Start', 'Priority', $milestone_for_import);
        $this->simplexmlInsertNewAfter('ManualStart', 'Duration', $milestone_for_import);

				$milestone_for_import->Start = str_replace(' ','T',$milestone->getStartOn()->format('Y-m-d H:i:s'));
				$milestone_for_import->ManualStart = str_replace(' ','T',$milestone->getStartOn()->format('Y-m-d H:i:s'));
			} else {
        $this->simplexmlInsertNewAfter('StartText', 'Priority', $milestone_for_import);
      }//if

			if ($milestone->getDueOn()) {
        $this->simplexmlInsertNewAfter('Finish', 'Start', $milestone_for_import);
        $this->simplexmlInsertNewAfter('ManualFinish', 'Duration', $milestone_for_import);

				$milestone_for_import->Finish = str_replace(' ','T',$milestone->getDueOn()->format('Y-m-d H:i:s'));
				$milestone_for_import->ManualFinish = str_replace(' ','T',$milestone->getDueOn()->format('Y-m-d H:i:s'));

        $this->simplexmlInsertNewAfter('Deadline', 'CalendarUID', $milestone_for_import);
        $milestone_for_import->Deadline = str_replace(' ','T',$milestone->getDueOn()->format('Y-m-d H:i:s'));
			} else {
        $this->simplexmlInsertNewAfter('FinishText', 'Priority', $milestone_for_import);
      }//if

			if ($milestone->getBody()) {
				$milestone_for_import->Notes = str_replace('&','&amp;',$milestone->getBody());
			} //if
			$milestone_for_import->Milestone = 1;
			$this->first_outline_number++;
			$milestone_for_import->OutlineNumber = $this->first_outline_number;
			$milestone_for_import->OutlineLevel = 1;
			$tasks = new Tasks();
			if (count($tasks->findByMilestone($milestone,STATE_VISIBLE,$this->visibility)) > 0) {
				$milestone_for_import->Summary = 1;
			} //if
			$this->simplexmlAppendTask($sxe,$milestone_for_import);
			$this->uid++;
			$this->importTasks($sxe,$milestone,$milestone_for_import->UID);
			$this->importTodoLists($sxe,$milestone,$milestone_for_import->UID);
			$this->second_outline_number = 0;
			$this->third_outline_number = 0;
		} //foreach
	} //importMilestones
	
	/**
     * Imports tasks and all of its subtasks into a SimpleXMLElement object
     *
     * @param SimpleXMLElement $sxe
     * @param Milestone $milestone
     * @param integer $milestone_uid
     * @return null
     */
	private function importTasks(&$sxe,$milestone = null, $milestone_uid = null) {
    if (!$this->active_project->hasTab('tasks',$this->logged_user)) {
      return false;
    } //if
		$tasks = new Tasks();
		if ($milestone) { 
			$tasks = $tasks->findByMilestone($milestone,STATE_VISIBLE,$this->visibility);
			$has_milestone = true;
		} else {
			$tasks = ProjectObjects::find(array(
		      'conditions' => array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', 
			   0,$this->active_project->getId(), 'Task', STATE_VISIBLE, $this->visibility), 'order' => 'priority DESC',
		    ));
		    $has_milestone = false;
		} //if
		foreach ($tasks as $task) {
			$task_for_import = $this->getEmptyTask();
			$task_for_import->UID = $this->uid;
			$task_for_import->ID = $this->uid;
			$name = ($this->write_task) ? 'Task: '.$task->getName() : $task->getName(); 
			$task_for_import->Name = str_replace('&','&amp;',$name);
			$task_for_import->CreateDate = str_replace(' ','T',$task->getCreatedOn()->format('Y-m-d H:i:s')); 
			$task_for_import->Priority = $this->transformPriority($task->getPriority());
      $this->simplexmlInsertNewAfter('StartText', 'Priority', $task_for_import);

			if ($task->getDueOn()) {
        $this->simplexmlInsertNewAfter('Finish', 'Priority', $task_for_import);
        $this->simplexmlInsertNewAfter('ManualFinish', 'Duration', $task_for_import);

				$task_for_import->Finish = str_replace(' ','T',$task->getDueOn()->format('Y-m-d H:i:s'));
				$task_for_import->ManualFinish = str_replace(' ','T',$task->getDueOn()->format('Y-m-d H:i:s'));

        $this->simplexmlInsertNewAfter('Deadline', 'CalendarUID', $task_for_import);
        $task_for_import->Deadline = str_replace(' ','T',$task->getDueOn()->format('Y-m-d H:i:s'));
			} else {
        $this->simplexmlInsertNewAfter('FinishText', 'StartText', $task_for_import);
      }//if

			if ($task->getBody()) {
				$task_for_import->Notes = str_replace('&','&amp;',$task->getBody());
			} //if
			if ($milestone) {
				$this->second_outline_number++;
				$task_for_import->IsSubproject = 1;
				$task_for_import->OutlineNumber = "$this->first_outline_number.$this->second_outline_number";
				$task_for_import->OutlineLevel = 2;
			} else {
				$this->first_outline_number++;
				$task_for_import->OutlineNumber = $this->first_outline_number;
				$task_for_import->OutlineLevel = 1;
			} //if
			$subtasks = new Subtasks();
			if (count($subtasks->findByParent($task,STATE_VISIBLE,$this->visibility)) > 0) {
				$task_for_import->Summary = 1;
			} //if
			$this->simplexmlAppendTask($sxe,$task_for_import,$milestone_uid);
			$this->uid++;
			$this->importSubTasks($sxe,$task,$task_for_import->UID,$has_milestone);
			$has_milestone ? $this->third_outline_number = 0 : $this->second_outline_number = 0;
		} //foreach
    return true;
	}//importTasks
	
	/**
     * Imports todolists and all of its subtasks into a SimpleXMLElement object
     *
     * @param SimpleXMLElement $sxe
     * @param Milestone $milestone
     * @param integer $milestone_uid
     * @return null
     */
	private function importTodoLists(&$sxe,$milestone = null, $milestone_uid = null) {
    if (!$this->active_project->hasTab('todo_lists',$this->logged_user)) {
      return false;
    } //if
		$todolists = new TodoLists();
		if ($milestone) {
			$todolists = $todolists->findByMilestone($milestone,STATE_VISIBLE,$this->visibility);
			$has_milestone = true;
		} else {
			$todolists = ProjectObjects::find(array(
		      'conditions' => array('milestone_id = ? AND project_id = ? AND type = ? AND state >= ? AND visibility >= ?', 
			   0,$this->active_project->getId(), 'TodoList', STATE_VISIBLE, $this->visibility), 'order' => 'priority DESC',
		    ));
		    $has_milestone = false;
		} //if
		foreach ($todolists as $todolist) {
			$todolist_for_import = $this->getEmptyTask();
			$todolist_for_import->UID = $this->uid;
			$todolist_for_import->ID = $this->uid;
			$name = ($this->write_todolist) ? 'Todolist: '.$todolist->getName() : $todolist->getName(); 
			$todolist_for_import->Name = str_replace('&','&amp;',$name);
			$todolist_for_import->CreateDate = str_replace(' ','T',$todolist->getCreatedOn()->format('Y-m-d H:i:s')); 
			$todolist_for_import->Priority = $this->transformPriority($todolist->getPriority());
			if ($todolist->getDueOn()) {
				$todolist_for_import->Finish = str_replace(' ','T',$todolist->getDueOn()->format('Y-m-d H:i:s'));
			} //if
			if ($todolist->getBody()) {
				$todolist_for_import->Notes = str_replace('&','&amp;',$todolist->getBody());
			} //if
			if ($milestone) {
				$this->second_outline_number++;
				$todolist_for_import->IsSubproject = 1;
				$todolist_for_import->OutlineNumber = "$this->first_outline_number.$this->second_outline_number";
				$todolist_for_import->OutlineLevel = 2;
			} else {
				$this->first_outline_number++;
				$todolist_for_import->OutlineNumber = $this->first_outline_number;
				$todolist_for_import->OutlineLevel = 1;
			} //if
			$subtasks = new Subtasks();
			if (count($subtasks->findByParent($todolist,STATE_VISIBLE,$this->visibility)) > 0) {
				$todolist_for_import->Summary = 1;
			} //if
			$this->simplexmlAppendTask($sxe,$todolist_for_import,$milestone_uid);
			$this->uid++;
			$this->importSubTasks($sxe,$todolist,$todolist_for_import->UID,$has_milestone);
			$has_milestone ? $this->third_outline_number = 0 : $this->second_outline_number = 0;
		} //foreach
    return true;
	}//importTodoLists
	
	/**
     * Imports subtasks into a SimpleXMLElement object
     *
     * @param SimpleXMLElement $sxe
     * @param Task/Todolist $task
     * @param integer $task_uid
     * @param boolean $has_milestone
     * @return null
     */
	private function importSubTasks (&$sxe,$task,$task_uid,$has_milestone) {
		$subtasks = new Subtasks();
		foreach ($subtasks->findByParent($task,STATE_VISIBLE,$this->visibility) as $subtask ){
			$subtask_for_import = $this->getEmptyTask();
			$subtask_for_import->UID = $this->uid;
			$subtask_for_import->ID = $this->uid;
			$subtask_for_import->Name = str_replace('&','&amp;',$subtask->getBody());
			$subtask_for_import->CreateDate = str_replace(' ','T',$subtask->getCreatedOn()->format('Y-m-d H:i:s')); 
			$subtask_for_import->Priority = $this->transformPriority($subtask->getPriority());
      $this->simplexmlInsertNewAfter('StartText', 'Priority', $subtask_for_import);

			if ($subtask->getDueOn()) {
        $this->simplexmlInsertNewAfter('Finish', 'Priority', $subtask_for_import);
        $this->simplexmlInsertNewAfter('ManualFinish', 'Duration', $subtask_for_import);

				$subtask_for_import->Finish = str_replace(' ','T',$subtask->getDueOn()->format('Y-m-d H:i:s'));
				$subtask_for_import->ManualFinish = str_replace(' ','T',$subtask->getDueOn()->format('Y-m-d H:i:s'));

        $this->simplexmlInsertNewAfter('Deadline', 'CalendarUID', $subtask_for_import);
        $subtask_for_import->Deadline = str_replace(' ','T', $subtask->getDueOn()->format('Y-m-d H:i:s'));
			} else {
        $this->simplexmlInsertNewAfter('FinishText', 'StartText', $subtask_for_import);
      }//if

			$subtask_for_import->IsSubproject = 1;
			if ($has_milestone) {
				$this->third_outline_number++;
				$subtask_for_import->OutlineNumber = "$this->first_outline_number.$this->second_outline_number.$this->third_outline_number";
				$subtask_for_import->OutlineLevel = 3;
			} else {
				$this->second_outline_number++;
				$subtask_for_import->OutlineNumber = "$this->first_outline_number.$this->second_outline_number";
				$subtask_for_import->OutlineLevel = 2;
			} //if
			$this->simplexmlAppendTask($sxe,$subtask_for_import,$task_uid);
			$this->uid++;
		}//foreach	
	} //importSubTasks
	
	/**
     * Transforms priority value from activeCollab to MS Project format
     *
     * @param integer $ac_priority
     * @return integer
     */
    private function transformPriority($ac_priority) {
    	return (($ac_priority+2) * 250);
    } //transformPriority
	
    /**
     * Insert one task into SimpleXMLElement object
     *
     * @param SimpleXMLElement $parent
     * @param SimpleXMLElement $new_child
     * @param integer $parent_UID
     * @return null
     */
	private function simplexmlAppendTask(SimpleXMLElement &$parent, SimpleXMLElement $new_child,$parent_UID = null) {
		$new_task = $parent->Tasks->addChild('Task');
		foreach ($new_child->children() as $name=>$value) {
			$new_task->addChild($name,$value);
		} //foreach
//		if (($parent_UID != null) && ($new_task->IsSubproject)) {
//			$predecessor = $new_task->addChild('PredecessorLink');
//    		$predecessor->addChild('PredecessorUID',$parent_UID);
//    		$predecessor->addChild('Type',1);
//    		$predecessor->addChild('CrossProject',0);
//    		$predecessor->addChild('LinkLag',0);
//    		$predecessor->addChild('LagFormat',7);
//		} //if
    } //simplexmlAppendTask

    /**
     * Insert insert a new SimpleXMLElement after some other SimpleXMLElement object
     *
     * @param string $new
     * @param string $after
     * @param SimpleXMLElement $sxe
     * @return null
     */
    function simplexmlInsertNewAfter($new, $after, SimpleXMLElement $sxe) {
      $insert = new SimpleXMLElement('<'.$new.'/>');
      $target = current($sxe->xpath('//'.$after.'[last()]'));

      $target_dom = dom_import_simplexml($target);
      $insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
      if($target_dom->nextSibling) {
        return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
      } else {
        return $target_dom->parentNode->appendChild($insert_dom);
      } // if
    } // simplexmlInsertNewAfter
    
    /**
     * Inserts an empty task into SimpleXMLElement
     *
     * @param void
     * @return SimpleXMLElement
     */
    private function getEmptyTask() {
    	return new SimpleXMLElement(
					 '<Task>
					  <UID></UID> 
					  <ID></ID>
					  <Name></Name> 
					  <Manual>1</Manual>
					  <Type>1</Type>
					  <IsNull>0</IsNull> 
					  <CreateDate></CreateDate> 
					  <WBS>0</WBS> 
					  <OutlineNumber>0</OutlineNumber> 
					  <OutlineLevel>0</OutlineLevel> 
					  <Priority>500</Priority>
					  <Notes></Notes>
					  <Duration>PT0H0M0S</Duration> 
					  <DurationFormat>53</DurationFormat> 
					  <Work>PT0H0M0S</Work> 
					  <ResumeValid>0</ResumeValid> 
					  <EffortDriven>0</EffortDriven> 
					  <Recurring>0</Recurring> 
					  <OverAllocated>0</OverAllocated> 
					  <Estimated>1</Estimated> 
					  <Milestone>0</Milestone> 
					  <Summary>0</Summary> 
					  <Critical>1</Critical> 
					  <IsSubproject>0</IsSubproject> 
					  <IsSubprojectReadOnly>0</IsSubprojectReadOnly> 
					  <ExternalTask>0</ExternalTask> 
					  <EarlyStart></EarlyStart> 
					  <EarlyFinish></EarlyFinish> 
					  <LateStart></LateStart> 
					  <LateFinish></LateFinish> 
					  <StartVariance>0</StartVariance> 
					  <FinishVariance>0</FinishVariance> 
					  <WorkVariance>0</WorkVariance> 
					  <FreeSlack>0</FreeSlack> 
					  <TotalSlack>0</TotalSlack> 
					  <FixedCost>0</FixedCost> 
					  <FixedCostAccrual>3</FixedCostAccrual> 
					  <PercentComplete>0</PercentComplete> 
					  <PercentWorkComplete>0</PercentWorkComplete> 
					  <Cost>0</Cost> 
					  <OvertimeCost>0</OvertimeCost> 
					  <OvertimeWork>PT0H0M0S</OvertimeWork> 
					  <ActualDuration>PT0H0M0S</ActualDuration> 
					  <ActualCost>0</ActualCost> 
					  <ActualOvertimeCost>0</ActualOvertimeCost> 
					  <ActualWork>PT0H0M0S</ActualWork> 
					  <ActualOvertimeWork>PT0H0M0S</ActualOvertimeWork> 
					  <RegularWork>PT0H0M0S</RegularWork> 
					  <RemainingDuration>PT0H0M0S</RemainingDuration> 
					  <RemainingCost>0</RemainingCost> 
					  <RemainingWork>PT0H0M0S</RemainingWork> 
					  <RemainingOvertimeCost>0</RemainingOvertimeCost> 
					  <RemainingOvertimeWork>PT0H0M0S</RemainingOvertimeWork> 
					  <ACWP>0</ACWP> 
					  <CV>0</CV> 
					  <ConstraintType>0</ConstraintType> 
					  <CalendarUID>-1</CalendarUID> 
					  <LevelAssignments>1</LevelAssignments> 
					  <LevelingCanSplit>1</LevelingCanSplit> 
					  <LevelingDelay>0</LevelingDelay> 
					  <LevelingDelayFormat>8</LevelingDelayFormat> 
					  <IgnoreResourceCalendar>0</IgnoreResourceCalendar> 
					  <HideBar>0</HideBar> 
					  <Rollup>0</Rollup> 
					  <BCWS>0</BCWS> 
					  <BCWP>0</BCWP> 
					  <PhysicalPercentComplete>0</PhysicalPercentComplete> 
					  <EarnedValueMethod>0</EarnedValueMethod> 
					  <ActualWorkProtected>PT0H0M0S</ActualWorkProtected> 
					  <ActualOvertimeWorkProtected>PT0H0M0S</ActualOvertimeWorkProtected>
					  </Task>');
    } // getEmptyTask
  }