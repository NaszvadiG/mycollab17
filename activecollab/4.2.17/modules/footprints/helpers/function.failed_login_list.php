<?php

/**
 * Failed login list implementation
 *
 * @package activeCollab.modules.footprints
 * @subpackage helpers
 */

/**
 * Render failed login list from date value
 *
 * @param array $param
 * @return string
 */
function smarty_function_failed_login_list($param) {
	$user = array_required_var($param, 'user', true, 'User');
	$data = array_var($param, 'data');

	// sort logs by dates
	$sorted_by_dates = array();
	if (is_foreachable($data)) {
		foreach ($data as $log) {
			$event_on = array_required_var($log, 'event_on', false, 'DateTimeValue');

			if ($event_on instanceof DateTimeValue) {
				$date_string = $event_on->format('Y-m-d');

				if (!isset($sorted_by_dates[$date_string])) {
					$sorted_by_dates[$date_string] = array();
				} // if

				$sorted_by_dates[$date_string][] = $log;
			} // if
		} // foreach
	} // if

	// prepare content for table tag if data exist
	$content = '';
	if (is_foreachable($sorted_by_dates)) {
		foreach ($sorted_by_dates as $date_string => $logs) {
			$date = DateValue::makeFromString($date_string);

			$user_formatted_date = $date->formatDateForUser($user);

			if($date->isToday()) {
				$th_content = '<span class="today" title="'. $user_formatted_date .'">' . lang('Today') . '</span>';
			} elseif($date->isYesterday()) {
				$th_content = '<span class="yesterday" title="' . $user_formatted_date . '">' . lang('Yesterday') . '</span>';
			} else {
				$th_content = '<span class="date">' . $user_formatted_date . '</span>';
			} // if

			$th = HTML::openTag('th', array('class' => 'date', 'align' => 'center', 'colspan' => '2'), $th_content);
			$tr = HTML::openTag('tr', null, $th);
			$thead = HTML::openTag('thead', null, $tr);

			$tbody_content = '';
			if (is_foreachable($logs)) {
				foreach ($logs as $log) {
					$event_on = array_required_var($log, 'event_on', false, 'DateTimeValue');

					$login_ip = array_var($log, 'user_ip', lang('unknown'));
					$is_api = array_var($log, 'is_api', false);
					$text = (!$is_api) ? 'Try as <b>:email</b> from <b>:ip_address</b>' : 'Try as <b>:email</b> from <b>:ip_address</b> through API (:user_agent)';
					$subject = lang($text, array(
						'ip_address'  => $ip_address = $login_ip == '127.0.0.1' || $login_ip == '::1' ? lang('localhost') : $login_ip,
						'email' => array_var($log, 'user_email', lang("Unknown")),
						'user_agent' => array_var($log, 'user_agent')
					));

					$timestamp = $event_on->formatTimeForUser($user);

					$td = HTML::openTag('td', array('class' => 'subject'), $subject);
					$td .= HTML::openTag('td', array('class' => 'timestamp'), $timestamp);

					$tbody_content .= HTML::openTag('tr', null, $td);
				} // foreach
			} // if

			if (!empty($tbody_content)) {
				$tbody = HTML::openTag('tbody', null, $tbody_content);
				$content .= HTML::openTag('table', array('class' => 'common'), $thead . $tbody);
			} // if
		} // foreach
	} // if

	$message_empty_list = HTML::openTag('p', array('class' => 'empty_page', 'style' => 'display: block;'), lang('There is no activities'));

	return !empty($content) ? $content : $message_empty_list;
} // smarty_function_past