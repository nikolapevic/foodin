<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
* Register new endpoint to use inside My Account page.
*/

function nutritionist_dash_endpoints() {
    add_rewrite_endpoint( 'nutritionist-dashboard', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'nutritionist_dash_endpoints' );

/**
* Add new query var.
* @param array $vars
* @return array
*/

function nutritionist_dash_query_vars( $vars ) {
    $vars[] = 'nutritionist-dashboard';

    return $vars;
}

add_filter( 'query_vars', 'nutritionist_dash_query_vars', 0 );


/*
     * Change endpoint title.
     *
     * @param string $title
     * @return string
     */

function nutritionist_dash_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['nutritionist-dashboard'] );

    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        // New page title.
        $title = __( 'Nutritionist Dashboard', 'woocommerce' );

        remove_filter( 'the_title', 'nutritionist_dash_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'nutritionist_dash_endpoint_title' );


//Content

function nutritionist_dash_endpoint_content() {
	$user = wp_get_current_user();
	$user_id = $user->ID;
	global $nutritional_subscription;
	if ( active_subscription($nutritional_subscription,$user_id) ) {
		$user->add_role( 'nutritionist' );
		echo nutritionist_content();
	} else {
		$id = $nutritional_subscription;
		$subscription = wc_get_product($id);
		$name = $subscription->get_name();
		$price = $subscription->get_price_html();
		$description = $subscription->get_description();
		
		$user->remove_role( 'nutritionist' );
		$output .= '<div class="text-center white-container">';
		$output .= '<h2>Become a nutritionist</h2>';
		$output .= '<img width=600 src="https://foodin.io/wp-content/uploads/2020/08/start_scheduling-1.jpg">';
		$output .= '<h4>Start Scheduling Your Clients</h4>';
		$output .= '<p>Gain access to our platform and push your nutrition business into the future.</p>';
		$output .= '<p class="offer-price mb-0">' . $price . '</p>';
		$output .= '<a href="?add-to-cart='.$id.'" data-quantity="'.$quantity.'" class="btn btn-secondary btn-sm button add_to_cart_button ajax_add_to_cart m-3" data-product_id="'.$id.'" data-product_sku="" aria-label="Read more about “'.$name.'”" rel="nofollow"><i class="mdi mdi-cart-outline"></i> Subscribe</a>';
		$output .= '</div>';
		
		echo $output;
	}
}

add_action( 'woocommerce_account_nutritionist-dashboard_endpoint', 'nutritionist_dash_endpoint_content' );


/**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
function nutritionist_dash_insert_after_helper( $items, $new_items, $after ) {
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search( $after, array_keys( $items ) ) + 1;

    // Insert the new item.
    $array = array_slice( $items, 0, $position, true );
    $array += $new_items;
    $array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
}

/**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
function nutritionist_dash_menu_items( $items ) {
    $new_items = array();
    $new_items['nutritionist-dashboard'] = __( 'Nutritionist Dashboard', 'woocommerce' );

    // Add the new item after `orders`.
    return foodin_feed_insert_after_helper( $items, $new_items, 'schedule' );
}

add_filter( 'woocommerce_account_menu_items', 'nutritionist_dash_menu_items' );



?>