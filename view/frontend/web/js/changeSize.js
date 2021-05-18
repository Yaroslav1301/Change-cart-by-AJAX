require(
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
        $('tbody').each(function () {
            var id = $(this).closest('tbody').find('input').val();
            var qty = $(this).find("td.col.qty").find('input').val();
            $('#' + id + 'size' + ' div').bind('click', function () {
                var selectedId = $('#' + id + 'size' + ' div').index(this);
                $('#' + id + 'size' + ' div.selected').removeClass('selected');
                $(this).addClass('selected');
                var selectedSize = $(this).text();
                $.ajax({
                    url: "http://magento.loc/change/index/index",
                    type: "POST",
                    data: {'sku' : id, 'selectedSize': selectedSize, 'qty' : qty},
                    showLoader: true,
                    cache: false,
                    /*success: function (res) {
                        var sections = ['cart'];
                        customerData.reload(sections, true);

                        /!* Totals summary reloading *!/
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                    }*/
                });
                window.location.reload(true);
            });
        })
    }
);
