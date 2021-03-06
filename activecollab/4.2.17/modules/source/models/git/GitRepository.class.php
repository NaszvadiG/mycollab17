<?php

  /**
   * GIT repository implementeation
   * 
   * @package activeCollab.modules.source
   * @subpackage models
   */
  class GitRepository extends SourceRepository implements IRoutingContext {
  
    /**
     * Return revision number based on revision name
     * 
     * @param string $revision_name
     * @return integer
     */
    function getRevisionNumber($revision_name) {
      return (integer) DB::executeFirstCell("SELECT revision_number FROM " . TABLE_PREFIX . 'source_commits WHERE repository_id = ? AND type = ? AND name = ?', $this->getId(), 'GitCommit', $revision_name);
    } // getRevisionNumber
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'admin_source_git_repository';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('source_repository_id' => $this->getId());
    } // getRoutingContextParams

    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $result = parent::describe($user, $detailed, $for_interface);
      $result['project_count'] = count($this->getProjects());
      
      $result['urls']['usage'] = $this->getUsageUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      throw new NotImplementedError(__METHOD__);
    } // describeForApi
    
    /**
     * Return source repository project usage URL
     * 
     * @return string
     */
    function getUsageUrl() {
    	return Router::assemble('admin_source_git_repository_usage',array('source_repository_id' => $this->getId()));
    } //getUsageUrl
    
    /**
     * Use specific version control engine
     *
     * @return boolean
     */
    function loadEngine() {
      require_once SOURCE_MODULE_PATH . "/engines/git.class.php";
      return true;
    } //loadEngine
    
    /**
     * Get information about latest commit(s). Count is the number of the commmits
     * 
     * @param int $count 
     * @param boolean $always_array
     *
     * @return GitCommit or array of GitCommit
     */
    function getLastCommit($branch_name, $count = 1, $always_array = false) {
    	$count = intval($count);
    	if ($count == 1) {
    		if ($always_array) {
		  	  return SourceCommits::find(array(
		      	'conditions'  => array('repository_id = ? AND `type` = ? AND branch_name = ?', $this->getId(), 'GitCommit', $branch_name),
		        'order'       => 'commited_on DESC',
		        'limit'         => 1
		      ));
    		} else {
    			return SourceCommits::find(array(
		      	'conditions'  => array('repository_id = ? AND `type` = ? AND branch_name = ?', $this->getId(), 'GitCommit', $branch_name),
		        'order'       => 'commited_on DESC',
		        'one'         => true
		      ));
    		} //if
    	} elseif ($count > 1) {
    		return SourceCommits::find(array(
	      	'conditions'  => array('repository_id = ? AND `type` = ? AND branch_name = ?', $this->getId(), 'GitCommit', $branch_name),
	        'order'       => 'commited_on DESC',
	        'limit'         => $count
	      ));
    	} else {
    		return false;
    	} //if
    } //getLastCommit

    /**
     * Returns SourceCommit by revision
     *
     * @param int $revision
     * @param string $branch_name
     *
     * @return SourceCommit
     */
    function getCommitByRevision($revision, $branch_name) {
    	return BaseSourceCommits::find(array(
          'conditions'  => array('revision_number = ? AND repository_id = ? AND type = ? AND branch_name = ?', $revision, $this->getId(), 'GitCommit', $branch_name),
          'one'         => true
        ));
    }//getCommitByRevision
    
    /**
     * Returns object of repository engine
     *
     * @return Object of engine
     */
    function getEngine($project_id = null,$project_object_repository = null) {
      return ($project_object_repository) ? new GitRepositoryEngine($project_object_repository,$project_id) : new GitRepositoryEngine($this,$project_id);
    } //getEngine
    
    /**
     * Tests if connection parameters for this repository are valid
     *
     * @return boolean
     */
    function testRepositoryConnection() {
      return GitRepositoryEngine::testRepositoryConnection($this);
    } //testRepositoryConnection
    
    /**
     * Tests if engines can be used
     *
     * @return boolean
     */
    function engineIsUsable() {
      return GitRepositoryEngine::isUsable() === true ? true : false;
    } //engineIsUsable
    
    /**
     * Return new instance of GitCommit
     * 
     * @return GitCommit
     */
    function getNewCommit() {
      return new GitCommit();
    } //getNewCommit
    
    /**
     * Returns the name of the repository commit
     * 
     * @return string
     */
    function getCommitName() {
      return "GitCommit";
    } //getCommitName

    /**
     * Returns the name of the repository in real language
     *
     * @return string
     */
    function getVerboseName() {
      return lang('Git');
    } //getVerboseName

    /**
     * Returns the name of icon file representing the repository
     *
     * @return string
     */
    function getIconFileName() {
      return 'git-icon.png';
    } //getIconFileName

    /**
     * Returns true if repository has branches
     *
     * @return bool
     */
    function hasBranches() {
      return true;
    } //hasBranches

    /**
     * Returns the name of the default branch or false
     *
     * @return string or FALSE
     */
    function getDefaultBranch() {
      return 'master';
    } //getDefaultBranch

  }