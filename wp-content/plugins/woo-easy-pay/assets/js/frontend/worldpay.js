/* WorldpayJS v0.75.1 - Wed, 17 Jan 2018 12:10:44 GMT */ ! function(w) {
    var Worldpay = Worldpay || {
            api_path: "https://api.worldpay.com/v1/",
            templates_path: "https://online.worldpay.com/templates/",
            clientKey: "",
            templateCode: !1,
            token: "",
            reusable: !1,
            cvc: !0,
            timeout: 10,
            currencyCode: "EUR",
            validationType: "advanced",
            templateSaveButton: !0,
            templateOptions: {
                addedText: "Payment details added",
                addedTextCVC: "CVC added",
                enterCardDetailsButton: "Enter card details",
                iframeOverlay: "position: absolute;top: 0px;bottom: 0px;left: 0px;right: 0px;background-color: #000;-ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=80)';filter: alpha(opacity=80);-moz-opacity: 0.8;-khtml-opacity: 0.8;opacity: 0.8;",
                iframeClose: "position: absolute;top: 100px;height: 20px;width: 20px;right: -350px;text-decoration: none;margin-right: 50%;cursor: pointer;z-index: 999;color: #000;font-size: 16px;font-weight: bold;text-align: center;",
                iframeHolderInline: "position: relative;top: 0px;background-color: #fff;border:none;",
                iframeHolderModal: "position: absolute;top: 100px;left: -350px;background-color: #fff;width: 220px;height: 220px;margin-left: 50%;border:none;",
                dimensions: {
                    width: !1,
                    height: !1
                },
                images: {
                    enabled: !0
                },
                autofocus: !1,
                formatCardNumber: !0
            },
            displayType: "ownForm",
            templateType: "card",
            invalidClientKey: !1,
            tokenType: "card",
            callbacks: {
                beforeSubmit: function() {
                    return !0
                },
                validationError: function() {}
            },
            templateFormVisible: !1,
            debug: !1
        },
        defaultCardFormat = /(\d{1,4})/g,
        cardSettings = [{
            type: "amex",
            pattern: /^3[47]/,
            format: /(\d{1,4})(\d{1,6})?(\d{1,5})?/,
            length: [15],
            cvcLength: [4],
            luhn: !0
        }, {
            type: "dankort",
            pattern: /^5019/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "dinersclub",
            pattern: /^(36|38|30[0-5])/,
            format: defaultCardFormat,
            length: [14],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "discover",
            pattern: /^(6011|65|64[4-9]|622)/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "jcb",
            pattern: /^35/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "laser",
            pattern: /^(6706|6771|6709)/,
            format: defaultCardFormat,
            length: [16, 17, 18, 19],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "maestro",
            pattern: /^(5018|5020|5038|6304|6703|6759|676[1-3])/,
            format: defaultCardFormat,
            length: [12, 13, 14, 15, 16, 17, 18, 19],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "mastercard",
            pattern: /^5[1-5]/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "unionpay",
            pattern: /^62/,
            format: defaultCardFormat,
            length: [16, 17, 18, 19],
            cvcLength: [3],
            luhn: !1
        }, {
            type: "visaelectron",
            pattern: /^4(026|17500|405|508|844|91[37])/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "elo",
            pattern: /^4011|438935|45(1416|76)|50(4175|6699|67|90[4-7])|63(6297|6368)/,
            format: defaultCardFormat,
            length: [16],
            cvcLength: [3],
            luhn: !0
        }, {
            type: "visa",
            pattern: /^4/,
            format: defaultCardFormat,
            length: [13, 16],
            cvcLength: [3],
            luhn: !0
        }],
        defaultTemplateDimensions = {
            width: 220,
            height: 220
        };
    Worldpay.useOwnForm = function(a) {
        Worldpay.form.useOwnForm(a)
    }, Worldpay.useForm = function(a, b, c) {
        Worldpay.form.useOwnForm({
            form: a,
            callback: b,
            useReusableToken: c
        })
    }, Worldpay.useTemplateForm = function(a) {
        Worldpay.template.useTemplateForm(a)
    }, Worldpay.useTemplate = function(a, b, c, d) {
        Worldpay.template.useTemplateForm({
            form: a,
            paymentSection: b,
            display: c,
            callback: d
        })
    }, Worldpay.submitTemplateForm = function() {
        Worldpay.template.submitTemplateForm()
    }, Worldpay.getTemplateToken = function() {
        Worldpay.template.getTemplateToken()
    }, Worldpay.closeTemplateModal = function() {
        return Worldpay.template.closeTemplateModal()
    }, Worldpay.checkTemplateForm = function(a) {
        return Worldpay.template.checkTemplateForm(a)
    }, Worldpay.recalculateTemplateDimensions = function() {
        Worldpay.template.recalculateDimensions()
    }, Worldpay.setTemplateDimensions = function(a) {
        return a && a.height && a.width ? void Worldpay.template.changeTemplateDimensions(a) : void Worldpay.helpers.outputDevError("Worldpay.setTemplateDimensions No object found", "TD01")
    }, Worldpay.setClientKey = function(a) {
        this.clientKey = a
    }, Worldpay.setTemplate = function(a) {
        this.templateCode = a
    }, Worldpay.setReusable = function(a) {
        this.reusable = !!a
    }, Worldpay.setCVC = function(a) {
        this.cvc = !!a
    }, Worldpay.setValidationType = function(a) {
        this.validationType = "basic" == a || "advanced" == a ? a : "advanced"
    }, Worldpay.setTemplateOptions = function(a) {
        a.addedText && (Worldpay.templateOptions.addedText = a.addedText), a.addedTextCVC && (Worldpay.templateOptions.addedTextCVC = a.addedTextCVC), a.enterCardDetailsButton && (Worldpay.templateOptions.enterCardDetailsButton = a.enterCardDetailsButton), a.iframeOverlay && (Worldpay.templateOptions.iframeOverlay = a.iframeOverlay), a.iframeHolderInline && (Worldpay.templateOptions.iframeHolderInline = a.iframeHolderInline), a.iframeHolderModal && (Worldpay.templateOptions.iframeHolderModal = a.iframeHolderModal), a.dimensions && (Worldpay.templateOptions.dimensions = a.dimensions), a.dimensions && (Worldpay.templateOptions.dimensions = a.dimensions), a.images && (Worldpay.templateOptions.images = a.images), a.autofocus && (Worldpay.templateOptions.autofocus = a.autofocus)
    }, Worldpay.setDebugMode = function(a) {
        this.debug = a
    }, Worldpay.createAPMToken = function(a) {
        Worldpay.apm.createTokenFromObject(a)
    }, Worldpay.handleError = function(a, b, c) {
        if (!b) return !1;
        if (c.message && (c = c.message), void 0 != c) {
            "string" == typeof c ? b.innerHTML = Worldpay.helpers.capitaliseFirstLetter(c) : "object" == typeof c && c.length > 0 && (b.innerHTML = c.join("<br/>"));
            for (var d = 0; d < a.length; d++)("button" === a[d].type || "submit" === a[d].type) && (a[d].disabled = !1)
        }
    }, Worldpay.formBuilder = function(a, b, c, d, e, f) {
        var g = document.createElement(b);
        g.type = c, g.name = d, g.id = f || d, g.value = e, a.appendChild(g)
    }, Worldpay.form = {}, Worldpay.form.useOwnForm = function(a) {
        var b = !1,
            c = !1,
            d = !1,
            e = !1;
        a.form && (b = Worldpay.helpers.getElement(a.form, "FORM")), a.callback && (c = a.callback), a.useReusableToken && (d = a.useReusableToken), a.clientKey && Worldpay.setClientKey(a.clientKey), a.beforeSubmit && (Worldpay.callbacks.beforeSubmit = a.beforeSubmit), a.reusable && Worldpay.setReusable(!!a.reusable), a.cvc && (e = a.cvc, Worldpay.templateType = "cvc");
        for (var f = {}, g = 0; g < b.length; g++) b[g].getAttribute("data-worldpay") && (f[b[g].getAttribute("data-worldpay")] = b[g]);
        if (a.formatCardNumber && "card" == Worldpay.templateType) {
            if (!f.number) return console.log("WorldpayJS: Cannot find number - clkno"), !1;
            Worldpay.helpers.on(f.number, "keypress", Worldpay.helpers.restrictNumeric), Worldpay.helpers.on(f.number, "keypress", Worldpay.card.restrictCardNumber), Worldpay.helpers.on(f.number, "keypress", Worldpay.card.formatCardNumber), Worldpay.helpers.on(f.number, "keydown", Worldpay.card.formatBackCardNumber), Worldpay.helpers.on(f.number, "paste", Worldpay.card.reFormatCardNumber), Worldpay.helpers.on(f.cvc, "keypress", Worldpay.helpers.restrictNumeric), f["exp-month"] && "text" == f["exp-month"].type ? (Worldpay.helpers.on(f["exp-month"], "keypress", Worldpay.helpers.restrictNumeric), Worldpay.helpers.on(f["exp-year"], "keypress", Worldpay.helpers.restrictNumeric)) : f["exp-monthyear"] && (Worldpay.helpers.on(f["exp-monthyear"], "keypress", Worldpay.helpers.restrictNumeric), Worldpay.helpers.on(f["exp-monthyear"], "keypress", Worldpay.helpers.restrictCombinedExpiry), Worldpay.helpers.on(f["exp-monthyear"], "keypress", Worldpay.helpers.formatExpiry), Worldpay.helpers.on(f["exp-monthyear"], "keypress", Worldpay.helpers.formatForwardSlash), Worldpay.helpers.on(f["exp-monthyear"], "keypress", Worldpay.helpers.formatForwardExpiry), Worldpay.helpers.on(f["exp-monthyear"], "keydown", Worldpay.helpers.formatBackExpiry))
        } else "cvc" == Worldpay.templateType && Worldpay.helpers.on(f.cvc, "keypress", Worldpay.helpers.restrictNumeric);
        var h = document.getElementById("payment-form");
        Worldpay._shouldCheckFields = [];
        for (var i in f) Worldpay.helpers.on(f[i], "change", function(a) {
            var c = a.target.getAttribute("data-worldpay");
            "exp-month" == c ? (c = "expiration", void 0 == Worldpay._shouldCheckFields[c] && (Worldpay._shouldCheckFields.expirationmonth = !0)) : "exp-year" == c ? (c = "expiration", void 0 == Worldpay._shouldCheckFields[c] && (Worldpay._shouldCheckFields.expirationyear = !0)) : "exp-monthyear" == c ? (c = "expiration", void 0 == Worldpay._shouldCheckFields[c] && (Worldpay._shouldCheckFields.expirationboth = !0)) : void 0 == Worldpay._shouldCheckFields[c] && (Worldpay._shouldCheckFields[c] = !0), window.Placeholders && h && (window.Placeholders.disable(h), setTimeout(function() {
                window.Placeholders.enable(h);
                for (var a in f) document.activeElement === f[a] && window.Placeholders.disable(f[a])
            }, 50)), Worldpay.card.createTokenValidate(b, function(a, c) {
                Worldpay.helpers.templateFormCallback(a, c, b)
            })
        });
        return Worldpay.clientKey ? b === !1 ? (Worldpay.helpers.outputDevError("WorldpayJS: No form foun", "f04"), !1) : "function" != typeof c ? (Worldpay.helpers.outputDevError("WorldpayJS: No callback found", "c04"), !1) : void(b.onsubmit = function() {
            if (!Worldpay.callbacks.beforeSubmit()) return !1;
            Worldpay._shouldCheckFields.name = !0, Worldpay._shouldCheckFields.number = !0, Worldpay._shouldCheckFields.expirationboth = !0, Worldpay._shouldCheckFields.cvc = !0;
            var a = document.getElementById("payment-form");
            window.Placeholders && a && (window.Placeholders.disable(a), setTimeout(function() {
                window.Placeholders.enable(a)
            }, 50));
            for (var f = 0; f < b.length; f++)("button" === b[f].type || "submit" === b[f].type) && ("modal" != Worldpay.displayType ? 0 == Worldpay.templateSaveButton && (b[f].disabled = "disabled") : 0 == Worldpay.templateSaveButton && (b[f].disabled = "disabled"));
            return ("inline" == Worldpay.displayType || "modal" == Worldpay.displayType) && (c = function(a, c) {
                Worldpay.helpers.templateFormCallback(a, c, b)
            }), "apm" === Worldpay.tokenType ? (Worldpay.apm.createToken(b, c), !1) : (0 != e ? Worldpay.card.updateToken(b, e, c) : void 0 == d || 0 == d ? Worldpay.card.createToken(b, c) : Worldpay.card.reuseToken(b, c), !1)
        }) : (Worldpay.helpers.outputDevError("WorldpayJS: No clientKey found", "clk04"), !1)
    }, Worldpay.template = {}, Worldpay.template.messageListener, Worldpay.template.useTemplateForm = function(a) {
        var b = !1,
            c = !1,
            d = !1,
            e = !1,
            f = !1,
            g = (Worldpay.templateSaveButton, !1);
        if (a.form && (b = Worldpay.helpers.getElement(a.form, "FORM")), a.paymentSection && (c = Worldpay.helpers.getElement(a.paymentSection)), a.display && (d = a.display), a.type && (e = a.type), a.callback && (f = a.callback), a.clientKey && Worldpay.setClientKey(a.clientKey), a.beforeSubmit && (Worldpay.callbacks.beforeSubmit = a.beforeSubmit), a.validationError && (Worldpay.callbacks.validationError = a.validationError), a.reusable && Worldpay.setReusable(!!a.reusable), void 0 != a.saveButton && (Worldpay.templateSaveButton = !!a.saveButton), a.templateOptions && Worldpay.setTemplateOptions(a.templateOptions), a.code && (Worldpay.templateCode = a.code), a.token) g = a.token, Worldpay.cvcToken = g;
        else if ("cvc" == e) return Worldpay.helpers.outputDevError("WorldpayJS: No token defined for CVC type", "cvctkn04"), !1;
        if (!Worldpay.clientKey) return Worldpay.helpers.outputDevError("WorldpayJS: No clientKey found", "clk04"), !1;
        if (b === !1 && "function" != typeof f) return Worldpay.helpers.outputDevError("WorldpayJS: No form or callback found", "fc04"), !1;
        if (c === !1) return Worldpay.helpers.outputDevError("WorldpayJS: No payment section found", "psn04"), !1;
        if (d === !1) return Worldpay.helpers.outputDevError("WorldpayJS: No display set", "t04"), !1;
        Worldpay.displayType = d = "inline" == d || "modal" == d ? d : "modal", Worldpay.templateType = e = "card" == e || "cvc" == e ? e : "card", b !== !1 && b.setAttribute("onsubmit", "return Worldpay.checkTemplateForm(this)"), "modal" == d ? c.innerHTML = '<div id="token_container"><input type="button" value="' + Worldpay.templateOptions.enterCardDetailsButton + '" id="token_container-button" onclick="Worldpay.getTemplateToken()" /><div id="token_container_holder" style="visibility:hidden"></div></div>' : "inline" == d && (c.innerHTML = '<div id="token_container"><div id="token_container_holder" style="visibility:hidden"></div></div>', Worldpay.getTemplateToken());
        var h = function(a) {
            if (a && a.data) {
                var c = Worldpay.helpers.JSON.parse(a.data);
                Worldpay._dimensions = Worldpay._dimensions || {
                    width: defaultTemplateDimensions.width,
                    height: defaultTemplateDimensions.height
                };
                var d = Worldpay._dimensions;
                if (void 0 != c.token) {
                    var e = document.getElementById("token_container");
                    void 0 != f && 0 != f ? (b !== !1 && b.setAttribute("onsubmit", ""), f(c)) : e.innerHTML = '<div class="token_data_div"><input type="hidden" id="worldpayjs_token" name="token" value="' + c.token + '" />' + Worldpay.templateOptions.addedText + "</div>"
                } else if (void 0 != c.cvc) {
                    var e = document.getElementById("token_container");
                    void 0 != f && 0 != f ? (b !== !1 && b.setAttribute("onsubmit", ""), f(c)) : e.innerHTML = ""
                } else if (void 0 != c.errors) Worldpay.callbacks.validationError && Worldpay.callbacks.validationError(c.errors || !1);
                else if (void 0 != c.dimensions) Worldpay.templateOptions.dimensions.width ? d.width = Worldpay.templateOptions.dimensions.width : c.dimensions.width && c.dimensions.width >= defaultTemplateDimensions.width && (d.width = c.dimensions.width), Worldpay.templateOptions.dimensions.height ? d.height = Worldpay.templateOptions.dimensions.height : c.dimensions.height && (d.height = c.dimensions.height);
                else if (void 0 != c.visibility) {
                    var g = document.getElementById("token_container_holder"),
                        h = 200;
                    if (!g) return;
                    if (g.style.opacity = 0, g.style.filter = "alpha(opacity=0)", g.style.display = "inline-block", g.style.visibility = "visible", Worldpay.templateFormVisible = !0, h) var i = 0,
                        j = setInterval(function() {
                            i += 50 / h, i >= 1 && (clearInterval(j), i = 1), g.style.opacity = i, g.style.filter = "alpha(opacity=" + 100 * i + ")"
                        }, 50);
                    else g.style.opacity = 1, g.style.filter = "alpha(opacity=1)"
                }
                document.getElementById("_iframe_holder") && document.getElementById("_iframe_holder").style && (Worldpay.templateOptions.dimensions.width && (d.width = Worldpay.templateOptions.dimensions.width.toString().replace("px", ""), document.getElementById("_iframe_holder").style.width = d.width + "px"), Worldpay.templateOptions.dimensions.height && (d.height = Worldpay.templateOptions.dimensions.height.toString().replace("px", ""), document.getElementById("_iframe_holder").style.height = d.height + "px"), document.getElementById("_iframe_holder").style.width = d.width.toString().replace("px", "") + "px", document.getElementById("_iframe_holder").style.height = d.height.toString().replace("px", "") + "px", null != document.getElementById("_iframe_close") && (document.getElementById("_iframe_holder").style.left = -(d.width.toString().replace("px", "") / 2) + "px", document.getElementById("_iframe_close").style.right = -(d.width.toString().replace("px", "") / 2) + "px"))
            }
        };
        window.addEventListener ? (window.removeEventListener("message", Worldpay.template.messageListener, !1), window.addEventListener("message", h, !1)) : (window.detachEvent("onmessage", Worldpay.template.messageListener, !1), window.attachEvent("onmessage", h, !1)), Worldpay.template.messageListener = h
    }, Worldpay.template.submitTemplateForm = function() {
        return Worldpay.callbacks.beforeSubmit() ? void Worldpay.template.sendMessage(Worldpay.helpers.JSON.stringify({
            submit: !0
        })) : !1
    }, Worldpay.template.getTemplateToken = function() {
        if ("modal" == Worldpay.displayType) {
            var a = document.getElementById("token_container_holder");
            a.innerHTML = '<div id="_iframe_overlay" style="' + Worldpay.templateOptions.iframeOverlay + '"></div><a id="_iframe_close" onclick="Worldpay.closeTemplateModal()" href="javascript:;" style="' + Worldpay.templateOptions.iframeClose + '">&times;</a><iframe id="_iframe_holder" scrolling="no" style="' + Worldpay.templateOptions.iframeHolderModal + '" marginheight="0" marginwidth="0" frameborder="0"></iframe>';
            var b = setInterval(function() {
                    document.getElementById("_iframe_overlay").style.webkitTransform = "scale(0.9999999)"
                }, 1),
                c = setInterval(function() {
                    document.getElementById("_iframe_overlay").style.webkitTransform = "scale(1)"
                }, 5);
            setTimeout(function() {
                clearInterval(b), clearInterval(c)
            }, 5e3)
        } else if ("inline" == Worldpay.displayType) {
            var a = document.getElementById("token_container_holder");
            a.innerHTML = '<iframe id="_iframe_holder" scrolling="no" style="' + Worldpay.templateOptions.iframeHolderInline + '" marginheight="0" marginwidth="0" frameborder="0"></iframe>'
        }
        var d = Worldpay.templates_path,
            e = document.getElementById("_iframe_holder"),
            f = e.contentWindow || e.contentDocument;
        if (f.document && (f = f.document), "modal" == Worldpay.displayType) f.write('<body><form action="' + d + '" method="POST" id="iframepost"><input type="hidden" name="clientKey" value="' + Worldpay.clientKey + '" /><input type="hidden" name="templateType" value="' + Worldpay.templateType + '" /><input type="hidden" name="cvc" value="' + Worldpay.cvc + '" /><input type="hidden" name="reusable" value="' + Worldpay.reusable + '" /><input type="hidden" name="cvcToken" value="' + (Worldpay.cvcToken || !1) + '" /><input type="hidden" name="displayType" value="modal" /><input type="hidden" name="button" value="Save" /><input type="hidden" name="templateCode" value="' + (Worldpay.templateCode || 0) + '" /><input type="hidden" name="images" value="' + Worldpay.templateOptions.images.enabled + '" /><input type="hidden" name="autofocus" value="' + Worldpay.templateOptions.autofocus + '" /></form></body>');
        else if ("inline" == Worldpay.displayType) {
            var g = "";
            (0 != Worldpay.templateOptions.dimensions.width || 0 != Worldpay.templateOptions.dimensions.height) && (g = "w:" + Worldpay.templateOptions.dimensions.width + ",h:" + Worldpay.templateOptions.dimensions.height), f.write('<body><form action="' + d + '" method="POST" id="iframepost"><input type="hidden" name="clientKey" value="' + Worldpay.clientKey + '" /><input type="hidden" name="templateType" value="' + Worldpay.templateType + '" /><input type="hidden" name="cvc" value="' + Worldpay.cvc + '" /><input type="hidden" name="reusable" value="' + Worldpay.reusable + '" /><input type="hidden" name="cvcToken" value="' + (Worldpay.cvcToken || !1) + '" /><input type="hidden" name="displayType" value="inline" /><input type="hidden" name="button" value="Save" /><input type="hidden" name="templateCode" value="' + (Worldpay.templateCode || 0) + '" /><input type="hidden" name="templateSaveButton" value="' + Worldpay.templateSaveButton + '" /><input type="hidden" name="templateDimensions" value="' + g + '" /><input type="hidden" name="images" value="' + Worldpay.templateOptions.images.enabled + '" /><input type="hidden" name="autofocus" value="' + Worldpay.templateOptions.autofocus + '" /></form></body>')
        }
        Worldpay.templateFormVisible = !1, e.onload = Worldpay.template.formLoaded, f.getElementById("iframepost").submit()
    }, Worldpay.template.closeTemplateModal = function() {
        var a = document.getElementById("token_container_holder"),
            b = 300;
        if (a)
            if (b) var c = 1,
                d = setInterval(function() {
                    c -= 50 / b, 0 >= c && (clearInterval(d), c = 0, a.style.display = "none", a.style.visibility = "hidden"), a.style.opacity = c, a.style.filter = "alpha(opacity=" + 100 * c + ")"
                }, 50);
            else a.style.opacity = 0, a.style.filter = "alpha(opacity=0)", a.style.display = "none", a.style.visibility = "hidden"
    }, Worldpay.template.checkTemplateForm = function(a) {
        return void 0 != a.token ? !0 : !1
    }, Worldpay.template.formLoaded = function() {
        Worldpay.templateFormVisible || setTimeout(function() {
            Worldpay.templateFormVisible || (window.postMessage(Worldpay.helpers.JSON.stringify({
                visibility: !0
            }), "*"), window.postMessage(Worldpay.helpers.JSON.stringify({
                dimensions: {
                    width: "520",
                    height: "400"
                }
            }), "*"))
        }, 5e3)
    }, Worldpay.template.sendMessage = function(a) {
        var b = document.getElementById("_iframe_holder"),
            c = b.contentWindow || b.contentDocument;
        c.postMessage(a, "*")
    }, Worldpay.template.recalculateDimensions = function() {
        window.postMessage(Worldpay.helpers.JSON.stringify({
            dimensions: {
                width: defaultTemplateDimensions.width,
                height: defaultTemplateDimensions.height
            }
        }), "*"), Worldpay.template.sendMessage(Worldpay.helpers.JSON.stringify({
            recalculateDimensions: !0
        }))
    }, Worldpay.template.changeTemplateDimensions = function(a) {
        window.postMessage(Worldpay.helpers.JSON.stringify({
            dimensions: {
                width: a.width,
                height: a.height
            }
        }), "*")
    }, Worldpay.helpers = {}, Worldpay.helpers.getElement = function(a, b) {
        if ("" != a && void 0 != a) {
            if (a && a.tagName && (!b || b && a.tagName == b)) return a;
            var c = document.getElementById(a);
            if (c && c.tagName && (!b || b && c.tagName == b)) return a = document.getElementById(a);
            if (a[0] && a[0].tagName && (!b || b && a[0].tagName == b)) return a[0]
        }
        return !1
    }, Worldpay.helpers.deleteFunction = function(a) {
        try {
            delete w[a]
        } catch (b) {
            w[a] = void 0
        }
    }, Worldpay.helpers.getEl = function(a) {
        return "string" == typeof a && (a = document.getElementById(a)), a
    }, Worldpay.helpers.collect = function(a, b) {
        for (var c = [], d = 0; d < a.length; d++) {
            var e = b(a[d]);
            null !== e && c.push(e)
        }
        return c
    }, Worldpay.helpers.createErrorResponse = function(a) {
        var b = {
            error: {
                message: "",
                object: []
            }
        };
        if (a.length) {
            b.error.object = a;
            for (var c = [], d = 0; d < a.length; d++)
                for (var e in a[d]) c.push(a[d][e]);
            c.join("<br/>"), b.error.message = c
        }
        return b
    };
    var isEmptyError = function(a) {
            return "object" == typeof a && void 0 != a.object && 0 == a.object.length
        },
        isSuccessfulResponse = function(a) {
            return !a.error || isEmptyError(a.error)
        };
    Worldpay.helpers.templateFormCallback = function(a, b, c) {
        var d = "_error",
            e = Worldpay.helpers.removeClass,
            f = Worldpay.helpers.addClass,
            g = g || null;
        document.getElementById("_el_error_nameoncard") && (document.getElementById("_el_error_nameoncard").style.display = "none", document.getElementById("_el_input_nameoncard") && (e(document.getElementById("_el_input_nameoncard").firstChild, d), document.getElementById("_el_label_nameoncard") && e(document.getElementById("_el_label_nameoncard").firstChild, d))), document.getElementById("_el_error_cardnumber") && (document.getElementById("_el_error_cardnumber").style.display = "none", document.getElementById("_el_input_cardnumber") && (e(document.getElementById("_el_input_cardnumber").firstChild, d), document.getElementById("_el_label_cardnumber") && e(document.getElementById("_el_label_cardnumber").firstChild, d), e(document.getElementById("_el_error_cardnumber").firstChild, d))), document.getElementById("_el_error_expiration") && (document.getElementById("_el_error_expiration").style.display = "none", document.getElementById("_el_input_expirationmonth") && e(document.getElementById("_el_input_expirationmonth").firstChild, d), document.getElementById("_el_input_expirationyear") && e(document.getElementById("_el_input_expirationyear").firstChild, d), document.getElementById("_el_input_expirationmonth_dd") && e(document.getElementById("_el_input_expirationmonth_dd").firstChild, d), document.getElementById("_el_input_expirationyear_dd") && e(document.getElementById("_el_input_expirationyear_dd").firstChild, d), document.getElementById("_el_input_expirationboth") && e(document.getElementById("_el_input_expirationboth").firstChild, d), document.getElementById("_el_label_expiration") && e(document.getElementById("_el_label_expiration").firstChild, d)), document.getElementById("_el_error_cvc") && (document.getElementById("_el_error_cvc").style.display = "none", document.getElementById("_el_input_cvc") && (e(document.getElementById("_el_input_cvc").firstChild, d), document.getElementById("_el_label_cvc") && e(document.getElementById("_el_label_cvc").firstChild, d))), document.getElementById("payment-errors") && (document.getElementById("payment-errors").style.display = "none");
        var h = function(a) {
            document.getElementById("payment-errors") && (document.getElementById("payment-errors").style.display = "block", document.getElementById("payment-errors").innerHTML = a)
        };
        if (isSuccessfulResponse(b))
            if (b.error);
            else if (b.token) {
            var i = b.token;
            Worldpay.formBuilder(c, "input", "hidden", "token", i), window.parent.postMessage(Worldpay.helpers.JSON.stringify(b), "*")
        } else "cvc" == Worldpay.templateType && window.parent.postMessage(Worldpay.helpers.JSON.stringify({
            cvc: !0,
            status: a,
            response: b
        }), "*");
        else {
            if ("object" == typeof b.error)
                if ("BAD_REQUEST" == b.error.customCode) {
                    if (Worldpay.handleError(c, document.getElementById("payment-errors"), b.error.message), document.getElementById("_el_error_cvc") && (document.getElementById("_el_error_cvc").style.display = "block", document.getElementById("_el_input_cvc") && f(document.getElementById("_el_input_cvc").firstChild, d), g)) {
                        if (document.getElementById("_el_label_cvc")) var j = document.getElementById("_el_label_cvc"),
                            k = j.offsetTop + j.getBoundingClientRect().height / 2;
                        if (document.getElementById("_el_error_cvc")) {
                            var l = document.getElementById("_el_error_cvc");
                            l.style.setProperty("top", Math.abs(parseFloat(k) - parseFloat(l.getBoundingClientRect().height / 2)) + "px", "important")
                        }
                    }
                } else if (b.error.object)
                for (var m in b.error.object)
                    for (var n in b.error.object[m])
                        if ("nameoncard" == n && Worldpay._shouldCheckFields.name) {
                            if (document.getElementById("_el_error_nameoncard") && (document.getElementById("_el_error_nameoncard").style.display = "block"), document.getElementById("_el_input_nameoncard") && (f(document.getElementById("_el_input_nameoncard").firstChild, d), document.getElementById("_el_label_nameoncard") && f(document.getElementById("_el_label_nameoncard").firstChild, d), document.getElementById("_el_error_nameoncard") && f(document.getElementById("_el_error_nameoncard").firstChild, d)), g) {
                                if (document.getElementById("_el_label_nameoncard")) var o = document.getElementById("_el_label_nameoncard"),
                                    p = o.offsetTop + o.getBoundingClientRect().height / 2;
                                if (document.getElementById("_el_error_nameoncard")) {
                                    var q = document.getElementById("_el_error_nameoncard");
                                    q.style.setProperty("top", Math.abs(parseFloat(p) - parseFloat(q.getBoundingClientRect().height / 2)) + "px", "important")
                                }
                            }
                        } else if ("cardnumber" == n && Worldpay._shouldCheckFields.number) {
                if (document.getElementById("_el_error_cardnumber") && (document.getElementById("_el_error_cardnumber").style.display = "block"), document.getElementById("_el_input_cardnumber") && (f(document.getElementById("_el_input_cardnumber").firstChild, d), document.getElementById("_el_label_cardnumber") && f(document.getElementById("_el_label_cardnumber").firstChild, d), document.getElementById("_el_error_cardnumber") && f(document.getElementById("_el_error_cardnumber").firstChild, d)), g) {
                    if (document.getElementById("_el_label_cardnumber")) var r = document.getElementById("_el_label_cardnumber"),
                        s = r.offsetTop + r.getBoundingClientRect().height / 2;
                    if (document.getElementById("_el_error_cardnumber")) {
                        var t = document.getElementById("_el_error_cardnumber");
                        t.style.setProperty("top", Math.abs(parseFloat(s) - parseFloat(t.getBoundingClientRect().height / 2)) + "px", "important")
                    }
                }
            } else if ("expiration" == n && (Worldpay._shouldCheckFields.expirationboth || Worldpay._shouldCheckFields.expirationmonth && Worldpay._shouldCheckFields.expirationyear)) {
                if (document.getElementById("_el_error_expiration") && (document.getElementById("_el_error_expiration").style.display = "block"), document.getElementById("_el_input_expirationmonth") && f(document.getElementById("_el_input_expirationmonth").firstChild, d), document.getElementById("_el_input_expirationyear") && f(document.getElementById("_el_input_expirationyear").firstChild, d), document.getElementById("_el_input_expirationmonth_dd") && f(document.getElementById("_el_input_expirationmonth_dd").firstChild, d), document.getElementById("_el_input_expirationyear_dd") && f(document.getElementById("_el_input_expirationyear_dd").firstChild, d), document.getElementById("_el_input_expirationboth") && f(document.getElementById("_el_input_expirationboth").firstChild, d), document.getElementById("_el_label_expiration") && f(document.getElementById("_el_label_expiration").firstChild, d), document.getElementById("_el_error_expiration") && f(document.getElementById("_el_error_expiration").firstChild, d), g) {
                    if (document.getElementById("_el_label_expiration")) var u = document.getElementById("_el_label_expiration"),
                        k = u.offsetTop + u.getBoundingClientRect().height / 2;
                    if (document.getElementById("_el_error_expiration")) {
                        var v = document.getElementById("_el_error_expiration");
                        v.style.setProperty("top", Math.abs(parseFloat(k) - parseFloat(v.getBoundingClientRect().height / 2)) + "px", "important")
                    }
                }
            } else if ("cvc" == n && Worldpay._shouldCheckFields.cvc) {
                if (document.getElementById("_el_error_cvc") && (document.getElementById("_el_error_cvc").style.display = "block"), document.getElementById("_el_input_cvc") && (f(document.getElementById("_el_input_cvc").firstChild, d), document.getElementById("_el_label_cvc") && f(document.getElementById("_el_label_cvc").firstChild, d), document.getElementById("_el_error_cvc") && f(document.getElementById("_el_error_cvc").firstChild, d)), g) {
                    if (document.getElementById("_el_label_cvc")) var j = document.getElementById("_el_label_cvc"),
                        k = j.offsetTop + j.getBoundingClientRect().height / 2;
                    if (document.getElementById("_el_error_cvc")) {
                        var l = document.getElementById("_el_error_cvc");
                        l.style.setProperty("top", Math.abs(parseFloat(k) - parseFloat(l.getBoundingClientRect().height / 2)) + "px", "important")
                    }
                }
            } else "other" == n && document.getElementById("payment-errors") && (document.getElementById("payment-errors").style.display = "block", document.getElementById("payment-errors").innerHTML = b.error.object[m].other);
            else h(b.error.message);
            else h(b.error);
            window.parent.postMessage(Worldpay.helpers.JSON.stringify({
                errors: !0
            }), "*")
        }
    }, Worldpay.helpers.capitaliseFirstLetter = function(a) {
        return a ? a.charAt(0).toUpperCase() + a.slice(1) : ""
    }, Worldpay.helpers.ajax = {}, Worldpay.helpers.ajax.x = function(a, b) {
        var c = new XMLHttpRequest;
        return "withCredentials" in c ? c.open(a, b, !0) : "undefined" != typeof XDomainRequest ? (c = new XDomainRequest, c.open(a, b)) : c = null, c
    }, Worldpay.helpers.ajax.send = function(a, b, c, d) {
        try {
            var e = function() {
                var e = new XDomainRequest;
                e.open("POST", a), e.onload = function() {
                    var a = e.responseText;
                    try {
                        var d = 200,
                            f = {};
                        if ("PUT" == c.toUpperCase()) d = 200;
                        else {
                            var g = Worldpay.helpers.JSON.parse(a);
                            f = g.customCode && "" != g.customCode ? {
                                error: {
                                    message: g.message,
                                    customCode: g.customCode
                                }
                            } : g, g.token || (d = 400)
                        }
                        b(d, f)
                    } catch (h) {
                        b(400, Worldpay.helpers.JSON.parse('{"error":"Failed to process card, please check your details."}'))
                    }
                }, e.onerror = function() {
                    "PUT" == c.toUpperCase() ? b(400, {
                        error: {
                            customCode: "BAD_REQUEST",
                            message: "CVC is invalid"
                        }
                    }) : b(400, Worldpay.helpers.JSON.parse('{"error":"Failed to process card, please check your details."}'))
                }, e.onprogress = function() {}, e.ontimeout = function() {
                    b(408, Worldpay.helpers.JSON.parse('{"error":"Processing card timed out, please try again."}'))
                }, setTimeout(function() {
                    e.send(d)
                }, 500)
            };
            if (window.XMLHttpRequest) {
                var f = new XMLHttpRequest;
                "withCredentials" in f ? (f.onreadystatechange = function() {
                    if (f.readyState && 4 === f.readyState) {
                        var a = f.responseText ? f.responseText : "{}";
                        try {
                            var d = Worldpay.helpers.JSON.parse(a),
                                e = d.customCode && "" != d.customCode ? {
                                    error: {
                                        message: d.message,
                                        customCode: d.customCode
                                    }
                                } : d,
                                g = 200;
                            d.token || (g = "PUT" == c.toUpperCase() ? f.status && 0 != parseInt(f.status) ? f.status : d.customCode && "" != d.customCode ? 400 : 200 : 400), b(g, e)
                        } catch (h) {
                            b(f.status || 400, Worldpay.helpers.JSON.parse('{"error":"' + (f.statusText || "Could not connect to server") + '"}'))
                        }
                    }
                }, f.open(c, a, !0), f.setRequestHeader("Content-type", "application/json"), f.send(d)) : window.XDomainRequest && e()
            } else window.XDomainRequest && e()
        } catch (g) {
            b(400, Worldpay.helpers.JSON.parse('{"error":"Failed to process card, please check your details."}'))
        }
    }, Worldpay.helpers.ajax.post = function(a, b, c) {
        Worldpay.helpers.ajax.send(a, b, "POST", c)
    }, Worldpay.helpers.ajax.put = function(a, b, c) {
        Worldpay.helpers.ajax.send(a, b, "PUT", c)
    }, Worldpay.helpers.JSON = {}, Worldpay.helpers.JSON.stringify = function(a) {
        if (void 0 != JSON && void 0 != JSON.stringify) return JSON.stringify(a);
        var b = typeof a;
        if ("object" !== b || null === a) return "string" === b && (a = '"' + a + '"'), String(a);
        var c, d, e = [],
            f = a && a.constructor == Array;
        for (c in a) d = a[c], b = typeof d, "string" === b ? d = '"' + d + '"' : "object" === b && null !== d && (d = Worldpay.helpers.JSON.stringify(d)), e.push((f ? "" : '"' + c + '":') + String(d));
        return (f ? "[" : "{") + String(e) + (f ? "]" : "}")
    }, Worldpay.helpers.JSON.parse = function(str) {
        return void 0 != JSON && void 0 != JSON.parse ? JSON.parse(str) : ("" === str && (str = '""'), eval("var p=" + str + ";"), p)
    }, Worldpay.helpers.Object = {}, Worldpay.helpers.Object.keys = Object.keys || function(a) {
        var b = Object.prototype.hasOwnProperty,
            c = !{
                toString: null
            }.propertyIsEnumerable("toString"),
            d = ["toString", "toLocaleString", "valueOf", "hasOwnProperty", "isPrototypeOf", "propertyIsEnumerable", "constructor"],
            e = d.length;
        return function(a) {
            if ("object" != typeof a && ("function" != typeof a || null === a)) throw new TypeError("Object.keys called on non-object");
            var f, g, h = [];
            for (f in a) b.call(a, f) && h.push(f);
            if (c)
                for (g = 0; e > g; g++) b.call(a, d[g]) && h.push(d[g]);
            return h
        }
    }, Worldpay.helpers.on = function(a, b, c) {
        var d, e, f, g, h, i, j, k;
        if(typeof a === "undefined"){
        	return;
        }
        if (a.length && "SELECT" != a.tagName)
            for (e = 0, g = a.length; g > e; e++) d = a[e], Worldpay.helpers.on(d, b, c);
        else {
            if (!b.match(" ")) return j = c, c = function(a) {
                return a = Worldpay.helpers.normalizeEvent(a), j(a)
            }, a.addEventListener ? a.addEventListener(b, c, !1) : a.attachEvent ? (b = "on" + b, a.attachEvent(b, c)) : void(a["on" + b] = c);
            for (k = b.split(" "), f = 0, h = k.length; h > f; f++) i = k[f], Worldpay.helpers.on(a, i, c)
        }
    }, Worldpay.helpers.normalizeEvent = function(a) {
        var b;
        return b = a, a = {
                which: null != b.which ? b.which : void 0,
                target: b.target || b.srcElement,
                preventDefault: function() {
                    return Worldpay.helpers.preventDefault(b)
                },
                originalEvent: b,
                data: b.data || b.detail
            }, null == a.which && (a.which = null != b.charCode ? b.charCode : b.keyCode),
            a
    }, Worldpay.helpers.val = function(a, b) {
        var c;
        return arguments.length > 1 ? a.value = b : (c = a.value, "string" == typeof c ? c.replace(/\r/g, "") : null === c ? "" : c)
    }, Worldpay.helpers.hasTextSelected = function(a) {
        var b;
        return null != a.selectionStart && a.selectionStart !== a.selectionEnd ? !0 : null != ("undefined" != typeof document && null !== document && null != (b = document.selection) ? b.createRange : void 0) && document.selection.createRange().text ? !0 : !1
    }, Worldpay.helpers.preventDefault = function(a) {
        return "function" == typeof a.preventDefault ? void a.preventDefault() : (a.returnValue = !1, !1)
    }, Worldpay.helpers.restrictNumeric = function(a) {
        var b;
        return a.metaKey || a.ctrlKey ? !0 : 32 === a.which ? a.preventDefault() : 0 === a.which ? !0 : a.which < 33 ? !0 : (b = String.fromCharCode(a.which), /[\d\s]/.test(b) ? void 0 : a.preventDefault())
    }, Worldpay.helpers.formatExpiry = function(a) {
        var b, c, d;
        return b = String.fromCharCode(a.which), /^\d+$/.test(b) ? (c = a.target, d = Worldpay.helpers.val(c) + b, /^\d$/.test(d) && "0" !== d && "1" !== d ? (a.preventDefault(), Worldpay.helpers.val(c, "0" + d + " / ")) : /^\d\d$/.test(d) ? (a.preventDefault(), Worldpay.helpers.val(c, d + " / ")) : void 0) : void 0
    }, Worldpay.helpers.formatForwardSlash = function(a) {
        var b, c, d;
        return b = String.fromCharCode(a.which), "/" === b ? (c = a.target, d = Worldpay.helpers.val(c), /^\d$/.test(d) && "0" !== d ? Worldpay.helpers.val(c, "0" + d + " / ") : void 0) : void 0
    }, Worldpay.helpers.formatForwardExpiry = function(a) {
        var b, c, d;
        return b = String.fromCharCode(a.which), /^\d+$/.test(b) ? (c = a.target, d = Worldpay.helpers.val(c), /^\d\d$/.test(d) ? Worldpay.helpers.val(c, d + " / ") : void 0) : void 0
    }, Worldpay.helpers.formatForwardSlash = function(a) {
        var b, c, d;
        return b = String.fromCharCode(a.which), "/" === b ? (c = a.target, d = Worldpay.helpers.val(c), /^\d$/.test(d) && "0" !== d ? Worldpay.helpers.val(c, "0" + d + " / ") : void 0) : void 0
    }, Worldpay.helpers.formatBackExpiry = function(a) {
        var b, c;
        if (!a.metaKey && (b = a.target, c = Worldpay.helpers.val(b), 8 === a.which && (null == b.selectionStart || b.selectionStart === c.length))) return /\d(\s|\/)+$/.test(c) ? (a.preventDefault(), Worldpay.helpers.val(b, c.replace(/\d(\s|\/)*$/, ""))) : /\s\/\s?\d?$/.test(c) ? (a.preventDefault(), Worldpay.helpers.val(b, c.replace(/\s\/\s?\d?$/, ""))) : void 0
    }, Worldpay.helpers.restrictExpiry = function(a, b) {
        var c, d, e;
        return d = a.target, c = String.fromCharCode(a.which), /^\d+$/.test(c) && !Worldpay.helpers.hasTextSelected(d) ? (e = Worldpay.helpers.val(d) + c, e = e.replace(/\D/g, ""), e.length > b ? a.preventDefault() : void 0) : void 0
    }, Worldpay.helpers.restrictCombinedExpiry = function(a) {
        return Worldpay.helpers.restrictExpiry(a, 6)
    }, Worldpay.helpers.restrictMonthExpiry = function(a) {
        return Worldpay.helpers.restrictExpiry(a, 2)
    }, Worldpay.helpers.restrictYearExpiry = function(a) {
        return Worldpay.helpers.restrictExpiry(a, 4)
    }, Worldpay.helpers.outputDevError = function(a, b) {
        Worldpay.debug ? console.log("WorldpayJS: " + a + " - CODE: " + b) : console && console.log && console.log("WorldpayJS: " + b)
    }, Worldpay.helpers.cleanInput = function(a) {
        return a ? (a = a.trim(), a = a.toUpperCase()) : ""
    }, Worldpay.helpers.addClass = function(a, b) {
        a && (a.classList ? a.classList.add(b) : a.className += " " + b)
    }, Worldpay.helpers.removeClass = function(a, b) {
        var c = "(^|\\b)",
            d = "(\\b|$)";
        a && (a.classList ? a.classList.remove(b) : a.className = a.className.replace(new RegExp(c + b.split(" ").join("|") + d, "gi"), " "))
    }, Worldpay.apm = {}, Worldpay.apm.createToken = function(a, b) {
        var c = Worldpay.apm.createTokenValidate(a, b);
        if (0 != c) {
            var d = {
                reusable: Worldpay.reusable,
                paymentMethod: {
                    type: "APM",
                    apmName: c["apm-name"],
                    shopperCountryCode: c["country-code"],
                    apmFields: c["apm-fields"]
                },
                clientKey: Worldpay.clientKey
            };
            c["language-code"] && (d.shopperLanguageCode = c["language-code"]), Worldpay.helpers.ajax.post(Worldpay.api_path + "tokens", b, Worldpay.helpers.JSON.stringify(d))
        }
    }, Worldpay.apm.createTokenValidate = function(a, b) {
        var c = {};
        c["apm-fields"] = {};
        for (var d = 0; d < a.length; d++) a[d].getAttribute("data-worldpay") ? c[a[d].getAttribute("data-worldpay")] = a[d].value : a[d].getAttribute("data-worldpay-apm") && (c["apm-fields"][a[d].getAttribute("data-worldpay-apm")] = a[d].value);
        var e = [];
        return c["apm-name"] = c["apm-name"] || "", c["country-code"] = c["country-code"] || "", Worldpay.apm.validateApmName(c["apm-name"]) || e.push({
            apmname: "APM name is not valid"
        }), Worldpay.apm.validateCountryCode(c["country-code"]) || e.push({
            countrycode: "Country code is not valid"
        }), e.length ? (b(501, Worldpay.helpers.createErrorResponse(e)), !1) : (b(200, Worldpay.helpers.createErrorResponse(e)), c)
    }, Worldpay.apm.validateExists = function(a) {
        return null === a || "" === a ? !1 : !0
    }, Worldpay.apm.validateApmName = function(a) {
        return null === a || "" === a ? !1 : !0
    }, Worldpay.apm.validateCountryCode = function(a) {
        return null === a || "" === a ? !1 : !0
    }, Worldpay.apm.validateIdealShopperBankCode = function(a) {
        if (a = Worldpay.helpers.cleanInput(a), "" === a) return !1;
        switch (a) {
            case "ABN_AMRO":
            case "ASN":
            case "ING":
            case "FRIESLAND":
            case "RABOBANK":
            case "SNS":
            case "SNS_REGIO":
            case "TRIODOS":
            case "VAN_LANSCHOT":
            case "KNAB":
            case "BUNQ":
                return !0
        }
        return !1
    }, Worldpay.apm.createTokenFromObject = function(a) {
        if (!a || "object" != typeof a) return Worldpay.helpers.outputDevError("Worldpay.createAPMToken No object found", "A01"), !1;
        var b = a.callback,
            c = !1,
            d = [];
        if (a.clientKey && Worldpay.setClientKey(a.clientKey), a.useReusableToken && (c = a.useReusableToken), "function" != typeof b) return Worldpay.helpers.outputDevError("Worldpay.createAPMToken No callback found", "A02"), !1;
        if (a.apmName = a.apmName || "", a.countryCode = a.countryCode || "", a.apmFields = a.apmFields || {}, Worldpay.apm.validateApmName(a.apmName) || d.push({
                apmname: "APM name is not valid"
            }), Worldpay.apm.validateCountryCode(a.countryCode) || d.push({
                countrycode: "Country code is not valid"
            }), "giropay" == a.apmName && (Worldpay.apm.validateExists(a.apmFields) && Worldpay.apm.validateExists(a.apmFields.swiftCode) || d.push({
                swiftCode: "Swift code is not valid"
            })), "ideal" == a.apmName && (Worldpay.apm.validateExists(a.apmFields) && Worldpay.apm.validateExists(a.apmFields.shopperBankCode) && Worldpay.apm.validateIdealShopperBankCode(a.apmFields.shopperBankCode) || d.push({
                shopperBankCode: "Bank code is not valid"
            })), d.length) return b(501, Worldpay.helpers.createErrorResponse(d)), !1;
        var e = {
            reusable: c,
            paymentMethod: {
                type: "APM",
                apmName: a.apmName,
                shopperCountryCode: a.countryCode,
                apmFields: a.apmFields
            },
            clientKey: Worldpay.clientKey
        };
        a.languageCode && (e.shopperLanguageCode = a.languageCode), Worldpay.helpers.ajax.post(Worldpay.api_path + "tokens", b, Worldpay.helpers.JSON.stringify(e))
    }, Worldpay.card = {}, Worldpay.card.updateToken = function(a, b, c) {
        for (var d = {}, e = 0; e < a.length; e++) a[e].getAttribute("data-worldpay") && (d[a[e].getAttribute("data-worldpay")] = a[e].value);
        d.cvc = d.cvc || "";
        var f = [];
        if (Worldpay.card.validateCVC(d.cvc) || f.push({
                cvc: "CVC is not valid"
            }), f.length) return c(501, Worldpay.helpers.createErrorResponse(f)), !1;
        var g = {
            clientKey: Worldpay.clientKey,
            cvc: d.cvc
        };
        Worldpay.helpers.ajax.put(Worldpay.api_path + "tokens/" + b, c, Worldpay.helpers.JSON.stringify(g))
    }, Worldpay.card.createToken = function(a, b) {
        var c = Worldpay.card.createTokenValidate(a, b);
        if (0 != c) {
            var d = {
                reusable: Worldpay.reusable,
                paymentMethod: {
                    type: "Card",
                    name: c.name,
                    expiryMonth: c["exp-month"],
                    expiryYear: c["exp-year"],
                    cardNumber: c.number,
                    cvc: c.cvc
                },
                clientKey: Worldpay.clientKey
            };
            c["language-code"] && (d.shopperLanguageCode = c["language-code"]), Worldpay.helpers.ajax.post(Worldpay.api_path + "tokens", b, Worldpay.helpers.JSON.stringify(d))
        }
    }, Worldpay.card.createTokenValidate = function(a, b) {
        for (var c = {}, d = 0; d < a.length; d++) a[d].getAttribute("data-worldpay") && ("number" === a[d].getAttribute("data-worldpay") && (a[d].value = Worldpay.card.stripCardNumber(a[d].value)), c[a[d].getAttribute("data-worldpay")] = a[d].value);
        if (c["exp-monthyear"]) {
            if (c["exp-monthyear"].indexOf("/")) {
                var e = c["exp-monthyear"].split("/");
                e[0] && e[1] ? (c["exp-month"] = c["exp-monthyear"].split("/")[0].replace(" ", ""), c["exp-year"] = c["exp-monthyear"].split("/")[1].replace(" ", "")) : (c["exp-month"] = "", c["exp-year"] = "")
            }
            6 == c["exp-monthyear"].length && (c["exp-month"] = c["exp-monthyear"].substr(0, 2), c["exp-year"] = c["exp-monthyear"].substr(2))
        }
        c.name = c.name || "", c.number = c.number || "", c["exp-month"] = c["exp-month"] || "", c["exp-year"] = c["exp-year"] || "", c.cvc = c.cvc || "";
        var f = [];
        return Worldpay.card.validateExpiry(c["exp-month"], c["exp-year"]) || f.push({
            expiration: "Card Expiry is not valid"
        }), "basic" == Worldpay.validationType ? Worldpay.card.validateCardNumberBasic(c.number) || f.push({
            cardnumber: "Card Number is not valid"
        }) : "advanced" == Worldpay.validationType ? Worldpay.card.validateCardNumberAdvanced(c.number) || f.push({
            cardnumber: "Card Number is not valid"
        }) : f.push({
            other: "Invalid validation type"
        }), Worldpay.card.validateCardHolderName(c.name) || f.push({
            nameoncard: "Name on card is not valid"
        }), Worldpay.card.validateCVC(c.cvc) || f.push({
            cvc: "CVC is not valid"
        }), f.length ? (b(501, Worldpay.helpers.createErrorResponse(f)), !1) : (b(200, Worldpay.helpers.createErrorResponse(f)), c)
    }, Worldpay.card.reuseToken = function(a, b, c) {
        for (var d = {}, e = 0; e < a.length; e++) a[e].getAttribute("data-worldpay") && (d[a[e].getAttribute("data-worldpay")] = a[e].value);
        d.cvc = d.cvc || "", d.token = d.token || c || "";
        var f = [];
        if (Worldpay.card.validateCVC(d.cvc) || f.push({
                cvc: "CVC is not valid"
            }), "" == d.token && f.push({
                token: "Token can not be blank"
            }), f.length) return b(501, Worldpay.helpers.createErrorResponse(f)), !1;
        var g = {
            clientKey: Worldpay.clientKey,
            cvc: d.cvc
        };
        Worldpay.helpers.ajax.put(Worldpay.api_path + "tokens/" + d.token, b, Worldpay.helpers.JSON.stringify(g))
    }, Worldpay.card.stripCardNumber = function(a) {
        return a = a.replace(/[\s]/g, ""), a = a.replace(/[-]/g, "")
    }, Worldpay.card.validateCardNumberBasic = function(a) {
        return null === a ? !1 : /[^0-9]+/.test(a) ? !1 : "" === a ? !1 : !0
    }, Worldpay.card.validateCardNumberAdvanced = function(a) {
        if (null === a) return !1;
        if ("" === a) return !1;
        if (/[^0-9-\s]+/.test(a)) return !1;
        var b = a.split(" ").join("");
        if (b.length < 12 || b.length > 19) return !1;
        var c = 0,
            d = 0,
            e = !1;
        a = a.replace(/\D/g, "");
        for (var f = a.length - 1; f >= 0; f--) {
            var g = a.charAt(f),
                d = parseInt(g, 10);
            e && (d *= 2) > 9 && (d -= 9), c += d, e = !e
        }
        return c % 10 == 0
    }, Worldpay.card.validateExpiry = function(a, b) {
        if (null === a || null === b || "" === a || "" === b) return !1;
        if (/[^0-9]+/.test(a)) return !1;
        if (/[^0-9]+/.test(b)) return !1;
        if (a.length > 2) return !1;
        if (b.length > 4 || b.length < 4) return !1;
        b = b.toString();
        var c = (new Date).getMonth() + 1,
            d = (new Date).getFullYear().toString();
        return a = parseInt(a, 10), b = parseInt(b, 10), isNaN(a) || isNaN(b) ? !1 : a > 12 || 1 > a ? !1 : b > d || b == d && a >= c
    }, Worldpay.card.validateCVC = function(a) {
        return "" === a ? !0 : (a || (a = ""), -1 !== a.indexOf(" ") ? !1 : /[^0-9]+/.test(a) ? !1 : 1 == a.length || 2 == a.length ? !1 : !0)
    }, Worldpay.card.validateCardHolderName = function(a) {
        return a || (a = ""), a = a.replace(new RegExp(" ", "g"), ""), /^[a-zA-Z'\-\xAA\xB5\xBA\xC0-\xD6\xD8-\xF6\xF8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u037F\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u052F\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0-\u08B4\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0980\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0AF9\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C39\u0C3D\u0C58-\u0C5A\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D5F-\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F5\u13F8-\u13FD\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u16F1-\u16F8\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191E\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19B0-\u19C9\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FD5\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA69D\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA7AD\uA7B0-\uA7B7\uA7F7-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA8FD\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uA9E0-\uA9E4\uA9E6-\uA9EF\uA9FA-\uA9FE\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA7E-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uAB30-\uAB5A\uAB5C-\uAB65\uAB70-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC]+$/.test(a)
    }, Worldpay.card.formatCardNumber = function(a) {
        var b, c, d, e, f, g, h;
        return c = String.fromCharCode(a.which), !/^\d+$/.test(c) || (f = a.target, h = Worldpay.helpers.val(f), b = Worldpay.card.cardFromNumber(h + c), d = (h.replace(/\D/g, "") + c).length, g = 16, b && (g = b.length[b.length.length - 1]), d >= g || null != f.selectionStart && f.selectionStart !== h.length) ? void 0 : (e = b && "amex" === b.type ? /^(\d{4}|\d{4}\s\d{6})$/ : /(?:^|\s)(\d{4})$/, e.test(h) ? (a.preventDefault(), Worldpay.helpers.val(f, h + " " + c)) : e.test(h + c) ? (a.preventDefault(), Worldpay.helpers.val(f, h + c + " ")) : void 0)
    }, Worldpay.card.formatBackCardNumber = function(a) {
        var b, c;
        return b = a.target, c = Worldpay.helpers.val(b), a.meta || 8 !== a.which || null != b.selectionStart && b.selectionStart !== c.length ? void 0 : /\d\s$/.test(c) ? (a.preventDefault(), Worldpay.helpers.val(b, c.replace(/\d\s$/, ""))) : /\s\d?$/.test(c) ? (a.preventDefault(), Worldpay.helpers.val(b, c.replace(/\s\d?$/, ""))) : void 0
    }, Worldpay.card.restrictCardNumber = function(a) {
        var b, c, d, e;
        if (d = a.target, c = String.fromCharCode(a.which), /^\d+$/.test(c) && !Worldpay.helpers.hasTextSelected(d))
            if (e = (Worldpay.helpers.val(d) + c).replace(/\D/g, ""), b = Worldpay.card.cardFromNumber(e)) {
                if (!(e.length <= b.length[b.length.length - 1])) return a.preventDefault()
            } else if (!(e.length <= 16)) return a.preventDefault()
    }, Worldpay.card.formatCardNumberFromScratch = function(a) {
        var b, c, d, e;
        return (b = Worldpay.card.cardFromNumber(a)) ? (e = b.length[b.length.length - 1], a = a.replace(/\D/g, ""), a = a.slice(0, +e + 1 || 9e9), b.format.global ? null != (d = a.match(b.format)) ? d.join(" ") : void 0 : (c = b.format.exec(a), null != c && c.shift(), null != c ? c.join(" ") : void 0)) : ""
    }, Worldpay.card.reFormatCardNumber = function(a) {
        return setTimeout(function(b) {
            return function() {
                var b, c;
                return b = a.target, c = Worldpay.helpers.val(b), c = Worldpay.card.formatCardNumberFromScratch(c), Worldpay.helpers.val(b, c)
            }
        }(this))
    }, Worldpay.card.cardFromNumber = function(a) {
        var b, c, d;
        for (a = (a + "").replace(/\D/g, ""), c = 0, d = cardSettings.length; d > c; c++)
            if (b = cardSettings[c], b.pattern.test(a)) return b
    }, w.Worldpay = Worldpay
}(window);