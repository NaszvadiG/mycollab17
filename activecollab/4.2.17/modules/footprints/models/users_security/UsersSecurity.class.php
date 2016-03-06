<?php

/**
 * Class SecurityLogs
 */
class UsersSecurity extends FwSecurityLogs {

	/**
	 * @var array
	 */
	static protected $cached_users = array();

	/**
	 * Find recent logs by this user
	 *
	 * @param IUser $user
	 * @param IUser $by
	 * @return array
	 */
	static function findRecentBy(IUser $user, IUser $by) {
		$result = array();
		$map_result = array();

		// Find all login, logout log
		$logs = DB::execute("SELECT * FROM " . TABLE_PREFIX . "security_logs WHERE user_email = ? ORDER BY event_on DESC", $by->getEmail());
		if (is_foreachable($logs)) {
			foreach($logs as $log) {
				$login_ip = array_var($log, 'user_ip', lang('unknown'));

				$datetime = new DateTimeValue(strtotime($log['event_on']));
				$login_as_name = clean(array_var($log, 'login_as_name', null));
				$ip_address = $login_ip == '127.0.0.1' || $login_ip == '::1' ? lang('localhost') : $login_ip;
				$is_api = (boolean) $log['is_api'];

				$event = $log['event'];
				switch ($event) {
					case 'login':
						if ($login_as_name) {
							$log_text = lang('Login from <b>:ip_address</b> as <b>:login_as_name</b>', array(
								'ip_address' => $ip_address,
								'login_as_name' => $login_as_name
							));
						} else {
							$text = (!$is_api) ? 'Login from <b>:ip_address</b>' : 'Login from <b>:ip_address</b> through API (:user_agent)';
							$log_text = lang($text, array(
								'ip_address' => $ip_address,
								'user_agent' => array_var($log, 'user_agent', lang('Unknown'))
							));
						} // if
						break;
					case 'logout':
						$log_text = lang('Logout from <b>:ip_address</b>', array('ip_address' => $ip_address));
						break;
					case 'expired':
						$log_text = lang('Session expired from <b>:ip_address</b>', array('ip_address' => $ip_address));
						break;
					default:
						$text = (!$is_api) ? 'Failed to login from <b>:ip_address</b>' : 'Failed to login from <b>:ip_address</b> through API (:user_agent)';
						$log_text = lang($text, array(
							'ip_address' => $ip_address,
							'user_agent' => array_var($log, 'user_agent', lang('Unknown'))
						));
						break;
				} // switch
				$map_result[$datetime->getTimestamp()][] = array(
					'date'    => $datetime->format('Y-m-d'),
					'result'  => array(
						'for'     => '',
						'text'    => $log_text,
						'time'    => $datetime->formatTimeForUser()
					)
				);
			} // foreach
		} // if

		// Find all Modification Logs
		$logs = ModificationLogs::findBySQL("SELECT * FROM " . TABLE_PREFIX . "modification_logs WHERE created_by_id = ? AND parent_type IN (?) ORDER BY created_on DESC", $by->getId(), Users::getAvailableUserClasses());
		if (is_foreachable($logs)) {
			$logs = $logs->toArray();

			$rows = DB::execute("SELECT * FROM " . TABLE_PREFIX . "modification_log_values WHERE modification_id IN (?)", objects_array_extract($logs, 'getId'));
			if (is_foreachable($rows)) {
				$modifications = array();
				$cached_user_id_values = array();

				// Loop through rows and get modification => field => value map
				for($i = 0, $count = $rows->count(); $i < $count; $i++) {
					$field = $rows[$i]['field'];

					if (in_array($field, array('leader_id', 'user_id', 'assignee_id'))) {
						$cached_user_id_values[] = $rows[$i]['value'];
					} // if

					// Loop through older rows and get old value, if present
					$old_value = null;
					for($j = $i-1; $j >= 0; $j--) {
						if($rows[$j]['field'] == $field) {
							$old_value = $rows[$j]['value'];
							break;
						} // if
					} // for
					// And now map the data that we collected
					$modification_id = (integer) $rows[$i]['modification_id'];

					if(isset($modifications[$modification_id])) {
						$modifications[$modification_id][$field] = array($rows[$i]['value'], $old_value);
					} else {
						$modifications[$modification_id] = array(
							$field => array($rows[$i]['value'], $old_value)
						);
					} // if
				} // for
			} // if

			// Cache modification log creators and users in log values table
			$cached_user_ids = array();

			if (is_array($cached_user_id_values)) {
				$cached_user_ids = array_merge($cached_user_ids, $cached_user_id_values); // merge values from modification_log_values
			} // if

			$modification_author_ids = array_unique(objects_array_extract($logs, "getFieldValue", "parent_id"));
			if (is_array($modification_author_ids)) {
				$cached_user_ids = array_merge($cached_user_ids, $modification_author_ids); // merge values from modification_logs
			} // if

			if (is_foreachable($cached_user_ids)) {
				$cached_user_ids = array_unique($cached_user_ids);
				self::$cached_users = Users::getIdNameMap($cached_user_ids, true);
			} // if

			foreach($logs as $log) {
				$log_texts = isset($modifications[$log->getId()]) ?  self::renderModifications($modifications[$log->getId()]) : array();
				$map_result[$log->getCreatedOn()->getTimestamp()][] = array(
					'date'    => $log->getCreatedOn()->format('Y-m-d'),
					'result'  => array(
						'for'     => self::getCachedUserDetails($log->getParentId()),
						'text'    => $log_texts,
						'time'    => $log->getCreatedOn()->formatTimeForUser()
					)
 				);
			} // foreach
		} // if

		krsort($map_result);
		foreach($map_result as $row) {
			if (is_foreachable($row)) {
				foreach($row as $value) {
					$result[$value['date']][] = $value['result'];
				} // foreach
			} // if
		} // foreach

		return $result;
	} // findRecentBy

	static function findRecentOn(IUser $user) {
		$result = array();
		$map_result = array();

		// Find all login, logout log
		$logs = DB::execute("SELECT * FROM " . TABLE_PREFIX . "security_logs WHERE login_as_id = ? ORDER BY event_on DESC", $user->getId());
		if (is_foreachable($logs)) {
			foreach($logs as $log) {
				$login_ip = array_var($log, 'user_ip', lang('unknown'));

				$datetime = new DateTimeValue(strtotime(array_var($log, 'event_on')));
				$user_name = array_var($log, 'user_name', null);
				$ip_address = $login_ip == '127.0.0.1' || $login_ip == '::1' ? lang('localhost') : $login_ip;

				$event = $log['event'];
				if ($event == 'login' && $user_name) {
					$map_result[$datetime->getTimestamp()][] = array(
						'date' => $datetime->format('Y-m-d'),
						'result' => array(
							'for'           => '',
							'text'          => lang('<b>:user_name</b> logged as this account from <b>:ip_address</b>', array(
								'ip_address'    => $ip_address,
								'user_name'     => $user_name
							)),
							'time'          => $datetime->formatTimeForUser()
						)
					);
				} // if
			} // foreach
		} // if

		// Find all Modification Logs
		$logs = ModificationLogs::findBySQL("SELECT * FROM " . TABLE_PREFIX . "modification_logs WHERE parent_id = ? AND parent_type IN (?) ORDER BY created_on DESC", $user->getId(), Users::getAvailableUserClasses());
		if (is_foreachable($logs)) {
			$logs = $logs->toArray();

			$rows = DB::execute("SELECT * FROM " . TABLE_PREFIX . "modification_log_values WHERE modification_id IN (?)", objects_array_extract($logs, 'getId'));
			if (is_foreachable($rows)) {
				$modifications = array();
				$cached_user_id_values = array();

				// Loop through rows and get modification => field => value map
				for($i = 0, $count = $rows->count(); $i < $count; $i++) {
					$field = $rows[$i]['field'];

					if (in_array($field, array('leader_id', 'user_id', 'assignee_id'))) {
						$cached_user_id_values[] = $rows[$i]['value'];
					} // if

					// Loop through older rows and get old value, if present
					$old_value = null;
					for($j = $i-1; $j >= 0; $j--) {
						if($rows[$j]['field'] == $field) {
							$old_value = $rows[$j]['value'];
							break;
						} // if
					} // for
					// And now map the data that we collected
					$modification_id = (integer) $rows[$i]['modification_id'];

					if(isset($modifications[$modification_id])) {
						$modifications[$modification_id][$field] = array($rows[$i]['value'], $old_value);
					} else {
						$modifications[$modification_id] = array(
							$field => array($rows[$i]['value'], $old_value)
						);
					} // if
				} // for
			} // if

			// Cache modification log creators and users in log values table
			$cached_user_ids = array();

			if (is_array($cached_user_id_values)) {
				$cached_user_ids = array_merge($cached_user_ids, $cached_user_id_values); // merge values from modification_log_values
			} // if

			$modification_author_ids = array_unique(objects_array_extract($logs, "getFieldValue", "created_by_id"));
			if (is_array($modification_author_ids)) {
				$cached_user_ids = array_merge($cached_user_ids, $modification_author_ids); // merge values from modification_logs
			} // if

			if (is_foreachable($cached_user_ids)) {
				$cached_user_ids = array_unique($cached_user_ids);
				self::$cached_users = Users::getIdNameMap($cached_user_ids, true);
			} // if

			foreach($logs as $log) {
				$log_texts = isset($modifications[$log->getId()]) ?  self::renderModifications($modifications[$log->getId()]) : array();
				$map_result[$log->getCreatedOn()->getTimestamp()][] = array(
					'date' => $log->getCreatedOn()->format('Y-m-d'),
					'result' => array(
						'for'  => self::getCachedUserDetails($log->getCreatedById()),
						'text' => $log_texts,
						'time' => $log->getCreatedOn()->formatTimeForUser()
					)
				);
			} // foreach
		} // if

		krsort($map_result);
		foreach($map_result as $row) {
			if (is_foreachable($row)) {
				foreach($row as $value) {
					$result[$value['date']][] = $value['result'];
				} // foreach
			} // if
		} // foreach

		return $result;
	} // findRecentOn

	/**
	 * Return cached user value for given user ID
	 *
	 * @param integer $user_id
	 * @return string
	 */
	private static function getCachedUserDetails($user_id) {
    return isset(self::$cached_users[$user_id]) && !empty(self::$cached_users[$user_id]) ? clean(self::$cached_users[$user_id]) : lang('Unknown User');
	} // getCachedUserDetails

	/**
	 * Render modification log
	 *
	 * @param $modifications
	 * @param null $parent
	 * @return array
	 */
	static function renderModifications($modifications, $parent=null) {
		$result = array();

		$field_renders = self::getFieldRenderers();

		foreach($modifications as $field => $v) {
			list($new_value, $old_value) = $v;

			if(isset($field_renders[$field]) && $field_renders[$field] instanceof Closure) {
				$result[] = $field_renders[$field]->__invoke($old_value, $new_value);
			} else {
				if($new_value) {
					if($old_value) {
						$result[] = lang(':field changed from :old_value to :new_value', array('field' => $field, 'old_value' => $old_value, 'new_value' => $new_value));
					} else {
						$result[] = lang(':field set to :new_value', array('field' => $field, 'new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						$result[] = lang(':field set to empty value', array('field' => $field));
					} // if
				} // if
			} // if
		} // if

		return $result;
	} // renderModifications

	protected static function getFieldRenderers() {
		$result = array(
			'company_id' => function($old_value, $new_value) {
				$new_company = Companies::findById($new_value);
				$old_company = Companies::findById($old_value);
				if ($new_company instanceof Company) {
					if ($old_company instanceof Company) {
						return lang('Company changed from <b>:old_value</b> to <b>:new_value</b>', array(
							'old_value' => $old_company->getName(),
							'new_value' => $new_company->getName(),
						));
					} else {
						return lang('Company changed to <b>:new_value</b>', array(
							'new_value' => $new_company->getName()
						));
					} // if
				} else {
					if($old_company instanceof Company || is_null($new_company)) {
						return lang('Company set to empty value');
					} // if
				} // if
			},
			'first_name' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('First Name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('First Name set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('First Name set to empty value');
					} // if
				} // if
			},
			'last_name' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Last Name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Last Name set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Last Name set to empty value');
					} // if
				} // if
			},
			'title' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Title changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Title set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Title set to empty value');
					} // if
				} // if
			},
			'phone_work' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Office Phone Number changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Office Phone Number set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Office Phone Number set to empty value');
					} // if
				} // if
			},
			'phone_mobile' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Mobile Phone Number changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Mobile Phone Number set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Mobile Phone Number set to empty value');
					} // if
				} // if
			},
			'im_type' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('IM changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('IM set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('IM set to empty value');
					} // if
				} // if
			},
			'im_value' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('IM name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('IM name set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('IM name set to empty value');
					} // if
				} // if
			},
			'language' => function($old_value, $new_value) {
				$languages = Languages::getIdNameMap();
				if (!is_null($old_value)) {
					$old_value = $languages[$old_value];
					$new_value = $languages[$new_value];
					return lang('Language changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
				} else {
					$new_value = $languages[$new_value];
					return lang('Language changed to <b>:new_value</b>', array('new_value' => $new_value));
				} // if
			},
			'format_date' => function($old_value, $new_value) {
				$reference = DateTimeValue::makeFromString(date('Y') . '-08-21 20:45:15')->getTimestamp();
				if (!is_null($old_value)) {
					$old_value = strftime($old_value, $reference);
					$new_value = strftime($new_value, $reference);
					return lang('Date format changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
				} else {
					$new_value = strftime($new_value, $reference);
					return lang('Date format changed to <b>:new_value</b>', array('new_value' => $new_value));
				} // if
			},
			'format_time' => function($old_value, $new_value) {
				$reference = DateTimeValue::makeFromString(date('Y') . '-08-21 20:45:15')->getTimestamp();
				if (!is_null($old_value)) {
					$old_value = strftime($old_value, $reference);
					$new_value = strftime($new_value, $reference);
					return lang('Time format changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
				} else {
					$new_value = strftime($new_value, $reference);
					return lang('Time format changed to <b>:new_value</b>', array('new_value' => $new_value));
				} // if
			},
			'time_dst' => function($old_value, $new_value) {
				return $new_value ? lang('Daylight saving time enabled') : lang('Daylight saving time disabled');
			},
			'time_first_week_day' => function($old_value, $new_value) {
				$possibilities = Globalization::getDayNames();
				if(!is_null($old_value)) {
					$old_value = $possibilities[$old_value];
					$new_value = $possibilities[$new_value];
					return lang('First Day of the Week changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
				} else {
					$new_value = $possibilities[$new_value];
					return lang('First Day of the Week changed to <b>:new_value</b>', array('new_value' => $new_value));
				} // if
			},
			'time_timezone' => function($old_value, $new_value) {
				$possibilities = array();
				foreach(Globalization::getTimezones() as $offset => $timezone) {
					$possibilities[$offset] = Globalization::getFormattedTimezone($offset, implode(', ', $timezone));
				} // foreach
				if (!is_null($old_value)) {
					$old_value = $possibilities[$old_value];
					$new_value = $possibilities[$new_value];
					return lang('Timezone changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
				} else {
					$new_value = $possibilities[$new_value];
					return lang('Timezone changed to <b>:new_value</b>', array('new_value' => $new_value));
				} // if
			},
			'password' => function($old_value, $new_value) {
				return lang('Password changed');
			},
			'expired_password' => function($old_value, $new_value) {
				return lang('Password changed due to expiration date');
			},
			'type' => function($old_value, $new_value) {
				if($new_value) {
					if($old_value) {
						return lang('Role changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $new_value));
					} else {
						return lang('Role set to <b>:new_value</b>', array('new_value' => $new_value));
					} // if
				} else {
					if($old_value) {
						return lang('Role set to empty value');
					} // if
				} // if
			},
			'custom_permissions' => function($old_value, $new_value) {
				$new_value = unserialize($new_value);
				$old_value = unserialize($old_value);
				foreach(Users::getAvailableUserInstances() as $available_user_instance) {
					$custom_permissions = $available_user_instance->getAvailableCustomPermissions();
					if($custom_permissions->count()) {
						foreach($custom_permissions as $permission_name => $permission_details) {
							if (is_foreachable($new_value)) {
								foreach ($new_value as &$value) {
									if ($value == $permission_name) {
										$value = $permission_details['name'];
									} // if
								} // foreach
							} // if
							if (is_foreachable($old_value)) {
								foreach ($old_value as &$value) {
									if ($value == $permission_name) {
										$value = $permission_details['name'];
									} // if
								} // foreach
							} // if
						} // foreach
					} // if
				} // foreach
				if ($new_value) {
					if ($old_value) {
						return lang('Extra permissions changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => implode(', ', $old_value), 'new_value' => implode(', ', $new_value)));
					} else {
						return lang('Extra permissions set to <b>:new_value</b>', array('new_value' => implode(', ', $new_value)));
					} // if
				} else {
					if($old_value) {
						return lang('Extra permissions set to none');
					} // if
				} // if
			}
		);

		return $result;
	}

	/**
	 * Render field
	 *
	 * @param $field
	 * @param $value
	 * @param $old_value
	 * @param null $parent
	 * @return string
	 */
	private static function renderField($field, $value, $old_value, $parent = null) {
		switch($field) {

			// First Name
			case 'first_name':
				if($value) {
					if($old_value) {
						return lang('First Name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $value));
					} else {
						return lang('First Name set to <b>:new_value</b>', array('new_value' => $value));
					} // if
				} else {
					if($old_value) {
						return lang('First Name set to empty value');
					} // if
				} // if

			// Last Name
			case 'last_name':
				if($value) {
					if($old_value) {
						return lang('Last Name changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $value));
					} else {
						return lang('Last Name set to <b>:new_value</b>', array('new_value' => $value));
					} // if
				} else {
					if($old_value) {
						return lang('Last Name set to empty value');
					} // if
				} // if

			// set companies
			case 'company_id':
				$new_company = Companies::findById($value);
				$old_company = Companies::findById($old_value);
				if ($new_company instanceof Company) {
					if ($old_company instanceof Company) {
						return lang('Company changed from <b>:old_value</b> to <b>:new_value</b>', array(
							'old_value' => $old_company->getName(),
							'new_value' => $new_company->getName(),
						));
					} else {
						return lang('Company changed to <b>:new_value</b>', array(
							'new_value' => $new_company->getName()
						));
					} // if
				} else {
					if($old_company instanceof Company || is_null($new_company)) {
						return lang('Company set to empty value');
					} // if
				} // if

			// role
			case 'role_id':
				$new_role = Roles::findById($value);
				$old_role = Roles::findById($old_value);

				if ($new_role instanceof Role) {
					if ($old_role instanceof Role) {
						return lang('Role changed from <b>:old_value</b> to <b>:new_value</b>', array(
							'old_value' => $old_role->getName(),
							'new_value' => $new_role->getName()
						));
					} else {
						return lang('Role set to <b>:new_value</b>', array(
							'new_value' => $new_role->getName()
						));
					} // if
				} else {
					if($old_role instanceof Role || is_null($new_role)) {
						return lang('Role set to empty value');
					} // if
				} // if

			// State
			case 'state':
				if($value == STATE_TRASHED) {
					return lang('Moved to trash');
				} elseif($value == STATE_ARCHIVED) {
					if($old_value == STATE_VISIBLE) {
						return lang('Moved to archive');
					} elseif($old_value == STATE_TRASHED) {
						return lang('Restored from trash');
					} // if
				} elseif($value == STATE_VISIBLE) {
					if($old_value == STATE_ARCHIVED) {
						return lang('Restored from archive');
					} elseif($old_value == STATE_TRASHED) {
						return lang('Restored from trash');
					} else {
						return lang('User created');
					} // if
				} // if

			case 'number':
				if ($parent instanceof Invoice) {
					if($value) {
						if($old_value) {
							return lang('Invoice Number changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $old_value, 'new_value' => $value));
						} else {
							return lang('Invoice Number set to <b>:new_value</b>', array('new_value' => $value));
						} // if
					} else {
						if($old_value) {
							return lang('Invoice Number set to empty value');
						} // if
					} // if
				} // if
				break;

			// Set due date
			case 'due_on':
				$new_due_on = $value ? new DateValue($value) : null;
				$old_due_on = $old_value ? new DateValue($old_value) : null;

				if($new_due_on instanceof DateValue) {
					AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

					if($old_due_on instanceof DateValue) {
						return lang('Due date changed from <b>:old_value</b> to <b>:new_value</b>', array(
							'old_value' => smarty_modifier_date($old_due_on, 0),
							'new_value' => smarty_modifier_date($new_due_on, 0),
						));
					} else {
						return lang('Due date changed to <b>:new_value</b>', array(
							'new_value' => smarty_modifier_date($new_due_on, 0),
						));
					} // if
				} else {
					if($old_due_on instanceof DateValue || is_null($new_due_on)) {
						return lang('Due date set to empty value');
					} // if
				} // if

				break;

			// Label ID
			case 'label_id':
				if($value) {
					if($old_value) {
						return lang('Label changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => Labels::getLabelName($old_value, lang('Unknown Label')), 'new_value' => Labels::getLabelName($value, lang('Unknown Label'))));
					} else {
						return lang('Label set to <b>:new_value</b>', array('new_value' => Labels::getLabelName($value, lang('Unknown Label'))));
					} // if
				} else {
					if($old_value) {
						return lang('Label <b>:old_value</b> removed', array('old_value' => Labels::getLabelName($old_value, lang('Unknown Label'))));
					} // if
				} // if

				break;

			// Visibility
			case 'visibility':
				$old_value = is_null($old_value) ? $parent->getOriginalVisibility() : $old_value;
				$verbose_visibility = array(
					VISIBILITY_NORMAL => lang('Normal'),
					VISIBILITY_PRIVATE => lang('Private'),
					VISIBILITY_PUBLIC => lang('Public')
				);

				return lang('Visibility changed from <b>:old_value</b> to <b>:new_value</b>', array('old_value' => $verbose_visibility[$old_value], 'new_value' => $verbose_visibility[$value]));

			// Is locked
			case 'is_locked':
				if($value) {
					return lang('Comments locked');
				} else {
					return lang('Comments unlocked');
				} // if

			// Default behavior
			default:
				if($value) {
					if($old_value) {
						return lang(':field changed from :old_value to :new_value', array('field' => $field, 'old_value' => $old_value, 'new_value' => $value));
					} else {
						return lang(':field set to :new_value', array('field' => $field, 'new_value' => $value));
					} // if
				} else {
					if($old_value) {
						return lang(':field set to empty value', array('field' => $field));
					} // if
				} // if
		} // switch
	} // renderField

	/**
	 * Find all failed logins
	 *
	 * @return array
	 */
	static function findFailedLogins() {
		$result = array();

		$failed = DB::execute("SELECT * FROM " . TABLE_PREFIX . "security_logs WHERE event = ? ORDER BY event_on DESC", array('failed'));

		if (is_foreachable($failed)) {
			$failed->setCasting(array(
				'event_on' => DBResult::CAST_DATETIME,
			));

			foreach($failed as $subobject) {
				$result[] = array(
					'is_api'      => (boolean) $subobject['is_api'],
					'user_email'  => $subobject['user_email'],
					'user_agent'  => array_var($subobject, 'user_agent', lang('Unknown')),
					'user_ip'     => array_var($subobject, 'user_ip', lang('unknown')),
					'event_on'    => $subobject['event_on']
				);
			} // foreach
		} // if

		return $result;
	} // findFailedLogins

	/**
	 * Find all active sessions
	 *
	 * @param User $user
	 * @return array
	 */
	static function findSessionsForList(User $user) {
		$result = array();

		$user_sessions_table = TABLE_PREFIX . 'user_sessions';
		$sessions = DB::execute("SELECT * FROM $user_sessions_table WHERE user_id = ?", $user->getId());

		AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');

		if (is_foreachable($sessions)) {
			foreach($sessions as $session) {
				$session_id = array_var($session, 'id');
				$login_ip = array_var($session, 'user_ip', lang('unknown'));
				$last_activity_on = new DateTimeValue(strtotime(array_var($session, 'last_activity_on')));
				$result[] = array(
					'id'                => $session_id,
					'ip_address'        => $login_ip == '127.0.0.1' || $login_ip == '::1' ? lang('localhost') : $login_ip,
					'time_ago'          => smarty_modifier_ago($last_activity_on, null, true),
					'is_current'        => Authentication::getProvider()->isSessionActive($session_id) ? true : false,
					'user_agent'        => array_var($session, 'user_agent')
				);
			} // foreach
		} // if

		return $result;
	} // findUserActiveSessions

}