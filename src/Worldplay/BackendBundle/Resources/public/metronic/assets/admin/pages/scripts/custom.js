/**
 Custom module for you to write your own javascript functions
 **/
var Custom = function () {

    var initModals = function () {

        var modalDelete = $('#modal-delete');

        $('.btn-delete').on('click', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');
            var href = $(this).data('href');

            modalDelete.find('[data-id]').text(id);
            modalDelete.find('[data-name]').text(name);
            modalDelete.find('form').attr('action', href);
            modalDelete.modal('show');
        });

    };

    var initForm = function () {
        $("select.customSelect").select2();
    };

    var initCustomTable = function () {

        var table = $('#customDataTable');

        // begin first table
        table.dataTable({
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.7/i18n/Portuguese-Brasil.json'
            },
            "aaSorting": []

        });

        var tableWrapper = jQuery('#sample_1_wrapper');

        table.find('.group-checkable').change(function () {
            var set = jQuery(this).attr("data-set");
            var checked = jQuery(this).is(":checked");
            jQuery(set).each(function () {
                if (checked) {
                    $(this).attr("checked", true);
                    $(this).parents('tr').addClass("active");
                } else {
                    $(this).attr("checked", false);
                    $(this).parents('tr').removeClass("active");
                }
            });
            jQuery.uniform.update(set);
        });

        table.on('change', 'tbody tr .checkboxes', function () {
            $(this).parents('tr').toggleClass("active");
        });

        tableWrapper.find('.dataTables_length select').addClass("form-control input-xsmall input-inline"); // modify table per page dropdown
    };

    var initReportTable = function () {

        var table = $('#reportDataTable');

        table.dataTable({
            "language": {
                url: '//cdn.datatables.net/plug-ins/1.10.7/i18n/Portuguese-Brasil.json',
            },
            "pageLength": 30,
            "bFilter": false,
            "bLengthChange": false,
        });

    };

    var handleInputMasks = function () {
        $.extend($.inputmask.defaults, {
            'autounmask': true
        });

        $('.mask_balance').inputmask('999.999.999,99', {
            allowPlus: false,
            allowMinus: true,
            numericInput: true
        });

        $(".numeric").inputmask("numeric", {
            allowPlus: false,
            allowMinus: false
        });

        $(".mask_currency").inputmask('999.999.999,99', {
            numericInput: true
        });

        $(".mask_currency").on("focus", function () {
            $(this)
                .one('mouseup', function () {
                    $(this).select();
                    return false;
                })
                .select();
        });

        $(".mask_balance").on("focus", function () {
            $(this)
                .one('mouseup', function () {
                    $(this).select();
                    return false;
                })
                .select();
        });

        $(".mask_cellphone").inputmask({mask: "(99) 9999-9999[9]", greedy: false});

        $(".mask_zipcode").inputmask(
            "numeric",
            {
                mask: "99999-999",
                rightAlign: false,
                onUnMask: function (maskedValue, unmaskedValue) {
                    //do something with the value
                    return unmaskedValue;
                }
            });

    };

    return {

        init: function () {
            if (!jQuery().dataTable) {
                return;
            }

            initForm();
            initModals();
            initCustomTable();
            initReportTable();
            handleInputMasks();
        },

        //some helper function
        doSomeStuff: function () {
            //initTable();
        }

    };

}();