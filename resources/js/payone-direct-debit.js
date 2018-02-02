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

        return success;
    };

    $.payoneDirectDebit.showSepaMandate = function () {
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: '/payone/checkout/getSepaMandateStep',
        })
            .done(function (data) {
                if (!data.success) {
                    if (data.errors.message) {
                        $.payonePayment.showErrorMessage(data.errors.message);
                    }
                    console.log(data);
                }
                $('#payonePaymentModal').append(data.data.html).show();
            })
            .fail(function (data) {
                console.log(data);
            });
    };

    $.payoneDirectDebit.hideAccountForm = function () {
        $('#payonePaymentModal').hide();
    };

    $(function () {

        $('#sepaMandateConfirmation:input[type="checkbox"]').change(function (event) {
            event.stopPropagation();
            var isDisabled = ($('#sepaMandateConfirmation:input[type="checkbox"]').length !== $('#sepaMandateConfirmation:input[type="checkbox"]:checked').length);
            $.payonePayment.setCheckoutDisabled(isDisabled);
        });

        var submitted = false;
        $('#orderPlaceForm').on("submit", function (event) {
            console.log('submitting orderPlaceForm for sepa');
            event.preventDefault();

            var termsCheckboxes = $('#sepaMandateConfirmation:input[type="checkbox"]');
            termsCheckboxes.prop('disabled', true);
            $.payonePayment.setCheckoutDisabled(true);


            var form = $(this);
            $.when($.payonePayment.doAuth(form)).done(function (data) {

                submitted = true;
                console.log(form);
                form.unbind('submit');
                form.submit();
            }).fail(function (data, textStatus, jqXHR) {
                $.payonePayment.showErrorMessage(jqXHR.responseText);
                return false;
            });

        });

        $('#createSepamandateForm').on("submit", function (event) {
            console.log('submit button clicked');
            event.preventDefault();

            $('#sepaContinue').prop('disabled', true);

            var form = $('#createSepamandateForm');
            console.log('storing account data');

            $.when($.payoneDirectDebit.storeAccountData(form)).done(function (data) {
                console.log('submitting orderPlaceForm');

                $.payoneDirectDebit.hideAccountForm();
                $.payoneDirectDebit.showSepaMandate(form);

            }).fail(function (data, textStatus, jqXHR) {
                $.payonePayment.setCheckoutDisabled(false);
                return false;
            });

        });

        $(document).on('click', 'button.payone-cancel', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });

    });

}(window.jQuery, window, document));
