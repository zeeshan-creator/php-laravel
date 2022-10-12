<?php

$allDateColoumns = array("forecasted_close_date", "unqualified_date", "updated_date", "created_date");

if (isset($_POST['filter-records'])) {
    $_SESSION['first-filter-opportunities'] = $_POST['first-filter-opportunities'][0];
    $_SESSION['second-filter-opportunities'] = $_POST['second-filter-opportunities'][0];
    $_SESSION['filter-text-opportunities'] = $_POST['filter-text-opportunities'][0];
    $query = "";
    $i = 0;
    foreach ($_POST['filter-text-opportunities'] as $key => $filter_text) {
        $i++;
        $_SESSION['save-filter-text-opportunities'][$key] = $_POST['filter-text-opportunities'][$key];
        $_SESSION['save-first-filter-opportunities'][$key] = $_POST['first-filter-opportunities'][$key];
        $_SESSION['save-second-filter-opportunities'][$key] = $_POST['second-filter-opportunities'][$key];
        $_SESSION['save-operation-text-opportunities'][$key] = $_POST['operation-opportunities'][$key];


        //Todo: ===== All the fields, linked with another table =====

        //! ====  Assigned To ====
        if ($_POST['first-filter-opportunities'][$key] == 'assigned_to') {
            $_SESSION['assigned_to_opportunities'][$key] = "assigned_to=(SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text')";
        }

        //! ====  Opportunity Follower ====
        if ($_POST['first-filter-opportunities'][$key] == 'opportunity_follower') {
            $_SESSION['opportunity_follower_opportunities'][$key] = "opportunity_follower=(SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$mfilter_text'OR concat(f_name,' ',l_name) = '$filter_text' )";
        }

        //! ====  Opportunity Contact ====
        if ($_POST['first-filter-opportunities'][$key] == 'opportunity_contact') {
            $_SESSION['opportunity_contact_opportunities'][$key] = "opportunity_contact=(SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text' )";
        }

        //! ====  Opportunity Onwner ====
        if ($_POST['first-filter-opportunities'][$key] == 'opportunity_owner') {
            $_SESSION['opportunity_owner_opportunities'][$key] = "opportunity_owner=(SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text' )";
        }

        //! ====  unqualified Owner ====
        if ($_POST['first-filter-opportunities'][$key] == 'unqualified_owner') {
            $_SESSION['unqualified_owner_opportunities'][$key] = "unqualified_owner=(SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text' )";
        }

        //! ====  Opportunity Stage ====
        if ($_POST['first-filter-opportunities'][$key] == 'opportunity_stage') {
            $_SESSION['opportunity_stage_opportunities'][$key] = "opportunity_stage=(SELECT id FROM pipeline_stages WHERE name='$filter_text')";
        }


        //Todo: ===== All the conditioning operations =====

        //! ====  Equals ====
        if ($_POST['second-filter-opportunities'][$key] == "equals") {
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace("unqualified_owner=(", "(unqualified_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace("opportunity_owner=(", "(opportunity_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace("opportunity_follower=(", "(opportunity_follower LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace("opportunity_contact=(", "(opportunity_contact LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));

                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " LIKE '%$filter_text%'";
            } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
            } else {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "='$filter_text'";
            }

            $query = str_replace(")='$filter_text'", ")", $query);
        }

        //! ====  Not Equals ====
        if ($_POST['second-filter-opportunities'][$key] == "not_equals") {

            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("assigned_to=(", "assigned_to!=(", $stage);
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("opportunity_stage=(", "opportunity_stage!=(", $stage);

                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace("unqualified_owner=(", "(unqualified_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace("opportunity_owner=(", "(opportunity_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND  opportunity_follower != (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace("opportunity_contact=(", "(opportunity_contact NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= "OR opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text' OR concat(f_name,' ',l_name) = '$filter_text'))";
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));

                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " NOT LIKE '%$filter_text%'";
            } else {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "!='$filter_text'";
            }
            $query = str_replace(")!='$filter_text'", ")", $query);
        }

        //! ====  Contains ====
        if ($_POST['second-filter-opportunities'][$key] == "contains") {
            $_contain_stage = false;

            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("name ='$filter_text'", "name LIKE '%$filter_text%' ", $stage);
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "(unqualified_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "(opportunity_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "(opportunity_contact LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text%'", $stage);
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {

                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                }

                if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " LIKE '%$filter_text%'";
                }
            }



            $query = str_replace("), '%' LIKE '%$filter_text%'", "), '%')", $query);
        }

        //! ====  Dose Not Contains ====
        if ($_POST['second-filter-opportunities'][$key] == "does_not_contain") {
            $_contain_stage = false;
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("assigned_to=(", "(assigned_to IN(", $stage);
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text%'", $stage);
                    $stage .= " OR  assigned_to = '') ";
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("opportunity_stage=(", "(opportunity_stage IN(", $stage);
                    $stage = str_replace("name='$filter_text'", "name NOT LIKE '%$filter_text%' ", $stage);
                    $stage .= " OR  opportunity_stage = '') ";
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "( unqualified_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND  unqualified_owner != (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage .= " OR ( unqualified_owner=' 'OR  unqualified_owner IS NULL))";
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====   Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "( opportunity_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND  opportunity_owner != (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage .= " OR ( opportunity_owner=' 'OR  opportunity_owner IS NULL))";
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "( opportunity_follower NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND  opportunity_follower != (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage .= " OR ( opportunity_follower=' 'OR  opportunity_follower IS NULL))";
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "( opportunity_contact NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " AND  opportunity_contact != (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text')";
                    $stage .= " OR ( opportunity_contact=' 'OR  opportunity_contact IS NULL))";
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);

                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " NOT LIKE '%$filter_text%'";
                }
            }


            $query = str_replace("), '%' NOT LIKE '%$filter_text%'", "), '%')", $query);
        }

        //! ====  In Between ====
        if ($_POST['second-filter-opportunities'][$key] == "in_between") {

            $stage = '' . $_POST['first-filter-opportunities'][$key] . '';

            if (in_array($stage, $allDateColoumns)) {
                $datesArray = explode(" - ", $filter_text);
                $startDate = date("Y-m-d", strtotime($datesArray[0]));
                $endDate = date("Y-m-d", strtotime($datesArray[1]));

                $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));

                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " >= '$startDate' AND " . $stage . " <= '$endDate'";
            } else {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " LIKE '%$filter_text%'";
            }

            $query = str_replace("), '%' LIKE '%$filter_text%'", "), '%')", $query);
        }

        //! ====  Starts With ====
        if ($_POST['second-filter-opportunities'][$key] == "starts_with") {
            $_contain_stage = false;
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("name ='$filter_text'", "name LIKE '$filter_text%' ", $stage);
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "(unqualified_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "(opportunity_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "(opportunity_contact LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {

                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));

                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " >= '$filter_text'";
                } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " LIKE '$filter_text%'";
                }
            }


            $query = str_replace("), '%' LIKE '$filter_text%'", "), '%')", $query);
        }

        //! ====  Ends With ====
        if ($_POST['second-filter-opportunities'][$key] == "ends_with") {
            $_contain_stage = false;
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("name ='$filter_text'", "name LIKE '%$filter_text' ", $stage);
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "(unqualified_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "(opportunity_owner LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "(opportunity_contact LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));

                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " <= '$filter_text'";
                } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " LIKE '%$filter_text'";
                }
            }



            $query = str_replace(")) LIKE '%$filter_text'", "))", $query);
        }

        //! ====  Dose Not Starts With ====
        if ($_POST['second-filter-opportunities'][$key] == "does_not_start_with") {
            $_contain_stage = false;
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("assigned_to=(", "(assigned_to IN(", $stage);
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '$filter_text%'", $stage);
                    $stage .= " OR  assigned_to = '') ";
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("opportunity_stage=(", "(opportunity_stage IN(", $stage);
                    $stage = str_replace("name='$filter_text'", "name NOT LIKE '$filter_text%' ", $stage);
                    $stage .= " OR  opportunity_stage = '') ";
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            }
            //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "(unqualified_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '$filter_text%'", $stage);
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "(opportunity_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "(opportunity_contact NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '$filter_text%' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '$filter_text%'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '$filter_text%'", $stage);
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " NOT LIKE '$filter_text%'";
                }
            }



            $query = str_replace("), '%' NOT LIKE '$filter_text%'", "), '%')", $query);
        }

        //! ====  Dose Not End With ====
        if ($_POST['second-filter-opportunities'][$key] == "does_not_end_with") {
            $_contain_stage = false;
            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {

                    $stage = $_SESSION['assigned_to_opportunities'][$key];
                    $stage = str_replace("assigned_to=(", "(assigned_to IN(", $stage);
                    $stage = str_replace(
                        "f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text' ",
                        "CONCAT(f_name,' ',l_name) NOT LIKE '%$filter_text' ",
                        $stage
                    );
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                    $stage .= " OR assigned_to = '') ";
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_stage_opportunities'][$key];
                    $stage = str_replace("opportunity_stage=(", "(opportunity_stage IN(", $stage);
                    $stage = str_replace(
                        "name='$filter_text' ",
                        "name NOT LIKE '%$filter_text' ",
                        $stage
                    );
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                    $stage .= " OR opportunity_stage = '') ";
                }
            } //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['unqualified_owner_opportunities'][$key];
                    $stage = str_replace(" unqualified_owner=(", "(unqualified_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  unqualified_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text'", $stage);
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_owner_opportunities'][$key];
                    $stage = str_replace(" opportunity_owner=(", "(opportunity_owner NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_owner = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_follower_opportunities'][$key];
                    $stage = str_replace(" opportunity_follower=(", "(opportunity_follower NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_follower = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                    $_contain_stage = true;
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {

                    $stage = $_SESSION['opportunity_contact_opportunities'][$key];
                    $stage = str_replace(" opportunity_contact=(", "(opportunity_contact NOT LIKE CONCAT('%',(", $stage);
                    $stage .= ", ',%')";
                    $stage .= " OR  opportunity_contact = (SELECT id FROM users WHERE f_name='$filter_text'OR l_name='$filter_text'OR concat(f_name,' ',l_name) = '$filter_text'))";
                    $stage = str_replace("f_name='$filter_text'", "f_name NOT LIKE '%$filter_text' ", $stage);
                    $stage = str_replace("l_name='$filter_text'", "l_name NOT LIKE '%$filter_text'", $stage);
                    $stage = str_replace("concat(f_name,' ',l_name) = '$filter_text'", "concat(f_name,' ',l_name) NOT LIKE '%$filter_text'", $stage);
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                    $_contain_stage = true;
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if ($_contain_stage) {
                $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "";
            } else {
                if (in_array($stage, $allDateColoumns)) {
                    $filter_text = date("Y-m-d", strtotime($filter_text));
                } elseif (isset($_SESSION['assigned_to_opportunities'][$key])) {
                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage;
                } else {

                    $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . " NOT LIKE '%$filter_text'";
                }
            }



            $query = str_replace(")) NOT LIKE '%$filter_text'", "))", $query);
        }

        //! ====  Is Empty ====
        if ($_POST['second-filter-opportunities'][$key] == "is_empty") {

            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {
                    $stage = "(assigned_to = '' OR assigned_to IS NULL)";
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {
                    $stage = "(opportunity_stage=''OR opportunity_stage IS NULL)";
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            } //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {
                    $stage = "(unqualified_owner=''OR unqualified_owner IS NULL)";
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {
                    $stage = "(opportunity_owner=''OR opportunity_owner IS NULL)";
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {
                    $stage = "(opportunity_follower=''OR opportunity_follower IS NULL)";
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {
                    $stage = "(opportunity_contact=''OR opportunity_contact IS NULL)";
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }

            if (in_array($stage, $allDateColoumns)) {
                $filter_text = date("Y-m-d", strtotime($filter_text));
            }
            $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "=''";

            $query = str_replace(")=''", ")", $query);
        }

        //! ====  Is Not Empty ====
        if ($_POST['second-filter-opportunities'][$key] == "is_not_empty") {

            //! ====  Assigned To ====
            if (isset($_SESSION['assigned_to_opportunities'][$key])) {
                if ($_SESSION['assigned_to_opportunities'][$key] != '') {
                    $stage = "(assigned_to != '' OR assigned_to IS NOT NULL)";
                    unset($_SESSION['assigned_to_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Stage ====
            else if (isset($_SESSION['opportunity_stage_opportunities'][$key])) {
                if ($_SESSION['opportunity_stage_opportunities'][$key] != '') {
                    $stage = "(opportunity_stage!=''OR opportunity_stage IS NOT NULL)";
                    unset($_SESSION['opportunity_stage_opportunities'][$key]);
                }
            } //! ====  unqualified Owner ====
            else if (isset($_SESSION['unqualified_owner_opportunities'][$key])) {
                if ($_SESSION['unqualified_owner_opportunities'][$key] != '') {
                    $stage = "(unqualified_owner!=''OR unqualified_owner IS NOT  NULL)";
                    unset($_SESSION['unqualified_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Onwner ====
            else if (isset($_SESSION['opportunity_owner_opportunities'][$key])) {
                if ($_SESSION['opportunity_owner_opportunities'][$key] != '') {
                    $stage = "(opportunity_owner!=''OR opportunity_owner IS NOT NULL)";
                    unset($_SESSION['opportunity_owner_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Follower ====
            else if (isset($_SESSION['opportunity_follower_opportunities'][$key])) {
                if ($_SESSION['opportunity_follower_opportunities'][$key] != '') {
                    $stage = "(opportunity_follower!=''OR opportunity_follower IS NOT NULL)";
                    unset($_SESSION['opportunity_follower_opportunities'][$key]);
                }
            }
            //! ====  Opportunity Contact ====
            else if (isset($_SESSION['opportunity_contact_opportunities'][$key])) {
                if ($_SESSION['opportunity_contact_opportunities'][$key] != '') {
                    $stage = "(opportunity_contact!=''OR opportunity_contact IS NOT NULL)";
                    unset($_SESSION['opportunity_contact_opportunities'][$key]);
                }
            } else {
                $stage = '' . $_POST['first-filter-opportunities'][$key] . '';
            }
            $query .= "  " . $_POST['operation-opportunities'][$key] . " " . $stage . "!=''";

            $query = str_replace(")!=''", ")", $query);
        }
    }
    $_SESSION['all_opportunities_query'] = $query;
}
