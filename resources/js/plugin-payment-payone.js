(function ($) {

    $.payolution = $.payolution || {};

    $.payolution.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    /**
     *
     * @param form
     * @returns {boolean}
     */
    $.payolution.storeAccountData = function (form) {
        var success = false;

        $.ajax({
            type: 'POST',
            url: '/payolution/checkout/storeAccountData',
            data: form.serialize(),
            dataType: 'json',
            async: false
        })
            .done(function (data) {
                var errorClasses = 'has-error error has-feedback';
                form.find('input, select').parent().removeClass(errorClasses);
                success = true;
                if (!data.success) {
                    $.payolution.showValidationErrors(form, data.errors, errorClasses);
                    if (data.errors.message) {
                        $.payolution.showErrorMessage(data.errors.message);
                    }
                    form.unbind('submit');
                    console.log(data);
                    success = false;
                }
            }).fail(function (data, textStatus, jqXHR) {
            console.log(jqXHR);
            $.payolution.showErrorMessage(jqXHR.responseText);
            form.unbind('submit');
            success = false;
        });

        return success;
    };

    $.payolution.doPreAuth = function (form) {

        return $.ajax({
            type: 'POST',
            url: '/payolution/checkout/doPreAuth',
            data: '',
            dataType: 'json',
            async: true
        }).done(function (data) {
            if (!data.success) {
                if (data.errors.message) {
                    console.log('done with errors');
                    console.log(data.errors.message);
                    $.payolution.showErrorMessage(data.errors.message);
                }
                form.unbind('submit');
                success = false;
            }
            console.log('done');
            console.log(data);
        }).fail(function (data, textStatus, jqXHR) {
            console.log('fail');
            console.log(jqXHR);
            console.log(jqXHR.responseText);
            form.unbind('submit');
            $.payolution.showErrorMessage(jqXHR.responseText);
        });
    };

    $.payolution.doPreCheck = function (form) {

        return $.ajax({
            type: 'POST',
            url: '/payolution/checkout/doPreCheck',
            data: '',
            dataType: 'json',
            async: true
        }).done(function (data) {
            if (!data.success) {
                if (data.errors.message) {
                    console.log('done with errors');
                    console.log(data.errors.message);
                    $.payolution.showErrorMessage(data.errors.message);
                }
                form.unbind('submit');
                success = false;
            }
            console.log('done');
            console.log(data);
        }).fail(function (data, textStatus, jqXHR) {
            console.log('fail');
            console.log(jqXHR);
            console.log(jqXHR.responseText);
            form.unbind('submit');
            $.payolution.showErrorMessage(jqXHR.responseText);
        });
    };

    $.payolution.showValidationErrors = function (form, errors, errorClasses) {
        for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
                form.find('[name="' + key + '"]').parent().addClass(errorClasses);
            }
        }
    };

    $.payolution.renderTemplate = function (template, values) {
        var re = new RegExp('\\{(' + Object.keys(values).join('|') + ')\\}', 'g');
        return template.replace(re, function (a, b) {
            return values[b];
        });
    };

    $.payolution.showErrorMessage = function (message) {
        $('#checkoutError').remove();
        var content = $.payolution.renderTemplate(Templates.errorMessage, {errorMessage: message});
        $(content).insertBefore('#payolutionPaymentModal');
    };

    $.payolution.showSpinner = function () {
        $('#payolutionSpinner').show();
    };

    $.payolution.hideSpinner = function () {
        $('#payolutionSpinner').hide();
    };

    $.payolution.resetForm = function (termsCheckboxes) {
        $.payolution.setCheckoutDisabled(false);
        termsCheckboxes.prop('disabled', false);
        $.payolution.hideSpinner();
    };

    $(function () {
        $('.payolutionIns-tac:input[type="checkbox"]').change(function (event) {
            event.stopPropagation();
            var isDisabled = ($('.payolutionIns-tac:input[type="checkbox"]').length !== $('.payolutionIns-tac:input[type="checkbox"]:checked').length);
            $.payolution.setCheckoutDisabled(isDisabled);
        });

        var submitted = false;
        $('#orderPlaceForm').on("submit", function (event) {
            event.preventDefault();
            if (submitted) {
                return false;
            }
            var form = $(this);
            console.log('submitting orderPlaceForm');

            var termsCheckboxes = $('.payolutionIns-tac:input[type="checkbox"]');
            $.payolution.showSpinner();
            $.payolution.setCheckoutDisabled(true);

            termsCheckboxes.prop('disabled', true);

            var $accountDataStored = $.payolution.storeAccountData(form);

            if (!$accountDataStored) {
                $.payolution.resetForm(termsCheckboxes);
                $(this).unbind(event);
                return false;
            }
            form = this;
            $.when($.payolution.doPreAuth(form)).done(function () {
                submitted = true;
                console.log(form);
                form.submit();
            }).fail(function (data, textStatus, jqXHR) {
                $.payolution.showErrorMessage(jqXHR.responseText);
                $.payolution.resetForm(termsCheckboxes);
                $(this).unbind(event);
            });
        });
        $(document).on('click', '.payolution-cancel button', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });
    });
}(window.jQuery, window, document));
