(function ($) {
  $.payoneIframe = $.payoneIframe || {};
  $.payoneIframe.iframe = null;
  $.payoneIframe.setCheckoutDisabled = function (isDisabled) {
    $("#orderPlace").prop("disabled", isDisabled);
  };

  $.payoneIframe.check = function () {
    // Function called by submitting PAY-button
    if ($.payoneIframe.iframe.isComplete()) {
      $.payoneIframe.iframe.creditCardCheck("checkCallback"); // Perform "CreditCardCheckRequestData" to create and get a // PseudoCardPan; then call your function "checkCallback"
    } else {
      console.debug("not complete");
    }
  };

  $.payoneIframe.checkCallback = function (response) {
    console.debug(response);
    if (response.status === "VALID") {
      document.getElementById("pseudocardpan").value = response.pseudocardpan;
      document.getElementById("truncatedcardpan").value =
        response.truncatedcardpan;
    }
  };

  $.payoneIframe.getPayoneLocaleConfig = function (locale) {
    if (locale.indexOf("de") !== -1) {
      return Payone.ClientApi.Language.de;
    }
    return Payone.ClientApi.Language.en;
  };

  $.payoneIframe.createIframe = function (
    locale,
    request,
    allowedCCTypes,
    defaultWidthInPx,
    defaultHeightInPx,
    defaultStyle,
    trailingSlash = ''
  ) {
    $.payoneIframe.trailingSlash = trailingSlash;
    var n = document.createElement("script");
    n.setAttribute("type", "text/javascript");
    n.setAttribute(
      "src",
      "https://secure.pay1.de/client-api/js/v1/payone_hosted_min.js"
    );
    n.onload = function () {
      $.payoneIframe.iframe = new Payone.ClientApi.HostedIFrames(
        $.payoneIframe.getPayoneConfig(
          allowedCCTypes,
          locale,
          defaultWidthInPx,
          defaultHeightInPx,
          defaultStyle
        ),
        request
      );
    };
    document.getElementsByTagName("body")[0].appendChild(n);
  };

  $.payoneIframe.storeCCResponse = function (response, trailingSlash = '') {
    return $.ajax({
      type: "POST",
      url: "/payment/payone/checkout/storeCCCheckResponse" + trailingSlash,
      data: response,
      dataType: "json",
      async: true,
    })
      .done(function (data) {
        console.log("done");
        console.log(data);
      })
      .fail(function (data) {
        var data = data.responseJSON;
        if (data.errors && data.errors.message) {
          $.payonePayment.showErrorMessage(data.errors.message);
        }
        console.log(data);
      });
  };

  $.payoneIframe.getPayoneConfig = function (
    allowedCCTypes,
    locale,
    defaultWidthInPx,
    defaultHeightInPx,
    defaultStyle
  ) {
    if (!(defaultWidthInPx > 0)) {
      defaultWidthInPx = $("#firstname").show().outerWidth();
    }
    if (!(defaultHeightInPx > 0)) {
      defaultHeightInPx = $("#firstname").show().outerHeight();
    }
    var config = {
      fields: {
        cardtype: {
          selector: "cardtype",
          cardtypes: allowedCCTypes,
        },
        cardpan: {
          size: "19",
          maxlength: "19",
          selector: "cardpan",
          type: "text",
          iframe: {
            width: defaultWidthInPx / 2 + "px",
          },
        },
        cardcvc2: {
          selector: "cardcvc2",
          type: "password",
          size: "4",
          maxlength: "4",
          iframe: {
            width: defaultWidthInPx + "px",
          },
        },
        cardexpiremonth: {
          selector: "cardexpiremonth",
          type: "select",
          size: "2",
          maxlength: "2",
          iframe: {
            width: defaultWidthInPx / 2 + "px",
          },
        },
        cardexpireyear: {
          selector: "cardexpireyear",
          type: "select",
          iframe: {
            width: defaultWidthInPx / 2 + "px",
          },
        },
      },
      autoCardtypeDetection: {
        supportedCardtypes: allowedCCTypes,
        deactivate: false,
        activate: true,
      },
      defaultStyle: {
        input: defaultStyle,
        select: defaultStyle,
        iframe: {
          width: defaultWidthInPx + "px",
          height: defaultHeightInPx + "px",
        },
      },
      error: "errorOutput",
    };
    config.language = $.payoneIframe.getPayoneLocaleConfig(locale);
    config.autoCardtypeDetection.callback = function (detectedCardtype) {
      $.payoneIframe.iframe.setCardType(detectedCardtype);
    };

    return config;
  };

  window.createIframeStart = function (trailingSlash) {
    $.payoneIframe.createIframe(
      Templates.locale,
      request,
      allowedCCTypes,
      defaultWidthInPx,
      defaultHeightInPx,
      defaultStyle,
      trailingSlash
    );
  };

  window.orderPlaceForm = function (event, orderId) {
    window.sessionStorage.setItem("cardOrderId", orderId);
    event.preventDefault();

    $.payonePayment.setCheckoutDisabled(true);
    $.payoneIframe.check();
  };
})(window.jQuery, window, document);
var submitted = false;

function checkCallback(response, trailingSlash = '') {
  console.log("doing callback...");
  console.debug(response);
  var form = $("#orderPlaceForm");
  if (submitted) {
    return false;
  }
  if (response.status !== "VALID") {
    $.payonePayment.setCheckoutDisabled(false);
    return false;
  }
  console.log("storing cc check response");
  $.when($.payoneIframe.storeCCResponse(response))
    .done(function () {
      console.log(response);
      console.log("submitting orderPlaceForm");
      var orderId = window.sessionStorage.getItem("cardOrderId");
      console.log(orderId);
      if (orderId > 0) {
        $.when($.payonePayment.doAuthFromOrder(form, orderId, $.payoneIframe.trailingSlash))
          .done(function (data) {
            if (data.data.redirecturl) {
              window.location.replace(data.data.redirecturl);
              window.sessionStorage.removeItem("cardOrderId");
              window.sessionStorage.clear();
              return false;
            }
            submitted = true;
            form.removeAttr("onsubmit");
            form.submit();
          })
          .fail(function (data, textStatus, jqXHR) {
            form.removeAttr("onsubmit");
          });
      } else {
        $.when($.payonePayment.doAuth(form, $.payoneIframe.trailingSlash))
          .done(function (data) {
            if (data.data.redirecturl) {
              window.location.replace(data.data.redirecturl);
              return false;
            }
            submitted = true;
            form.removeAttr("onsubmit");
            form.submit();
          })
          .fail(function (data, textStatus, jqXHR) {
            form.removeAttr("onsubmit");
          });
      }
    })
    .fail(function (data, textStatus, jqXHR) {
      form.removeAttr("onsubmit");
    });
}
