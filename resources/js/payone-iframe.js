(function ($) {

    $.payoneIframe = $.payoneIframe || {};
    $.payoneIframe.iframe = null;
    $.payoneIframe.setCheckoutDisabled = function (isDisabled) {
        $('#orderPlace').prop('disabled', isDisabled);
    };

    $.payoneIframe.check = function () { // Function called by submitting PAY-button
        if ($.payoneIframe.iframe.isComplete()) {
            $.payoneIframe.iframe.creditCardCheck('checkCallback');// Perform "CreditCardCheckRequestData" to create and get a // PseudoCardPan; then call your function "checkCallback"
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

    $.payoneIframe.createIframe = function (locale, request, allowedCCTypes) {

        var config= $.payoneIframe.getPayoneConfig(allowedCCTypes);
        var n = document.createElement("script");
        n.setAttribute("type", "text/javascript");
        n.setAttribute("src", 'https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js');
        n.onload = function () {
            config.fields.language = $.payoneIframe.getPayoneLocaleConfig(locale);
            config.autoCardtypeDetection.callback = function (detectedCardtype) {
                $.payoneIframe.iframe.setCardType(detectedCardtype);
            };
            $.payoneIframe.iframe = new Payone.ClientApi.HostedIFrames(config, request);
        };
        document.getElementsByTagName("body")[0].appendChild(n);

    };

    $.payoneIframe.doAuth = function () {
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
                success = false;
            }
            console.log('done');
            console.log(data);
        });
    };

    $.payoneIframe.storeCCResponse = function (response) {

        return $.ajax({
            type: 'POST',
            url: '/payone/checkout/storeCCCheckResponse',
            data: response,
            dataType: 'json',
            async: true
        }).done(function (data) {
            if (!data.success) {
                if (data.errors.message) {
                    console.log('done with errors');
                    console.log(data.errors.message);
                    $.payoneIframe.showErrorMessage(data.errors.message);
                }
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

    $.payoneIframe.getPayoneConfig = function (allowedCCTypes) {
        var defaultWidthInPx = $('#firstname').width();
        return {
            autoCardtypeDetection: {
                supportedCardtypes: allowedCCTypes,
                deactivate: false
            },
            fields: {
                cardtype: {
                    selector: "cardtype",
                    cardtypes: allowedCCTypes,
                    iframe: {
                        width: defaultWidthInPx + "px"
                    }
                },
                cardpan: {
                    size: "19",
                    maxlength: "19",
                    selector: "cardpan",
                    type: "text",
                    iframe: {
                        width: defaultWidthInPx + "px"
                    }
                    //style: "font-size: 1em; border: 1px solid #000;"
                },
                cardcvc2: {
                    selector: "cardcvc2",
                    type: "password",
                    style: "font-size: 1em; border: 1px solid #000;",
                    size: "4",
                    maxlength: "4",
                    iframe: {
                        width: defaultWidthInPx + "px"
                    }
                },
                cardexpiremonth: {
                    selector: "cardexpiremonth", type: "select",
                    size: "2",
                    maxlength: "2",
                    iframe: {
                        width: defaultWidthInPx / 2 + "px"
                    }
                },
                cardexpireyear: {
                    selector: "cardexpireyear", type: "select",
                    iframe: {
                        width: defaultWidthInPx / 2 + "px"
                    }
                }
            },
            defaultStyle: {
                input: "font-family: Helvetica; padding: .75rem 1.5rem; color: #7a7f7f; font-size: 1.25rem; border-radius: .2rem; border: 1px solid rgba(0,0,0,.15);",
                select: "font-family: Helvetica; padding: .75rem 1.5rem; color: #7a7f7f; font-size: 1.25rem; border-radius: .2rem; border: 1px solid rgba(0,0,0,.15);",
                iframe: {
                    width: defaultWidthInPx + "px",
                    height: "30px"
                }
            },
            error: "errorOutput"
        };
    };

    $(function () {
        $.payoneIframe.createIframe(Templates.locale, request, allowedCCTypes);
        $('#orderPlaceForm').on("submit", function (event) {
            event.preventDefault();

            $.payoneIframe.setCheckoutDisabled(true);
            $.payoneIframe.check();

        });
        $(document).on('click', 'button.payone-cancel', function () {
            $('button.btn.btn-primary.btn-block').prop('disabled', false);
        });

    });
}(window.jQuery, window, document));

var submitted = false;
function checkCallback(response) {
    console.debug(response);
    var form = $('#orderPlaceForm');
    if (submitted) {
        return false;
    }
    if (response.status !== "VALID") {
        $.payoneIframe.setCheckoutDisabled(false);
        return false;
    }
    console.log('storing cc check response');
    $.when($.payoneIframe.storeCCResponse(response)).done(function () {
        console.log('submitting orderPlaceForm');
        $.when($.payoneIframe.doAuth(form)).done(function () {
            submitted = true;
            console.log(form);
            form.unbind('submit');
            form.submit();
        }).fail(function (data, textStatus, jqXHR) {
            $.payoneIframe.showErrorMessage(jqXHR.responseText);
            form.unbind('submit');
        });
    }).fail(function (data, textStatus, jqXHR) {
        $.payoneIframe.showErrorMessage(jqXHR.responseText);
        form.unbind('submit');
    });
}
