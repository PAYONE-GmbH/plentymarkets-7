(function ($) {

    $.payonePayment = $.payonePayment || {};
    $.payonePayment.iframe = null;
    $.payonePayment.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    $.payonePayment.doAuth = function () {
        return $.ajax({
            type: 'POST',
            url: '/payone/checkout/doAuth',
            data: '',
            dataType: 'json',
            async: true
        }).done(function (data) {
            if (data.data.redirecturl) {
                window.location.replace(data.data.redirecturl);
            }
            console.log('done');
            console.log(data);
        }).fail(function (data) {
            var data = data.responseJSON;
            if (data.errors && data.errors.message) {
                $.payonePayment.showErrorMessage(data.errors.message);
            }
            console.log(data);
            console.log(data);
        });
    };

    $.payonePayment.showValidationErrors = function (form, errors, errorClasses) {
        for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
                form.find('[name="' + key + '"]').parent().addClass(errorClasses);
            }
        }
    };

    $.payonePayment.showErrorMessage = function (message) {
        $('#checkoutError').remove();
        var content = $.payonePayment.renderTemplate(Templates.errorMessage, {errorMessage: message});
        $(content).insertBefore('#payonePaymentModal');
    };

    $.payonePayment.renderTemplate = function (template, values) {
        var re = new RegExp('\\{(' + Object.keys(values).join('|') + ')\\}', 'g');
        return template.replace(re, function (a, b) {
            return values[b];
        });
    };

}(window.jQuery, window, document));
