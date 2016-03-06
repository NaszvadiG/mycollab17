<{wrap_php}>
  /**
   * Base<{$manager_class}> class
   *
   * @package <{$application}>.modules.<{$module}>
   * @subpackage models
   */
  abstract class Base<{$manager_class}> extends <{$base_manager_extends}> {

    /**
     * Return name of this model
     *
     * @param boolean $underscore
     * @return string
     */
    static function getModelName($underscore = false) {
      return $underscore ? '<{$model_name_underscore}>' : '<{$model_name}>';
    } // getModelName

    /**
     * Return name of the table where system will persist model instances
     *
     * @param boolean $with_prefix
     * @return string
     */
    static function getTableName($with_prefix = true) {
      return $with_prefix ? TABLE_PREFIX . '<{$table_name}>' : '<{$table_name}>';
    } // getTableName

    /**
     * Return class name of a single instance
     *
     * @return string
     */
    static function getInstanceClassName() {
      return '<{$object_class}>';
    } // getInstanceClassName

    /**
     * Return whether instance class name should be loaded from a field, or based on table name
     *
     * @return string
     */
    static function getInstanceClassNameFrom() {
      return <{$class_name_from}>;
    } // getInstanceClassNameFrom

    /**
     * Return name of the field from which we will read instance class
     *
     * @return string
     */
    static function getInstanceClassNameFromField() {
      return '<{$class_name_from_field}>';
    } // getInstanceClassNameFrom

    /**
     * Return name of this model
     *
     * @return string
     */
    static function getDefaultOrderBy() {
      return '<{$order_by}>';
    } // getDefaultOrderBy
  
  }<{/wrap_php}>