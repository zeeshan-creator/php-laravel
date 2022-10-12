<script>
    $(document).ready(function() {

        <?php if ((isset($db_fields['leads_export'])) || $auth_role_type == 'super_admin') { ?>
            $('body').on('click', '.export_opportunities', function() {
                $('#main_content').addClass('blur');
                $('body').addClass('overflow');
                $("#export_data").show();
            });
        <?php } ?>
        <?php if ((isset($db_fields['leads_import'])) || $auth_role_type == 'super_admin') { ?>
            $('body').on('click', '.import_opportunities', function() {
                $('#main_content').addClass('blur');
                $('body').addClass('overflow');
                $("#import_data").show();
            });
        <?php } ?>
        <?php if (isset($_GET['import']) || isset($_GET['final_import']) || isset($_GET['import_complete'])) { ?>
            $('#main_content').addClass('blur');
            $('body').addClass('overflow');
        <?php } ?>

        <?php if (isset($_GET['step']) && $_GET['step'] == 1) { ?>
            $('.import_opportunities').trigger('click');
        <?php } ?>

        $('.avdt').on('click', function() {
            $('.vdt').toggle();
        });

        $('.grps .btn-outline-danger').on('click', function() {
            window.location.href = 'all_opportunities.php';
        });

        $('.grps .btn-default').on('click', function() {
            window.location.href = 'all_opportunities.php?step=1';
        });
        $('body').on('click', '.close_popup, .close_popup_', function() {
            $('#main_content').removeClass('blur');
            $('body').removeClass('overflow');
            $(this).closest(".popup").hide();
        });
        $('body').on('change', 'input[name="export_all_data"]', function() {
            var export_data_value = $(this).val();
            console.log(export_data_value);
            if (export_data_value == 2) {
                $(".select_fields_export").show();
            } else {
                $(".select_fields_export").hide();
            }
        });
        $('body').on('click', '.drag_and_drop_select div', function() {
            $(this).closest(".drag_and_drop_select").find("div").removeClass("active");
            $(this).addClass("active");
        });

        $('body').on('click', '.fields_to_be_matched_select div', function() {
            $(this).closest(".fields_to_be_matched_select").find("div").removeClass("active");
            $(this).addClass("active");
        });

        $('body').on('click', '.move_to_right', function() {
            if ($(".drag_and_drop_select .active")[0]) {
                var move_to_right_text = $(".drag_and_drop_select .active").text();
                $(".fields_to_be_matched_select").append("<div>" + move_to_right_text + "</div>");
                $(".drag_and_drop_select .active").remove();
                $('input[name="fields_to_be_matched"]').val("test");
                var fields_input = '';
                $('.fields_to_be_matched_select div').each(function(index, element) {
                    fields_input = fields_input + "," + $(element).text();
                });
                $('input[name="fields_to_be_matched"]').val(fields_input);
            }
        });
        $('body').on('click', '.move_to_left', function() {
            if ($(".fields_to_be_matched_select .active")[0]) {
                var move_to_right_text = $(".fields_to_be_matched_select .active").text();
                $(".drag_and_drop_select").append("<div>" + move_to_right_text + "</div>");
                $(".fields_to_be_matched_select .active").remove();
                var fields_input = '';
                $('.fields_to_be_matched_select div').each(function(index, element) {
                    fields_input = fields_input + "," + $(element).text();
                });
                $('input[name="fields_to_be_matched"]').val(fields_input);
            }
        });
        $('.selectpicker').selectpicker();
        $('.bs-searchbox').find("input").attr("placeholder", "Search...");
        row = 0;
        $('.add-col').click(function(e) {
            let indexOfForm = $(".selectpicker.first-part").length + 1;

            row++;
            e.preventDefault();
            $.ajax({
                method: 'POST',
                url: "create_dd_opportunities.php",
                data: {
                    create_dd: "create_dd",
                    'row': row,
                    indexOfForm: indexOfForm
                },
                success: function(data) {
                    $('.col-filter').append(data);
                    $('.selectpicker').selectpicker();
                    showAndHideInBetween();
                }

            })

        });

        $(document).on('click', '.flb', function() {
            var t = $(this);
            var row = t.data('row');
            $('.fl' + row).remove();
        });

        $(document).on('click', '.btn-fl-rec', function() {
            if ($('input[name=search_name_field_opportunities]').val() != '') {
                <?php if ($auth_role_type == 'user') { ?>
                    $('#form_filter input[name=save-filter-records_opportunities]').val('query');
                    $('#form_filter')[0].submit();
                <?php } elseif ($auth_role_type == 'admin' || $auth_role_type == 'super_admin') { ?>
                    $('#shareSearch').modal('show');
                <?php } ?>
            } else {
                alert('Please add search name first!');
            }
        });

        $('.spsr-shr .custom-control-input').on('click', function() {
            var t = $(this);
            var _v = t.val();
            if (_v == 'specific') {
                $('.spsf_user').show();
                $('.spsf_role').hide();
            } else if (_v == 'role') {
                $('.spsf_user').hide();
                $('.c2_own').html('');
                $('#spsr-user').val('');
                $('.spsf_role').show();
            } else {
                $('.spsf_user').hide();
                $('.c2_own').html('');
                $('#spsr-user').val('');
                $('.spsf_role').hide();
            }
        });

        $('.spsr-user').on('click', function() {
            var t = $(this);
            var _v = $('#spsr-user').val();
            var _i = t.html();
            var _c = $('.c2_own');
            if (_v != '') {
                t.html('<i class="fa fa-spinner fa-pulse"></i>');
                var data = {
                    'query': _v,
                    'userType': 'typeSearch',
                    'getType': 'typeSearch1'
                }
                $.ajax({
                    type: 'POST',
                    url: './core/ajax/get_users.php',
                    data: data,
                    dataType: 'json',
                    success: function(res) {
                        t.html(_i);
                        _c.html('<div class="resusers">' + res.users + '</div>');

                    }
                });
            }
        });

        $('.btn-srsh').on('click', function() {
            var _i = $('.spsr-shr .custom-control-input:checked').val();
            var _v = '';
            if (_i == 'specific') {
                var _sr_check = $(document).find('.sr-check');
                var _v = [];
                var _do = false;
                _sr_check.each(function() {
                    var t = $(this);
                    if (t.is(':checked')) {
                        _v.push(t.val());
                        _do = true;
                    }
                });
                if (!_do) {
                    alert('Please saearch or select user(s) to continue!');
                    return false;
                }

            }
            if (_i == 'role') {
                var _v = $('#spsr-role').val();
            }
            var text = _i + ':' + _v;
            $('#form_filter input[name=save-filter-records_opportunities]').val(text);
            $('#form_filter')[0].submit();
        });





    });
</script>

<script>
    $(".kanbanbtn").hide();
    $(document).ready(function() {
        $("#list-tab").click(function() {
            $("#gridcol").show();
            $("#gridcol").addClass("listview");
            $("#kanban").hide();
            $(".kanbanbtn").hide();
        })
        $("#grid-tab").click(function() {
            $("#gridcol").show();
            $("#gridcol").removeClass("listview");
            $("#kanban").hide();
            $(".kanbanbtn").hide();
        })
        $("#kanban-tab").click(function() {
            $("#gridcol").hide();
            $("#kanban").show();
            $(".kanbanbtn").show();
        })

    })
</script>
