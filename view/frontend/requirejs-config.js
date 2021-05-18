var config = {
    map: {
        '*': {
            ajaxQty: 'Kozar_UpdateCard/js/cartQtyUpdate',
        }
    },
    paths: {
        'changeColor': "Kozar_UpdateCard/js/changeColor",
        'changeSize': 'Kozar_UpdateCard/js/changeSize'
    },
    shim: {
        'changeColor': {
            deps: ['jquery']
        }
    }
}

