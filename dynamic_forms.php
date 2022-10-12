<?php require_once "./core/login_checked.php";
if (!isset($auth_permissions["opportunities"])) {
    if ($auth_role_type != "super_admin") {
        header("location:index.php");
        exit();
    }
} elseif (isset($auth_permissions["opportunities"]) && $auth_permissions["opportunities"]["view"] == 0) {
    header("location:index.php");
    exit();
}

$db_fields = array();
if (isset($auth_permissions['opportunities']) && !empty($auth_permissions['opportunities']['fields'])) {
    $db_fields = $auth_permissions['opportunities']['fields'];
}

if (isset($_GET["opportunity"])) {
    $id = $_GET["opportunity"];
} else {
    header("location:index.php");
    exit();
}

include "include/lead/search/opportunities/post_filter_records.php";
include "include/lead/search/opportunities/post_save_filter_records.php";
include "include/lead/search/opportunities/post_use_old_search.php";
include "include/lead/search/opportunities/post_delete_old_search.php";
include "include/lead/search/opportunities/post_clear_saved_search_records.php";
include "include/lead/search/opportunities/post_clear_search_records.php";
include "include/properties.php";


?>

<body class="font-montserrat all_lead">
    <div class="page-loader-wrapper">
        <div class="loader">
        </div>
    </div>
    <div id="main_content">

        <?php include "settings_html.php"; ?>

        <?php include "include/template-parts/left_sidebar_info_box.php"; ?>

        <?php include "include/template-parts/main_menu_html.php"; ?>

        <div class="page">
            <?php include "include/header.php"; ?>
            <div class="section-body mt-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card leadCard">
                                <div class="card-headers p-3">
                                    <div class="d-md-flex justify-content-between align-item-center mb-4">
                                        <ul class="nav nav-tabs b-none">
                                            <li class="nav-item"><a class="nav-link active" id="grid-tab" data-toggle="tab" href="#grid"><i class="fa fa-th"></i> <?php echo check_isset($language, 'Grid'); ?></a></li>
                                            <li class="nav-item"><a class="nav-link" id="list-tab" data-toggle="tab" href="#list"><i class="fa fa-list-ul"></i> <?php echo check_isset($language, 'List'); ?></a></li>
                                            <li class="nav-item"><a class="nav-link" id="kanban-tab" data-toggle="tab" href="#kanban"><i class="fa fa-columns"></i> <?php echo check_isset($language, 'Kanban'); ?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="createopportunity.php"><i class="fa fa-plus"></i> <?php echo check_isset($language, 'Add New'); ?></a></li>
                                        </ul>
                                        <div class="row">
                                            <?php

                                            $queryCount = mysqli_query($conn, "SELECT `user_id`,`search_id`,`search_text`,`search_query` FROM `recent_searches` WHERE `user_id`= $auth_id AND `page_type` ='opportunities' OR (`search_type` = 'specific' AND CONCAT(',', search_type_value, ',') LIKE '%,$auth_id,%')  OR (`search_type` = 'global'  AND `page_type` ='opportunities')  OR (`search_type` = 'role' AND `search_type_value` = $auth_user_role_id)");
                                            if (mysqli_num_rows($queryCount) > 0) {
                                            ?>

                                                <form id="newform" method="POST">

                                                    <div class="">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <select class="form-control selectpicker" name="user-old-search-opportunities" data-live-search="true" onchange="newform.submit();">
                                                                    <option value="" disabled="" selected><?php echo check_isset($language, '--Select--'); ?></option>
                                                                    <?php
                                                                    while ($row = $queryCount->fetch_assoc()) {
                                                                        $m = str_replace('"', "'", $row['search_query']);
                                                                        $m = $row['search_id'] . "||" . $m;
                                                                        $n = str_replace('"', "'", $row['search_query']);
                                                                    ?>
                                                                        <option <?php if (isset($_SESSION['old-search-opportunities']) && $_SESSION['old-search-opportunities'] == $n) {
                                                                                    echo "selected";
                                                                                } ?> value="<?php echo $m ?>" data-tokens="<?php echo $m ?>"><?php echo ucfirst($row['search_text']) . ($row['user_id'] != $auth_id ? ' (Shared Search)' : ''); ?></option>
                                                                    <?php
                                                                    }

                                                                    ?>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>

                                            <?php }
                                            if (mysqli_num_rows($queryCount) > 0 && isset($_SESSION['old-search-opportunities']) && $_SESSION['old-search-opportunities'] != '') {
                                            ?>
                                                <form method="POST">
                                                    <div class="">
                                                        <div class="row">
                                                            <div class="col-sm-12">
                                                                <?php if ($do_remove_search) { ?>
                                                                    <button class="btn btn-outline-danger" name="delete-old-search-opportunities"><?php echo check_isset($language, 'Delete Saved search'); ?></button>
                                                                <?php } ?>
                                                                <button type="submit" name="clear-saved-search-records" class="btn btn-outline-warning"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;<?php echo check_isset($language, 'Clear Saved search'); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </form>
                                            <?php } ?>
                                        </div>
                                    </div>

                                    <form method="POST" id="form_filter" enctype="multipart/form-data">
                                        <div class="col-filter" style="display: grid;grid-template-columns: repeat(4,1fr);gap:1rem;">


                                            <?php if (isset($_SESSION['save-filter-text-opportunities'])) {
                                                foreach ($_SESSION['save-filter-text-opportunities'] as $key => $val) {
                                            ?>
                                                    <?php if ($key == 0) { ?>
                                                        <input type="hidden" name="operation-opportunities[]" value="AND">
                                                    <?php } ?>



                                                    <select class="form-control selectpicker first-part " name="first-filter-opportunities[<?php echo $key ?>]" data-live-search="true" required data-form-index="<?php echo $key ?>" data-first-part="<?php echo $key ?>">
                                                        <option <?php if (!isset($_SESSION['first-filter-opportunities']) || $_SESSION['first-filter-opportunities'] == "") {
                                                                    echo "selected";
                                                                } ?> value="">&nbsp;</option>
                                                        <?php
                                                        $result_columns = mysqli_query($conn, "SHOW COLUMNS FROM `opportunities`");
                                                        while ($row = $result_columns->fetch_assoc()) {
                                                            if (!in_array($row['Field'], $GLOBALS['excluded_fields_opportunities'])) {
                                                        ?>
                                                                <option <?php if (isset($_SESSION['first-filter-opportunities'])) {
                                                                            if ($_SESSION['save-first-filter-opportunities'][$key] == $row['Field']) {
                                                                                echo "selected";
                                                                            }
                                                                        }
                                                                        ?> value="<?php echo $row['Field']; ?>" data-tokens="<?php echo $row['Field']; ?>"><?php echo ucfirst(str_replace("_", " ", $row['Field'])) ?></option>
                                                        <?php
                                                            }
                                                        }

                                                        ?>

                                                    </select>

                                                    <select class="form-control selectpicker second-part" name="second-filter-opportunities[<?php echo $key ?>]" data-live-search="true" data-form-index="<?php echo $key ?>" data-second-part="<?php echo $key ?>">
                                                        <option <?php if (!isset($_SESSION['save-second-filter-opportunities']) || $_SESSION['save-second-filter-opportunities'] == "") {
                                                                    echo "selected";
                                                                } ?> value="">&nbsp;</option>

                                                        <option id="inBetweenOption" data-inbetween-option="<?php echo $key ?>" <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                                                                                    if ($_SESSION['second-filter-opportunities'] == 'in_between') {
                                                                                                                                        echo "selected";
                                                                                                                                    }
                                                                                                                                } ?> value="in_between"><?php echo check_isset($language, 'In between'); ?></option>

                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'equals') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="equals"><?php echo check_isset($language, 'Equals'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'not_equals') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="not_equals"><?php echo check_isset($language, 'Not equals'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'contains') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="contains"><?php echo check_isset($language, 'Contains'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'does_not_contain') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="does_not_contain"><?php echo check_isset($language, 'Does not contain'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'starts_with') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="starts_with"><?php echo check_isset($language, 'Starts with'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'ends_with') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="ends_with"><?php echo check_isset($language, 'Ends with'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'does_not_start_with') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="does_not_start_with"><?php echo check_isset($language, 'Does not start with'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'does_not_end_with') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="does_not_end_with"><?php echo check_isset($language, 'Does not ends with'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'is_empty') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="is_empty"><?php echo check_isset($language, 'Is empty'); ?></option>
                                                        <option <?php if (isset($_SESSION['save-second-filter-opportunities'])) {
                                                                    if ($_SESSION['save-second-filter-opportunities'][$key] == 'is_not_empty') {
                                                                        echo "selected";
                                                                    }
                                                                } ?> value="is_not_empty"><?php echo check_isset($language, 'Is not empty'); ?></option>

                                                    </select>

                                                    <input autocomplete="off" name="filter-text-opportunities[<?php echo $key ?>]" class="form-control third-part" placeholder="<?php echo check_isset($language, 'Text for search...'); ?>" value="<?php echo $val ?>" data-form-index="<?php echo $key ?>">

                                                    <?php if ($key != 0) { ?>
                                                        <select name="operation-opportunities[]" class="form-control selectpicker" data-live-search="true" required>
                                                            <option <?php if (isset($_SESSION['save-operation-text-opportunities'])) {
                                                                        if ($_SESSION['save-operation-text-opportunities'][$key] == "AND") {
                                                                            echo "selected";
                                                                        }
                                                                    } ?> value="AND"><?php echo check_isset($language, 'AND'); ?></option>
                                                            <option <?php if (isset($_SESSION['save-operation-text-opportunities'])) {
                                                                        if ($_SESSION['save-operation-text-opportunities'][$key] == "OR") {
                                                                            echo "selected";
                                                                        }
                                                                    } ?> value="OR"><?php echo check_isset($language, 'OR') ?></option>
                                                        </select>
                                                    <?php }
                                                    if ($key == 0) { ?>
                                                        <br>
                                                <?php }
                                                }
                                            } else { ?>
                                                <input type="hidden" name="operation-opportunities[]" value="AND">


                                                <select class="form-control selectpicker first-part" name="first-filter-opportunities[0]" data-live-search="true" data-form-index="1" data-first-part="1">
                                                    <option <?php if (!isset($_SESSION['first-filter-opportunities']) || $_SESSION['first-filter-opportunities'] == "") {
                                                                echo "selected";
                                                            } ?> value="">&nbsp;</option>
                                                    <?php
                                                    $result_columns = mysqli_query($conn, "SHOW COLUMNS FROM `opportunities`");
                                                    while ($row = $result_columns->fetch_assoc()) {
                                                        if (!in_array($row['Field'], $GLOBALS['excluded_fields_opportunities'])) {
                                                    ?>
                                                            <option <?php if (isset($_SESSION['first-filter-opportunities'])) {
                                                                        if ($_SESSION['first-filter-opportunities'] == $row['Field']) {
                                                                            echo "selected";
                                                                        }
                                                                    }
                                                                    ?> value="<?php echo $row['Field']; ?>" data-tokens="<?php echo $row['Field']; ?>"><?php echo ucfirst(str_replace("_", " ", $row['Field'])) ?></option>
                                                    <?php
                                                        }
                                                    }

                                                    ?>

                                                </select>

                                                <select class="form-control selectpicker second-part" name="second-filter-opportunities[0]" data-live-search="true" data-form-index="1" data-second-part="1">
                                                    <option <?php if (!isset($_SESSION['second-filter-opportunities']) || $_SESSION['second-filter-opportunities'] == "") {
                                                                echo "selected";
                                                            } ?> value="">&nbsp;</option>
                                                    <option id="inBetweenOption" data-inbetween-option='1' <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                                                                if ($_SESSION['second-filter-opportunities'] == 'in_between') {
                                                                                                                    echo "selected";
                                                                                                                }
                                                                                                            } ?> value="in_between">In between</option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'equals') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="equals"><?php echo check_isset($language, 'Equals'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'not_equals') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="not_equals"><?php echo check_isset($language, 'Not equals'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'contains') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="contains"><?php echo check_isset($language, 'Contains'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'does_not_contain') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="does_not_contain"><?php echo check_isset($language, 'Does not contain'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'starts_with') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="starts_with"><?php echo check_isset($language, 'Starts with'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'ends_with') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="ends_with"><?php echo check_isset($language, 'Ends with'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'does_not_start_with') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="does_not_start_with"><?php echo check_isset($language, 'Does not start with'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'does_not_end_with') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="does_not_end_with"><?php echo check_isset($language, 'Does not ends with'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'is_empty') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="is_empty"><?php echo check_isset($language, 'Is empty'); ?></option>
                                                    <option <?php if (isset($_SESSION['second-filter-opportunities'])) {
                                                                if ($_SESSION['second-filter-opportunities'] == 'is_not_empty') {
                                                                    echo "selected";
                                                                }
                                                            } ?> value="is_not_empty"><?php echo check_isset($language, 'Is not empty'); ?></option>

                                                </select>

                                                <input data-form-index="1" autocomplete="off" name="filter-text-opportunities[0]" class="form-control third-part" placeholder="<?php echo check_isset($language, 'Text for search...'); ?>" <?php if (isset($_SESSION['filter-text'])) {
                                                                                                                                                                                                                                                if ($_SESSION['filter-text'] != "") {
                                                                                                                                                                                                                                            ?> value="<?php echo $_SESSION['filter-text']; ?>" <?php
                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                        } ?>>

                                                <br>
                                            <?php } ?>
                                        </div><br>
                                        <a href="#" class="btn btn-outline-secondary add-col"><i class="fa fa-plus"></i>&nbsp; <?php echo check_isset($language, 'Add'); ?></a>
                                        <button type="submit" name="filter-records" class="btn btn-primary"><i class="fa fa-search"></i>&nbsp;<?php echo check_isset($language, 'Search'); ?></button>
                                        <a href="pipelines.php?id=<?php echo $id; ?>&mode=create_stage" class="btn btn-outline-secondary kanbanbtn"> <?php echo check_isset($language, 'Create Stage'); ?></a>
                                        <a href="pipelines.php?id=<?php echo $id; ?>&mode=setting" class="btn btn-outline-secondary kanbanbtn"> <?php echo check_isset($language, 'Stage Setting'); ?></a>
                                        <?php
                                        if (isset($_SESSION['first-filter-opportunities']) && isset($_SESSION['second-filter-opportunities']) && isset($_SESSION['filter-text-opportunities'])) {
                                        ?>
                                            <input style="display: inline-block;width: 20%;" type="text" class="form-control" name="search_name_field_opportunities" placeholder="<?php echo check_isset($language, 'Store search name.'); ?>">
                                            <button type="button" class="btn btn-outline-success btn-fl-rec"><i class="fa fa-floppy-o"></i>&nbsp;<?php echo check_isset($language, 'Save this search'); ?></button>
                                            <input type="hidden" name="save-filter-records_opportunities" value="">
                                            <button type="submit" name="clear-search-records" class="btn btn-outline-warning"><i class="fa fa-times" aria-hidden="true"></i>&nbsp;<?php echo check_isset($language, 'Clear search'); ?></button>
                                        <?php } ?>

                                    </form>

                                </div>
                            </div>


                            <div class="container-fluid px-0" id="gridcol">
                                <?php
                                if (isset($_SESSION['error'])) {
                                    foreach ($_SESSION['error'] as $errors) {
                                        echo "<p class='text-danger'>" . $errors . "</p>";
                                    }
                                    unset($_SESSION['error']);
                                }

                                if (isset($_SESSION['success'])) {
                                    foreach ($_SESSION['success'] as $errors) {
                                        echo "<p class='text-success'>" . $errors . "</p>";
                                    }
                                    unset($_SESSION['success']);
                                }
                                ?>

                                <div class="row clearfix">
                                    <?php


                                    $records_per_page = isset($settings_data['leads_per_page']) ? $settings_data['leads_per_page'] : 6;
                                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                    $select_query = mysqli_query($conn, "SELECT * FROM `opportunities`");
                                    $number_of_records = mysqli_num_rows($select_query);

                                    $number_of_pages = ceil($number_of_records / $records_per_page);
                                    $limit_start = ($page - 1) * $records_per_page;
                                    $limit_end = $records_per_page;


                                    /* $lead_stages_query = mysqli_query($conn, "SELECT id, name,body,lead_type FROM `lead_stage` WHERE `module`='Lead' OR `pipeline`='Lead'");
                                    $lead_stage = array();
                                    $lead_stage_label = array();
                                    while ($row = $lead_stages_query->fetch_assoc()) {
                                        $lead_stage[$row['id']] = $row['name'];
                                        $lead_stage_label[$row['id']] = $row['body'];
                                    } */

                                    $share_role = array();
                                    $select_query = mysqli_query($conn, "SELECT * FROM `system_roles`");
                                    if (mysqli_num_rows($select_query) > 0) {
                                        $data = array();
                                        foreach ($select_query as $key => $row) {
                                            $data[] = $row;
                                            $role['roles'][$row['role_id']] = $row;
                                            $role['parents'][$row['role_parent']][] = $row['role_id'];

                                            if ($row['role_parent'] != 0) {
                                                $share_role[$row['role_id']] = $row['role_name'];
                                            }
                                        }
                                        function getTree($parent, $role)
                                        {
                                            $ids = '';
                                            if (isset($role['parents'][$parent])) {
                                                foreach ($role['parents'][$parent] as $cat_id) {
                                                    if (!isset($role['parents'][$cat_id])) {
                                                        $ids .= ',' . $role['roles'][$cat_id]['role_id'];
                                                    }

                                                    if (isset($role['parents'][$cat_id])) {
                                                        $ids .= ',' . $role['roles'][$cat_id]['role_id'];
                                                        $ids .= ',' . getTree($cat_id, $role);
                                                    }
                                                }
                                            }
                                            return $ids;
                                        }

                                        $ids = getTree($qr1['role_id'], $role);
                                        if (!empty(trim($ids))) {
                                            $exp = explode(',', $ids);
                                            $filt = array_filter($exp);
                                            $filt[] = $auth_id;

                                            $flip_var = array_flip($filt);
                                            $flip_index = array_flip($flip_var);
                                            $user_ids = implode(',', $flip_index); // user_roles ids
                                            $query_user_ids = mysqli_query($conn, "SELECT `id` FROM `users` WHERE `user_role_id` IN($user_ids)");
                                            $user_ids = array();
                                            foreach ($query_user_ids as $index_uid => $value_uid) {
                                                $user_ids[] = $value_uid['id'];
                                            }
                                            if (sizeof($user_ids) > 0) {
                                                $user_ids[] = $auth_id;
                                            }
                                            $implode_user_ids = implode(',', $user_ids);
                                        }
                                    }

                                    if (isset($_SESSION['all_opportunities_query'])) {
                                        if ($auth_role_type == 'super_admin') {

                                            $select_query = mysqli_query($conn, "SELECT opps.*, pipe.pipeline_title, stg.name FROM `opportunities` as opps LEFT JOIN `opportunity_pipelines` as pipe ON opps.pipeline = pipe.db_id LEFT JOIN `pipeline_stages` as stg ON opps.opportunity_stage = stg.id WHERE opps.pipeline = $id " . $_SESSION['all_opportunities_query'] . " LIMIT $limit_start, $limit_end");
                                        } else {
                                            if (!empty($implode_user_ids)) {
                                                //$select_query = mysqli_query($conn, "SELECT leads.*,leads.id as leadid, leads.email as lead_email FROM `opportunities` WHERE `lead_stage`!=0 ".$_SESSION['all_opportunities_query']." AND (`lead_by` IN ($implode_user_ids) ".$_SESSION['all_opportunities_query'].") OR (`lead_assigned_to` IN ($implode_user_ids) ".$_SESSION['all_opportunities_query'].") OR  (CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' ".$_SESSION['all_opportunities_query'].") AND `module`='Lead' LIMIT $limit_start, $limit_end");
                                                $select_query = mysqli_query($conn, "SELECT leads.*,leads.id as leadid, leads.email as lead_email FROM `opportunities` WHERE `lead_stage` != 0 " . $_SESSION['all_opportunities_query'] . " AND leads.`module`='Lead' AND (`lead_by` IN ($implode_user_ids) " . $_SESSION['all_opportunities_query'] . ") OR (`lead_assigned_to` IN ($implode_user_ids) AND `module` = 'Lead' " . $_SESSION['all_opportunities_query'] . ") OR (CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' AND `module`='Lead' " . $_SESSION['all_opportunities_query'] . ") LIMIT $limit_start, $limit_end");
                                            } else {
                                                $select_query = mysqli_query($conn, "SELECT leads.*,leads.id as leadid, leads.email as lead_email FROM `opportunities` WHERE `lead_stage` != 0 " . $_SESSION['all_opportunities_query'] . " AND `lead_by` = $auth_id OR (`lead_assigned_to` IN ($auth_id) " . $_SESSION['all_opportunities_query'] . ") OR  (CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%') AND `module`='Lead' " . $_SESSION['all_opportunities_query'] . " LIMIT $limit_start, $limit_end");
                                            }
                                        }
                                    } else {
                                        if ($auth_role_type == 'super_admin') {
                                            //$select_query = mysqli_query($conn, "SELECT opps.*, pipe.pipeline_title, stg.name FROM `opportunities` as opps LEFT JOIN `opportunity_pipelines` as pipe ON opps.pipeline = pipe.db_id LEFT JOIN `pipeline_stages` as stg ON opps.opportunity_stage = stg.id LIMIT $limit_start, $limit_end");
                                            $select_query = custom_query("SELECT opps.*, pipe.pipeline_title, stg.name FROM `opportunities` as opps LEFT JOIN `opportunity_pipelines` as pipe ON opps.pipeline = pipe.db_id LEFT JOIN `pipeline_stages` as stg ON opps.opportunity_stage = stg.id WHERE opps.pipeline = $id LIMIT $limit_start, $limit_end");
                                            //$select_query = get_alll('opportunities', array('limit' => array($limit_start, $limit_end)) );
                                        } else {
                                            if (!empty($implode_user_ids)) {
                                                $select_query = mysqli_query($conn, "SELECT leads.*,leads.id as leadid, leads.email as lead_email FROM `opportunities` WHERE `lead_stage` != 0 AND leads.`module`='Lead' AND `lead_by` IN ($implode_user_ids) OR (`lead_assigned_to` IN ($implode_user_ids) AND `module` = 'Lead') OR (CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' AND `module`='Lead') LIMIT $limit_start, $limit_end");
                                            } else {
                                                $select_query = mysqli_query($conn, "SELECT leads.*,leads.id as leadid, leads.email as lead_email FROM `opportunities` WHERE `lead_stage` != 0 AND leads.`module`='Lead' AND `lead_by` = $auth_id OR (`lead_assigned_to` IN ($auth_id) AND leads.`module` = 'Lead' ) OR  (CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' AND `module`='Lead') LIMIT $limit_start, $limit_end");
                                            }
                                        }
                                    }


                                    if (mysqli_num_rows($select_query) > 0) {

                                        foreach ($select_query as $key => $data) {
                                            $opp_pipeline = $data['pipeline'];
                                            $contacts = !empty($data['opportunity_contact']) ? explode(',', $data['opportunity_contact']) : array();
                                            $contact = !empty($contacts) ? prospect_as_contact($contacts[0]) : '&nbsp;';
                                            $contact_name = !empty($contacts) ? 'Contact: ' . $contact->prospect_name : '&nbsp;';
                                            $contact_email = !empty($contacts) ? $contact->email : '&nbsp;';
                                            $contact_mobile_phone = !empty($contacts) ? $contact->mobile_phone : '&nbsp;';
                                            $number = $number_whatsapp = '';
                                            if (!empty($contacts)) {
                                                if (!empty($contact->number)) {
                                                    $xplode = explode('_', $contact->number);
                                                    if (isset($xplode[1]) && !empty($xplode[1])) {
                                                        $number = str_replace(array('_', ' '), array('', ''), $contact->number);
                                                        $number_whatsapp = str_replace(array('+', '-'), array('', ''), $number);
                                                    } else {
                                                        $number = '&nbsp;';
                                                        $number_whatsapp = '';
                                                    }
                                                } else {
                                                    $number = '&nbsp;';
                                                    $number_whatsapp = '';
                                                }
                                            }
                                    ?>
                                            <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                                                <div class="card borderblue">
                                                    <div class="card-body text-center ribbon">
                                                        <div class="starBox">
                                                            <a href="javascript:void(0);" class="mail-star"><i class="fa fa-star"></i></a>
                                                        </div>

                                                        <div class="card_heading">
                                                            <h6 class="mt-3 mb-3">
                                                                <?php if (isset($auth_permissions['opportunities']['view']) && $auth_permissions['opportunities']['view'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                    <a class="leadname" href="./opportunity.php?id=<?php echo $data['op_id']; ?>"><?php echo !empty($data['opportunity_value']) ? $data['opportunity_value'] : '&nbsp;'; ?></a>
                                                                <?php } else { ?>
                                                                    <?php echo !empty($data['opportunity_value']) ? $data['opportunity_value'] : '&nbsp;'; ?>
                                                                <?php } ?>
                                                            </h6>
                                                            <span><?php echo !empty($data['forecasted_close_date']) ? 'Forcaste: ' . $data['forecasted_close_date'] : '&nbsp;'; ?></span>

                                                        </div>
                                                        <div class="dateBox"><span><?php echo !empty($data['forecasted_close_date']) ? 'Forcaste: ' . $data['forecasted_close_date'] : '&nbsp;'; ?></span></div>
                                                        <div class="contactBox"><span><?php echo $contact_name; ?></span></div>
                                                        <?php if (!empty($contacts)) { ?>
                                                            <div class="mb-1 socialBtn">
                                                                <a href="mailto:<?php echo $contact_email; ?>"><i class="fa fa-envelope"></i></a> &nbsp;
                                                                <a href="tel:<?php echo $contact_mobile_phone; ?>"><i class="fa fa-phone"></i></a> &nbsp;
                                                                <a href="sms:<?php echo $number; ?>"><i class="fa fa-mobile"></i></a> &nbsp;
                                                                <a href="https://wa.me/<?php echo $number_whatsapp; ?>"><i class="fa fa-whatsapp"></i></a> &nbsp;
                                                                <a href="https://justcall.io/app/macapp/dialpad_app.php?numbers=<?php echo $number; ?>"> <img src="./assets/images/dial.png"> </a>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="editdetabtn">
                                                            <?php if (isset($auth_permissions['opportunities']['edit']) && $auth_permissions['opportunities']['edit'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                <a href="editopportunity.php?id=<?php echo $data['op_id']; ?>" class="btn btn-default btn-sm"><i class="fa fa-edit"></i> <?php echo check_isset($language, 'Edit'); ?></a>
                                                            <?php } ?>

                                                            <?php if (isset($auth_permissions['opportunities']['delete']) && $auth_permissions['opportunities']['delete'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                <a href="javascript:;" class="btn btn-default btn-sm"><i class="fa fa-trash-o"></i><?php echo check_isset($language, 'Delete'); ?></a>
                                                            <?php } ?>
                                                        </div>
                                                        <div class="row text-center mt-4 leadbot">
                                                            <div class="col-6 border-right">
                                                                <label class="mb-0"><?php echo check_isset($language, 'PIPELINE'); ?></label>
                                                                <h4 class="font-16"><?php echo $data['pipeline_title']; ?> </h4>

                                                            </div>
                                                            <div class="col-6">
                                                                <label class="mb-0"><?php echo check_isset($language, 'STAGE'); ?></label>
                                                                <h4 class="font-16"><?php echo $data['name']; ?></h4>
                                                            </div>
                                                            <div class="col-12">

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <div class="col-12"><?php echo check_isset($language, 'No any associated Opportunity found!'); ?></div>
                                    <?php } ?>
                                </div>

                                <?php
                                if ($number_of_records < 0) {
                                ?>
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination justify-content-start">

                                            <?php if ($page == 1) { ?>
                                                <li class="page-item disabled" data-toggle="tooltip" data-title="First Page" data-placement="bottom"> <a class="page-link"><i class="fa fa-angle-left"></i></a> </li>
                                            <?php } elseif ($page > 1) { ?>
                                                <li class="page-item" data-toggle="tooltip" data-title="First" data-placement="bottom"> <a class="page-link" href="?page=1"><i class="fa fa-angle-left"></i></a> </li>
                                            <?php } ?>

                                            <?php if ($page == 1) { ?>
                                                <li class="page-item disabled" data-toggle="tooltip" data-title="Previous" data-placement="bottom"> <a class="page-link"><i class="fa fa-angle-double-left"></i></a> </li>
                                            <?php } elseif ($page > 1) { ?>
                                                <li class="page-item" data-toggle="tooltip" data-title="Previous" data-placement="bottom"> <a class="page-link" href="?page=<?php echo $page - 1; ?>"><i class="fa fa-angle-double-left"></i></a> </li>
                                            <?php } ?>

                                            <?php for ($i = 1; $i <= $number_of_pages; $i++) { ?>
                                                <?php if ($page == $i) { ?>
                                                    <li class="page-item active"><a class="page-link"><?php echo $i; ?></a></li>
                                                <?php } else { ?>
                                                    <li class="page-item"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                                                <?php } ?>

                                            <?php } ?>

                                            <?php if ($page < $number_of_pages) { ?>
                                                <li class="page-item" data-toggle="tooltip" data-title="Next" data-placement="bottom"><a class="page-link" href="?page=<?php echo $page + 1; ?>"><i class="fa fa-angle-double-right"></i></a></li>
                                            <?php } elseif ($page == $number_of_pages) { ?>
                                                <li class="page-item disabled" data-toggle="tooltip" data-title="Next" data-placement="bottom"><a class="page-link"><i class="fa fa-angle-double-right"></i></a></li>
                                            <?php } ?>

                                            <?php if ($page < $number_of_pages) { ?>
                                                <li class="page-item" data-toggle="tooltip" data-title="Last Page" data-placement="bottom"><a class="page-link" href="?page=<?php echo $number_of_pages; ?>"><i class="fa fa-angle-right"></i></a></li>
                                            <?php } elseif ($page == $number_of_pages) { ?>
                                                <li class="page-item disabled" data-toggle="tooltip" data-title="Last Page" data-placement="bottom"><a class="page-link"><i class="fa fa-angle-right"></i></a></li>
                                            <?php } ?>
                                        </ul>
                                    </nav>
                                <?php } ?>

                            </div>


                            <div class="tab-content taskboard">
                                <div class="tab-pane fade" id="kanban" role="tabpanel">

                                    <div class="overflowdesign" style="overflow: auto;">
                                        <div class="full_lead_stage full_leadnew full_lead_stage_new" id="sticker">
                                            <?php

                                            /* $get_contact_types = get_row('typeof_contacts', array('type_module' => 'Prospect'));
											$contact_types_list = array();
											if(numRows($get_contact_types) > 0){
												while($row = $get_contact_types->fetch_object()){
													$contact_types_list[$row->db_id] = $row->type_name;
												}
											} */

                                            $stage_list = $prospect_stage_list = array();
                                            //$select_prospectStage_query = query_prospect_stages($conn);
                                            $select_prospectStage_query = query_opportunity_pipeline_stages($id);
                                            if (numRows($select_prospectStage_query) > 0) {
                                                while ($pros_stg_row = $select_prospectStage_query->fetch_object()) {
                                                    $prospect_stage_list[$pros_stg_row->id] = $pros_stg_row->name;
                                                }
                                            }

                                            $full_lead_stage = 0;
                                            //$get_reason_list_query = mysqli_query($conn, "SELECT * FROM `stage_lost_reasons` WHERE `reason_status` = 1 AND `module` = 'Lead' AND `pipeline` = 'Lead'");
                                            $get_reason_list_query = get_row('pipeline_stage_lost_reasons', array('pipeline' => $opp_pipeline));
                                            //$select_query = mysqli_query($conn, "SELECT * FROM `lead_stage` WHERE (module='Lead' OR pipeline='Lead') ORDER BY order_number ASC");
                                            $select_query = get_row('pipeline_stages', array('pipeline_id' => $id), 'ORDER BY order_number ASC');
                                            foreach ($select_query as $key => $data) {
                                                $stage_list[$data['id']] = $data['name'];
                                                $data_column_width = $data['column_width'] > 0 || !empty($data['column_width']) ? $data['column_width'] : 300;
                                                $full_lead_stage = $full_lead_stage + preg_replace("/[^0-9]/", "", $data_column_width) + 100; ?>
                                                <div class="custom_lead_box custom_lead_box_new ui-sortable-handle" style="min-width: <?php echo $data_column_width; ?>px;  min-height: 300px; margin-right:9px;">
                                                    <div class="card planned_task">
                                                        <div class="card-header" style="background: <?php if (!empty($data['bg_color'])) {
                                                                                                        echo $data['bg_color'];
                                                                                                    } ?>; color: <?php if (!empty($data['font_color'])) {
                                                                                                                        echo $data['font_color'];
                                                                                                                    } ?>;">
                                                            <h3 class="card-title" data_stage_name="<?php echo $data['body']; ?>"><?php echo $data['name']; ?></h3>
                                                            <div class="card-options">
                                                                <a href="#" class="card-options-collapse" data-toggle="card-collapse" style="color:<?php if (!empty($data['font_color'])) {
                                                                                                                                                        echo $data['font_color'];
                                                                                                                                                    } ?>;"><i class="fe fe-chevron-up"></i></a>
                                                                <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen" style="color:<?php if (!empty($data['font_color'])) {
                                                                                                                                                            echo $data['font_color'];
                                                                                                                                                        } ?>;"><i class="fe fe-maximize"></i></a>

                                                                <?php if ((isset($auth_permissions['opportunity_stages']['edit']) && $auth_permissions['opportunity_stages']['edit'] == 1) || (isset($auth_permissions['opportunity_stages']['delete']) && $auth_permissions['opportunity_stages']['delete'] == 1) || $auth_role_type == 'super_admin') { ?>
                                                                    <div class="item-action dropdown ml-2">
                                                                        <a href="javascript:void(0)" data-toggle="dropdown" aria-expanded="false" style="color:<?php if (!empty($data['font_color'])) {
                                                                                                                                                                    echo $data['font_color'];
                                                                                                                                                                } ?>;"><i class="fe fe-more-vertical"></i></a>
                                                                        <div class="dropdown-menu dropdown-menu-right">
                                                                            <?php if (isset($auth_permissions['opportunity_stages']['edit']) && $auth_permissions['opportunity_stages']['edit'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                                <a href="./pipelines.php?id=<?php echo $id; ?>&mode=edit_stage_<?php echo $data['id']; ?>" class="dropdown-item"><i class="dropdown-icon fa fa-edit"></i> Edit</a>
                                                                            <?php } ?>

                                                                            <?php if (isset($auth_permissions['opportunity_stages']['delete']) && $auth_permissions['opportunity_stages']['delete'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                                <a href="javascript:void(0)" class="dropdown-item item-delete" data-type="stage_remove" data-stage="<?php echo $data['id']; ?>"><i class="dropdown-icon fa fa-trash"></i> Delete</a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="card-bodyn">
                                                                <div class="dd" data-plugin="nestable" data_stage_id="<?php echo $data['id']; ?>">
                                                                    <ul class="sortable1 p-3" id="<?php echo $data['wonlost']; ?>" data-id="<?php echo $data['id']; ?>" data-order="<?php echo $data['order_number']; ?>">
                                                                        <?php

                                                                        if ($auth_role_type == 'super_admin') {
                                                                            $query_type = array('super_admin');
                                                                        } else {
                                                                            $query_type = array('owner', 'own', 'follower');
                                                                        }

                                                                        $q_round = 0;
                                                                        foreach ($query_type as $qtype) {

                                                                            if ($qtype == 'super_admin') {
                                                                                if (isset($_SESSION['all_opportunities_query'])) {
                                                                                    $q = $_SESSION['all_opportunities_query'];
                                                                                    $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE 1=1 " . $_SESSION['all_opportunities_query'] . " ");
                                                                                } else {
                                                                                    //$lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE lead_stage = '" . $data['id'] . "' AND module = 'Lead' ");
                                                                                    //echo "SELECT * FROM `opportunities` WHERE `pipeline` = $id AND `opportunity_stage` = ". $data['id'] ."";
                                                                                    $lead_query = get_row('opportunities', array('pipeline' => $id, 'opportunity_stage' => $data['id']));
                                                                                }

                                                                                $dd_handle = '';
                                                                            }
                                                                            if ($qtype == 'owner') {
                                                                                if (isset($_SESSION['all_opportunities_query'])) {
                                                                                    $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND `lead_by` = $auth_id " . $_SESSION['all_opportunities_query'] . " ");
                                                                                } else {

                                                                                    $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND `lead_by` = $auth_id ");
                                                                                }
                                                                                $dd_handle = '';
                                                                            }

                                                                            if ($qtype == 'own') {
                                                                                if (isset($_SESSION['all_opportunities_query'])) {
                                                                                    $lead_query = $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND `lead_assigned_to` IN ($implode_user_ids) " . $_SESSION['all_opportunities_query'] . " ");
                                                                                } else {
                                                                                    $lead_query = $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND `lead_assigned_to` IN ($implode_user_ids) ");
                                                                                }
                                                                                $dd_handle = 'ld_own';
                                                                            }

                                                                            if ($qtype == 'follower') {
                                                                                if (isset($_SESSION['all_opportunities_query'])) {
                                                                                    $lead_query = $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' " . $_SESSION['all_opportunities_query'] . " ");
                                                                                } else {

                                                                                    $lead_query = $lead_query = mysqli_query($conn, "SELECT * FROM `opportunities` WHERE `lead_stage` = '" . $data['id'] . "' AND module = 'Lead' AND CONCAT(',', lead_followers, ',') LIKE '%,$auth_id,%' ");
                                                                                }
                                                                                $dd_handle = 'ld_follow';
                                                                            }

                                                                            if ($lead_query->num_rows == 0) {
                                                                                $q_round++;
                                                                            }

                                                                            if ($lead_query->num_rows == 0) {
                                                                            } else {
                                                                                foreach ($lead_query as $lead_key => $lead_data) {
                                                                                    $stage_name = $data['name']; //get_lead_stage_name($conn, $stage_id);

                                                                                    $contacts = !empty($lead_data['opportunity_contact']) ? explode(',', $lead_data['opportunity_contact']) : array();
                                                                                    $contact = !empty($contacts) ? prospect_as_contact($contacts[0]) : '&nbsp;';
                                                                                    $contact_name = !empty($contacts) ? 'Contact: ' . $contact->prospect_name : '&nbsp;';
                                                                                    $contact_email = !empty($contacts) ? $contact->email : '&nbsp;';
                                                                                    $contact_mobile_phone = !empty($contacts) ? $contact->mobile_phone : '&nbsp;';
                                                                                    $number = $number_whatsapp = '';
                                                                                    if (!empty($contacts)) {
                                                                                        if (!empty($contact->number)) {
                                                                                            $xplode = explode('_', $contact->number);
                                                                                            if (isset($xplode[1]) && !empty($xplode[1])) {
                                                                                                $number = str_replace(array('_', ' '), array('', ''), $contact->number);
                                                                                                $number_whatsapp = str_replace(array('+', '-'), array('', ''), $number);
                                                                                            } else {
                                                                                                $number = '&nbsp;';
                                                                                                $number_whatsapp = '';
                                                                                            }
                                                                                        } else {
                                                                                            $number = '&nbsp;';
                                                                                            $number_whatsapp = '';
                                                                                        }
                                                                                    }



                                                                                    /* if (!empty($lead_data['number'])) {
																						$xplode = explode('_', $lead_data['number']);
																						if(isset($xplode[1]) && !empty($xplode[1])){
																							$number = str_replace(array('_', ' '), array('', ''), $lead_data['number']);
																						}else{
																							$number = '&nbsp;';
																						}
																					} else {
																						$number = '&nbsp;';
																					} */

                                                                                    if ($dd_handle == 'ld_own') {
                                                                                        if ($lead_data['lead_assigned_to'] == $auth_id) {
                                                                                            $dd_handle = 'ld_owner';
                                                                                        }
                                                                                    }
                                                                        ?>
                                                                                    <li class="ui-state-default lead-item lead-item-<?php echo $lead_data['op_id']; ?> lsort-<?php echo $dd_handle; ?>" data-old-stage="<?php echo $data['op_id']; ?>" data-new-stage="" data-id="<?php echo $lead_data['op_id']; ?>" data-opportunities_id="<?php echo $lead_data['op_id']; ?>">
                                                                                        <div class="sortable1__haeader d-flex justify-content-between align-items-center">
                                                                                            <div class="sortable1__haeader--left">
                                                                                                <span>#<?php echo $lead_data['op_id']; ?></span>
                                                                                            </div>
                                                                                            <div class="sortable1__haeader--right">
                                                                                                <ul class="list d-flex align-items-center">
                                                                                                    <?php if ($lead_data["opportunity_owner"] == $auth_id || $auth_role_type == "super_admin") { ?>
                                                                                                        <li class="box"><a href="javascript:;" class="agn-ac" data-toggle="tooltip" title="Assign To.." data-placement="top" data-row="<?php echo $lead_data['op_id']; ?>"><i class="fas fa-thumbtack"></i></a></li>
                                                                                                    <?php } ?>

                                                                                                    <?php if (isset($auth_permissions['opportunity_stages']['edit']) && $auth_permissions['opportunity_stages']['edit'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                                                        <li class="boxicon"><a href="./editopportunity.php?id=<?php echo $lead_data['op_id']; ?>" data-toggle="tooltip" title="Edit" data-placement="top"><i class="fa fa-pencil"></i></a></li>
                                                                                                    <?php } ?>

                                                                                                    <?php if (isset($auth_permissions['opportunity_stages']['view']) && $auth_permissions['opportunity_stages']['view'] == 1 || $auth_role_type == 'super_admin') { ?>
                                                                                                        <li class="boxicon"><a href="./opportunity.php?id=<?php echo $lead_data['op_id']; ?>" data-toggle="tooltip" title="View" data-placement="top"><i class="fa fa-eye"></i></a></li>
                                                                                                    <?php } ?>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="sortable1__bottom <?php echo ($dd_handle == 'ld_own' ? ($lead_data['lead_assigned_to'] == $auth_id ? 'ld_owner' : $dd_handle) : $dd_handle); ?>" data-assign="<?php echo $lead_data['lead_assigned_to']; ?>">
                                                                                            <ul class="list">
                                                                                                <li class="h7"><i class="fa-sharp fa-solid fa-tag"></i> <a><?php echo $lead_data['opportunity_value'];  ?></a></li>
                                                                                                <li><i class="fa-solid fa-clock"></i> <span><?php echo !empty($lead_data['forecasted_close_date']) ? convert_date($lead_data['forecasted_close_date'], 'date') : '';;  ?></span></li>
                                                                                                <li><i class="fa fa-phone"></i> <span><?php echo $number; ?></span></li>
                                                                                            </ul>
                                                                                            <a href="./opportunity.php?id=<?php echo $lead_data['op_id']; ?>" class="btn btn-sortlist"><span>More Info</span></a>
                                                                                            <?php
                                                                                            if ($stage_name == 'Won Stage' || $stage_name == 'Won' || $stage_name == 'Qualified') {
                                                                                                echo '<div> <button type="button" class="btn btn-sm bsm btn-default btn-conv" data-stage="' . $data['op_id'] . '" data-opportunities="' . $lead_data['op_id'] . '">Convert & Assign</button> </div>';
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                    </li>
                                                                        <?php }
                                                                            }
                                                                        }
                                                                        if ($q_round > 0) {
                                                                            //echo '<li class="dd-item" data-id="non_entry_'.$data['id'].'" lead_id="'.$data['id'].'"></li>';
                                                                        }
                                                                        ?>

                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php }     ?>
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <?php include 'include/import.php'; ?>
            <?php if ((isset($db_fields['leads_import'])) || $auth_role_type == 'super_admin') { ?>
                <div id="import_data" style="display: none;" class="popup">
                    <form action="./core/import_data.php" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo check_isset($language, 'Import Leads'); ?></h3>
                                    <div class="card-options">
                                        <a href="#" class="card-options-fullscreen" data-toggle="card-fullscreen"><i class="fe fe-maximize"></i></a>
                                        <a href="#" class="close_popup_"><i class="fe fe-x"></i></a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div id="wizard_horizontal" role="application" class="wizard clearfix">
                                        <div class="steps clearfix">
                                            <ul role="tablist">
                                                <li role="tab" class="first current">
                                                    <a id="wizard_horizontal-t-0" href="javascript:;">
                                                        <span class="number"><?php echo check_isset($language, '1.'); ?></span>
                                                        <?php echo check_isset($language, 'Upload CSV File') ?></a>
                                                </li>
                                                <li role="tab" class="disabled"><a id="wizard_horizontal-t-1" href="javascript:;"><span class="number"><?php echo check_isset($language, '2.'); ?></span><?php echo check_isset($language, 'Duplicate Handling'); ?></a></li>
                                                <li role="tab" class="disabled"><a id="wizard_horizontal-t-2" href="javascript:;"><span class="number"><?php echo check_isset($language, '3.'); ?></span><?php echo check_isset($language, 'Field Mapping'); ?></a></li>
                                            </ul>
                                        </div>
                                        <div class="content clearfix">
                                            <section id="wizard_horizontal-p-0" role="tabpanel" class="body current">
                                                <h6 class="mb-0"><?php echo check_isset($language, 'Import from CSV file'); ?></h6>
                                                <hr>

                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label"><?php echo check_isset($language, 'Select CSV file'); ?></label>
                                                    <div class="col-sm-7">
                                                        <div class="btn btn-primary btn-file">
                                                            <i class="fa fa-laptop"></i>
                                                            <span class="hidden-xs"><?php echo check_isset($language, 'Select from My Computer'); ?></span>
                                                            <input type="file" name="import_file" id="import_file">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label"><?php echo check_isset($language, 'Has Header'); ?></label>
                                                    <div class="col-sm-7">
                                                        <label class="custom-control custom-checkbox">
                                                            <input class="custom-control-input" type="checkbox" value="1" name="has-header">
                                                            <span class="custom-control-label"></span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-sm-5 col-form-label"><?php echo check_isset($language, 'Delimiter'); ?></label>
                                                    <div class="col-sm-7">
                                                        <div class="ggrid">
                                                            <label class="custom-control custom-radio custom-control-inline">
                                                                <input class="custom-control-input" type="radio" name="delimeters-post" id="comma" value=",}}34" checked="checked">
                                                                <span class="custom-control-label"><?php echo check_isset($language, 'Comma'); ?></span>
                                                            </label>

                                                            <label class="custom-control custom-radio custom-control-inline">
                                                                <input class="custom-control-input" type="radio" name="delimeters-post" id="semicolon" value=";}}1">
                                                                <span class="custom-control-label"><?php echo check_isset($language, 'Semicolon'); ?></span>
                                                            </label>

                                                            <label class="custom-control custom-radio custom-control-inline">
                                                                <input class="custom-control-input" type="radio" name="delimeters-post" id="pipe" value="|}}1">
                                                                <span class="custom-control-label"><?php echo check_isset($language, 'Pipe'); ?></span>
                                                            </label>

                                                            <label class="custom-control custom-radio custom-control-inline">
                                                                <input class="custom-control-input" type="radio" name="delimeters-post" id="caret" value="^}}1">
                                                                <span class="custom-control-label"><?php echo check_isset($language, 'Caret'); ?></span>
                                                            </label>
                                                        </div>

                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-sm-12 text-right">
                                                        <input type="submit" class="btn btn-primary" value="<?php echo check_isset($language, 'Next'); ?>" name="import_data">
                                                    </div>
                                                </div>

                                            </section>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>

            <div class="modal fade" id="shareSearch" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="title" id="lostLabel"><?php echo check_isset($language, 'Share Search to...'); ?></h6>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group spsr-shr">
                                <div class="custom-controls-stacked">
                                    <label class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="typeSearch" value="private" checked="">
                                        <div class="custom-control-label"><?php echo check_isset($language, 'Private'); ?></div>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="typeSearch" value="global">
                                        <div class="custom-control-label"><?php echo check_isset($language, 'Global'); ?></div>
                                    </label>
                                    <label class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="typeSearch" value="specific">
                                        <div class="custom-control-label"><?php echo check_isset($language, 'Specific User(s)'); ?></div>
                                    </label>
                                    <div class="spsf_user">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="<?php echo check_isset($language, 'Type to Search'); ?>" id="spsr-user">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary spsr-user" type="button"> <i class="fa fa-search"></i> </button>
                                            </div>
                                        </div>
                                        <div class="c2_own"></div>
                                    </div>

                                    <label class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" name="typeSearch" value="role">
                                        <div class="custom-control-label"><?php echo check_isset($language, 'Role'); ?></div>
                                    </label>

                                    <div class="spsf_role">
                                        <div class="input-group mb-3">
                                            <select class="form-control" placeholder="<?php echo check_isset($language, 'Type to Search'); ?>" id="spsr-role">
                                                <?php
                                                foreach ($share_role as $role_index => $index_value) {
                                                    echo '<option value="' . $role_index . '">' . $index_value . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="c3_own"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-srsh"><?php echo check_isset($language, 'Save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="newstagebox" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="title" id="newLabel"><?php echo check_isset($language, 'if you wants to assign to another user'); ?></h6>
                            <button type="button" class="close new_close_">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="new-stage-content">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-new"><?php echo check_isset($language, 'Assign & Convert'); ?></button>
                            <button type="button" class="btn btn-primary not_now"><?php echo check_isset($language, 'Not Now'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="loststagebox" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="title" id="lostLabel"><?php echo check_isset($language, 'Reason for Lost Stage'); ?></h6>
                            <button type="button" class="close close_">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="stage-content">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-lost"><?php echo check_isset($language, 'Save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="stgrequired" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="title"><?php echo check_isset($language, 'Please fill the fields before update'); ?></h6>
                            <button type="button" class="close stg_close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="req-content">
                                <div class="text-center"> Please wait... <i class="fa fa-spinner fa-pulse"></i> </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-req"><?php echo check_isset($language, 'Save & Continue'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="newasgnbox" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="title" id="newLabel"><?php echo check_isset($language, 'Assign Lead'); ?></h6>
                            <button type="button" class="close new_close_">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="asgn-content">

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default not_now"><?php echo check_isset($language, 'Not Now'); ?></button>
                            <button type="button" class="btn btn-primary btn-opportunitiesassignst"><?php echo check_isset($language, 'Assign Lead'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="./assets/bundles/lib.vendor.bundle.js"></script>
            <script>
                /*!
                 * jQuery UI Touch Punch 0.2.3
                 *
                 * Copyright 2011–2014, Dave Furfero
                 * Dual licensed under the MIT or GPL Version 2 licenses.
                 *
                 * Depends:
                 *  jquery.ui.widget.js
                 *  jquery.ui.mouse.js
                 */
                ! function(a) {
                    function f(a, b) {
                        if (!(a.originalEvent.touches.length > 1)) {
                            a.preventDefault();
                            var c = a.originalEvent.changedTouches[0],
                                d = document.createEvent("MouseEvents");
                            d.initMouseEvent(b, !0, !0, window, 1, c.screenX, c.screenY, c.clientX, c.clientY, !1, !1, !1, !1, 0, null), a.target.dispatchEvent(d)
                        }
                    }
                    if (a.support.touch = "ontouchend" in document, a.support.touch) {
                        var e, b = a.ui.mouse.prototype,
                            c = b._mouseInit,
                            d = b._mouseDestroy;
                        b._touchStart = function(a) {
                            var b = this;
                            !e && b._mouseCapture(a.originalEvent.changedTouches[0]) && (e = !0, b._touchMoved = !1, f(a, "mouseover"), f(a, "mousemove"), f(a, "mousedown"))
                        }, b._touchMove = function(a) {
                            e && (this._touchMoved = !0, f(a, "mousemove"))
                        }, b._touchEnd = function(a) {
                            e && (f(a, "mouseup"), f(a, "mouseout"), this._touchMoved || f(a, "click"), e = !1)
                        }, b._mouseInit = function() {
                            var b = this;
                            b.element.bind({
                                touchstart: a.proxy(b, "_touchStart"),
                                touchmove: a.proxy(b, "_touchMove"),
                                touchend: a.proxy(b, "_touchEnd")
                            }), c.call(b)
                        }, b._mouseDestroy = function() {
                            var b = this;
                            b.element.unbind({
                                touchstart: a.proxy(b, "_touchStart"),
                                touchmove: a.proxy(b, "_touchMove"),
                                touchend: a.proxy(b, "_touchEnd")
                            }), d.call(b)
                        }
                    }
                }(jQuery);
            </script>
            <script src="./assets/bundles/apexcharts.bundle.js"></script>
            <script src="./assets/bundles/counterup.bundle.js"></script>
            <script src="./assets/plugins/dropify/js/dropify.min.js"></script>
            <script src="./assets/bundles/c3.bundle.js"></script>
            <script src="./assets/bundles/jvectormap1.bundle.js"></script>
            <script src="./assets/plugins/jquery-steps/jquery.steps.js"></script>
            <script src="./assets/bundles/sweetalert.bundle.js"></script>
            <script src="./assets/js/core.js"></script>
            <script src="./assets/js/form/form-advanced.js"></script>
            <script src="assets/js/table/datatable.js"></script>
            <script src="assets/js/page/index.js"></script>
            <script src="assets/js/page/sweetalert.js"></script>
            <script src="assets/js/bootstrap-select.min.js"></script>
            <?php include 'assets/script_js/all_opportunities_js.php'; ?>
            <?php include 'assets/script_js/all_oppstages_js.php'; ?>
            <?php include 'assets/script_js/dateTime_inBetween.js.php'; ?>

            <script src="assets/js/import.js"></script>
            <script src="assets/js/jquery-ui.js"></script>

            <?php include "include/footer.php"; ?>
            <?php include "settings_script.php"; ?>
            <script>
                $(document).ready(function() {
                    $(".ui-sortable-helper").parent().css("z-index", "2");
                });
            </script>
</body>

</html>
