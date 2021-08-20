<?php get_header(); ?>
    <style>
        .select2 {
            min-width: 100%;
        }
    </style>
    <div class="wt-background-light-gray wt-account">
        <div class="row align-center">
            <div class="column small-12 large-8 wt-gutter-bottom-x2">
                <?php
                while (have_posts()) {
                    the_post();
                    ?>

                    <h1 class="text-center wt-h3 wt-gutter-vertical-x2"><?php the_title(); ?></h1>

                    <form id="contact-form" data-route="wt/v1/comments" method="post"
                          class="wt-background-white wt-rounded wt-gutter-x2 wt-gutter-vertical-x2" autocomplete="off">
                        <div class="callout hide"
                             data-success="Thank you for your feedback. We will get back to you in 3 working days."></div>

                        <label>
                            Nature of Feedback*
                            <select name="comment_meta[wt_nature]" required class="wt-select">
                                <option value="">Select</option>
                                <option value="Feedback (Products)">Feedback (Products)</option>
                                <option value="Feedback (Service)">Feedback (Service)</option>
                                <option value="Membership">Membership</option>
                                <option value="Sponsorship / Partnership">Sponsorship / Partnership</option>
                            </select>
                        </label>

                        <label class="label-question" style="display: none">
                            Question Type*
                            <select id="select-question" name="comment_meta[wt_question]" class="wt-select">
                                <option value="">Select</option>
                                <option value="Forget Password">I’ve forgotten my password</option>
                                <option value="Points Request">wt+ Points were not issued</option>
                                <option value="Update Member Details">My member details are incorrect</option>
                                <option value="Others">Others</option>
                            </select>
                        </label>

                        <label class="label-question-forget-password" style="display: none">
                            Please click <a target="_blank" href="https://www.wtplus.com.sg/forgot-password/">here</a>
                            to reset your password
                        </label>

                        <label class="label-question-points-request" style="display: none">
                            Please complete the form <a target="_blank"
                                                        href="https://crm5.ascentis.com.sg/MatrixCRM2/Survey/publicpages/TakeSurvey.aspx?publicKey=90a978d2-8acc-4460-898c-3e5e9bc90574&cc=wingtai">here</a>
                        </label>

                        <label class="label-brand" style="display: none">
                            Brand*

                            <?php
                            $brands = wt_crm_get_brands('BrandPreference');
                            if (!empty($brands)) {
                                ?>

                                <select id="select-brand" name="comment_post_ID" class="wt-select">
                                    <option value="">Select</option>
<!--                                    <option value="wt+">wt+</option>-->

                                    <?php foreach ($brands as $b) { ?>
                                        <option value="<?= $b['id']; ?>"><?= $b['post_title']; ?></option>
                                    <?php } ?>

                                    <option value="Others">Others</option>
                                </select>
                            <?php } ?>
                        </label>

                        <label class="label-store" style="display: none">
                            Store*
                            <select id="select-store" name="wt_store" class="wt-select">
                                <option value="">Select</option>
<!--                                <option value="wt+">wt+</option>-->
                                <option value="Others">Others</option>
                            </select>
                        </label>

                        <label class="label-name">
                            Name*
                            <input id="input-name" type="text" name="comment_author" -autocomplete="name" required>
                        </label>

                        <label class="label-mobile">
                            Mobile*
                            <input id="input-mobile" type="tel" name="comment_meta[wt_mobile]" -autocomplete="tel"
                                   required>
                            <span class="form-error">
							<span class="customError">Please enter a valid number</span>
						</span>
                        </label>

                        <label class="label-email">
                            Email*
                            <input id="input-email" type="email" name="comment_author_email" -autocomplete="email"
                                   required>
                        </label>

                        <label class="label-subject">
                            Your Subject*
                            <input id="input-subject" type="text" name="comment_meta[wt_subject]" required>
                        </label>

                        <label class="label-comments">
                            Your Comments*
                            <textarea id="input-comments" name="comment_content" rows="3" required></textarea>
                        </label>

                        <?php
                        if (function_exists('wt_captcha'))
                            wt_captcha();
                        ?>

                        <div class="text-center">
                            <input type="hidden" name="email" value=""/>
                            <input type="hidden" name="corp" value="<?= $_GET['corp'] == "true" ? 1 : 0 ?>">
                            <button type="submit" class="button secondary" data-submit="Submit"
                                    data-submitting="Submitting..." data-submitted="Submitted">Submit
                            </button>
                        </div>
                    </form>

                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        jQuery(document).on('change', 'select[name="comment_meta[wt_nature]"]', function () {
            //console.log(jQuery(this).find(":selected").val());
            var value = jQuery(this).find(":selected").val();

            //clear email value
            jQuery("input[name=email]").val("");

            //show name, email, mobile
            jQuery(".label-name").show();
            jQuery(".label-email").show();
            jQuery(".label-mobile").show();
            jQuery("form button[type=submit]").show();
            document.getElementById("input-name").required = true;
            document.getElementById("input-email").required = true;
            document.getElementById("input-mobile").required = true;

            if (value == "Feedback (Products)" || value == "Feedback (Service)") {

                //Show brand and stores
                jQuery(".label-brand").show();
                jQuery(".label-store").show();
                document.getElementById("select-brand").required = true;
                document.getElementById("select-store").required = true;

                //Show subject and comments
                jQuery(".label-subject").show();
                jQuery(".label-comments").show();
                document.getElementById("input-subject").required = true;
                document.getElementById("input-comments").required = true;

                //Hide question
                jQuery(".label-question").hide();
                document.getElementById("select-question").required = false;

                //Trigger change for question
                jQuery('select[name="comment_meta[wt_question]"]').val('').trigger('change');

            } else if (value == "Membership") {
                //Show questions
                jQuery(".label-question").show();
                document.getElementById("select-question").required = true;

                //Hide brand and stores
                jQuery(".label-brand").hide();
                jQuery(".label-store").hide();
                document.getElementById("select-brand").required = false;
                document.getElementById("select-store").required = false;

                //Hide subject and comments
                jQuery(".label-subject").hide();
                jQuery(".label-comments").hide();
                document.getElementById("input-subject").required = false;
                document.getElementById("input-comments").required = false;

                //Trigger change for question
                jQuery('select[name="comment_meta[wt_question]"]').trigger('change');

            } else {
                //Hide brand, stores, question
                jQuery(".label-brand").hide();
                jQuery(".label-store").hide();
                jQuery(".label-question").hide();
                document.getElementById("select-brand").required = false;
                document.getElementById("select-store").required = false;
                document.getElementById("select-question").required = false;

                //Show subject and commemts
                jQuery(".label-subject").show();
                jQuery(".label-comments").show();
                document.getElementById("input-subject").required = true;
                document.getElementById("input-comments").required = true;

                //Trigger change for question
                jQuery('select[name="comment_meta[wt_question]"]').val('').trigger('change');
            }
        });

        jQuery(document).on('change', 'select[name="comment_meta[wt_question]"]', function () {
            var value = jQuery(this).find(":selected").val();
            //console.log(value);

            if (value == "Forget Password") {
                jQuery(".label-question-forget-password").show();
            } else {
                jQuery(".label-question-forget-password").hide();
            }

            if (value == "Points Request") {
                jQuery(".label-question-points-request").show();
            } else {
                jQuery(".label-question-points-request").hide();
            }

            if (value == "Forget Password" || value == "Points Request") {
                jQuery("form button[type=submit]").hide();
            } else {
                jQuery("form button[type=submit]").show();
            }


            if (value == "Forget Password" || value == "Points Request") {
                jQuery(".label-name").hide();
                jQuery(".label-email").hide();
                jQuery(".label-mobile").hide();
                jQuery(".label-subject").hide();
                jQuery(".label-comments").hide();
                document.getElementById("input-name").required = false;
                document.getElementById("input-email").required = false;
                document.getElementById("input-mobile").required = false;
                document.getElementById("input-subject").required = false;
                document.getElementById("input-comments").required = false;

            } else if (value == "Update Member Details") {
                jQuery(".label-name").show();
                jQuery(".label-email").show();
                jQuery(".label-mobile").show();
                jQuery(".label-subject").hide();
                jQuery(".label-comments").hide();
                document.getElementById("input-name").required = true;
                document.getElementById("input-email").required = true;
                document.getElementById("input-mobile").required = true;
                document.getElementById("input-subject").required = false;
                document.getElementById("input-comments").required = false;

            } else {
                jQuery(".label-name").show();
                jQuery(".label-email").show();
                jQuery(".label-mobile").show();
                jQuery(".label-subject").show();
                jQuery(".label-comments").show();
                document.getElementById("input-name").required = true;
                document.getElementById("input-email").required = true;
                document.getElementById("input-mobile").required = true;
                document.getElementById("input-subject").required = true;
                document.getElementById("input-comments").required = true;
            }
        });

        jQuery(document).on('change', 'select[name="comment_post_ID"]', function () {
            var value = jQuery(this).val();
            var name = jQuery(this).find('option:selected').html();
            var store_selector = jQuery('select[name="wt_store"]');
            //console.log(value);

            store_selector.find('option').remove().end();
            //store_selector.append('<option value="">Select</option>').append('<option value="wt+">wt+</option>');

            jQuery.ajax({
                type: 'GET',
                headers: {
                    '_wpnonce': '1',
                },
                url: '<?php echo get_home_url(); ?>/wp-json/wt/v1/stores_by_brand?brand=' + value,
                success: function (data, textStatus, XMLHttpRequest) {
                    //console.log(data);
                    for (i = 0; i < data.return.length; i++) {
                        let s = data.return[i].name.replace("&", "&amp;");
                        let regex = new RegExp(name + ' ', 'gi');
                        let store = s.replace(regex, '');

                        // console.log(s);
                        // console.log(name);
                        // console.log(store);
                        store_selector.append('<option data-email="' + data.return[i].email + '" value="' + data.return[i].id + '">' + store + '</option>');
                    }
                    store_selector.append('<option value="Others">Others</option>');
                },
                error: function (MLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        jQuery(document).on('change', 'select[name="wt_store"]', function () {
            var value = jQuery(this).val();
            //console.log(value);
            var email = jQuery('option:selected', this).attr('data-email');
            //console.log(email);
            jQuery("input[name=email]").val(email);
        });
    </script>

<?php get_footer();
