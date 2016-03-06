<?php

  // We need projects controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * MS Project Manager controller
   *
   * @package activeCollab.modules.ms_project_manager
   * @subpackage controllers
   */
  class MsProjectManagerController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = MS_PROJECT_MANAGER_MODULE;
    
      
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      // create folder for exporting projects if does not exist
      if (!is_dir(WORK_PATH . '/ms_project_export')) {
        create_dir(WORK_PATH . '/ms_project_export', true);
      } //if
      
      if(!$this->logged_user->isAdministrator() && !$this->active_project->isLeader($this->logged_user) && !$this->logged_user->isProjectManager()) {
        $this->response->forbidden();
      } // if

      $this->wireframe->hidePrintButton();
      $this->smarty->assign(array(
        'active_project' => $this->active_project,
        'logged_user'    => $this->logged_user
      ));
    } //__before
    
    /**
     * Index - main page for MS Project Manager
     */
    function index() {

      $is_writable = folder_is_writable(MSPROJECT_EXPORT_PATH);
      if (!$is_writable) {
        $this->smarty->assign(array(
          'is_writable' => false,
        )); //array
      } else {
      	$upload_url = Router::assemble('ms_project_manager_upload', array('project_slug' => $this->active_project->getSlug()));
      	$download_url = Router::assemble('ms_project_manager_download', array('project_slug' => $this->active_project->getSlug()));
      	$this->smarty->assign(array(
          'is_writable' => true,
          'upload_url' => $upload_url,
          'download_url' => $download_url,
          'visibility' => $this->active_project->getDefaultVisibility(),
          'max_file_size' => get_max_upload_size()
        )); //array
      } //if
    } //index

    /**
     * Upload - upload and parses data
     *
     * @param void
     * @return null
     */

    function upload() {
      if ($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        if (!isset($_FILES['xmlfile']) || !is_uploaded_file($_FILES['xmlfile']['tmp_name'])) {
          $this->response->respondWithData(array(
            'xml_valid' => false,
            'message' => lang('Please select an XML file'),
          )); //array
        } else {
          // create importer and insert data from XML
          $ms_project_importer = new MsProjectManagerImporter();

          $import_error = $ms_project_importer->insertXmlFromFile($_FILES['xmlfile']);
          $filename = $ms_project_importer->uploadFile($_FILES['xmlfile']);

          if($import_error instanceof Error) {
            $this->response->respondWithData(array(
              'xml_valid' => false,
              'message' => lang('Error! :message',array('message' => $import_error->getMessage())),
            )); //array
          } elseif (!$filename) {
            $this->response->respondWithData(array(
              'xml_valid' => false,
              'message' => lang('Error! :message',array('message' => lang('Failed to upload temporary XML file'))),
            )); //array
          } else {
            $this->response->assign(array(
              'project_details' => $ms_project_importer->getProjectDetails(),
              'project_objects' => $ms_project_importer->getProjectObjects(),
              'import_url' => Router::assemble('ms_project_manager_import', array('project_slug' => $this->active_project->getSlug(), 'filename' => $filename)),
            )); //array
          } // if
        } //if
      } else {
        $this->response->badRequest();
      } //if
    } //upload
    
    /**
     * Import - imports data to activeCollab project
     *
     * @param void
     * @return null
     */
    function import() {
      if ($this->request->isSubmitted() && $this->request->isAsyncCall()) {
        $filename_hash = $this->request->get('filename');
        $details = $this->request->post('project_details');
        $milestones = $this->request->post('milestones');
        $tasks = $this->request->post('tasks');

        $filename = MsProjectManagerImporter::filenameValid($filename_hash);
        if ($filename) {
          $filepath = WORK_PATH.'/ms_project_export/'.$filename;
          $file = array(
            'name' => $filename,
            'tmp_name' => $filepath,
            'size' => filesize($filepath),
            'type' => 'text/xml'
          ); //array
          $ms_project_importer = new MsProjectManagerImporter();
          $ms_project_importer->insertXmlFromFile($file);
          $project_objects = $ms_project_importer->getProjectObjects();
          $imported = false;

          if ($details != NULL) {
            $imported = $ms_project_importer->insertDetails($ms_project_importer->getProjectDetails(),$this->active_project);
          } //if
          if (count($milestones) >0){
            $imported = $ms_project_importer->insertMilestones($milestones,$project_objects,$this->active_project->getId(),$this->logged_user);
          } //if
          if (count($tasks) >0){
            $imported = $ms_project_importer->insertTasks($tasks,$project_objects,$this->active_project->getId(),$this->logged_user);
          } //if

          if ($imported) {
            $ms_project_importer->deleteFile($filepath);
            $this->response->respondWithData(array(
              'imported' => true,
            )); //array
          } else {
            $this->response->respondWithData(array(
              'imported' => false,
              'message' => lang('Failed to import MS Project Data'),
            )); //array
          } //if
        } else {
          $this->response->respondWithData(array(
            'imported' => false,
            'message' => lang('Failed to import MS Project Data'),
          )); //array
        } //if
      } else {
        $this->response->badRequest();
      } //if
    } //import
    
    /**
     * Download - export an XML MS Project file for download
     *
     * @param void
     * @return null
     */
    function download() {
    	if ($this->request->isSubmitted()) {
	    	$visibility = $this->request->post('visibility');
	    	$write_task = $this->request->post('write_task') ? true : false;
	    	$write_todo =  $this->request->post('write_todo') ? true : false;
	    	$exportObject = new MsProjectManagerExporter($this->active_project,$this->logged_user,$visibility,$write_task,$write_todo);
			$exportObject->ExportProject();
    	} //if	
    	$this->response->redirectTo('ms_project_manager', array('project_slug' => $this->active_project->getSlug()));
    } // download
  }