(function ($) {
    $(function () {
        var submitted = false;

        window.setCheckout = function(event) {
            event.stopPropagation();
            var isDisabled = ($('#sepaMandateConfirmation input[type="checkbox"]').length !== $('#sepaMandateConfirmation input[type="checkbox"]:checked').length);
            $.payonePayment.setCheckoutDisabled(isDisabled);
        }

        window.sepaOrder = function(event, form, orderId, trailingSlash = '') {
            event.preventDefault();

            var termsCheckboxes = $('#sepaMandateConfirmation input[type="checkbox"]');
            termsCheckboxes.prop('disabled', true);
            $.payonePayment.setCheckoutDisabled(true);

            var form = $(form);

            if(orderId){
                $.when($.payonePayment.doAuthFromOrder(form, orderId, trailingSlash)).done(function () {

                    submitted = true;
                    form.removeAttr('onsubmit');
                    form.submit();
                }).fail(function (data, textStatus, jqXHR) {
                    return false;
                });
            }else {
                $.when($.payonePayment.doAuth(form, trailingSlash)).done(function () {

                    submitted = true;
                    form.removeAttr('onsubmit');
                    form.submit();
                }).fail(function (data, textStatus, jqXHR) {
                    return false;
                });
            }
        }
    });
}(window.jQuery, window, document));
