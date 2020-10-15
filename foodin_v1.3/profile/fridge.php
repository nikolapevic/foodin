<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
* Register new endpoint to use inside My Account page.
*/

function fridge_endpoints() {
    add_rewrite_endpoint( 'fridge', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'fridge_endpoints' );

/**
* Add new query var.
* @param array $vars
* @return array
*/

function fridge_query_vars( $vars ) {
    $vars[] = 'fridge';

    return $vars;
}

add_filter( 'query_vars', 'fridge_query_vars', 0 );


/*
     * Change endpoint title.
     *
     * @param string $title
     * @return string
     */

function fridge_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['fridge'] );

    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        // New page title.
        $title = __( 'Fridge', 'woocommerce' );

        remove_filter( 'the_title', 'fridge_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'fridge_endpoint_title' );


//Content

function fridge_endpoint_content() {

	$user = wp_get_current_user();
	$user_id = get_current_user_id();

	$custom_diet = get_user_meta($user_id, 'custom_diet', true);
	$assinged_diet_id = get_user_meta($user_id, 'assigned_diet', true);
	$date = getdate();
	$day = $date['weekday'];
	$weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

	$date = time(); //Current date
	$weekDay = date('w', strtotime('+1 day',$date));

	$tomorrow = strtolower($weekdays[$weekDay]);
	$tomorrow_custom_meals = get_user_meta($user_id, $tomorrow, true);

	if($custom_diet=='Yes'){
		$tomorrows_meals = $tomorrow_custom_meals;
	} else {
		$tomorrows_meals = assigned_diet_meals($assinged_diet_id, $tomorrow);
	}

	//array = assigned_diet_meals($id, $today)
	
	echo '<div class="col-lg-12 fridge"><a class="btn-white button" href="/profile"><ion-icon class="nav__icon" name="arrow-back-outline"></ion-icon>  Profile</a></div>';
	echo sort_fridge();
}

add_action( 'woocommerce_account_fridge_endpoint', 'fridge_endpoint_content' );


/**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
function fridge_insert_after_helper( $items, $new_items, $after ) {
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search( $after, array_keys( $items ) ) + 2;

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
function fridge_menu_items( $items ) {
    $new_items = array();
    $new_items['fridge'] = __( 'Fridge', 'woocommerce' );

    // Add the new item after `orders`.
    return fridge_insert_after_helper( $items, $new_items, 'schedule' );
}

add_filter( 'woocommerce_account_menu_items', 'fridge_menu_items' );



?>