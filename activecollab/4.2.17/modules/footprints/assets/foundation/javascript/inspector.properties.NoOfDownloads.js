/**
 * Favorite indicator handler
 */
App.Inspector.Properties.NoOfDownloads = function (object, client_interface, total) {
	var wrapper = $(this);

	wrapper.append(total);
}; 