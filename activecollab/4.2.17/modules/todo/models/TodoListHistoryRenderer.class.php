<?php

  class TodoListHistoryRenderer extends ProjectObjectHistoryRenderer {
    
    /**
     * Render single field value
     *
     * @param IUser $user
     * @param string $field
     * @param mixed $value
     * @param mixed $old_value
     * @return string
     */
    protected function renderField(IUser $user, $field, $value, $old_value) {
      
      // Category ID
      if($field == 'category_id') {
        if($value) {
          if($old_value) {
            return lang('Moved from <b>:old_value</b> category to <b>:new_value</b> category', array(
              'old_value' => $this->getCategoryInfo($user, $old_value), 
              'new_value' => $this->getCategoryInfo($user, $value), 
            ));
          } else {
            return lang('Moved to <b>:new_value</b> category', array(
              'new_value' => $this->getCategoryInfo($user, $value), 
            ));
          } // if
        } else {
          if($old_value) {
            return lang('Category set to empty value'); // This would be an error actually
          } // if
        } // if
      } // if
      
      return parent::renderField($user, $field, $value, $old_value);
    } // renderField
    
    /**
     * Map of priject IDs and names
     *
     * @var array
     */
    private $categories_map = false;
    
    /**
     * Return category info based on category ID
     *
     * @param IUser $user
     * @param integer $category_id
     * @return string
     */
    function getCategoryInfo(IUser $user, $category_id) {
      if($this->categories_map === false) {
        $this->categories_map = $user instanceof User ? Categories::getIdNameMap(null, 'TodoListCategory') : null;
      } // if
      
      return $this->categories_map && isset($this->categories_map[$category_id]) ? $this->categories_map[$category_id] : lang('Unknown Category');
    } // getCategoryInfo
    
  }