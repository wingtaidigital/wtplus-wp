<?php
defined('ABSPATH') || exit;

function wt_get_store_cc_emails($store_id){
    wt_log("[wt_get_store_cc_emails] Store ID: " . $store_id);
    $cc_email = get_post_meta($store_id, 'wt_email_cc', true);
    $result = array_map('trim', explode(',', $cc_email));
    wt_log("[wt_get_store_cc_emails] Result: " . json_encode($result));
    return $result;
}

function wt_send_email_template($type, $params, $email, $name, $store_id = 0)
{
    if ($type == "Update Member Details") {
        ob_start();
        ?>

        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
                "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>wt+</title>
            <meta http-equiv="content-type" content="text/html; charset= iso-8859-1"/>
            <style type="text/css">
                .footer a {
                    color: #333333;
                    text-decoration: underline;
                    font-size: 11px;
                    font-family: "Helvetica", Arial, Tahoma, Gotham, Helvetica Neue, sans-serif;
                }
            </style>
        </head>

        <body bgcolor="#E6E7E8" style="padding: 0; margin: 0;">

        <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #E6E7E8;">
            <tr>
                <td align="center">
                    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff">

                        <!-- ------------- HEADER ------------- -->
                        <tr bgcolor="#E6E7E8">
                            <td height="34">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td height="20">
                                <!-- PLACE PRE-HEADER HERE
                                <span style="font-size: 1px; color: #FFFFFF; text-align: center; display: none !important; line-height: 1px; margin: 0; padding: 0;">XXXXXXXXX</span> 	 -->
                                <!-- END PRE-HEADER HERE -->
                            </td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td width="524">&nbsp;</td>
                                        <td width="56"><a
                                                    href="https://www.wtplus.com.sg/login/?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                    target="_blank"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/header-login-btn.jpg"
                                                        width="56" height="20" alt="LOGIN"></a></td>
                                        <td width="20">&nbsp;</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><a
                                        href="https://www.wtplus.com.sg/?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                        title="" target="_blank" style="display: block;"><img
                                            src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/header-wtplus-02.jpg"
                                            alt="wt+" width="600" height="104" style="display:block" title=""
                                            border="0"/></a></td>
                        </tr>
                        <!-- ------------- END HEADER ------------- -->


                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <!-- CONTENT -->
                                    <tr>
                                        <td align="center"><img
                                                    src="<?= get_stylesheet_directory_uri() ?>/assets/img/email/UPDATE-MEMBER-DETAILS_01.jpg"
                                                    alt="UPDATED YOUR PROFILE" width="600" height="281"
                                                    border="0" style="display:block" title=""/></td>
                                    </tr>
                                    <tr>
                                        <td width="600" bgcolor="#ffffff" valign="top" align="center"
                                            style="color: #000000; font-size: 22px; line-height: 28px; font-family: 'Helvetica', Arial, Tahoma; text-align: center;">
                                            Hi <b><?= $name ?></b>.

                                            <br><br>
                                            Thanks for reaching out.<br>
                                            Update your profile in 3 easy steps.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="center"><img
                                                    src="<?= get_stylesheet_directory_uri() ?>/assets/img/email/UPDATE-MEMBER-DETAILS_03.jpg"
                                                    alt="UPDATED NOW" width="600" height="517" border="0"
                                                    style="display:block" title=""/></td>
                                    </tr>


                                    <!-- 3 col -->
                                    <tr>
                                        <td align="center">
                                            <table align="center" border="0" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                <tr>
                                                    <td><img alt=""
                                                             src="<?= get_stylesheet_directory_uri() ?>/assets/img/email/UPDATE-MEMBER-DETAILS_04.jpg"
                                                             style="display: block; border-width: 0px; border-style: solid;"/>
                                                    </td>
                                                    <td>
                                                        <a href="https://www.wtplus.com.sg/login/?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                           target="_blank"><img alt=""
                                                                                src="<?= get_stylesheet_directory_uri() ?>/assets/img/email/UPDATE-MEMBER-DETAILS_05.jpg"
                                                                                style="display: block; border-width: 0px; border-style: solid;"/></a>
                                                    </td>
                                                    <td><img alt=""
                                                             src="<?= get_stylesheet_directory_uri() ?>/assets/img/email/UPDATE-MEMBER-DETAILS_06.jpg"
                                                             style="display: block; border-width: 0px; border-style: solid;"/>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <!-- 3 col -->

                                    <tr>
                                        <td bgcolor="#ffffff" valign="top" align="center" height="40">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center" bgcolor="#ffffff" valign="top"
                                            style="color: #000000; font-size: 14px; line-height: 24px; font-family: 'Helvetica', Arial, Tahoma; text-align: center;">
                                            <b>Pro Tip:</b> You can amend communication preferences<br>
                                            and change password too.<br>

                                            <p>Still having issues?<br>
                                                Reach out to us at <b>help@wtplus.com.sg</b> for further assistance.</p>


                                        </td>
                                    </tr>

                                    <tr>
                                        <td bgcolor="#ffffff" valign="top" align="center" height="40">&nbsp;</td>
                                    </tr>
                                    <!-- END CONTENT -->
                                </table>
                            </td>
                        </tr>


                        <!-- ------------- FOOTER ------------- -->

                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_01.jpg"
                                                    width="20" height="60" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                        <td align="center"><a href="https://www.wtplus.com.sg/brands/" title=""
                                                              target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_02.jpg"
                                                        width="186" height="60" border="0" style="display:block"
                                                        title="" alt="BRANDS"/> </a></td>
                                        <td align="center"><a href="https://www.wtplus.com.sg/wt/" title=""
                                                              target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_03.jpg"
                                                        width="188" height="60" border="0" style="display:block"
                                                        title="" alt=""/></a></td>
                                        <td align="center"><a href="https://www.wtplus.com.sg/stores/" title=""
                                                              target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_04.jpg"
                                                        width="186" height="60" border="0" style="display:block"
                                                        title="" alt=""/></a></td>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_05.jpg"
                                                    width="20" height="60" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_06.jpg"
                                                    alt="FOLLOW US" width="600" height="53" style="display:block;"
                                                    title="" border="0"/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_07.jpg"
                                                    width="179" height="62" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                        <td align="center"><a
                                                    href="https://www.facebook.com/wtplussg?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                    title="" target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_08.jpg"
                                                        width="64" height="62" border="0" style="display:block" title=""
                                                        alt="FACEBOOK"/></a></td>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_09.jpg"
                                                    width="22" height="62" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                        <td align="center"><a
                                                    href="https://www.instagram.com/wtplussg/?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                    title="" target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_10.jpg"
                                                        width="64" height="62" border="0" style="display:block" title=""
                                                        alt="INSTAGRAM"/> </a></td>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_11.jpg"
                                                    width="22" height="62" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                        <td align="center"><a
                                                    href="https://www.youtube.com/channel/UCOlGJN6QG7vcbDF-2ZdJMEA?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                    title="" target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_12.jpg"
                                                        width="64" height="62" border="0" style="display:block"
                                                        alt="YOUTUBE"/></a></td>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_13.jpg"
                                                    width="185" height="62" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_14.jpg"
                                                    width="600" height="26" border="0" style="display:block" title=""/>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_15.jpg"
                                                    width="212" height="18" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                        <td align="center"><a
                                                    href="https://www.wtplus.com.sg/?utm_source=crm&amp;utm_medium=edm&amp;utm_content=crm"
                                                    title="" target="_blank" style="display: block;"><img
                                                        src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_16.jpg"
                                                        width="170" height="18" border="0" style="display:block"
                                                        title="" alt="WTPLUS.COM.SG"/> </a></td>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_17.jpg"
                                                    width="218" height="18" border="0" style="display:block" title=""
                                                    alt=""/></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#ffffff">
                                    <tr>
                                        <td align="center"><img
                                                    src="https://edmsource.ascentis.com.sg/MatrixResourcesCRM5/FileManager/wingtai/templates/01/footer_18.jpg"
                                                    width="600" height="26" border="0" style="display:block" title=""/>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table width="600" border="0" align="center" cellpadding="0" cellspacing="0"
                                       bgcolor="#E6E7E8">
                                    <!-- T&CS
                                    <tr>
                                      <td height="34">&nbsp;</td>
                                    </tr>
                                    <tr>
                                      <td style="font-size: 11px; font-family: 'Helvetica', Arial, Tahoma, Verdana; color: #333; padding: 0 10px;" align="center">
                                          Terms and Conditions:  XX
                                    </tr>  -->
                                    <!-- END T&CS -->
                                    <tr>
                                        <td height="34">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td align="center" class="footer"
                                            style="font-size: 11px; font-family: 'Helvetica', Arial, Tahoma, Verdana; color: #333;">
                                            <a href="https://www.wtplus.com.sg/login/?utm_source=crm&utm_medium=edm&utm_content=crm"
                                               style="color: #333333; text-decoration: underline;">Update Your
                                                Preference</a> | <a
                                                    href="https://www.wtplus.com.sg/pdpa/?utm_source=crm&utm_medium=edm&utm_content=crm"
                                                    style="color: #333333; text-decoration: underline;">Privacy
                                                Policy</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="20">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="20"
                                            style="font-size: 11px; font-family: 'Helvetica', Arial, Tahoma, Verdana; color: #333; padding: 0 10px;"
                                            align="center">
                                            Please do not reply to this email address. For assistance, please contact us
                                            at <a href="mailto:help@wtplus.com.sg" target="_blank"
                                                  style="color: #333333; text-decoration: underline;">help@wtplus.com.sg</a>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center"
                                            style="font-size: 11px; font-family: 'Helvetica', Arial, Tahoma, Verdana; color: #333;">
                                            &copy; 2020 Wing Tai Retail. All rights reserved.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td height="34">&nbsp;</td>
                                    </tr>

                                </table>
                            </td>
                        </tr>
                        <!-- END FOOTER -->
                    </table>
                </td>
            </tr>
        </table>
        </body>
        </html>


        <?php
        $html = ob_get_clean();

        //Change email to customer email
        $email = $params['comment_author_email'];

    } else {
        $brand = empty($params['comment_post_ID']) ? "N/A" :
            (is_numeric($params['comment_post_ID']) ? get_the_title($params['comment_post_ID']) : $params['comment_post_ID']);
        $store = empty($params['wt_store']) ? "N/A" :
            (is_numeric($params['wt_store']) ? get_the_title($params['wt_store']) : $params['wt_store']);

        $fields = [
            [
                'label' => 'Brand',
                'value' => $brand,
            ],
            [
                'label' => 'Store',
                'value' => $store,
            ],
            [
                'label' => 'Name',
                'value' => $params['comment_author']
            ],
            [
                'label' => 'Mobile',
                'value' => $params['comment_meta']['wt_mobile']
            ],
            [
                'label' => 'Email',
                'value' => $params['comment_author_email']
            ],
            [
                'label' => 'Nature of Feedback',
                'value' => $params['comment_meta']['wt_nature']
            ],
            [
                'label' => 'Subject',
                'value' => empty($params['comment_meta']['wt_subject']) ? "" : $params['comment_meta']['wt_subject'],
            ],
            [
                'label' => 'Comments',
                'value' => empty($params['comment_content']) ? "" : nl2br($params['comment_content']),
            ],
        ];


        ob_start();
        ?>

        <table>
            <?php foreach ($fields as $field) { ?>
                <tr>
                    <th valign="top" align="left"><?php echo $field['label']; ?></th>
                    <td valign="top"><?php echo $field['value']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <?php
        $html = ob_get_clean();
    }

    $subject = "[{$params['comment_meta']['wt_nature']}] " . $params['comment_meta']['wt_subject'];


    if ($params['corp']) {
        $email_part = "Cc: <help@wtplus.com.sg>, <Group-WTR-Operations@wingtaiasia.com.sg>, <corpcomms@wingtaiasia.com.sg>";

    } else if ($params['comment_meta']['wt_nature'] == "Sponsorship / Partnership") {
        $email_part = "Cc: <help@wtplus.com.sg>, <Group-WTR-Marcoms@wingtaiasia.com.sg >";

    } else if ($params['comment_meta']['wt_nature'] == "Membership" && $params['comment_meta']['wt_question'] == "Others") {
        $email_part = "Cc: <help@wtplus.com.sg>";

    } else {
        $email_part = "Cc: <help@wtplus.com.sg>, <Group-WTR-Operations@wingtaiasia.com.sg>";
        //$email_part = "Cc: <evan.chong@cleargo.com>";
    }

    //Fetch cc emails if store id not empty
    if ($store_id > 0) {
        $cc_emails = wt_get_store_cc_emails($store_id);

        foreach ($cc_emails as $cc_email) {
            $s = sprintf(", <%s>", $cc_email);
            $email_part .= $s;
        }
    }

    if ($type == "Update Member Details") {
        $email_part = "";
        $subject    = "Need help to wt+ membership details?";
    }

    $email_header = array(
        'Content-Type: text/html; charset=UTF-8',
        "From: wt+ <noreply@wtplus.com.sg>",
        $email_part,
    );

    wt_log("Email header: " . json_encode($email_header));
    wp_mail($email, $subject, $html, $email_header);

    wt_log("[wt_send_email_template] Done");
}

function wt_crm_get_brands($parent_code)
{
    $transient = 'wt_system_codes_' . $parent_code;
    $codes     = get_transient($transient);

    if (!$codes) {
        $response = wt_crm_api([
            "ParentCode" => $parent_code,
            "Command" => "GET SYSTEM CODES",
        ]);

        if (!is_wp_error($response) && isset($response['SystemCodes']) && is_array($response['SystemCodes'])) {
            $codes = [];

            foreach ($response['SystemCodes'] as $code) {
                $codes[$code['SystemCode']] = $code['Name'];
            }
            set_transient($transient, $codes);
        }
    }

    $query = wt_get_brand_query();
    $posts = $query->posts;
    $brand = array();

    foreach ($posts as $post) {
        foreach ($codes as $code) {
            if (strcasecmp($post->post_title, $code) == 0) {
                $brand[] = array(
                    "id" => $post->ID,
                    "post_title" => $post->post_title,
                );
            }
        }
    }

    if ($brand) {
        return $brand;
    }
}

function wt_get_brand_query($args = [])
{
    $args = wp_parse_args($args, [
        'post_type' => 'wt_brand',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    return new WP_Query($args);
}

function wt_get_social_icon($url)
{
    $icons = array(
        'behance.net' => 'behance',
        'codepen.io' => 'codepen',
        'deviantart.com' => 'deviantart',
        'digg.com' => 'digg',
        'dribbble.com' => 'dribbble',
        'dropbox.com' => 'dropbox',
        'facebook.com' => 'facebook-official',
        'flickr.com' => 'flickr',
        'foursquare.com' => 'foursquare',
        'plus.google.com' => 'google-plus',
        'github.com' => 'github',
        'instagram.com' => 'instagram',
        'linkedin.com' => 'linkedin-square',
        'mailto:' => 'envelope-o',
        'medium.com' => 'medium',
        'pinterest.com' => 'pinterest-p',
        'getpocket.com' => 'get-pocket',
        'reddit.com' => 'reddit-alien',
        'skype.com' => 'skype',
        'skype:' => 'skype',
        'slideshare.net' => 'slideshare',
        'snapchat.com' => 'snapchat-ghost',
        'soundcloud.com' => 'soundcloud',
        'spotify.com' => 'spotify',
        'stumbleupon.com' => 'stumbleupon',
        'tumblr.com' => 'tumblr',
        'twitch.tv' => 'twitch',
        'twitter.com' => 'twitter',
        'vimeo.com' => 'vimeo',
        'vine.co' => 'vine',
        'vk.com' => 'vk',
        'wordpress.org' => 'wordpress',
        'wordpress.com' => 'wordpress',
        'yelp.com' => 'yelp',
        'youtube.com' => 'youtube-play',
    );

    foreach ($icons as $domain => $class) {
        if (false !== strpos($url, $domain)) {
            return $class;
        }
    }
}

function wt_is_dummy_email($email)
{
    return preg_match("/@(dummyemail.com|dummymobile.com)$/", $email);
}

function wt_login_error($error)
{
    if (!is_wp_error($error)) {
        return $error;
    }

    $error_code = $error->get_error_code();

    if ($error_code !== 'wt_lockout' && strpos($error_code, 'captcha') === false) {
        return new WP_Error('wt_error', 'Login incorrect. <a href="' . wp_lostpassword_url() . '">Forgot your password?</a>', ['status' => 400]);
    }

    return $error;
}

function wt_redirect($url)
{
    $url = esc_url($url);

    if (headers_sent()) {
        ?>
        <script>
            location.replace('<?php echo $url; ?>');
        </script>
        <?php
    } else {
        wp_redirect($url);
    }

    exit;
}

if (!function_exists('wt_dump')) {
    function wt_dump($message)
    {
        echo '<pre>';
        print_r($message);
        echo '</pre>';
    }

    function wt_log($message, $filename = 'wt')
    {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        } else {
            $message .= "\n";
        }

        error_log('[' . date("d-M-Y H:i:s e") . '] ' . $message, 3, WP_CONTENT_DIR . '/' . $filename . '.log');
    }
}
