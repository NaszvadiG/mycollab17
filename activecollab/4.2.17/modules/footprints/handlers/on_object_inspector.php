<?php

/**
 * Footprints module on_object_inspector events handler
 *
 * @package activeCollab.modules.footprints
 * @subpackage handlers
 */

/**
 * Populate object inspector
 *
 * @param IInspectorImplementation $inspector
 * @param IInspector $object
 * @param IUser $user
 * @param string $interface
 */
function footprints_handle_on_object_inspector(IInspectorImplementation &$inspector, IInspector &$object, IUser &$user, $interface) {
	if ($object instanceof File) {
		$inspector->addProperty('number_of_downloads', lang('Number of Downloads'), new NoOfDownloadsInspectorProperty($object->download()->count()));
	}
} // files_handle_on_object_inspector