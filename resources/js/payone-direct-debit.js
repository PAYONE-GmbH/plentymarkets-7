(function ($) {

    $.payoneDirectDebit = $.payoneDirectDebit || {};
    $.payoneDirectDebit.iframe = null;
    $.payoneDirectDebit.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    /**
     * @param form
     */
    $.payoneDirectDebit.storeAccountData = function (form) {
        var success = false;

        $.ajax({
            type: 'POST',
            url: '/payone/checkout/storeAccountData',
            data: form.serialize(),
            dataType: 'json',
            async: false
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
                success = true;
                if (!data.success) {
                    $.payonePayment.showValidationErrors(form, data.errors, errorClasses);
                    if (data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    form.unbind('submit');
                    console.log(data);
                    success = false;
                }
            });
    };

    $.payoneDirectDebit.showSepaMandate = function (form) {
        $.ajax({
            type: 'POST',
            url: '/payone/checkout/getSepaMandateStep',
            data: form.serialize(),
            dataType: 'json',
        })
            .done(function (data) {
                if (!data.success) {
                    if (data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    console.log(data);
                }
                $('#payonePaymentModal').append(data.html).show();
            })
            .fail(function (data) {
                console.log(data);
                form.unbind('submit');
            });

    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#payonePaymentModal').hide();
    };

    $(function () {
        $('#orderPlaceForm').on("submit", function (event) {
            event.preventDefault();

            $.payonePayment.setCheckoutDisabled(true);

        });
        $(document).on('click', 'button.payone-cancel', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });
        var form = $('#orderPlaceForm');
        var $accountDataStored = $.payolution.storeAccountData(form);
        if (!$accountDataStored) {
            return false;
        }
        console.log('submitting orderPlaceForm');
        $.when($.payonePayment.doAuth(form)).done(function (data) {
            $.payoneDirectDebit.showSepaMandate();
            $.payoneDirectDebit.hideAccountForm();
            submitted = true;
            console.log(form);
            form.unbind('submit');
            form.submit();
        }).fail(function (data, textStatus, jqXHR) {
            $.payonePayment.showErrorMessage(jqXHR.responseText);
            form.unbind('submit');
        });
    });
    
}(window.jQuery, window, document));
