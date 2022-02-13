(function ($) {

    $.payoneDirectDebit = $.payoneDirectDebit || {};
    $.payoneDirectDebit.iframe = null;
    $.payoneDirectDebit.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };
    /**
     * @param form
     */
    $.payoneDirectDebit.storeAccountDataForReinit = function (form, orderId) {
        console.log('reinit with order id ')
        return $.ajax({
            type: 'POST',
            url: '/payment/payone/checkout/storeAccountDataForReinit/' + orderId,
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
                    console.log(data);
                }
            );

    };

    /**
     * @param form
     */
    $.payoneDirectDebit.storeAccountData = function (form) {
        console.log('normal flow ')
        return $.ajax({
            type: 'POST',
            url: '/payment/payone/checkout/storeAccountData',
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
                    console.log(data);
                }
            );

    };

    $.payoneDirectDebit.showSepaMandate = function () {
        return $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/payment/payone/checkout/getSepaMandateStep'
        })
            .done(function (data) {
                $(data.data.html).insertAfter('#createSepamandate');
                $('#sepaMandateConfirmation').show();

            }).fail(function (data) {
                    var data = data.responseJSON;
                    if (data.errors && data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    console.log(data);
                }
            );
    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#createSepamandate').hide();
    };

    window.sepaForm = function(event, orderId) {

        console.log('submit button clicked now');
        event.preventDefault();

        $('#sepaContinue').prop('disabled', true);

        var form = $('#createSepamandateForm');
        console.log('storing account data');
        console.log(orderId)
        if(orderId) {
            $.when($.payoneDirectDebit.storeAccountDataForReinit(form, orderId)).done(function () {
                console.log('submitting orderPlaceForm');

                $.payoneDirectDebit.hideAccountForm();
                $.payoneDirectDebit.showSepaMandate(form);

            }).fail(function (data, textStatus, jqXHR) {
                return false;
            });
        }else {
            $.when($.payoneDirectDebit.storeAccountData(form)).done(function () {
                console.log('submitting orderPlaceForm');

                $.payoneDirectDebit.hideAccountForm();
                $.payoneDirectDebit.showSepaMandate(form);

            }).fail(function (data, textStatus, jqXHR) {
                return false;
            });
        }
        return false;
    }
}(window.jQuery, window, document));
