<?php

$allDateColoumns = array("contact_date", "birthday", "updated_date", "created_date", "validation_date");

if (isset($_POST['filter-records'])) {
    $_SESSION['first-filter-contact'] = $_POST['first-filter-contact'][0];
    $_SESSION['second-filter-contact'] = $_POST['second-filter-contact'][0];
    $_SESSION['filter-text-contact'] = $_POST['filter-text-contact'][0];
    $query = "";
    $i = 0;
    foreach ($_POST['filter-text-contact'] as $key => $filter_text) {
        $i++;
        $_SESSION['save-filter-text-contact'][$key] = $_POST['filter-text-contact'][$key];
        $_SESSION['save-first-filter-contact'][$key] = $_POST['first-filter-contact'][$key];
        $_SESSION['save-second-filter-contact'][$key] = $_POST['second-filter-contact'][$key];
        $_SESSION['save-operation-text-contact'][$key] = $_POST['operation-contact'][$key];


        if ($_POST['second-filter-contact'][$key] == "equals") {
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {

                    $stage = $_SESSION['lead_stage_contact'][$key];
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];
                    $stage = str_replace("lead_followers=(", "lead_followers LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR lead_followers = (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));

                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " LIKE '%$filter_text%'";
            } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
            } else {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "='$filter_text'";
            }

            $query = str_replace(")='$filter_text'", ")", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "not_equals") {

            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("lead_assigned_to=(", "lead_assigned_to!=(", $stage);
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {

                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = str_replace("lead_stage=(", "lead_stage!=(", $stage);
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];
                    $stage = str_replace("lead_followers=(", "lead_followers NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND lead_followers != (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }
            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));

                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " NOT LIKE '%$filter_text%'";
            } else {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "!='$filter_text'";
            }
            $query = str_replace(")!='$filter_text'", ")", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "contains") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage IN (SELECT id FROM lead_stage WHERE name LIKE '%$filter_text%')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];
                    $stage = str_replace("lead_followers=(", "lead_followers LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR lead_followers = (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);

                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {

                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                }

                if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " LIKE '%$filter_text%'";
                }
            }



            $query = str_replace("), '%' LIKE '%$filter_text%'", "), '%')", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "does_not_contain") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("lead_assigned_to=(", "(lead_assigned_to IN(", $stage);
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text%'", $stage);
                    $stage .= " OR lead_assigned_to = '') ";
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage NOT IN (SELECT id FROM lead_stage WHERE name LIKE '%$filter_text%')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];
                    $stage = str_replace("lead_followers=(", "(lead_followers NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND lead_followers != (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage .= " OR (lead_followers=' ' OR lead_followers IS NULL))";

                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " NOT LIKE '%$filter_text%'";
                }
            }


            $query = str_replace("), '%' NOT LIKE '%$filter_text%'", "), '%')", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "in_between") {

            $stage = '' . $_POST['first-filter-contact'][$key] . '';

            if (in_array($stage, $allDateColoumns)) {
                $datesArray = explode(" - ", $filter_text);
                $startDate = date("Y-m-d", strtotime($datesArray[0]));
                $endDate = date("Y-m-d", strtotime($datesArray[1]));

                $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));

                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " >= '$startDate' AND " . $stage . " <= '$endDate'";
            } else {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " LIKE '%$filter_text%'";
            }

            $query = str_replace("), '%' LIKE '%$filter_text%'", "), '%')", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "starts_with") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage IN (SELECT id FROM lead_stage WHERE name LIKE '$filter_text%')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];

                    $stage = str_replace("lead_followers=(", "lead_followers LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR lead_followers = (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {

                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));

                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " >= '$filter_text'";
                } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " LIKE '$filter_text%'";
                }
            }


            $query = str_replace("), '%' LIKE '$filter_text%'", "), '%')", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "ends_with") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage IN (SELECT id FROM lead_stage WHERE name LIKE '%$filter_text')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];

                    $stage = str_replace("lead_followers=(", "lead_followers LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR lead_followers = (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));

                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " <= '$filter_text'";
                } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " LIKE '%$filter_text'";
                }
            }



            $query = str_replace(")) LIKE '%$filter_text'", "))", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "does_not_start_with") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("lead_assigned_to=(", "(lead_assigned_to IN(", $stage);
                    $stage = str_replace(
                        "f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text' ",
                        "CONCAT(f_name,' ',l_name) NOT LIKE '$filter_text%' ",
                        $stage
                    );
                    $stage .= " OR lead_assigned_to = '') ";
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage NOT IN (SELECT id FROM lead_stage WHERE name LIKE '$filter_text%')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];

                    $stage = str_replace("lead_followers=(", "(lead_followers NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND lead_followers != (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    $stage .= " OR (lead_followers='' OR lead_followers IS NULL)) ";

                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " NOT LIKE '$filter_text%'";
                }
            }



            $query = str_replace("), '%' NOT LIKE '$filter_text%'", "), '%')", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "does_not_end_with") {
            $_contain_stage = false;
            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {

                    $stage = $_SESSION['lead_assigned_to_contact'][$key];
                    $stage = str_replace("lead_assigned_to=(", "(lead_assigned_to IN(", $stage);
                    $stage = str_replace(
                        "f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text' ",
                        "CONCAT(f_name,' ',l_name) NOT LIKE '%$filter_text' ",
                        $stage
                    );
                    $stage .= " OR lead_assigned_to = '') ";
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {
                    $_contain_stage = true;
                    $stage = $_SESSION['lead_stage_contact'][$key];
                    $stage = "lead_stage NOT IN (SELECT id FROM lead_stage WHERE name LIKE '%$filter_text')";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    $stage = $_SESSION['lead_followers_contact'][$key];
                    $stage = str_replace("lead_followers=(", "(lead_followers NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND lead_followers != (SELECT id FROM users WHERE f_name='$filter_text' OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    $stage .= " OR (lead_followers='' OR lead_followers IS NULL)) ";

                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage;
                } else {

                    $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . " NOT LIKE '%$filter_text'";
                }
            }



            $query = str_replace(")) NOT LIKE '%$filter_text'", "))", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "is_empty") {

            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {


                    unset($_SESSION['lead_assigned_to_contact'][$key]);
                    $stage = "(lead_assigned_to='' OR lead_assigned_to IS NULL)";
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {


                    unset($_SESSION['lead_stage_contact'][$key]);
                    $stage = "(lead_stage='' OR lead_stage IS NULL)";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    unset($_SESSION['lead_followers_contact'][$key]);
                    $stage = "(lead_followers='' OR lead_followers IS NULL)";
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }

            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));
            }
            $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "=''";

            $query = str_replace(")=''", ")", $query);
        }
        if ($_POST['second-filter-contact'][$key] == "is_not_empty") {

            if (isset($_SESSION['lead_assigned_to_contact'][$key])) {
                if ($_SESSION['lead_assigned_to_contact'][$key] != '') {


                    unset($_SESSION['lead_assigned_to_contact'][$key]);
                    $stage = "(lead_assigned_to!='' )";
                }
            } else if (isset($_SESSION['lead_stage_contact'][$key])) {
                if ($_SESSION['lead_stage_contact'][$key] != '') {


                    unset($_SESSION['lead_stage_contact'][$key]);
                    $stage = "(lead_stage!='' OR lead_stage IS NOT NULL)";
                }
            } else if (isset($_SESSION['lead_followers_contact'][$key])) {
                if ($_SESSION['lead_followers_contact'][$key] != '') {

                    unset($_SESSION['lead_followers_contact'][$key]);
                    $stage = "(lead_followers!='' OR lead_followers IS NOT NULL)";
                }
            } else {
                $stage = '' . $_POST['first-filter-contact'][$key] . '';
            }
            $query .= "  " . $_POST['operation-contact'][$key] . " " . $stage . "!=''";

            $query = str_replace(")!=''", ")", $query);
        }
    }
    $_SESSION['all_contact_query'] = $query . "AND";
}
