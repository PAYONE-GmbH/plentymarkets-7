(function ($) {

    $.payoneDirectDebit = $.payoneDirectDebit || {};
    $.payoneDirectDebit.iframe = null;
    $.payoneDirectDebit.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };
    /**
     * @param form
     * @param orderId
     * @param trailingSlash
     */
    $.payoneDirectDebit.storeAccountDataForReinit = function (form, orderId, trailingSlash = '') {
        return $.ajax({
            type: 'POST',
            url: '/payment/payone/checkout/storeAccountDataForReinit/' + orderId + trailingSlash,
            data: form.serialize(),
            dataType: 'json',
            async: true
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
            }).fail(function (data) {
                    var data = data.responseJSON;
                    if (data.errors && data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                }
            );

    };

    /**
     * @param form
     * @param trailingSlash
     */
    $.payoneDirectDebit.storeAccountData = function (form, trailingSlash = '') {
        return $.ajax({
            type: 'POST',
            url: '/payment/payone/checkout/storeAccountData' + trailingSlash,
            data: form.serialize(),
            dataType: 'json',
            async: true
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
            }).fail(function (data) {
                    var data = data.responseJSON;
                    if (data.errors && data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                }
            );
    };

    /**
     * @param trailingSlash
     */
    $.payoneDirectDebit.showSepaMandate = function (trailingSlash = '') {
        return $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/payment/payone/checkout/getSepaMandateStep' + trailingSlash
        })
            .done(function (data) {
                $(data.data.html).insertAfter('#createSepamandate');
                $('#sepaMandateConfirmation').show();
            }).fail(function (data) {
                    var data = data.responseJSON;
                    if (data.errors && data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                }
            );
    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#createSepamandate').hide();
    };

    window.sepaForm = function(event, orderId, trailingSlash = '') {
        event.preventDefault();
        $('#sepaContinue').prop('disabled', true);

        var form = $('#createSepamandateForm');
        if(orderId) {
            $.when($.payoneDirectDebit.storeAccountDataForReinit(form, orderId, trailingSlash)).done(function () {
                $.payoneDirectDebit.hideAccountForm();
                $.payoneDirectDebit.showSepaMandate(trailingSlash);
            }).fail(function (data, textStatus, jqXHR) {
                return false;
            });
        }else {
            $.when($.payoneDirectDebit.storeAccountData(form, trailingSlash)).done(function () {
                $.payoneDirectDebit.hideAccountForm();
                $.payoneDirectDebit.showSepaMandate(trailingSlash);
            }).fail(function (data, textStatus, jqXHR) {
                return false;
            });
        }
        return false;
    }
}(window.jQuery, window, document));
