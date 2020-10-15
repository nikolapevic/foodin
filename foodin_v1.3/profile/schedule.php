<?php
/**
     * Register new endpoint to use inside My Account page.
     *
     * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
     */
function foodin_feed_endpoints() {
	add_rewrite_endpoint( 'schedule', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'foodin_feed_endpoints' );

/**
     * Add new query var.
     *
     * @param array $vars
     * @return array
     */
function foodin_feed_query_vars( $vars ) {
	$vars[] = 'schedule';

	return $vars;
}

add_filter( 'query_vars', 'foodin_feed_query_vars', 0 );

/*
     * Change endpoint title.
     *
     * @param string $title
     * @return string
     */
function foodin_feed_endpoint_title( $title ) {
	global $wp_query;

	$is_endpoint = isset( $wp_query->query_vars['schedule'] );

	if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
		// New page title.
		$title = __( 'Schedule', 'woocommerce' );

		remove_filter( 'the_title', 'foodin_feed_endpoint_title' );
	}

	return $title;
}

add_filter( 'the_title', 'foodin_feed_endpoint_title' );


/**
     * Endpoint HTML content.
     */
function foodin_feed_endpoint_content() {
	
	$user_id = get_current_user_id();

	if($_POST['assigned_diet']){
		update_user_meta( $user_id, 'assigned_diet', $_POST['assigned_diet'] );
	}

	$week = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

	$product_id = get_the_author_meta( 'assigned_diet', $user_id );
	$product = wc_get_product( $product_id );
	if($product){
		$assigned_diet = $product_id;
	}

	$user = wp_get_current_user();

	global $plan_subscription;
	global $nutritional_subscription;

	$custom_diet = get_the_author_meta( 'custom_diet', $user_id );

	foodin_form_update('gender', $user_id);
	foodin_form_update('goal', $user_id);
	foodin_form_update('height', $user_id);
	foodin_form_update('weight', $user_id);

	if($_POST['weight']){
		update_user_meta( $user_id, 'starting_weight', $_POST['weight'] );
		update_calculate_bmr($_POST['gender'], $_POST['weight'], $_POST['height'], $_POST['age'], $_POST['activity'], $user_id);
	}

	foodin_form_update('desired_weight', $user_id);
	foodin_form_update('age', $user_id);
	foodin_form_update('body_type', $user_id);
	foodin_form_update('typical_day', $user_id);
	foodin_form_update('habits', $user_id);//Checkbox
	foodin_form_update('activity', $user_id);

	foodin_form_update('sleep', $user_id);
	foodin_form_update('drink', $user_id);
	foodin_form_update('motivation', $user_id);
	foodin_form_update('behavior', $user_id);
	foodin_form_update('vegetables', $user_id);//Checkbox
	foodin_form_update('products', $user_id);//Checkbox
	foodin_form_update('meat', $user_id);//Checkbox
	foodin_form_update('cooking', $user_id);


	$height = get_the_author_meta( 'height', $user_id );

	if (! $height){

		register_form($user_id);

	} elseif (active_subscription($nutritional_subscription,$user_id) || active_subscription($plan_subscription,$user_id)) {

		if(! $assigned_diet && $custom_diet == 'No'){

			pick_diets();

		} else {

			$date = getdate();
			$day = $date['weekday'];
			echo '<div class="ddwc-inline inline"><h2>This week&apos;s schedule</h2></div>';
			echo '<div class="week-container white-container">';
			foreach($week as $key => $weekday){
				if ($weekday == $day){
					echo '<a class="week-day day" href="#'. $weekday.'"><b>' . $weekday . ' </b></a>';
				} else {
					echo '<a class="week-day" href="#'. $weekday.'">' . $weekday . '</a>';
				};
			}
			echo '</div>';

			if ($custom_diet == 'Yes'){
				echo construct_day($user, 'monday', 'Monday');
				echo construct_day($user, 'tuesday', 'Tuesday');
				echo construct_day($user, 'wednesday', 'Wednesday');
				echo construct_day($user, 'thursday', 'Thursday');
				echo construct_day($user, 'friday', 'Friday');
				echo construct_day($user, 'saturday', 'Saturday');
				echo construct_day($user, 'sunday', 'Sunday');
				
				if (!construct_day($user, 'monday', 'Monday') && !construct_day($user, 'tuesday', 'Tuesday') && !construct_day($user, 'wednesday', 'Wednesday') && ! construct_day($user, 'thursday', 'Thursday') && !construct_day($user, 'friday', 'Friday') && !construct_day($user, 'saturday', 'Saturday') && !construct_day($user, 'sunday', 'Sunday')){
					echo '<div class="white-container col-lg-12">You still do not have a custom diet made. Ask your <a href="/profile" class="blue">nutritionist</a> to make you one, or follow a free <a class="blue" href="/product-category/plans">plan</a>.</div>';
				}

			} elseif ($assigned_diet) {

				echo display_diet($assigned_diet);

			}
			
			echo '<div class="meal-list-wrapper"><div class="meal-list"><form method="post" action="/profile/shopping-list" id="meal-list"></form></div><div class="meal-toggle btn-white inline"><h5>'.meals_icon().'<small class="meal-counter">0</small></h5></div></div>';
			
			?>
			<script>
				var menu = document.getElementsByClassName("account-left");
				if (screen.width < 591){
					menu[0].style.display = "none";
				}
				var day = document.getElementById("<?php echo $day?>");
				if (day){
					jQuery(document).ready(function($){
						if ( $(window).width() < 768 || window.Touch) {
							$('html, body').animate({
								scrollTop: $("#<?php echo $day?>").offset().top
							}, 1000);
						}
					});
				}
			</script>
			<?php
				
		}

	} else {

		$ns_img = '<img width="600" src="https://foodin.io/wp-content/uploads/2020/08/diet.jpg">';
		$id = $plan_subscription;
		
		$output = '';
		$output .= '<div class="empty-feed white-container">';
		$output .= '<h3><b>Get your personal plan</b></h3>';
		$output .= $ns_img;
		$output .= subscribe_to_plan($id);
		$output .= '</div>';
		$output .= '</div>';

		echo $output;

	}

}
//<i class="mdi mdi-checkbox-blank-circle"></i>
add_action( 'woocommerce_account_schedule_endpoint', 'foodin_feed_endpoint_content' );


function subscribe_to_plan($id){
	$subscription = wc_get_product($id);
	$name = $subscription->get_name();
	$description = $subscription->get_description();
	$price = $subscription->get_price_html();
	$add_to_cart = '<a href="?add-to-cart='.$id.'" data-quantity="1" class="btn btn-secondary btn-sm button add_to_cart_button ajax_add_to_cart" data-product_id="'.$id.'" data-product_sku="" aria-label="Read more about “'.$name.'”" rel="nofollow"><i class="mdi mdi-cart-outline"></i> Subscribe</a>';
	$output .= '<h4>' . $name . '</h4>';
	$output .= '<p>' . $description . '</p>';
	$output .= '<p class="offer-price mb-0">' . $price . '</p>';
	$output .= $add_to_cart;
	return $output;
}

/**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
function foodin_feed_insert_after_helper( $items, $new_items, $after ) {
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
function foodin_feed_menu_items( $items ) {
	$new_items = array();
	$new_items['schedule'] = __( 'Schedule', 'woocommerce' );

	// Add the new item after `orders`.
	return foodin_feed_insert_after_helper( $items, $new_items, 'dashboard' );
}

add_filter( 'woocommerce_account_menu_items', 'foodin_feed_menu_items' );


?>

