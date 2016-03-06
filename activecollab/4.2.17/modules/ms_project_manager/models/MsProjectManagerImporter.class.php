<?php

	/**
   * Class for importing MS Project XML files
   * MS Project Manager module
   * 
   * @package activeCollab.modules.ms_project_manager
   * @subpackage models
   *
   */
  class MsProjectManagerImporter {
  	
    /**
     * Information about uploaded file
     *
     * @var DOMDocument $dom_document
     */
    private $dom_document;

    /**
     * Simple XML objext
     *
     * @var SimpleXMLElement $simple_xml
     */
    private $simple_xml;

    /**
     * Namespaces for Ms Project XML
     *
     * @var array $namespaces
     */
    private $namespaces = array(
      'http://schemas.microsoft.com/project',
      'http://schemas.microsoft.com/developer/msbuild/2003',
      'http://schemas.microsoft.com/developer/msbuild/2008',
      'http://schemas.microsoft.com/developer/msbuild/2010'
    );

    /**
	 * Nodes which determine if a xml is a valid MS project 
	 *
	 * @var array
	 */
    
    private $important_nodes = array(
      0 => 'Name',
      1 => 'StartDate',
      2 => 'CreationDate',
      3 => 'Tasks',
      4 => 'Calendars',
      5 => 'Assignments',
      6 => 'Resources'
    );//array

    /**
     * Project/ProjectObject name field length
     *
     * @var integer
     */
    private $name_length = 150;

    /**
     * Constructor initialises DomDocument
     */
    function __construct() {
      $this->dom_document = new DOMDocument();
    } //__construct

    /**
     * Uploads and inserts MS project data from XML file
     *
     * @param string $xml_file
     *
     * @return mixed true or Error
     */
    public function insertXmlFromFile($xml_file) {
      if ($xml_file['error'] > 0 || $xml_file['type'] !== 'text/xml') {
        return new Error(lang('Please upload a valid Microsoft Project XML file'));
      } else {
        if (!$this->dom_document->load($xml_file['tmp_name'])) {
          return new Error(lang('XML file is not valid'));
        } //if

        $namespace = $this->dom_document->documentElement->lookupnamespaceURI(NULL);
        if (!in_array($namespace,$this->namespaces)) {
          return new Error(lang('XML file namespace is valid MS Project namespace'));
        } //if

        $items = $this->dom_document->getElementsByTagName('Project');
        if ($items->length > 0) {
          foreach ($this->important_nodes as $node) {
            if (!($this->nodeHasChild($items->item(0),$node))) {
              return new Error(lang("XML file is not valid MS Project XML file - \"$node\" node not found"));
              break;
            }//if
          }//foreach
        } else {
          return new Error(lang('XML file is not valid MS Project XML file - "Project" node not found'));
        } //if
      } //if

      //initializing simple XML
      $this->simple_xml = simplexml_import_dom($this->dom_document);

      return true;
    } //insertXmlFromFile

    /**
     * upload_file - uploads a file and returns sha-1 encrypted file path if upload was successful
     *
     * @param string $xml_file
     * @return mixed FALSE or string
     */
    public function uploadFile($xml_file)
    {
      $xmlfile_tmp_name = $xml_file['tmp_name'];
      $xmlfile_name = $xml_file['name'];
      $upload_dir = WORK_PATH.'/ms_project_export/';
      $i = 0;
      $file_path = $upload_dir.'tmpxmlmsprojectfile'.$i.$xmlfile_name;
      while (file_exists($file_path)) {
        $i++;
        $file_path = $upload_dir.'tmpxmlmsprojectfile'.$i.$xmlfile_name;
      }//while

      $upload = move_uploaded_file($xmlfile_tmp_name,$file_path);

      if ($upload){
        return sha1($file_path);
      } else {
        return FALSE;
      }//if
    } //uploadFile
		
     /**
       * get_project_details - parsing project details from file
       *
       * @param void
       * @return array
       */
    public function getProjectDetails()
    {
      $xml_details = array();
      $items = $this->dom_document->getElementsByTagName('Project');

      if ($this->nodeHasChild($items->item(0),'Title')) {
        $project_name = $this->getOneNodeValue($this->dom_document,'Project','Title');
      } else {
        $project_name = $this->removeExt($this->getOneNodeValue($this->dom_document,'Project','Name'));
      }//if

      $xml_details['name'] = strlen($project_name) > $this->name_length ? substr($project_name, 0, $this->name_length - 3) . '...' : $project_name;
      $xml_details['starts_on'] = $this->transformDate($this->getOneNodeValue($this->dom_document,'Project','StartDate'));
      $xml_details['created_on'] = $this->transformDate($this->getOneNodeValue($this->dom_document,'Project','CreationDate'));

      if ($this->nodeHasChild($items->item(0),'Subject')) {
        $xml_details['overview'] = $this->getOneNodeValue($this->dom_document,'Project','Subject');
      }//if
      return ($xml_details);
    }//getProjectDetails
	
     /**
       * get_project_details - parsing project tasks from xml file
       *
       * @param void
       * @return array
       */
    public function getProjectObjects()
    {

      $r = $this->simple_xml->xpath('Project/Task/Name');
      $tasks_xml = $this->simple_xml->Tasks;
      $milestones = array();
      $tasks = array();
      $subtasks = array();
      foreach ($tasks_xml->Task as $task_row) {
        if ($this->isMilestone($task_row)) {
          $milestones[] = self::simpleXmlToArray($this->delPredecessors($task_row));
        }//if
      }//foreach

      foreach ($tasks_xml->Task as $task_row) {

        if ($this->isTask($task_row)) {
          $parent = $this->getDirectParent($task_row);

          if (!$parent) {
            $task_row->IsSubproject = -1;
          } else {
            $task_row->IsSubproject = (integer)$parent->UID;
          }//if
          $tasks[] = self::simpleXmlToArray($task_row);
        }//if
      }//foreach
      foreach ($tasks_xml->Task as $task_row) {
        if ($this->isSubtask($task_row)) {
          $parent = $this->getTaskParent($task_row);
          $task_row->IsSubproject = (integer)$parent->UID;
          $subtasks[] = self::simpleXmlToArray($task_row);
        }//if
      }//foreach
      return array(
        'milestones' => $milestones,
        'tasks' => $tasks,
        'subtasks' => $subtasks
        );
    }//getProjectObjects
	
	 /**
     * node_has_child - checks if a node has children (optionally you can insert child name
     * and than it will check if a child with that name exist) 
     *
     * @param $p
     * @param [opt]$child_name
     * @return bool
     */
    private function nodeHasChild($p, $child_name = NULL)
    {
      if ($p->hasChildNodes()) {
         foreach ($p->childNodes as $c) {
          if ($c->nodeType == XML_ELEMENT_NODE) {
            if ($child_name == NULL) {
              return TRUE;
            } else {
              if ($c->nodeName == $child_name){
                return TRUE;
              }//if
            }//if
          }//if
         }//foreach
        }//if
        return FALSE;
     }//nodeHasChild
	 
     /**
       * get_node_value - gets value of subnodes in node from DOMdocument
       * (optionally you can insert subnode name -it will return only subnodes with that name)
       *
       * @param DOMDocument $xdoc,$main_node, [opt]$sub_node_name
       * @return mixed array or NULL
       */
    function getOneNodeValue($xdoc,$main_node,$sub_node_name) {
      $subNode = $xdoc->getElementsByTagName($main_node);
      foreach($subNode as $subTag) {
        foreach($subTag->childNodes as $sub) {
                if ($sub_node_name == $sub->nodeName) {
                return $sub->nodeValue;
              }//if
            }//foreach
       }//foreach
      return null;
    }//getOneNodeValue


     /**
       * returns MS task by UID
       *
       * @param int $id
       * @return $xml_task object
       */

    private function getTaskById($id) {
      $tasks = $this->simple_xml->Tasks;

      foreach ($tasks->Task as $task_row) {
        if ($task_row->UID == $id && $task_row->IsNull == 0) {
          return $task_row;
        }//if
      }//foreach
      return FALSE;
    }//getTaskById
	 
     /**
       * checks if a MS task is a AC Milestone
       *
       * @param $xml_task object
       * @return bool
       */
    private function isMilestone($xml_task)
    {
      return ($xml_task->Milestone == 1 && $xml_task->IsNull == 0 )? TRUE:FALSE;
    }//isMilestone

     /**
       * checks if a MS task is a AC Task
       *
       * @param $xml_task object
       * @return bool or $xml_task milestone
       */
    private function isTask($xml_task)
    {
      if ($xml_task->Milestone == 0 && $xml_task->IsNull == 0 && $xml_task->ID != 0) {
        $parent = $this->getDirectParent($xml_task);
        if (! $parent OR $this->isMilestone($parent)) {
          return TRUE;
        }//if
      }//if
      return FALSE;
    }//isTask

     /**
       * checks if a MS task is a AC Subtask
       *
       * @param $xml_task object
       * @return bool
       */
    private function isSubtask($xml_task)
    {
      return ($xml_task->IsNull == 0 && !$this->isMilestone($xml_task)
      && !$this->isTask($xml_task))? TRUE:FALSE;
    }//isSubtask
	 
     /**
       * returns task for subtask
       *
       * @param $xml_task object
       * @return bool FALSE or $xml_task
       */
    private function getTaskParent($xml_task)
    {
      foreach ($xml_task->PredecessorLink as $key => $xml_pred_row) {
        $pred_id = (integer)$xml_pred_row->PredecessorUID;
        $xml_task_parent = array(0 => $this->getTaskById($pred_id));
        $i = 0;

        while (! $this->isTask($xml_task_parent[$i])) {
          $i++;
          $xml_task_parent[$i] = $this->getDirectParent($xml_task_parent[$i-1]);
        }//while
        return $xml_task_parent[$i];
      }//foreach
      return FALSE;
    }//getTaskParent

     /**
       * returns first predecessor
       *
       * @param $xml_task object
       * @return bool FALSE or $xml_task
       */
    private function getDirectParent($xml_task)
    {
      foreach ($xml_task->PredecessorLink as $key => $xml_pred_row) {
        $pred_id = (integer)$xml_pred_row->PredecessorUID;
        $xml_task_parent = $this->getTaskById($pred_id);
        return $xml_task_parent;
      }//foreach
      return FALSE;
    }//getDirectParent
	 
     /**
       * delete all predecessors from task
       *
       * @param $xml_task object
       * @return $xml_task object
       */
    private function delPredecessors($xml_task)
    {
      foreach ($xml_task->PredecessorLink as $key => $xml_pred_row) {
        unset($xml_task->PredecessorLink[$key]);
      }//foreach
      return $xml_task;
    }//delPredecessors
	 
     /**
       * removes extension from file and returns name of file
       *
       * @param $string string
       * @return $string string
       */
    private function removeExt($string)
    {
      if ((strlen($string) < 5) || (substr($string,strlen($string)-4) != '.xml')) {
        return $string;
      } //if
      while ( $string[strlen($string)-1] != '.') {
        $string = substr($string,0,strlen($string)-1);
      }//while
      return substr($string,0,strlen($string)-1);
    }//removeExt
	 
     /**
     * gets array of milestones UID and inserts milestones with its subobjects
     *
     * @param $milestones array, $project_objects object, $project object
     * @return bool
     */
    public function insertMilestones($milestones,$project_objects,$project_id,$logged_user) {
     	foreach ($milestones as $UID) {
     		$milestone_row = $this->getTaskById($UID);
     		$milestone_id = $this->insertOneMilestone($milestone_row,$project_id,$logged_user);

        $tasks = array();

     		foreach ($project_objects['tasks'] as $task_row) {
     			if ($task_row['IsSubproject'] == $UID) {
     				$tasks[] = (integer)$task_row['UID'];
     			}//if
     		}//foreach

     		$this->insertTasks($tasks,$project_objects,$project_id,$logged_user,$milestone_id);
     	}//foreach
     	return TRUE;
    }//insertMilestones
     
     /**
     * gets array of tasks UID and inserts tasks with its subobjects
     *
     * @param $tasks array, $project_objects object, $project object
     * @return bool
     */
  	public function insertTasks($tasks,$project_objects,$project_id,$logged_user,$milestone_id = 0) {
     	foreach ($tasks as $UID) {
     		$task_row = $this->getTaskById($UID);
     		$task_id = $this->insertOneTask($task_row,$project_id,$logged_user,$milestone_id);
     		
     		foreach ($project_objects['subtasks'] as $task_row) {
     			if ($task_row['IsSubproject'] == $UID) {
     				$this->insertOneSubtask($task_row,$logged_user,$task_id);
     			} //if
     		} //foreach
     	} //foreach
     	return TRUE;
    } //insertTasks
     
     /**
     * gets array of project details inserts it in AC project
     *
     * @param array $details
     * @param Project $project
     * @return bool
     */
  	public function insertDetails($details,$project) {
     	$details['created_on'] = $this->transformDate($details['created_on']);
     	$project->setAttributes($details);
     	$project->save();
     	return TRUE;
    } //insertDetails
     
     /**
     * gets task and inserts it as milestone. Returns id of inserted milestone
     *
     * @param $task_row object
     * @return int $milestone_id
     */
    private function insertOneMilestone($task_row,$project_id,$logged_user) {
     	$attributes = array(
	     	'name' => strlen($task_row->Name) > $this->name_length ? substr($task_row->Name, 0, $this->name_length - 3) . '...' : $task_row->Name,
	     	'start_on' => $this->transformDate($task_row->Start),
	     	'due_on' => $this->transformDate($task_row->Finish),
	     	'project_id' => $project_id,
	     	'priority' => $this->transformPriority((integer)$task_row->Priority
     	));//array
     	
     	if (isset ($task_row->Notes)) {
     		$attributes['body'] = '<p>'.$task_row->Notes.'</p>';
     	}//if
     	
     	$milestone = new Milestone();
     	$milestone->setCreatedBy($logged_user);
     	$milestone->setAttributes($attributes);
     	$milestone->setState(STATE_VISIBLE);
     	$milestone->setVisibility(VISIBILITY_NORMAL); 	
     	$milestone->save();
     	return $milestone->getId();
    } //insertOneMilestone
     
 	 /**
     * gets task and inserts it as task
     *
     * @param $task_row object
     * @return int
     */
    private function insertOneTask($task_row,$project_id,$logged_user,$milestone_id = 0) {
     	$attributes = array(
	     	'name' => strlen($task_row->Name) > $this->name_length ? substr($task_row->Name, 0, $this->name_length - 3) . '...' : $task_row->Name,
	     	'priority' => $this->transformPriority($task_row->Priority),
	     	'due_on' => $this->transformDate($task_row->Finish),
	     	'project_id' => $project_id,
	     	'milestone_id' => $milestone_id
     	); //array

     	if (isset ($task_row->Notes)) {
     		$attributes['body'] = '<p>'.$task_row->Notes.'</p>';
     	} //if
     	
     	$task = new Task();
     	$task->setCreatedBy($logged_user);
     	$task->setAttributes($attributes);
     	$task->setState(STATE_VISIBLE);
     	$task->setVisibility(VISIBILITY_NORMAL); 	
     	$task->save();
     	return $task->getId();
    } //insertOneTask
     
 	 /**
     * gets task and inserts it as task
     *
     * @param array $task_row
     * @param User $logged_user
     * @param int $task_id
     *
     * @return void
     */
    private function insertOneSubtask($task_row,$logged_user,$task_id) {
     	$attributes = array(
     		'type' => 'ProjectObjectSubtask',
	     	'body' => $task_row['Name'],
	     	'due_on' => $this->transformDate($task_row['Finish']),
	     	'parent_id' => $task_id,
	     	'parent_type' => 'Task',
	     	'priority' => $this->transformPriority($task_row['Priority'])
     	);//array
     	$subtask = new ProjectObjectSubtask();
     	$subtask->setCreatedBy($logged_user);
     	$subtask->setAttributes($attributes);
     	$subtask->setState(STATE_VISIBLE);
     	$subtask->save();
    }//insertOneTask
     
     /**
     * deletes xml file from server
     *
     * @param void
     * @return bool
     */
    public function deleteFile($filepath = NULL) {
     	if ($filepath != NULL) {
     		$f = fopen($filepath, 'w');
        if ($f != FALSE) {
          fclose($f);
          if (unlink($filepath)) {
            return TRUE;
          }//if
        }//if
     	}//if
     	return FALSE;
    }//deleteFile
     
     /**
     * transforms MS Project priority value into AC pr. value
     *
     * @param $priority integer
     * @return integer
     */
    private function transformPriority($priority) {
     	if ($priority > 999) {
     		$priority = 999;
     	} //if
     	if ($priority < 0) {
     		$priority = 0;
     	} //if
     	return (integer)((floor($priority/200))-2);
    } //transformPriority
     
     /**
     * transforms MS Project date value into AC pr. value
     *
     * @param $date string
     * @return $string
     */
    private function transformDate($date) {
     	return str_replace_first('T',' ',$date);
    } //transformDate
    
    /**
     * checks if filename is valid. This method is static
     *
     * @param $filename_hash
     * @return mixed
     */
    public static function filenameValid($filename_hash) {
    	foreach(glob(WORK_PATH.'/ms_project_export/*.xml') as $filepath) {
		    if(is_dir($filepath)) {
		        continue;
		    }//if
		    if (sha1($filepath) == $filename_hash) {
		    	$filename = substr($filepath, strrpos($filepath,'/')+1,strlen($filepath)-strrpos($filepath,'/'));
		    	return $filename;
		    }//if
		  }//foreach

		  return FALSE;
    }//filenameValid

    /**
     * Method that converts SimpleXMLElement object to associative array
     *
     * @param Object $xml
     * @return array
     */

    public static function simpleXmlToArray($xml) {
      $array = json_decode(json_encode($xml), TRUE);

      foreach ( array_slice($array, 0) as $key => $value ) {
        if (empty($value)) {
          $array[$key] = NULL;
        } elseif (is_array($value)){
          $array[$key] = self::simpleXmlToArray($value);
        } //if
      } //foreach

      return $array;
    } //SimpleXmlToArray
  	
  	
  }