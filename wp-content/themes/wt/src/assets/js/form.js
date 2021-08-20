(function ($) {
    if (typeof wpApiSettings === 'undefined')
        return;


    let $input = $(':input');

    if (!$input.length)
        return;

    function error($field, message, state) {
        // console.log($field);

        if (!$field.length || !$field.is(':input'))
            return;

        let $error = $field.nextAll('.form-error');

        if (!$error.length) {
            $error = $field.closest('fieldset').find('.form-error');
        }

        let $message;
        let validityState = state;

        if (!message) {
            if (!$error.length)
                return;

            let field = $field[0];

            if (field.name == '' && $.fn.getNativeElement) {
                field = $field.getNativeElement();
                field = field[0];
            }

            //console.log(field.name, field.validity);
            for (let p in field.validity) {
                if (p == 'valid')
                    continue;

                if (field.validity[p] || p == state) {
                    // console.log($field[0].name, p, state);
                    $message = $error.find('.' + p);
                    validityState = p;

                    if (!$message.length)
                        continue;

                    message = $message.html();

                    // console.log($field[0].name, p, message);

                    if (p != 'customError')
                        break;
                }
            }
            // console.log($message);
            if (!message)
                message = field.validationMessage;
            // return;
        }

        if (message.indexOf('<a') > -1) {
            let $text = $("<div/>").html(message);

            $text.find('a').remove();

            message = $text.text();
        }

        if ($error.length) {
            $error.addClass('is-visible');
            $error.children().removeClass('is-visible');

            if (!$message || !$message.length) {
                $message = $error.find('.' + validityState);

                if ($message.length && message.length)
                    $message.html(message);
            }

            if ($message && $message.length) {
                $message.addClass('is-visible');
            } else if (message)//if (validityState)
            {
                $error.append('<span class="is-visible ' + validityState + '">' + message + '</span>');
            }
            // else
            // {
            // 	console.log($field[0].name, $field[0].validity);
            // }
        }

        let $parent = $field.closest('label');

        if (!$parent.length) {
            $parent = $field.closest('fieldset');
        }

        $parent.addClass('is-invalid-label');

        if ($.fn.setCustomValidity)
            $field.setCustomValidity(message);
        else
            $field[0].setCustomValidity(message);

        // $.webshims.validityAlert.showFor( $field, message );
        // $field.next().addClass('is-visible');

        // if (typeof $field[0].reportValidity !== 'undefined' && $field.is(':visible'))
        // 	$field[0].reportValidity();
    }

    function success($field) {
        let $parent = $field.closest('label');

        if (!$parent.length) {
            $parent = $field.closest('fieldset');
        }

        let $error = $field.nextAll('.form-error');

        if (!$error.length) {
            $error = $field.closest('fieldset').find('.form-error');
        }

        if ($.fn.setCustomValidity)
            $field.setCustomValidity('');
        else
            $field[0].setCustomValidity('');

        $parent.removeClass('is-invalid-label');
        $error.removeClass('is-visible');
    }


    let $header = $('body > header');

    function isElementInViewport(el) {
        // console.log(el, el instanceof jQuery);
        //special bonus for those using jQuery
        if (typeof jQuery === "function" && el instanceof jQuery) {
            el = el[0];
        }

        let rect = el.getBoundingClientRect();

        return (
            rect.top >= $header.outerHeight() &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && /*or $(window).height() */
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) /*or $(window).width() */
        );
    }

    function scrollTo($ele) {
        if (isElementInViewport($ele))
            return;
        // console.log($ele.offset().top, $header.outerHeight(), $ele.offset().top - $header.outerHeight());
        $('html, body').animate(
            {
                scrollTop: ($ele.offset().top - $header.outerHeight()) + 'px'
            }, 'fast');
        // console.log($('html, body').scrollTop());
    }


    let $exists = $('.wt-exists');

    if ($exists.length) {
        function exists() {
            if (this.validity.valueMissing || this.validity.typeMismatch || this.validity.patternMismatch)
                return;

            let $field = $(this);
            let route = $field.data('route');

            if (route === undefined)
                return;

            let value = encodeURIComponent(this.value);
            // let exclude = $field.data('exclude');
            let settings = {
                type: 'POST',
                url: wpApiSettings.root + route + value
            };

            if (typeof wpApiSettings.nonce !== 'undefined') {
                settings.beforeSend = function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                };
            }

            // if ($field.hasClass('wt-exists'))
            // {
            // 	settings.url = wpApiSettings.root + 'wt/v1/email-exists';
            // 	settings.data = 'email=' + value;
            // }
            // else
            // {
            // 	settings.url = wpApiSettings.root + 'wt/v1/crm/exists';
            // 	settings.data = 'nric_or_email=' + value;
            // }

            // if (exclude !== undefined)
            // 	settings.data = 'exclude=' + exclude;
            // settings.data += '&exclude=' + exclude;

            $.ajax(settings)
                .done(function (response) {
                    if (response) {
                        error($field, null, 'customError');
                        // let $message = $("<div/>").html($field.next().html());
                        //
                        // $message.find('a').remove();
                        //
                        // $field.setCustomValidity($message.text());
                        // // $field.next().html(message);
                        // $field.closest('label').addClass('is-invalid-label');
                        // // $.webshims.validityAlert.showFor( $field, message );
                        // // $field.next().addClass('is-visible');
                        //
                        // if (typeof $field[0].reportValidity !== 'undefined')
                        // 	$field[0].reportValidity();
                        // else
                        // 	$field.data('errormessage', {customError: 'qwe'});
                    } else {
                        success($field);
                        // $field.setCustomValidity('');
                        // $field.closest('label').removeClass('is-invalid-label');
                        // $field.next().removeClass('is-visible');
                    }
                })
        }

        // $('.wt-exists, .wt-crm-exists').blur(function()
        // $('.wt-exists, .wt-crm-exists').on('input', function()
        $exists.on('input', Foundation.util.throttle(exists, 1000));
    }


    // let scrolled = false;
    //
    // function scrollToInvalid(e)
    // {
    // 	// console.log(e.target);
    //
    // 	if (!scrolled)
    // 		scrollTo($(e.target));
    //
    // 	scrolled = true;
    // 	// $input.off('invalid', scrollToInvalid);
    // }
    //
    // $input.closest('form').find('[type=submit]').click(function()
    // {
    // 	scrolled = false;
    // });

    // $input.on('input', function()
    // {
    // 	success($(this));
    // });

    function renderValidity(e) {
        // if (this.validity.customError)
        // 	return;
        // scrolled = false;
        // let $this = $(this);
        let t = e.target;
        let $this = $(t);
        // console.log(e.target.name, t.validity.valid);
        if ($this.is(':hidden'))
            $this = $this.getShadowElement();
        //
        // if (!$this.length)
        // {
        // 	// success($(this));
        // 	return;
        // }

        if (t.validity.valid) {
            success($this);
            return;
        }

        // let bail = false;

        for (let p in t.validity) {
            if (t.validity[p] && p != 'customError') {
                // if (p === 'customError')
                // {
                // 	bail = true;
                // }
                // else
                // {
                error($this, null, p);
                return;
                // }

            }
        }

        // if (bail)
        // {
        // 	error($this, null, 'customError');
        // 	return;
        // }

        success($this);

        // $input.on('invalid', scrollToInvalid);

        // if (this.validity.valid)
        // {
        // 	// console.log(this.name, this.validity);
        // 	success($this);
        // 	return;
        // }
        // // for (let p in this.validity)
        // // {
        // // 	if (p)
        // // 	{
        // // 		let $message = $(this).next('.form-error').find('.' + p);
        // //
        // // 		if (!$message.length)
        // // 			continue;
        // //
        // // 		error($(this), $message.html());
        // //
        // // 		return;
        // // 	}
        // // }
        //
        // error($this);

        // $this.closest('label').addClass('is-invalid-label');
        // console.log(this.name, this.validity);
    }

    $input.on('change', renderValidity);
    $('input:invalid').on('input', renderValidity); //unsupported pseudo: invalid
    // $input.on('blur', Foundation.util.throttle(renderValidity, 1000));//.on('invalid', scrollToInvalid);


    $input.on('invalid', function (e) {
        if ($(this).is(':visible')) // filter doesn't work
            return;

        if ($(this).closest('.hide').length) {
            $(this).prop('disabled', true);
        }
        // else
        // {
        // 	$(this).prop('required', false);
        // 	// this.setCustomValidity('');
        // }
        // console.log(this.validity);
    });


    $input.closest('form').find('[type=submit]').click(function () {
        let $field = $(this).closest('form').find('input:invalid, select:invalid').filter(':visible').first();

        if ($field.length) {
            setTimeout(function () {
                scrollTo($field);
                // error($field);
            }, 1000);
        }
    });


    $('fieldset input:checkbox').on('change', function () {
        let $checkbox = $(this).siblings(':checkbox').addBack();

        success($checkbox);
    });


    $('[type=date]').on('firstinvalid', function (e) // prevent webshim bubble
    {
        let $shadow = $(e.target).getShadowElement();

        if (!$shadow.length)
            return;

        e.stopImmediatePropagation();

        error($shadow, $(e.target).getErrorMessage());
    });


    if (typeof goog !== 'undefined')
        goog.require('i18n.phonenumbers.PhoneNumberUtil');

    function isPossibleNumber(number) {
        if (typeof i18n === 'undefined')
            return true;

        let phoneUtil = i18n.phonenumbers.PhoneNumberUtil.getInstance();
        // let phoneNumber = phoneUtil.parseAndKeepRawInput(number, 'sg');
        // phoneNumber.setCountryCode(countryCode);
        // let isPossible = phoneUtil.isPossibleNumber(phoneNumber);
        return phoneUtil.isPossibleNumberString(number);
    }


    let $mobile = $('.wt-mobile');

    if ($mobile.length) {
        let $countryCode = $mobile.find('select');
        let $number = $mobile.find('input');

        function checkMobile() {
            if (this.validity.valueMissing || this.validity.typeMismatch || this.validity.patternMismatch || this.validity.tooShort || this.validity.tooLong)
                return;

            let countryCode = $countryCode.val();

            if (countryCode === '')
                return;

            let number = $number.val();

            if (!number.length)
                return;

            if (countryCode === "65") {
                var re = /^[89]\d{7}/;
                if (!(re.test(number))) {
                    error($number, 'Please enter a valid number', 'badInput');
                    return;
                }
            }

            if (countryCode != "65") {
                if (!isPossibleNumber('+' + countryCode + number)) {
                    // console.log('isPossibleNumber');
                    error($number, 'Please enter a valid number', 'badInput');
                    return;
                }
            }

            let route = 'wt/v1/crm/mobile-exists';
            let data = 'MobileNumberCountryCode=' + countryCode + '&MobileNumber=' + number;
            let exclude = $(this).data('exclude');

            if (exclude && exclude !== '')
                data += '&exclude=' + exclude;

            let settings = {
                type: 'POST',
                url: wpApiSettings.root + route,
                data: data
            };

            if (typeof wpApiSettings.nonce !== 'undefined') {
                settings.beforeSend = function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                };
            }

            $.ajax(settings)
                .done(function (response) {
                    if (response) {
                        error($number, null, 'customError');
                    } else {
                        success($number);
                    }
                })
        }

        $countryCode.on('change', checkMobile);
        $number.on('input', Foundation.util.throttle(checkMobile, 1000));
    }


    let $receipt = $('.wt-receipt');

    if ($receipt.length) {
        let $receiptFields = $receipt.find('input');
        let $number = $receiptFields.filter('[type=text]');
        let $date = $receiptFields.filter('[type=date]');
        let $amount = $receiptFields.filter('[type=number]');

        // function allFieldsFilled($fieldset)
        // {
        // 	let $fields = $fieldset.find(':input');
        // 	let filled = true;
        //
        // 	$fields.each(function()
        // 	{
        // 		if ($(this).val() === '')
        // 		{
        // 			filled = false;
        // 			return false;
        // 		}
        // 	});
        //
        // 	return filled;
        // }

        function resetValidity() {
            $receiptFields.each(function () {
                let $this = $(this);

                if ($this.is(':hidden'))
                    $this = $this.getShadowElement();

                success($this);
                // $(this).setCustomValidity('');
            });
        }

        function allOrNoneRequired() {
            // console.log('allOrNoneRequired');
            let filled = false;
            let empty = false;

            // resetValidity();

            $receiptFields.each(function () {
                if ($(this).val() === '')
                    empty = true;
                else
                    filled = true;

                // success($(this));
                // $(this).setCustomValidity('');
            });

            if (empty && filled) {
                $receiptFields.prop('required', true);

                // $fields.each(function()
                // {
                // 	$(this)[0].checkValidity();
                // });
            } else {
                $receiptFields.prop('required', false);

                if (empty)
                    resetValidity();
            }

            // if (empty && !filled)
            // {
            // 	$fields.each(function()
            // 	{
            // 		success($(this));
            // 	});
            // }
        }

        function isReceipt() {
            if (this.validity.valueMissing || this.validity.typeMismatch || this.validity.rangeUnderflow)
                return;

            // resetValidity();

            let number = $number.val();

            if (number === '')
                return;

            let date = $date.val();

            if ($date.val() === '')
                return;

            let amount = $amount.val();

            if ($amount.val() === '')
                return;

            let route = 'wt/v1/crm/is-receipt';
            let data = 'ReceiptId=' + number + '&TransactionDate=' + date + '&SalesAmount=' + amount;

            let settings = {
                type: 'POST',
                url: wpApiSettings.root + route,
                data: data
            };

            if (typeof wpApiSettings.nonce !== 'undefined') {
                settings.beforeSend = function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
                };
            }

            $.ajax(settings)
                .done(function (response) {
                    // console.log(response);
                    if (typeof response.Status === 'undefined')
                        return;

                    switch (response.Status) {
                        case 'VALID':
                            success($number);
                            break;

                        case 'INVALID':
                            error($number, 'Receipt is invalid', 'customError');
                            break;

                        case 'TAGGED':
                            error($number, 'Receipt has already been registered', 'customError');
                            break;
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    if (typeof jqXHR.responseJSON.code !== 'undefined' && jqXHR.responseJSON.code == 'wt_invalid_date')
                        error($date, null, 'rangeUnderflow');
                    else
                        error($number, 'Error occurred. Please proceed without receipt or try again later.', 'customError');
                });
        }

        $receiptFields.on('input change', allOrNoneRequired);
        $receiptFields.on('input', Foundation.util.throttle(isReceipt, 1000));
        $receiptFields.on('change', isReceipt);
        // $receiptFields.off('input change', renderValidity);

        // $receiptFields.on('blur', function()
        // {
        // 	if ($(document.activeElement).closest('.wt-receipt').length)
        // 		return;
        //
        // 	$(this).trigger('change');
        // });

        // $receiptFields.closest('form').find('[type=submit]').click(function()
        // {
        // 	allOrNoneRequired();
        // });
    }


    $(document.body).on('submit', 'form[data-route]', function (e) {
        e.preventDefault();

        let $form = $(this);
        let confirmMessage = $form.data('confirm');

        if (confirmMessage && confirmMessage.length) {
            if (!confirm(confirmMessage))
                return false;
        }

        // let $invalid = $form.find('.is-invalid-label');
        //
        // $invalid.each(function()
        // {
        // 	let $field = $(this).find(':input');
        // 	$.webshims.validityAlert.showFor( $field, $field.attr('customValidationMessage') );
        //
        // 	return false;
        // });


        let $mobile = $form.find('.wt-mobile');

        if ($mobile.length) {
            let $countryCode = $mobile.find('select');
            let $number = $mobile.find('input');

            let countryCode = $countryCode.val();

            // if (countryCode === '')
            // 	return;

            let number = $number.val();

            // if (number.length < 5)
            // 	return;

            if (!isPossibleNumber('+' + countryCode + number)) {
                error($number, 'Please enter a valid number', 'badInput');
                return false;
            }
        } else {
            let $tel = $form.find('[type="tel"]');
            let bail = false;

            $tel.each(function () {
                let number = this.value;

                if (number[0] !== '+')
                    number = '+65' + number;

                if (!isPossibleNumber(number)) {
                    error($(this), 'Please enter a valid number', 'patternMismatch');
                    bail = true;
                    return false;
                }
            });

            if (bail)
                return false;
        }


        let $passwords = $form.find('[type=password]');

        if ($passwords.length >= 2) {
            let pass1 = $passwords[$passwords.length - 2];
            // let $error = $(pass1).next();

            // if (typeof wtPasswordSettings  !== 'undefined')
            // {
            // 	// if (wtPasswordSettings.hasOwnProperty('password_min_length') && wtPasswordSettings.password_min_length)
            // 	// {
            // 	// 	// wtPasswordSettings.password_min_length.length = parseInt(wtPasswordSettings.password_min_length);
            // 	// 	// console.log(pass1.value.length, wtPasswordSettings.password_min_length.len);
            // 	// 	if (pass1.value.length < wtPasswordSettings.password_min_length.len)
            // 	// 	{
            // 	// 		error($(pass1), wtPasswordSettings.password_min_length.error);
            // 	// 		// $error.text('Password must have at least ' + wtPasswordSettings.password_min_length +' characters.');
            // 	// 		// $error.addClass('is-visible');
            // 	//
            // 	// 		return false;
            // 	// 	}
            // 	//
            // 	// }
            //
            // 	if (wtPasswordSettings.hasOwnProperty('password_complexity') && wtPasswordSettings.password_complexity)
            // 	{
            // 		let regex = new RegExp(wtPasswordSettings.password_complexity.regex);
            //
            // 		if (!regex.test(pass1.value))
            // 		{
            // 			error($(pass1), wtPasswordSettings.password_complexity.error);
            //
            // 			return false;
            // 		}
            // 	}
            // }

            let pass2 = $passwords[$passwords.length - 1];

            if (pass1.value !== pass2.value) {
                error($(pass2), null, 'customError');
                // $error.text('Passwords must match.');
                // $error.addClass('is-visible');

                return false;
            }
        }


        let $required = $form.find('.wt-required').filter(':visible');

        if ($required.length) {
            let $checkbox = $required.find(':checkbox');

            if ($checkbox.length) {
                // console.log($checkbox.filter(':checked'));
                if (!$checkbox.filter(':checked').length) {
                    // console.log($checkbox.filter(':last-of-type'));
                    error($checkbox.filter(':last-of-type'), null, 'valueMissing');
                    return false;
                }
            }
        }


        // let $agreed = $form.find('[name=agreed]');
        //
        // if ($agreed.length && !$agreed.prop( "checked" ))
        // {
        // 	$agreed.next('.form-error').addClass('is-visible');
        //
        // 	return false;
        // }

        let $callout = $form.find('.callout');
        let $submit = $form.find('[type=submit]');
        let $paged = $form.find('[name="paged"]');
        let text = {
            submit: 'Submit',
            submitting: 'Submitting',
            submitted: 'Submitted'
        };

        for (let t in text) {
            let data = $submit.data(t);

            if (data === undefined)
                continue;

            text[t] = data;
        }

        $submit.prop('disabled', true);
        $submit.text(text.submitting);

        $callout.addClass('hide');

        let settings = {
            url: wpApiSettings.root + $form.data('route'),
            type: 'POST',
            dataType: 'json',
            data: $form.serialize()
        };

        if (typeof wpApiSettings.nonce !== 'undefined') {
            settings.beforeSend = function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
            };
        }

        $.ajax(settings)
            .done(function (response) {
                // console.log(response);

                if (typeof response === 'string' && response.indexOf('http') === 0) {
                    window.location = response;
                } else {
                    if (response && typeof response === 'object' && response.hasOwnProperty('paged') && response.hasOwnProperty('html')) {
                        $('#' + $form.data('content')).append(response.html);

                        if (parseInt(response.paged) > 0)
                            $paged.val(response.paged);
                        else
                            $form.addClass('hide');
                    } else {
                        if ($form.data('hide')) {
                            let $parent = $form.closest('.wt-not-submitted');

                            $parent.addClass('hide');
                            $parent.next('.wt-submitted').removeClass('hide');
                        } else if ($callout.length) {
                            $callout.html($callout.data('success')).removeClass('hide alert').addClass('success');
                            // $callout.attr('class', 'callout success');
                            scrollTo($callout);
                        }
                    }
                }

                $submit.text(text.submitted);

                if ($submit.data('enable'))
                    $submit.prop('disabled', false);
            })
            .fail(function (jqXHR, textStatus, errorThrown) {
                // console.log(jqXHR);
                // console.log(textStatus);
                // console.log(errorThrown);

                $submit.text(text.submit);
                $submit.prop('disabled', false);

                if ($callout.length) {
                    let message = 'Error occurred. Please try again';

                    if (typeof jqXHR.responseJSON !== 'undefined') {
                        if (jqXHR.responseJSON.code == "rest_cookie_invalid_nonce")
                            message = 'Session timeout. Please refresh and try again.';
                        else
                            message = jqXHR.responseJSON.message;
                    }

                    $callout.html(message).removeClass('hide success').addClass('alert');
                    ;
                    // $callout.attr('class', 'callout alert');
                    scrollTo($callout);
                } else if ($paged.length) {
                    $form.addClass('hide');
                }
            });
    });


    // $(':input').on('invalid', function(e)
    // {
    // 	if ($(this).is(':hidden'))
    // 	{
    // 		$(this).prop('disabled', true);
    // 		success($(this));
    // 	}
    // });


    // $('[type=password]').on('input', function()
    // {
    // 	if (typeof wtPasswordSettings === 'undefined')
    // 		return;
    //
    // 	// console.log(this.validity);
    //
    // 	if (this.validity.tooShort)
    // 	{
    // 		if (typeof wtPasswordSettings.password_min_length  !== 'undefined')
    // 			error($(this), wtPasswordSettings.password_min_length.error);
    // 			// $(this).setCustomValidity(wtPasswordSettings.password_min_length.error);
    //
    // 		return;
    // 	}
    //
    // 	if (this.validity.patternMismatch)
    // 	{
    // 		if (typeof wtPasswordSettings.password_complexity  !== 'undefined')
    // 			error($(this), wtPasswordSettings.password_complexity.error);
    //
    // 		return;
    // 	}
    //
    // 	success($(this));
    //
    // 	// if (this.validity.valid)
    // 	// {
    // 	// 	success($(this));
    // 	// 	return;
    // 	// }
    // });


    // $(document.body).on('change', '.is-invalid-label :input', function()
    // {
    // 	success($(this));
    // });
})(jQuery);
