define(
    [
        'jquery',
        'Magento_Checkout/js/action/get-totals',
        'Magento_Customer/js/customer-data'
    ],
    function (
        $,
        getTotalsAction,
        customerData
    ) {
        function changeSelectedSize()
        {
            $('tbody').each(function () {
                var id = $(this).closest('tbody').find('input').val();
                var qty = $(this).find("td.col.qty").find('input').val();
                $('#' + id + 'size' + ' div').bind('click', function () {
                    $('#' + id + 'size' + ' div.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedSize = $(this).text();
                    $.ajax({
                        url: "http://magento.loc/change/index/index",
                        type: "POST",
                        data: {'sku': id, 'selectedColor': "", 'selectedSize': selectedSize, 'qty': qty},
                        showLoader: true,
                        cache: false,
                        success: function (res) {
                            var form = $('form#form-validate');
                            $.ajax({
                                url: form.attr('action'),
                                data: form.serialize(),
                                showLoader: true,
                                success: function (res) {
                                    var parsedResponse = $.parseHTML(res);
                                    var result = $(parsedResponse).find("#form-validate");


                                    $("#form-validate").replaceWith(result);
                                    changeSelectedColor();
                                    changeSelectedSize();
                                    removeItem();
                                    setCoupon();
                                    /* Minicart reloading */
                                    var sections = ['cart'];
                                    customerData.invalidate(sections);
                                    customerData.reload(sections, true);

                                    /* Totals summary reloading */
                                    var deferred = $.Deferred();
                                    getTotalsAction([], deferred);
                                },
                                error: function (xhr, status, error) {
                                    var err = eval("(" + xhr.responseText + ")");
                                    console.log(err.Message);
                                }
                            });
                        }
                    });
                });
            })
        }
        function changeSelectedColor()
        {
            $('tbody').each(function () {
                var id = $(this).closest('tbody').find('input').val();
                var qty = $(this).find("td.col.qty").find('input').val();
                $('#' + id + ' div').bind('click', function () {
                    var selectedId = $('#' + id + ' div').index(this);
                    $('#' + id + ' div.selected').removeClass('selected');
                    $(this).addClass('selected');
                    var selectedColor = $(this).data('color');
                    $.ajax({
                        url: "http://magento.loc/change/index/index",
                        type: "POST",
                        data: {'sku': id, 'selectedColor': selectedColor, 'selectedSize': "", 'qty': qty},
                        showLoader: true,
                        cache: false,
                        success: function (res) {
                            var form = $('form#form-validate');
                            $.ajax({
                                url: form.attr('action'),
                                data: form.serialize(),
                                showLoader: true,
                                success: function (res) {
                                    var parsedResponse = $.parseHTML(res);
                                    var result = $(parsedResponse).find("#form-validate");


                                    $("#form-validate").replaceWith(result);
                                    changeSelectedColor();
                                    changeSelectedSize();
                                    removeItem();
                                    setCoupon();
                                    /* Minicart reloading */
                                    var sections = ['cart'];
                                    customerData.invalidate(sections);
                                    customerData.reload(sections, true);

                                    /* Totals summary reloading */
                                    var deferred = $.Deferred();
                                    getTotalsAction([], deferred);
                                },
                                error: function (xhr, status, error) {
                                    var err = eval("(" + xhr.responseText + ")");
                                    console.log(err.Message);
                                }
                            });
                        }
                    });
                });
            })
        }
        function removeItem()
        {
            $('tbody').each(function () {
                $('.action.action-delete').click(function (event) {
                    var data = this.dataset.post;
                    this.dataset.post = "";
                    $.ajax({
                        url: "http://magento.loc/change/index/delete",
                        type: "POST",
                        data: {'data': data},
                        showLoader: true,
                        cache: false,
                        success: function (res) {
                            var form = $('form#form-validate');
                            $.ajax({
                                url: form.attr('action'),
                                data: form.serialize(),
                                showLoader: true,
                                success: function (res) {
                                    var parsedResponse = $.parseHTML(res);
                                    var result = $(parsedResponse).find("#form-validate");


                                    $("#form-validate").replaceWith(result);
                                    changeSelectedColor();
                                    changeSelectedSize();
                                    removeItem();
                                    setCoupon();
                                    /* Minicart reloading */
                                    var sections = ['cart'];
                                    customerData.invalidate(sections);
                                    customerData.reload(sections, true);

                                    /* Totals summary reloading */
                                    var deferred = $.Deferred();
                                    getTotalsAction([], deferred);
                                },
                                error: function (xhr, status, error) {
                                    var err = eval("(" + xhr.responseText + ")");
                                    console.log(err.Message);
                                }
                            });
                        }
                    });
                });
            })
        }

        function setCoupon()
        {
            var form = $('#discount-coupon-form');
            var url = form.attr('action');
            var button = form.find('button');
            button.off();
            button.click(
                function (event) {
                    var coupon_code = $('#coupon_code').val();
                    var code = '1';
                    if ($(this).val() === 'Apply Discount') {
                        code = '0';
                    }
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: {'coupon_code': coupon_code, 'remove': code},
                        showLoader: true,
                        cache: false,
                        success: function () {
                            $.ajax({
                                context: '#block-discount',
                                url: 'http://magento.loc/change/index/coupon',
                                type: "POST",
                                data: {},
                            }).done(function (data) {
                                $('#block-discount').replaceWith(data.output);
                                    var form = $('form#form-validate');
                                    $.ajax({
                                        url: form.attr('action'),
                                        data: form.serialize(),
                                        showLoader: true,
                                        success: function (res) {
                                            var parsedResponse = $.parseHTML(res);
                                            var result = $(parsedResponse).find("#form-validate");

                                            $("#form-validate").replaceWith(result);
                                            changeSelectedColor();
                                            changeSelectedSize();
                                            removeItem();
                                            setCoupon();
                                            /* Minicart reloading */
                                            var sections = ['cart'];
                                            customerData.invalidate(sections);
                                            customerData.reload(sections, true);

                                            /* Totals summary reloading */
                                            var deferred = $.Deferred();
                                            getTotalsAction([], deferred);
                                        },
                                        error: function (xhr, status, error) {
                                            var err = eval("(" + xhr.responseText + ")");
                                            console.log(err.Message);
                                        }
                                    });

                            });
                        }
                    });
                }
            );
        }

        $(document).ready(function () {
            changeSelectedSize();
            changeSelectedColor();
            removeItem();
            setCoupon();

            $(document).on('change', 'input[name$="[qty]"]', function () {
                var form = $('form#form-validate');
                $.ajax({
                    url: form.attr('action'),
                    data: form.serialize(),
                    showLoader: true,
                    success: function (res) {
                        var parsedResponse = $.parseHTML(res);
                        var result = $(parsedResponse).find("#form-validate");
                        var sections = ['cart'];

                        $("#form-validate").replaceWith(result);
                        changeSelectedColor();
                        changeSelectedSize();
                        setCoupon();
                        removeItem();
                        /* Minicart reloading */
                        customerData.reload(sections, true);

                        /* Totals summary reloading */
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    },
                    error: function (xhr, status, error) {
                        var err = eval("(" + xhr.responseText + ")");
                        console.log(err.Message);
                    }
                });
            });
        });

    }
);
