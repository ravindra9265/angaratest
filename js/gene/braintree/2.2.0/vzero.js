var vZero = Class.create();
vZero.prototype = {
        initialize: function(e, t, i, n, r, o, s, a, d) {
            this.code = e, this.clientToken = t || !1, this.clientTokenUrl = d, this.threeDSecure = i, this.hostedFields = n, r && (this.billingName = r), o && (this.billingPostcode = o), this.billingCountryId = !1, s && (this.quoteUrl = s), a && (this.tokenizeUrl = a), this._hostedFieldsTokenGenerated = !1, this.acceptedCards = !1, this._hostedFieldsTimeout = !1, this._updateDataCallbacks = [], this._updateDataTimeout = null, this.client = !1, this.threeDSpecificCountries = !1, this.threeDCountries = [], this.threeDSecureFailedAction = 0, this.supportedCards = [], this.cardType = !1, this.initEvents()
        },
        initEvents: function() {
            this.events = {
                onBeforeUpdateData: [],
                onAfterUpdateData: [],
                onHandleAjaxRequest: [],
                integration: {
                    onInit: [],
                    onInitDefaultMethod: [],
                    onInitSavedMethods: [],
                    onShowHideOtherMethod: [],
                    onCheckSavedOther: [],
                    onPaymentMethodSwitch: [],
                    onReviewInit: [],
                    onBeforeSubmit: [],
                    onAfterSubmit: [],
                    onObserveAjaxRequests: []
                }
            }
        },
        setKount: function(e, t) {
            this.kountEnvironment = e, "" != t && (this.kountId = t)
        },
        setSupportedCards: function(e) {
            "string" == typeof e && (e = e.split(",")), this.supportedCards = e
        },
        setThreeDCountries: function(e) {
            "string" == typeof e && (e = e.split(",")), this.threeDSpecificCountries = !0, this.threeDCountries = e
        },
        setThreeDFailedAction: function(e) {
            this.threeDSecureFailedAction = e
        },
        observeEvent: function(e, t, i) {
            Array.isArray(e) || (e = [e]), e.each(function(e) {
                var n = this._resolveEvent(e);
                void 0 === n ? console.warn("Event for " + e + " does not exist.") : n.push({
                    fn: t,
                    params: i
                })
            }.bind(this))
        },
        fireEvent: function(e, t, i) {
            var n = this._resolveEvent(t);
            void 0 !== n && n.length > 0 && n.each(function(t) {
                if ("function" == typeof t.fn) {
                    var arguments = [i];
                    "object" == typeof t.params && arguments.push(t.params), t.fn.apply(e, arguments)
                }
            })
        },
        _resolveEvent: function(e) {
            return e.split(".").reduce(function(e, t) {
                return e ? e[t] : void 0
            }, this.events)
        },
        getClientToken: function(e) {
            return this.clientToken !== !1 ? e(this.clientToken) : window.braintreeClientToken ? e(window.braintreeClientToken) : void new Ajax.Request(this.clientTokenUrl, {
                method: "get",
                onSuccess: function(t) {
                    if (t && (t.responseJSON || t.responseText)) {
                        var i = this._parseTransportAsJson(t);
                        if (1 == i.success && "string" == typeof i.client_token) return this.clientToken = i.client_token, window.braintreeClientToken = i.client_token, e(this.clientToken);
                        console.error("We were unable to retrieve a client token from the server to initialize the Braintree flow."), i.error && console.error(i.error)
                    }
                }.bind(this),
                onFailure: function() {
                    console.error("We were unable to retrieve a client token from the server to initialize the Braintree flow.")
                }.bind(this)
            })
        },
        getClient: function(e) {
            this.client !== !1 ? "function" == typeof e && e(this.client) : this.getClientToken(function(t) {
                braintree.client.create({
                    authorization: t
                }, function(t, i) {
                    return t ? void console.error(t) : (this.client = i, void e(this.client))
                }.bind(this))
            })
        },
        initHostedFields: function(e) {
            return $$('iframe[name^="braintree-"]').length > 0 ? !1 : null === $("braintree-hosted-submit") ? !1 : (this.integration = e, this._hostedFieldsTokenGenerated = !1, clearTimeout(this._hostedFieldsTimeout), void(this._hostedFieldsTimeout = setTimeout(function() {
                if (this._hostedIntegration !== !1) try {
                    this._hostedIntegration.teardown(function() {
                        this._hostedIntegration = !1, this.setupHostedFieldsClient()
                    }.bind(this))
                } catch (e) {
                    this.setupHostedFieldsClient()
                } else this.setupHostedFieldsClient()
            }.bind(this), 50)))
        },
        teardownHostedFields: function(e) {
            "undefined" != typeof this._hostedIntegration && this._hostedIntegration !== !1 ? this._hostedIntegration.teardown(function() {
                this._hostedIntegration = !1, "function" == typeof e && e()
            }.bind(this)) : "function" == typeof e && e()
        },
        setupHostedFieldsClient: function() {
            return $$('iframe[name^="braintree-"]').length > 0 ? !1 : (this._hostedIntegration = !1, this.checkSubmitAfterPayment(), void this.getClient(function(e) {
                var t = {
                    client: e,
                    styles: this.getHostedFieldsStyles(),
                    fields: {
                        number: {
                            selector: "#card-number",
                            placeholder: "0000 0000 0000 0000"
                        },
                        expirationMonth: {
                            selector: "#expiration-month",
                            placeholder: "MM"
                        },
                        expirationYear: {
                            selector: "#expiration-year",
                            placeholder: "YY"
                        }
                    }
                };
                null !== $("cvv") && (t.fields.cvv = {
                    selector: "#cvv"
                }), braintree.hostedFields.create(t, function(e, t) {
                    if (e) {
                        if ("HOSTED_FIELDS_FIELD_DUPLICATE_IFRAME" == e.code) return;
                        return void console.error(e)
                    }
                    return this.hostedFieldsOnReady(t)
                }.bind(this))
            }.bind(this)))
        },
        hostedFieldsOnReady: function(e) {
            this._hostedIntegration = e, $$("#credit-card-form.loading").length && $$("#credit-card-form.loading").first().removeClassName("loading"), this.checkSubmitAfterPayment(), e.on("cardTypeChange", this.hostedFieldsCardTypeChange.bind(this))
        },
        checkSubmitAfterPayment: function() {
            if (this.integration.submitAfterPayment) {
                if (null == $("braintree-submit-after-payment")) {
                    var e = new Element("input", {
                        type: "hidden",
                        name: "payment[submit_after_payment]",
                        value: 1,
                        id: "braintree-submit-after-payment"
                    });
                    $("payment_form_gene_braintree_creditcard").insert(e)
                }
            } else $("braintree-submit-after-payment") && $("braintree-submit-after-payment").remove()
        },
        getHostedFieldsStyles: function() {
            return "function" == typeof this.integration.getHostedFieldsStyles ? this.integration.getHostedFieldsStyles() : {
                input: {
                    "font-size": "14pt",
                    color: "#3A3A3A"
                },
                ":focus": {
                    color: "black"
                },
                ".valid": {
                    color: "green"
                },
                ".invalid": {
                    color: "red"
                }
            }
        },
        hostedFieldsCardTypeChange: function(e) {
            if ("undefined" != typeof e.cards) {
                var t = {
                    visa: "VI",
                    "american-express": "AE",
                    "master-card": "MC",
                    discover: "DI",
                    jcb: "JCB",
                    maestro: "ME"
                };
                void 0 !== typeof t[e.cards[0].type] ? (this.cardType = t[e.cards[0].type], this.updateCardType(!1, this.cardType), -1 == this.supportedCards.indexOf(this.cardType) ? this.showCardUnsupported() : this.removeCardUnsupported()) : (this.removeCardUnsupported(), this.cardType = !1, this.updateCardType(!1, "card"))
            }
        },
        showCardUnsupported: function() {
            if ($$(".braintree-card-input-field").length > 0) {
                var e = $$(".braintree-card-input-field").first().up();
                if (0 == e.select(".braintree-card-unsupported").length) {
                    var t = new Element("div", {
                        "class": "braintree-card-unsupported"
                    }).update(Translator.translate("We're currently unable to process this card type, please try another card or payment method."));
                    e.insert(t)
                }
            }
        },
        removeCardUnsupported: function() {
            $$(".braintree-card-unsupported").length > 0 && $$(".braintree-card-unsupported").each(function(e) {
                e.remove()
            })
        },
        getBillingCountryId: function() {
            if (null == $("billing-address-select") || "" == $("billing-address-select").value) {
                var e = this.getBillingAddress();
                if ("undefined" != typeof e["billing[country_id]"]) return e["billing[country_id]"]
            }
            return this.billingCountryId ? this.billingCountryId : !1
        },
        shouldInvokeThreeDSecure: function() {
            if (this.threeDSpecificCountries && this.threeDCountries.length > 0) {
                var e;
                if (e = this.getBillingCountryId()) return -1 !== this.threeDCountries.indexOf(e)
            }
            return this.threeDSecure
        },
        hostedFieldsNonceReceived: function(e, t) {
            this.shouldInvokeThreeDSecure() ? ("function" == typeof this.integration.setLoading && this.integration.setLoading(), this.verify3dSecureNonce(e, {
                onSuccess: function(e) {
                    this.updateNonce(e.nonce), "function" == typeof t.onSuccess && t.onSuccess()
                }.bind(this),
                onFailure: function() {
                    "function" == typeof t.onFailure && t.onFailure()
                }.bind(this)
            })) : (this.updateNonce(e), "function" == typeof t.onSuccess && t.onSuccess())
        },
        updateNonce: function(e) {
            $("creditcard-payment-nonce").value = e, $("creditcard-payment-nonce").setAttribute("value", e), "function" == typeof this.integration.resetLoading && this.integration.resetLoading(), this._hostedFieldsTokenGenerated = !0
        },
        hostedFieldsError: function(e) {
            return "function" == typeof this.integration.resetLoading && this.integration.resetLoading(), "undefined" != typeof e.message && -1 == e.message.indexOf("Cannot place two elements in") && -1 == e.message.indexOf("Unable to find element with selector") && -1 == e.message.indexOf("User did not enter a payment method") && alert(e.message), this._hostedFieldsTokenGenerated = !1, "function" == typeof this.integration.afterHostedFieldsError && this.integration.afterHostedFieldsError(e.message), !1
        },
        usingSavedCard: function() {
            return void 0 != $("creditcard-saved-accounts") && void 0 != $$("#creditcard-saved-accounts input:checked[type=radio]").first() && "other" !== $$("#creditcard-saved-accounts input:checked[type=radio]").first().value
        },
        usingSavedThreeDCard: function() {
            return this.usingSavedCard() && $$("#creditcard-saved-accounts input:checked[type=radio]").first().hasAttribute("data-threedsecure-nonce")
        },
        setThreeDSecure: function(e) {
            this.threeDSecure = e
        },
        setAmount: function(e) {
            this.amount = parseFloat(e)
        },
        setBillingName: function(e) {
            this.billingName = e
        },
        getBillingName: function() {
            return "object" == typeof this.billingName ? this.combineElementsValues(this.billingName) : this.billingName
        },
        setBillingPostcode: function(e) {
            this.billingPostcode = e
        },
        getBillingPostcode: function() {
            if ("string" == typeof this.billingPostcode) return this.billingPostcode;
            if ("object" == typeof this.billingPostcode) return this.combineElementsValues(this.billingPostcode);
            var e = this.getBillingAddress();
            return "undefined" != typeof e["billing[postcode]"] ? e["billing[postcode]"] : null
        },
        setAcceptedCards: function(e) {
            this.acceptedCards = e
        },
        getBillingAddress: function() {
            if ("function" == typeof this.integration.getBillingAddress) return this.integration.getBillingAddress();
            var e = {};
            return null !== $("co-billing-form") ? e = "FORM" == $("co-billing-form").tagName ? $("co-billing-form").serialize(!0) : this.extractBilling($("co-billing-form").up("form").serialize(!0)) : null !== $("billing:firstname") && (e = this.extractBilling($("billing:firstname").up("form").serialize(!0))), e ? e : void 0
        },
        extractBilling: function(e) {
            var t = {};
            return $H(e).each(function(e) {
                0 == e.key.indexOf("billing") && -1 == e.key.indexOf("password") && (t[e.key] = e.value)
            }), t
        },
        getAcceptedCards: function() {
            return this.acceptedCards
        },
        combineElementsValues: function(e, t) {
            t || (t = " ");
            var i = [];
            return e.each(function(e, t) {
                void 0 !== $(e) && (i[t] = $(e).value)
            }), i.join(t)
        },
        updateCardType: function(e, t) {
            if (void 0 != $("card-type-image")) {
                var i = $("card-type-image").src.substring(0, $("card-type-image").src.lastIndexOf("/"));
                $("card-type-image").setAttribute("src", i + "/" + t + ".png")
            }
        },
        observeAjaxRequests: function(e, t) {
            return vZero.prototype.observingAjaxRequests ? !1 : (vZero.prototype.observingAjaxRequests = !0, Ajax.Responders.register({
                onComplete: function(i) {
                    return this.handleAjaxRequest(i.url, e, t)
                }.bind(this)
            }), void(window.jQuery && jQuery(document).ajaxComplete(function(i, n, r) {
                return this.handleAjaxRequest(r.url, e, t)
            }.bind(this))))
        },
        handleAjaxRequest: function(e, t, i) {
            if ("undefined" != typeof i && i instanceof Array && i.length > 0) {
                var n = !1;
                if (i.each(function(t) {
                        e && -1 != e.indexOf(t) && (n = !0)
                    }), n === !0) return !1
            }
            e && -1 == e.indexOf("/braintree/") && (this.fireEvent(this, "onHandleAjaxRequest", {
                url: e
            }), t ? t(e) : this.updateData())
        },
        updateData: function(e, t) {
            this._updateDataCallbacks.push(e), clearTimeout(this._updateDataTimeout), this._updateDataTimeout = setTimeout(function() {
                var e = this._updateDataCallbacks;
                this._updateDataCallbacks = [], this.fireEvent(this, "onBeforeUpdateData", {
                    params: t
                }), new Ajax.Request(this.quoteUrl, {
                    method: "post",
                    parameters: t,
                    onSuccess: function(t) {
                        if (t && (t.responseJSON || t.responseText)) {
                            var i = this._parseTransportAsJson(t);
                            void 0 != i.billingName && (this.billingName = i.billingName), void 0 != i.billingPostcode && (this.billingPostcode = i.billingPostcode), void 0 != i.billingCountryId && (this.billingCountryId = i.billingCountryId), void 0 != i.grandTotal && (this.amount = i.grandTotal), void 0 != i.threeDSecure && this.setThreeDSecure(i.threeDSecure), "undefined" != typeof vzeroPaypal && void 0 != i.grandTotal && void 0 != i.currencyCode && vzeroPaypal.setPricing(i.grandTotal, i.currencyCode), e.length > 0 && e.each(function(e) {
                                e(i)
                            }.bind(this)), this.fireEvent(this, "onAfterUpdateData", {
                                response: i
                            })
                        }
                    }.bind(this),
                    onFailure: function() {}.bind(this)
                })
            }.bind(this), 250)
        },
        tokenize3dSavedCards: function(e) {
            if (this.threeDSecure)
                if (void 0 !== $$("[data-token]").first()) {
                    var t = [];
                    $$("[data-token]").each(function(e, i) {
                        t[i] = e.getAttribute("data-token")
                    }), new Ajax.Request(this.tokenizeUrl, {
                        method: "post",
                        onSuccess: function(t) {
                            if (t && (t.responseJSON || t.responseText)) {
                                var i = this._parseTransportAsJson(t);
                                i.success && $H(i.tokens).each(function(e) {
                                    void 0 != $$('[data-token="' + e.key + '"]').first() && $$('[data-token="' + e.key + '"]').first().setAttribute("data-threedsecure-nonce", e.value)
                                }), e && e(i)
                            }
                        }.bind(this),
                        parameters: {
                            tokens: Object.toJSON(t)
                        }
                    })
                } else e();
            else e()
        },
        verify3dSecureNonce: function(e, t) {
            this.getClient(function(i) {
                braintree.threeDSecure.create({
                    client: i
                }, function(i, n) {
                    if (i) return void console.error(i);
                    var r = {
                        amount: this.amount,
                        nonce: e,
                        addFrame: function(e, t) {
                            $$("#three-d-modal .bt-modal-body").first().insert(t), $("three-d-modal").removeClassName("hidden")
                        },
                        removeFrame: function() {
                            $$("#three-d-modal .bt-modal-body iframe").first().remove(), $("three-d-modal").addClassName("hidden")
                        }.bind(this)
                    };
                    n.verifyCard(r, function(e, i) {
                        e ? t.onFailure && t.onFailure(i, e) : i.liabilityShifted ? t.onSuccess && t.onSuccess(i) : 1 == this.threeDSecureFailedAction ? t.onFailure && t.onFailure(i, Translator.translate("Your payment has failed 3D secure verification, please try an alternate payment method.")) : t.onSuccess && t.onSuccess(i)
                    }.bind(this))
                }.bind(this))
            }.bind(this))
        },
        verify3dSecureVault: function(e) {
            var t = $$("#creditcard-saved-accounts input:checked[type=radio]").first().getAttribute("data-threedsecure-nonce");
            t ? this.verify3dSecureNonce(t, {
                onSuccess: function(t) {
                    $("creditcard-payment-nonce").removeAttribute("disabled"), $("creditcard-payment-nonce").value = t.nonce, $("creditcard-payment-nonce").setAttribute("value", t.nonce), "function" == typeof e.onSuccess && e.onSuccess()
                },
                onFailure: function(t, i) {
                    alert(i), "function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1)
                }
            }) : (alert("No payment nonce present."), "function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1))
        },
        processCard: function(e) {
            var t = this.getBillingPostcode(),
                i = {};
            t && (i = {
                billingAddress: {
                    postalCode: t
                }
            }), this._hostedIntegration.tokenize(i, function(t, i) {
                return t ? ("function" == typeof e.onFailure ? e.onFailure() : checkout.setLoadWaiting(!1), void("string" == typeof t.message && alert(t.message))) : this.hostedFieldsNonceReceived(i.nonce, e)
            }.bind(this))
        },
        shouldInterceptCreditCard: function() {
            return "0.00" != this.amount
        },
        shouldInterceptPayPal: function() {
            return !0
        },
        process: function(e) {
            return e = e || {}, this._hostedFieldsTokenGenerated || this.usingSavedCard() && !this.usingSavedThreeDCard() ? void("function" == typeof e.onSuccess && e.onSuccess()) : this.usingSavedThreeDCard() ? this.verify3dSecureVault(e) : this.processCard(e)
        },
        creditCardLoaded: function() {
            return !1
        },
        paypalLoaded: function() {
            return !1
        },
        _parseTransportAsJson: function(transport) {
            return transport.responseJSON && "object" == typeof transport.responseJSON ? transport.responseJSON : transport.responseText ? "object" == typeof JSON && "function" == typeof JSON.parse ? JSON.parse(transport.responseText) : eval("(" + transport.responseText + ")") : {}
        }
    },
    function() {
        for (var e, t = function() {}, i = ["assert", "clear", "count", "debug", "dir", "dirxml", "error", "exception", "group", "groupCollapsed", "groupEnd", "info", "log", "markTimeline", "profile", "profileEnd", "table", "time", "timeEnd", "timeStamp", "trace", "warn"], n = i.length, r = window.console = window.console || {}; n--;) e = i[n], r[e] || (r[e] = t)
    }();