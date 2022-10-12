<?php
require_once('./core/login_checked.php');
if (isset($_POST)) {
	if (isset($_POST['create_dd'])) {
		$sr_row = $_POST['row'];
		$indexOfForm = $_POST['indexOfForm'];
		$temp = '';

		$temp .= '<select id="selectone-" class="form-control selectpicker first-part   fl' . $sr_row . '  mt-1 first-part" name="first-filter-opportunities[]" data-live-search="true" data-form-index="' . $indexOfForm . '" data-first-part="' . $indexOfForm . '"  >
		<option value="">&nbsp;</option>';
		$result_columns = mysqli_query($conn, "SHOW COLUMNS FROM `opportunities`");
		while ($row = $result_columns->fetch_assoc()) {
			if (!in_array($row['Field'], $GLOBALS['excluded_fields_opportunities'])) {

				$temp .= '<option value="' . $row['Field'] . '" data-tokens="' . $row['Field'] . '">' . ucfirst(str_replace("_", " ", $row['Field'])) . '</option>';
			}
		}
		$temp .= '</select>		';

		$temp .= '<select id="selecttwo-" class="form-control selectpicker second-part  fl' . $sr_row . ' mt-1" name="second-filter-opportunities[]" data-live-search="true" data-form-index="' . $indexOfForm . '" data-second-part="' . $indexOfForm . '">
		<option value="">&nbsp;</option>
		<option class="inBetweenOption" data-inbetween-option="' . $indexOfForm . '" value="in_between">In between</option>
		<option value="equals">' . check_isset($language, 'Equals') . '</option>
		<option value="not_equals">' . check_isset($language, 'Not equals') . '</option>
		<option value="contains">' . check_isset($language, 'Contains') . '</option>
		<option value="does_not_contain">' . check_isset($language, 'Does not contain') . '</option>
		<option value="starts_with">' . check_isset($language, 'Starts with') . '</option>
		<option value="ends_with">' . check_isset($language, 'Ends with') . '</option>
		<option value="does_not_start_with">' . check_isset($language, 'Does not start with') . '</option>
		<option value="does_not_end_with">' . check_isset($language, 'Does not ends with') . '</option>
		<option value="is_empty">' . check_isset($language, 'Is empty') . '</option>
		<option value="is_not_empty">' . check_isset($language, 'Is not empty') . '</option>  
		</select>';


		$temp .= '<input type="text" id="text-' . $sr_row . '" name="filter-text-opportunities[]" class="form-control third-part fl' . $sr_row . '  mt-1" placeholder="' . check_isset($language, 'Text for search...') . '" data-form-index="' . $indexOfForm . '" autocomplete="off"  >';

		$temp .= '
		<div class="input-group fl' . $sr_row . '  mt-1">
		<select name="operation-opportunities[]" class="form-control selectpicker" data-live-search="true" >
		<option value="AND">' . check_isset($language, 'AND') . '</option>
		<option value="OR">OR</option>
		</select>
		<span class="input-group-append"><button class="btn btn-info flb" type="button" data-row="' . $sr_row . '"> <i class="fa fa-trash"></i> </button></span>
		</div>
		';
		$temp .= '';
		echo $temp;
	}
}
