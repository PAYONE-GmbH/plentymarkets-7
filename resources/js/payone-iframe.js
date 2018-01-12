(function ($) {

    $.payoneIframe = $.payoneIframe || {};
    $.payoneIframe.iframe = null;
    $.payoneIframe.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    $.payoneIframe.check = function () { // Function called by submitting PAY-button
        if (iframes.isComplete()) {
            iframes.creditCardCheck('$.payoneIframe.checkCallback');// Perform "CreditCardCheck" to create and get a // PseudoCardPan; then call your function "checkCallback"
        } else {
            console.debug("not complete");
        }
    };

    $.payoneIframe.checkCallback = function (response) {
        console.debug(response);
        if (response.status === "VALID") {
            document.getElementById("pseudocardpan").value = response.pseudocardpan;
            document.getElementById("truncatedcardpan").value = response.truncatedcardpan;
        }
    };

    $.payoneIframe.getPayoneLocaleConfig = function (locale) {

        if (locale.indexOf('de') !== -1) {
            return Payone.ClientApi.Language.de;
        }
        return Payone.ClientApi.Language.en;
    };

    $.payoneIframe.createIframe = function (locale, request, config) {
        config.fields.language = $.payoneIframe.getPayoneLocaleConfig(locale);

        $.payoneIframe.iframe = new Payone.ClientApi.Hosted$.payoneIframe.iframe(config, request);
        return $.payoneIframe.iframe;
    };

    $.payoneIframe.doAuth = function (form) {

        return $.ajax({
            type: 'POST',
            url: '/payone/checkout/doAuth',
            data: '',
            dataType: 'json',
            async: true
        }).done(function (data) {
            if (!data.success) {
                if (data.errors.message) {
                    console.log('done with errors');
                    console.log(data.errors.message);
                    $.payoneIframe.showErrorMessage(data.errors.message);
                }
                form.unbind('submit');
                success = false;
            }
            console.log('done');
            console.log(data);
        });
    };

    $.payoneIframe.showValidationErrors = function (form, errors, errorClasses) {
        for (var key in errors) {
            if (errors.hasOwnProperty(key)) {
                form.find('[name="' + key + '"]').parent().addClass(errorClasses);
            }
        }
    };

    $.payoneIframe.showErrorMessage = function (message) {
        $('#checkoutError').remove();
        var content = $.payoneIframe.renderTemplate(Templates.errorMessage, {errorMessage: message});
        $(content).insertBefore('#payonePaymentModal');
    };

    $.payoneIframe.renderTemplate = function (template, values) {
        var re = new RegExp('\\{(' + Object.keys(values).join('|') + ')\\}', 'g');
        return template.replace(re, function (a, b) {
            return values[b];
        });
    };

    $(function () {
        $.payoneIframe.createIframe(Templates.locale, request, config);

        var submitted = false;
        $('#orderPlaceForm').on("submit", function (event) {
            event.preventDefault();
            if (submitted) {
                return false;
            }
            var form = $(this);
            console.log('submitting orderPlaceForm');

            $.payoneIframe.setCheckoutDisabled(true);

            form = this;

            $.payoneIframe.check();
            if (!$('#pseudocardpan').val()){
                return;
            }
            $.when($.payoneIframe.doAuth(form)).done(function () {
                submitted = true;
                console.log(form);
                form.submit();
            }).fail(function (data, textStatus, jqXHR) {
                $.payoneIframe.showErrorMessage(jqXHR.responseText);
                $(this).unbind(event);
            });

        });
        $(document).on('click', 'button.payone-cancel', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });
    });
}(window.jQuery, window, document));
