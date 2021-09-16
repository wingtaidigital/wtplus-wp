<?php get_header(); ?>

<?php
while (have_posts()) {
    the_post();

    $title = get_the_title();
    $brand = get_post_meta($post->ID, 'wt_brand', true);
    $brand_title = '';
    $case_sensitive = '';

    if ($brand) {
        $brand = get_post($brand);

        if ($brand) {
            $brand_title = get_the_title($brand);
            $case_sensitive = get_post_meta($brand->ID, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : '';
        }
    }

    $regular_price = number_format(get_post_meta($post->ID, 'wt_regular_price', true),2);
    $sale_price = get_post_meta($post->ID, 'wt_sale_price', true);
    $sale_price = $sale_price ? number_format((float) $sale_price, 2) : "";
    $price = $sale_price ? $sale_price : $regular_price;
    $sku = get_post_meta($post->ID, 'wt_sku', true);
    $categories = get_post_meta($post->ID, 'wt_categories', true);

//	$cta = sanitize_text_field(get_post_meta($post->ID, 'wt_cta', true));
//	$url = esc_url(get_post_meta($post->ID, 'wt_url', true));
    $cta = strtoupper(sanitize_text_field(get_field('wt_cta')));
    $url = get_field('wt_url');
    $cta_2 = strtoupper(sanitize_text_field(get_field('wt_cta_2')));
    $url_2 = get_field('wt_url_2');
    $details = wpautop(wp_kses_post(get_post_meta($post->ID, 'wt_details', true)));
    $gallery_background_color = sanitize_hex_color(get_field('wt_gallery_background_color'));
    ?>

    <div class="row xlarge-collapse">
        <div class="column small-12">
            <ul class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="<?php echo home_url(); ?>" itemprop="item">Home</a>
                </li>

                <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <a href="<?php echo get_permalink(get_page_by_path('brands')); ?>" itemprop="item">Brands</a>
                </li>

                <?php
                if ($brand) {
                    if ($brand->post_status == 'publish') {
                        ?>

                        <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                            <a href="<?php echo get_permalink($brand); ?>" itemprop="item"
                               class="<?php echo $case_sensitive; ?>"><?php echo $brand_title; ?></a>
                        </li>

                        <?php
                    } else if ($brand->post_parent) {
                        $parent_brand = get_post($brand->post_parent);

                        if ($parent_brand) {
                            $parent_case_sensitive = get_post_meta($parent_brand->ID, 'wt_case_sensitive', true) ? 'wt-case-sensitive' : '';
                            ?>

                            <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <a href="<?php echo get_permalink($parent_brand); ?>" itemprop="item"
                                   class="<?php echo $parent_case_sensitive; ?>"><?php echo get_the_title($parent_brand); ?></a>
                            </li>

                            <?php
                        }
                    }
                }
                ?>

                <li itemprop="itemListElement">
                    <span class="show-for-sr">Current:</span>
                    <?php echo $title; ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="wt-gutter-bottom-x2" itemscope itemtype="http://schema.org/Product">
        <div class="row collapse wt-margin-bottom-small">
            <div class="column small-12 medium-7"
                <?php if ($gallery_background_color) { ?>
                    style="background-color: <?php echo $gallery_background_color; ?>"
                <?php } ?>
            >
                <?php
                $thumbnail = get_post_thumbnail_id();
                $gallery = get_post_meta($post->ID, 'wt_gallery', true);

                if (!is_array($gallery))
                    $gallery = [];

                if ($thumbnail)
                    array_unshift($gallery, $thumbnail);

                if ($gallery) {
                    ?>

                    <div class="wt-slick-arrows-single">
                        <?php foreach ($gallery as $image) { ?>
                            <div class="text-center">
                                <div class="wt-relative">
                                    <?php
                                    echo wp_get_attachment_image($image, 'medium', false, [
                                        'class' => 'wt-zoom',
                                        'itemprop' => "image"
                                    ]);
                                    ?>

                                    <div class="hide wt-background-cover"
                                         style="background-image: url(<?php echo wp_get_attachment_url($image); ?>)"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php
                }
                ?>
            </div>
            <div class="column small-12 medium-5 wt-background-pink wt-gutter-x2 wt-gutter-vertical-x2">
                <header class="wt-upper">
                    <?php if ($brand) { ?>
                        <h1 class="wt-h3 <?php echo $case_sensitive; ?>"
                            itemprop="brand"><?php echo $brand_title; ?></h1>
                    <?php } ?>

                    <h2 class="wt-h3" itemprop="name"><?php echo $title; ?></h2>

                    <?php if ($price) { ?>
                        <div itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                            <span class="wt-h3" itemprop="price">SGD<?php echo $price; ?></span>

                            <?php if ($regular_price && $regular_price > $price) { ?>
                                <s>(U.P. SGD<?php echo $regular_price; ?>)</s>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($sku) { ?>
                        <span class="wt-h4" itemprop="price">SKU: <?php echo $sku; ?></span>
                    <?php } ?>
                    <br>
                    <?php if ($categories) { ?>
                        <span class="wt-h4" itemprop="price">CATEGORIES: <?php echo  implode(", ", $categories); ?></span>
                    <?php } ?>
                </header>

                <div class="wt-gutter-vertical" itemprop="description">
                    <?php the_content(); ?>
                </div>


                <div class="row align-justify xlarge-unstack wt-row-nest">
                    <?php if ($url && $cta) { ?>
                        <div class="column">
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noreferrer"
                               class="button secondary expanded wt-upper" itemprop="url"
                               onClick="
                                       ga('send', 'event', 'Product: Brand', '<?php echo $cta; ?>', '<?php echo $brand_title; ?>');
                                       ga('send', 'event', 'Product: Name', '<?php echo $cta; ?>', '<?php echo $title; ?>');
                                       ScarabQueue.push(['tag', '<?php echo $title; ?> - <?php echo $cta; ?>']);
                                       ScarabQueue.push(['go']);
                                       ">
                                <?php echo $cta; ?>
                            </a>
                        </div>
                    <?php } ?>

                    <?php if ($url_2 && $cta_2) { ?>
                        <div class="column">
                            <a href="<?php echo esc_url($url_2); ?>" target="_blank" rel="noreferrer"
                               class="button expanded wt-upper" itemprop="url"
                               style="background-color: <?php echo sanitize_hex_color(get_option('options_wt_button_background_color')); ?>"
                               onClick="
                                       ga('send', 'event', 'Product: Brand', '<?php echo $cta_2; ?>', '<?php echo $brand_title; ?>');
                                       ga('send', 'event', 'Product: Name', '<?php echo $cta_2; ?>', '<?php echo $title; ?>');
                                       ScarabQueue.push(['tag', '<?php echo $title; ?> - <?php echo $cta_2; ?>']);
                                       ScarabQueue.push(['go']);
                                       ">
                                <?php echo $cta_2; ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php if ($details) { ?>
            <div class="row wt-background-light-gray wt-gutter-x2 wt-gutter-vertical-x2">
                <div class="column small-12 wt-clear-last-child-margin" itemprop="description">
                    <h1 class="wt-h6 wt-margin-bottom">PRODUCT DETAILS</h1>

                    <?php echo $details; ?>
                </div>
            </div>
        <?php } ?>
    </div>

    <?php
}
?>

<?php get_footer();
