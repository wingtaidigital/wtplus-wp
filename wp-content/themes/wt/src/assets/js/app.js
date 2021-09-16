(function ($) {
    $(document).foundation();

    $('#login-modal, #showcase-login-modal').on('open.zf.reveal', function (e) {
        if (typeof grecaptcha === 'undefined')
            return;

        let $container = $(this).find('.g-recaptcha');

        if (!$container.length || !$container.is(':empty'))
            return;

        grecaptcha.render($container[0], {sitekey: $container.data('sitekey')});
    });


    // $(window).on('beforeunload', function(){
    // 	$('#login-modal').foundation('open');
    // 	return "Do you really want to leave this page?";
    // });

    if (!Modernizr.touchevents) {
        let $zoom = $('.wt-zoom');

        if ($zoom.length) {
            $zoom.on('mousemove touchmove', function (e) {
                $(this).next().removeClass('hide');
            });

            $zoom.on('mousemove', function (e) {
                $(this).next()[0].style.backgroundPosition = -e.offsetX + "px " + -e.offsetY + "px";
            });

            $zoom.on('touchmove', function (e) {
                let touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
                let elm = $(this).offset();
                let x = touch.pageX - elm.left;
                let y = touch.pageY - elm.top;

                $(this).next()[0].style.backgroundPosition = -x + "px " + -y + "px";
            });

            $zoom.on('mouseleave touchend', function (e) {
                $(this).next().addClass('hide');
            });
        }
    }


    // if (typeof wpApiSettings === 'undefined')
    // 	return;
    //
    // $.ajax(
    // {
    // 	url: wpApiSettings.root + 'wt/v1/nonce',
    // 	type: 'GET'
    // })
    // .done(function(response)
    // {
    // 	wpApiSettings.nonce = response;
    // 	console.log(wpApiSettings);
    // })


    // if ($('.wt-no-history').length && history.replaceState)
    // {
    // 	let url = document.referrer;
    //
    // 	if (url === '')
    // 		url = '../';
    //
    // 	history.replaceState({}, '', url);
    // }

    // $('form').trigger('reset');

})(jQuery);
