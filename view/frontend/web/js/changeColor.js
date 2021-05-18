require(
    [
        'jquery'
    ],
    function (
        $
    ) {

        /* $.ajax({
             url: "http://magento.loc/buy/oneclick/product",
             type: "POST",
             data: {"number": number, "name": name, "email": email, "qty": qty, "selected": selected, "sku": sku},
             showLoader: true,
             cache: false,
         })
         clearModal();
         this.closeModal();});*/
        $('tbody').each(function () {
            var id = $(this).closest('tbody').find('input').val();
            $('#' + id + ' div').bind('click', function () {
                var selectedId = $('#' + id + ' div').index(this);
                $('#' + id + ' div.selected').removeClass('selected');
                $(this).addClass('selected');
            });
        })

    }
);
