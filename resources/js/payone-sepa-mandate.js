(function ($) {
    $(function () {
        var submitted = false;

        window.setCheckout = function(event) {
            event.stopPropagation();
            var isDisabled = ($('#sepaMandateConfirmation input[type="checkbox"]').length !== $('#sepaMandateConfirmation input[type="checkbox"]:checked').length);
            $.payonePayment.setCheckoutDisabled(isDisabled);
        }

        window.sepaOrder = function(event) {
            console.log('submitting orderPlaceForm for sepa');
            event.preventDefault();

            var termsCheckboxes = $('#sepaMandateConfirmation input[type="checkbox"]');
            termsCheckboxes.prop('disabled', true);
            $.payonePayment.setCheckoutDisabled(true);

            var form = $(this);
            $.when($.payonePayment.doAuth(form)).done(function () {

                submitted = true;
                console.log(form);
                form.unbind('submit');
                form.submit();
            }).fail(function (data, textStatus, jqXHR) {
                return false;
            });
        }
    });
}(window.jQuery, window, document));