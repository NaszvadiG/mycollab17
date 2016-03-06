<?php

/**
 * Footprints module on_admin_panel event handler
 *
 * @package activeCollab.modules.footprints
 * @subpackage handlers
 */

/**
 * Set failed login tool in admin panel
 *
 * @param $admin_panel
 */
function footprints_handle_on_admin_panel(AdminPanel &$admin_panel) {
	$admin_panel->addToTools('failed_logins_admin', lang('Failed Logins'), Router::assemble('failed_logins_admin'), AngieApplication::getImageUrl('admin_panel/failed-logins.png', FOOTPRINTS_MODULE));
} // footprints_handle_on_admin_panel