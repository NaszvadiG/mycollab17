<?php

  /**
   * Rebuild language index
   * 
   * @package angie.tools.cli_commands
   */
  class CLICommandLangIndex extends CLICommandExecutable {
  	
  	const DICTIONARY_SERVERSIDE = 'serverside';
  	const DICTIONARY_CLIENTSIDE = 'clientside';

  	const EXTENSION_PHP = 'php';
  	const EXTENSION_TPL = 'tpl';
  	const EXTENSION_JS = 'js';
    
    /**
     * Command description
     *
     * @var string
     */
    var $description = 'Rebuild language index';

    /**
		 * Pattern for dictionary file names
     */
    var $dictionary_file_name = '/resources/dictionary.--DICTIONARY-NAME--.php';
    
    /**
     * Patterns
     * 
     * @var array
     */
    var $patterns = array(
      array(
      	"pattern"				=> "/\{title[^}]*?\}(.*?)\{\/title\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE 
      ),
      array(
      	"pattern"				=> "/\{label[^}]*?\}(.*?)\{\/label\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE 
      ),
      array(
      	"pattern"				=> "/\{lang[^}]*?\}(.*?)\{\/lang\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE 
      ),
      array(
      	"pattern"				=> "/\{link[^}]*?\}(.*?)\{\/link\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE 
      ),
      array(
      	"pattern"				=> "/\{add_bread_crumb[^}]*?\}(.*?)\{\/add_bread_crumb\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE 
      ),
      array(
      	"pattern"				=> "/\{button[^}]*?\}(.*?)\{\/button\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{submit[^}]*?\}(.*?)\{\/submit\}/is",
      	"result_index"	=> 1,
       	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
       	"pattern"				=> "/\{link[^}]*?confirm='(.*?)'[^}]*?\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
				"pattern"				=> '/\{link[^}]*?confirm="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{select_visibility[^}]*?normal_caption='(.*?)'[^}]*?\}/is",
       	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/\{select_visibility[^}]*?normal_caption="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{select_visibility[^}]*?private_caption='(.*?)'[^}]*?\}/is",
       	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/\{select_visibility[^}]*?private_caption="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{[^}]*?title='(.*?)'[^}]*?\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/\{[^}]*?title="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{[^}]*?label='(.*?)'[^}]*?\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/\{[^}]*?label="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
        "pattern"				=> "/\{[^}]*?yes_text='(.*?)'[^}]*?\}/is",
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL),
        "dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
        "pattern"				=> '/\{[^}]*?yes_text="(.*?)"[^}]*?\}/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL),
        "dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
        "pattern"				=> '/\{[^}]*?no_text="(.*?)"[^}]*?\}/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL),
        "dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
        "pattern"				=> "/\{[^}]*?no_text='(.*?)'[^}]*?\}/is",
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL),
        "dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/\{[^}]*?confirm='(.*?)'[^}]*?\}/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/\{[^}]*?confirm="(.*?)"[^}]*?\}/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_TPL),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      
      
      array(
      	"pattern"				=> "/flash_error\('(.*?)'.*?\)/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_PHP),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/flash_success\('(.*?)'.*?\)/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_PHP),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> "/lang\('(.*?)'.*?\)/is",
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_PHP),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/flash_error\("(.*?)".*?\)/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_PHP),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/flash_success\("(.*?)".*?\)/is',
      	"result_index"	=> 1,
      	"extensions" 		=> array(self::EXTENSION_PHP),
       	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
      array(
      	"pattern"				=> '/lang\("(.*?)".*?\)/is',
      	"result_index"	=> 1,
       	"extensions" 		=> array(self::EXTENSION_PHP),
      	"dictionary"		=> self::DICTIONARY_SERVERSIDE
      ),
       
        
			array(
				"pattern"				=> "/App.lang\('(.*?)'.*?\)/is",
				"result_index"	=> 1,
       	"extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
      	"dictionary"		=> self::DICTIONARY_CLIENTSIDE
			),
      array(
      	"pattern"				=> '/App.lang\("(.*?)".*?\)/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
        "dictionary"		=> self::DICTIONARY_CLIENTSIDE
      ),
			array(
				"pattern"				=> "/App.Wireframe.Flash.success\('(.*?)'.*?\)/is",
				"result_index"	=> 1,
       	"extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
      	"dictionary"		=> self::DICTIONARY_CLIENTSIDE
			),
      array(
      	"pattern"				=> '/App.Wireframe.Flash.success\("(.*?)".*?\)/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
        "dictionary"		=> self::DICTIONARY_CLIENTSIDE
      ),
			array(
				"pattern"				=> "/App.Wireframe.Flash.error\('(.*?)'.*?\)/is",
				"result_index"	=> 1,
       	"extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
      	"dictionary"		=> self::DICTIONARY_CLIENTSIDE
			),
      array(
      	"pattern"				=> '/App.Wireframe.Flash.error\("(.*?)".*?\)/is',
        "result_index"	=> 1,
        "extensions" 		=> array(self::EXTENSION_TPL, self::EXTENSION_JS, self::EXTENSION_PHP),
        "dictionary"		=> self::DICTIONARY_CLIENTSIDE
      ),
    );
    
    /**
     * Invalid patterns
     * 
     * @var array
     */
    var $invalid_patterns = array(
      '/^\{[^}].*?\}$/is',
      '/^<[^>].*?>$/is',
      '/\{lang/is',
    	'/App.lang/is',
    );
    
    /**
     * Invalid phrases that should be ignored
     * 
     * @var array
     */
    var $invalid_phrases = array('!','@','#','$','%','^','&','*','(',')','[', ']','\\',';',',','.','/','`','§','±','<','>', '?',':','\"','{','}','`');
    
    /**
     * Sorted patterns by extension
     * 
     * @var array
     */
    var $sorted_patterns = false;
        
    /**
     * Result of the parsing
     * 
     * @var array
     */
    var $phrases = array();
    
    /**
     * Number of phrases found
     * 
     * @var integer
     */
    var $phrases_found = 0;
    
    /**
     * Number of phrases accepted
     * 
     * @var integer
     */
    var $phrases_accepted = 0;
    
    /**
     * Number of phrases denied
     * 
     * @var integer
     */
    var $phrases_denied = 0;
    
    /**
     * Number of save errors ocurred
     * 
     * @var integer
     */
    var $save_errors = 0;
    
    /**
     * Check if posible lang is valid
     *
     * @param string $result
     * @return boolean
     */
    function validate_possible_lang($result) {
      $result = trim($result);
      
      if (!$result) {
        return false;
      } // if
      
      if (in_array($result, $this->invalid_phrases)) {
        return false;
      } // if

      if(str_ends_with($result, '.') && str_starts_with($result, '.')) {
        return false; // joined PHP string
      } // if
      
      if (is_foreachable($this->invalid_patterns)) {
        foreach ($this->invalid_patterns as $invalid_pattern) {
        	if (preg_match($invalid_pattern, $result)) {
        	  return false;
        	} // if
        } // foreach
      } // if
      
      return true;
    } // validate_possible_lang
    
    /**
     * Get patterns for $extension
     * 
     * @param string $extension
     * @return array
     */
    function get_patterns($extension) {
    	if ($this->sorted_patterns === false) {
				foreach ($this->patterns as $pattern) {
					foreach ($pattern['extensions'] as $pattern_extension) {
						if (!isset($this->sorted_patterns[$pattern_extension])) {
							$this->sorted_patterns[$pattern_extension] = array();
						} // if
						
						$this->sorted_patterns[$pattern_extension][] = array(
							'pattern'				=> $pattern['pattern'],
							'dictionary'		=> $pattern['dictionary'],
							'result_index'	=> $pattern['result_index']
						);
					} // foreach
				} // foreach  		
    	} // if
    	
    	return array_var($this->sorted_patterns, $extension, null);
    } // get_patterns
    
    /**
     * Process the phrase
     * 
     * @param string $phrase
     * @param string $destination
     * @param string $dictionary
     */
		function process_phrase($phrase, $destination, $dictionary) {			
			$this->phrases_found ++;
			
			if ($this->validate_possible_lang($phrase)) {
        if (!isset($this->phrases[$destination])) {
					$this->phrases[$destination] = array();
				} // if
				
				if (!isset($this->phrases[$destination][$dictionary])) {
					$this->phrases[$destination][$dictionary] = array();
				} // if
				
				if (!in_array($phrase, $this->phrases[$destination][$dictionary])) {
					$this->phrases[$destination][$dictionary][] = $phrase;
					$this->phrases_accepted ++;
				} // if
			} else {
				$this->phrases_denied ++;
			} // if
		} // process_phrase
		
		/**
		 * Save all results in folders they belong
		 */
		function save_results() {
			if (!is_foreachable($this->phrases)) {
				return false;
			} // if
			
			foreach ($this->phrases as $directory => $dictionaries) {

				if (is_foreachable($dictionaries)) {
					foreach ($dictionaries as $dictionary_name => $dictionary_phrases) {
						if (is_foreachable($dictionary_phrases)) {
							
							$content = "<?php return array(\n";
							foreach ($dictionary_phrases as $dictionary_phrase) {
								$content.= '  ' . var_export($dictionary_phrase, true) . ",\n";
							} // foreach
							$content.= '); ?>';
							
							$destination = $directory . str_replace('--DICTIONARY-NAME--', $dictionary_name, $this->dictionary_file_name);
							
							if (!file_put_contents($destination, $content)) {
								$this->save_errors ++;
								echo 'Failed to output file: ' . $destination . "\n";							
							} // if
						} // if
					} // foreach
				}
				
			} // foreach			
		} // save_results
  
    /**
     * Execute the command
     *
     * @param Output $output
     */
    function execute(Output $output) {
      CLI::initEnvironment($output);
      
      // list all folders
      $sources = array_merge(
      	(array) get_folders_with_priority(ANGIE_PATH . '/frameworks', array( ENVIRONMENT_FRAMEWORK )),
      	(array) get_folders_with_priority(APPLICATION_PATH . '/modules', array( SYSTEM_MODULE ))
      );
      
      if (!is_foreachable($sources)) {
      	return false;
      } // if
      
      // loop through sources and find files
      foreach ($sources as $source) {
      	// get files in this source
      	$files = get_files($source, array(self::EXTENSION_JS, self::EXTENSION_PHP, self::EXTENSION_TPL), true);
      	
      	// loop through all files in this source and parse them
      	foreach ($files as $file) {
					$content = file_get_contents($file);
					$patterns = $this->get_patterns(strtolower(get_file_extension($file, false)));
					
					// loop through all patterns that can be applied to this file
					foreach ($patterns as $pattern) {
						$results = array();
						if (preg_match_all($pattern["pattern"], $content, $results)) {
							foreach($results[$pattern["result_index"]] as $phrase) {
								// process al matched phrases
								$this->process_phrase($phrase, $source, $pattern['dictionary']);
							} // if
						}	// if
					} // foreach
					
      	} // foreach      	

      } // foreach
      
      $this->save_results();   

      echo "\n\n---------------------------------------------------------------------\n";
      echo "      Phrases found: $this->phrases_found\n";
      echo "      Phrases accepted: $this->phrases_accepted\n";
      echo "      Phrases ignored: $this->phrases_denied\n";
      echo "---------------------------------------------------------------------\n";
      
      if ($this->save_errors) {
      	echo "      Save errors: $this->save_errors\n";
      	echo "---------------------------------------------------------------------\n"; 
      } // if
      
      echo "\n";
    } // execute
  
  }