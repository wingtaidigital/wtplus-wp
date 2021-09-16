<?php get_header(); ?>
<?php
$faq = array(
    // "1" => array(
    //     "title" => "1. Wing Tai Retail Vouchers",
    //     "detail" => "All issued vouchers expiring between 1 April to 30 June 2020 will be extended till 30 September 2020.",
    // ),
    "1" => array(
        "title" => "1. In-Store Exchange",
        "detail" => "Exchange is only valid within 14 days from the date of purchase for regular-price items and 7 days for sale items.<br><br>*Please produce your receipt during exchange. Standard exchange policy requirements apply.",
    ),
    // "3" => array(
    //     "title" => "3. Your WT+ Points & Vouchers",
    //     "detail" => "All wt+ points expiring between March and June 2020 and all active vouchers have been extended till 30 September 2020. <br><br>Check your updated points <a href='" . network_site_url() . "/login/' target=\"_blank\">here</a>.",
    // ),
);
?>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <style>
        .accordion {
            width: 500px;
            margin: auto;
            font-family: 'Roboto', sans-serif !important;
        }

        .accordion-title, .tabs-title > a, .accordion-content {
            border: none !important;
        }

        .accordion-title {
            padding: 15px;
        }

        .accordion-title:focus, .accordion-title:hover {
            background-color: transparent;
        }

        .accordion-item h3 {
            color: #21409b;
            font-size: 20px;
            font-weight: lighter;
            font-family: 'Roboto', sans-serif !important;
        }

        .stay-safe-icon {
            max-width: 40px;
            margin: 0px 10px;
            display: inline-block;
        }

        .wt-img-faq {
            margin-top: 80px;
            margin-bottom: 50px;
        }

        .wt-img-3 {
            margin-top: 100px;
        }

        .wt-img-4 {
            margin-top: 100px;
        }

        .brands {
            padding: 0px 38px;
        }

        .brands .column {
            padding: 1px;
        }

        .brands .row {
            margin: 0px;
        }

        .border-top {
            border-top-color: #1f43aa;
            border-top-style: solid;
            border-top-width: 1px;
        }

        .border-right {
            border-right-color: #1f43aa;
            border-right-style: solid;
            border-right-width: 1px;
        }

        .border-left {
            border-left-color: #1f43aa;
            border-left-style: solid;
            border-left-width: 1px;
        }

        .border-bottom {
            border-bottom-color: #1f43aa;
            border-bottom-style: solid;
            border-bottom-width: 1px;
        }

        @media only screen and (max-width: 850px) {
            .accordion-item h3 {
                font-size: 14px;
            }

            .accordion {
                max-width: 100%;
            }

            .wt-stay-safe {
                padding: 0px;
            }

            .wt-img-faq {
                margin-bottom: 20px;
                margin-top: 55px;
            }

            .wt-img-3 {
                margin-top: 40px;
            }

            .wt-img-4 {
                margin-top: 40px;
            }

            .brands {
                padding: 0px 18px;
            }
        }

    </style>

    <div class="row">
        <article class="column" itemscope="" itemtype="http://schema.org/Article">
            <div class="wt-content" itemprop="articleBody">
                <div class="wt-layout-wysiwyg wt-gutter-bottom-x2">

                    <div class="row align-center">
                        <div class="column medium-8 wt-clear-last-child-margin wt-stay-safe">
                            <img class="aligncenter size-full"
                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-1a.png"
                                 alt="">
                            <a href="<?= network_site_url() ?>/stores/" target="_blank"
                               style="display: block; margin-top: 10px; margin-bottom: 10px">
                                <img class="aligncenter size-full"
                                     src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-2.jpg"
                                     alt="">
                            </a>

                            <img class="aligncenter size-full wt-img-3"
                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-5.jpg"
                                 alt="">

                            <div class="brands">
                                <div class="row">
                                    <div class="column small-4 border-right border-bottom">
                                        <a href="https://forms.gle/NUEuUJhXU2en3zQVA" target="_blank">
                                            <img class="size-full" alt="" src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/cath.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-bottom">
                                    <a href="https://forms.gle/mXmZLLYHPmDKKoSf9" target="_blank">
                                            <img class="size-full" alt="" src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/dpm.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-left border-bottom">
                                        <a href="https://forms.gle/qHhaqLhHTrb2GRRe6" target="_blank">
                                            <img class="size-full" alt="" src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/dp.jpg">
                                        </a>
                                    </div>
                                </div>
                                <div class="row align-center">
                                    <div class="column small-4 border-right">
                                        <a href="https://forms.gle/76P7Z6ie69QA7NVA9" target="_blank">
                                            <img class="size-full" alt="" src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/fox.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4">
                                        <a href="https://forms.gle/fW7eMU2g2aggdyjh8" target="_blank">
                                            <img class="size-full" alt="" src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/g2000.jpg">
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <img class="aligncenter size-full wt-img-4"
                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-4.jpg"
                                 alt="">

                            <div class="brands">
                                <div class="row">
                                    <div class="column small-4 border-right border-bottom">
                                        <a href="https://www.zalora.sg/men/burton-menswear-london/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/burton.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-bottom">
                                        <a href="https://www.cathkidston.com.sg/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/cath.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-left border-bottom">
                                        <a href="https://www.zalora.sg/kids/catalog/?q=dpam"
                                           target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/dpm.jpg">
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="column small-4 border-right border-bottom">
                                        <a href="https://sg.dorothyperkins.com/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/dp.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-bottom">
                                        <a href="https://foxfashion.sg/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/fox.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-left border-bottom">
                                        <a href="https://www.g2000.com.sg/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/g2000.jpg">
                                        </a>
                                    </div>
                                </div>
                                <div class="row align-center">
                                    <div class="column small-4 border-right border-bottom">
                                        <a href="https://www.zalora.sg/topman/?q=topman" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/topman.jpg">
                                        </a>
                                    </div>
                                    <div class="column small-4 border-bottom">
                                        <a href="https://sg.topshop.com/" target="_blank">
                                            <img class="size-full" alt=""
                                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/shop-online/topshop.jpg">
                                        </a>
                                    </div>
                                </div>

                            </div>

                            <img class="aligncenter size-full wt-img-faq"
                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-faq.jpg"
                                 alt="">

                            <ul class="accordion" data-accordion data-multi-expand="true"
                                data-allow-all-closed="true">

                                <?php foreach ($faq as $k => $v) { ?>
                                    <li data-accordion-item="" class="accordion-item">
                                        <a class="accordion-title" aria-controls="<?= $k ?>-accordion" role="tab"
                                           id="<?= $k ?>-accordion-label" aria-expanded="false"
                                           aria-selected="false">
                                            <h3><?= $v['title'] ?></h3>
                                        </a>
                                        <div data-tab-content="" class="accordion-content" role="tabpanel"
                                             aria-labelledby="<?= $k ?>-accordion-label" aria-hidden="true"
                                             id="<?= $k ?>-accordion" style="display: none;">
                                            <?= $v['detail'] ?>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>

                            <img class="aligncenter size-full" style="margin-top: 50px; margin-bottom: 0px"
                                 src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-footer.jpg"
                                 alt="">

                            <div class="text-center" style="margin-bottom: 100px">
                                <a href="https://www.facebook.com/wtplussg" target="_blank">
                                    <img class="aligncenter size-full stay-safe-icon"
                                         src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-fb.png"
                                         alt="">
                                </a>
                                <a href="https://www.instagram.com/wtplussg/" target="_blank">
                                    <img class="aligncenter size-full stay-safe-icon"
                                         src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-insta.png"
                                         alt="">
                                </a>
                                <a href="https://t.me/wtplussg" target="_blank">
                                    <img class="aligncenter size-full stay-safe-icon"
                                         src="<?= network_site_url() ?>/wp-content/themes/wt/assets/img/stay-safe/stay-safe-tele.png"
                                         alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </div>

<?php get_footer();
