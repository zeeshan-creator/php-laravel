function create_instances($table, $row_data)
{
	global $conn;
	$days =
		["MO" => "Monday", "TU" => "Tuesday", "WE" => "Wednesday", "TH" => "Thursday", "FR" => "Friday", "SA" => "Saturday", "SU" => "Sunday"];
	$week_no = ["1" => "first", "2" => "second", "3" => "third", "4" => "fourth", "5" => "fifth"];
	$custom_rrule = $row_data['custom_rrule'];

	$rrule = str_replace("RRULE:", "", $custom_rrule);
	$rrule = explode(';', $rrule);

	// then make an key value pairs of each of the RRULE feilds
	$reccurence = array();
	foreach ($rrule as $value) {
		$key_value = explode('=', $value);
		$reccurence[trim($key_value[0])] = trim($key_value[1]);
	}

	if ($reccurence['FREQ'] == 'DAILY') {
		$freq = 1;
	}
	if ($reccurence['FREQ'] == 'WEEKLY') {
		$freq = 7;
	}
	if ($reccurence['FREQ'] == 'MONTHLY') {
		$freq = 30;
	}
	if ($reccurence['FREQ'] == 'YEARLY') {
		$freq = 365;
	}

	$freq  = (isset($reccurence['INTERVAL'])) ? $freq * $reccurence['INTERVAL'] : $freq;

	$last_insert_id = last_insert_id();
	$cols = $values = $values100 = array();

	foreach ($row_data as $index => $value) {
		$cols[] = '`' . $index . '`';
		$values[] = "'" . $value . "'";
	}

	$cols[] = '`event_db_id`';
	$values[] = "'" . $last_insert_id . "'";

	if (!isset($reccurence['FREQ'])) {
		if (mysqli_query($conn, "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES(" . implode(',', $values) . ")")) {
			return true;
		} else {
			return false;
		}
	}

	$count = (isset($reccurence['COUNT'])) ? $reccurence['COUNT'] : 730;
	$until = (isset($reccurence['UNTIL'])) ? date("Y-m-d", strtotime($reccurence['UNTIL'])) : null;
	$byday = (isset($reccurence['BYDAY'])) ? explode(',', $reccurence['BYDAY']) : null;

	$byday_index = 0;
	$arrr = array();
	for ($i = 1; $i <= $count; $i++) {

		if ($reccurence['FREQ'] == 'DAILY') {
			$values100[] = "(" . implode(',', $values) . ")";
			$start_date_index = array_search('`start_date`', $cols);
			$start_date = str_replace("'", "", $values[$start_date_index]);
			$values[$start_date_index] = "'" . date('Y-m-d', strtotime($start_date . " + $freq days")) . "'";

			$end_date_index = array_search('`end_date`', $cols);
			$end_date = str_replace("'", "", $values[$end_date_index]);
			$values[$end_date_index] = "'" . date('Y-m-d', strtotime($end_date . " + $freq days")) . "'";
		}

		if ($reccurence['FREQ'] == 'WEEKLY') {
			if (!isset($reccurence['BYDAY'])) {
				$start_date_index = array_search('`start_date`', $cols);
				$start_date = str_replace("'", "", $values[$start_date_index]);
				$values[$start_date_index] = "'" . date('Y-m-d', strtotime($start_date . " + $freq days")) . "'";

				$end_date_index = array_search('`end_date`', $cols);
				$end_date = str_replace("'", "", $values[$end_date_index]);
				$values[$end_date_index] = "'" . date('Y-m-d', strtotime($end_date . " + $freq days")) . "'";
				$values100[] = "(" . implode(',', $values) . ")";
			} else {
				$values100[] = "(" . implode(',', $values) . ")";
				// Date
				$dayofweek = substr(date('D', strtotime(date('Y-m-d'))), 0, 2);
				if ($i == 1) {
					$byday_index = array_search(strtoupper($dayofweek), $byday) + 1;
				}

				// Start date index
				$start_date_index = array_search('`start_date`', $cols);
				$start_date = str_replace("'", "", $values[$start_date_index]);
				$date = DateTime::createFromFormat('Y-m-d',  $start_date);
				$weekday =  $days[$byday[$byday_index]];
				$date->modify("next $weekday");
				$values[$start_date_index] = "'" . $date->format('Y-m-d') . "'";

				// End date index
				$end_date_index = array_search('`end_date`', $cols);
				$values[$end_date_index] = "'" . $date->format('Y-m-d') . "'";

				$arrr[] = $weekday;
				if ($byday_index == count($byday) - 1) {
					$byday_index = 0;
				} else {
					$byday_index += 1;
				}
			}
		}

		if ($reccurence['FREQ'] == 'MONTHLY') {
			if (!isset($reccurence['BYDAY'])) {
				$values100[] = "(" . implode(',', $values) . ")";
				$start_date_index = array_search('`start_date`', $cols);
				$start_date = str_replace("'", "", $values[$start_date_index]);
				$values[$start_date_index] = "'" . date('Y-m-d', strtotime($start_date . " + $freq days")) . "'";

				$end_date_index = array_search('`end_date`', $cols);
				$end_date = str_replace("'", "", $values[$end_date_index]);
				$values[$end_date_index] = "'" . date('Y-m-d', strtotime($end_date . " + $freq days")) . "'";
			} else {
				$values100[] = "(" . implode(',', $values) . ")";
				$no_of_week = $reccurence['BYDAY'][0];
				$name_of_day = substr($reccurence['BYDAY'], 1, 2);

				// Start date index
				$start_date_index = array_search('`start_date`', $cols);
				$start_date = str_replace("'", "", $values[$start_date_index]);
				$date = DateTime::createFromFormat('Y-m-d',  $start_date);
				$date->modify("$week_no[$no_of_week]  $days[$name_of_day] of next month");
				$values[$start_date_index] = "'" . $date->format('Y-m-d') . "'";

				// End date index
				$end_date_index = array_search('`end_date`', $cols);
				$values[$end_date_index] = "'" . $date->format('Y-m-d') . "'";
			}
		}

		if ($reccurence['FREQ'] == 'YEARLY') {
			if (!isset($reccurence['BYDAY'])) {
				$values100[] = "(" . implode(',', $values) . ")";
				$start_date_index = array_search('`start_date`', $cols);
				$start_date = str_replace("'", "", $values[$start_date_index]);
				$values[$start_date_index] = "'" . date('Y-m-d', strtotime($start_date . " + $freq days")) . "'";

				$end_date_index = array_search('`end_date`', $cols);
				$end_date = str_replace("'", "", $values[$end_date_index]);
				$values[$end_date_index] = "'" . date('Y-m-d', strtotime($end_date . " + $freq days")) . "'";
			}
		}

		if (isset($until)) {
			if (strtotime($until) == strtotime($start_date)) {
				break;
			}
		}
	}
	$query = "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES " . implode(',', $values100);

	if (mysqli_query($conn, $query)) {
		return true;
	} else {
		return false;
	}
}
