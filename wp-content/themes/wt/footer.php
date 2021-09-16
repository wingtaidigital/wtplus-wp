<?php defined('ABSPATH') || exit; ?>
</main>
<footer>
    <div class="row  align-justify">
        <div class="column -small-12 -medium-3 wt-gutter-x2 wt-gutter-bottom-x2">
            <a href="http://www.wingtaiasia.com.sg/Home/" target="_blank" rel="noreferrer">
                <img src="<?php echo get_theme_file_uri('/assets/img/wing-tai-logo-black.png'); ?>" alt="Wing Tai Asia Retail" style="width: 200px; margin-bottom: 0;">
            </a>
            <small>&copy; <?php echo date('Y'); ?> Wing Tai Asia Retail. All rights reserved</small>
        </div>

        <section class="column shrink wt-gutter-x2 wt-gutter-bottom">
            <h1>Corporate</h1>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'corporate',
                'menu_class' => 'no-bullet',
            ));
            ?>
        </section>
        <section class="column shrink wt-gutter-x2 wt-gutter-bottom">
            <h1>Helpdesk</h1>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'help-desk',
                'menu_class' => 'no-bullet',
            ));
            ?>
        </section>
        <section class="column shrink wt-gutter-x2">
            <h1>Follow us on</h1>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'social',
                'menu_class' => 'menu simple',
                'container_class' => 'wt-margin-bottom',
            ));
            ?>

            <!--<a href='https://play.google.com/store/apps/details?id=com.memberson.wingtaiplus&hl=en&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1' target="_blank" rel="noreferrer"><img alt='Get it on Google Play' src='<?php /*echo get_theme_file_uri('/assets/img/google-play-badge-small.png'); */ ?>' srcset="<?php /*echo get_theme_file_uri('/assets/img/google-play-badge.png'); */ ?> 2x" width="135"></a>
					<a href="https://itunes.apple.com/th/app/wt/id1049653732?mt=8" target="_blank" rel="noreferrer"><img src="<?php /*echo get_theme_file_uri('/assets/img/Download_on_the_App_Store_Badge_US-UK_135x40.svg'); */ ?>" alt="Download on the AppStore" width="135"></a>-->
        </section>
    </div>
</footer>

<?php
if (!wt_is_user_logged_in()) {
    $query = new WP_Query([
        'post_type' => 'wt_notification',
        'post_status' => 'publish',
        'name' => 'sign-up'
    ]);

    if ($query->have_posts()) {
        $now = time();

        if (!isset($_SESSION['wt_prompt_at'])) {
            $_SESSION['wt_prompt_at'] = strtotime("+5 seconds");
        }

        if ($_SESSION['wt_prompt_at'] >= $now) {
            while ($query->have_posts()) {
                $query->the_post();
                ?>

                <section class="reveal text-center" id="sign-up-prompt" data-reveal>

                    <?php the_content(); ?>

                    <?php
                    $cta = get_field('wt_cta');

                    if ($cta) {
                        ?>

                        <a href="<?php echo esc_url($cta['url']); ?>" target="<?php esc_attr_e($cta['target']); ?>" class="button secondary"><?php echo sanitize_text_field($cta['title']); ?></a>

                        <?php
                    }
                    ?>

                    <button class="close-button" data-close aria-label="Close modal" type="button">
                        <span aria-hidden="true" class="wt-sans-serif">&times;</span>
                    </button>
                </section>

                <script>
                    (function ($) {
                        var timeout = <?php echo $_SESSION['wt_prompt_at'] - $now; ?>;

                        if (timeout < 1)
                            timeout = 1

                        timeout *= 1000;

                        setTimeout(function () {
                            if ($('.reveal-overlay > [aria-hidden="false"]').length)
                                return;

                            // $('#sign-up-prompt').foundation('open');
                        }, timeout);
                    })(jQuery);
                </script>
                <?php
            }
            wp_reset_postdata();
        }
    }
}
?>

<?php if (!is_user_logged_in()) { ?>
    <section class="reveal" id="showcase-login-modal" data-reveal>
        <?php get_template_part('template-parts/form/form-showcase-login'); ?>

        <button class="close-button" data-close aria-label="Close modal" type="button">
            <span aria-hidden="true" class="wt-sans-serif">&times;</span>
        </button>
    </section>
<?php } ?>

<?php wp_footer(); ?>

<script>
    (function ($) {
        function adjustPadding() {
            $(document.body).css('padding-top', $('body > header').height());
        }

        adjustPadding();

        $(window).resize(adjustPadding);

    })(jQuery);
</script>

<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-105246934-1', 'auto');
    ga('send', 'pageview');

    (function ($) {
        $('[target]').not('[href^="<?php echo home_url(); ?>"]').click(function () {
            ga('send', 'event', 'outbound', 'click', $(this).attr('href'), {
                'transport': 'beacon'
            });
        });

    })(jQuery);
</script>

<!-- Facebook Pixel Code -->

<script>
    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
            n.callMethod ?

                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };

        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';

        n.queue = [];
        t = b.createElement(e);
        t.async = !0;

        t.src = v;
        s = b.getElementsByTagName(e)[0];

        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',

        'https://connect.facebook.net/en_US/fbevents.js');

    fbq('init', '183788381980835');
    fbq('track', 'PageView');
</script>

<noscript>
    <img height="1" width="1" src="https://www.facebook.com/tr?id=183788381980835&ev=PageView&noscript=1"/>
</noscript>

<!-- End Facebook Pixel Code -->
<script type="text/javascript">
    var ScarabQueue = ScarabQueue || [];
    (function (id) {
        if (document.getElementById(id)) return;
        var js = document.createElement('script');
        js.id = id;
        js.src = '//cdn.scarabresearch.com/js/19DBB8C8A3287884/scarab-v2.js';
        var fs = document.getElementsByTagName('script')[0];
        fs.parentNode.insertBefore(js, fs);
    })('scarab-js-api');

    <?php if (!empty($_SESSION['wt_crm']['CustomerNumber'])) { ?>
    ScarabQueue.push(['setCustomerId', '<?php echo sanitize_text_field($_SESSION['wt_crm']['CustomerNumber']); ?>']);
    <?php } ?>

    <?php
    $title = ltrim(wp_title('', false));

    if (!$title)
        $title = 'Home'
    ?>
    ScarabQueue.push(['tag', '<?php echo $title; ?>']);
    ScarabQueue.push(['go']);
</script>
<script>
    (function (w, d, u, t, o, c) {
        w['dmtrackingobjectname'] = o;
        c = d.createElement(t);
        c.async = 1;
        c.src = u;
        t = d.getElementsByTagName

        (t)[0];
        t.parentNode.insertBefore(c, t);
        w[o] = w[o] || function () {
            (w[o].q = w[o].q || []).push(arguments);
        };

    })(window, document, '//static.trackedweb.net/js/_dmptv4.js', 'script', 'dmPt');
    window.dmPt('create', 'DM-4137340455-03', 'wtplus.com.sg');
    window.dmPt('track');
    // window.dmPt("identify", test@test.com);  // Hardcoded example, inject contact email address.
</script>
</body>
</html>
