<{wrap_php}>
  /**
   * Base<{$association_class_name}> class
   *
   * @package <{$application}>.modules.<{$module}>
   * @subpackage models
   */
  abstract class Base<{$association_class_name}> extends DataAssociationHasMany {

    /**
     * Return name of the source model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    protected function getSourceModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? '<{$source_underscore_signular}>' : '<{$source_singular}>';
      } else {
        return $underscore ? '<{$source_underscore_plural}>' : '<{$source_plural}>';
      } // if
    } // getSourceModelName

    /**
     * Return name of the target model
     *
     * @param boolean $underscore
     * @param boolean $singular
     * @return string
     */
    protected function getTargetModelName($underscore = false, $singular = false) {
      if($singular) {
        return $underscore ? '<{$target_underscore_signular}>' : '<{$target_singular}>';
      } else {
        return $underscore ? '<{$target_underscore_plural}>' : '<{$target_plural}>';
      } // if
    } // getTargetModelName

  }<{/wrap_php}>