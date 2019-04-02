window.onload = function () {
    Worldpay.useTemplateForm({
        'clientKey': 'T_C_5a5c185b-ef1d-424d-9978-b3d0f929183a',
        'saveButton': false,
        'form': 'paymentForm',
        'paymentSection': 'myPaymentSection',
        'display': 'inline',
        'reusable': true,
        'type': 'card',
        'callback': function (obj) {
            if (obj && obj.token) {
                var _el = document.createElement('input');
                _el.value = obj.token;
                _el.type = 'hidden';
                _el.name = 'token';
                document.getElementById('paymentForm').appendChild(_el);
                document.getElementById('paymentForm').submit();
            }
        }
    });
}