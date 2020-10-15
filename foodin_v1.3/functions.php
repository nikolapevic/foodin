<?php

// Foodin form update if there is a post
function foodin_form_update($name, $user_id){
	if($_POST[$name]){
		update_user_meta( $user_id, $name, $_POST[$name] );
	}
}

function add_meal_icon(){
	$icon = '<svg width="27px" height="26px" viewBox="0 0 27 26" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <title>cart_black</title>
    <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
        <g id="add_cart_green" transform="translate(-15.000000, -16.000000)" stroke="#FFFFFF" stroke-width="2.11184989">
            <g id="Group" transform="translate(16.500000, 17.500000)">
                <polyline id="Stroke-1" points="21.8369258 12.2765246 23.6216407 6.08051943 0.0818191921 6.08051943 3.76376461 19.2410806 13.2021636 19.2410806"></polyline>
                <path d="M24.2278844,19.2410806 L16.3440991,19.2410806 M20.3958091,15.0778447 L20.3958091,23.4043815" id="Stroke-5"></path>
                <line x1="6.48852523" y1="0.0880803855" x2="3.74775401" y2="6.08053568" id="Stroke-7"></line>
                <line x1="17.1047412" y1="0.0812574858" x2="19.8455124" y2="6.07371278" id="Stroke-9"></line>
            </g>
        </g>
    </g>
</svg>';
	
	return $icon;
}


function meals_icon(){
	$icon = '<svg width="28px" height="24px" viewBox="0 0 28 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <title>Group</title>
    <g id="Symbols" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
        <g id="cart_empty" transform="translate(2.000000, 2.000000)" stroke="#4C4C4C" stroke-width="2.11184989">
            <g id="Group" transform="translate(0.081819, 0.081257)">
                <polygon id="Stroke-1" points="23.5398215 5.99926195 2.84217094e-14 5.99926195 3.68194541 19.1598231 19.9181808 19.1598231"></polygon>
                <line x1="6.40670604" y1="0.00682289965" x2="3.66593482" y2="5.99927819" id="Stroke-7"></line>
                <line x1="17.022922" y1="0" x2="19.7636932" y2="5.99245529" id="Stroke-9"></line>
            </g>
        </g>
    </g>
</svg>';
	
	return $icon;
}

// Construct day for Schedule
function construct_day($user, $name, $day_name) {

	$values = get_the_author_meta( $name, $user->ID );
	$prod_values = [];
	foreach($values as $key => $id){
		$product = wc_get_product($id);
		if ($product) {
			array_push($prod_values, $id);
		}
	}

	$numItems = count($prod_values);
	$i = 0;
	$output = '';
	foreach($prod_values as $key => $id){
		if(++$i === $numItems){
			$output .= $id;
		} else {
			$output .= $id . ',';
		}
	}
	if ($output == ''){
		return null;
	} else {
		return do_shortcode('[diet_meals ids='. $output . ' day_name=' . $day_name. ']');
	}
}

global $plan_subscription;
$plan_subscription = 18826;
global $nutritional_subscription;
$nutritional_subscription = 19897;

// Calculate nutritional info from custom field
function nutritional_info($override,$needed,$weight,$value,$servings){
	if ($override == 'yes'){
		$output = ($needed / 100 * $value) / $servings;
	} elseif ($weight){
		$output = ($weight / 100 * $value) / $servings;
	}

	return $output;
}

// Update bundle products with nutritional info
function update_nutrition($post_id){
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_product', $post_id ) ) {
		return $post_id;
	}

	$product = wc_get_product( $post_id );
	if($product){
		$type = $product->get_type();
	}

	if( get_post_status( $post_id ) == 'publish' && $type == 'bundle'){
		$results = WC_PB_DB::query_bundled_items( array(
			'return'    => 'id=>product_id',
			'bundle_id' => $post_id,
		) );
		$meal_kcal = 0;
		$meal_proteins = 0;
		$meal_carbs = 0;
		$meal_fats = 0;
		$meal_fiber = 0;

		$servings = intval(get_post_meta( $post_id, 'servings', true ));
		if (empty($servings)){
			$servings = 1;
		}
		foreach($results as $key => $id){
			$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
			$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
			$description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));

			$bproduct = wc_get_product( $id );
			$weight = $bproduct->get_weight();

			$calories = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'calories', true ),$servings),1);
			$proteins = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'proteins', true ),$servings),1);
			$carbs = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'carbs', true ),$servings),1);
			$fats = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fats', true ),$servings),1);
			$fiber = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fiber', true ),$servings),1);
			$meal_kcal += $calories;
			$meal_proteins += $proteins;
			$meal_carbs += $carbs;
			$meal_fats += $fats;
			$meal_fiber += $fiber;
		}

		update_post_meta( $post_id, 'calories', $meal_kcal);
		update_post_meta( $post_id, 'proteins', $meal_proteins);
		update_post_meta( $post_id, 'carbs', $meal_carbs);
		update_post_meta( $post_id, 'fats', $meal_fats);
		update_post_meta( $post_id, 'fiber', $fiber);
	}

}

add_action( 'save_post', 'update_nutrition' );


function bundle_test_function(){
	global $post;
	$product = wc_get_product( $post->ID );
	if($product){
		$type = $product->get_type();
	}

	if ( $type == 'bundle' && empty(get_post_meta($post->ID, 'servings'))) {
		add_post_meta($post->ID, 'servings', 2);
	}

	if ( ($type == 'bundle' || $type == 'simple') && empty(get_post_meta($post->ID, 'calories'))){
		add_post_meta($post->ID, 'calories', 2);
		add_post_meta($post->ID, 'carbs', 2);
		add_post_meta($post->ID, 'proteins', 2);
		add_post_meta($post->ID, 'fats', 2);
		add_post_meta($post->ID, 'fiber', 2);
	}

}

add_action( 'add_meta_boxes', 'bundle_test_function' );

function client_checks($array, $title){
	$output = '';
	$output = '<h4>' . $title . '</h4>';
	foreach($array as $k => $v){
		$output .= '<div class="dash-cont preference inline">' . $v . '</div>';
	}
	if(empty($array)){
		$output .= '<div class="dash-cont preference inline">No preferences</div>';
	}
	return $output;
}

function client_select($meta, $title, $array){
	$output = '';
	$output = '<h4>' . $title . '</h4>';
	foreach($array as $k => $v){
		if ($v == $meta){
			$output .= '<div class="dash-cont preference inline green-bcg">' . $v . '</div>';
		} else {
			$output .= '<div class="dash-cont preference inline">' . $v . '</div>';
		}

	}
	return $output;
}


function percentage($nominator,$denominator){
	$output = round(intval($nominator)/intval($denominator)*100,1);
	return $output;
};

function assigned_diet_meals($id, $today){
	$bproduct = wc_get_product($id);
	if($bproduct){

		$day = strtolower($today);

		$values = get_post_meta($id, $day,true);

		$meals = explode(',',$values);
		$products = [];
		foreach($meals as $meal){
			$mproduct = wc_get_product($meal);
			if($mproduct){
				array_push($products,$meal);
			}
		}
	}

	return $products;
}

function recommended_diets($array,$user,$my_diets_title,$href){
	$preview = '<div class="schedule-head">';
	$preview .= '<div class="schedule-left"><h3>'.$my_diets_title.'</h3></div>';
	$preview .= '<div class="schedule-right">';
	$preview .= '<a class="text-right blue" href="'.$href.'">SEE MORE</a>';
	$preview .= '</div>'; 
	$preview .= '</div>';
	$preview .= '<div class="preview-wrapper">';
	$preview .= '<div class="scrollbtn"><ion-icon class="nav__icon" name="arrow-forward-outline"></ion-icon></div>';
	$preview .= '<div class="preview-container">';

	foreach($array as $k => $id){
		$id = (float)$id;
		$product = wc_get_product( $id );
		if($product){
			$i++;
			$pname = $product->get_name();
			$image_id = $product->get_image_id();
			$image_url = wp_get_attachment_url($image_id);
			$link = get_permalink($id);
			$servings = get_post_meta($id, 'servings', true);
			$duration = get_post_meta($id, 'duration', true);
			if (empty($servings)){
				$servings = 1;
			}
			if (count($array) == $i){
				$meals .= $id;
			} else {
				$meals .= $id . ',';
			}

			$preview .= '<a href="'.$link.'"><div class="meal-preview diet">';
			if ($product->get_image_id()){
				$preview .= '<img class="plan_pic" loading="lazy" src="' . groci_resize( wp_get_attachment_url($product->get_image_id()), 500, 500, true, true, true ) . '">';
			} else {
				$preview .= '<img class="plan_pic" loading="lazy" src="' . wc_placeholder_img_src() . '">';
			}
			$preview .= '<div class="meal-preview-info diet-hover">';
			$preview .= '<h4>' . $product->get_name() . '</h4>';
			$duration = get_post_meta($id,'duration',true);
			$rating = $product->get_average_rating();;
			if($rating){
				$preview .= '<div class="inline"><ion-icon name="star"></ion-icon> ' . $rating . '</div>';
			} else {
				$preview .= '<div class="inline"><ion-icon name="star"></ion-icon> 5.00</div>';
			}
			$preview .= '</div>';
			$preview .= '</div></a>';
		}
	}
	$preview .= '</div>';
	$preview .= '</div>';

	return $preview;
}

function recommended_meals($array,$user,$my_meals_title,$href){
	$output = '';
	$i=0;
	$client_id = $user->ID;
	$qr_user = get_current_user_id();

	global $plan_subscription;
	global $nutritional_subscription;


	$prodArr = [];
	foreach($array as $val){
		$product = wc_get_product($val);
		if($product){
			array_push($prodArr,$val);
		}
	}


	$needed_kcal = get_user_meta($client_id, 'amr', true);
	$needed_fat = get_user_meta($client_id, 'needed_fat', true);
	$needed_protein = get_user_meta($client_id, 'needed_protein', true);
	$needed_fiber = get_user_meta($client_id, 'needed_fiber', true);
	$needed_carbs = get_user_meta($client_id, 'needed_carbs', true);

	$day_kcal = 0; $day_proteins = 0; $day_carbs = 0; $day_fats = 0; $day_fiber = 0;
	foreach($array as $k => $id){
		$id = (float)$id;
		$product = wc_get_product( $id );
		if($product){
			$i++;
			$pname = $product->get_name();
			$image_id = $product->get_image_id();
			$image_url = wp_get_attachment_url($image_id);
			$link = get_permalink($id);
			$servings = get_post_meta($id, 'servings', true);
			$duration = get_post_meta($id, 'duration', true);
			if (empty($servings)){
				$servings = 1;
			}
			if (count($prodArr) == $i){
				$meals .= $id;
			} else {
				$meals .= $id . ',';
			}
			$results = WC_PB_DB::query_bundled_items( array(
				'return'    => 'id=>product_id',
				'bundle_id' => $id,
			) );

			$meal_kcal = 0; $meal_proteins = 0; $meal_carbs = 0; $meal_fats = 0; $meal_fiber = 0;
			foreach($results as $key => $id){
				$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
				$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
				$description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));

				$bproduct = wc_get_product( $id );
				$bname = $bproduct->get_name();
				$weight = $bproduct->get_weight() * 1000;

				$calories = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'calories', true ),$servings),1);
				$proteins = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'proteins', true ),$servings),1);
				$carbs = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'carbs', true ),$servings),1);
				$fats = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fats', true ),$servings),1);
				$fiber = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fiber', true ),$servings),1);
				$meal_kcal += $calories; $day_kcal += $calories;
				$meal_proteins += $proteins; $day_proteins += $proteins;
				$meal_carbs += $carbs; $day_carbs += $carbs;
				$meal_fats += $fats; $day_fats += $fats;
				$meal_fiber += $fiber; $day_fiber += $fiber;
			}
		}
	}
	if ($i==0){
		$meals = '<div class="dash-cont preference">No meals scheduled for today</div>';
	}
	$meal_kcal = round($meal_kcal, 1);
	$meal_proteins = round($meal_proteins, 1);
	$meal_carbs = round($meal_carbs, 1);
	$meal_fats = round($meal_fats, 1);
	$meal_fiber = round($meal_fiber, 1);
	$output .= '<div class="schedule-head">';
	$output .= '<div class="schedule-left"><h3>'.$my_meals_title.'</h3></div>';
	if ($href == null){
		$output .= '<div class="schedule-right">';
		$output .= '<div class="macro kcal" data-percent="'.percentage($day_kcal,$needed_kcal).'"><p>Kcal</p><h4>'.$day_kcal.'</h4></div>';
		$output .= '<div class="macro protein" data-percent="'.percentage($day_proteins,$needed_protein).'"><p>Protein</p><h4>'.$day_proteins.'</h4></div>';
		$output .= '<div class="macro carbs" data-percent="'.percentage($day_carbs,$needed_carbs).'"><p>Carbs</p><h4>'.$day_carbs.'</h4></div>';
		$output .= '<div class="macro fats" data-percent="'.percentage($day_fats,$needed_fat).'"><p>Fats</p><h4>'.$day_fats.'</h4></div>';
		$output .= '<div class="macro fiber" data-percent="'.percentage($day_fiber,$needed_fiber).'"><p>Fiber</p><h4>'.$day_fiber.'</h4></div>';
		$output .= '</div>';
	} else {
		$output .= '<div class="schedule-right">';
		$output .= '<a class="text-right blue" href="'.$href.'">SEE MORE</a>';
		$output .= '</div>';
	}
	$output .= '</div>';
	if (!empty($prodArr)){
		$output .= '<div class="scheduled-meals">'.do_shortcode('[diet_meals ids='.$meals.' day_name=' . $day_name. ']').'</div>';
	} else {
		$output .= '<div class="scheduled-meals"><div class="dash-cont preference">No meals are assigned for today</div></div>';
	}



	return $output;
}

function my_meals($array,$user,$my_meals_title,$href){
	$output = '';
	$i=0;
	$client_id = $user->ID;
	$qr_user = get_current_user_id();

	global $plan_subscription;
	global $nutritional_subscription;

	if(active_subscription($nutritional_subscription,$qr_user) || active_subscription($plan_subscription,$qr_user)){
		$prodArr = [];
		foreach($array as $val){
			$product = wc_get_product($val);
			if($product){
				array_push($prodArr,$val);
			}
		}


		$needed_kcal = get_user_meta($client_id, 'amr', true);
		$needed_fat = get_user_meta($client_id, 'needed_fat', true);
		$needed_protein = get_user_meta($client_id, 'needed_protein', true);
		$needed_fiber = get_user_meta($client_id, 'needed_fiber', true);
		$needed_carbs = get_user_meta($client_id, 'needed_carbs', true);

		$day_kcal = 0; $day_proteins = 0; $day_carbs = 0; $day_fats = 0; $day_fiber = 0;
		foreach($array as $k => $id){
			$id = (float)$id;
			$product = wc_get_product( $id );
			if($product){
				$i++;
				$pname = $product->get_name();
				$image_id = $product->get_image_id();
				$image_url = wp_get_attachment_url($image_id);
				$link = get_permalink($id);
				$servings = get_post_meta($id, 'servings', true);
				$duration = get_post_meta($id, 'duration', true);
				if (empty($servings)){
					$servings = 1;
				}
				if (count($prodArr) == $i){
					$meals .= $id;
				} else {
					$meals .= $id . ',';
				}
				$results = WC_PB_DB::query_bundled_items( array(
					'return'    => 'id=>product_id',
					'bundle_id' => $id,
				) );

				$meal_kcal = 0; $meal_proteins = 0; $meal_carbs = 0; $meal_fats = 0; $meal_fiber = 0;
				foreach($results as $key => $id){
					$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
					$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
					$description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));

					$bproduct = wc_get_product( $id );
					$bname = $bproduct->get_name();
					$weight = $bproduct->get_weight() * 1000;

					$calories = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'calories', true ),$servings),1);
					$proteins = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'proteins', true ),$servings),1);
					$carbs = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'carbs', true ),$servings),1);
					$fats = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fats', true ),$servings),1);
					$fiber = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fiber', true ),$servings),1);
					$meal_kcal += $calories; $day_kcal += $calories;
					$meal_proteins += $proteins; $day_proteins += $proteins;
					$meal_carbs += $carbs; $day_carbs += $carbs;
					$meal_fats += $fats; $day_fats += $fats;
					$meal_fiber += $fiber; $day_fiber += $fiber;
				}
			}
		}
		if ($i==0){
			$meals = '<div class="dash-cont preference">No meals scheduled for today</div>';
		}
		$meal_kcal = round($meal_kcal, 1);
		$meal_proteins = round($meal_proteins, 1);
		$meal_carbs = round($meal_carbs, 1);
		$meal_fats = round($meal_fats, 1);
		$meal_fiber = round($meal_fiber, 1);
		$output .= '<div class="schedule-head">';
		$output .= '<div class="schedule-left"><h3>'.$my_meals_title.'</h3>';

		//Assistant function - better to migrate to functions or assistant.php

		$starvingAns = ['You must be starving... Go shop some groceries, you need ', 'This schedule is way under your optimal intake. If you don&apos;t wanna starve yourself, go and shop for groceries. You need ','In case you don&apos;t wanna starve yourself, go and buy some food. You need '];
		$lowAns = ['I feel like you&apos;re missing some meals, you should add ','Hey ' .$user->first_name . ', you are not doing bad, but try to put in ', 'You should give yourself a bit more calories. Go and add '];
		$highAns = ['You shouldn&apos;t go above than ', 'Don&apos;t eat too much today, you are over your daily recommended '];
		$optimalAns = ['Awesome ' .$user->first_name . ', you are on your way to success!', 'Today&apos;s schedule is looking great', 'I feel like you are crushing this today' . $user->first_name . '.'];

		$prepared = 'By the way, I prepared your shopping list for tomorrow.';
		$shopping_list = 'Shopping list <ion-icon class="nav__icon" name="arrow-forward-outline"></ion-icon>';
		$assistant_img_url = "https://foodin.io/wp-content/uploads/2020/09/assistant.jpg";
		$assistantimg = groci_resize( $assistant_img_url, 100, 100, true, true, true );

		$too_much = '';
		$too_low = '';
		$low = '';
		$just_right = '';

		$kcalpct = percentage($day_kcal,$needed_kcal);
		if($kcalpct > 100){
			$response = $highAns[array_rand($highAns,1)] . $needed_kcal.'.';
		} elseif ($kcalpct > 35 && $kcalpct < 75) {
			$response = $lowAns[array_rand($lowAns,1)] . ($needed_kcal - $day_kcal) . ' calories to keep up.';
		} elseif ($kcalpct < 35) {
			$response =  $starvingAns[array_rand($starvingAns,1)] . ($needed_kcal - $day_kcal) . ' calories to keep up.';
		} else {
			$response = $optimalAns[array_rand($optimalAns,1)];
		}
		$output .= '<p>You are scheduled to eat '.$day_kcal.' kcal. </p>';
		$output .= '<div class="assistant disappear">';
		$output .= '<div class="assistant_pic"><img src="'.$assistantimg.'"></div>';
		$output .= '<div class="answer"><p>Assistant</p>';
		$output .= '<div class="typing preference inline dash-cont">&bull; &bull; &bull;</div>';
		$output .= '<p class="chat disappear">'.$response.'</p>';
		$output .= '<p class="chat list disappear">'.$prepared.'</p>';
		$output .= '<a href="/profile/shopping-list" class="chat link disappear">'.$shopping_list.'</a>';
		$output .= '</div>';
		$output .= '</div>';

		//Assistant function *****************************************

		$output .= '</div>';
		$output .= '<div class="schedule-right">';
		$output .= '<div class="macro kcal" data-percent="'.percentage($day_kcal,$needed_kcal).'"><p>Kcal</p><h4>'.$day_kcal.'</h4></div>';
		$output .= '<div class="macro protein" data-percent="'.percentage($day_proteins,$needed_protein).'"><p>Protein</p><h4>'.$day_proteins.'</h4></div>';
		$output .= '<div class="macro carbs" data-percent="'.percentage($day_carbs,$needed_carbs).'"><p>Carbs</p><h4>'.$day_carbs.'</h4></div>';
		$output .= '<div class="macro fats" data-percent="'.percentage($day_fats,$needed_fat).'"><p>Fats</p><h4>'.$day_fats.'</h4></div>';
		$output .= '<div class="macro fiber" data-percent="'.percentage($day_fiber,$needed_fiber).'"><p>Fiber</p><h4>'.$day_fiber.'</h4></div>';
		$output .= '</div>';
		$output .= '</div>';
		if (!empty($prodArr)){
			$output .= '<div class="scheduled-meals">'.do_shortcode('[diet_meals ids='.$meals.' day_name=' . $day_name. ']').'</div>';
		} else {
			$output .= '<div class="scheduled-meals"><div class="dash-cont preference">No meals are assigned for today</div></div>';
		}

	} else {
		$output .= '<div class="dash-cont preference">You can&apos;t view your schedule since you aren&apos;t subscribed. Subscribe to achieve goals faster. <a href="/profile/schedule" class="blue">Subscribe</a>';
		$output .= '</div>';
	}

	return $output;
}


// Shopping list Array var is where you put all of the meals that your shopping list requires
function shopping_list($array, $sl_title, $no_items_description){
	$output = '';
	$i=0;
	$qr_user = get_current_user_id();

	global $plan_subscription;
	global $nutritional_subscription;

	if(active_subscription($nutritional_subscription,$qr_user) || active_subscription($plan_subscription,$qr_user)){
		$prodArr = [];
		foreach($array as $val){
			$product = wc_get_product($val);
			if($product){
				array_push($prodArr,$val);
			}
		}

		$shopping_list = array();
		foreach ($prodArr as $product_id){
			$product = wc_get_product($product_id);
			if ($product->get_image_id()){
				$mealimage = '<img class="meal__pic" src="' .groci_resize( wp_get_attachment_url($product->get_image_id()), 100, 100, true, true, true ). '">';
			} else {
				$mealimage = '<img class="meal__pic" src="' .  wc_placeholder_img_src(). '">';
			}
			$mealimgs .= $mealimage;
			$results = WC_PB_DB::query_bundled_items( array(
				'return'    => 'id=>product_id',
				'bundle_id' => $product_id,
			) );
			foreach ($results as $key => $id){
				$title =  WC_PB_DB::get_bundled_item_meta( $key, 'title' );
				$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
				$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
				$description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));

				$bproduct = wc_get_product( $id );
				$img_id = $bproduct->get_image_id();
				$type = $bproduct->get_type();
				$name = $bproduct->get_name();
				$weight = $bproduct->get_weight() * 1000;
				$categories = array();
				$terms = wp_get_post_terms( $id, 'product_cat' );
				foreach ( $terms as $term ) if ($term->parent == 145) array_push($categories, $term->name);
				$maincat = $categories[0];

				if (empty($shopping_list)){
					array_push($shopping_list, array(
						'id' => $id,
						'name' => $name,
						'qty' => $quantity,
						'description' => $description
					));
				} else {
					$bool = false;
					foreach($shopping_list as $k => $shopping_item){
						if ($shopping_item['id'] == $id){
							$shopping_list[$k]['qty'] = $shopping_item['qty'] + $quantity;
							$shopping_list[$k]['description'] = $shopping_item['description'] + $description;
							$bool = true;
						} elseif ($k == count($shopping_list)-1 && ! $bool) {
							array_push($shopping_list, array(
								'id' => $id,
								'name' => $name,
								'qty' => $quantity,
								'description' => $description
							));
						}
					}
				}
			}
		}

		if (! empty($shopping_list)){
			foreach($shopping_list as $shopping_item){
				$description = $shopping_item['description'];
				$quantity = $shopping_item['qty'];
				$bproduct = wc_get_product( $shopping_item['id'] );
				$bname = $bproduct->get_name();
				if ($title){
					$bname = $title;
				}
				$mweight = $bproduct->get_weight() * 1000;
				$bprice = $bproduct->get_price_html();

				$weight = $mweight;

				$varoutput = '';
				$variations = $bproduct->get_children();
				$brand_name = '';
				$n = 0;
				if($variations){
					foreach($variations as $k => $v){
						$n++;
						$vproduct = wc_get_product($v);
						$vname = $vproduct->get_name();
						$vprice = $vproduct->get_price_html();
						$vweight = $vproduct->get_weight() * 1000;

						$picked_shop = 'Any Shop';
						if($_COOKIE['shop']){
							$picked_shop = $_COOKIE['shop'];
						}

						$shop = $vproduct->get_attribute('shop');
						$brand = $vproduct->get_attribute('brand');
						$default = $bproduct->get_default_attributes();
						$default_brand = $default['brand'];
						if ($shop == $picked_shop){
							if($brand == $default_brand){
								$brand_name = $default_brand;
								$id = $v;
								$bprice = $vprice;
								$weight = $vweight;
							} elseif ($n == 1) {
								$brand_name = $brand;
								$id = $v;
								$bprice = $vprice;
								$weight = $vweight;
							}
						}
					}
				}

				if ($description){
					$need = $description . 'g';
					if($weight > 0){
						if ($description < $weight){
							$quantity = 1;
						} else {
							$quantity = round($description/$weight,1);
							if ($quantity > 1.2 && $quantity < 1.5){
								$quantity = 2;
							} else {
								$quantity = round($quantity,0);
							}
						}
					}
				} elseif (! $description){
					$need = $quantity;
					if($mweight > 0){
						if($quantity * $mweight < $weight){
							$quantity = 1;
						} else {
							$quantity = round($quantity*$mweight/$weight,0);
						}
					}
				}

				if($weight < 1000){
					$title_weight = $weight;
					$unit = 'g';
				} else {
					$title_weight = $weight/1000;
					$unit = 'kg';
				}

				$add_to_cart = '<a href="?add-to-cart='.$id.'" data-quantity="'.$quantity.'" class="btn btn-secondary btn-sm button add_to_cart_button ajax_add_to_cart" data-product_id="'.$id.'" data-product_sku="" aria-label="Read more about “'.$bpname.'”" rel="nofollow"><i class="mdi mdi-cart-outline"></i> Add</a>';

				if ($bproduct->get_image_id()){
					$image = '<img class="bundle-pic" src="' . wp_get_attachment_url($bproduct->get_image_id(),'thumbinail'). '">';
				} else {
					$image = '<img class="bundle-pic" src="' .  wc_placeholder_img_src(). '">';
				}
				$shopping_products.= '<div class="bundle-product">';
				$shopping_products .= $image;
				$shopping_products .= '<div class="bundle-product-description">';
				$shopping_products .= '<h5>' . $need . ' ' . $bname . '</h5>';
				$shopping_products .= 'Add '. $quantity . ' x ' . $title_weight . $unit . ' ' . $brand_name;
				$shopping_products .= '<p class="offer-price mb-0">' . $bprice . '</p>';
				$shopping_products .= '</div>';
				$shopping_products .= '<div class="product-footer">';
				$shopping_products .= $add_to_cart;
				$shopping_products .= '</div>';
				$shopping_products .= '</div>';
			}

			$output = '<div class="col-lg-12 fridge"><h2>'.$sl_title.'</h2><h4>Meals</h4><div class="meal__pics white-container">'.$mealimgs.'</div><h4>Ingredients</h4><div class="white-container fridge-items">'. $shopping_products. '</div></div>';
		} else {
			
			$output = '<div class="col-lg-12 fridge"><h2>'.$sl_title.'</h2><div class="white-container fridge-items">'.$no_items_description.'</div></div>';
				
		}

	} else {
		$output = '<div class="col-lg-12 fridge"><h2>Tomorrow&apos;s List</h2><div class="white-container fridge-items">You can&apos;t view your shopping liste since you aren&apos;t subscribed.</div></div>';
	}

	return $output;
}

function client_meals($array,$user){
	$output = '';
	$i=0;
	$client_id = $user->ID;
	$qr_user = get_current_user_id();

	global $plan_subscription;
	global $nutritional_subscription;

	if(active_subscription($nutritional_subscription,$qr_user) || active_subscription($plan_subscription,$qr_user)){
		$length = count($array);


		$needed_kcal = get_user_meta($client_id, 'amr', true);
		$needed_fat = get_user_meta($client_id, 'needed_fat', true);
		$needed_protein = get_user_meta($client_id, 'needed_protein', true);
		$needed_fiber = get_user_meta($client_id, 'needed_fiber', true);
		$needed_carbs = get_user_meta($client_id, 'needed_carbs', true);

		$day_kcal = 0; $day_proteins = 0; $day_carbs = 0; $day_fats = 0; $day_fiber = 0;
		foreach($array as $k => $id){
			$id = (float)$id;
			$product = wc_get_product( $id );
			if($product){
				$i++;
				$pname = $product->get_name();
				$image = $product->get_image();
				$link = get_permalink($id);
				$servings = intval(get_post_meta($id, 'servings', true));
				if (empty($servings)){
					$servings = 1;
				}
				$results = WC_PB_DB::query_bundled_items( array(
					'return'    => 'id=>product_id',
					'bundle_id' => $id,
				) );
				$meal_kcal = 0; $meal_proteins = 0; $meal_carbs = 0; $meal_fats = 0; $meal_fiber = 0;
				foreach($results as $key => $id){
					$quantity = WC_PB_DB::get_bundled_item_meta( $key, 'quantity_min' );
					$override = WC_PB_DB::get_bundled_item_meta($key, 'override_description');
					$description = number_format(WC_PB_DB::get_bundled_item_meta($key, 'description'));

					$bproduct = wc_get_product( $id );
					$bname = $bproduct->get_name();
					$weight = $bproduct->get_weight() * 1000;

					$calories = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'calories', true ),$servings),1);
					$proteins = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'proteins', true ),$servings),1);
					$carbs = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'carbs', true ),$servings),1);
					$fats = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fats', true ),$servings),1);
					$fiber = number_format(nutritional_info($override,$description,$weight,get_post_meta( $id, 'fiber', true ),$servings),1);
					$meal_kcal += $calories; $day_kcal += $calories;
					$meal_proteins += $proteins; $day_proteins += $proteins;
					$meal_carbs += $carbs; $day_carbs += $carbs;
					$meal_fats += $fats; $day_fats += $fats;
					$meal_fiber += $fiber; $day_fiber += $fiber;
				}
				$output .= '<a href="'.$link.'"><div class="scheduled-cont"><div class="scheduled-img">'. $image . '</div><div class="scheduled-info"><h5>' .$pname . '</h5></div></div></a>';
			}
		}
		if ($i==0){
			$output .= '<div class="dash-cont preference">No meals scheduled for today</div>';
		}
		$meal_kcal = round($meal_kcal, 1);
		$meal_proteins = round($meal_proteins, 1);
		$meal_carbs = round($meal_carbs, 1);
		$meal_fats = round($meal_fats, 1);
		$meal_fiber = round($meal_fiber, 1);
		$output .= '<div class="nutrition"><h4 class="text-left">Today&apos;s Macronutrients</h4>';
		$output .= '<div class="macro kcal" data-percent="'.percentage($day_kcal,$needed_kcal).'"><p>Kcal</p><h4>'.$day_kcal.'</h4></div>';
		$output .= '<div class="macro protein" data-percent="'.percentage($day_proteins,$needed_protein).'"><p>Protein</p><h4>'.$day_proteins.'</h4></div>';
		$output .= '<div class="macro carbs" data-percent="'.percentage($day_carbs,$needed_carbs).'"><p>Carbs</p><h4>'.$day_carbs.'</h4></div>';
		$output .= '<div class="macro fats" data-percent="'.percentage($day_fats,$needed_fat).'"><p>Fats</p><h4>'.$day_fats.'</h4></div>';
		$output .= '<div class="macro fiber" data-percent="'.percentage($day_fiber,$needed_fiber).'"><p>Fiber</p><h4>'.$day_fiber.'</h4></div>';
		$output .= '</div>';
	} else {
		$output .= '<div class="dash-cont preference">You can&apos;t view your schedule since you aren&apos;t subscribed.</div>';
	}

	return $output;
}

function client_info($title, $name, $user_id){
	$meta = get_user_meta($user_id, $name, true);
	if($meta){
		$output = '<tr><td>'.$title.'</td><td><h6>' . $meta . '</h6></td></tr>';
	} else {
		$output = '<tr><td>'.$title.'</td><td><h6>n/a</h6></td></tr>';
	}
	return $output;
}


function active_subscription($product_id,$user_id){
	$active_subscriptions = get_posts( array(
		'numberposts' => 10, 
		'meta_key'    => '_customer_user',
		'meta_value'  => $user_id,
		'post_type'   => 'shop_subscription', // Subscription post type
		'post_status' => 'wc-active', // Active subscription
		'fields'      => 'ids', // return only IDs (instead of complete post objects)
	) );

	$i = 0;
	foreach($active_subscriptions as $sub_id){
		$order = new WC_Order( $sub_id );
		$items = $order->get_items();
		foreach ( $items as $product ) {
			if ($product['product_id'] == $product_id){
				$output = true;
				$i++;
			}
		}
	}

	if($i == 0){
		$output = false;
	}

	return $output;
}


function change_role_on_purchase( $order_id ) {

	$order = new WC_Order( $order_id );
	$items = $order->get_items();

	global $plan_subscription;
	global $nutritional_subscription;

	foreach ( $items as $item ) {
		$product_name = $item['name'];
		$product_id = $item['product_id'];
		$product_variation_id = $item['variation_id'];

		if ( $order->user_id > 0 && $product_id == $plan_subscription ) {
			update_user_meta( $order->user_id, 'paying_customer', 1 );
			$user = new WP_User( $order->user_id );

			// Add role
			$user->add_role( 'nutritionist' );
		}

		if ( $order->user_id > 0 && $product_id == $nutritional_subscription ) {
			update_user_meta( $order->user_id, 'paying_customer', 1 );
			$user = new WP_User( $order->user_id );

			// Add role
			$user->add_role( 'nutritionist' );
		}
	}
}

add_action( 'woocommerce_order_status_processing', 'change_role_on_purchase' );

//Convert Measures
add_filter('woocommerce_product_data_tabs', function($tabs) {
	$tabs['conversions'] = [
		'label' => __('Conversion Measures', 'txtdomain'),
		'target' => 'conversion_measures',
		'class' => ['show_if_variable'],
		'priority' => 45
	];
	return $tabs;
});

add_action('woocommerce_product_data_panels', function() {
?><div id="conversion_measures" class="panel woocommerce_options_panel hidden"><?php

	woocommerce_wp_text_input([
		'id' => 'cup',
		'type' => 'text',
		'label' => __('Cup', 'txtdomain'),
		'wrapper_class' => 'show_if_variable',
	]);
	woocommerce_wp_text_input([
		'id' => 'tbsp',
		'type' => 'text',
		'label' => __('Tablespoon', 'txtdomain'),
		'wrapper_class' => 'show_if_variable',
	]);
	woocommerce_wp_text_input([
		'id' => 'tsp',
		'type' => 'text',
		'label' => __('Teaspoon', 'txtdomain'),
		'wrapper_class' => 'show_if_variable',
	]);

	?></div><?php
});


add_action('woocommerce_process_product_meta', function($post_id) {
	$product = wc_get_product($post_id);

	sanitize_product_update('cup', $product);
	sanitize_product_update('tbsp', $product);
	sanitize_product_update('tsp', $product);
	
	$product->save();
});


//Recipe Tab in Product Meta Data
add_filter('woocommerce_product_data_tabs', function($tabs) {
	$tabs['recipe_steps'] = [
		'label' => __('Recipe Steps', 'txtdomain'),
		'target' => 'recipe_steps_data',
		'class' => ['show_if_bundle'],
		'priority' => 45
	];
	return $tabs;
});

add_action('woocommerce_product_data_panels', function() {
?><div id="recipe_steps_data" class="panel woocommerce_options_panel hidden"><?php

	woocommerce_wp_text_input([
		'id' => 'duration',
		'label' => __('Duration', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'servings',
		'label' => __('Servings', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_1',
		'label' => __('Image 1', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_1',
		'type' => 'textbox',
		'label' => __('Step 1', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_2',
		'label' => __('Image 2', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_2',
		'type' => 'textbox',
		'label' => __('Step 2', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_3',
		'label' => __('Image 3', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_3',
		'type' => 'textbox',
		'label' => __('Step 3', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_4',
		'label' => __('Image 4', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_4',
		'type' => 'textbox',
		'label' => __('Step 4', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_5',
		'label' => __('Image 5', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_5',
		'type' => 'textbox',
		'label' => __('Step 5', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_6',
		'label' => __('Image 6', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_6',
		'type' => 'textbox',
		'label' => __('Step 6', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_7',
		'label' => __('Image 7', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_7',
		'type' => 'textbox',
		'label' => __('Step 7', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_8',
		'label' => __('Image 8', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_8',
		'type' => 'textbox',
		'label' => __('Step 8', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_text_input([
		'id' => 'step_image_9',
		'label' => __('Image 9', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);
	woocommerce_wp_textarea_input([
		'id' => 'step_9',
		'type' => 'textbox',
		'label' => __('Step 9', 'txtdomain'),
		'wrapper_class' => 'show_if_bundle',
	]);

	?></div><?php
});


function sanitize_product_update($name, $product){
	if($_POST[$name]){
		$product->update_meta_data($name, sanitize_text_field($_POST[$name]));
	}
}

function decode_product_update($name, $product){
	if($_POST[$name]){
		$product->update_meta_data($name, html_entity_decode($_POST[$name]));
	}
}


add_action('woocommerce_process_product_meta', function($post_id) {
	$product = wc_get_product($post_id);

	sanitize_product_update('servings', $product);
	sanitize_product_update('duration', $product);
	sanitize_product_update('step_image_1', $product);
	sanitize_product_update('step_image_2', $product);
	sanitize_product_update('step_image_3', $product);
	sanitize_product_update('step_image_4', $product);
	sanitize_product_update('step_image_5', $product);
	sanitize_product_update('step_image_6', $product);
	sanitize_product_update('step_image_7', $product);
	sanitize_product_update('step_image_8', $product);
	sanitize_product_update('step_image_9', $product);
	decode_product_update('step_1', $product);
	decode_product_update('step_2', $product);
	decode_product_update('step_3', $product);
	decode_product_update('step_4', $product);
	decode_product_update('step_5', $product);
	decode_product_update('step_6', $product);
	decode_product_update('step_7', $product);
	decode_product_update('step_8', $product);
	decode_product_update('step_9', $product);

	$product->save();
});

add_action('woocommerce_product_data_panels', function() {

?><div id="plan_meals_data" class="panel woocommerce_options_panel hidden">

	<p class="form-field _select_field">
		<label for="_select">Meal info</label>
		<span style="width:80%;" id="preview_pic"></span>
		<br><span style="width:80%;color:red;" id="error"></span>
	</p>
	<?php

	$recipes = wc_get_products(array(
		'category' => array('recipes'),
		'posts_per_page' => 1000,
		'status' => 'publish'
	));

	foreach($recipes as $k => $option){
		$option_id = $option->get_id();
		$option_name = $option->get_name();
		$img_id = $option->get_image_id();
		$option_pic = wp_get_attachment_image_url($img_id, 'full');
		$options[$option_pic] = $option_id . ' | ' . $option_name;
	}
	
	woocommerce_wp_text_input([
		'id' => 'myInput',
		'label' => __('Search...', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => 'Search...',
	]);

	woocommerce_wp_select( array(
		'id'      => '_select',
		'label'   => __( 'Pick Meal', 'woocommerce' ),
		'wrapper_class' => 'show_if_plan',
		'options' =>  $options, 
	) );

	woocommerce_wp_text_input([
		'id' => 'monday',
		'label' => __('Monday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);

	woocommerce_wp_text_input([
		'id' => 'tuesday',
		'label' => __('Tuesday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);
	woocommerce_wp_text_input([
		'id' => 'wednesday',
		'label' => __('Wednesday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);

	woocommerce_wp_text_input([
		'id' => 'thursday',
		'label' => __('Thursday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);

	woocommerce_wp_text_input([
		'id' => 'friday',
		'label' => __('Friday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);

	woocommerce_wp_text_input([
		'id' => 'saturday',
		'label' => __('Saturday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);
	woocommerce_wp_text_input([
		'id' => 'sunday',
		'label' => __('Sunday', 'txtdomain'),
		'wrapper_class' => 'show_if_plan',
		'placeholder' => '1234',
		'class' => 'day',
	]);


	?></div>
<script>
	var id = document.getElementById("_select");
	id.addEventListener("change", function(){
		var mealVal = id.selectedOptions[0].innerText.split(" | ")[0];
		output.innerHTML = '<img width=300 src="' + id.value + '">';
		output.innerHTML += '<br>Copy Id ' + mealVal;
	});
	var output = document.getElementById("preview_pic");
	var error = document.getElementById("error");

	var plan_data = document.getElementById("plan_meals_data").getElementsByClassName("day");
	for(i=0;i<plan_data.length;i++){
		plan_data[i].outerHTML += '<button class="button" onclick="putMeal(this)" type="button">Add Meal</button>';
	}

	function putMeal(item){
		var mealValues = (item.previousSibling.value.split(','));
		var mealVal = id.selectedOptions[0].innerText.split(" | ")[0];
		if (mealValues.length<5){
			error.innerHTML = '';
			if(item.previousSibling.value == ''){
				item.previousSibling.value += mealVal;
			} else {
				item.previousSibling.value += ',' + mealVal;
			}
		}else {
			error.innerHTML = 'You cant add more than 5 meals in 1 day';
		}

	}
	
	var input = document.getElementById("myInput");
	input.addEventListener('keyup',filterFunction);

	function filterFunction() {
		var input, filter, ul, li, a, i, select;
		filter = this.value.toUpperCase();
		select = document.getElementById("_select");
		a = select.getElementsByTagName("option");
		for (i = 0; i < a.length; i++) {
			txtValue = a[i].textContent || a[i].innerText;
			if (txtValue.toUpperCase().indexOf(filter) > -1) {
				a[i].style.display = "";
			} else {
				a[i].style.display = "none";
			}
		}
	}
</script><?php
});

add_action('woocommerce_process_product_meta', function($post_id) {
	$product = wc_get_product($post_id);
	// Select
	sanitize_product_update('monday', $product);
	sanitize_product_update('tuesday', $product);
	sanitize_product_update('wednesday', $product);
	sanitize_product_update('thursday', $product);
	sanitize_product_update('friday', $product);
	sanitize_product_update('saturday', $product);
	sanitize_product_update('sunday', $product);


	$product->save();
});

//Plan Bundle
add_filter('woocommerce_product_data_tabs', function($tabs) {
	$tabs['plan_meals'] = [
		'label' => __('Plan Meals', 'txtdomain'),
		'target' => 'plan_meals_data',
		'class' => ['show_if_plan'],
		'priority' => 55
	];
	return $tabs;
});

function day_meals_product($name, $title, $breakfast, $snack_one, $lunch, $snack_two, $dinner, $post_id){
	$values = get_post_meta( $name, $post_id );
	if(empty($values)){
		$values = ['Select Breakfast', 'Select Snack', 'Select Lunch', 'Select Snack', 'Select Dinner'];
	}
	$row = '';
	$row .= '<tr><th><label for="'.$name.'">' . $title . '</label></th><td>';
	$row .= '<select name="'.$name.'[]">' . run_options($breakfast, $values[0]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($snack_one, $values[1]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($lunch, $values[2]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($snack_two, $values[3]) . '</select><br>';
	$row .= '<select name="'.$name.'[]">' . run_options($dinner, $values[4]) . '</select><br>';
	$row .= '</td></tr>';

	echo $row;
}


//Fridge

add_shortcode('fridge', 'sort_fridge');

function sort_fridge(){

	$current_user = wp_get_current_user();
	if ( 0 == $current_user->ID ) return;

	// GET USER ORDERS (COMPLETED + PROCESSING)
	$customer_orders = get_posts( array(
		'numberposts' => -1,
		'meta_key'    => '_customer_user',
		'meta_value'  => $current_user->ID,
		'post_type'   => wc_get_order_types(),
		'post_status' => array_keys( wc_get_is_paid_statuses() ),
	) );

	// LOOP THROUGH ORDERS AND GET PRODUCT IDS
	if ( ! $customer_orders ) return;
	$purchased_items = array();
	$fridge_items = array();
	foreach ( $customer_orders as $customer_order ) {
		$order = wc_get_order( $customer_order->ID );
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();
			$qty = $item->get_quantity();
			$product = wc_get_product($product_id);
			if($product){
				$img_id = $product->get_image_id();
				$type = $product->get_type();
				$name = $product->get_name();
				$weight = $product->get_weight() * 1000;
				$sumweight = $weight * $qty;
				$categories = array();
				$terms = wp_get_post_terms( $product_id, 'product_cat' );
				foreach ( $terms as $term ) if ($term->parent == 145) array_push($categories, $term->name);
				$maincat = $categories[0];

				if($type == 'variable'){
					if (empty($purchased_items)){
						array_push($purchased_items, array(
							'id' => $product_id,
							'img_id' => $img_id,
							'name' => $name,
							'weight' => $sumweight,
							'category' => $maincat,
							'qty'=>$qty
						));
					} else {
						$bool = false;
						foreach($purchased_items as $k => $purchased_item){
							if ($purchased_item['id'] == $product_id){
								$purchased_items[$k]['qty'] = $purchased_item['qty'] + $qty;
								$purchased_items[$k]['weight'] = $purchased_item['weight'] + $sumweight;
								$bool = true;
							} elseif ($k == count($purchased_items)-1 && ! $bool) {
								array_push($purchased_items, array(
									'id' => $product_id,
									'img_id' => $img_id,
									'name' => $name,
									'weight' => $sumweight,
									'category' => $maincat,
									'qty'=>$qty
								));
							}
						}
					}
				}
			}
		}
	}

	$cat_col  = array_column($purchased_items, 'category');
	$name_col = array_column($purchased_items, 'name');
	array_multisort($cat_col, SORT_ASC, $name_col, SORT_ASC, $purchased_items);

	$cat_col = array_unique($cat_col);

	$output = '<div class="col-lg-12 fridge"><h2>Your purchases</h2>';
	$output .= '<div class="fridge">';
	foreach ($cat_col as $cat){
		$output .= "<h4>$cat</h4>";
		$output .= '<div class="white-container fridge-items">';
		foreach($purchased_items as $purchased_item){
			if ($purchased_item['category'] == $cat){
				if ($purchased_item['img_id']){
					$image = '<img class="bundle-pic" src="' . groci_resize( wp_get_attachment_url($purchased_item['img_id']), 300, 300, true, true, true ) . '">';
				} else {
					$image = '<img class="bundle-pic" src="' .  wc_placeholder_img_src(). '">';
				}
				$output .= '<div class="bundle-product">';
				$output .= $image;
				$output .= '<div class="bundle-product-description">';
				$output .= '<div class="product-body">';
				$output .= '<h5>' . $purchased_item['qty'] . ' x ' . $purchased_item['name'] . '</h5>';
				if($purchased_item['weight'] < 1000){
					$output .= '<p>' . $purchased_item['weight'] . 'g</p>';
				} else {
					$output .= '<p>' . $purchased_item['weight'] . 'kg</p>';
				}

				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
			}
		}
		$output .= '</div>';
	}
	$output .= '</div>';
	$output .= '</div>';


	return $output;
}

//Register plan as a product
add_action( 'init', 'register_plan_product_type' );
 
function register_plan_product_type() {
 
  class WC_Product_Plan extends WC_Product {
			
    public function __construct( $product ) {
        $this->product_type = 'plan';
	parent::__construct( $product );
    }
  }
}


//Add plan to types
add_filter( 'product_type_selector', 'add_plan_product_type' );

function add_plan_product_type( $types ){
    $types[ 'plan' ] = __( 'Meal Plan' ,'mp_product');

    return $types;	
}


//Data panels
add_action( 'woocommerce_product_data_panels', 'plan_product_tab_product_tab_content' );

function plan_product_tab_product_tab_content() {

 ?><div id='plan_product_options' class='panel woocommerce_options_panel'><?php
 ?><div class='options_group'><?php
				
    woocommerce_wp_text_input(
	array(
	  'id' => 'plan_product_info',
	  'label' => __( 'Meal Plan', 'mp_product' ),
	  'placeholder' => '',
	  'desc_tip' => 'true',
	  'description' => __( 'Enter Plan Info.', 'mp_product' ),
	  'type' => 'text'
	)
    );
 ?></div>
 </div><?php
}

//Save 
add_action( 'woocommerce_process_product_meta', 'save_plan_product_settings' );
	
function save_plan_product_settings( $post_id ){
		
    $plan_product_info = $_POST['plan_product_info'];
		
    if( !empty( $plan_product_info ) ) {

	update_post_meta( $post_id, 'plan_product_info', esc_attr( $plan_product_info ) );
    }
}

//Front
add_action( 'woocommerce_single_product_summary', 'plan_product_front' );
	
function plan_product_front () {
    global $product;

    if ( 'plan' == $product->get_type() ) {  	
       echo( get_post_meta( $product->get_id(), 'plan_product_info' )[0] );

  }
}

add_filter( 'product_type_selector', 'remove_product_types' );

function remove_product_types( $types ){
	if(! is_super_admin()){
		unset( $types['variable'] );
		unset( $types['simple'] );
		unset( $types['grouped'] );
		unset( $types['external'] );
		
	}
	return $types;
}

add_action('save_post', 'add_title_as_category');

function add_title_as_category( $postid ) {
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
  $post = get_post($postid);
  if ( $post->post_type == 'post') { // change 'post' to any cpt you want to target
    $term = get_term_by('slug', $post->post_name, 'category');
    if ( empty($term) ) {
      $add = wp_insert_term( $post->post_title, 'category', array('slug'=> $post->post_name) );
      if ( is_array($add) && isset($add['term_id']) ) {
        wp_set_object_terms($postid, $add['term_id'], 'category', true );
      }
    }
  }
}