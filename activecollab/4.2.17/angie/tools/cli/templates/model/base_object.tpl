<{wrap_php}>
  /**
   * Base<{$object_class}> class
   *
   * @package <{$application}>.modules.<{$module}>
   * @subpackage models
   */
  abstract class Base<{$object_class}> extends <{$base_object_extends}> {
  
    /**
     * Name of the table where records are stored
     *
     * @var string
     */
    protected $table_name = '<{$table_name}>';
    
    /**
     * All table fields
     *
     * @var array
     */
    protected $fields = array(<{$field_names_as_string}>);
    
    /**
     * Primary key fields
     *
     * @var array
     */
    protected $primary_key = array('id');

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    function getModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? '<{$model_name_singular_underscore}>' : '<{$model_name_singular}>';
      } else {
        return $underscore ? '<{$model_name_underscore}>' : '<{$model_name}>';
      } // if
    } // getModelName

    /**
     * Name of AI field (if any)
     *
     * @var string
     */
    protected $auto_increment = 'id';
<{if count($belongs_to_associations)}>
<{foreach $belongs_to_associations as $association}>

    /**
     * Return parent <{$association.name}>
     *
     * @return <{$association.object_class}>
     */
    function <{$association.getter_name}>() {
      return DataObjectPool::get('<{$association.object_class}>', $this->getFieldValue('<{$association.key_field_name}>'));
    } // <{$association.getter_name}>

<{if $association.required}>
    /**
     * Set parent <{$association.name}>
     *
     * @param <{$association.object_class}> $value
     * @param boolean $save
     * @return <{$association.object_class}>
     * @throws InvalidParamError
     */
    function <{$association.setter_name}>(<{$association.object_class}> $value, $save = false) {
      if($value instanceof <{$association.object_class}>) {
        $this->setFieldValue('<{$association.key_field_name}>', $value->getId());
      } else {
        throw new InvalidParamError('value', $value, 'Instance of <{$association.object_class}> is expected');
      } // if

      if($save) {
        $this->save();
      } // if

      return $value;
    } // <{$association.setter_name}>
<{else}>
    /**
     * Set parent <{$association.name}>
     *
     * @param <{$association.object_class}>|null $value
     * @param boolean $save
     * @return <{$association.object_class}>|null
     * @throws InvalidParamError
     */
    function <{$association.setter_name}>($value, $save = false) {
      if($value instanceof <{$association.object_class}>) {
        $this->setFieldValue('<{$association.key_field_name}>', $value->getId());
      } elseif($value === null) {
        $this->setFieldValue('<{$association.key_field_name}>', null);
      } else {
        throw new InvalidParamError('value', $value, 'Instance of <{$association.object_class}> or NULL is expected');
      } // if

      if($save) {
        $this->save();
      } // if

      return $value;
    } // <{$association.setter_name}>
<{/if}>
<{/foreach}>
<{/if}><{if count($has_one_associations)}>
<{foreach $has_one_associations as $association}>

    /**
     * Return parent <{$association.name}>
     *
     * @return <{$association.object_class}>
     */
    function <{$association.getter_name}>() {
      return <{$association.manager_class}>::find(array(
        'conditions' => array('<{$association.key_field_name}> = ?', $this->getId()),
        'one' => true,
      ));
    } // <{$association.getter_name}>

<{if $association.required}>
    /**
     * Set <{$association.name}>
     *
     * @param <{$association.object_class}> $value
     * @param boolean $save
     * @return <{$association.object_class}>
     * @throws InvalidParamError
     */
    function <{$association.setter_name}>(<{$association.object_class}> $value, $save = false) {
      if($value instanceof <{$association.object_class}>) {
        $value->setFieldValue('<{$association.key_field_name}>', $value->getId());
      } else {
        throw new InvalidParamError('value', $value, 'Instance of <{$association.object_class}> is expected');
      } // if

      if($save) {
        $value->save();
      } // if

      return $value;
    } // <{$association.setter_name}>
<{else}>
    /**
     * Set <{$association.name}>
     *
     * @param <{$association.object_class}>|null $value
     * @param boolean $save
     * @return <{$association.object_class}>|null
     * @throws InvalidParamError
     */
    function <{$association.setter_name}>($value, $save = false) {
      if($value instanceof <{$association.object_class}>) {
        $value->setFieldValue('<{$association.key_field_name}>', $value->getId());
      } elseif($value === null) {
        <{$association.manager_class}>::update(array('<{$association.key_field_name}>' => null), array('<{$association.key_field_name}> = ?', $this->getId()));
      } else {
        throw new InvalidParamError('value', $value, 'Instance of <{$association.object_class}> or NULL is expected');
      } // if

      if($save) {
        $value->save();
      } // if

      return $value;
    } // <{$association.setter_name}>
<{/if}>
<{/foreach}>
<{/if}><{if count($has_many_associations)}>

    /**
     * List of association instances
     *
     * @var array
     */
    private $associations = array();
<{foreach $has_many_associations as $association}>

    /**
     * Return '<{$association.name}>' helper
     *
     * @return <{$association.class_name}>
     */
    function &<{$association.method_name}>() {
      if(!isset($this->associations['<{$association.name}>'])) {
        require_once __DIR__ . '/../<{$association.dir_name}>/<{$association.class_name}>.class.php';

        $this->associations['<{$association.name}>'] = new <{$association.class_name}>($this, <{$association.params|var_export}>);
      } // if
      return $this->associations['<{$association.name}>'];
    } // <{$association.method_name}>
<{/foreach}>

<{/if}><{if $generate_permissions}>

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can access this <{$object}>
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return true;
    } // canView
    
    /**
     * Returns true if $user can create new <{$object}>
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return true;
    } // canAdd
    
    /**
     * Returns true if $user can edit this <{$object}>
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return true;
    } // canEdit
    
    /**
     * Returns true if $user can delete this <{$object}>
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return true;
    } // canDelete
    
<{/if}><{if $generate_urls}>
    
    // ---------------------------------------------------
    //  Urls
    // ---------------------------------------------------
    
    /**
     * Return <{$object}> URL
     *
     * @return string
     */
    function getUrl() {
      return Router::assemble('<{$object}>', array('<{$object}>_id' => $this->getId()));
    } // getUrl
    
    /**
     * Return edit <{$object}> URL
     *
     * @return string
     */
    function getEditUrl() {
      return Router::assemble('<{$object}>_edit', array('<{$object}>_id' => $this->getId()));
    } // getEditUrl
    
    /**
     * Return delete <{$object}> URL
     *
     * @return string
     */
    function getDeleteUrl() {
      return Router::assemble('<{$object}>_delete', array('<{$object}>_id' => $this->getId()));
    } // getDeleteUrl
<{/if}>
    // ---------------------------------------------------
    //  Fields
    // ---------------------------------------------------
<{foreach $fields as $field}>

    /**
     * Return value of <{$field->getName()}> field
     *
     * @return <{$field->getPhpType()}>
     */
    function get<{$field->getName()|camelize}>() {
      return $this->getFieldValue('<{$field->getName()}>');
    } // get<{$field->getName()|camelize}>
    
    /**
     * Set value of <{$field->getName()}> field
     *
     * @param <{$field->getPhpType()}> $value
     * @return <{$field->getPhpType()}>
     */
    function set<{$field->getName()|camelize}>($value) {
      return $this->setFieldValue('<{$field->getName()}>', $value);
    } // set<{$field->getName()|camelize}>
<{/foreach}>

    /**
     * Set value of specific field
     *
     * @param string $name
     * @param mixed $value
     * @return mixed
     * @throws InvalidParamError
     */
    function setFieldValue($name, $value) {
      $real_name = $this->realFieldName($name);

      if($value === null) {
        return parent::setFieldValue($real_name, null);
      } else {
        switch($real_name) {
<{foreach from=$fields item=field}>
          case '<{$field->getName()}>':
            return parent::setFieldValue($real_name, <{$field->getCastingCode('$value')}>);
<{/foreach}>
        } // switch

        throw new InvalidParamError('name', $name, "Field $name (maps to $real_name) does not exist in this table");
      } // if
    } // setFieldValue
  
  }<{/wrap_php}>