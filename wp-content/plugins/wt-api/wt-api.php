<?php
/*
Plugin Name: wt+ API
*/

// remove_action( 'rest_api_init', 'create_initial_rest_routes', 99 );
remove_action('rest_api_init', 'wp_oembed_register_route');

add_action('rest_api_init', function ($wp_rest_server) {
    // require_once 'inc/crm.php';
    require_once 'inc/ascentis.php';
    require_once 'inc/event.php';
    require_once 'inc/user.php';

    add_filter('rest_pre_serve_request', function ($served, $result, $request, $server) {
        $accept = $request->get_header('accept');

        if ($accept && strpos($accept, 'text/html') !== false && is_string($result->data)) {
            // wt_dump($result->data);
            echo $result->data;
            return true;
        }

        return $served;
    }, 10, 4);

    // wp/v2/comments only allows logged in user to update meta

    add_action('phpmailer_init', function ($phpmailer) {
        wt_log("phpmailer_init");
        //$phpmailer->Hostname = get_network()->domain;
        $phpmailer->isSMTP();

        //Enable SMTP debugging.
        $phpmailer->SMTPDebug = 0;
        //Set PHPMailer to use SMTP.
        $phpmailer->isSMTP();
        //Set SMTP host name
        $phpmailer->Host = "smtp.gmail.com";
        //Set this to true if SMTP host requires authentication to send email
        $phpmailer->SMTPAuth = true;
        //Provide username and password
        $phpmailer->Username = "noreply@wtplus.com.sg";
        $phpmailer->Password = "noreply1234";
        //If SMTP requires TLS encryption then set it
        $phpmailer->SMTPSecure = "tls";
        //Set TCP port to connect to
        $phpmailer->Port = 587;

        $phpmailer->From = "noreply@wtplus.com.sg";
        //$phpmailer->FromName = "Full Name";
        wt_log("phpmailer_init complete");
    });

    add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
    function log_mailer_errors($wp_error)
    {
        $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
        $fp = fopen($fn, 'a');
        fputs($fp, "Mailer Error: " . $wp_error->get_error_message() . "\n");
        fclose($fp);
    }

    register_rest_route('wt/v1', '/comments', array(
        'methods' => 'POST',
        'callback' => function ($request) {
            if (function_exists('wt_verify_captcha')) {
                $verified = wt_verify_captcha();

                if (is_wp_error($verified))
                    return $verified;
            }

            $params = $request->get_params();

            $meta = ['mobile', 'nature'];

            foreach ($meta as $m) {
                if (empty($params['comment_meta']["wt_$m"]))
                    return new WP_Error('wt_missing_field', "Please enter $m.", ['status' => 400]);
            }

            wt_log("Input Params: " . json_encode($params));
            $email = $params['email'];

            //Get email detail if input email not pass in
            if (empty($params['email'])) {
                $email = get_option('options_wt_email');
                wt_log("Params email empty using option email: " . $email);
            }

            if (empty($email))
                $email = get_option('admin_email');
            wt_log("Params and option email empty using admin email: " . $email);

            //$email = "evan.chong@cleargo.com";

            wt_log("Sending email: " . $email);

            if (!is_email($email))
                return;

            if ($params['comment_meta']['wt_nature'] != "Membership") {

                wt_log("Not membership option: " . $params['comment_meta']['wt_nature']);
                wt_send_email_template("default", $params, $email, $params['comment_author_email'], $params['wt_store']);

                //if nature of feedback = membership
            } else {

                wt_log("It is membership");

                if ($params['comment_meta']['wt_question'] != "Others") {
                    wt_log("Membership option: " . $params['comment_meta']['wt_question']);
                    $email_type = $params['comment_meta']['wt_question'];
                    wt_send_email_template($email_type, $params, $email, $params['comment_author']);

                } else {
                    wt_log("Membership option: Others");
                    wt_send_email_template("default", $params, $email, $params['comment_author']);
                }
            }

            wt_log("End");
            // wp_insert_comment($params);
        },
        'args' => [
            // 'comment_post_ID' => [
            //     'required' => true,
            //     'validate_callback' => function ($param, $request, $key) {
            //         return !empty($param);
            //     },
            //     'sanitize_callback' => function ($param, $request, $key) {
            //         return sanitize_text_field($param);
            //     },
            // ],
            'comment_author' => [
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param);
                },
                'sanitize_callback' => function ($param, $request, $key) {
                    return sanitize_text_field($param);
                },
            ],
            'comment_meta' => [
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return !empty($param) && is_array($param);
                },
                'sanitize_callback' => function ($param, $request, $key) {
                    foreach ($param as $key => $value)
                        $param[$key] = sanitize_text_field($value);

                    return $param;
                },
            ],
            'comment_author_email' => [
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return is_email($param);
                },
                'sanitize_callback' => function ($param, $request, $key) {
                    return sanitize_email($param);
                },
            ],
            // 'comment_content' => [
            //     'required' => true,
            //     'validate_callback' => function ($param, $request, $key) {
            //         return !empty($param);
            //     },
            //     'sanitize_callback' => function ($param, $request, $key) {
            //         return wp_kses_post($param);
            //     },
            // ],
        ]
    ));

    register_rest_route('wt/v1', '/posts', [
        'methods' => 'POST',
        'callback' => function ($request) {
            $params = wp_parse_args($request->get_params(), [
                'post_type' => 'post',
                'paged' => 2,
            ]);
            // wt_log($params);
            $query = new WP_Query($params);

            if (!$query->have_posts())
                return new WP_Error('wt_not_found', 'Not Found', ['status' => 404]);

            ob_start();

            while ($query->have_posts()) {
                $query->the_post();

                load_template(TEMPLATEPATH . '/template-parts/content/' . $params['post_type'] . '.php', false);
            }

            $html = ob_get_clean();
            // wt_dump($query);
            if ($query->max_num_pages > $params['paged'])
                $paged = $params['paged'] + 1;
            else
                $paged = 0;

            return [
                'paged' => $paged,
                'html' => $html,
            ];
        },
    ]);

    register_rest_route('wt/v1', '/products', [
        'methods' => 'GET',
        'callback' => function ($request) {
            $params    = wp_parse_args($request->get_params(), ['paged' => 2]);
            $max_pages = get_option('options_wt_max_pages');

            if ($max_pages && $params['paged'] > $max_pages)
                return new WP_Error('wt_404', 'Not Found', ['status' => 404]);

            $cta = '';

            if (!empty($params['brand'])) {
                $params['meta_query'][] = [
                    'key' => 'wt_brand',
                    'value' => (int)$params['brand']
                ];
                $params['orderby']      = [
                    'menu_order' => 'ASC',
                    'date' => 'DESC'
                ];
                $cta                    = sanitize_text_field(get_post_meta($params['brand'], 'wt_products_cta', true));
            } elseif (!empty($params['post__in'])) {
                $params['post__in'] = explode(',', $params['post__in']);
                $params['orderby']  = [
                    'post__in' => 'ASC',
                ];
            }

            $query = new WP_Query(wp_parse_args([
                'post_type' => 'wt_product',
                'posts_per_page' => get_option('posts_per_page') * 2,
            ], $params));

            if (!$query->have_posts())
                return new WP_Error('wt_404', 'Not Found', ['status' => 404]);

            global $post;

            ob_start();

            while ($query->have_posts()) {
                $query->the_post();
                ?>

                <div class="column small-6 medium-3">
                    <?php wt_product($post, $cta); ?>
                </div>

                <?php
            }

            return ob_get_clean();
        },
    ]);

    register_rest_route('wt/v1', '/stores', [
        'methods' => 'GET',
        'callback' => function ($request) {
            $params = wp_parse_args($request->get_params(), [
                'post_type' => 'wt_store',
                'posts_per_page' => -1,
            ]);

            $query = new WP_Query($params);

            if (!$query->have_posts())
                return new WP_Error('wt_not_found', 'Not Found', ['status' => 404]);

            ob_start();

            while ($query->have_posts()) {
                $query->the_post();

                load_template(TEMPLATEPATH . '/template-parts/content/' . $params['post_type'] . '.php', false);
            }

            return ob_get_clean();
        },
    ]);

    register_rest_route('wt/v1', '/stores_by_brand', [
        'methods' => 'GET',
        'callback' => function ($request) {

            $params = wp_parse_args($request->get_params(), [
                'post_type' => 'wt_store',
                'posts_per_page' => -1,
            ]);

            $query  = new WP_Query($params);
            $return = array();

            $posts = $query->posts;

            foreach ($posts as $post) {
                $email = get_metadata('post', $post->ID, "wt_email", true);

                $p        = array(
                    'name' => $post->post_title,
                    'id' => $post->ID,
                    'email' => $email,
                );
                $return[] = $p;
            }

            return [
                'return' => $return,
            ];
        },
    ]);
});

// function wt_has_registered_event($event_id, $user_id = null)
// {
// 	if (!$user_id)
// 		$user_id = get_current_user_id();
//
// 	$registrations = get_user_meta($user_id, 'wt_registrations', true);
//
// 	if (!is_array($registrations))
// 		return false;
//
// 	return array_key_exists($event_id, $registrations);
// }
