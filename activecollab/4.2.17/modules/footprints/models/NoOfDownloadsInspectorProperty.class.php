<?php

/**
 * Number of downloads property definition class
 *
 * @package angie.frameworks.footprints
 * @subpackage models
 */
class NoOfDownloadsInspectorProperty extends InspectorProperty {

	/**
	 * Total number of downloads
	 *
	 * @var
	 */
	var $total;

	/**
	 * @param int $total
	 */
	function __construct($total = 0) {
		$this->total = $total;
	} // __construct

	/**
	 * Render callback for rendering
	 *
	 * @return string
	 */
	function render() {
		return '(function (field, object, client_interface) { App.Inspector.Properties.NoOfDownloads.apply(field, [object, client_interface, ' . JSON::encode($this->total) . ']) })';
	} // render
}