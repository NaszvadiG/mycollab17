<script type="text/javascript">

	(function () {
		var sessions = {$sessions|json nofilter};
		var session_list = $('#user_sessions .session_list');

		/**
		 * Render session
		 *
		 * @param session
		 */
		var render_session = function(session) {
			var render = '';

			render += '<li>';
			render +=   '<div class="s_left">';
			if (!session.is_current) {
				render +=   '<input type="checkbox" name="session_ids[]" value="'+ session.id +'" />';
			} else {
				render +=   '<span>' + App.lang('Active') + '</span>';
			} // if
			render +=   '</div>'
			render +=   '<div class="s_right">';
			render +=     '<span>' + App.lang('From') + ' <b>' + session.ip_address + '</b></span>';
			render +=     '<p>' + App.lang('Agent') + ': ' + session.user_agent + '</p>';
			render +=     '<p>' + App.lang('Last activity') + ': ' + session.time_ago + '</p>';
			render +=   '</div>';
			render += '</li>';

			var session_jquery = $(render);

			session_jquery.appendTo(session_list);
		} // render_session

		var render_no_sessions = function() {
			var render = '';

			render += '<li class="empty_list">';
			render +=   '<span>' + App.lang('No active sessions') + '</span>';
			render += '</li>';

			var no_sessions_jquery = $(render);

			no_sessions_jquery.appendTo(session_list)
		}

		/**
		 * Init sessions list
		 */
		var init_sessions = function () {
			// add existing notebooks to the list
			if (sessions && sessions.length) {
				$.each(sessions, function (index, session) {
					render_session(session);
				});
			} else {
				render_no_sessions();
			} // if
		}; // init_sessions

		init_sessions();
	}());

</script>