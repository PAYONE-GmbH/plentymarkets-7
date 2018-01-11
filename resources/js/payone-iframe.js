function ($) {

    $.payoneIframe = $.payoneIframe || {};

    $.payoneIframe.check = function () { // Function called by submitting PAY-button
        if (iframes.isComplete()) {
            iframes.creditCardCheck('checkCallback');// Perform "CreditCardCheck" to create and get a // PseudoCardPan; then call your function "checkCallback"
        } else {
            console.debug("not complete");
        }
    };

    $.payoneIframe.checkCallback = function (response) {
        console.debug(response);
        if (response.status === "VALID") {
            document.getElementById("pseudocardpan").value = response.pseudocardpan;
            document.getElementById("truncatedcardpan").value = response.truncatedcardpan;
            document.paymentform.submit();
        }
    };

    $.payoneIframe.getPayoneLocaleConfig = function (locale) {

        if (locale.indexOf('de') !== -1) {
            return Payone.ClientApi.Language.de;
        }
        return Payone.ClientApi.Language.en;
    };

    $.payoneIframe.createIframe = function (locale, request) {
        request.fields.language = $.payoneIframe.getPayoneLocaleConfig(locale);

        return new Payone.ClientApi.HostedIFrames(getPayoneLocaleConfig(locale), request);
    };

    $(function () {
        window.onload = function () {
            var iframes = $.payoneIframe.createIframe(request);
        }
    });
}