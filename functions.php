<?php
/* enqueue scripts and style from parent theme */
function twentytwenttwo_styles()
{
    wp_enqueue_style(
        'child-style',
        get_stylesheet_uri(),
        array('twenty-twenty-two-style'),
        wp_get_theme()->get('Version')
    );
}
/* enqueue scripts and style from child theme */
function rocks_styles()
{
    wp_enqueue_style('rocks-custom-style', get_stylesheet_directory_uri() . '/build/index.css',array(), '6.9.3');
    wp_enqueue_style('rocks-theme-style', get_stylesheet_directory_uri() . '/style.css');

    wp_enqueue_script(
        'main',
        get_stylesheet_directory_uri() . '/build/index.js',
        array(),
        false,
        true
    );
}

add_action('wp_enqueue_scripts', 'twentytwentytwo_styles');
add_action('wp_enqueue_scripts', 'rocks_styles');

/* year shortcode for copyright */
function current_year()
{
   $year = date('Y');
   return $year;
}
add_shortcode('year', 'current_year');

/* general woocommerce customization */
//
// Change the breadcrumb delimeter from '/' to '>' on shop pages
add_filter('woocommerce_breadcrumb_defaults', 'rocks_change_breadcrumb_delimiter');
function rocks_change_breadcrumb_delimiter($defaults)
{
    $defaults['delimiter'] = "<span class='breadcrumbs__separator'></span>";
    return $defaults;
}
// Add a "New" badge
add_action('woocommerce_shop_loop_item_title', 'rocks_new_badge', 10);
add_action('woocommerce_single_product_summary', 'rocks_new_badge', 7);
function rocks_new_badge()
{
    global $product;
    $newness_days = 30; // Newness in days 
    $created = strtotime($product->get_date_created());
    if ((time() - (60 * 60 * 24 * $newness_days)) < $created) {
        echo '<span class="itsnew">' . esc_html__('NEW', 'woocommerce') . '</span>';
    }
}
/* customizing single product page */
// Remove the reviews tab
add_filter('woocommerce_product_tabs', 'rocks_remove_product_tabs', 98);
function rocks_remove_product_tabs($tabs)
{
    unset($tabs['reviews']);
    return $tabs;
}
// remove related products
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
// remove single product images
remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
// display emebedded video on single product page
add_action('woocommerce_single_product_summary', 'rocks_video_embed', 10);
function rocks_video_embed()
{
    $the_video = get_field('video');

    if ($the_video) {
        echo "<div class=\"container\"><div class=\"plyr__video-embed\" id=\"player\">";
        echo "<iframe src=\"" . $the_video . "\" allowfullscreen allowtransparency allow=\"autoplay\"></iframe>";
        echo "</div></div>";
    }
}
/* end of single product page customizations */
//
/* customizing shop/archive pages */
//remove "add to cart" button
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
// remove sorting drop down menu
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
// remove images from shop pages
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
// display video poster frame image on shop pages
add_action('woocommerce_after_shop_loop_item_title', 'rocks_poster_image', 10);
function rocks_poster_image()
{
    global $product;
    $quality = "hq"; // "maxres" or "hq"
    if ($product && !$product->is_in_stock()) {
        $poster = get_stylesheet_directory_uri() . "/assets/img/soldout-" . $quality . ".png";
    } else if ($video_url = get_field('video', $product->get_id())) {
        $id = parse_url($video_url, PHP_URL_PATH);
        $poster = "https://i.ytimg.com/vi" . $id . "/" . $quality . "default.jpg";
    } else {
        $poster = get_stylesheet_directory_uri() . "/assets/img/placeholder-" . $quality . ".png";
    }
    echo "<div class=\"container\">";
    echo "<img src=\"" . $poster . "\">";
    echo "</div>";
}
/* end of shop/archive pages customizations */
//
/* customizing category page */
// remove product count next to category title
add_filter('woocommerce_subcategory_count_html', '__return_false');
// remove category images
remove_action('woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10);

//add category short description
add_action('woocommerce_after_subcategory', 'rocks_add_short_description', 30);
function rocks_add_short_description($category)
{
    $short_desc = get_term_meta($category->term_id, 'short_description', true);
    echo '<span class="short-description">' . $short_desc . '</span>';
}
/* end of category page customizations */
